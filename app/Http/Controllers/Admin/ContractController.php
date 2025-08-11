<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::latest()->paginate(12);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('admin.contracts.create'); // same view used for edit
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id') ?? 1;

        $contract = null;

        DB::transaction(function () use ($data, $tenantId, &$contract) {
            $contract = new Contract($data);
            $contract->tenant_id = $tenantId;
            $contract->contract_number = $contract->contract_number ?: $this->nextNumber($tenantId);
            $contract->save();

            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) continue;

                $contract->milestones()->create([
                    'tenant_id'  => $tenantId,
                    'name'       => $m['name'] ?? '',
                    'description'=> $m['description'] ?? '',
                    'due_date'   => $m['due_date'] ?? null,
                    'amount'     => $m['amount'] ?? 0,
                ]);
            }

            $contract->recalcTotal();
            $contract->save();
        });

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract created.');
    }

    public function show(Contract $contract)
    {
        $contract->load('milestones');
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $contract->load('milestones');
        // reuse the create view
        return view('admin.contracts.create', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $this->validated($request);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id') ?? 1;

        DB::transaction(function () use ($data, $tenantId, $contract) {
            $contract->fill($data);
            $contract->tenant_id = $tenantId;
            $contract->save();

            $contract->milestones()->delete();
            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) continue;

                $contract->milestones()->create([
                    'tenant_id'  => $tenantId,
                    'name'       => $m['name'] ?? '',
                    'description'=> $m['description'] ?? '',
                    'due_date'   => $m['due_date'] ?? null,
                    'amount'     => $m['amount'] ?? 0,
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
        $contract->delete();
        return back()->with('success', 'Contract deleted.');
    }

    /** ------------ Helpers ------------- */

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

            // arrays
            'milestone_name'         => ['array'],
            'milestone_name.*'       => ['nullable','string','max:191'],
            'milestone_desc'         => ['array'],
            'milestone_desc.*'       => ['nullable','string'],
            'milestone_date'         => ['array'],
            'milestone_date.*'       => ['nullable','date'],
            'milestone_amount'       => ['array'],
            'milestone_amount.*'     => ['nullable','numeric','min:0'],
        ]);

        // normalize milestones
        $milestones = [];
        $names = $request->input('milestone_name', []);
        $descs = $request->input('milestone_desc', []);
        $dates = $request->input('milestone_date', []);
        $amts  = $request->input('milestone_amount', []);
        $count = max(count($names), count($descs), count($dates), count($amts));

        for ($i=0; $i<$count; $i++) {
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

    protected function nextNumber(int $tenantId): string
    {
        $last = Contract::forTenant($tenantId)->orderByDesc('id')->value('contract_number');
        $n = (int) preg_replace('/\D+/', '', (string) $last);
        return '#' . ($n ? $n + 1 : 2001);
    }
}
