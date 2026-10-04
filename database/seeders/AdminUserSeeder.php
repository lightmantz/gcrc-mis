<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gcrc.local'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('ChangeMe!2025'),
            ]
        );

        $admin->assignRole('System Administrator');
    }
}