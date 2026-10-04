<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\StaffController;
use Illuminate\Support\Facades\Route;

// Redirect the root: guests → login, signed-in users → dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {

        /* ---------------------------------------------------------------
         |  User Accounts
         * --------------------------------------------------------------- */
        Route::resource('users', UserController::class)
            ->middleware([
                'index'   => 'permission:users.view',
                'show'    => 'permission:users.view',
                'create'  => 'permission:users.create',
                'store'   => 'permission:users.create',
                'edit'    => 'permission:users.edit',
                'update'  => 'permission:users.edit',
                'destroy' => 'permission:users.delete',
            ]);

        Route::post('users/{id}/restore', [UserController::class, 'restore'])
            ->name('users.restore')
            ->middleware('permission:users.delete');

        Route::post('users/{user}/activate', [UserController::class, 'activate'])
            ->name('users.activate')
            ->middleware('permission:users.activate');

        Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate')
            ->middleware('permission:users.deactivate');

        /* ---------------------------------------------------------------
         |  Roles & Permissions
         |  destroy is registered separately so we can apply its own
         |  middleware without Laravel's resource defaults interfering.
         * --------------------------------------------------------------- */
        Route::resource('roles', RoleController::class)
            ->except(['destroy'])
            ->middleware([
                'index'   => 'permission:roles.view',
                'show'    => 'permission:roles.view',
                'create'  => 'permission:roles.create',
                'store'   => 'permission:roles.create',
                'edit'    => 'permission:roles.edit',
                'update'  => 'permission:roles.edit',
            ]);

        Route::delete('roles/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy')
            ->middleware('permission:roles.delete');

        /* ---------------------------------------------------------------
         |  Audit Trail
         |  `export` MUST be declared before `{audit}` or Laravel will try
         |  to resolve "export" as an audit ID and fail with 404.
         * --------------------------------------------------------------- */
        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/', [AuditController::class, 'index'])
                ->name('index')
                ->middleware('permission:audit.view');

            Route::get('export', [AuditController::class, 'export'])
                ->name('export')
                ->middleware('permission:audit.view');

            Route::get('{audit}', [AuditController::class, 'show'])
                ->name('show')
                ->middleware('permission:audit.view');
        });

        /* ---------------------------------------------------------------
         |  Login History
         |  `purge` MUST be declared before `{loginHistory}` or Laravel
         |  will try to resolve "purge" as a login-history ID.
         * --------------------------------------------------------------- */
        Route::prefix('login-history')->name('login-history.')->group(function () {
            Route::get('/', [LoginHistoryController::class, 'index'])
                ->name('index')
                ->middleware('permission:login_history.view');

            Route::post('purge', [LoginHistoryController::class, 'purge'])
                ->name('purge')
                ->middleware('permission:login_history.delete');

            Route::get('{loginHistory}', [LoginHistoryController::class, 'show'])
                ->name('show')
                ->middleware('permission:login_history.view');
        });

        /* ---------------------------------------------------------------
         |  Staff
         |  `parameters(['staff' => 'staff'])` prevents Laravel from
         |  generating `{staffs}` as the resource parameter name, which
         |  would break route-model binding on show/edit/update/destroy.
         * --------------------------------------------------------------- */
        Route::resource('staff', StaffController::class)
            ->parameters(['staff' => 'staff'])
            ->middleware([
                'index'   => 'permission:staff.view',
                'show'    => 'permission:staff.view',
                'create'  => 'permission:staff.create',
                'store'   => 'permission:staff.create',
                'edit'    => 'permission:staff.edit',
                'update'  => 'permission:staff.edit',
                'destroy' => 'permission:staff.delete',
            ]);

        Route::post('staff/{id}/restore', [StaffController::class, 'restore'])
            ->name('staff.restore')
            ->middleware('permission:staff.delete');

        Route::post('staff/{staff}/activate', [StaffController::class, 'activate'])
            ->name('staff.activate')
            ->middleware('permission:staff.edit');

        Route::post('staff/{staff}/suspend', [StaffController::class, 'suspend'])
            ->name('staff.suspend')
            ->middleware('permission:staff.edit');
    });
});

require __DIR__.'/auth.php';