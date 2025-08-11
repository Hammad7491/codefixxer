<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /** List (tenant-scoped). Ordered oldest->newest so newly added appears last like your other lists. */
    public function index()
    {
        $tenantId = $this->tenantId();
        $q = request('q', '');

        $languages = Language::where('tenant_id', $tenantId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%")
                      ->orWhere('proficiency', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'asc')   // newest last
            ->paginate(10)
            ->withQueryString();

        return view('admin.languages.index', compact('languages', 'q'));
    }

    /** Create form */
    public function create()
    {
        $language = null;
        return view('admin.languages.create', compact('language'));
    }

    /** Store */
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['tenant_id'] = $this->tenantId();

        Language::create($data);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language saved.');
    }

    /** Edit form */
    public function edit(Language $language)
    {
        $this->authorizeTenant($language);
        return view('admin.languages.create', compact('language'));
    }

    /** Update */
    public function update(Request $request, Language $language)
    {
        $this->authorizeTenant($language);

        $data = $this->validated($request);

        // keep tenant_id unchanged
        $language->update($data);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated.');
    }

    /** Delete */
    public function destroy(Language $language)
    {
        $this->authorizeTenant($language);
        $language->delete();

        return back()->with('success', 'Language deleted.');
    }

    // ===== Helpers =====

    protected function validated(Request $request): array
    {
        // if you want to hard-enforce allowed values, you can switch to 'in:English,Urdu,...'
        return $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'proficiency' => ['required', 'string', 'max:50'], // Beginner/Intermediate/Advanced/Fluent/Native
        ]);
    }

    protected function tenantId(): int
    {
        $user = auth()->user();
        if (!$user) abort(401, 'Please log in.');
        // same friendly fallback you used elsewhere
        return (int) ($user->tenant_id ?? 1);
    }

    protected function authorizeTenant(Language $language): void
    {
        if ((int) $language->tenant_id !== $this->tenantId()) {
            abort(404); // hide across tenants
        }
    }
}
