<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PmsClient;
use App\Models\PmsProject;
use App\Models\Milestone;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PmsController extends Controller
{
    protected function tenantId(): int
    {
        return (int) Auth::id(); // tenant = current logged-in user id
    }

    // Show the PMS page
    public function create()
    {
        return view('admin.pms.create');
    }

    // Save Project (+ optional Client) + Milestones + Tasks
    public function store(Request $request)
    {
        $data = $request->validate([
            'has_client'                       => ['nullable','boolean'],

            'client.name'                      => ['required_if:has_client,1','nullable','string','max:191'],
            'client.email'                     => ['required_if:has_client,1','nullable','email','max:191'],
            'client.phone'                     => ['required_if:has_client,1','nullable','string','max:191'],

            'project.name'                     => ['required','string','max:191'],
            'project.description'              => ['nullable','string'],
            'project.deadline'                 => ['nullable','date'],

            'milestones'                       => ['nullable','array'],
            'milestones.*.title'               => ['nullable','string','max:191'],
            'milestones.*.description'         => ['nullable','string'],
            'milestones.*.due_date'            => ['nullable','date'],

            'milestones.*.tasks'               => ['nullable','array'],
            'milestones.*.tasks.*.name'        => ['nullable','string','max:191'],
            'milestones.*.tasks.*.description' => ['nullable','string'],
            'milestones.*.tasks.*.status'      => ['nullable','in:todo,in_progress,done'],
        ]);

        $tenantId = $this->tenantId();

        DB::transaction(function () use ($data, $tenantId) {
            // 1) Project
            $project = PmsProject::create([
                'tenant_id'   => $tenantId,
                'name'        => data_get($data, 'project.name'),
                'description' => data_get($data, 'project.description'),
                'deadline'    => data_get($data, 'project.deadline'),
            ]);

            // 2) Optional Client
            if (data_get($data, 'has_client')) {
                PmsClient::create([
                    'tenant_id'  => $tenantId,
                    'project_id' => $project->id,
                    'name'       => data_get($data, 'client.name'),
                    'email'      => data_get($data, 'client.email'),
                    'phone'      => data_get($data, 'client.phone'),
                ]);
            }

            // 3) Milestones + 4) Tasks
            foreach (data_get($data, 'milestones', []) as $ms) {
                // skip fully blank milestone
                if (blank(data_get($ms, 'title')) &&
                    blank(data_get($ms, 'description')) &&
                    blank(data_get($ms, 'due_date'))) {
                    continue;
                }

                $milestone = Milestone::create([
                    'tenant_id'   => $tenantId,
                    'project_id'  => $project->id,
                    'title'       => data_get($ms, 'title'),
                    'description' => data_get($ms, 'description'),
                    'due_date'    => data_get($ms, 'due_date'),
                ]);

                foreach (data_get($ms, 'tasks', []) as $t) {
                    if (blank(data_get($t, 'name')) && blank(data_get($t, 'description'))) {
                        continue;
                    }
                    Task::create([
                        'tenant_id'    => $tenantId,
                        'milestone_id' => $milestone->id,
                        'name'         => data_get($t, 'name'),
                        'description'  => data_get($t, 'description'),
                        'status'       => data_get($t, 'status', 'todo'),
                    ]);
                }
            }
        });

        return back()->with('success', 'Project saved successfully.');
    }
}
