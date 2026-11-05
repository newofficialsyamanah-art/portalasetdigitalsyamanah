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
        if ($user && $user->role === 'general_affair') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: General Affair tidak memiliki izin menghapus data.'], 403);
            }
            return back()->with('error', 'Akses ditolak: General Affair tidak memiliki izin menghapus data.');
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

