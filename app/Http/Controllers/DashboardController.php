<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\Division;
use App\Models\AppConfig;
use App\Models\User;
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

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $activeTab = $request->query('tab', $user->role === 'general_affair' ? 'inventory' : 'spreadsheet');
        
        // General Affair restricted from certain tabs
        if ($user->role === 'general_affair' && !in_array($activeTab, ['inventory', 'phones', 'pc'])) {
            $activeTab = 'inventory';
        }

        $submissions = Submission::orderBy('created_at', 'desc')->get();
        $mikrotikAccounts = MikrotikAccount::orderBy('created_at', 'desc')->get();
        $divisions = Division::orderBy('name')->pluck('name')->toArray();
        $users = User::with('roles', 'permissions')->get();

        $roles = class_exists(Role::class) && Schema::hasTable('roles')
            ? Role::with('permissions')->get() 
            : collect();
        $allPermissions = class_exists(Permission::class) && Schema::hasTable('permissions')
            ? Permission::orderBy('name')->get() 
            : collect();

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
            'config'
        ));
    }
}
