<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order is critical:
     *   1. Roles must exist before permissions are attached to them.
     *   2. Permissions must exist before they can be assigned to roles.
     *   3. Settings are independent and can run any time.
     *   4. The admin user must be created after its role has permissions,
     *      so that the user inherits the full permission set.
     *   5. Demo data (users, staff, children, assessments, …) runs last so
     *      it can attach roles, link to staff, and reference the admin
     *      account for finalized-by fields.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            AdminUserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}