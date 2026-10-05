<?php

namespace App\Support;

class Permissions
{
    /**
     * All module names from the GCRC sidebar.
     */
    public const MODULES = [
        // System
        'users', 'roles', 'permissions', 'settings', 'audit', 'login_history',

        // Children
        'children', 'guardians', 'referrals',

        // Clinical
        'assessments', 'diagnoses', 'treatment_plans', 'therapy_records',
        'admissions', 'appointments',

        // Education
        'students', 'academic_years', 'classes', 'curriculum', 'ieps',
        'lessons', 'attendance', 'performance', 'exams', 'timetables',

        // Operations
        'staff', 'staff_attendance', 'daily_activities', 'pharmacy',
        'equipment', 'nutrition',

        // Community
        'social_work', 'outreach', 'documents',

        // Reports
        'reports',
    ];

    /**
     * Default actions applied to every module.
     */
    public const DEFAULT_ACTIONS = ['view', 'create', 'edit', 'delete'];

    /**
     * Extra actions beyond the default set.
     */
    public const EXTRA_ACTIONS = [
        'children'        => ['export', 'view_medical'],
        'assessments'     => ['approve'],
        'diagnoses'       => ['approve'],           // ← added
        'treatment_plans' => ['approve'],
        'admissions'      => ['discharge', 'transfer'],
        'pharmacy'        => ['dispense'],
        'equipment'       => ['assign', 'return'],
        'users'           => ['activate', 'deactivate'],
        'staff'           => ['view_sensitive'],    // ← also present
        'reports'         => ['export', 'clinical', 'education', 'operational'],
    ];

    public static function all(): array
    {
        $permissions = [];

        foreach (self::MODULES as $module) {
            foreach (self::DEFAULT_ACTIONS as $action) {
                $permissions[] = "{$module}.{$action}";
            }

            if (isset(self::EXTRA_ACTIONS[$module])) {
                foreach (self::EXTRA_ACTIONS[$module] as $action) {
                    $permissions[] = "{$module}.{$action}";
                }
            }
        }

        return array_unique($permissions);
    }
}