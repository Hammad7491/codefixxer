{{-- resources/views/admin/educations/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Education|null $education */
  $isEdit = isset($education) && $education;
@endphp

@section('title', $isEdit ? 'Edit Education' : 'Add Education')

@section('content')
<style>
  :root { color-scheme: light; }
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }

  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select {
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
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Education' : 'Add Education' }}</h1>
    <a href="{{ route('admin.educations.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Education List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:school-outline"></iconify-icon>
      <strong class="fs-6">{{ $isEdit ? 'Update Education Details' : 'Education Details' }}</strong>
    </div>

    <div class="card-body p-4">
      <form method="POST"
            action="{{ $isEdit ? route('admin.educations.update', $education) : route('admin.educations.store') }}"
            id="educationForm" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
          {{-- Degree Name --}}
          <div class="col-md-6">
            <label class="form-label" for="degree_name">Degree Name</label>
            @php
              $degrees = ['Matric','Intermediate','BSc','BA','BS','MSc','MA','MS','MPhil','PhD','Diploma','Certification','Other'];
              $deg = old('degree_name', $education->degree_name ?? '');
            @endphp
            <select class="form-select" id="degree_name" name="degree_name" required>
              <option value="" disabled {{ $deg==='' ? 'selected' : '' }}>Select Degree</option>
              @foreach($degrees as $d)
                <option value="{{ $d }}" {{ $deg===$d ? 'selected' : '' }}>{{ $d }}</option>
              @endforeach
            </select>
          </div>

          {{-- Institute Name --}}
          <div class="col-md-6">
            <label class="form-label" for="institute_name">Institute Name</label>
            <input type="text" class="form-control" id="institute_name" name="institute_name"
                   value="{{ old('institute_name', $education->institute_name ?? '') }}"
                   placeholder="e.g., University of Karachi" required>
          </div>

          {{-- Start / End Dates (formatted for date input) --}}
          <div class="col-md-6">
            <label class="form-label" for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date"
                   value="{{ old('start_date', optional($education?->start_date)->format('Y-m-d')) }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date"
                   value="{{ old('end_date', optional($education?->end_date)->format('Y-m-d')) }}">
            <div class="help-text mt-1">Leave empty if currently enrolled.</div>
          </div>

          {{-- Field of Study --}}
          <div class="col-md-6">
            <label class="form-label" for="field_of_study">Field of Study</label>
            <input type="text" class="form-control" id="field_of_study" name="field_of_study"
                   value="{{ old('field_of_study', $education->field_of_study ?? '') }}"
                   placeholder="e.g., Computer Science, Mathematics" required>
          </div>

          {{-- Grade / GPA --}}
          <div class="col-md-3">
            <label class="form-label" for="grade_gpa">Grade/GPA</label>
            <input type="text" class="form-control" id="grade_gpa" name="grade_gpa"
                   value="{{ old('grade_gpa', $education->grade_gpa ?? '') }}"
                   placeholder="e.g., 3.5 / A">
          </div>

          {{-- Location --}}
          <div class="col-md-3">
            <label class="form-label" for="location">Location</label>
            <input type="text" class="form-control" id="location" name="location"
                   value="{{ old('location', $education->location ?? '') }}"
                   placeholder="City, Country">
          </div>

          {{-- Certifications / Projects (tags) --}}
          <div class="col-12">
            <label class="form-label">Certifications/Projects</label>
            <div class="tags-input" id="tagsBox">
              <span class="help-text m-0">Press Enter, comma, or Tab to add each item (e.g., Coursera ML Cert, Final Year Project).</span>
              <input type="text" id="tagInput" placeholder="Type and press Enter…" autocomplete="off">
            </div>
            <input type="hidden" name="certifications" id="tagsHidden"
                   value='@json(old("certifications", $education->certifications ?? []))'>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-soft-primary">
            <iconify-icon icon="{{ $isEdit ? 'mdi:content-save-edit' : 'mdi:content-save' }}" class="me-1"></iconify-icon>
            {{ $isEdit ? 'Update Education' : 'Save Education' }}
          </button>
          <a href="{{ route('admin.educations.index') }}" class="btn btn-outline-dark">
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
    const box   = document.getElementById('tagsBox');
    const input = document.getElementById('tagInput');
    const hidden= document.getElementById('tagsHidden');

    let tags = [];
    try {
      const init = hidden.value;
      if (init && typeof init === 'string') {
        const parsed = JSON.parse(init);
        if (Array.isArray(parsed)) tags = parsed;
      }
    } catch(e){}

    function render() {
      box.querySelectorAll('.tag-chip').forEach(el => el.remove());
      tags.forEach((t, i) => {
        const chip = document.createElement('span');
        chip.className = 'tag-chip';
        chip.innerHTML = `<span>${t}</span> <button type="button" aria-label="Remove"><iconify-icon icon="mdi:close"></iconify-icon></button>`;
        chip.querySelector('button').addEventListener('click', () => { tags.splice(i,1); render(); });
        box.insertBefore(chip, input);
      });
      hidden.value = JSON.stringify(tags);
    }
    function addOne(v){ v=(v||'').trim(); if(!v||tags.includes(v)) return; if(tags.length>=40) return alert('Maximum 40 items.'); tags.push(v); }
    function addMany(raw){ if(!raw) return; raw.split(',').forEach(p => addOne(p)); }

    input.addEventListener('keydown', function(e){
      if (e.key==='Enter' || e.key===',' || e.key==='Tab') {
        e.preventDefault(); addMany(input.value); input.value=''; render();
      } else if (e.key==='Backspace' && !this.value && tags.length) {
        tags.pop(); render();
      }
    });
    input.addEventListener('blur', function(){
      if (this.value.trim()) { addMany(this.value); this.value=''; render(); }
    });
    document.getElementById('educationForm').addEventListener('submit', function(){
      if (input.value.trim()) { addMany(input.value); input.value=''; }
      hidden.value = JSON.stringify(tags);
    });

    render();
  })();
</script>
@endsection
