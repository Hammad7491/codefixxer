@extends('layouts.app')

@section('title', 'Skills List')

@section('content')
<style>
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .table thead th { color:#475569; font-weight:700; }
  .badge-soft { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
  .btn-rounded { border-radius: 12px; }
</style>

<div class="container-fluid py-3">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Skills</h1>
    <a href="{{ route('admin.skills.create') }}" class="btn btn-primary btn-rounded">
      <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>
      Add Skill
    </a>
  </div>

  <div class="card card-soft">
    <div class="card-header">
      <div class="row g-2 align-items-center">
        <div class="col-md-6">
          <strong>All Skills</strong>
        </div>
        <div class="col-md-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Search name or category…" name="q" value="{{ $q }}">
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
              <th>Name</th>
              <th>Category</th>
              <th>Exp (yrs)</th>
              <th>Tools</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($skills as $skill)
              <tr>
                <td>{{ $skill->id }}</td>
                <td>{{ $skill->first_name }} {{ $skill->last_name }}</td>
                <td>{{ $skill->category }}</td>
                <td>{{ $skill->experience_years }}</td>
                <td>
                  @php
                    // Robust tools decode: array, JSON string, or CSV string
                    $toolsRaw = $skill->tools;
                    if (is_string($toolsRaw)) {
                      $json = json_decode($toolsRaw, true);
                      if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                        $tools = $json;
                      } else {
                        $tools = array_filter(array_map('trim', explode(',', $toolsRaw)));
                      }
                    } else {
                      $tools = is_array($toolsRaw) ? $toolsRaw : [];
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
                  <a href="{{ route('admin.skills.edit', $skill) }}" class="btn btn-sm btn-outline-primary btn-rounded">
                    <iconify-icon icon="mdi:pencil"></iconify-icon>
                  </a>
                  <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Delete this skill?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger btn-rounded" type="submit">
                      <iconify-icon icon="mdi:trash-can"></iconify-icon>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No skills found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer">
      {{ $skills->links() }}
    </div>
  </div>
</div>
@endsection
