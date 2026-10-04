<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ChildController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\ChildGuardianController;
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

        /* ---------------------------------------------------------------
         |  Children
         * --------------------------------------------------------------- */
        Route::resource('children', ChildController::class)
            ->middleware([
                'index'   => 'permission:children.view',
                'show'    => 'permission:children.view',
                'create'  => 'permission:children.create',
                'store'   => 'permission:children.create',
                'edit'    => 'permission:children.edit',
                'update'  => 'permission:children.edit',
                'destroy' => 'permission:children.delete',
            ]);

        Route::post('children/{id}/restore', [ChildController::class, 'restore'])
            ->name('children.restore')
            ->middleware('permission:children.delete');

        /* ---------------------------------------------------------------
         |  Child ↔ Guardian (attach / update / detach)
         |  Registered before the guardians resource so `children/{child}/
         |  guardians/...` doesn't collide with `children/{child}`.
         * --------------------------------------------------------------- */
        Route::prefix('children/{child}/guardians')->name('children.guardians.')->group(function () {
            Route::post('/', [ChildGuardianController::class, 'store'])
                ->name('store')
                ->middleware('permission:children.edit');

            Route::put('{guardian}', [ChildGuardianController::class, 'update'])
                ->name('update')
                ->middleware('permission:children.edit');

            Route::delete('{guardian}', [ChildGuardianController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:children.edit');
        });

        /* ---------------------------------------------------------------
         |  Guardians (standalone CRUD)
         * --------------------------------------------------------------- */
        Route::resource('guardians', GuardianController::class)
            ->except(['destroy'])
            ->middleware([
                'index'   => 'permission:guardians.view',
                'show'    => 'permission:guardians.view',
                'create'  => 'permission:guardians.create',
                'store'   => 'permission:guardians.create',
                'edit'    => 'permission:guardians.edit',
                'update'  => 'permission:guardians.edit',
            ]);

        Route::delete('guardians/{guardian}', [GuardianController::class, 'destroy'])
            ->name('guardians.destroy')
            ->middleware('permission:guardians.delete');

        Route::post('guardians/{id}/restore', [GuardianController::class, 'restore'])
            ->name('guardians.restore')
            ->middleware('permission:guardians.delete');
    });
});

require __DIR__.'/auth.php';