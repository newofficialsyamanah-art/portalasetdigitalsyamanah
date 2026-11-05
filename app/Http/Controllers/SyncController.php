<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SyncController extends Controller
{
    public function status()
    {
        $gasUrl = AppConfig::get('gasUrl');
        $spreadsheetId = AppConfig::get('spreadsheetId');

        return response()->json([
            'status' => 'online',
            'hasGasConfigured' => !empty($gasUrl),
            'hasSpreadsheet' => !empty($spreadsheetId),
            'submissionCount' => Submission::count(),
            'mikrotikCount' => MikrotikAccount::count(),
            'lastSyncedAt' => now()->format('d/m/Y, H:i:s'),
        ]);
    }

    public function pullSpreadsheet(Request $request)
    {
        $gasUrl = AppConfig::get('gasUrl');
        if (empty($gasUrl)) {
            return response()->json(['success' => false, 'message' => 'URL Google Apps Script belum dikonfigurasi.'], 400);
        }

        try {
            $response = Http::timeout(30)->get($gasUrl, ['action' => 'getAll']);
            if ($response->successful()) {
                $data = $response->json();
                $rows = $data['data'] ?? ($data['submissions'] ?? []);
                $count = 0;

                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $id = $row['id'] ?? ('sheet_' . uniqid());
                        $type = $row['type'] ?? 'pc';
                        $payload = $row['data'] ?? $row;

                        $nama = $payload['nama'] ?? $payload['namaLengkap'] ?? ($payload['computerName'] ?? 'Aset');
                        $departemen = $payload['departemen'] ?? $payload['kategori'] ?? 'Umum';
                        $jabatan = $payload['jabatan'] ?? '-';
                        $jenis = $payload['jenisPerangkat'] ?? $payload['jenisHp'] ?? ($type === 'pc' ? 'Laptop' : 'Smartphone');

                        Submission::updateOrCreate(
                            ['id' => $id],
                            [
                                'type' => $type,
                                'timestamp' => $row['timestamp'] ?? now()->toDateTimeString(),
                                'nama' => $nama,
                                'departemen' => $departemen,
                                'jabatan' => $jabatan,
                                'jenis_perangkat' => $jenis,
                                'data' => $payload,
                                'synced_to_google' => true,
                            ]
                        );
                        $count++;
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menyinkronkan {$count} baris data dari Google Spreadsheet!",
                    'count' => $count
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal menghubungi Google Apps Script: ' . $response->status()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error sinkronisasi: ' . $e->getMessage()], 500);
        }
    }
}

