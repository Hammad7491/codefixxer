<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1) all your permissions…
        $permissions = [
            // dashboard
            'view dashboard',

            // clients
            'create clients','view clients','edit clients','delete clients',

            // users
            'create users','view users','edit users','delete users',

            // roles
            'create roles','view roles','edit roles','delete roles',

            // permissions
            'create permissions','view permissions','edit permissions','delete permissions',

            // projects (sites)
            'create projects','view projects','edit projects','delete projects',

            // purchase orders
            'create purchase orders','view purchase orders','edit purchase orders','delete purchase orders',

            // subtasks
            'create subtasks','view subtasks','edit subtasks','delete subtasks',

            // notes
            'create notes','view notes','edit notes','delete notes',

            // events
            'create events','view events','edit events','delete events',

            // reminders
            'create reminders','view reminders','edit reminders','delete reminders',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2) define roles & assign permissions
        $roles = [
            'Admin' => Permission::all()->pluck('name')->toArray(),

            'Site Manager' => array_merge(
                ['view dashboard','view users','view clients'],
                [
                    'create projects','view projects','edit projects','delete projects',
                    'create purchase orders','view purchase orders','edit purchase orders','delete purchase orders',
                    'create subtasks','view subtasks','edit subtasks','delete subtasks',
                    'create notes','view notes','edit notes','delete notes',
                    'create events','view events','edit events','delete events',
                    'create reminders','view reminders','edit reminders','delete reminders',
                ]
            ),

            'Collaborator' => [
                'view dashboard','view projects','view purchase orders','view subtasks',
                'create notes','view notes','edit notes','delete notes',
                'view events','view reminders',
            ],

            'Client' => [
                'view dashboard','view projects','view purchase orders','view events',
            ],
        ];

        foreach ($roles as $name => $perms) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->syncPermissions($perms);
        }
    }
}
