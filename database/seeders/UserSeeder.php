<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => 'password',
                'full_name' => 'Super Admin',
                'role' => 'super_admin',
                'status' => 'active',
                'email' => 'admin@school.com',
                'phone' => '012345678',
            ]
        );
    }
}
