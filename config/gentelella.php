<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Menu\Filters\ActiveFilter;
use ColorlibHQ\Gentelella\Menu\Filters\GateFilter;
use ColorlibHQ\Gentelella\Menu\Filters\HrefFilter;
use ColorlibHQ\Gentelella\Menu\Filters\SearchFilter;

return [

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */

    'title' => 'GCRC-MIS',
    'title_prefix' => '',
    'title_postfix' => '',

    'brand_name' => 'GCRC',
    'brand_suffix' => 'MIS',
    'brand_initial' => 'G',

    /*
    |--------------------------------------------------------------------------
    | Document
    |--------------------------------------------------------------------------
    */

    'favicon' => null,
    'manifest' => null,
    'apple_touch_icon' => null,
    'google_fonts' => true,
    'service_worker' => false,

    /*
    |--------------------------------------------------------------------------
    | Sidebar user block
    |--------------------------------------------------------------------------
    */

    'user' => [
        'enabled' => true,
        'name_field' => 'name',
        'role_field' => 'role',
        'fallback_name' => 'Guest',
        'fallback_role' => 'Visitor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Sidebar menu
    |--------------------------------------------------------------------------
    */

    'menu' => [

        // ─── DASHBOARD ─────────────────────────────────────
        [
            'label' => 'OVERVIEW',
            'items' => [
                ['key' => 'dashboard', 'text' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
            ],
        ],

        // ─── 4.2 CHILD REGISTRATION ────────────────────────
        [
            'label' => 'CHILDREN',
            'items' => [
                ['key' => 'children',        'text' => 'All Children',   'icon' => 'users',    'route' => 'admin.children.index'],
                ['key' => 'children.create', 'text' => 'Register Child', 'icon' => 'user-plus','route' => 'admin.children.create'],
                ['key' => 'guardians',       'text' => 'Guardians',      'icon' => 'heart',    'route' => 'admin.guardians.index'],
                // ['key' => 'referrals',    'text' => 'Referrals',      'icon' => 'share',    'route' => 'admin.referrals.index'],
            ],
        ],

        // ─── 4.3 – 4.8 CLINICAL ────────────────────────────
        [
            'label' => 'CLINICAL',
            'items' => [
                // Unlock each item as its module is built.
                // ['key' => 'assessments',     'text' => 'Assessments',     'icon' => 'clipboard', 'route' => 'admin.assessments.index'],
                // ['key' => 'diagnoses',       'text' => 'Diagnoses',       'icon' => 'medical',   'route' => 'admin.diagnoses.index'],
                // ['key' => 'treatment-plans', 'text' => 'Treatment Plans', 'icon' => 'heart',     'route' => 'admin.treatment-plans.index'],
                // ['key' => 'therapy-records', 'text' => 'Therapy Records', 'icon' => 'activity',  'route' => 'admin.therapy-records.index'],
                // ['key' => 'admissions',      'text' => 'Admissions',      'icon' => 'login',     'route' => 'admin.admissions.index'],
                // ['key' => 'appointments',    'text' => 'Appointments',    'icon' => 'calendar',  'route' => 'admin.appointments.index'],
            ],
        ],

        // ─── 4.11 SPECIAL EDUCATION ────────────────────────
        [
            'label' => 'EDUCATION',
            'items' => [
                // Unlock each item as its module is built.
                // ['key' => 'students',    'text' => 'Students',              'icon' => 'education', 'route' => 'admin.students.index'],
                // ['key' => 'academic',    'text' => 'Academic Years',        'icon' => 'calendar',  'route' => 'admin.academic-years.index'],
                // ['key' => 'classes',     'text' => 'Classes & Grades',      'icon' => 'grid',      'route' => 'admin.classes.index'],
                // ['key' => 'curriculum',  'text' => 'Curriculum & Subjects', 'icon' => 'book',      'route' => 'admin.curriculum.index'],
                // ['key' => 'iep',         'text' => 'IEPs',                  'icon' => 'target',    'route' => 'admin.ieps.index'],
                // ['key' => 'lessons',     'text' => 'Lesson Plans',          'icon' => 'file-text', 'route' => 'admin.lessons.index'],
                // ['key' => 'attendance',  'text' => 'Student Attendance',    'icon' => 'check',     'route' => 'admin.attendance.index'],
                // ['key' => 'performance', 'text' => 'Academic Performance',  'icon' => 'chart',     'route' => 'admin.performance.index'],
                // ['key' => 'exams',       'text' => 'Examinations',          'icon' => 'award',     'route' => 'admin.exams.index'],
                // ['key' => 'timetables',  'text' => 'Teacher Timetables',    'icon' => 'clock',     'route' => 'admin.timetables.index'],
            ],
        ],

        // ─── 4.9 STAFF + 4.10 DAILY OPERATIONS ─────────────
        [
            'label' => 'OPERATIONS',
            'items' => [
                ['key' => 'staff', 'text' => 'Staff', 'icon' => 'users', 'route' => 'admin.staff.index'],
                // Unlock each item as its module is built.
                // ['key' => 'staff.attendance', 'text' => 'Staff Attendance',  'icon' => 'check',    'route' => 'admin.staff-attendance.index'],
                // ['key' => 'daily-activities', 'text' => 'Daily Activities',  'icon' => 'sun',      'route' => 'admin.daily-activities.index'],
                // ['key' => 'pharmacy',         'text' => 'Pharmacy',          'icon' => 'pill',     'route' => 'admin.pharmacy.index'],
                // ['key' => 'equipment',        'text' => 'Equipment',         'icon' => 'tool',     'route' => 'admin.equipment.index'],
                // ['key' => 'nutrition',        'text' => 'Nutrition & Meals', 'icon' => 'coffee',   'route' => 'admin.nutrition.index'],
            ],
        ],

        // ─── 4.15 – 4.17 COMMUNITY ─────────────────────────
        [
            'label' => 'COMMUNITY',
            'items' => [
                // Unlock each item as its module is built.
                // ['key' => 'social-work', 'text' => 'Social Work', 'icon' => 'heart',  'route' => 'admin.social-work.index'],
                // ['key' => 'outreach',    'text' => 'Outreach',    'icon' => 'globe',  'route' => 'admin.outreach.index'],
                // ['key' => 'documents',   'text' => 'Documents',   'icon' => 'folder', 'route' => 'admin.documents.index'],
            ],
        ],

        // ─── 4.19 REPORTS ──────────────────────────────────
        [
            'label' => 'REPORTS',
            'items' => [
                // Unlock each item as its module is built.
                // ['key' => 'reports',            'text' => 'All Reports',         'icon' => 'chart',    'route' => 'admin.reports.index'],
                // ['key' => 'reports.clinical',   'text' => 'Clinical Reports',    'icon' => 'activity', 'route' => 'admin.reports.clinical'],
                // ['key' => 'reports.education',  'text' => 'Education Reports',   'icon' => 'book',     'route' => 'admin.reports.education'],
                // ['key' => 'reports.operational','text' => 'Operational Reports', 'icon' => 'settings', 'route' => 'admin.reports.operational'],
            ],
        ],

        // ─── 4.1 SYSTEM & ACCESS ───────────────────────────
        [
            'label' => 'SYSTEM',
            'items' => [
                ['key' => 'users',  'text' => 'User Accounts',       'icon' => 'users',    'route' => 'admin.users.index'],
                ['key' => 'roles',  'text' => 'Roles & Permissions', 'icon' => 'lock',     'route' => 'admin.roles.index'],
                ['key' => 'audit',  'text' => 'Audit Trail',         'icon' => 'activity', 'route' => 'admin.audit.index'],
                ['key' => 'logins', 'text' => 'Login History',       'icon' => 'clock',    'route' => 'admin.login-history.index'],
            ],
        ],
    ],

    'filters' => [
        GateFilter::class,
        HrefFilter::class,
        ActiveFilter::class,
        SearchFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Topbar
    |--------------------------------------------------------------------------
    */

    'docs_url' => 'https://gentelella.colorlib.com/docs/laravel/',

    /*
    |--------------------------------------------------------------------------
    | Shell links
    |--------------------------------------------------------------------------
    */

    'links' => [
        'profile' => null,
        'settings' => null,
        'theme' => null,
        'help' => null,
        'lock' => null,
        'logout' => 'logout',
    ],
    'search_enabled' => true,
    'theme_toggle' => true,
    'notifications_enabled' => true,
    'messages_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer_left' => 'Gentelella — free admin dashboard template by <a href="https://colorlib.com">Colorlib</a>',
    'footer_right' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'enabled' => (bool) env('GENTELELLA_AUTH', true),
        'prefix' => '',
        'middleware' => ['web'],
        'register' => true,
        'reset' => true,
        'home' => '/',
        'throttle' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo pages
    |--------------------------------------------------------------------------
    */

    'demo' => (bool) env('GENTELELLA_DEMO', false),
    'demo_prefix' => 'demo',
    'demo_middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Demo account
    |--------------------------------------------------------------------------
    */

    'demo_user' => [
        'name' => 'Demo User',
        'email' => 'demo@example.com',
        'password' => 'Gentelella-Demo-7Fq2-Vx9k-Rm4t',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vite entry points
    |--------------------------------------------------------------------------
    */

    'vite' => [
        'resources/js/gentelella.js',
    ],

];