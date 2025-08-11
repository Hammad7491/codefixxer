{{-- resources/views/admin/projects/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Project|null $project */
  $isEdit = isset($project) && $project;
@endphp

@section('title', $isEdit ? 'Edit Project' : 'Add Project')

@section('content')
<style>
  :root { color-scheme: light; }
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select, .form-file {
    border-radius: 12px; border-color: #e2e8f0;
    background: #fff !important; color: #0f172a !important;
  }
  .form-control::placeholder { color:#64748b; }
  .form-control:focus, .form-select:focus { border-color:#6366f1; box-shadow:0 0 0 .2rem rgba(99,102,241,.15); }
  .help-text { color:#64748b; font-size:.875rem; }

  .tags-input { display:flex; gap:.5rem; flex-wrap: wrap; min-height: 48px; padding: .35rem .5rem;
    border: 1px solid #e2e8f0; border-radius: 12px; background:#fff !important; color:#0f172a; }
  .tags-input input { border:0; outline:0; min-width:180px; flex:1; padding:.4rem .25rem; background:#fff !important; color:#0f172a; }
  .tag-chip { display:inline-flex; align-items:center; gap:.4rem; background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; padding:.35rem .6rem; border-radius:999px; font-size:.875rem; }
  .tag-chip button { border:0; background:transparent; line-height:1; padding:0; cursor:pointer; color:#4f46e5; }

  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
  .btn-light-ghost { border-radius:12px; }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Project' : 'Add Project' }}</h1>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Projects List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:folder-outline"></iconify-icon>
      <strong class="fs-6">{{ $isEdit ? 'Update Project Details' : 'Project Details' }}</strong>
    </div>

    <div class="card-body p-4">
      <form method="POST"
            action="{{ $isEdit ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
            id="projectForm" enctype="multipart/form-data" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
          <!-- Project Name -->
          <div class="col-md-6">
            <label class="form-label" for="name">Project Name</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="{{ old('name', $project->name ?? '') }}"
                   placeholder="e.g., Online Food Ordering System" required>
          </div>

          <!-- Project Type -->
          <div class="col-md-6">
            <label class="form-label" for="type">Project Type</label>
            @php
              $types = ['Web Application','Mobile Application','Desktop Application','API/Backend Service','E-commerce','CMS/Website','UI/UX Case Study','Data Science/ML','Other'];
              $sel = old('type', $project->type ?? '');
            @endphp
            <select class="form-select" id="type" name="type" required>
              <option value="" disabled {{ $sel==='' ? 'selected' : '' }}>Choose Project Type</option>
              @foreach($types as $t)
                <option value="{{ $t }}" {{ $sel===$t ? 'selected' : '' }}>{{ $t }}</option>
              @endforeach
            </select>
            <div class="help-text mt-1">Pick the closest category.</div>
          </div>

          <!-- Client/Organization Name -->
          <div class="col-md-6">
            <label class="form-label" for="client">Client/Organization Name</label>
            <input type="text" class="form-control" id="client" name="client"
                   value="{{ old('client', $project->client ?? '') }}"
                   placeholder="e.g., Acme Corp / Personal" required>
          </div>

          <!-- Duration (in weeks) -->
          <div class="col-md-6">
            <label class="form-label" for="duration_weeks">Duration (in weeks)</label>
            <div class="input-group">
              <input type="number" min="1" max="520" step="1" class="form-control" id="duration_weeks" name="duration_weeks"
                     value="{{ old('duration_weeks', $project->duration_weeks ?? '') }}"
                     placeholder="e.g., 8" required>
              <span class="input-group-text">weeks</span>
            </div>
          </div>

          <!-- Live Link -->
          <div class="col-12">
            <label class="form-label" for="live_link">Live Link</label>
            <input type="url" class="form-control" id="live_link" name="live_link"
                   value="{{ old('live_link', $project->live_link ?? '') }}"
                   placeholder="https://example.com (optional)">
            <div class="help-text mt-1">Include http/https.</div>
          </div>

          <!-- Project Description -->
          <div class="col-12">
            <label class="form-label" for="description">Project Description</label>
            <textarea class="form-control" id="description" name="description" rows="5"
                      placeholder="Brief overview, goals, features…">{{ old('description', $project->description ?? '') }}</textarea>
          </div>

          <!-- Project Video -->
          <div class="col-md-6">
            <label class="form-label" for="video">Project Video</label>
            <input type="file" class="form-control" id="video" name="video" accept="video/*">
            <div class="help-text mt-1">Leave empty to keep existing.</div>
          </div>

          <!-- Documentation (ZIP/PDF) -->
          <div class="col-md-6">
            <label class="form-label" for="documentation">Documentation (ZIP/PDF)</label>
            <input type="file" class="form-control" id="documentation" name="documentation" accept=".pdf,.zip,.rar">
            <div class="help-text mt-1">Leave empty to keep existing.</div>
          </div>

          <!-- Project Images -->
          <div class="col-12">
            <label class="form-label" for="images">Project Images</label>
            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
            <div class="help-text mt-1">Selecting new images replaces the existing set.</div>
          </div>

          <!-- Technologies/Tools Used (tags) -->
          <div class="col-12">
            <label class="form-label">Technologies/Tools Used</label>
            <div class="tags-input" id="toolsBox">
              <span class="help-text m-0">Press Enter, comma, or Tab to add each item (e.g., Laravel, MySQL, Bootstrap).</span>
              <input type="text" id="toolInput" placeholder="Type and press Enter…" autocomplete="off">
            </div>
            <input type="hidden" name="tools_used" id="toolsHidden"
                   value='@json(old("tools_used", $project->tools_used ?? []))'>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-soft-primary">
            <iconify-icon icon="{{ $isEdit ? 'mdi:content-save-edit' : 'mdi:content-save' }}" class="me-1"></iconify-icon>
            {{ $isEdit ? 'Update Project' : 'Save Project' }}
          </button>
          <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-dark">
            <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon>
            Back to List
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Tags JS --}}
<script>
  (function(){
    const toolsBox = document.getElementById('toolsBox');
    const input    = document.getElementById('toolInput');
    const hidden   = document.getElementById('toolsHidden');

    let tools = [];
    try {
      const init = hidden.value;
      if (init && typeof init === 'string') {
        const parsed = JSON.parse(init);
        if (Array.isArray(parsed)) tools = parsed;
      }
    } catch(e){}

    function renderTags() {
      toolsBox.querySelectorAll('.tag-chip').forEach(el => el.remove());
      tools.forEach((t, i) => {
        const chip = document.createElement('span');
        chip.className = 'tag-chip';
        chip.innerHTML = `<span>${t}</span> <button type="button" aria-label="Remove"><iconify-icon icon="mdi:close"></iconify-icon></button>`;
        chip.querySelector('button').addEventListener('click', () => { tools.splice(i,1); renderTags(); });
        toolsBox.insertBefore(chip, input);
      });
      hidden.value = JSON.stringify(tools);
    }
    function addOne(v){ v=(v||'').trim(); if(!v||tools.includes(v)) return; if(tools.length>=40) return alert('Maximum 40 items.'); tools.push(v); }
    function addMany(raw){ if(!raw) return; raw.split(',').forEach(p => addOne(p)); }

    input.addEventListener('keydown', function(e){
      if (e.key==='Enter' || e.key===',' || e.key==='Tab') {
        e.preventDefault();
        addMany(input.value); input.value=''; renderTags();
      } else if (e.key==='Backspace' && !this.value && tools.length) {
        tools.pop(); renderTags();
      }
    });
    input.addEventListener('blur', function(){
      if (this.value.trim()) { addMany(this.value); this.value=''; renderTags(); }
    });
    document.getElementById('projectForm').addEventListener('submit', function(){
      if (input.value.trim()) { addMany(input.value); input.value=''; }
      hidden.value = JSON.stringify(tools);
    });

    renderTags();
  })();
</script>
@endsection
