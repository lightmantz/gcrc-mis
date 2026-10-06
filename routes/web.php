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
use App\Http\Controllers\Admin\EmergencyContactController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\TreatmentPlanController;
use App\Http\Controllers\Admin\TreatmentGoalController;
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
         |
         |  `print` and `export` are declared BEFORE the resource so
         |  that {child} doesn't capture the literal strings "print"
         |  and "export" as if they were model IDs.
         * --------------------------------------------------------------- */
        Route::get('children/{child}/print', [ChildController::class, 'printView'])
            ->name('children.print')
            ->middleware('permission:children.view');

        Route::get('children/export', [ChildController::class, 'export'])
            ->name('children.export')
            ->middleware('permission:children.view');

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
         |  Child ↔ Guardian
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
         |  Child ↔ Emergency Contact
         * --------------------------------------------------------------- */
        Route::prefix('children/{child}/emergency-contacts')->name('children.emergency-contacts.')->group(function () {
            Route::post('/', [EmergencyContactController::class, 'store'])
                ->name('store')
                ->middleware('permission:children.edit');

            Route::put('{contact}', [EmergencyContactController::class, 'update'])
                ->name('update')
                ->middleware('permission:children.edit');

            Route::delete('{contact}', [EmergencyContactController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:children.edit');
        });

        /* ---------------------------------------------------------------
         |  Child ↔ Referral
         * --------------------------------------------------------------- */
        Route::prefix('children/{child}/referrals')->name('children.referrals.')->group(function () {
            Route::post('/', [ReferralController::class, 'store'])
                ->name('store')
                ->middleware('permission:children.edit');

            Route::put('{referral}', [ReferralController::class, 'update'])
                ->name('update')
                ->middleware('permission:children.edit');

            Route::delete('{referral}', [ReferralController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:children.edit');
        });

        /* ---------------------------------------------------------------
         |  Child ↔ Document
         * --------------------------------------------------------------- */
        Route::prefix('children/{child}/documents')->name('children.documents.')->group(function () {
            Route::post('/', [DocumentController::class, 'store'])
                ->name('store')
                ->middleware('permission:children.edit');
        });

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('{document}/download', [DocumentController::class, 'download'])
                ->name('download')
                ->middleware('permission:children.view');

            Route::delete('{document}', [DocumentController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:children.edit');
        });

        /* ---------------------------------------------------------------
         |  Referrals (standalone list)
         * --------------------------------------------------------------- */
        Route::get('referrals', [ReferralController::class, 'index'])
            ->name('referrals.index')
            ->middleware('permission:referrals.view');

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

        /* ---------------------------------------------------------------
         |  Assessments
         * --------------------------------------------------------------- */
        Route::resource('assessments', AssessmentController::class)
            ->except(['destroy'])
            ->middleware([
                'index'   => 'permission:assessments.view',
                'show'    => 'permission:assessments.view',
                'create'  => 'permission:assessments.create',
                'store'   => 'permission:assessments.create',
                'edit'    => 'permission:assessments.edit',
                'update'  => 'permission:assessments.edit',
            ]);

        Route::post('assessments/{assessment}/finalize', [AssessmentController::class, 'finalize'])
            ->name('assessments.finalize')
            ->middleware('permission:assessments.approve');

        Route::delete('assessments/{assessment}', [AssessmentController::class, 'destroy'])
            ->name('assessments.destroy')
            ->middleware('permission:assessments.delete');

        Route::post('assessments/{id}/restore', [AssessmentController::class, 'restore'])
            ->name('assessments.restore')
            ->middleware('permission:assessments.delete');

        /* ---------------------------------------------------------------
         |  Diagnoses & Conditions
         * --------------------------------------------------------------- */
        Route::resource('diagnoses', DiagnosisController::class)
            ->except(['destroy'])
            ->middleware([
                'index'   => 'permission:diagnoses.view',
                'show'    => 'permission:diagnoses.view',
                'create'  => 'permission:diagnoses.create',
                'store'   => 'permission:diagnoses.create',
                'edit'    => 'permission:diagnoses.edit',
                'update'  => 'permission:diagnoses.edit',
            ]);

        Route::post('diagnoses/{diagnosis}/close', [DiagnosisController::class, 'close'])
            ->name('diagnoses.close')
            ->middleware('permission:diagnoses.approve');

        Route::delete('diagnoses/{diagnosis}', [DiagnosisController::class, 'destroy'])
            ->name('diagnoses.destroy')
            ->middleware('permission:diagnoses.delete');

        Route::post('diagnoses/{id}/restore', [DiagnosisController::class, 'restore'])
            ->name('diagnoses.restore')
            ->middleware('permission:diagnoses.delete');

        /* ---------------------------------------------------------------
         |  Treatment Plans
         |  `parameters(['treatment-plans' => 'treatmentPlan'])` forces the
         |  route parameter name to {treatmentPlan}, matching the controller
         |  method signatures.
         * --------------------------------------------------------------- */
        Route::resource('treatment-plans', TreatmentPlanController::class)
            ->parameters(['treatment-plans' => 'treatmentPlan'])
            ->except(['destroy'])
            ->middleware([
                'index'   => 'permission:treatment_plans.view',
                'show'    => 'permission:treatment_plans.view',
                'create'  => 'permission:treatment_plans.create',
                'store'   => 'permission:treatment_plans.create',
                'edit'    => 'permission:treatment_plans.edit',
                'update'  => 'permission:treatment_plans.edit',
            ]);

        Route::post('treatment-plans/{treatmentPlan}/activate', [TreatmentPlanController::class, 'activate'])
            ->name('treatment-plans.activate')
            ->middleware('permission:treatment_plans.approve');

        Route::post('treatment-plans/{treatmentPlan}/close', [TreatmentPlanController::class, 'close'])
            ->name('treatment-plans.close')
            ->middleware('permission:treatment_plans.approve');

        Route::delete('treatment-plans/{treatmentPlan}', [TreatmentPlanController::class, 'destroy'])
            ->name('treatment-plans.destroy')
            ->middleware('permission:treatment_plans.delete');

        Route::post('treatment-plans/{id}/restore', [TreatmentPlanController::class, 'restore'])
            ->name('treatment-plans.restore')
            ->middleware('permission:treatment_plans.delete');

        /* ---------------------------------------------------------------
         |  Treatment Goals (scoped to a plan)
         * --------------------------------------------------------------- */
        Route::prefix('treatment-plans/{treatmentPlan}/goals')
            ->name('treatment-plans.goals.')
            ->middleware('permission:treatment_plans.edit')
            ->group(function () {
                Route::post('/', [TreatmentGoalController::class, 'store'])->name('store');
                Route::put('{goal}', [TreatmentGoalController::class, 'update'])->name('update');
                Route::delete('{goal}', [TreatmentGoalController::class, 'destroy'])->name('destroy');

                Route::post('{goal}/progress', [TreatmentGoalController::class, 'recordProgress'])
                    ->name('progress.store');
            });
    });
});

require __DIR__.'/auth.php';