<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Which permissions each role receives.
     * Wildcard "*" grants everything.
     */
    private array $rolePermissions = [
        'System Administrator' => ['*'],

        'Center Manager/Director' => [
            'children.*', 'staff.*', 'reports.*', 'users.view',
            'assessments.view', 'treatment_plans.view', 'admissions.*',
            'appointments.*', 'daily_activities.*',
        ],

        'Clinical/Medical Staff' => [
            'children.view', 'children.edit', 'children.view_medical',
            'assessments.*', 'diagnoses.*', 'treatment_plans.*',
            'therapy_records.*', 'admissions.view', 'appointments.*',
        ],

        'Physiotherapist' => [
            'children.view', 'assessments.view', 'assessments.create', 'assessments.edit',
            'treatment_plans.view', 'treatment_plans.create', 'treatment_plans.edit',
            'therapy_records.*', 'appointments.view', 'appointments.create',
        ],

        'Occupational Therapist' => [
            'children.view', 'assessments.view', 'assessments.create', 'assessments.edit',
            'treatment_plans.view', 'treatment_plans.create', 'treatment_plans.edit',
            'therapy_records.*', 'appointments.view', 'appointments.create',
        ],

        'Speech/Language Therapist' => [
            'children.view', 'assessments.view', 'assessments.create', 'assessments.edit',
            'treatment_plans.view', 'treatment_plans.create', 'treatment_plans.edit',
            'therapy_records.*', 'appointments.view', 'appointments.create',
        ],

        'Nurse' => [
            'children.view', 'children.view_medical', 'assessments.view',
            'pharmacy.view', 'pharmacy.dispense', 'admissions.view',
            'appointments.view', 'appointments.create',
        ],

        'Teacher' => [
            'students.view', 'classes.view', 'lessons.*', 'attendance.view', 'attendance.create',
            'ieps.view', 'ieps.create', 'ieps.edit', 'performance.view', 'performance.create',
        ],

        'Head Teacher / Education Coordinator' => [
            'students.*', 'academic_years.*', 'classes.*', 'curriculum.*',
            'ieps.*', 'lessons.*', 'attendance.*', 'performance.*', 'exams.*',
            'timetables.*', 'reports.education',
        ],

        'Special Education Teacher' => [
            'students.view', 'students.edit', 'classes.view', 'curriculum.view',
            'ieps.*', 'lessons.*', 'attendance.view', 'attendance.create',
            'performance.*', 'timetables.view',
        ],

        'Social Worker' => [
            'children.view', 'children.edit', 'guardians.*', 'social_work.*',
            'referrals.*', 'documents.*',
        ],

        'Psychologist/Counselor' => [
            'children.view', 'children.view_medical', 'assessments.view', 'assessments.create',
            'treatment_plans.view', 'social_work.view', 'social_work.create',
            'appointments.view', 'appointments.create',
        ],

        'Reception/Records Officer' => [
            'children.view', 'children.create', 'children.edit', 'guardians.*',
            'appointments.*', 'documents.*',
        ],

        'Pharmacy/Store Officer' => [
            'pharmacy.*', 'equipment.view', 'equipment.edit',
        ],

        'Finance/Accounts Officer' => [
            'reports.view', 'reports.export', 'documents.view',
        ],

        'Data/Reporting Officer' => [
            'reports.*', 'children.view', 'students.view', 'staff.view',
            'assessments.view', 'admissions.view', 'appointments.view',
        ],

        'Matron/Patron' => [
            'children.view', 'daily_activities.*', 'nutrition.*',
            'appointments.view', 'staff_attendance.view',
        ],

        'Vocational Training Expert' => [
            'students.view', 'lessons.view', 'lessons.create', 'performance.view',
            'performance.create', 'ieps.view',
        ],

        // These roles exist but may have minimal or no permissions yet
        'Teaching Assistant' => [
            'students.view', 'attendance.view', 'attendance.create', 'lessons.view',
        ],

        'School Records Officer' => [
            'students.view', 'students.create', 'students.edit', 'attendance.*',
            'performance.view', 'exams.view', 'documents.*',
        ],

        'School Administrator' => [
            'students.*', 'academic_years.*', 'classes.*', 'timetables.*',
            'reports.education',
        ],

        'Examination/Assessment Officer' => [
            'exams.*', 'performance.*', 'students.view', 'reports.education',
        ],
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->rolePermissions as $roleName => $patterns) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($patterns === ['*']) {
                $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
                continue;
            }

            $permissionNames = [];
            foreach ($patterns as $pattern) {
                if (str_ends_with($pattern, '.*')) {
                    $prefix = str_replace('.*', '', $pattern);
                    $permissionNames = array_merge(
                        $permissionNames,
                        \Spatie\Permission\Models\Permission::where('name', 'like', "{$prefix}.%")
                            ->pluck('name')
                            ->toArray()
                    );
                } else {
                    $permissionNames[] = $pattern;
                }
            }

            $role->syncPermissions(array_unique($permissionNames));
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}