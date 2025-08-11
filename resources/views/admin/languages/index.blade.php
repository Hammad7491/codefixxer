{{-- resources/views/admin/languages/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Languages List')

@section('content')
<style>
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .table thead th { color:#475569; font-weight:700; }
  .badge-soft { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
  .btn-rounded { border-radius: 12px; }
  @media (max-width: 768px) { .hide-sm { display:none; } }
</style>

<div class="container-fluid py-3">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Languages</h1>
    <a href="{{ route('admin.languages.create') }}" class="btn btn-primary btn-rounded">
      <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>
      Add Language
    </a>
  </div>

  <div class="card card-soft">
    <div class="card-header">
      <div class="row g-2 align-items-center">
        <div class="col-lg-6">
          <strong>All Languages</strong>
        </div>
        <div class="col-lg-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Search language or proficiency…"
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
              <th style="width:80px">#</th>
              <th>Language</th>
              <th class="hide-sm">Proficiency</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($languages as $language)
              <tr>
                <td>{{ $language->id }}</td>
                <td class="fw-semibold">
                  {{ $language->name }}
                </td>
                <td class="hide-sm">
                  <span class="badge badge-soft">{{ $language->proficiency }}</span>
                </td>
                <td>
                  <a href="{{ route('admin.languages.edit', $language) }}"
                     class="btn btn-sm btn-outline-primary btn-rounded" title="Edit">
                    <iconify-icon icon="mdi:pencil"></iconify-icon>
                  </a>
                  <form action="{{ route('admin.languages.destroy', $language) }}" method="POST"
                        class="d-inline-block"
                        onsubmit="return confirm('Delete this language?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger btn-rounded" type="submit" title="Delete">
                      <iconify-icon icon="mdi:trash-can"></iconify-icon>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">No languages found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer">
      {{ $languages->links() }}
    </div>
  </div>
</div>
@endsection
