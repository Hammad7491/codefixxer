{{-- resources/views/admin/educations/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Education List')

@section('content')
<style>
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .table thead th { color:#475569; font-weight:700; }
  .badge-soft { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
  .btn-rounded { border-radius: 12px; }
  .link-muted { color:#475569; text-decoration: none; }
  .link-muted:hover { text-decoration: underline; }
  @media (max-width: 992px) { .hide-md { display:none; } }
  @media (max-width: 768px) { .hide-sm { display:none; } }
</style>

<div class="container-fluid py-3">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Education</h1>
    <a href="{{ route('admin.educations.create') }}" class="btn btn-primary btn-rounded">
      <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>
      Add Education
    </a>
  </div>

  <div class="card card-soft">
    <div class="card-header">
      <div class="row g-2 align-items-center">
        <div class="col-lg-6">
          <strong>All Education</strong>
        </div>
        <div class="col-lg-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Search degree, institute, field, location…"
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
              <th>Degree</th>
              <th class="hide-sm">Institute</th>
              <th class="hide-md">Field</th>
              <th class="hide-md">Duration</th>
              <th class="hide-sm">Location</th>
              <th>Certifications / Projects</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($educations as $edu)
              <tr>
                <td>{{ $edu->id }}</td>
                <td class="fw-semibold">{{ $edu->degree_name }}</td>
                <td class="hide-sm">{{ $edu->institute_name }}</td>
                <td class="hide-md">{{ $edu->field_of_study }}</td>
                <td class="hide-md">
                  @php
                    $sd = optional($edu->start_date)->format('Y-m-d');
                    $ed = optional($edu->end_date)->format('Y-m-d');
                  @endphp
                  {{ $sd }} – {{ $ed ?: 'Present' }}
                </td>
                <td class="hide-sm">{{ $edu->location ?: '—' }}</td>
                <td>
                  @php
                    $certs = is_array($edu->certifications)
                      ? $edu->certifications
                      : (is_string($edu->certifications)
                          ? (json_decode($edu->certifications, true) ?? array_filter(array_map('trim', explode(',', $edu->certifications))))
                          : []);
                  @endphp
                  @if(count($certs))
                    @foreach($certs as $c)
                      <span class="badge badge-soft me-1 mb-1">{{ $c }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.educations.edit', $edu) }}"
                     class="btn btn-sm btn-outline-primary btn-rounded" title="Edit">
                    <iconify-icon icon="mdi:pencil"></iconify-icon>
                  </a>
                  <form action="{{ route('admin.educations.destroy', $edu) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Delete this education entry?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger btn-rounded" type="submit" title="Delete">
                      <iconify-icon icon="mdi:trash-can"></iconify-icon>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No education records found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer">
      {{ $educations->links() }}
    </div>
  </div>
</div>
@endsection
