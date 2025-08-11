<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    // List (scoped to tenant)
 public function index()
{
    $tenantId = Auth::user()->id;
    $q = request('q');

    $skills = Skill::where('tenant_id', $tenantId)
        ->when($q, function ($query) use ($q) {
            $query->where(function ($s) use ($q) {
                $s->where('first_name', 'like', "%{$q}%")
                  ->orWhere('last_name', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            });
        })
        ->orderBy('id', 'asc')      // 👈 oldest first (newly added appears at the bottom)
        ->paginate(10)
        ->withQueryString();

    return view('admin.skills.index', compact('skills', 'q'));
}

    public function create()
    {
        $skill = null;
        return view('admin.skills.create', compact('skill'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'        => ['required','string','max:100'],
            'last_name'         => ['required','string','max:100'],
            'category'          => ['required','string','max:150'],
            'experience_years'  => ['required','integer','min:0','max:50'],
            'tools'             => ['nullable'],
            'notes'             => ['nullable','string'],
        ]);

        // Assign tenant (safe fallback so you don't 500 while setting up tenanting)
        $data['tenant_id'] = Auth::user()->id;

        // Decode tools JSON -> array
        if (!empty($data['tools']) && is_string($data['tools'])) {
            $decoded = json_decode($data['tools'], true);
            $data['tools'] = is_array($decoded) ? array_values(array_filter($decoded)) : [];
        } elseif (!is_array($data['tools'])) {
            $data['tools'] = [];
        }

        $created = Skill::create($data);

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill saved. ID: '.$created->id);
    }

    public function edit(Skill $skill)
    {
        $this->authorizeTenant($skill);
        return view('admin.skills.create', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $this->authorizeTenant($skill);

        $data = $request->validate([
            'first_name'        => ['required','string','max:100'],
            'last_name'         => ['required','string','max:100'],
            'category'          => ['required','string','max:150'],
            'experience_years'  => ['required','integer','min:0','max:50'],
            'tools'             => ['nullable'],
            'notes'             => ['nullable','string'],
        ]);

        if (!empty($data['tools']) && is_string($data['tools'])) {
            $decoded = json_decode($data['tools'], true);
            $data['tools'] = is_array($decoded) ? array_values(array_filter($decoded)) : [];
        } elseif (!is_array($data['tools'])) {
            $data['tools'] = [];
        }

        $skill->update($data);

        return redirect()->route('admin.skills.index')->with('success', 'Skill updated.');
    }

    public function destroy(Skill $skill)
    {
        $this->authorizeTenant($skill);
        $skill->delete();

        return back()->with('success', 'Skill deleted.');
    }



    protected function authorizeTenant(Skill $skill): void
    {
        if ((int) $skill->tenant_id !== Auth::user()->id) {
            abort(404);
        }
    }
}
