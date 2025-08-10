<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // List (tenant scoped) — oldest first so new ones appear at the bottom
    public function index()
    {
        $tenantId = $this->tenantId();
        $q = request('q');

        $jobs = Job::where('tenant_id', $tenantId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($s) use ($q) {
                    $s->where('title', 'like', "%{$q}%")
                      ->orWhere('organization_name', 'like', "%{$q}%")
                      ->orWhere('employment_type', 'like', "%{$q}%")
                      ->orWhere('location', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'asc') // oldest first
            ->paginate(10)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs', 'q'));
    }

    public function create()
    {
        $job = null;
        return view('admin.jobs.create', compact('job'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['tenant_id']  = $this->tenantId();
        $data['tools_used'] = $this->decodeTools($data['tools_used'] ?? null);

        $job = Job::create($data);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job saved. ID: '.$job->id);
    }

    public function show(Job $job)
    {
        $this->authorizeTenant($job);
        // If you don't have a show view, redirect to edit or index:
        return redirect()->route('admin.jobs.edit', $job);
    }

    public function edit(Job $job)
    {
        $this->authorizeTenant($job);
        return view('admin.jobs.create', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorizeTenant($job);

        $data = $this->validated($request);
        $data['tools_used'] = $this->decodeTools($data['tools_used'] ?? null);

        $job->update($data);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job updated.');
    }

    public function destroy(Job $job)
    {
        $this->authorizeTenant($job);
        $job->delete();

        return back()->with('success', 'Job deleted.');
    }

    // ===== Helpers =====

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title'              => ['required','string','max:150'],
            'organization_name'  => ['required','string','max:150'],
            'employment_type'    => ['required','string','in:Full-Time,Part-Time,Freelance,Internship,Contractual'],
            'start_date'         => ['required','date'],
            'end_date'           => ['nullable','date','after_or_equal:start_date'],
            'location'           => ['nullable','string','max:150'],
            'tools_used'         => ['nullable'], // JSON or CSV string from hidden field
        ]);
    }

    protected function decodeTools($val): array
    {
        if (is_array($val)) return array_values(array_filter($val));
        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') return [];
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return array_values(array_filter($decoded));
            // CSV fallback
            return array_values(array_filter(array_map('trim', explode(',', $val))));
        }
        return [];
    }

    protected function tenantId(): int
    {
        $user = auth()->user();
        if ($user && $user->tenant_id) return (int) $user->tenant_id;

        // DEV fallback so you can work before wiring tenant IDs on users
        return 1;
    }

    protected function authorizeTenant(Job $job): void
    {
        if ((int) $job->tenant_id !== $this->tenantId()) {
            abort(404);
        }
    }
}
