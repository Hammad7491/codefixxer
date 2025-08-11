<?php

namespace App\Http\Controllers\Admin;

use App\Models\Education;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->id;
        $q = request('q', '');

        $educations = Education::where('tenant_id', $tenantId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($s) use ($q) {
                    $s->where('degree_name', 'like', "%{$q}%")
                      ->orWhere('institute_name', 'like', "%{$q}%")
                      ->orWhere('field_of_study', 'like', "%{$q}%")
                      ->orWhere('location', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'asc') // newest appears last
            ->paginate(10)
            ->withQueryString();

        return view('admin.educations.index', compact('educations', 'q'));
    }

    public function create()
    {
        $education = null;
        return view('admin.educations.create', compact('education'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['tenant_id'] = Auth::user()->id;
        $data['certifications'] = $this->decodeArray($data['certifications'] ?? null);

        Education::create($data);

        return redirect()
            ->route('admin.educations.index')
            ->with('success', 'Education saved.');
    }

    public function edit(Education $education)
    {
        $this->authorizeTenant($education);
        return view('admin.educations.create', compact('education'));
    }

    public function update(Request $request, Education $education)
    {
        $this->authorizeTenant($education);

        $data = $this->validated($request);
        $data['certifications'] = $this->decodeArray($data['certifications'] ?? null);

        $education->update($data);

        return redirect()
            ->route('admin.educations.index')
            ->with('success', 'Education updated.');
    }

    public function destroy(Education $education)
    {
        $this->authorizeTenant($education);
        $education->delete();

        return back()->with('success', 'Education deleted.');
    }

    // ---- helpers ----

    protected function validated(Request $request): array
    {
        return $request->validate([
            'degree_name'     => ['required','string','max:100'],
            'institute_name'  => ['required','string','max:200'],
            'start_date'      => ['required','date'],
            'end_date'        => ['nullable','date','after_or_equal:start_date'],
            'field_of_study'  => ['required','string','max:150'],
            'grade_gpa'       => ['nullable','string','max:50'],
            'location'        => ['nullable','string','max:150'],
            'certifications'  => ['nullable'], // hidden JSON
        ]);
    }

    protected function decodeArray($val): array
    {
        if (is_array($val)) {
            return array_values(array_filter($val));
        }

        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') return [];
            $decoded = json_decode($val, true);
            return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                ? array_values(array_filter($decoded))
                : [];
        }

        return [];
    }

    protected function authorizeTenant(Education $education): void
    {
        if ((int) $education->tenant_id !== Auth::user()->id) {
            abort(404);
        }
    }
}
