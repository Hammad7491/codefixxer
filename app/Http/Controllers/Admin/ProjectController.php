<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // List (tenant scoped)
    public function index()
    {
        $tenantId = $this->tenantId();
        $q = request('q');

        $projects = Project::where('tenant_id', $tenantId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%")
                      ->orWhere('client', 'like', "%{$q}%")
                      ->orWhere('type', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'q'));
    }

    public function create()
    {
        $project = null;
        return view('admin.projects.create', compact('project'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['tenant_id']  = $this->tenantId();
        $data['tools_used'] = $this->decodeTools($data['tools_used'] ?? null);

        // handle uploads
        $paths = $this->storeUploads($request);
        $data  = array_merge($data, $paths);

        // images array
        if (empty($data['images'])) $data['images'] = [];

        $project = Project::create($data);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project saved. ID: '.$project->id);
    }

    public function show(Project $project)
    {
        $this->authorizeTenant($project);
        // if no dedicated show page, redirect to edit:
        return redirect()->route('admin.projects.edit', $project);
    }

    public function edit(Project $project)
    {
        $this->authorizeTenant($project);
        return view('admin.projects.create', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeTenant($project);

        $data = $this->validated($request);
        $data['tools_used'] = $this->decodeTools($data['tools_used'] ?? null);

        // uploads: if new file provided, replace and delete old
        $paths = $this->storeUploads($request, $project);
        $data  = array_merge($data, $paths);

        // If new images uploaded, they replaced the entire set. If none uploaded, keep existing.
        if (!array_key_exists('images', $paths)) {
            unset($data['images']); // keep old
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeTenant($project);

        // delete stored files
        if ($project->video_path) Storage::disk('public')->delete($project->video_path);
        if ($project->documentation_path) Storage::disk('public')->delete($project->documentation_path);
        if (is_array($project->images)) {
            foreach ($project->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $project->delete();

        return back()->with('success', 'Project deleted.');
    }

    // ===== Helpers =====

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name'            => ['required','string','max:200'],
            'type'            => ['required','string','max:120'],
            'client'          => ['required','string','max:200'],
            'duration_weeks'  => ['required','integer','min:1','max:520'],
            'live_link'       => ['nullable','url','max:255'],
            'description'     => ['nullable','string'],

            // uploads
            'video'           => ['nullable','file','mimetypes:video/avi,video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/x-matroska','max:51200'], // ~50MB
            'documentation'   => ['nullable','file','mimetypes:application/pdf,application/zip,application/x-zip-compressed,application/x-rar-compressed,application/vnd.rar','max:20480'], // ~20MB
            'images'          => ['nullable','array'],
            'images.*'        => ['file','image','max:5120'], // each <=5MB

            // tags
            'tools_used'      => ['nullable'], // JSON or CSV string
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



    public function media(Project $project, string $kind, ?int $index = null)
{
    $this->authorizeTenant($project);

    $path = null;
    switch ($kind) {
        case 'video':
            $path = $project->video_path;
            break;
        case 'doc':
            $path = $project->documentation_path;
            break;
        case 'image':
            $images = is_array($project->images) ? $project->images : [];
            $i = is_numeric($index) ? (int)$index : 0;
            $path = $images[$i] ?? null;
            break;
        default:
            abort(404);
    }

    if (!$path || !Storage::disk('public')->exists($path)) {
        abort(404);
    }

    // stream in browser (use ->download($path) if you want forced download)
    return Storage::disk('public')->response($path);
}

    protected function storeUploads(Request $request, ?Project $existing = null): array
    {
        $out = [];

        if ($request->hasFile('video')) {
            if ($existing && $existing->video_path) {
                Storage::disk('public')->delete($existing->video_path);
            }
            $out['video_path'] = $request->file('video')->store('projects/videos', 'public');
        }

        if ($request->hasFile('documentation')) {
            if ($existing && $existing->documentation_path) {
                Storage::disk('public')->delete($existing->documentation_path);
            }
            $out['documentation_path'] = $request->file('documentation')->store('projects/docs', 'public');
        }

        if ($request->hasFile('images')) {
            // replace the entire set if new images provided
            if ($existing && is_array($existing->images)) {
                foreach ($existing->images as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('projects/images', 'public');
            }
            $out['images'] = $paths;
        }

        return $out;
    }

    protected function tenantId(): int
    {
        $user = auth()->user();
        if ($user && $user->tenant_id) return (int) $user->tenant_id;
        // Dev fallback so you can test before wiring tenant on users
        return 1;
    }

    protected function authorizeTenant(Project $project): void
    {
        if ((int) $project->tenant_id !== $this->tenantId()) {
            abort(404);
        }
    }
}
