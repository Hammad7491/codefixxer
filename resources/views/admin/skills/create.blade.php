{{-- resources/views/admin/skills/create.blade.php --}}
@extends('layouts.app')

@php
  /** @var \App\Models\Skill|null $skill */
  $isEdit = isset($skill) && $skill;
@endphp

@section('title', $isEdit ? 'Edit Skill' : 'Add Skill')

@section('content')
<style>
  body .page-wrap { background: #0f172a0d; }
  .page-title { font-weight: 700; letter-spacing: .3px; }
  .card-soft { border: 1px solid rgba(148,163,184,.25); box-shadow: 0 10px 30px rgba(2,8,23,.05); border-radius: 18px; overflow: hidden; }
  .card-soft .card-header { background: linear-gradient(135deg,#0ea5e9 0%, #6366f1 100%); color: #fff; border: 0; padding: 1.1rem 1.25rem; }
  .form-label { font-weight: 600; color: #334155; }
  .form-control, .form-select { border-radius: 12px; border-color: #e2e8f0; }
  .form-control:focus, .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 .2rem rgba(99,102,241,.15); }
  .tags-input { display:flex; gap:.5rem; flex-wrap: wrap; min-height: 48px; padding: .35rem .5rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
  .tags-input input { border: 0; outline: 0; min-width: 180px; flex: 1; padding:.4rem .25rem; }
  .tag-chip { display:inline-flex; align-items:center; gap:.4rem; background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; padding:.35rem .6rem; border-radius: 999px; font-size:.875rem; }
  .tag-chip button { border:0; background:transparent; line-height:1; padding:0; cursor:pointer; color:#4f46e5; }
  .help-text { color:#64748b; font-size:.875rem; }
  .btn-soft-primary { background:#4f46e5; color:#fff; border-radius:12px; }
  .btn-soft-primary:hover { background:#4338ca; color:#fff; }
  .btn-light-ghost { border-radius:12px; }
  @media (prefers-color-scheme: dark) {
    .form-label { color:#cbd5e1; }
    .form-control, .form-select { background:#0b1220; color:#e2e8f0; border-color:#334155; }
    .tags-input { background:#0b1220; border-color:#334155; }
    .tag-chip { background:#0b1220; color:#c7d2fe; border-color:#334155; }
    .help-text { color:#94a3b8; }
    .card { background:#0b1220; }
  }
</style>

<div class="container-fluid page-wrap py-3 py-md-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 page-title mb-0">{{ $isEdit ? 'Edit Skill' : 'Add Skill' }}</h1>
    <a href="{{ route('admin.skills.index') }}" class="btn btn-light-ghost border">
      <iconify-icon icon="mdi:format-list-bulleted" class="me-1"></iconify-icon>
      Skills List
    </a>
  </div>

  {{-- Validation errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header d-flex align-items-center">
      <iconify-icon icon="mdi:tools" class="me-2"></iconify-icon>
      <strong>{{ $isEdit ? 'Update Skill' : 'Create New Skill' }}</strong>
    </div>
    <div class="card-body p-4">
      <form method="POST"
            action="{{ $isEdit ? route('admin.skills.update', $skill) : route('admin.skills.store') }}"
            id="skillForm" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
          <!-- First Name -->
          <div class="col-md-6">
            <label class="form-label" for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name"
                   value="{{ old('first_name', $skill->first_name ?? '') }}" placeholder="e.g., John" required>
            <div class="help-text mt-1">Enter the person's given name.</div>
          </div>

          <!-- Last Name -->
          <div class="col-md-6">
            <label class="form-label" for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name"
                   value="{{ old('last_name', $skill->last_name ?? '') }}" placeholder="e.g., Doe" required>
            <div class="help-text mt-1">Enter the family/surname.</div>
          </div>

          <!-- Skill Category -->
          <div class="col-md-6">
            <label class="form-label" for="category">Skill Category</label>
            @php
              $cats = [
                'Programming & Tech','Graphic Design','Digital Marketing','Video & Animation',
                'Content Writing','UI/UX Design','Data Science & Analytics',
                'Networking & Cybersecurity','DevOps & Cloud','Other'
              ];
              $selectedCat = old('category', $skill->category ?? '');
            @endphp
            <select class="form-select" id="category" name="category" required>
              <option value="" disabled {{ $selectedCat==='' ? 'selected' : '' }}>Choose Category</option>
              @foreach($cats as $c)
                <option value="{{ $c }}" {{ $selectedCat===$c ? 'selected' : '' }}>{{ $c }}</option>
              @endforeach
            </select>
            <div class="help-text mt-1">Pick the category that best fits the skill.</div>
          </div>

          <!-- Years of Experience -->
          <div class="col-md-6">
            <label class="form-label" for="experience_years">Years of Experience</label>
            <div class="input-group">
              <input type="number" min="0" max="50" step="1" class="form-control"
                     id="experience_years" name="experience_years"
                     value="{{ old('experience_years', $skill->experience_years ?? '') }}"
                     placeholder="e.g., 3" required>
              <span class="input-group-text">years</span>
            </div>
            <div class="help-text mt-1">Enter whole years (0–50).</div>
          </div>

          <!-- Related Tools / Technologies (tags) -->
          <div class="col-12">
            <label class="form-label">Related Tools / Technologies</label>
            <div class="tags-input" id="toolsBox">
              <span class="help-text m-0">Press Enter (or comma/Tab) to add each tool (e.g., Laravel, MySQL, Vue).</span>
              <input type="text" id="toolInput" placeholder="Type and press Enter…" autocomplete="off">
            </div>
            {{-- Hidden field stores JSON array; prefill with old() OR existing record --}}
            <input type="hidden" name="tools" id="toolsHidden"
                   value="{{ old('tools', json_encode($skill->tools ?? [])) }}">
          </div>

          <!-- Notes (optional) -->
          <div class="col-12">
            <label class="form-label" for="notes">Notes (optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"
                      placeholder="Any extra details…">{{ old('notes', $skill->notes ?? '') }}</textarea>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-soft-primary">
            <iconify-icon icon="{{ $isEdit ? 'mdi:content-save-edit' : 'mdi:content-save' }}" class="me-1"></iconify-icon>
            {{ $isEdit ? 'Update Skill' : 'Save Skill' }}
          </button>
          <a href="{{ route('admin.skills.index') }}" class="btn btn-outline-dark">
            <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon>
            Back to List
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Tags JS: prefill from hidden JSON; add/remove; sync on blur/submit --}}
<script>
  (function(){
    const toolsBox = document.getElementById('toolsBox');
    const input    = document.getElementById('toolInput');
    const hidden   = document.getElementById('toolsHidden');

    let tools = [];
    // Prefill from hidden (old() or $skill->tools)
    try {
      const init = hidden.value;
      if (init && typeof init === 'string') {
        const parsed = JSON.parse(init);
        if (Array.isArray(parsed)) tools = parsed;
      }
    } catch(e){ /* ignore */ }

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

    function addOne(v){
      v = (v||'').trim();
      if (!v) return;
      if (tools.includes(v)) return;
      if (tools.length >= 20) { alert('Maximum 20 tools.'); return; }
      tools.push(v);
    }
    function addMany(raw){
      if (!raw) return;
      raw.split(',').forEach(p => addOne(p));
    }

    input.addEventListener('keydown', function(e){
      if (e.key === 'Enter' || e.key === ',' || e.key === 'Tab') {
        e.preventDefault();
        addMany(input.value);
        input.value = '';
        renderTags();
      } else if (e.key === 'Backspace' && !this.value && tools.length) {
        tools.pop(); renderTags();
      }
    });
    input.addEventListener('blur', function(){
      if (this.value.trim()) {
        addMany(this.value);
        this.value = '';
        renderTags();
      }
    });

    document.getElementById('skillForm').addEventListener('submit', function(){
      if (input.value.trim()) {
        addMany(input.value);
        input.value = '';
      }
      hidden.value = JSON.stringify(tools); // final sync
    });

    renderTags();
  })();
</script>
@endsection
