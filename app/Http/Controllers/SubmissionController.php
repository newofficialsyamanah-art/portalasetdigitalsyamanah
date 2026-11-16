<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $type = $request->input('type', 'pc');
        $id = $type . '_' . uniqid();
        $payload = $request->input('data', $request->except(['_token', 'type']));

        // Handle file attachments (Bukti Display, Bukti Sound, Bukti Input, Foto Fisik)
        $attachmentFields = [
            'file_display' => 'displayLink',
            'file_sound' => 'soundLink',
            'file_input' => 'inputLink',
            'foto' => 'foto',
        ];

        foreach ($attachmentFields as $inputKey => $payloadKey) {
            if ($request->hasFile($inputKey)) {
                $file = $request->file($inputKey);
                if ($file->isValid()) {
                    $ext = $file->getClientOriginalExtension() ?: 'jpg';
                    $filename = time() . '_' . $inputKey . '_' . uniqid() . '.' . $ext;
                    $destinationPath = public_path('uploads/assets');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $payload[$payloadKey] = '/uploads/assets/' . $filename;
                    $payload[$payloadKey . '_nama'] = $file->getClientOriginalName();
                }
            }
        }

        // Handle dynamic accounts array from HP form
        if ($request->has('accounts')) {
            $accounts = $request->input('accounts');
            if (is_array($accounts)) {
                $cleanAccounts = [];
                foreach ($accounts as $acc) {
                    if (!empty($acc['username']) || !empty($acc['identifier'])) {
                        $cleanAccounts[] = [
                            'platform' => $acc['platform'] ?? 'Lainnya',
                            'username' => $acc['username'] ?? ($acc['identifier'] ?? ''),
                            'password' => $acc['password'] ?? '-'
                        ];
                    }
                }
                $payload['accounts'] = $cleanAccounts;

                // Sync first matching platform to legacy fields
                foreach ($cleanAccounts as $acc) {
                    $p = strtolower($acc['platform']);
                    if (str_contains($p, 'email') && empty($payload['email1'])) {
                        $payload['email1'] = $acc['username'];
                        $payload['passEmail1'] = $acc['password'];
                    } elseif ((str_contains($p, 'ig') || str_contains($p, 'instagram')) && empty($payload['ig1'])) {
                        $payload['ig1'] = $acc['username'];
                        $payload['passIg1'] = $acc['password'];
                    } elseif ((str_contains($p, 'fb') || str_contains($p, 'facebook')) && empty($payload['fb1'])) {
                        $payload['fb1'] = $acc['username'];
                        $payload['passFb1'] = $acc['password'];
                    }
                }
            }
        }

        $nama = $payload['nama'] ?? $payload['namaLengkap'] ?? ($payload['computerName'] ?? 'Aset Syamanah');
        $departemen = $payload['departemen'] ?? $payload['kategori'] ?? 'Umum';
        $jabatan = $payload['jabatan'] ?? '-';
        $jenis = $payload['jenisPerangkat'] ?? $payload['jenisHp'] ?? ($type === 'pc' ? 'Laptop' : 'Smartphone');

        $submission = Submission::create([
            'id' => $id,
            'type' => $type,
            'timestamp' => now()->format('m/d/Y H:i:s'),
            'nama' => $nama,
            'departemen' => $departemen,
            'jabatan' => $jabatan,
            'jenis_perangkat' => $jenis,
            'data' => $payload,
            'synced_to_google' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data inventaris berhasil disimpan!',
                'record' => $submission
            ]);
        }

        return redirect()->route('dashboard', ['tab' => $type === 'pc' ? 'inventory' : 'phones'])
            ->with('success', 'Data inventaris ' . ($type === 'pc' ? 'PC/Laptop' : 'HP & Akun') . ' berhasil disimpan!');
    }

    public function update(Request $request, string $id)
    {
        $submission = Submission::findOrFail($id);
        $payload = $request->input('data', $request->except(['_token', '_method']));

        // Handle file attachments (Bukti Display, Bukti Sound, Bukti Input, Foto Fisik)
        $attachmentFields = [
            'file_display' => 'displayLink',
            'file_sound' => 'soundLink',
            'file_input' => 'inputLink',
            'foto' => 'foto',
        ];

        foreach ($attachmentFields as $inputKey => $payloadKey) {
            if ($request->hasFile($inputKey)) {
                $file = $request->file($inputKey);
                if ($file->isValid()) {
                    $ext = $file->getClientOriginalExtension() ?: 'jpg';
                    $filename = time() . '_' . $inputKey . '_' . uniqid() . '.' . $ext;
                    $destinationPath = public_path('uploads/assets');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $payload[$payloadKey] = '/uploads/assets/' . $filename;
                    $payload[$payloadKey . '_nama'] = $file->getClientOriginalName();
                }
            }
        }

        // Handle dynamic accounts array from HP form
        if ($request->has('accounts')) {
            $accounts = $request->input('accounts');
            if (is_array($accounts)) {
                $cleanAccounts = [];
                foreach ($accounts as $acc) {
                    if (!empty($acc['username']) || !empty($acc['identifier'])) {
                        $cleanAccounts[] = [
                            'platform' => $acc['platform'] ?? 'Lainnya',
                            'username' => $acc['username'] ?? ($acc['identifier'] ?? ''),
                            'password' => $acc['password'] ?? '-'
                        ];
                    }
                }
                $payload['accounts'] = $cleanAccounts;
            }
        }

        // Merge updated fields with existing data
        $mergedData = array_merge($submission->data ?? [], $payload);

        $nama = $mergedData['nama'] ?? $mergedData['namaLengkap'] ?? ($mergedData['computerName'] ?? $submission->nama);
        $departemen = $mergedData['departemen'] ?? $mergedData['kategori'] ?? $submission->departemen;
        $jabatan = $mergedData['jabatan'] ?? $submission->jabatan;
        $jenis = $mergedData['jenisPerangkat'] ?? $mergedData['jenisHp'] ?? $submission->jenis_perangkat;

        $submission->update([
            'nama' => $nama,
            'departemen' => $departemen,
            'jabatan' => $jabatan,
            'jenis_perangkat' => $jenis,
            'data' => $mergedData,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data aset berhasil diperbarui!',
                'record' => $submission
            ]);
        }

        return back()->with('success', 'Data aset ' . $submission->nama . ' berhasil diperbarui!');
    }

    public function destroy(Request $request, string $id)
    {
        $user = Auth::user();
        if ($user && !$user->can('delete records') && $user->role !== 'superadmin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki hak akses untuk menghapus rekaman data ini.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki hak akses untuk menghapus rekaman data ini.');
        }

        $submission = Submission::findOrFail($id);
        $name = $submission->nama;
        $submission->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data aset ' . $name . ' berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Data aset ' . $name . ' berhasil dihapus!');
    }
}

