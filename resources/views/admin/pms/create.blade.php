@extends('layouts.app')

@section('title', 'PMS - Create')

@section('content')
<style>
  .card { border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 6px 18px rgba(0,0,0,.04); }
  .card-header { padding:.9rem 1rem; border-bottom:1px solid #eef2f7; font-weight:700; background:#f8fafc; border-radius:16px 16px 0 0; }
  .card-body { padding:1rem; }
  .section-title { font-weight:700; margin: 1rem 0 .5rem; }
  .btn-soft { background:#4f46e5; color:#fff; border-radius:10px; padding:.45rem .8rem; border:0; }
  .btn-soft:hover { background:#4338ca; color:#fff; }
  .btn-outline { border:1px solid #e5e7eb; border-radius:10px; padding:.4rem .7rem; }
  .milestone { border:1px dashed #cbd5e1; border-radius:12px; padding:.75rem; margin-bottom:.75rem; background:#fff; }
  .task { border:1px solid #e5e7eb; border-radius:10px; padding:.6rem; margin:.5rem 0; background:#f9fafb; }
  .label { font-weight:600; color:#334155; }
</style>

<div class="container-fluid py-3">
  <h1 class="h4 mb-3">Project Management System — Create</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.pms.store') }}" id="pmsForm">
    @csrf

    {{-- PART 1: Client toggle + form --}}
    <div class="card mb-3">
      <div class="card-header">Client</div>
      <div class="card-body">
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="hasClient" name="has_client" value="1" checked>
          <label class="form-check-label" for="hasClient">Client</label>
        </div>

        <div id="clientFields">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="label">Name</label>
              <input type="text" name="client[name]" class="form-control" placeholder="Client name" value="{{ old('client.name') }}">
            </div>
            <div class="col-md-4">
              <label class="label">Email</label>
              <input type="email" name="client[email]" class="form-control" placeholder="client@email.com" value="{{ old('client.email') }}">
            </div>
            <div class="col-md-4">
              <label class="label">Phone</label>
              <input type="text" name="client[phone]" class="form-control" placeholder="+92 3xx xxxxxxx" value="{{ old('client.phone') }}">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- PART 2: Project details --}}
    <div class="card mb-3">
      <div class="card-header">Project Details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="label">Name</label>
            <input type="text" name="project[name]" class="form-control" placeholder="Project name" value="{{ old('project.name') }}">
          </div>
          <div class="col-md-4">
            <label class="label">Deadline</label>
            <input type="date" name="project[deadline]" class="form-control" value="{{ old('project.deadline') }}">
          </div>
          <div class="col-12">
            <label class="label">Description</label>
            <textarea name="project[description]" class="form-control" rows="3" placeholder="Short description">{{ old('project.description') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- PART 3 + 4: Milestones and Tasks --}}
    <div class="card mb-3">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span>Milestones & Tasks</span>
        <button class="btn btn-soft btn-sm" type="button" id="addMilestone">Add Milestone</button>
      </div>
      <div class="card-body" id="milestonesWrap">
        <!-- milestones will be injected here -->
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-soft" type="submit">Save</button>
      <a href="{{ url()->previous() }}" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

{{-- Templates (hidden) --}}
<template id="tpl-milestone">
  <div class="milestone" data-mi="__MI__">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <strong>Milestone <span class="ms-number">#__MS_NO__</span></strong>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline btn-sm js-add-task">Add Task</button>
        <button type="button" class="btn btn-outline btn-sm js-remove-milestone">Remove</button>
      </div>
    </div>

    <div class="row g-2 mb-2">
      <div class="col-md-4">
        <label class="label">Title</label>
        <input type="text" class="form-control ms-title"
               name="milestones[__MI__][title]" placeholder="Milestone title">
      </div>
      <div class="col-md-5">
        <label class="label">Description</label>
        <input type="text" class="form-control ms-desc"
               name="milestones[__MI__][description]" placeholder="Short description">
      </div>
      <div class="col-md-3">
        <label class="label">Due Date</label>
        <input type="date" class="form-control"
               name="milestones[__MI__][due_date]">
      </div>
    </div>

    <div class="section-title">Tasks</div>
    <div class="tasks" id="tasks-__MI__">
      <!-- default task will be injected empty -->
    </div>
  </div>
</template>

<template id="tpl-task">
  <div class="task" data-ti="__TI__">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="label">Task Name</label>
        <input type="text" class="form-control task-name"
               name="milestones[__MI__][tasks][__TI__][name]"
               placeholder="Task name">
      </div>
      <div class="col-md-5">
        <label class="label">Description</label>
        <input type="text" class="form-control task-desc"
               name="milestones[__MI__][tasks][__TI__][description]"
               placeholder="Short description">
      </div>
      <div class="col-md-3">
        <label class="label">Status</label>
        <select class="form-control task-status"
                name="milestones[__MI__][tasks][__TI__][status]">
          <option value="in_progress" selected>In Progress</option>
          <option value="done">Done</option>
        </select>
      </div>
    </div>
    <div class="mt-2">
      <button type="button" class="btn btn-outline btn-sm js-remove-task">Remove Task</button>
    </div>
  </div>
</template>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // PART 1: Client toggle show/hide + disable/enable inputs
    const hasClient = document.getElementById('hasClient');
    const clientFields = document.getElementById('clientFields');
    const setClientState = () => {
      const show = hasClient.checked;
      clientFields.style.display = show ? '' : 'none';
      clientFields.querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = !show;
      });
    };
    hasClient.addEventListener('change', setClientState);
    setClientState(); // default on

    // Milestones + tasks
    const wrap = document.getElementById('milestonesWrap');
    const tplMilestone = document.getElementById('tpl-milestone').innerHTML;
    const tplTask = document.getElementById('tpl-task').innerHTML;
    let mi = 0; // milestone index
    const taskCounters = {}; // per milestone

    // ---- helpers ----
    function addTask(milestoneNode) {
      const mIndex = parseInt(milestoneNode.getAttribute('data-mi'), 10);
      const ti = taskCounters[mIndex] ?? 0;

      const html = tplTask
        .replaceAll('__MI__', mIndex)
        .replaceAll('__TI__', ti);

      const div = document.createElement('div');
      div.innerHTML = html.trim();
      const node = div.firstElementChild;

      milestoneNode.querySelector('.tasks').appendChild(node);
      taskCounters[mIndex] = ti + 1;
      return node;
    }

    function addMilestone() {
      const html = tplMilestone
        .replaceAll('__MI__', mi)
        .replace('__MS_NO__', mi + 1);
      const div = document.createElement('div');
      div.innerHTML = html.trim();
      const node = div.firstElementChild;
      wrap.appendChild(node);

      taskCounters[mi] = 0; // init counter for this milestone

      // Add ONE default task but keep it EMPTY
      addTask(node);

      mi++;
      return node;
    }

    // Fill first task from milestone fields IF the task is still empty
    function finalizeCurrentMilestone() {
      const current = wrap.querySelector('.milestone:last-of-type');
      if (!current) return;

      const title = current.querySelector('.ms-title')?.value?.trim() || '';
      const desc  = current.querySelector('.ms-desc')?.value?.trim()  || '';

      // first task (data-ti=0)
      let firstTask = current.querySelector('.tasks .task[data-ti="0"]');
      if (!firstTask) {
        // if user removed it, create a new one
        firstTask = addTask(current);
        // ensure it's the first logically (we won't reorder visually, that's okay)
      }

      const tName = firstTask.querySelector('.task-name');
      const tDesc = firstTask.querySelector('.task-desc');

      if (tName && tName.value.trim() === '' && title !== '') tName.value = title;
      if (tDesc && tDesc.value.trim() === '' && desc  !== '') tDesc.value = desc;

      // status already defaults to in_progress in the template
    }

    // Add first milestone on load
    addMilestone();

    // Events
    document.getElementById('addMilestone').addEventListener('click', () => {
      // BEFORE adding a new milestone, finalize the current one
      finalizeCurrentMilestone();
      addMilestone();
    });

    // Also finalize before submit to catch the last milestone case
    document.getElementById('pmsForm').addEventListener('submit', (e) => {
      finalizeCurrentMilestone();
    });

    wrap.addEventListener('click', (e) => {
      const btnAddTask = e.target.closest('.js-add-task');
      if (btnAddTask) {
        const m = e.target.closest('.milestone');
        addTask(m); // extra tasks are manual; no auto-fill
      }

      const btnRemoveTask = e.target.closest('.js-remove-task');
      if (btnRemoveTask) {
        const task = e.target.closest('.task');
        task?.remove();
      }

      const btnRemoveMilestone = e.target.closest('.js-remove-milestone');
      if (btnRemoveMilestone) {
        const m = e.target.closest('.milestone');
        m?.remove();
      }
    });
  });
</script>
@endsection
