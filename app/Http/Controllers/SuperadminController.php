<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SuperadminController extends Controller
{
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'role' => 'required|in:superadmin,manager,general_affair',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->input('email', $request->username . '@syamanah.com'),
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);
        $user->syncRoles([$request->role]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna ' . $user->name . ' berhasil ditambahkan!',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Pengguna ' . $user->name . ' berhasil ditambahkan!');
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|in:superadmin,manager,general_affair',
        ]);

        $user->name = $request->name;
        $user->role = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $user->syncRoles([$request->role]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diperbarui!',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui!');
    }

    public function destroyUser(Request $request, int $id)
    {
        if (Auth::id() === $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang aktif.');
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

        return back()->with('success', 'Pengguna ' . $name . ' berhasil dihapus!');
    }

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

