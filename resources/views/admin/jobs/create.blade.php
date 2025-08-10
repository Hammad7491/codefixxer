{{-- resources/views/admin/jobs/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Job|null $job */
  $isEdit = isset($job) && $job;
@endphp

@section('title', $isEdit ? 'Edit Job' : 'Add Job')

@section('content')
<style>
  /* Force light look */
  :root { color-scheme: light; }

  /* Same light theme as Skills */
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft {
      border: 1px solid rgba(148,163,184,.25);
      box-shadow: 0 10px 30px rgba(2,8,23,.05);
      border-radius: 18px;
      overflow: hidden;
  }
  .card-soft .card-header {
      background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%);
      color: #fff;
      border: 0;
      padding: 1.1rem 1.25rem;
  }
  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select {
      border-radius: 12px;
      border-color: #e2e8f0;
      background: #fff !important;
      color: #0f172a !important;
  }
  .form-control::placeholder { color:#64748b; }
  .form-control:focus, .form-select:focus {
      border-color: #6366f1; box-shadow: 0 0 0 .2rem rgba(99,102,241,.15);
  }

  /* Tags */
  .tags-input {
    display:flex; gap:.5rem; flex-wrap: wrap;
    min-height: 48px; padding: .35rem .5rem;
    border: 1px solid #e2e8f0; border-radius: 12px;
    background: #fff !important; color:#0f172a;
  }
  .tags-input input {
    border: 0; outline: 0; min-width: 180px;
    flex: 1; padding:.4rem .25rem; background:#fff !important; color:#0f172a;
  }
  .tag-chip {
    display:inline-flex; align-items:center; gap:.4rem;
    background:#eef2ff; color:#3730a3;
    border:1px solid #c7d2fe; padding:.35rem .6rem;
    border-radius: 999px; font-size:.875rem;
  }
  .tag-chip button { border:0; background:transparent; line-height:1; padding:0; cursor:pointer; color:#4f46e5; }
  .help-text { color:#64748b; font-size:.875rem; }

  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
  .btn-light-ghost { border-radius:12px; }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Job' : 'Add Job' }}</h1>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Jobs List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header text-center d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:briefcase-outline"></iconify-icon>
      <strong class="fs-6">{{ $isEdit ? 'Update Job Details' : 'Job Experience Details' }}</strong>
    </div>

    <div class="card-body p-4">
      <form method="POST"
            action="{{ $isEdit ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}"
            id="jobForm" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
          <!-- Job Title/Role -->
          <div class="col-12">
            <label class="form-label" for="title">Job Title/Role</label>
            <input type="text" class="form-control" id="title" name="title"
                   value="{{ old('title', $job->title ?? '') }}"
                   placeholder="e.g., Software Engineer" required>
          </div>

          <!-- Organization + Employment Type -->
          <div class="col-md-6">
            <label class="form-label" for="organization_name">Organization Name</label>
            <input type="text" class="form-control" id="organization_name" name="organization_name"
                   value="{{ old('organization_name', $job->organization_name ?? '') }}"
                   placeholder="e.g., Google Inc." required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="employment_type">Employment Type</label>
            @php
              $types = ['Full-Time','Part-Time','Freelance','Internship','Contractual'];
              $sel = old('employment_type', $job->employment_type ?? '');
            @endphp
            <select class="form-select" id="employment_type" name="employment_type" required>
              <option value="" disabled {{ $sel==='' ? 'selected' : '' }}>Choose Employment Type</option>
              @foreach($types as $t)
                <option value="{{ $t }}" {{ $sel===$t ? 'selected':'' }}>{{ $t }}</option>
              @endforeach
            </select>
          </div>

          <!-- Dates -->
          <div class="col-md-6">
            <label class="form-label" for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date"
                   value="{{ old('start_date', isset($job->start_date) ? $job->start_date->format('Y-m-d') : '') }}"
                   required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date"
                   value="{{ old('end_date', isset($job->end_date) ? $job->end_date->format('Y-m-d') : '') }}">
            <div class="help-text mt-1">Leave empty if currently working.</div>
          </div>

          <!-- Location -->
          <div class="col-12">
            <label class="form-label" for="location">Location</label>
            <input type="text" class="form-control" id="location" name="location"
                   value="{{ old('location', $job->location ?? '') }}"
                   placeholder="e.g., San Francisco, CA">
          </div>

          <!-- Tools & Technologies Used (tags) -->
          <div class="col-12">
            <label class="form-label">Tools & Technologies Used</label>
            <div class="tags-input" id="toolsBox">
              <span class="help-text m-0">Press Enter, comma, or Tab to add each item (e.g., Laravel, MySQL, Vue).</span>
              <input type="text" id="toolInput" placeholder="Type and press Enter…" autocomplete="off">
            </div>
            <input type="hidden" name="tools_used" id="toolsHidden"
                   value="{{ old('tools_used', json_encode($job->tools_used ?? [])) }}">
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-soft-primary">
            <iconify-icon icon="{{ $isEdit ? 'mdi:content-save-edit' : 'mdi:content-save' }}" class="me-1"></iconify-icon>
            {{ $isEdit ? 'Update Job' : 'Save Job' }}
          </button>
          <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-dark">
            <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon>
            Back to List
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

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

    function addOne(v){ v=(v||'').trim(); if(!v||tools.includes(v)) return; if(tools.length>=30) return alert('Max 30'); tools.push(v); }
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

    document.getElementById('jobForm').addEventListener('submit', function(){
      if (input.value.trim()) { addMany(input.value); input.value=''; }
      hidden.value = JSON.stringify(tools);
    });

    renderTags();
  })();
</script>
@endsection
