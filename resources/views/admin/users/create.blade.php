@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-3">
    <div class="card-header bg-primary text-white d-flex align-items-center">
      <h3 class="mb-0">
        @if(isset($user))
          <i class="fas fa-user-edit me-2"></i>Edit User
        @else
          <i class="fas fa-user-plus me-2"></i>Add New User
        @endif
      </h3>
    </div>

    <div class="card-body">
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
        action="{{ isset($user)
                   ? route('admin.users.update', $user)
                   : route('admin.users.store') }}"
        method="POST"
      >
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                placeholder="Name"
                value="{{ old('name', $user->name ?? '') }}"
                required
              >
              <label for="name">
                <i class="bi bi-person-fill me-1"></i>Name
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Email"
                value="{{ old('email', $user->email ?? '') }}"
                required
              >
              <label for="email">
                <i class="bi bi-envelope-fill me-1"></i>Email
              </label>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Password"
                {{ isset($user) ? '' : 'required' }}
              >
              <label for="password">
                <i class="bi bi-lock-fill me-1"></i>
                {{ isset($user)
                   ? 'New Password (leave blank to keep)'
                   : 'Password' }}
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Confirm Password"
                {{ isset($user) ? '' : 'required' }}
              >
              <label for="password_confirmation">
                <i class="bi bi-lock-fill me-1"></i>Confirm Password
              </label>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <label for="roles" class="form-label">
            <i class="fas fa-user-shield me-1"></i>Roles
          </label>
          <select
            id="roles"
            name="roles[]"
            class="form-select"
            multiple
            required
          >
            @foreach($roles as $role)
              <option
                value="{{ $role->name }}"
                {{ in_array($role->name, $userRoles ?? []) ? 'selected' : '' }}
              >
                {{ ucfirst($role->name) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn btn-success me-2">
            <i class="fas fa-save me-1"></i>
            {{ isset($user) ? 'Update User' : 'Create User' }}
          </button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i>Back to List
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
