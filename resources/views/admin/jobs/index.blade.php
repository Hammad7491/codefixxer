@extends('layouts.app')

@section('title', 'Jobs List')

@section('content')
<style>
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .table thead th { color:#475569; font-weight:700; }
  .badge-soft { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
  .btn-rounded { border-radius: 12px; }
  @media (max-width: 768px){
    .hide-sm { display:none; }
  }
</style>

<div class="container-fluid py-3">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Jobs</h1>
    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary btn-rounded">
      <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>
      Add Job
    </a>
  </div>

  <div class="card card-soft">
    <div class="card-header">
      <div class="row g-2 align-items-center">
        <div class="col-md-6">
          <strong>All Jobs</strong>
        </div>
        <div class="col-md-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Search title, organization, location…"
                   name="q" value="{{ $q ?? '' }}">
            <button class="btn btn-light border btn-rounded" type="submit">
              <iconify-icon icon="mdi:magnify" class="me-1"></iconify-icon> Search
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:60px">#</th>
              <th>Title</th>
              <th class="hide-sm">Organization</th>
              <th>Type</th>
              <th class="hide-sm">Start</th>
              <th class="hide-sm">End</th>
              <th class="hide-sm">Location</th>
              <th>Tools</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($jobs as $job)
              <tr>
                <td>{{ $job->id }}</td>
                <td>{{ $job->title }}</td>
                <td class="hide-sm">{{ $job->organization_name }}</td>
                <td>{{ $job->employment_type }}</td>
                <td class="hide-sm">{{ $job->start_date?->format('Y-m-d') }}</td>
                <td class="hide-sm">{{ $job->end_date ? $job->end_date->format('Y-m-d') : 'Present' }}</td>
                <td class="hide-sm">{{ $job->location ?? '—' }}</td>
                <td>
                  @php
                    // Robust decode: array, JSON string, or CSV string
                    $raw = $job->tools_used;
                    if (is_string($raw)) {
                      $json = json_decode($raw, true);
                      if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                        $tools = $json;
                      } else {
                        $tools = array_filter(array_map('trim', explode(',', $raw)));
                      }
                    } else {
                      $tools = is_array($raw) ? $raw : [];
                    }
                  @endphp
                  @if(count($tools))
                    @foreach($tools as $t)
                      <span class="badge badge-soft me-1 mb-1">{{ $t }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary btn-rounded" title="Edit">
                    <iconify-icon icon="mdi:pencil"></iconify-icon>
                  </a>
                  <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Delete this job?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger btn-rounded" type="submit" title="Delete">
                      <iconify-icon icon="mdi:trash-can"></iconify-icon>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">No jobs found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer">
      {{ $jobs->links() }}
    </div>
  </div>
</div>
@endsection
