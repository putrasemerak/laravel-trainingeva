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
    // Dashboard Dispatcher
    Route::get('/dashboard', function() {
        if (auth()->user()->hasPermission('dashboard')) {
            return app(App\Http\Controllers\EvaluationController::class)->index(request());
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/my-dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('user.dashboard');
    
    // Shared: Language Switching (Handled globally, but keep here for safety)
    Route::get('/lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');
    Route::get('/api/topics', [\App\Http\Controllers\EmployeeController::class, 'getTopics'])->name('api.topics.index');
    Route::get('/api/employees/{empno}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('api.employees.show');

    // Role: Regular User + (Creation only)
    Route::middleware(['permission:evaluation_request'])->group(function () {
        Route::get('/user/evaluate/new', [EvaluationController::class, 'createByUser'])->name('user.evaluations.create');
        Route::post('/user/evaluate', [EvaluationController::class, 'storeFromUser'])->name('user.evaluations.store');
    });

    // Role: Evaluator & Admin & SuperUser (Listings and individual evaluation)
    Route::middleware(['permission:evaluation_list'])->group(function () {
        Route::get('/evaluations', [EvaluationController::class, 'evaluatorIndex'])->name('evaluations');
        Route::get('/evaluations/{id}/evaluate', [EvaluationController::class, 'evaluate'])->name('evaluations.evaluate');
        Route::put('/evaluations/{id}', [EvaluationController::class, 'update'])->name('evaluations.update');
        Route::get('/evaluations/{id}/print', [EvaluationController::class, 'print'])->name('evaluations.print');
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
