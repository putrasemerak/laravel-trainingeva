<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvaluationController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    // Dashboard Dispatcher (Seamless Traffic Controller)
    Route::get('/dashboard', function() {
        $user = auth()->user();
        
        // 1. HR Admin / Superadmin -> Lite Summary Dashboard
        if ($user->isSuperUser() || $user->hasLegacyAccess('HG10', ['01','02','03'])) {
            return app(App\Http\Controllers\EvaluationController::class)->index(request());
        }
        
        // 2. Evaluator (Superior) -> Task List
        $hasTasks = \App\Models\Evaluation::where('eemp', $user->EmpNo)->where('status', '!=', 'Evaluated')->exists();
        if ($hasTasks) {
            return redirect()->route('evaluations');
        }
        
        // 3. Regular User -> Personal History
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/my-dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/api/evaluations/{id}', [EvaluationController::class, 'showApi'])->name('api.evaluations.show');

    // ... Shared ...

    // Training Attendance (Legacy HG10)
    Route::middleware(['legacy_access:HG10,02,03'])->group(function () {
        Route::get('/training/attendance', [\App\Http\Controllers\TrainingAttendanceController::class, 'index'])->name('training.attendance');
        Route::get('/training/notifications', [\App\Http\Controllers\TrainingAttendanceController::class, 'notifications'])->name('training.notifications');
        Route::get('/training/evaluation-form', [EvaluationController::class, 'createMaster'])->name('training.master_form');
        Route::get('/training/list', [EvaluationController::class, 'listAll'])->name('evaluations.list');
        Route::get('/training/search', [\App\Http\Controllers\TrainingAttendanceController::class, 'searchTraining']);
        Route::get('/employee/search', [\App\Http\Controllers\TrainingAttendanceController::class, 'searchEmployee']);
        Route::get('/employee/details/{empno}', [\App\Http\Controllers\TrainingAttendanceController::class, 'getEmployeeDetails']);
        Route::post('/training/attendance/store', [\App\Http\Controllers\TrainingAttendanceController::class, 'store']);
    });

    // Role: Regular User + (Creation only)
    // - Removed redundant manual request routes -

    // Role: Evaluator & Admin & SuperUser (Listings and individual evaluation)
    Route::middleware(['permission:evaluation_list'])->group(function () {
        Route::get('/evaluations', [EvaluationController::class, 'evaluatorIndex'])->name('evaluations');
        Route::get('/evaluations/{id}/evaluate', [EvaluationController::class, 'evaluate'])->name('evaluations.evaluate');
        Route::put('/evaluations/{id}', [EvaluationController::class, 'update'])->name('evaluations.update');
        Route::get('/evaluations/{id}/print', [EvaluationController::class, 'print'])->name('evaluations.print');
    });

    // Admin User Management
    Route::middleware(['permission:system_settings'])->group(function () {
        Route::get('/admin/users/register', [\App\Http\Controllers\UserManagementController::class, 'create'])->name('admin.users.register');
        Route::get('/admin/users/lookup/{empno}', [\App\Http\Controllers\UserManagementController::class, 'lookup'])->name('admin.users.lookup');
        Route::post('/admin/users/register', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('admin.users.store');
    });

    // Role: Admin & SuperUser (Management Dashboard)
    Route::middleware(['permission:dashboard'])->group(function () {
        Route::post('/evaluations/export', [EvaluationController::class, 'export'])->name('evaluations.export');
        Route::get('/evaluations/{id}/show', [EvaluationController::class, 'show'])->name('evaluations.show');
    });

    // Role: Admin & SuperUser (Creation)
    Route::middleware(['permission:evaluation_create'])->group(function () {
        Route::get('/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    });

    // Role: SuperUser Only (Audit Trail)
    Route::middleware(['permission:audit_trail'])->group(function () {
        Route::get('/admin/audit', [\App\Http\Controllers\AuditController::class, 'index'])->name('admin.audit');
        Route::post('/admin/audit/export', [\App\Http\Controllers\AuditController::class, 'export'])->name('admin.audit.export');
    });

    // Role: SuperUser Only (System Admin)
    Route::middleware(['permission:system_settings'])->group(function () {
        Route::get('/admin/settings', [\App\Http\Controllers\SystemController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings/roles', [\App\Http\Controllers\SystemController::class, 'updateRole'])->name('admin.roles.update');
        Route::post('/admin/settings/global', [\App\Http\Controllers\SystemController::class, 'updateSetting'])->name('admin.settings.update');
        Route::post('/admin/settings/permissions', [\App\Http\Controllers\SystemController::class, 'updatePermission'])->name('admin.permissions.update');
    });
});
