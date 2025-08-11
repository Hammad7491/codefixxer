{{-- resources/views/admin/invoices/show.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Invoice $invoice */
  $invNo = $invoice->invoice_number ?: ('#'.$invoice->id);
@endphp

@section('title', 'Invoice '.$invNo)

@section('content')
<style>
  :root { color-scheme: light; }
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .ip-table { width:100%; border-collapse:collapse; }
  .ip-table th, .ip-table td { border:1px solid #e5e7eb; padding:.6rem .75rem; vertical-align: top; }
  .ip-totals td { border:none; padding:.35rem 0; }
  .ip-total { background:#e7f0ff; border:2px solid #bfdbfe; font-weight:800; }
  .badge-soft { background:#e0ebff; color:#1f4fd7; padding:.45rem .75rem; border-radius:999px; font-weight:700; }
  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">Invoice {{ $invNo }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-dark">
        <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon> Back
      </a>
      <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-soft-primary">
        <iconify-icon icon="mdi:pencil" class="me-1"></iconify-icon> Edit
      </a>
      <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice?')" class="d-inline">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">
          <iconify-icon icon="mdi:trash-can-outline" class="me-1"></iconify-icon> Delete
        </button>
      </form>
    </div>
  </div>

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <iconify-icon icon="mdi:file-document-outline"></iconify-icon>
        <strong class="fs-6">Invoice Summary</strong>
      </div>
      <span class="badge-soft">Grand Total: {{ '$'.number_format((float)$invoice->total,2) }}</span>
    </div>

    <div class="card-body p-4">
      {{-- Meta --}}
      <div class="row g-4 mb-3">
        <div class="col-md-6">
          <div class="small text-muted">Invoice No</div>
          <div class="fw-semibold">{{ $invNo }}</div>
          <div class="small text-muted mt-2">Created</div>
          <div>{{ optional($invoice->created_at)->format('d M Y') }}</div>
        </div>
        <div class="col-md-6">
          <div class="small text-muted">Client</div>
          <div class="fw-semibold">{{ $invoice->client_name }}</div>
          <div class="text-muted">{{ $invoice->contact_person }}</div>
          <div class="mt-1 small">{{ $invoice->client_email }}</div>
          <div class="small">{{ $invoice->client_phone }}</div>
          <div class="small">{{ $invoice->client_address }}</div>
        </div>
      </div>

      {{-- Project --}}
      <h6 class="mb-2">Project Details</h6>
      <table class="ip-table mb-3">
        <tr>
          <th style="width:20%">Title</th>
          <td>{{ $invoice->project_title ?: '—' }}</td>
        </tr>
        <tr>
          <th>Description</th>
          <td>{{ $invoice->project_description ?: '—' }}</td>
        </tr>
      </table>

      {{-- Milestones --}}
      <h6 class="mb-2">Milestones</h6>
      <div class="table-responsive">
        <table class="ip-table mb-3">
          <thead>
            <tr>
              <th style="width:55%">Milestone</th>
              <th style="width:25%">Due Date</th>
              <th style="width:20%" class="text-end">Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoice->milestones as $m)
              <tr>
                <td>{{ $m->name ?: '—' }}</td>
                <td>{{ optional($m->due_date)->format('d M Y') ?: '—' }}</td>
                <td class="text-end">{{ '$'.number_format((float)$m->amount,2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center py-4 text-muted">No milestones added.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Totals --}}
      <div class="row g-4">
        <div class="col-md-7">
          <div class="small"><strong>Payment info:</strong></div>
          @if($invoice->bank_name)<div class="small">Bank: {{ $invoice->bank_name }}</div>@endif
          @if($invoice->bank_account)<div class="small">Account: {{ $invoice->bank_account }}</div>@endif
          @if($invoice->account_holder)<div class="small">Account Holder: {{ $invoice->account_holder }}</div>@endif
          <div class="small mt-2"><strong>Amount Due:</strong> {{ '$'.number_format((float)$invoice->total,2) }}</div>

          <div class="small text-muted mt-3"><strong>Terms &amp; Conditions</strong></div>
          <div class="small">{{ $invoice->terms ?: '—' }}</div>
        </div>
        <div class="col-md-5">
          <table class="w-100 ip-totals">
            <tr><td class="text-end w-50">Subtotal</td><td class="text-end">{{ '$'.number_format((float)$invoice->subtotal,2) }}</td></tr>
            <tr><td class="text-end">{{ $invoice->discount_name ? 'Discount ('.$invoice->discount_name.')' : 'Discount' }}</td><td class="text-end text-danger">-{{ '$'.number_format((float)$invoice->discount_amount,2) }}</td></tr>
            <tr><td class="text-end">Tax</td><td class="text-end">{{ '$'.number_format(0,2) }}</td></tr>
            <tr class="ip-total"><td class="text-end">Grand Total</td><td class="text-end">{{ '$'.number_format((float)$invoice->total,2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
