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
    |
    | `title` is the base <title>; each page prepends its own section name.
    | The sidebar brand renders `brand_initial` in the square badge, then
    | `brand_name` with `brand_suffix` as a muted <small>.
    |
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
    |
    | Asset paths are resolved with asset(), so they are relative to public/.
    | `manifest` and `apple_touch_icon` are null by default: the PWA files are
    | opt-in, and pointing at one that was never published only buys a 404.
    |
    */

    'favicon' => null,
    'manifest' => null,
    'apple_touch_icon' => null,
    'google_fonts' => true,

    // The design system registers a service worker in production builds. Leave
    // this off unless you have actually published a sw.js to public/ — an app
    // that has not would take a 404 on every page load for a file it never had.
    'service_worker' => false,

    /*
    |--------------------------------------------------------------------------
    | Sidebar user block
    |--------------------------------------------------------------------------
    |
    | Read off the authenticated user when there is one. The fallbacks are what
    | the demo shows to a guest.
    |
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
    |
    | `middleware` wraps every route registered through Route::gentelella().
    | It needs to be session-backed: the forms post with CSRF, flash messages
    | come back through the session, and validation errors only reach the views
    | via the web group's ShareErrorsFromSession middleware.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Sidebar menu
    |--------------------------------------------------------------------------
    |
    | Leave `menu` as null to use the bundled demo sidebar (resources/menu.php,
    | generated from NAV in the upstream template). Set an array to replace it.
    |
    | Each group is ['label' => ..., 'items' => [...]]. An item is a leaf:
    |
    |     ['key' => 'products', 'text' => 'Products', 'icon' => 'shop',
    |      'route' => 'admin.products.index']
    |
    | ...or a parent carrying 'children' => [...]. Address the target with one
    | of 'route' (a route name), 'url' (verbatim), or 'page' (a demo page slug).
    | `key` is matched against the current page key to mark an item active.
    |
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
            ['key' => 'children',        'text' => 'All Children',   'icon' => 'users',    'route' => 'children.index'],
            ['key' => 'children.create', 'text' => 'Register Child', 'icon' => 'user-plus','route' => 'children.create'],
            ['key' => 'referrals',       'text' => 'Referrals',      'icon' => 'share',    'route' => 'referrals.index'],
        ],
    ],

    // ─── 4.3 – 4.8 CLINICAL ────────────────────────────
    [
        'label' => 'CLINICAL',
        'items' => [
            ['key' => 'assessments',     'text' => 'Assessments',     'icon' => 'clipboard', 'route' => 'assessments.index'],
            ['key' => 'diagnoses',       'text' => 'Diagnoses',       'icon' => 'medical',   'route' => 'diagnoses.index'],
            ['key' => 'treatment-plans', 'text' => 'Treatment Plans', 'icon' => 'heart',     'route' => 'treatment-plans.index'],
            ['key' => 'therapy-records', 'text' => 'Therapy Records', 'icon' => 'activity',  'route' => 'therapy-records.index'],
            ['key' => 'admissions',      'text' => 'Admissions',      'icon' => 'login',     'route' => 'admissions.index'],
            ['key' => 'appointments',    'text' => 'Appointments',    'icon' => 'calendar',  'route' => 'appointments.index'],
        ],
    ],

    // ─── 4.11 SPECIAL EDUCATION ────────────────────────
    [
        'label' => 'EDUCATION',
        'items' => [
            ['key' => 'students',    'text' => 'Students',              'icon' => 'education', 'route' => 'students.index'],
            ['key' => 'academic',    'text' => 'Academic Years',        'icon' => 'calendar',  'route' => 'academic-years.index'],
            ['key' => 'classes',     'text' => 'Classes & Grades',      'icon' => 'grid',      'route' => 'classes.index'],
            ['key' => 'curriculum',  'text' => 'Curriculum & Subjects', 'icon' => 'book',      'route' => 'curriculum.index'],
            ['key' => 'iep',         'text' => 'IEPs',                  'icon' => 'target',    'route' => 'ieps.index'],
            ['key' => 'lessons',     'text' => 'Lesson Plans',          'icon' => 'file-text', 'route' => 'lessons.index'],
            ['key' => 'attendance',  'text' => 'Student Attendance',    'icon' => 'check',     'route' => 'attendance.index'],
            ['key' => 'performance', 'text' => 'Academic Performance',  'icon' => 'chart',     'route' => 'performance.index'],
            ['key' => 'exams',       'text' => 'Examinations',          'icon' => 'award',     'route' => 'exams.index'],
            ['key' => 'timetables',  'text' => 'Teacher Timetables',    'icon' => 'clock',     'route' => 'timetables.index'],
        ],
    ],

    // ─── 4.9 STAFF + 4.10 DAILY OPERATIONS ─────────────
    [
        'label' => 'OPERATIONS',
        'items' => [
            ['key' => 'staff', 'text' => 'Staff', 'icon' => 'users', 'route' => 'admin.staff.index'],
            ['key' => 'staff.attendance','text' => 'Staff Attendance', 'icon' => 'check',    'route' => 'staff.attendance'],
            ['key' => 'daily-activities','text' => 'Daily Activities', 'icon' => 'sun',      'route' => 'daily-activities.index'],
            ['key' => 'pharmacy',       'text' => 'Pharmacy',          'icon' => 'pill',     'route' => 'pharmacy.index'],
            ['key' => 'equipment',      'text' => 'Equipment',         'icon' => 'tool',     'route' => 'equipment.index'],
            ['key' => 'nutrition',      'text' => 'Nutrition & Meals', 'icon' => 'coffee',   'route' => 'nutrition.index'],
        ],
    ],

    // ─── 4.15 – 4.17 COMMUNITY ─────────────────────────
    [
        'label' => 'COMMUNITY',
        'items' => [
            ['key' => 'social-work',  'text' => 'Social Work',   'icon' => 'heart',    'route' => 'social-work.index'],
            ['key' => 'outreach',     'text' => 'Outreach',      'icon' => 'globe',    'route' => 'outreach.index'],
            ['key' => 'documents',    'text' => 'Documents',     'icon' => 'folder',   'route' => 'documents.index'],
        ],
    ],

    // ─── 4.19 REPORTS ──────────────────────────────────
    [
        'label' => 'REPORTS',
        'items' => [
            ['key' => 'reports',           'text' => 'All Reports',         'icon' => 'chart',    'route' => 'reports.index'],
            ['key' => 'reports.clinical',  'text' => 'Clinical Reports',    'icon' => 'activity', 'route' => 'reports.clinical'],
            ['key' => 'reports.education', 'text' => 'Education Reports',   'icon' => 'book',     'route' => 'reports.education'],
            ['key' => 'reports.operational','text' => 'Operational Reports','icon' => 'settings', 'route' => 'reports.operational'],
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

    // The Laravel edition's own documentation, not the HTML edition's — the
    // two cover different things, and a Blade developer sent to the static
    // template's docs finds nothing about panels, fields or filters.
    'docs_url' => 'https://gentelella.colorlib.com/docs/laravel/',

    /*
    |--------------------------------------------------------------------------
    | Shell links
    |--------------------------------------------------------------------------
    |
    | Where the account menu and the Cmd+K palette send people. Each value is a
    | route name or an absolute path; an entry that resolves to neither is left
    | out, and the affected menu item simply does not appear.
    |
    | Without these the design system falls back to the static template's own
    | pages — profile.html and friends — which do not exist in an application.
    |
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
    |
    | Ready-made login, registration and password-reset screens on the template's
    | own auth markup. Off by default: an application that already has auth — a
    | starter kit, Fortify, Breeze — should keep its own routes and simply point
    | them at the `gentelella::auth.*` views.
    |
    | `php artisan gentelella:make-auth` copies the controllers and views into
    | your application when you want to own them outright.
    |
    */

    'auth' => [
        'enabled' => (bool) env('GENTELELLA_AUTH', true),
        'prefix' => '',
        'middleware' => ['web'],

        // Each screen can be switched off on its own — a closed system wants
        // login without registration.
        'register' => true,
        'reset' => true,

        // Where a signed-in user lands, and where an unauthenticated one is sent.
        'home' => '/',

        // Failed logins per minute, keyed by email + IP.
        'throttle' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo pages
    |--------------------------------------------------------------------------
    |
    | The bundled showcase — every page from the static template, served under
    | `demo_prefix`. Off by default so a consumer app ships nothing it did not
    | ask for; the live preview turns it on.
    |
    */

    'demo' => (bool) env('GENTELELLA_DEMO', false),
    'demo_prefix' => 'demo',
    'demo_middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Demo account
    |--------------------------------------------------------------------------
    |
    | A public demo needs a way in, so `php artisan gentelella:demo` creates
    | this account and the sign-in screen fills it in.
    |
    | It is read ONLY while `demo` above is true — a production app cannot
    | print credentials on its login page by forgetting a setting. Set to null
    | to run the demo without an account.
    |
    | The password is long and has never appeared in a breach corpus, so
    | browsers do not flag it on sign-in. Note that a password field served
    | over plain HTTP is marked "Not secure" whatever the password is: a demo
    | with an account needs TLS.
    |
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
    |
    | Written by `php artisan gentelella:install` and referenced by the layout.
    |
    */

    'vite' => [
        'resources/js/gentelella.js',
    ],

];
