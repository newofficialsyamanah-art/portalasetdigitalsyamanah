<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\Division;
use App\Models\AppConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-initialize Spatie roles and permissions if not yet migrated
        if (!\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        }

        $user = Auth::user();
        if (!$user) {
            // Auto login as superadmin for convenience or redirect to login
            $user = User::where('role', 'superadmin')->first();
            if ($user) {
                Auth::login($user);
            } else {
                return redirect()->route('login');
            }
        }

        $activeTab = $request->query('tab', $user->role === 'general_affair' ? 'inventory' : 'spreadsheet');
        
        // General Affair restricted from certain tabs
        if ($user->role === 'general_affair' && !in_array($activeTab, ['inventory', 'phones', 'pc'])) {
            $activeTab = 'inventory';
        }

        $submissions = Submission::orderBy('created_at', 'desc')->get();
        $mikrotikAccounts = MikrotikAccount::orderBy('created_at', 'desc')->get();
        $divisions = Division::orderBy('name')->pluck('name')->toArray();
        $users = User::all();

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
            'deviceCount',
            'phoneCount',
            'totalSubmissions',
            'config'
        ));
    }
}

