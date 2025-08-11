<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? Auth::id();

        $contracts = Contract::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(12);

        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('admin.contracts.create'); // reuse for edit
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $tenantId = Auth::user()->tenant_id ?? Auth::id();

        $contract = null;

        DB::transaction(function () use ($data, $tenantId, &$contract) {
            // Create base contract
            $contract = new Contract($data);
            $contract->tenant_id = $tenantId;
            $contract->contract_number = $contract->contract_number ?: $this->nextNumber($tenantId);
            $contract->save();

            // Create milestones (if any)
            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) {
                    continue;
                }
                $contract->milestones()->create([
                    'tenant_id'   => $tenantId,
                    'name'        => $m['name'] ?? '',
                    'description' => $m['description'] ?? '',
                    'due_date'    => $m['due_date'] ?? null,
                    'amount'      => $m['amount'] ?? 0,
                ]);
            }

            // Totals
            $contract->recalcTotal();
            $contract->save();
        });

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract created.');
    }

    public function show(Contract $contract)
    {
        $this->authorizeTenant($contract);

        $contract->load('milestones');
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $this->authorizeTenant($contract);

        $contract->load('milestones');
        // reuse create blade (prefills with old(...) and $contract values)
        return view('admin.contracts.create', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $this->authorizeTenant($contract);

        $data = $this->validated($request);
        $tenantId = Auth::user()->tenant_id ?? Auth::id();

        DB::transaction(function () use ($data, $tenantId, $contract) {
            $contract->fill($data);
            $contract->tenant_id = $tenantId;
            $contract->save();

            // Simple sync: delete & recreate milestones
            $contract->milestones()->delete();

            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) {
                    continue;
                }
                $contract->milestones()->create([
                    'tenant_id'   => $tenantId,
                    'name'        => $m['name'] ?? '',
                    'description' => $m['description'] ?? '',
                    'due_date'    => $m['due_date'] ?? null,
                    'amount'      => $m['amount'] ?? 0,
                ]);
            }

            $contract->recalcTotal();
            $contract->save();
        });

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract updated.');
    }

    public function destroy(Contract $contract)
    {
        $this->authorizeTenant($contract);

        $contract->delete();
        return back()->with('success', 'Contract deleted.');
    }

    /* =================== Helpers =================== */

    protected function validated(Request $request): array
    {
        $validated = $request->validate([
            'contract_number'        => ['nullable','string','max:191'],
            'title'                  => ['nullable','string','max:191'],
            'purpose'                => ['nullable','string'],

            'client_name'            => ['nullable','string','max:191'],
            'client_email'           => ['nullable','email','max:191'],
            'client_phone'           => ['nullable','string','max:191'],
            'client_address'         => ['nullable','string'],

            'start_date'             => ['nullable','date'],
            'end_date'               => ['nullable','date','after_or_equal:start_date'],

            'project_timeline'       => ['nullable','string'],
            'payment_terms'          => ['nullable','string'],

            'revisions'              => ['nullable','string'],
            'ownership_ip'           => ['nullable','string'],
            'confidentiality'        => ['nullable','string'],
            'client_responsibilities'=> ['nullable','string'],
            'termination_clause'     => ['nullable','string'],

            'dispute_resolution'     => ['nullable','string'],
            'limitation_of_liability'=> ['nullable','string'],
            'amendments'             => ['nullable','string'],

            // milestone arrays
            'milestone_name'         => ['array'],
            'milestone_name.*'       => ['nullable','string','max:191'],
            'milestone_desc'         => ['array'],
            'milestone_desc.*'       => ['nullable','string'],
            'milestone_date'         => ['array'],
            'milestone_date.*'       => ['nullable','date'],
            'milestone_amount'       => ['array'],
            'milestone_amount.*'     => ['nullable','numeric','min:0'],
        ]);

        // Normalize milestones into a structured array for creation
        $milestones = [];
        $names = $request->input('milestone_name', []);
        $descs = $request->input('milestone_desc', []);
        $dates = $request->input('milestone_date', []);
        $amts  = $request->input('milestone_amount', []);
        $count = max(count($names), count($descs), count($dates), count($amts));

        for ($i = 0; $i < $count; $i++) {
            $milestones[] = [
                'name'        => $names[$i] ?? null,
                'description' => $descs[$i] ?? null,
                'due_date'    => $dates[$i] ?? null,
                'amount'      => $amts[$i] ?? 0,
            ];
        }

        $validated['milestones'] = $milestones;

        return $validated;
    }

    protected function nextNumber(?int $tenantId = null): string
    {
        $tenantId = $tenantId ?? (Auth::user()->tenant_id ?? Auth::id());

        $last = Contract::forTenant($tenantId)->orderByDesc('id')->value('contract_number');
        $n = (int) preg_replace('/\D+/', '', (string) $last);

        return '#' . ($n ? $n + 1 : 2001);
    }

    protected function authorizeTenant(Contract $contract): void
    {
        $currentTenant = Auth::user()->tenant_id ?? Auth::id();
        if ($contract->tenant_id !== $currentTenant) {
            abort(403, 'Unauthorized');
        }
    }
}
