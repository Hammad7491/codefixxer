{{-- resources/views/admin/invoices/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Invoice|null $invoice */
  $invoice = $invoice ?? null; // avoid "Undefined variable $invoice"
  $isEdit  = (bool) $invoice;
@endphp

@section('title', $isEdit ? 'Edit Invoice' : 'Add Invoice')

@section('content')
<style>
  :root { color-scheme: light; }
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }

  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select {
    border-radius: 12px; border-color: #e2e8f0;
    background: #fff !important; color: #0f172a !important;
  }
  .form-control::placeholder { color:#64748b; }
  .form-control:focus, .form-select:focus { border-color:#6366f1; box-shadow:0 0 0 .2rem rgba(99,102,241,.15); }
  .help-text { color:#64748b; font-size:.875rem; }

  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
  .btn-light-ghost, .btn-outline-dark, .btn-outline-secondary, .btn-outline-danger { border-radius:12px; }

  /* Wizard */
  .wizard { border-top: 1px solid rgba(148,163,184,.15); }
  .steps { display:flex; gap:1rem; padding:1rem 1.25rem; background:#f8fafc; border-bottom:1px solid #e5e7eb; }
  .step { flex:1; display:flex; align-items:center; gap:.75rem; padding:.6rem .8rem; border-radius:12px; transition:.2s; }
  .step .dot { width:32px; height:32px; display:grid; place-items:center; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:700; }
  .step small { color:#6b7280; display:block; margin-top:.15rem; }
  .step.active { background:#eef2ff; }
  .step.active .dot { background:#4f46e5; color:#fff; }
  .pane { display:none; padding:1.25rem; }
  .pane.active { display:block; }

  /* Milestones */
  .ms-table th, .ms-table td { vertical-align: middle; }
  .ms-table input { height: 44px; }

  /* Preview */
  .invoice-preview { background:#fff; color:#0f172a; border-radius:14px; border:1px solid #e5e7eb; overflow:hidden; }
  .ip-header { border-bottom:2px solid #e5e7eb; padding:1rem 1.25rem; display:flex; gap:1rem; align-items:center; justify-content:space-between; }
  .ip-body { padding:1rem 1.25rem; }
  .ip-badge { background:#e0ebff; color:#1f4fd7; padding:.5rem 1rem; border-radius:999px; font-weight:700; }
  .ip-table { width:100%; border-collapse:collapse; }
  .ip-table th, .ip-table td { border:1px solid #e5e7eb; padding:.6rem .75rem; }
  .ip-totals td { border:none; padding:.35rem 0; }
  .ip-total { background:#e7f0ff; border:2px solid #bfdbfe; font-weight:800; }
  .signature { font-family:cursive; font-size:1.6rem; }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Invoice' : 'Add Invoice' }}</h1>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Invoices List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:file-document-edit-outline"></iconify-icon>
      <strong class="fs-6">{{ $isEdit ? 'Update Invoice' : 'Invoice Details' }}</strong>
    </div>

    <div class="wizard">
      {{-- Steps --}}
      <div class="steps">
        <div class="step active" data-step="1">
          <span class="dot">1</span>
          <div><div class="fw-semibold">Basic Details</div><small>Client Information</small></div>
        </div>
        <div class="step" data-step="2">
          <span class="dot">2</span>
          <div><div class="fw-semibold">Work Experience</div><small>Project / Milestones</small></div>
        </div>
        <div class="step" data-step="3">
          <span class="dot">3</span>
          <div><div class="fw-semibold">Accomplishments</div><small>Discount (No Tax)</small></div>
        </div>
        <div class="step" data-step="4">
          <span class="dot">4</span>
          <div><div class="fw-semibold">Additional Info</div><small>Payment & Terms</small></div>
        </div>
      </div>

      <form method="POST"
            action="{{ $isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store') }}"
            id="invoiceForm" class="needs-validation" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Pane 1 --}}
        <div class="pane active" data-pane="1">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Client Name</label>
              <input class="form-control" name="client_name" placeholder="Client Name" value="{{ old('client_name', $invoice->client_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <input class="form-control" name="contact_person" placeholder="Contact Person" value="{{ old('contact_person', $invoice->contact_person ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="client_email" placeholder="Client Email" value="{{ old('client_email', $invoice->client_email ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input class="form-control" name="client_phone" placeholder="Phone Number" value="{{ old('client_phone', $invoice->client_phone ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" rows="3" name="client_address" placeholder="Client Address">{{ old('client_address', $invoice->client_address ?? '') }}</textarea>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 2 --}}
        <div class="pane" data-pane="2">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Project Title</label>
              <input class="form-control" name="project_title" placeholder="Project Title" value="{{ old('project_title', $invoice->project_title ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Project Description</label>
              <textarea class="form-control" rows="3" name="project_description" placeholder="Brief project description">{{ old('project_description', $invoice->project_description ?? '') }}</textarea>
            </div>
          </div>

          <div class="mt-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0">Milestones</h6>
              <button class="btn btn-soft-primary btn-sm" type="button" id="addMilestone">
                <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>Add Row
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered ms-table align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width:55%">Milestone Name</th>
                    <th style="width:25%">Due Date</th>
                    <th style="width:15%">Amount</th>
                    <th style="width:5%">Action</th>
                  </tr>
                </thead>
                <tbody id="milestones">
                  @php
                    $msNames = old('milestone_name', $invoice?->milestones?->pluck('name')->toArray() ?? ['']);
                    $msDates = old('milestone_date', $invoice?->milestones?->pluck('due_date')->map(fn($d)=>optional($d)->format('Y-m-d'))->toArray() ?? ['']);
                    $msAmts  = old('milestone_amount', $invoice?->milestones?->pluck('amount')->toArray() ?? ['']);
                    $rows    = max(1, count($msNames));
                  @endphp
                  @for($i=0; $i<$rows; $i++)
                  <tr class="milestone">
                    <td><input class="form-control" name="milestone_name[]" placeholder="Milestone Name" value="{{ $msNames[$i] ?? '' }}"></td>
                    <td><input type="date" class="form-control" name="milestone_date[]" value="{{ $msDates[$i] ?? '' }}"></td>
                    <td><input type="number" step="0.01" class="form-control amt" name="milestone_amount[]" placeholder="0.00" value="{{ $msAmts[$i] ?? '' }}"></td>
                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove">Remove</button></td>
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 3 (Discount only, Tax = 0) --}}
        <div class="pane" data-pane="3">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Discount Name</label>
              <input class="form-control" name="discount_name" placeholder="Discount Name" value="{{ old('discount_name', $invoice->discount_name ?? '') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Type</label>
              @php $dtype = old('discount_type', $invoice->discount_type ?? 'percent'); @endphp
              <select class="form-select" name="discount_type" id="discountType">
                <option value="percent" {{ $dtype==='percent'?'selected':'' }}>Percent (%)</option>
                <option value="fixed"   {{ $dtype==='fixed'?'selected':'' }}>Fixed (amount)</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Value</label>
              <input type="number" step="0.01" class="form-control" name="discount_value" id="discountValue" placeholder="e.g. 10" value="{{ old('discount_value', $invoice->discount_value ?? 0) }}">
            </div>

            <input type="hidden" name="tax_percent" id="taxPercent" value="0">

            <div class="col-md-9"></div>
            <div class="col-md-3">
              <div class="border rounded-3 p-3">
                <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="t-subtotal">$0.00</strong></div>
                <div class="d-flex justify-content-between"><span id="t-discount-label">Discount</span><strong class="text-danger" id="t-discount">-$0.00</strong></div>
                <div class="d-flex justify-content-between"><span>Tax</span><strong id="t-tax">$0.00</strong></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5"><span>Total</span><strong id="t-total">$0.00</strong></div>
              </div>
              <div class="help-text mt-2">Tax is disabled for all invoices (always 0).</div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 4 --}}
        <div class="pane" data-pane="4">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Bank Name</label>
              <input class="form-control" name="bank_name" placeholder="Bank Name" value="{{ old('bank_name', $invoice->bank_name ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Bank Account Number</label>
              <input class="form-control" name="bank_account" placeholder="Account Number" value="{{ old('bank_account', $invoice->bank_account ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Account Holder Name</label>
              <input class="form-control" name="account_holder" placeholder="Account Holder Name" value="{{ old('account_holder', $invoice->account_holder ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Terms & Conditions</label>
              <textarea class="form-control" name="terms" rows="4">{{ old('terms', $invoice->terms ?? 'Invoice was created on a computer and is valid without the signature and seal.') }}</textarea>
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary" id="btnPreview">Preview</button>
          </div>
        </div>
      </form>

      {{-- Pane 5: Preview --}}
      <div class="pane" data-pane="5">
        <div class="invoice-preview">
          <div class="ip-header">
            <div class="d-flex align-items-center gap-3">
              <img src="{{ asset('assets/images/logo.png') }}" alt="logo" height="36">
              <div class="small">
                <div><strong id="p-email">email@example.com</strong></div>
                <div id="p-email2" class="text-muted">support@example.com</div>
              </div>
            </div>
            <div class="ip-badge" id="p-grand-total">Grand Total: $0.00</div>
          </div>

          <div class="ip-body">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <div class="small text-muted">Invoice No:</div>
                <div><strong id="p-number">#AUTO</strong></div>
                <div class="small text-muted mt-2">Invoice Date:</div>
                <div id="p-date">{{ now()->format('d F Y') }}</div>
                <div class="small text-muted mt-2">Grand Total:</div>
                <div id="p-grand">$0.00</div>
              </div>
            </div>

            <h6 class="mb-2">Invoice To</h6>
            <table class="ip-table mb-3">
              <tr>
                <td>
                  <div><strong>Name:</strong> <span id="p-client-name">—</span></div>
                  <div><strong>Email:</strong> <span id="p-client-email">—</span></div>
                </td>
                <td>
                  <div><strong>Phone:</strong> <span id="p-client-phone">—</span></div>
                  <div><strong>Address:</strong> <span id="p-client-address">—</span></div>
                </td>
              </tr>
            </table>

            <h6 class="mb-2">Project Details</h6>
            <table class="ip-table mb-3">
              <tr><th>Project Title:</th><td id="p-project-title">—</td></tr>
              <tr><th>Project Description:</th><td id="p-project-desc">—</td></tr>
            </table>

            <table class="ip-table mb-3">
              <thead>
                <tr>
                  <th style="width:55%">Milestone</th>
                  <th style="width:25%">Due Date</th>
                  <th style="width:20%" class="text-end">Amount</th>
                </tr>
              </thead>
              <tbody id="p-milestones-body"></tbody>
            </table>

            <div class="row">
              <div class="col-md-7">
                <div class="small"><strong>Payment info:</strong></div>
                <div class="small" id="p-bank"></div>
                <div class="small" id="p-account"></div>
                <div class="small" id="p-holder"></div>
                <div class="small mt-2"><strong>Amount:</strong> <span id="p-amount">$0.00</span></div>
              </div>
              <div class="col-md-5">
                <table class="w-100 ip-totals">
                  <tr><td class="text-end w-50">Subtotal</td><td class="text-end" id="p-subtotal">$0.00</td></tr>
                  <tr><td class="text-end" id="p-discount-label">Discount</td><td class="text-end text-danger" id="p-discount">-$0.00</td></tr>
                  <tr><td class="text-end">Tax</td><td class="text-end" id="p-tax">$0.00</td></tr>
                  <tr class="ip-total"><td class="text-end">Grand Total</td><td class="text-end" id="p-total">$0.00</td></tr>
                </table>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-5">
              <div class="text-end">
                <div class="signature">Signature</div>
                <div class="small" id="p-phone">+92 310 1111571</div>
                <div class="small text-muted">Accounts Manager</div>
              </div>
            </div>

            <div class="mt-4">
              <div class="small"><strong>Thank you for your business.</strong></div>
              <div class="small text-muted">Terms &amp; Conditions</div>
              <div class="small" id="p-terms">—</div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-3">
          <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
          <button type="button" class="btn btn-soft-primary" id="btnSave">Save Invoice</button>
          <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">Back to List</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const wizard = document.querySelector('.wizard');
  const steps  = [...wizard.querySelectorAll('.step')];
  const panes  = [...wizard.querySelectorAll('.pane')];
  const form   = document.getElementById('invoiceForm');

  function setStep(n) {
    steps.forEach(s => s.classList.toggle('active', +s.dataset.step === n));
    panes.forEach(p => p.classList.toggle('active', +p.dataset.pane === n));
    window.scrollTo({ top: wizard.offsetTop - 20, behavior:'smooth' });
  }

  wizard.addEventListener('click', (e) => {
    if (e.target.closest('.js-next')) {
      const cur = +wizard.querySelector('.pane.active').dataset.pane;
      setStep(Math.min(cur + 1, 5));
    }
    if (e.target.closest('.js-prev')) {
      const cur = +wizard.querySelector('.pane.active').dataset.pane;
      setStep(Math.max(cur - 1, 1));
    }
  });

  // ----- Milestones dynamic rows -----
  const holder = document.getElementById('milestones');
  document.getElementById('addMilestone')?.addEventListener('click', () => {
    const tr = document.createElement('tr');
    tr.className = 'milestone';
    tr.innerHTML = `
      <td><input class="form-control" name="milestone_name[]" placeholder="Milestone Name"></td>
      <td><input type="date" class="form-control" name="milestone_date[]"></td>
      <td><input type="number" step="0.01" class="form-control amt" name="milestone_amount[]" placeholder="0.00"></td>
      <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove">Remove</button></td>`;
    holder.appendChild(tr);
    recalc();
  });
  holder?.addEventListener('click', (e) => {
    if (e.target.closest('.js-remove')) { e.target.closest('tr').remove(); recalc(); }
  });
  holder?.addEventListener('input', (e) => {
    if (e.target.classList.contains('amt')) recalc();
  });

  // ----- Totals (single source of truth) -----
  const dTypeEl = document.getElementById('discountType');
  const dValEl  = document.getElementById('discountValue');

  [dTypeEl, dValEl].forEach(el => el?.addEventListener('input', recalc));

  function parseNum(v){ const n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function fmt(n){ return '$' + (parseFloat(n||0)).toFixed(2); }

  function computeTotals() {
    const amts = [...form.querySelectorAll('.amt')].map(i => parseNum(i.value));
    const subtotal = amts.reduce((a,b)=>a+b,0);

    const dType = (dTypeEl?.value || 'percent');
    const dVal  = parseNum(dValEl?.value || 0);
    let discount = dType === 'percent' ? subtotal * (dVal/100) : dVal;
    discount = Math.max(0, Math.min(discount, subtotal)); // clamp to [0, subtotal]

    const tax = 0; // always 0
    const total = Math.max(0, subtotal - discount + tax);

    return { subtotal, discount, tax, total, dType, dVal };
  }

  function updatePane3() {
    const n = computeTotals();
    document.getElementById('t-subtotal').textContent = fmt(n.subtotal);
    document.getElementById('t-discount').textContent = '-' + fmt(n.discount).slice(1);
    document.getElementById('t-tax').textContent      = fmt(0);
    document.getElementById('t-total').textContent    = fmt(n.total);
    document.getElementById('t-discount-label').textContent =
      n.dType === 'percent' ? `Discount (${isNaN(n.dVal)?0:n.dVal}%)` : 'Discount';
  }

  function updatePreview() {
    const n = computeTotals();

    // Client
    document.getElementById('p-client-name').textContent    = form.client_name.value || '—';
    document.getElementById('p-client-email').textContent   = form.client_email.value || '—';
    document.getElementById('p-client-phone').textContent   = form.client_phone.value || '—';
    document.getElementById('p-client-address').textContent = form.client_address.value || '—';

    // Project
    document.getElementById('p-project-title').textContent = form.project_title.value || '—';
    document.getElementById('p-project-desc').textContent  = form.project_description.value || '—';

    // Milestones
    const tbody = document.getElementById('p-milestones-body');
    tbody.innerHTML = '';
    const names = [...form.querySelectorAll('[name="milestone_name[]"]')];
    const dates = [...form.querySelectorAll('[name="milestone_date[]"]')];
    const amts  = [...form.querySelectorAll('[name="milestone_amount[]"]')];

    names.forEach((nEl, idx) => {
      const name = (nEl.value || '').trim();
      const amount = parseNum(amts[idx]?.value);
      const date = dates[idx]?.value || '';
      if(!name && !amount) return;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${name || '—'}</td>
        <td>${date || '—'}</td>
        <td class="text-end">${fmt(amount)}</td>`;
      tbody.appendChild(tr);
    });

    // Payment + totals
    document.getElementById('p-bank').textContent    = form.bank_name.value ? `Bank: ${form.bank_name.value}` : '';
    document.getElementById('p-account').textContent = form.bank_account.value ? `Account: ${form.bank_account.value}` : '';
    document.getElementById('p-holder').textContent  = form.account_holder.value ? `Account Holder: ${form.account_holder.value}` : '';
    document.getElementById('p-terms').textContent   = form.terms.value || '';

    document.getElementById('p-subtotal').textContent   = fmt(n.subtotal);
    document.getElementById('p-discount').textContent   = '-' + fmt(n.discount).slice(1);
    document.getElementById('p-tax').textContent        = fmt(0);
    document.getElementById('p-total').textContent      = fmt(n.total);
    document.getElementById('p-grand-total').textContent= `Grand Total: ${fmt(n.total)}`;
    document.getElementById('p-amount').textContent     = fmt(n.total);
    document.getElementById('p-grand').textContent      = fmt(n.total);
  }

  function recalc(){ updatePane3(); }

  // initial totals
  recalc();

  // Preview button
  document.getElementById('btnPreview').addEventListener('click', () => {
    updatePreview();
    setStep(5);
  });

  // Save
  document.getElementById('btnSave').addEventListener('click', () => form.submit());
})();
</script>
@endsection
