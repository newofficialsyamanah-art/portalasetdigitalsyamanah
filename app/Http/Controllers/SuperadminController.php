<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperadminController extends Controller
{
    /**
     * Daftarkan Pengguna Baru
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|string',
        ]);

        $roleName = strtolower(trim($request->role));
        if (class_exists(Role::class)) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $email = $request->filled('email') ? $request->email : $request->username . '@syamanah.com';

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $email,
            'role' => $roleName,
            'password' => Hash::make($request->password),
        ]);

        if (class_exists(Role::class)) {
            $user->syncRoles([$roleName]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna ' . $user->name . ' berhasil ditambahkan!',
                'user' => $user->load('roles')
            ]);
        }

        return back()->with('success', 'Pengguna ' . $user->name . ' berhasil didaftarkan dengan peran ' . $roleName . '!');
    }

    /**
     * Perbarui Data Pengguna (Termasuk Ganti Role, Reset Password, Email, Username)
     */
    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string',
            'password' => 'nullable|string|min:4',
        ]);

        $roleName = strtolower(trim($request->role));
        if (class_exists(Role::class)) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->role = $roleName;

        // Reset password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if (class_exists(Role::class)) {
            $user->syncRoles([$roleName]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pengguna ' . $user->name . ' berhasil diperbarui!',
                'user' => $user->load('roles')
            ]);
        }

        return back()->with('success', 'Data akun pengguna ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * Hapus Pengguna
     */
    public function destroyUser(Request $request, int $id)
    {
        if (Auth::id() === $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang aktif digunakan.');
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna ' . $name . ' berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Pengguna ' . $name . ' berhasil dihapus dari sistem!');
    }

    /**
     * Sinkronkan Hak Akses (Permissions) Spatie untuk Suatu Peran (Role)
     */
    public function updateRolePermissions(Request $request, string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        
        // Ambil array izin dari checkbox form
        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        // Refresh cache permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Hak akses RBAC untuk role '{$role->name}' berhasil diperbarui!",
                'permissions' => $role->permissions->pluck('name')
            ]);
        }

        return back()->with('success', "Hak akses RBAC untuk peran '{$role->name}' berhasil diperbarui!");
    }

    /**
     * Tambah Peran (Role) Spatie Baru
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $normalizedName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $request->name)));

        if (Role::where('name', $normalizedName)->exists()) {
            return back()->with('error', "Peran '{$normalizedName}' sudah terdaftar.");
        }

        $role = Role::create([
            'name' => $normalizedName,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Peran baru '{$role->name}' berhasil dibuat!",
                'role' => $role
            ]);
        }

        return back()->with('success', "Peran baru '{$role->name}' berhasil dibuat!");
    }

    /**
     * Hapus Peran (Role) Kustom
     */
    public function destroyRole(Request $request, string $roleName)
    {
        if (in_array($roleName, ['superadmin', 'manager', 'general_affair'])) {
            return back()->with('error', "Peran sistem inti '{$roleName}' tidak boleh dihapus demi integritas keamanan.");
        }

        $role = Role::where('name', $roleName)->firstOrFail();

        // Alihkan user yang saat ini memiliki role ini ke general_affair
        $usersWithRole = User::role($roleName)->get();
        foreach ($usersWithRole as $u) {
            $u->role = 'general_affair';
            $u->save();
            $u->syncRoles(['general_affair']);
        }

        $role->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Peran '{$roleName}' berhasil dihapus!"
            ]);
        }

        return back()->with('success', "Peran '{$roleName}' berhasil dihapus!");
    }

    /**
     * Simpan Pengaturan Integrasi
     */
    public function updateConfig(Request $request)
    {
        $keys = ['gasUrl', 'spreadsheetId', 'driveFolderId', 'superadminPin'];
        foreach ($keys as $k) {
            if ($request->has($k)) {
                AppConfig::set($k, $request->input($k));
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi integrasi Google Apps Script & Spreadsheet berhasil disimpan!'
            ]);
        }

        return back()->with('success', 'Konfigurasi integrasi Google Apps Script & Spreadsheet berhasil disimpan!');
    }
}
