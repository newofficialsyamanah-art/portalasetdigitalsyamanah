<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ERPNextController;
use App\Http\Controllers\GuideBookController;
use App\Http\Controllers\AppSystemController;

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
Route::get('/inventory/seed-csv', function () {
    $count = \App\Services\InventoryImportService::importAll();
    return redirect()->route('dashboard', ['tab' => 'inventory'])->with('success', "Berhasil menyinkronkan {$count} data spesifikasi PC/Laptop & jaringan!");
})->name('inventory.seed-csv');
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

// Superadmin Controls (User & RBAC CRUD)
Route::post('/superadmin/users', [SuperadminController::class, 'storeUser'])->name('superadmin.users.store');
Route::put('/superadmin/users/{id}', [SuperadminController::class, 'updateUser'])->name('superadmin.users.update');
Route::delete('/superadmin/users/{id}', [SuperadminController::class, 'destroyUser'])->name('superadmin.users.destroy');
Route::post('/superadmin/roles', [SuperadminController::class, 'storeRole'])->name('superadmin.roles.store');
Route::put('/superadmin/roles/{role}/permissions', [SuperadminController::class, 'updateRolePermissions'])->name('superadmin.roles.permissions');
Route::delete('/superadmin/roles/{role}', [SuperadminController::class, 'destroyRole'])->name('superadmin.roles.destroy');
Route::post('/superadmin/config', [SuperadminController::class, 'updateConfig'])->name('superadmin.config.update');

// Superadmin Master Systems CRUD (Sistem Terdaftar)
Route::get('/superadmin/systems', [AppSystemController::class, 'index'])->name('superadmin.systems.index');
Route::post('/superadmin/systems', [AppSystemController::class, 'store'])->name('superadmin.systems.store');
Route::match(['put', 'post'], '/superadmin/systems/{id}', [AppSystemController::class, 'update'])->name('superadmin.systems.update');
Route::match(['delete', 'post'], '/superadmin/systems/{id}', [AppSystemController::class, 'destroy'])->name('superadmin.systems.destroy');
Route::get('/superadmin/systems/{id}/delete', [AppSystemController::class, 'destroy'])->name('superadmin.systems.destroy.get');
Route::match(['post', 'get'], '/superadmin/systems/action/clear-all', [AppSystemController::class, 'clearAll'])->name('superadmin.systems.clearAll');

// Excel Export & Import
Route::get('/excel/export', [ExcelController::class, 'export'])->name('excel.export');
Route::post('/excel/import', [ExcelController::class, 'import'])->name('excel.import');
Route::get('/excel/template', [ExcelController::class, 'template'])->name('excel.template');

// ERPNext Integration
Route::post('/erpnext/config', [ERPNextController::class, 'saveConfig'])->name('erpnext.config');
Route::post('/erpnext/test', [ERPNextController::class, 'testConnection'])->name('erpnext.test');
Route::get('/erpnext/assets', [ERPNextController::class, 'fetchAssets'])->name('erpnext.assets');
Route::post('/erpnext/push', [ERPNextController::class, 'pushAsset'])->name('erpnext.push');
Route::get('/erpnext/masters', [ERPNextController::class, 'getMasters'])->name('erpnext.masters');

// Asset Guide Books & System Manuals (Using /guide-books to prevent public folder collision)
Route::get('/guide-books/action/reset-defaults', [GuideBookController::class, 'resetDefaults'])->name('guidebooks.reset');
Route::get('/guide-books', function () {
    return redirect('/?tab=guides');
})->name('guidebooks.index');
Route::post('/guide-books', [GuideBookController::class, 'store'])->name('guidebooks.store');
Route::match(['post', 'put'], '/guide-books/{id}', [GuideBookController::class, 'update'])->name('guidebooks.update');
Route::delete('/guide-books/{id}', [GuideBookController::class, 'destroy'])->name('guidebooks.destroy');
Route::get('/guide-books/{id}', [GuideBookController::class, 'show'])->name('guidebooks.show');
Route::get('/guide-books/{id}/pdf', [GuideBookController::class, 'streamPdf'])->name('guidebooks.pdf');

// Backward compatibility aliases
Route::get('/guidebooks/action/reset-defaults', [GuideBookController::class, 'resetDefaults']);
Route::get('/guidebooks', function () {
    return redirect('/?tab=guides');
});
Route::post('/guidebooks', [GuideBookController::class, 'store']);
Route::match(['post', 'put'], '/guidebooks/{id}', [GuideBookController::class, 'update']);
Route::delete('/guidebooks/{id}', [GuideBookController::class, 'destroy']);
Route::get('/guidebooks/{id}', [GuideBookController::class, 'show']);
Route::get('/guidebooks/{id}/pdf', [GuideBookController::class, 'streamPdf']);

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
    Route::get('/excel/export', [ExcelController::class, 'export']);
    Route::post('/excel/import', [ExcelController::class, 'import']);
    Route::post('/erpnext/test', [ERPNextController::class, 'testConnection']);
    Route::get('/erpnext/assets', [ERPNextController::class, 'fetchAssets']);
    Route::post('/erpnext/push', [ERPNextController::class, 'pushAsset']);
    Route::get('/erpnext/masters', [ERPNextController::class, 'getMasters']);
    Route::get('/guidebooks', function () {
        return response()->json(\App\Models\GuideBook::orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->get());
    });
    Route::post('/guidebooks', [GuideBookController::class, 'store']);
    Route::match(['post', 'put'], '/guidebooks/{id}', [GuideBookController::class, 'update']);
    Route::delete('/guidebooks/{id}', [GuideBookController::class, 'destroy']);
    Route::get('/guidebooks/{id}', [GuideBookController::class, 'show']);
    Route::get('/guidebooks/{id}/pdf', [GuideBookController::class, 'streamPdf']);
    Route::get('/guide-books', function () {
        return response()->json(\App\Models\GuideBook::orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->get());
    });
    Route::post('/guide-books', [GuideBookController::class, 'store']);
    Route::match(['post', 'put'], '/guide-books/{id}', [GuideBookController::class, 'update']);
    Route::delete('/guide-books/{id}', [GuideBookController::class, 'destroy']);
    Route::get('/guide-books/{id}', [GuideBookController::class, 'show']);
    Route::get('/guide-books/{id}/pdf', [GuideBookController::class, 'streamPdf']);
});
