<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

// ------------------------------------------------------------------
// Public
// ------------------------------------------------------------------
Route::get('/', function () {
    $totalReports    = \App\Models\Report::count();
    $resolvedReports = \App\Models\Report::where('status', 'Resolved')->count();
    return view('welcome', compact('totalReports', 'resolvedReports'));
})->name('home');

// Public office listing
Route::get('/offices',        [OfficeController::class, 'index'])->name('offices.index');
Route::get('/offices/{slug}', [OfficeController::class, 'show'])->name('offices.show');

// ------------------------------------------------------------------
// Auth routes (guests only)
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    // Forgot / reset password
    Route::get('/forgot-password',        [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Report submission (guest + auth)
Route::get('/report',  [ReportController::class, 'create'])->name('report.create');
Route::post('/report', [ReportController::class, 'store'])->name('report.store');

// ------------------------------------------------------------------
// Authenticated citizen (role=user only — office_staff blocked)
// ------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',                           [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report/{report}',           [DashboardController::class, 'show'])->name('dashboard.report');
    Route::post('/dashboard/report/{report}/feedback', [DashboardController::class, 'feedback'])->name('dashboard.feedback');
    Route::delete('/dashboard/report/{report}',        [DashboardController::class, 'destroy'])->name('dashboard.report.destroy');
});

// ------------------------------------------------------------------
// Office staff panel (office_staff + super_admin)
// ------------------------------------------------------------------
Route::middleware(['auth', 'office'])->group(function () {
    Route::get('/office/panel',                    [OfficeController::class, 'panel'])->name('office.panel');
    Route::patch('/office/report/{report}/status', [OfficeController::class, 'updateStatus'])->name('office.updateStatus');
});

// ------------------------------------------------------------------
// Super admin
// ------------------------------------------------------------------
Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Office CRUD
        Route::get('/offices',               [AdminController::class, 'officesIndex'])->name('offices.index');
        Route::get('/offices/create',        [AdminController::class, 'officesCreate'])->name('offices.create');
        Route::post('/offices',              [AdminController::class, 'officesStore'])->name('offices.store');
        Route::get('/offices/{office}/edit', [AdminController::class, 'officesEdit'])->name('offices.edit');
        Route::patch('/offices/{office}',    [AdminController::class, 'officesUpdate'])->name('offices.update');
        Route::delete('/offices/{office}',   [AdminController::class, 'officesDestroy'])->name('offices.destroy');

        // Staff management
        Route::get('/staff',                        [AdminController::class, 'staff'])->name('staff.index');
        Route::post('/staff',                       [AdminController::class, 'createStaff'])->name('staff.store');
        Route::patch('/staff/{user}/reassign',      [AdminController::class, 'reassignStaff'])->name('staff.reassign');
        Route::patch('/staff/{user}/activate',      [AdminController::class, 'activateStaff'])->name('staff.activate');
        Route::patch('/staff/{user}/ban',           [AdminController::class, 'banStaff'])->name('staff.ban');
        Route::delete('/staff/{user}',              [AdminController::class, 'deleteStaff'])->name('staff.delete');

        // User management
        Route::get('/users',                        [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}/activate',      [AdminController::class, 'activateUser'])->name('users.activate');
        Route::patch('/users/{user}/ban',           [AdminController::class, 'banUser'])->name('users.ban');
        Route::delete('/users/{user}',              [AdminController::class, 'deleteUser'])->name('users.delete');
    });
