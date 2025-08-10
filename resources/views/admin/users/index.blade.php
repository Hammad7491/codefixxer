@extends('layouts.app')

@push('styles')
<style>
  /* Header gradient */
  .bg-gradient-primary {
    background: linear-gradient(45deg, #0d6efd, #6610f2) !important;
  }

  /* “Light primary” button */
  .btn-light-primary {
    color: #0d6efd;
    background-color: #f0f5ff;
    border: 1px solid #0d6efd;
  }
  .btn-light-primary:hover {
    background-color: #e2ecff;
  }

  /* Striped rows */
  .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: rgba(102,16,242,0.05);
  }

  /* Stronger table header line */
  .table thead th {
    border-bottom-width: 2px;
  }

  /* Custom badge color */
  .badge-role {
    background: #6610f2;
  }
</style>
@endpush

@section('content')
<div class="container my-5">
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-people-fill me-2"></i>
        Users List
      </h4>
      <a href="{{ route('admin.users.create') }}" class="btn btn-light-primary btn-sm d-flex align-items-center">
        <i class="bi bi-person-plus-fill me-1"></i>
        Add New User
      </a>
    </div>

    <div class="card-body p-4">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="bi bi-check-circle-fill me-2 fs-4"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th><i class="bi bi-person-fill me-1"></i>Name</th>
              <th><i class="bi bi-envelope-fill me-1"></i>Email</th>
              <th><i class="bi bi-shield-lock-fill me-1"></i>Roles</th>
              <th class="text-center"><i class="bi bi-gear-fill me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td class="fw-semibold">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach($user->roles as $role)
                    <span class="badge badge-role text-white me-1 rounded-pill">
                      {{ ucfirst($role->name) }}
                    </span>
                  @endforeach
                </td>
                <td class="text-center">
                  <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="btn btn-sm btn-outline-primary me-1"
                    title="Edit"
                  >
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                  <form
                    action="{{ route('admin.users.destroy', $user) }}"
                    method="POST"
                    class="d-inline"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Are you sure you want to delete this user?')"
                      title="Delete"
                    >
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
