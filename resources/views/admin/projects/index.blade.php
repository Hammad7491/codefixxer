@extends('layouts.app')

@section('title', 'Projects List')

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

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Projects</h1>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-rounded">
      <iconify-icon icon="mdi:plus" class="me-1"></iconify-icon>
      Add Project
    </a>
  </div>

  <div class="card card-soft">
    <div class="card-header">
      <div class="row g-2 align-items-center">
        <div class="col-lg-6">
          <strong>All Projects</strong>
        </div>
        <div class="col-lg-6">
          <form method="GET" class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Search name, type, client…"
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
              <th>Name</th>
              <th class="hide-sm">Type</th>
              <th class="hide-sm">Client</th>
              <th class="hide-md">Duration</th>
              <th class="hide-sm">Live</th>
              <th>Tools</th>
              <th class="hide-md">Files</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($projects as $project)
              <tr>
                <td>{{ $project->id }}</td>
                <td class="fw-semibold">{{ $project->name }}</td>
                <td class="hide-sm">{{ $project->type }}</td>
                <td class="hide-sm">{{ $project->client }}</td>
                <td class="hide-md">{{ $project->duration_weeks }} wk</td>
                <td class="hide-sm">
                  @if($project->live_link)
                    <a href="{{ $project->live_link }}" target="_blank" class="link-muted">
                      <iconify-icon icon="mdi:open-in-new"></iconify-icon> Open
                    </a>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @php
                    $tools = is_array($project->tools_used)
                      ? $project->tools_used
                      : (is_string($project->tools_used)
                          ? (json_decode($project->tools_used, true) ?? array_filter(array_map('trim', explode(',', $project->tools_used))))
                          : []);
                  @endphp
                  @if(count($tools))
                    @foreach($tools as $t)
                      <span class="badge badge-soft me-1 mb-1">{{ $t }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="hide-md">
                  <div class="d-flex gap-2 flex-wrap">
                    {{-- Video --}}
                    @if($project->video_path)
                      <a href="{{ route('admin.projects.media', [$project,'video']) }}"
                         target="_blank" class="btn btn-sm btn-outline-secondary btn-rounded" title="Video">
                        <iconify-icon icon="mdi:play-circle-outline"></iconify-icon>
                      </a>
                    @endif
                    {{-- Documentation --}}
                    @if($project->documentation_path)
                      <a href="{{ route('admin.projects.media', [$project,'doc']) }}"
                         target="_blank" class="btn btn-sm btn-outline-secondary btn-rounded" title="Documentation">
                        <iconify-icon icon="mdi:file-document-outline"></iconify-icon>
                      </a>
                    @endif
                    {{-- Images count / preview --}}
                    @if(is_array($project->images) && count($project->images))
                      <a href="{{ route('admin.projects.media', [$project,'image',0]) }}"
                         target="_blank" class="badge text-bg-light border" title="View image">
                        <iconify-icon icon="mdi:image-multiple-outline" class="me-1"></iconify-icon>
                        {{ count($project->images) }}
                      </a>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </div>
                </td>
                <td>
                  <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-outline-primary btn-rounded" title="Edit">
                    <iconify-icon icon="mdi:pencil"></iconify-icon>
                  </a>
                  <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Delete this project?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger btn-rounded" type="submit" title="Delete">
                      <iconify-icon icon="mdi:trash-can"></iconify-icon>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">No projects found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer">
      {{ $projects->links() }}
    </div>
  </div>
</div>
@endsection
