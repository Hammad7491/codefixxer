<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);

        return redirect()->route('admin.roles.index')
                         ->with('success','Role created successfully.');
    }

  public function edit(Role $role)
    {
        // fetch all permissions
        $permissions    = Permission::all();
        // get names of permissions this role already has
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.create', compact('role','permissions','rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'        => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'required|array|min:1',
        ]);

        // update name
        $role->update(['name' => $data['name']]);
        // sync permissions
        $role->syncPermissions($data['permissions']);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')
                         ->with('success','Role deleted successfully.');
    }
}
