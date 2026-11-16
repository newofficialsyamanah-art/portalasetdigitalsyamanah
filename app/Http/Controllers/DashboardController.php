<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\Division;
use App\Models\AppConfig;
use App\Models\User;
use App\Models\GuideBook;
use App\Models\AppSystem;
use App\Services\GuideBookService;
use App\Services\RbacService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-initialize Spatie roles and permissions if not yet migrated
        if (!Schema::hasTable('roles')) {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        }

        // Auto-ensure all granular RBAC permissions and roles exist
        RbacService::ensurePermissionsExist();

        // Auto-initialize Guide Books & Master Systems table and built-in guides
        GuideBook::ensureTableExists();
        AppSystem::ensureTableExists();

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role;
        $isSuperadmin = $userRole === 'superadmin';
        $isManager = $userRole === 'manager';
        $isGA = $userRole === 'general_affair';

        $activeTab = $request->query('tab');
        if (!$activeTab) {
            if ($isSuperadmin || $isManager || ($user && $user->can('manage spreadsheet'))) {
                $activeTab = 'spreadsheet';
            } else {
                $activeTab = 'inventory';
            }
        }
        
        // General Affair restricted from confidential or admin tabs
        if ($isGA && in_array($activeTab, ['spreadsheet', 'mikrotik', 'audit', 'superadmin'])) {
            $activeTab = 'inventory';
        }

        // Restrict guides tab if user has no view or manage guidebook permissions
        if ($activeTab === 'guides' && !$isSuperadmin && !$user->can('view guidebooks') && !$user->can('manage guidebooks')) {
            $activeTab = 'inventory';
        }

        $submissions = Submission::orderBy('created_at', 'desc')->get();

        // Auto-seed user hardware & software dataset if not yet loaded
        if ($submissions->where('type', 'pc')->count() < 40) {
            \App\Services\InventoryImportService::importAll();
            $submissions = Submission::orderBy('created_at', 'desc')->get();
        }

        $mikrotikAccounts = MikrotikAccount::orderBy('created_at', 'desc')->get();
        $divisions = Division::orderBy('name')->pluck('name')->toArray();
        $users = User::with('roles', 'permissions')->get();
        $guideBooks = GuideBook::orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->get();
        $guideSystems = GuideBookService::getSystems();
        $guideCategories = GuideBookService::getCategories();
        $guideRoles = GuideBookService::getTargetRoles();
        $systemFeaturesMap = GuideBookService::getSystemFeaturesMap();
        $appSystems = AppSystem::orderBy('order_num')->orderBy('name')->get();

        $roles = class_exists(Role::class) && Schema::hasTable('roles')
            ? Role::with('permissions')->get() 
            : collect();
        $allPermissions = class_exists(Permission::class) && Schema::hasTable('permissions')
            ? Permission::orderBy('name')->get() 
            : collect();
        $permissionMeta = RbacService::getPermissionsDefinition();

        // Calculate counts
        $deviceCount = $submissions->filter(function ($s) {
            $j = strtolower($s->jenis_perangkat ?? '');
            return str_contains($j, 'laptop') || str_contains($j, 'pc') || str_contains($j, 'desktop') || $s->type === 'pc';
        })->count();

        $phoneCount = $submissions->filter(function ($s) {
            $j = strtolower($s->jenis_perangkat ?? '');
            return str_contains($j, 'smartphone') || str_contains($j, 'hp') || str_contains($j, 'android') || $s->type === 'akun';
        })->count();

        $totalSubmissions = $submissions->count();

        $config = [
            'gasUrl' => AppConfig::get('gasUrl', ''),
            'spreadsheetId' => AppConfig::get('spreadsheetId', ''),
            'driveFolderId' => AppConfig::get('driveFolderId', ''),
            'superadminPin' => AppConfig::get('superadminPin', '1234'),
            'erpnextUrl' => AppConfig::get('erpnextUrl', 'https://syamanah4.sopwer.id'),
            'erpnextApiKey' => AppConfig::get('erpnextApiKey', '43895e90883a899'),
            'erpnextApiSecret' => AppConfig::get('erpnextApiSecret', '3d8c285da00a784'),
        ];

        return view('dashboard', compact(
            'user',
            'activeTab',
            'submissions',
            'mikrotikAccounts',
            'divisions',
            'users',
            'roles',
            'allPermissions',
            'deviceCount',
            'phoneCount',
            'totalSubmissions',
            'config',
            'guideBooks',
            'guideSystems',
            'guideCategories',
            'guideRoles',
            'systemFeaturesMap',
            'appSystems',
            'permissionMeta'
        ));
    }
}
