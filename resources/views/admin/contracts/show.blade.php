@extends('layouts.app')

@php $no = $contract->contract_number ?: '#'.$contract->id; @endphp
@section('title', 'Contract '.$no)

@section('content')
<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">Contract {{ $no }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-dark">Back</a>
      <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-primary">Edit</a>
      <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contract?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Delete</button>
      </form>
    </div>
  </div>

  <div class="card card-soft">
    <div class="card-header d-flex justify-content-between">
      <strong>Summary</strong>
      <span class="ip-badge">Total: {{ '$'.number_format((float)$contract->total_cost,2) }}</span>
    </div>
    <div class="card-body p-4">
      <div class="row g-3 mb-3">
        <div class="col-md-6 small">
          <div><strong>Title:</strong> {{ $contract->title ?: '—' }}</div>
          <div class="mt-1"><strong>Purpose:</strong> {{ $contract->purpose ?: '—' }}</div>
          <div class="mt-1"><strong>Start:</strong> {{ optional($contract->start_date)->format('d M Y') ?: '—' }}</div>
          <div class="mt-1"><strong>End:</strong> {{ optional($contract->end_date)->format('d M Y') ?: '—' }}</div>
        </div>
        <div class="col-md-6 small">
          <div><strong>Client:</strong> {{ $contract->client_name ?: '—' }}</div>
          <div><strong>Email:</strong> {{ $contract->client_email ?: '—' }}</div>
          <div><strong>Phone:</strong> {{ $contract->client_phone ?: '—' }}</div>
          <div><strong>Address:</strong> {{ $contract->client_address ?: '—' }}</div>
        </div>
      </div>

      <h6>Milestones</h6>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light"><tr>
            <th style="width:35%">Name</th><th style="width:35%">Description</th>
            <th style="width:15%">Due Date</th><th style="width:15%" class="text-end">Amount</th>
          </tr></thead>
          <tbody>
          @forelse($contract->milestones as $m)
            <tr>
              <td>{{ $m->name ?: '—' }}</td>
              <td>{{ $m->description ?: '—' }}</td>
              <td>{{ optional($m->due_date)->format('d M Y') ?: '—' }}</td>
              <td class="text-end">{{ '$'.number_format((float)$m->amount,2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center py-4 text-muted">No milestones.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-md-7">
          <div class="mb-2"><strong>Project Timeline</strong><div class="small">{{ $contract->project_timeline ?: '—' }}</div></div>
          <div class="mb-2"><strong>Payment Terms</strong><div class="small">{{ $contract->payment_terms ?: '—' }}</div></div>
          <div class="mb-2"><strong>Revisions</strong><div class="small">{{ $contract->revisions ?: '—' }}</div></div>
          <div class="mb-2"><strong>Ownership & IP</strong><div class="small">{{ $contract->ownership_ip ?: '—' }}</div></div>
          <div class="mb-2"><strong>Confidentiality</strong><div class="small">{{ $contract->confidentiality ?: '—' }}</div></div>
          <div class="mb-2"><strong>Client Responsibilities</strong><div class="small">{{ $contract->client_responsibilities ?: '—' }}</div></div>
          <div class="mb-2"><strong>Termination Clause</strong><div class="small">{{ $contract->termination_clause ?: '—' }}</div></div>
          <div class="mb-2"><strong>Dispute Resolution</strong><div class="small">{{ $contract->dispute_resolution ?: '—' }}</div></div>
          <div class="mb-2"><strong>Limitation of Liability</strong><div class="small">{{ $contract->limitation_of_liability ?: '—' }}</div></div>
          <div class="mb-2"><strong>Amendments</strong><div class="small">{{ $contract->amendments ?: '—' }}</div></div>
        </div>
        <div class="col-md-5">
          <table class="w-100">
            <tr class="ip-total"><td class="text-end">Total Cost</td><td class="text-end">{{ '$'.number_format((float)$contract->total_cost,2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
