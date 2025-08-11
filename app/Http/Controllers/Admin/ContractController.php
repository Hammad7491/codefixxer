<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /** quick helper: our tenant is just the logged-in user id */
    protected function tenantId(): int
    {
        return (int) Auth::id();
    }

    /** list contracts for this tenant */
    public function index()
    {
        $contracts = Contract::where('tenant_id', $this->tenantId())
            ->latest()
            ->paginate(12);

        return view('admin.contracts.index', compact('contracts'));
    }

    /** show create form */
    public function create()
    {
        return view('admin.contracts.create'); // your blade already handles create/edit
    }

    /** store new contract + milestones */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // create contract
        $contract = new Contract();
        $contract->tenant_id            = $this->tenantId();
        $contract->contract_number      = $data['contract_number'] ?? $this->nextNumber();
        $contract->title                = $data['title'] ?? null;
        $contract->purpose              = $data['purpose'] ?? null;

        $contract->client_name          = $data['client_name'] ?? null;
        $contract->client_email         = $data['client_email'] ?? null;
        $contract->client_phone         = $data['client_phone'] ?? null;
        $contract->client_address       = $data['client_address'] ?? null;

        $contract->start_date           = $data['start_date'] ?? null;
        $contract->end_date             = $data['end_date'] ?? null;

        $contract->project_timeline     = $data['project_timeline'] ?? null;
        $contract->payment_terms        = $data['payment_terms'] ?? null;

        $contract->revisions            = $data['revisions'] ?? null;
        $contract->ownership_ip         = $data['ownership_ip'] ?? null;
        $contract->confidentiality      = $data['confidentiality'] ?? null;
        $contract->client_responsibilities = $data['client_responsibilities'] ?? null;
        $contract->termination_clause   = $data['termination_clause'] ?? null;

        $contract->dispute_resolution   = $data['dispute_resolution'] ?? null;
        $contract->limitation_of_liability = $data['limitation_of_liability'] ?? null;
        $contract->amendments           = $data['amendments'] ?? null;

        $contract->total_cost           = 0; // will fill after milestones
        $contract->save();

        // milestones (simple createMany)
        $milestones = $this->parseMilestonesFromRequest($request);
        if (!empty($milestones)) {
            $contract->milestones()->createMany(
                array_map(function ($m) {
                    $m['tenant_id'] = $this->tenantId();
                    return $m;
                }, $milestones)
            );
        }

        // total = sum of milestone amounts (simple + reliable)
        $contract->total_cost = $contract->milestones()->sum('amount');
        $contract->save();

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract created.');
    }

    /** show one */
    public function show(Contract $contract)
    {
        $this->ensureTenant($contract);
        $contract->load('milestones');
        return view('admin.contracts.show', compact('contract'));
    }

    /** edit form */
    public function edit(Contract $contract)
    {
        $this->ensureTenant($contract);
        $contract->load('milestones');
        return view('admin.contracts.create', compact('contract'));
    }

    /** update contract + milestones (simple replace) */
    public function update(Request $request, Contract $contract)
    {
        $this->ensureTenant($contract);
        $data = $this->validateRequest($request);

        $contract->contract_number      = $data['contract_number'] ?? $contract->contract_number ?? $this->nextNumber();
        $contract->title                = $data['title'] ?? null;
        $contract->purpose              = $data['purpose'] ?? null;

        $contract->client_name          = $data['client_name'] ?? null;
        $contract->client_email         = $data['client_email'] ?? null;
        $contract->client_phone         = $data['client_phone'] ?? null;
        $contract->client_address       = $data['client_address'] ?? null;

        $contract->start_date           = $data['start_date'] ?? null;
        $contract->end_date             = $data['end_date'] ?? null;

        $contract->project_timeline     = $data['project_timeline'] ?? null;
        $contract->payment_terms        = $data['payment_terms'] ?? null;

        $contract->revisions            = $data['revisions'] ?? null;
        $contract->ownership_ip         = $data['ownership_ip'] ?? null;
        $contract->confidentiality      = $data['confidentiality'] ?? null;
        $contract->client_responsibilities = $data['client_responsibilities'] ?? null;
        $contract->termination_clause   = $data['termination_clause'] ?? null;

        $contract->dispute_resolution   = $data['dispute_resolution'] ?? null;
        $contract->limitation_of_liability = $data['limitation_of_liability'] ?? null;
        $contract->amendments           = $data['amendments'] ?? null;

        $contract->save();

        // replace milestones in a very straightforward way
        $contract->milestones()->delete();

        $milestones = $this->parseMilestonesFromRequest($request);
        if (!empty($milestones)) {
            $contract->milestones()->createMany(
                array_map(function ($m) {
                    $m['tenant_id'] = $this->tenantId();
                    return $m;
                }, $milestones)
            );
        }

        $contract->total_cost = $contract->milestones()->sum('amount');
        $contract->save();

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract updated.');
    }

    /** delete */
    public function destroy(Contract $contract)
    {
        $this->ensureTenant($contract);
        $contract->delete();

        return back()->with('success', 'Contract deleted.');
    }

    // ----------------- tiny helpers -----------------

    /** minimal validation; arrays are optional */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'contract_number'          => ['nullable','string','max:191'],
            'title'                    => ['nullable','string','max:191'],
            'purpose'                  => ['nullable','string'],

            'client_name'              => ['nullable','string','max:191'],
            'client_email'             => ['nullable','email','max:191'],
            'client_phone'             => ['nullable','string','max:191'],
            'client_address'           => ['nullable','string'],

            'start_date'               => ['nullable','date'],
            'end_date'                 => ['nullable','date','after_or_equal:start_date'],

            'project_timeline'         => ['nullable','string'],
            'payment_terms'            => ['nullable','string'],

            'revisions'                => ['nullable','string'],
            'ownership_ip'             => ['nullable','string'],
            'confidentiality'          => ['nullable','string'],
            'client_responsibilities'  => ['nullable','string'],
            'termination_clause'       => ['nullable','string'],

            'dispute_resolution'       => ['nullable','string'],
            'limitation_of_liability'  => ['nullable','string'],
            'amendments'               => ['nullable','string'],

            // milestones come as parallel arrays from your blade
            'milestone_name'           => ['sometimes','array'],
            'milestone_name.*'         => ['nullable','string','max:191'],
            'milestone_desc'           => ['sometimes','array'],
            'milestone_desc.*'         => ['nullable','string'],
            'milestone_date'           => ['sometimes','array'],
            'milestone_date.*'         => ['nullable','date'],
            'milestone_amount'         => ['sometimes','array'],
            'milestone_amount.*'       => ['nullable','numeric','min:0'],
        ]);
    }

    /** extract milestones from request arrays (skip empty rows) */
    protected function parseMilestonesFromRequest(Request $request): array
    {
        $names = $request->input('milestone_name', []);
        $descs = $request->input('milestone_desc', []);
        $dates = $request->input('milestone_date', []);
        $amts  = $request->input('milestone_amount', []);

        $rows = max(count($names), count($descs), count($dates), count($amts));
        $out  = [];

        for ($i = 0; $i < $rows; $i++) {
            $name   = $names[$i] ?? null;
            $amount = isset($amts[$i]) ? (float) $amts[$i] : 0;

            if (blank($name) && $amount <= 0) continue; // skip empty line

            $out[] = [
                'name'        => $name,
                'description' => $descs[$i] ?? null,
                'due_date'    => $dates[$i] ?? null,
                'amount'      => $amount,
            ];
        }

        return $out;
    }

    /** generate next simple number like #2001, #2002 scoped per tenant */
    protected function nextNumber(): string
    {
        $last = Contract::where('tenant_id', $this->tenantId())
            ->orderByDesc('id')
            ->value('contract_number');

        $n = $last ? (int) preg_replace('/\D+/', '', $last) : 0;
        return '#' . ($n ? $n + 1 : 2001);
    }

    /** simple guard to prevent cross-tenant access */
    protected function ensureTenant(Contract $contract): void
    {
        abort_unless($contract->tenant_id === $this->tenantId(), 403, 'Unauthorized');
    }
}
