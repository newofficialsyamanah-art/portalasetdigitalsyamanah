<?php

namespace App\Http\Controllers;

use App\Models\MikrotikAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MikrotikController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'namaPemilik' => 'required|string',
            'tim' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $id = 'mkt_' . uniqid();
        $namaPemilik = $request->input('namaPemilik');
        $tim = $request->input('tim');
        $jabatan = $request->input('jabatan', 'Staff');
        $username = $request->input('username');
        $password = $request->input('password');
        $profile = $request->input('profileHotspot', 'sales_' . strtolower(str_replace(' ', '_', $tim)));
        $rateLimit = $request->input('rateLimit', '10M/10M');

        $command = "/ip hotspot user add name=\"{$username}\" password=\"{$password}\" profile=\"{$profile}\" limit-rate=\"{$rateLimit}\" comment=\"{$namaPemilik} - {$jabatan} (Tim {$tim})\"";

        $account = MikrotikAccount::create([
            'id' => $id,
            'nama_pemilik' => $namaPemilik,
            'tim' => $tim,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => $password,
            'profile_hotspot' => $profile,
            'rate_limit' => $rateLimit,
            'command_terminal' => $command,
            'status' => 'aktif',
            'catatan' => $request->input('catatan', '-'),
            'created_at_str' => now()->format('d/m/Y H:i'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun MikroTik ' . $username . ' berhasil dibuat!',
                'account' => $account
            ]);
        }

        return redirect()->route('dashboard', ['tab' => 'mikrotik'])
            ->with('success', 'Akun MikroTik ' . $username . ' berhasil dibuat!');
    }

    public function update(Request $request, string $id)
    {
        $account = MikrotikAccount::findOrFail($id);
        
        $namaPemilik = $request->input('namaPemilik', $account->nama_pemilik);
        $tim = $request->input('tim', $account->tim);
        $jabatan = $request->input('jabatan', $account->jabatan);
        $username = $request->input('username', $account->username);
        $password = $request->input('password', $account->password);
        $profile = $request->input('profileHotspot', $account->profile_hotspot);
        $rateLimit = $request->input('rateLimit', $account->rate_limit);

        $command = "/ip hotspot user add name=\"{$username}\" password=\"{$password}\" profile=\"{$profile}\" limit-rate=\"{$rateLimit}\" comment=\"{$namaPemilik} - {$jabatan} (Tim {$tim})\"";

        $account->update([
            'nama_pemilik' => $namaPemilik,
            'tim' => $tim,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => $password,
            'profile_hotspot' => $profile,
            'rate_limit' => $rateLimit,
            'command_terminal' => $command,
            'status' => $request->input('status', $account->status),
            'catatan' => $request->input('catatan', $account->catatan),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun MikroTik berhasil diperbarui!',
                'account' => $account
            ]);
        }

        return back()->with('success', 'Akun MikroTik ' . $username . ' berhasil diperbarui!');
    }

    public function destroy(Request $request, string $id)
    {
        $account = MikrotikAccount::findOrFail($id);
        $username = $account->username;
        $account->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun MikroTik ' . $username . ' berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Akun MikroTik ' . $username . ' berhasil dihapus!');
    }
}

