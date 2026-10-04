<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'System Administrator',
            'Center Manager/Director',
            'Clinical/Medical Staff',
            'Physiotherapist',
            'Vocational Training Expert',
            'Occupational Therapist',
            'Speech/Language Therapist',
            'Nurse',
            'Teacher',
            'Head Teacher / Education Coordinator',
            'Special Education Teacher',
            'Teaching Assistant',
            'School Records Officer',
            'School Administrator',
            'Examination/Assessment Officer',
            'Social Worker',
            'Psychologist/Counselor',
            'Matron/Patron',
            'Reception/Records Officer',
            'Pharmacy/Store Officer',
            'Finance/Accounts Officer',
            'Data/Reporting Officer',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}