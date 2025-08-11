@extends('layouts.app')

@php
  /** @var \App\Models\Contract|null $contract */
  $contract = $contract ?? null;
  $isEdit   = (bool) $contract;
@endphp

@section('title', $isEdit ? 'Edit Contract' : 'Add Contract')

@section('content')
<style>
  :root { color-scheme: light; }
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }

  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select {
    border-radius: 12px; border-color:#e2e8f0; background:#fff!important; color:#0f172a!important;
  }
  .form-control::placeholder { color:#64748b; }
  .form-control:focus, .form-select:focus { border-color:#6366f1; box-shadow:0 0 0 .2rem rgba(99,102,241,.15); }
  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; }
  .btn-outline-dark, .btn-outline-secondary, .btn-outline-danger { border-radius:12px; }

  /* wizard */
  .wizard { border-top:1px solid rgba(148,163,184,.15); }
  .steps { display:flex; gap:1rem; padding:1rem 1.25rem; background:#f8fafc; border-bottom:1px solid #e5e7eb;}
  .step { flex:1; display:flex; align-items:center; gap:.75rem; padding:.6rem .8rem; border-radius:12px;}
  .step .dot{ width:32px;height:32px;display:grid;place-items:center;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:700;}
  .step.active{ background:#eef2ff; } .step.active .dot{ background:#4f46e5;color:#fff;}
  .step small{ color:#6b7280; display:block; margin-top:.15rem; }
  .pane{display:none; padding:1.25rem;} .pane.active{display:block;}

  .ms-table th,.ms-table td{vertical-align:middle;} .ms-table input, .ms-table textarea{ height:44px; }
  .ip-table{width:100%; border-collapse:collapse;} .ip-table th,.ip-table td{border:1px solid #e5e7eb;padding:.6rem .75rem;}
  .ip-total{background:#e7f0ff;border:2px solid #bfdbfe;font-weight:800;}
  .ip-badge{background:#e0ebff;color:#1f4fd7;padding:.5rem 1rem;border-radius:999px;font-weight:700;}
  .signature{font-family:cursive;font-size:1.6rem;}
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Contract' : 'Add Contract' }}</h1>
    <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-dark">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon> Contracts List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:file-document-edit-outline"></iconify-icon>
      <strong class="fs-6">Contract Wizard</strong>
    </div>

    <div class="wizard">
      <div class="steps">
        <div class="step active" data-step="1"><span class="dot">1</span><div><div class="fw-semibold">Introduction & Details</div><small>Contract & Client</small></div></div>
        <div class="step" data-step="2"><span class="dot">2</span><div><div class="fw-semibold">Additional Info</div><small>Timeline & Payments</small></div></div>
        <div class="step" data-step="3"><span class="dot">3</span><div><div class="fw-semibold">Additional Info</div><small>Terms</small></div></div>
        <div class="step" data-step="4"><span class="dot">4</span><div><div class="fw-semibold">Additional Info</div><small>Legal</small></div></div>
      </div>

      <form method="POST"
            action="{{ $isEdit ? route('admin.contracts.update', $contract) : route('admin.contracts.store') }}"
            id="contractForm" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Pane 1 --}}
        <div class="pane active" data-pane="1">
          <h6 class="mb-3">Create Contract: Introduction & Client Details</h6>
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label">Contract Title</label>
              <input class="form-control" name="title" placeholder="e.g., Freelance Services Agreement"
                     value="{{ old('title', $contract->title ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Purpose</label>
              <textarea class="form-control" name="purpose" rows="3" placeholder="Briefly explain the purpose of the contract">{{ old('purpose', $contract->purpose ?? '') }}</textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Client Name</label>
              <input class="form-control" name="client_name" placeholder="Client's Full Name"
                     value="{{ old('client_name', $contract->client_name ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Client Email</label>
              <input type="email" class="form-control" name="client_email" placeholder="Client's Email Address"
                     value="{{ old('client_email', $contract->client_email ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Client Phone</label>
              <input class="form-control" name="client_phone" placeholder="Client's Phone Number"
                     value="{{ old('client_phone', $contract->client_phone ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Client Address</label>
              <textarea class="form-control" name="client_address" rows="1" placeholder="Client's Address">{{ old('client_address', $contract->client_address ?? '') }}</textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Start Date</label>
              <input type="date" class="form-control" name="start_date"
                     value="{{ old('start_date', optional($contract?->start_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">End Date</label>
              <input type="date" class="form-control" name="end_date"
                     value="{{ old('end_date', optional($contract?->end_date)->format('Y-m-d')) }}">
            </div>
          </div>

          <div class="mt-4">
            <h6 class="mb-2">Milestones</h6>
            <div class="table-responsive">
              <table class="table table-bordered ms-table">
                <thead class="table-light">
                  <tr>
                    <th style="width:28%">Milestone Name</th>
                    <th style="width:37%">Milestone Desc.</th>
                    <th style="width:20%">Due Date</th>
                    <th style="width:10%">Amount</th>
                    <th style="width:5%">Action</th>
                  </tr>
                </thead>
                <tbody id="milestones">
                  @php
                    $mNames = old('milestone_name',  $contract?->milestones?->pluck('name')->toArray() ?? ['']);
                    $mDescs = old('milestone_desc',  $contract?->milestones?->pluck('description')->toArray() ?? ['']);
                    $mDates = old('milestone_date',  $contract?->milestones?->pluck('due_date')->map(fn($d)=>optional($d)->format('Y-m-d'))->toArray() ?? ['']);
                    $mAmts  = old('milestone_amount',$contract?->milestones?->pluck('amount')->toArray() ?? ['']);
                    $rows   = max(1, count($mNames));
                  @endphp
                  @for($i=0; $i<$rows; $i++)
                  <tr class="ms-row">
                    <td><input class="form-control" name="milestone_name[]"  value="{{ $mNames[$i] ?? '' }}"  placeholder="Milestone Name"></td>
                    <td><input class="form-control" name="milestone_desc[]"  value="{{ $mDescs[$i] ?? '' }}"  placeholder="Milestone Desc"></td>
                    <td><input type="date" class="form-control" name="milestone_date[]" value="{{ $mDates[$i] ?? '' }}"></td>
                    <td><input type="number" step="0.01" class="form-control amt" name="milestone_amount[]" value="{{ $mAmts[$i] ?? '' }}" placeholder="Amount"></td>
                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove">Remove</button></td>
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>
            <button class="btn btn-soft-primary btn-sm" type="button" id="addMilestone">
              <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon> Add Milestone
            </button>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 2 --}}
        <div class="pane" data-pane="2">
          <h6 class="mb-3">Timeline, Deliverables, and Payment Terms</h6>
          <div class="mb-3">
            <label class="form-label">Project Timeline</label>
            <textarea class="form-control" name="project_timeline" rows="4" placeholder="Briefly describe the timeline of the project">{{ old('project_timeline', $contract->project_timeline ?? '') }}</textarea>
          </div>
          <div>
            <label class="form-label">Payment Terms</label>
            <textarea class="form-control" name="payment_terms" rows="4" placeholder="Specify payment terms (e.g., 50% upfront, 50% upon completion)">{{ old('payment_terms', $contract->payment_terms ?? '') }}</textarea>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 3 --}}
        <div class="pane" data-pane="3">
          <h6 class="mb-3">Additional Terms and Conditions</h6>
          <div class="mb-3">
            <label class="form-label">Revisions</label>
            <textarea class="form-control" name="revisions" rows="3">{{ old('revisions', $contract->revisions ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Ownership and Intellectual Property</label>
            <textarea class="form-control" name="ownership_ip" rows="3">{{ old('ownership_ip', $contract->ownership_ip ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Confidentiality</label>
            <textarea class="form-control" name="confidentiality" rows="3">{{ old('confidentiality', $contract->confidentiality ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Client Responsibilities</label>
            <textarea class="form-control" name="client_responsibilities" rows="3">{{ old('client_responsibilities', $contract->client_responsibilities ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Termination Clause</label>
            <textarea class="form-control" name="termination_clause" rows="3">{{ old('termination_clause', $contract->termination_clause ?? '') }}</textarea>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary js-next">Next</button>
          </div>
        </div>

        {{-- Pane 4 --}}
        <div class="pane" data-pane="4">
          <h6 class="mb-3">Legal and Agreement Terms</h6>
          <div class="mb-3">
            <label class="form-label">Dispute Resolution</label>
            <textarea class="form-control" name="dispute_resolution" rows="3" placeholder="Describe how disputes will be resolved">{{ old('dispute_resolution', $contract->dispute_resolution ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Limitation of Liability</label>
            <textarea class="form-control" name="limitation_of_liability" rows="3">{{ old('limitation_of_liability', $contract->limitation_of_liability ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Amendments</label>
            <textarea class="form-control" name="amendments" rows="3" placeholder="Explain how changes will be handled">{{ old('amendments', $contract->amendments ?? '') }}</textarea>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
            <button type="button" class="btn btn-soft-primary" id="btnPreview">Submit</button>
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
                <div><strong>support@example.com</strong></div>
                <div class="text-muted">info@yourcompany.com</div>
              </div>
            </div>
            <div class="ip-badge" id="p-total">Total Cost: $0.00</div>
          </div>

          <div class="p-3">
            <div class="row g-3 mb-2">
              <div class="col-md-6 small">
                <div><strong>Contract #:</strong> <span id="p-number">#AUTO</span></div>
                <div class="mt-1"><strong>Created On:</strong> {{ now()->format('d M Y') }}</div>
                <div class="mt-1"><strong>Effective From:</strong> <span id="p-start">—</span></div>
                <div class="mt-1"><strong>Until:</strong> <span id="p-end">—</span></div>
              </div>
            </div>

            <div class="mb-2"><em id="p-title">—</em></div>
            <div class="small text-muted mb-3">Purpose: <span id="p-purpose">—</span></div>

            <h6>Client Information</h6>
            <table class="ip-table mb-3">
              <tr>
                <td><strong>Name:</strong> <span id="p-client-name">—</span></td>
                <td><strong>Email:</strong> <span id="p-client-email">—</span></td>
              </tr>
              <tr>
                <td><strong>Phone:</strong> <span id="p-client-phone">—</span></td>
                <td><strong>Address:</strong> <span id="p-client-address">—</span></td>
              </tr>
            </table>

            <h6>Milestones</h6>
            <table class="ip-table mb-3">
              <thead><tr>
                <th style="width:35%">Name</th>
                <th style="width:35%">Description</th>
                <th style="width:20%">Due Date</th>
                <th style="width:10%" class="text-end">Amount</th>
              </tr></thead>
              <tbody id="p-ms-body"></tbody>
            </table>

            <h6>Contract Details</h6>
            <div class="mb-2"><strong>1. Project Timeline</strong><div class="small" id="p-timeline">—</div></div>
            <div class="mb-2"><strong>2. Payment Terms</strong><div class="small" id="p-payterms">—</div></div>
            <div class="mb-2"><strong>3. Revisions</strong><div class="small" id="p-revisions">—</div></div>
            <div class="mb-2"><strong>4. Ownership & Intellectual Property</strong><div class="small" id="p-ip">—</div></div>
            <div class="mb-2"><strong>5. Confidentiality</strong><div class="small" id="p-conf">—</div></div>
            <div class="mb-2"><strong>6. Client Responsibilities</strong><div class="small" id="p-resp">—</div></div>
            <div class="mb-2"><strong>7. Termination Clause</strong><div class="small" id="p-term">—</div></div>
            <div class="mb-2"><strong>8. Dispute Resolution</strong><div class="small" id="p-dispute">—</div></div>
            <div class="mb-2"><strong>9. Limitation of Liability</strong><div class="small" id="p-limit">—</div></div>
            <div class="mb-2"><strong>10. Amendments</strong><div class="small" id="p-amend">—</div></div>

            <div class="row mt-3">
              <div class="col-md-7"></div>
              <div class="col-md-5">
                <table class="w-100">
                  <tr class="ip-total"><td class="text-end">Total Cost</td><td class="text-end" id="p-total2">$0.00</td></tr>
                </table>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-5">
              <div class="text-end">
                <div class="signature">Signature</div>
                <div class="small text-muted">Authorized Signatory</div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-3">
              <button type="button" class="btn btn-outline-dark js-prev">Previous</button>
              <button type="button" class="btn btn-soft-primary" id="btnSave">Save Contract</button>
            </div>
          </div>
        </div>
      </div> {{-- wizard --}}
    </div>
  </div>
</div>

<script>
(() => {
  const wizard = document.querySelector('.wizard');
  const steps  = [...wizard.querySelectorAll('.step')];
  const panes  = [...wizard.querySelectorAll('.pane')];
  const form   = document.getElementById('contractForm');

  function setStep(n){
    steps.forEach(s => s.classList.toggle('active', +s.dataset.step === n));
    panes.forEach(p => p.classList.toggle('active', +p.dataset.pane === n));
    window.scrollTo({ top: wizard.offsetTop - 20, behavior:'smooth' });
  }

  wizard.addEventListener('click', (e) => {
    if (e.target.closest('.js-next')) {
      const cur = +wizard.querySelector('.pane.active').dataset.pane;
      setStep(Math.min(cur+1, 5));
    }
    if (e.target.closest('.js-prev')) {
      const cur = +wizard.querySelector('.pane.active').dataset.pane;
      setStep(Math.max(cur-1, 1));
    }
  });

  // Dynamic milestones
  const holder = document.getElementById('milestones');
  document.getElementById('addMilestone')?.addEventListener('click', () => {
    const tr = document.createElement('tr');
    tr.className = 'ms-row';
    tr.innerHTML = `
      <td><input class="form-control" name="milestone_name[]"  placeholder="Milestone Name"></td>
      <td><input class="form-control" name="milestone_desc[]"  placeholder="Milestone Desc"></td>
      <td><input type="date" class="form-control" name="milestone_date[]"></td>
      <td><input type="number" step="0.01" class="form-control amt" name="milestone_amount[]" placeholder="Amount"></td>
      <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove">Remove</button></td>`;
    holder.appendChild(tr);
  });
  holder?.addEventListener('click', (e) => {
    if (e.target.closest('.js-remove')) e.target.closest('tr').remove();
  });

  function parseNum(v){ const n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function fmt(n){ return '$' + (parseFloat(n||0)).toFixed(2); }
  function sumTotal(){
    return [...document.querySelectorAll('.amt')].reduce((a,i)=>a+parseNum(i.value),0);
  }

  // Preview
  document.getElementById('btnPreview').addEventListener('click', () => {
    const total = sumTotal();

    // header & meta
    document.getElementById('p-total').textContent  = 'Total Cost: ' + fmt(total);
    document.getElementById('p-total2').textContent = fmt(total);
    document.getElementById('p-title').textContent  = form.title.value || '—';
    document.getElementById('p-purpose').textContent= form.purpose.value || '—';
    document.getElementById('p-start').textContent  = form.start_date.value || '—';
    document.getElementById('p-end').textContent    = form.end_date.value || '—';

    // client
    document.getElementById('p-client-name').textContent    = form.client_name.value || '—';
    document.getElementById('p-client-email').textContent   = form.client_email.value || '—';
    document.getElementById('p-client-phone').textContent   = form.client_phone.value || '—';
    document.getElementById('p-client-address').textContent = form.client_address.value || '—';

    // details
    document.getElementById('p-timeline').textContent = form.project_timeline.value || '—';
    document.getElementById('p-payterms').textContent = form.payment_terms.value || '—';
    document.getElementById('p-revisions').textContent= form.revisions.value || '—';
    document.getElementById('p-ip').textContent       = form.ownership_ip.value || '—';
    document.getElementById('p-conf').textContent     = form.confidentiality.value || '—';
    document.getElementById('p-resp').textContent     = form.client_responsibilities.value || '—';
    document.getElementById('p-term').textContent     = form.termination_clause.value || '—';
    document.getElementById('p-dispute').textContent  = form.dispute_resolution.value || '—';
    document.getElementById('p-limit').textContent    = form.limitation_of_liability.value || '—';
    document.getElementById('p-amend').textContent    = form.amendments.value || '—';

    // milestones table
    const tbody = document.getElementById('p-ms-body');
    tbody.innerHTML = '';
    const names = [...form.querySelectorAll('[name="milestone_name[]"]')];
    const descs = [...form.querySelectorAll('[name="milestone_desc[]"]')];
    const dates = [...form.querySelectorAll('[name="milestone_date[]"]')];
    const amts  = [...form.querySelectorAll('[name="milestone_amount[]"]')];
    names.forEach((n, i) => {
      const amount = parseNum(amts[i]?.value);
      if (!n.value && !amount) return;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${n.value || '—'}</td>
        <td>${descs[i]?.value || '—'}</td>
        <td>${dates[i]?.value || '—'}</td>
        <td class="text-end">${fmt(amount)}</td>`;
      tbody.appendChild(tr);
    });

    setStep(5);
  });

  document.getElementById('btnSave').addEventListener('click', () => form.submit());
})();
</script>
@endsection
