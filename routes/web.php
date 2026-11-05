<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\SyncController;

// Setup Spatie Roles & Permissions
Route::get('/setup-spatie', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return redirect()->route('dashboard')->with('success', 'Spatie Laravel-Permission berhasil dipasang & seluruh role/permission aktif!');
});

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/auth/switch-role', [AuthController::class, 'switchRole'])->name('auth.switch-role');

// Submissions (PC & HP Inventories)
Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
Route::put('/submissions/{id}', [SubmissionController::class, 'update'])->name('submissions.update');
Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');

// Mikrotik Hotspot Accounts
Route::post('/mikrotik', [MikrotikController::class, 'store'])->name('mikrotik.store');
Route::put('/mikrotik/{id}', [MikrotikController::class, 'update'])->name('mikrotik.update');
Route::delete('/mikrotik/{id}', [MikrotikController::class, 'destroy'])->name('mikrotik.destroy');

// Divisions & Teams
Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
Route::put('/divisions', [DivisionController::class, 'update'])->name('divisions.update');
Route::delete('/divisions/{name}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

// Superadmin Controls
Route::post('/superadmin/users', [SuperadminController::class, 'storeUser'])->name('superadmin.users.store');
Route::put('/superadmin/users/{id}', [SuperadminController::class, 'updateUser'])->name('superadmin.users.update');
Route::delete('/superadmin/users/{id}', [SuperadminController::class, 'destroyUser'])->name('superadmin.users.destroy');
Route::post('/superadmin/config', [SuperadminController::class, 'updateConfig'])->name('superadmin.config.update');

// Sync & Health Check
Route::get('/api/status', [SyncController::class, 'status']);
Route::post('/sync/spreadsheet', [SyncController::class, 'pullSpreadsheet'])->name('sync.spreadsheet');

// API Compatibility
Route::prefix('api')->group(function () {
    Route::get('/divisions', [DivisionController::class, 'index']);
    Route::post('/divisions', [DivisionController::class, 'store']);
    Route::put('/divisions', [DivisionController::class, 'update']);
    Route::delete('/divisions/{name}', [DivisionController::class, 'destroy']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::put('/submissions/{id}', [SubmissionController::class, 'update']);
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy']);
    Route::post('/mikrotik', [MikrotikController::class, 'store']);
    Route::put('/mikrotik/{id}', [MikrotikController::class, 'update']);
    Route::delete('/mikrotik/{id}', [MikrotikController::class, 'destroy']);
});
