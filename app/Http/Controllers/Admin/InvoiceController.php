<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? Auth::id();
        $invoices = Invoice::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(12);

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('admin.invoices.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $tenantId = Auth::user()->tenant_id ?? Auth::id();

        $data['tax_percent'] = 0;

        $invoice = null;

        DB::transaction(function () use ($data, $tenantId, &$invoice) {
            $invoice = new Invoice($data);
            $invoice->tenant_id = $tenantId;
            $invoice->invoice_number = $invoice->invoice_number ?: $this->nextNumber($tenantId);
            $invoice->save();

            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) continue;

                $invoice->milestones()->create([
                    'tenant_id' => $tenantId,
                    'name'      => $m['name'] ?? '',
                    'due_date'  => $m['due_date'] ?? null,
                    'amount'    => $m['amount'] ?? 0,
                ]);
            }

            $invoice->recalcTotals();
            $invoice->save();
        });

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeTenant($invoice);
        $invoice->load('milestones');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorizeTenant($invoice);
        $invoice->load('milestones');
        return view('admin.invoices.create', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeTenant($invoice);
        $data = $this->validated($request, $invoice->id);
        $tenantId = Auth::user()->tenant_id ?? Auth::id();
        $data['tax_percent'] = 0;

        DB::transaction(function () use ($data, $tenantId, $invoice) {
            $invoice->fill($data);
            $invoice->tenant_id = $tenantId;
            $invoice->save();

            $invoice->milestones()->delete();
            foreach ($data['milestones'] as $m) {
                if (blank($m['name']) && blank($m['amount'])) continue;

                $invoice->milestones()->create([
                    'tenant_id' => $tenantId,
                    'name'      => $m['name'] ?? '',
                    'due_date'  => $m['due_date'] ?? null,
                    'amount'    => $m['amount'] ?? 0,
                ]);
            }

            $invoice->recalcTotals();
            $invoice->save();
        });

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorizeTenant($invoice);
        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    protected function validated(Request $request, $ignoreId = null): array
    {
        $tenantId = Auth::user()->tenant_id ?? Auth::id();

        $validated = $request->validate([
            'invoice_number' => [
                'nullable', 'string', 'max:191',
                Rule::unique('invoices')
                    ->where(fn($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($ignoreId),
            ],
            'client_name'        => ['required', 'string', 'max:191'],
            'contact_person'     => ['nullable', 'string', 'max:191'],
            'client_email'       => ['nullable', 'email', 'max:191'],
            'client_phone'       => ['nullable', 'string', 'max:191'],
            'client_address'     => ['nullable', 'string'],

            'project_title'       => ['nullable', 'string', 'max:191'],
            'project_description' => ['nullable', 'string'],

            'discount_name'  => ['nullable', 'string', 'max:191'],
            'discount_type'  => ['required', Rule::in(['percent','fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0'],

            'tax_percent'    => ['nullable'],

            'bank_name'     => ['nullable', 'string', 'max:191'],
            'bank_account'  => ['nullable', 'string', 'max:191'],
            'account_holder'=> ['nullable', 'string', 'max:191'],
            'terms'         => ['nullable', 'string'],

            'milestone_name'     => ['array'],
            'milestone_name.*'   => ['nullable','string','max:191'],
            'milestone_date'     => ['array'],
            'milestone_date.*'   => ['nullable','date'],
            'milestone_amount'   => ['array'],
            'milestone_amount.*' => ['nullable','numeric','min:0'],
        ]);

        $milestones = [];
        $names  = $request->input('milestone_name', []);
        $dates  = $request->input('milestone_date', []);
        $amount = $request->input('milestone_amount', []);
        $count  = max(count($names), count($dates), count($amount));

        for ($i=0; $i<$count; $i++) {
            $milestones[] = [
                'name'     => $names[$i]  ?? null,
                'due_date' => $dates[$i]  ?? null,
                'amount'   => $amount[$i] ?? 0,
            ];
        }

        $validated['milestones'] = $milestones;
        $validated['tax_percent'] = 0;

        return $validated;
    }

    protected function nextNumber(?int $tenantId = null): string
    {
        $tenantId = $tenantId ?? (Auth::user()->tenant_id ?? Auth::id());
        $last = Invoice::forTenant($tenantId)->orderByDesc('id')->value('invoice_number');

        $n = (int) preg_replace('/\D+/', '', (string) $last);
        $n = $n ? $n + 1 : 1001;

        return '#' . $n;
    }

    protected function authorizeTenant(Invoice $invoice)
    {
        if ($invoice->tenant_id !== (Auth::user()->tenant_id ?? Auth::id())) {
            abort(403, 'Unauthorized');
        }
    }
}
