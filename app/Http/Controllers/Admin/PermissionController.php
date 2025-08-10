<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|unique:permissions,name'
        ]);
        Permission::create($data);

        return redirect()->route('admin.permissions.index')
                         ->with('success','Permission created.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.create', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name'=>'required|unique:permissions,name,'.$permission->id
        ]);
        $permission->update($data);

        return redirect()->route('admin.permissions.index')
                         ->with('success','Permission updated.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
                         ->with('success','Permission deleted.');
    }
}
