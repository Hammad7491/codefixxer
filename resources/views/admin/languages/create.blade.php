 {{-- resources/views/admin/languages/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Language|null $language */
  $isEdit = isset($language) && $language;
@endphp

@section('title', $isEdit ? 'Edit Language' : 'Add Language')

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
  .form-control:focus, .form-select:focus {
      border-color: #6366f1; box-shadow: 0 0 0 .2rem rgba(99,102,241,.15);
  }
  .help-text { color:#64748b; font-size:.875rem; }

  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
  .btn-light-ghost { border-radius:12px; }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Language' : 'Add Language' }}</h1>
    <a href="{{ route('admin.languages.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Languages List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-center gap-2">
      <iconify-icon icon="mdi:translate"></iconify-icon>
      <strong class="fs-6">{{ $isEdit ? 'Update Language' : 'Language Details' }}</strong>
    </div>

    <div class="card-body p-4">
      <form method="POST"
            action="{{ $isEdit ? route('admin.languages.update', $language) : route('admin.languages.store') }}"
            id="languageForm" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
          {{-- Language --}}
          <div class="col-md-6">
            <label class="form-label" for="name">Language</label>
            @php
              $all = [
                'English','Urdu','Hindi','Arabic','French','Spanish','Mandarin','Bengali','Russian',
                'Portuguese','Japanese','German','Turkish','Italian','Korean','Persian','Punjabi','Gujarati',
                'Pashto','Sindhi','Malay','Indonesian','Thai','Vietnamese','Polish','Dutch','Ukrainian',
                'Romanian','Greek','Hebrew','Swedish','Norwegian','Danish','Finnish','Czech','Hungarian',
                'Filipino','Tamil','Telugu','Marathi','Kannada','Malayalam','Sinhala','Nepali','Other'
              ];
              $selLang = old('name', $language->name ?? '');
            @endphp
            <select class="form-select" id="name" name="name" required>
              <option value="" disabled {{ $selLang==='' ? 'selected' : '' }}>Select Language</option>
              @foreach($all as $lang)
                <option value="{{ $lang }}" {{ $selLang===$lang ? 'selected' : '' }}>{{ $lang }}</option>
              @endforeach
            </select>
          </div>

          {{-- Proficiency --}}
          <div class="col-md-6">
            <label class="form-label" for="proficiency">Proficiency Level</label>
            @php
              $levels = ['Beginner','Intermediate','Advanced','Fluent','Native'];
              $selLevel = old('proficiency', $language->proficiency ?? '');
            @endphp
            <select class="form-select" id="proficiency" name="proficiency" required>
              <option value="" disabled {{ $selLevel==='' ? 'selected' : '' }}>Select Proficiency</option>
              @foreach($levels as $lv)
                <option value="{{ $lv }}" {{ $selLevel===$lv ? 'selected' : '' }}>{{ $lv }}</option>
              @endforeach
            </select>
            <div class="help-text mt-1">Choose your overall proficiency.</div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-soft-primary">
            <iconify-icon icon="{{ $isEdit ? 'mdi:content-save-edit' : 'mdi:content-save' }}" class="me-1"></iconify-icon>
            {{ $isEdit ? 'Update Language' : 'Save Language' }}
          </button>
          <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-dark">
            <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon>
            Back to List
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
