<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ create roles & permissions first
        $this->call(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // 2️⃣ then create users and assign those roles
        $this->call(\Database\Seeders\UserSeeder::class);
    }
}
