<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // — Admin User —
        $admin = User::updateOrCreate(
            ['name' => 'Admin User'],
            [
                'email'    => 'a@a',
                'password' => Hash::make('a'),
            ]
        );
        $admin->syncRoles('Admin');

        // — Site Manager User —
        $manager = User::updateOrCreate(
            ['name' => 'Site Manager User'],
            [
                'email'    => 'manager@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $manager->syncRoles('Site Manager');

        // — Collaborator User —
        $collaborator = User::updateOrCreate(
            ['name' => 'Collaborator User'],
            [
                'email'    => 'collaborator@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $collaborator->syncRoles('Collaborator');

        // — Client User —
        $client = User::updateOrCreate(
            ['name' => 'Client User'],
            [
                'email'    => 'client@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $client->syncRoles('Client');
    }
}
