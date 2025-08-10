@extends('layouts.app')

@push('styles')
<style>
  /* Header gradient */
  .bg-gradient-primary {
    background: linear-gradient(45deg, #0d6efd, #6610f2) !important;
  }

  /* Form card */
  .card-form {
    border: none;
  }

  /* Floating labels focus */
  .form-floating .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
  }

  /* Permission cards */
  .perm-card {
    border: 1px solid #e9ecef;
    transition: border-color .2s;
  }
  .perm-card:hover {
    border-color: #6610f2;
  }

  /* Save button */
  .btn-success {
    background-color: #198754;
    border: none;
  }
  .btn-success:hover {
    background-color: #157347;
  }
</style>
@endpush

@section('content')
<div class="container my-5">
  <div class="card shadow-lg rounded-3 card-form">
    <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-shield-lock-fill me-2"></i>
        {{ isset($role) ? 'Edit Role' : 'New Role' }}
      </h4>
    </div>
    <div class="card-body p-4">
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form
        method="POST"
        action="{{ isset($role)
                   ? route('admin.roles.update', $role)
                   : route('admin.roles.store') }}"
      >
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="mb-4 form-floating">
          <input
            type="text"
            id="name"
            name="name"
            class="form-control"
            placeholder="Role Name"
            value="{{ old('name', $role->name ?? '') }}"
            required
          >
          <label for="name">
            <i class="bi bi-tag-fill me-1"></i>
            Role Name
          </label>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold">
            <i class="bi bi-lock-fill me-1"></i>
            Permissions
          </label>
          <div class="row gy-3">
            @foreach($permissions as $perm)
              @php
                $checked = old('permissions')
                  ? in_array($perm->name, old('permissions'))
                  : (isset($rolePermissions) && in_array($perm->name, $rolePermissions));
              @endphp
              <div class="col-6 col-md-4 col-lg-3">
                <div class="card perm-card h-100 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <input
                      class="form-check-input me-2"
                      type="checkbox"
                      id="perm-{{ $perm->id }}"
                      name="permissions[]"
                      value="{{ $perm->name }}"
                      {{ $checked ? 'checked' : '' }}
                    >
                    <label
                      class="form-check-label mb-0 text-truncate"
                      for="perm-{{ $perm->id }}"
                    >
                      {{ ucfirst($perm->name) }}
                    </label>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-success me-2">
            <i class="bi bi-save-fill me-1"></i>
            {{ isset($role) ? 'Update Role' : 'Create Role' }}
          </button>
          <a
            href="{{ route('admin.roles.index') }}"
            class="btn btn-outline-secondary d-flex align-items-center"
          >
            <i class="bi bi-x-circle-fill me-1"></i>
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
