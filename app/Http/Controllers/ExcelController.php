<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExcelController extends Controller
{
    /**
     * Export all data in JSON format for client-side multi-sheet Excel generation
     * or stream as CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        // Check if user has permission to view confidential credentials
        $canViewCredentials = $user && (
            $user->role === 'superadmin' || 
            $user->role === 'manager' || 
            (method_exists($user, 'can') && $user->can('view credentials'))
        );

        $type = $request->query('type', 'all'); // 'all', 'pc', 'akun', 'mikrotik'
        $format = $request->query('format', 'json'); // 'json', 'csv'

        // 1. Fetch PC & Laptop Submissions
        $pcSubmissions = Submission::where(function ($q) {
            $q->where('type', 'pc')
              ->orWhere('jenis_perangkat', 'like', '%laptop%')
              ->orWhere('jenis_perangkat', 'like', '%pc%')
              ->orWhere('jenis_perangkat', 'like', '%desktop%');
        })->orderBy('created_at', 'desc')->get();

        $pcRows = $pcSubmissions->map(function ($sub, $idx) use ($canViewCredentials) {
            $d = $sub->data ?? [];
            return [
                'No' => $idx + 1,
                'ID' => $sub->id,
                'Timestamp' => $sub->timestamp ?: ($sub->created_at ? $sub->created_at->format('Y-m-d H:i:s') : ''),
                'Nama' => $sub->nama ?: ($d['nama'] ?? ''),
                'Departemen' => $sub->departemen ?: ($d['departemen'] ?? ''),
                'Jabatan' => $sub->jabatan ?: ($d['jabatan'] ?? ''),
                'Jenis Perangkat' => $sub->jenis_perangkat ?: ($d['jenisPerangkat'] ?? 'PC Desktop'),
                'Computer Name' => $d['computerName'] ?? '',
                'IP' => $d['ip'] ?? '',
                'Password PC' => $canViewCredentials ? ($d['passwordPc'] ?? '-') : '***TERLINDUNGI***',
                'Operating System' => $d['os'] ?? '',
                'System Manufacturer' => $d['manufacturer'] ?? '',
                'Processor' => $d['processor'] ?? '',
                'Memory' => $d['memory'] ?? '',
                'Display Link' => $d['displayLink'] ?? '',
                'Sound Link' => $d['soundLink'] ?? '',
                'Input Link' => $d['inputLink'] ?? '',
            ];
        })->toArray();

        // 2. Fetch Smartphone & Akun Sales Submissions
        $akunSubmissions = Submission::where(function ($q) {
            $q->where('type', 'akun')
              ->orWhere('jenis_perangkat', 'like', '%smartphone%')
              ->orWhere('jenis_perangkat', 'like', '%hp%')
              ->orWhere('jenis_perangkat', 'like', '%android%');
        })->orderBy('created_at', 'desc')->get();

        $akunRows = $akunSubmissions->map(function ($sub, $idx) use ($canViewCredentials) {
            $d = $sub->data ?? [];
            return [
                'No' => $idx + 1,
                'ID' => $sub->id,
                'Timestamp' => $sub->timestamp ?: ($sub->created_at ? $sub->created_at->format('Y-m-d H:i:s') : ''),
                'Nama Pemegang' => $sub->nama ?: ($d['nama'] ?? ''),
                'Kategori Tim/Role' => $sub->departemen ?: ($d['kategori'] ?? ''),
                'Jabatan' => $sub->jabatan ?: ($d['jabatan'] ?? ''),
                'Jenis Handphone' => $d['jenisHp'] ?? '',
                'Bekas handphone siapa?' => $d['bekasHp'] ?? '',
                'Akun login di PC' => $d['akunLoginPc'] ?? '',
                'Email 1' => $d['email1'] ?? '',
                'Password Email 1' => $canViewCredentials ? ($d['passEmail1'] ?? '-') : '***TERLINDUNGI***',
                'Email 2' => $d['email2'] ?? '',
                'Password Email 2' => $canViewCredentials ? ($d['passEmail2'] ?? '-') : '***TERLINDUNGI***',
                'Email 3' => $d['email3'] ?? '',
                'Password Email 3' => $canViewCredentials ? ($d['passEmail3'] ?? '-') : '***TERLINDUNGI***',
                'Username IG 1' => $d['ig1'] ?? '',
                'Password IG 1' => $canViewCredentials ? ($d['passIg1'] ?? '-') : '***TERLINDUNGI***',
                'Username IG 2' => $d['ig2'] ?? '',
                'Password IG 2' => $canViewCredentials ? ($d['passIg2'] ?? '-') : '***TERLINDUNGI***',
                'Username IG 3' => $d['ig3'] ?? '',
                'Password IG 3' => $canViewCredentials ? ($d['passIg3'] ?? '-') : '***TERLINDUNGI***',
                'Facebook 1' => $d['fb1'] ?? '',
                'Password FB 1' => $canViewCredentials ? ($d['passFb1'] ?? '-') : '***TERLINDUNGI***',
                'Facebook 2' => $d['fb2'] ?? '',
                'Password FB 2' => $canViewCredentials ? ($d['passFb2'] ?? '-') : '***TERLINDUNGI***',
                'Facebook 3' => $d['fb3'] ?? '',
                'Password FB 3' => $canViewCredentials ? ($d['passFb3'] ?? '-') : '***TERLINDUNGI***',
                'No Telpon / WhatsApp' => $d['noWa'] ?? '',
                'Akun lain terhubung aset Syamanah' => $d['akunLain'] ?? '',
                'Akun software lain' => $d['softwareLain'] ?? '',
                'Catatan Khusus / Audit' => $d['catatan'] ?? '',
            ];
        })->toArray();

        // 3. Fetch MikroTik Hotspot Accounts
        $mikrotikAccounts = MikrotikAccount::orderBy('created_at', 'desc')->get();
        $mikrotikRows = $mikrotikAccounts->map(function ($m, $idx) use ($canViewCredentials) {
            return [
                'No' => $idx + 1,
                'ID' => $m->id,
                'Tanggal Dibuat' => $m->created_at_str ?: ($m->created_at ? $m->created_at->format('Y-m-d H:i:s') : ''),
                'Nama Pemilik' => $m->nama_pemilik ?? '',
                'Tim' => $m->tim ?? '',
                'Jabatan' => $m->jabatan ?? '',
                'Username' => $m->username ?? '',
                'Password' => $canViewCredentials ? ($m->password ?? '-') : '***TERLINDUNGI***',
                'Profile Hotspot' => $m->profile_hotspot ?? '',
                'Rate Limit' => $m->rate_limit ?? '',
                'Status' => $m->status ?? 'aktif',
                'Terminal' => $m->command_terminal ?? '',
            ];
        })->toArray();

        // Return CSV stream if requested
        if ($format === 'csv') {
            $dataToStream = $type === 'pc' ? $pcRows : ($type === 'akun' ? $akunRows : ($type === 'mikrotik' ? $mikrotikRows : array_merge($pcRows, $akunRows)));
            $filename = "Export_{$type}_" . date('Ymd_His') . ".csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            return response()->stream(function () use ($dataToStream) {
                $out = fopen('php://output', 'w');
                // UTF-8 BOM for Microsoft Excel compatibility
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
                if (!empty($dataToStream)) {
                    fputcsv($out, array_keys($dataToStream[0]));
                    foreach ($dataToStream as $row) {
                        fputcsv($out, $row);
                    }
                }
                fclose($out);
            }, 200, $headers);
        }

        // Default: return JSON payload for client SheetJS (.xlsx generation)
        return response()->json([
            'success' => true,
            'canViewCredentials' => $canViewCredentials,
            'filename' => 'Database_Aset_Syamanah_' . date('Y-m-d') . '.xlsx',
            'sheets' => [
                'Spesifikasi PC & Laptop' => $pcRows,
                'Akun Sales & HP' => $akunRows,
                'Akun MikroTik' => $mikrotikRows,
            ],
            'counts' => [
                'pc' => count($pcRows),
                'akun' => count($akunRows),
                'mikrotik' => count($mikrotikRows),
                'total' => count($pcRows) + count($akunRows) + count($mikrotikRows),
            ]
        ]);
    }

    /**
     * Import records parsed from Excel / CSV
     */
    public function import(Request $request)
    {
        // 1. Check if parsed JSON payload is provided (via SheetJS)
        $pcList = $request->input('pcRecords', []);
        $akunList = $request->input('akunRecords', []);
        $mikrotikList = $request->input('mikrotikRecords', []);

        // 2. Fallback: Check if file was uploaded directly via multipart form
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'csv') {
                $csvRows = $this->parseCsv($file->getRealPath());
                // Sort into pc or akun based on header/columns
                foreach ($csvRows as $row) {
                    $normKeys = array_map(function($k) { return strtolower(preg_replace('/[^a-z0-9]/', '', $k)); }, array_keys($row));
                    if (in_array('computername', $normKeys) || in_array('processor', $normKeys) || in_array('dxdiag', $normKeys)) {
                        $pcList[] = $row;
                    } elseif (in_array('jenishp', $normKeys) || in_array('email1', $normKeys) || in_array('kategoritimrole', $normKeys)) {
                        $akunList[] = $row;
                    } elseif (in_array('profilehotspot', $normKeys) || in_array('commandterminal', $normKeys)) {
                        $mikrotikList[] = $row;
                    } else {
                        // Default to pc or akun based on column count
                        if (count($row) >= 12) {
                            $akunList[] = $row;
                        } else {
                            $pcList[] = $row;
                        }
                    }
                }
            }
        }

        $importedPC = 0;
        $importedAkun = 0;
        $importedMikrotik = 0;

        // Process PC & Laptop Records
        foreach ($pcList as $idx => $row) {
            $nama = $this->findVal($row, ['Nama', 'nama', 'Nama Lengkap', 'User', 'Pengguna', 'Nama Staf', 'Staf']);
            if (empty($nama)) continue;

            $dept = $this->findVal($row, ['Departemen', 'departemen', 'Divisi', 'Bagian', 'Department']);
            if (empty($dept)) $dept = 'General';
            Division::firstOrCreate(['name' => $dept]);

            $id = $this->findVal($row, ['ID', 'id']) ?: 'pc_import_' . time() . '_' . $idx . '_' . Str::random(4);
            $cName = $this->findVal($row, ['Computer Name', 'computerName', 'Nama Komputer', 'Device Name', 'Hostname']) ?: 'PC-' . $nama;
            $isLaptop = str_contains(strtoupper($cName), 'LAPTOP') || str_contains(strtoupper($cName), 'MACBOOK') || str_contains(strtoupper($this->findVal($row, ['Jenis Perangkat', 'jenisPerangkat']) ?: ''), 'LAPTOP');

            $data = [
                'nama' => $nama,
                'departemen' => $dept,
                'jabatan' => $this->findVal($row, ['Jabatan', 'jabatan', 'Posisi']) ?: '-',
                'jenisPerangkat' => $isLaptop ? 'Laptop' : 'PC Desktop',
                'computerName' => $cName,
                'ip' => $this->findVal($row, ['IP', 'ip', 'IP Address']) ?: '-',
                'passwordPc' => $this->findVal($row, ['Password PC', 'passwordPc', 'Password']) ?: '-',
                'os' => $this->findVal($row, ['Operating System', 'os', 'OS', 'Sistem Operasi']) ?: 'Windows 10 / 11',
                'manufacturer' => $this->findVal($row, ['System Manufacturer', 'manufacturer', 'Brand', 'Merk']) ?: '-',
                'processor' => $this->findVal($row, ['Processor', 'processor', 'CPU', 'Prosesor']) ?: '-',
                'memory' => $this->findVal($row, ['Memory', 'memory', 'RAM']) ?: '-',
                'displayLink' => $this->findVal($row, ['Display Link', 'displayLink', 'Display']) ?: '-',
                'soundLink' => $this->findVal($row, ['Sound Link', 'soundLink', 'Sound']) ?: '-',
                'inputLink' => $this->findVal($row, ['Input Link', 'inputLink', 'Input']) ?: '-',
            ];

            Submission::updateOrCreate(
                ['id' => $id],
                [
                    'type' => 'pc',
                    'timestamp' => $this->findVal($row, ['Timestamp', 'timestamp', 'Waktu', 'Tanggal']) ?: now()->format('Y-m-d H:i:s'),
                    'nama' => $nama,
                    'departemen' => $dept,
                    'jabatan' => $data['jabatan'],
                    'jenis_perangkat' => $data['jenisPerangkat'],
                    'data' => $data,
                    'synced_to_google' => true,
                ]
            );
            $importedPC++;
        }

        // Process Smartphone & Akun Sales Records
        foreach ($akunList as $idx => $row) {
            $nama = $this->findVal($row, ['Nama Pemegang', 'Nama', 'nama', 'Nama Lengkap', 'Sales', 'Staf Sales']);
            if (empty($nama)) continue;

            $kategori = $this->findVal($row, ['Kategori Tim/Role', 'Kategori', 'kategori', 'Departemen', 'departemen', 'Divisi', 'Tim']);
            if (empty($kategori)) $kategori = 'Sales & Marketing';
            Division::firstOrCreate(['name' => $kategori]);

            $id = $this->findVal($row, ['ID', 'id']) ?: 'akun_import_' . time() . '_' . $idx . '_' . Str::random(4);

            $data = [
                'nama' => $nama,
                'kategori' => $kategori,
                'jabatan' => $this->findVal($row, ['Jabatan', 'jabatan', 'Posisi']) ?: 'Staff Sales',
                'jenisHp' => $this->findVal($row, ['Jenis Handphone', 'Jenis HP', 'jenisHp', 'Tipe HP']) ?: '-',
                'bekasHp' => $this->findVal($row, ['Bekas handphone siapa?', 'Bekas HP Siapa', 'bekasHp']) ?: '-',
                'akunLoginPc' => $this->findVal($row, ['Akun login di PC', 'Akun Login PC', 'akunLoginPc']) ?: '-',
                'email1' => $this->findVal($row, ['Email 1', 'email1', 'Email']) ?: '-',
                'passEmail1' => $this->findVal($row, ['Password Email 1', 'passEmail1', 'Password 1']) ?: '-',
                'email2' => $this->findVal($row, ['Email 2', 'email2']) ?: '-',
                'passEmail2' => $this->findVal($row, ['Password Email 2', 'passEmail2']) ?: '-',
                'email3' => $this->findVal($row, ['Email 3', 'email3']) ?: '-',
                'passEmail3' => $this->findVal($row, ['Password Email 3', 'passEmail3']) ?: '-',
                'ig1' => $this->findVal($row, ['Username IG 1', 'ig1', 'Instagram 1']) ?: '-',
                'passIg1' => $this->findVal($row, ['Password IG 1', 'passIg1']) ?: '-',
                'ig2' => $this->findVal($row, ['Username IG 2', 'ig2']) ?: '-',
                'passIg2' => $this->findVal($row, ['Password IG 2', 'passIg2']) ?: '-',
                'ig3' => $this->findVal($row, ['Username IG 3', 'ig3']) ?: '-',
                'passIg3' => $this->findVal($row, ['Password IG 3', 'passIg3']) ?: '-',
                'fb1' => $this->findVal($row, ['Facebook 1', 'fb1']) ?: '-',
                'passFb1' => $this->findVal($row, ['Password FB 1', 'passFb1']) ?: '-',
                'fb2' => $this->findVal($row, ['Facebook 2', 'fb2']) ?: '-',
                'passFb2' => $this->findVal($row, ['Password FB 2', 'passFb2']) ?: '-',
                'fb3' => $this->findVal($row, ['Facebook 3', 'fb3']) ?: '-',
                'passFb3' => $this->findVal($row, ['Password FB 3', 'passFb3']) ?: '-',
                'noWa' => $this->findVal($row, ['No Telpon / WhatsApp', 'No WA', 'noWa', 'WhatsApp']) ?: '-',
                'akunLain' => $this->findVal($row, ['Akun lain terhubung aset Syamanah', 'akunLain']) ?: '-',
                'softwareLain' => $this->findVal($row, ['Akun software lain', 'softwareLain']) ?: '-',
                'catatan' => $this->findVal($row, ['Catatan Khusus / Audit', 'catatan', 'Catatan']) ?: '-',
            ];

            Submission::updateOrCreate(
                ['id' => $id],
                [
                    'type' => 'akun',
                    'timestamp' => $this->findVal($row, ['Timestamp', 'timestamp', 'Waktu', 'Tanggal']) ?: now()->format('Y-m-d H:i:s'),
                    'nama' => $nama,
                    'departemen' => $kategori,
                    'jabatan' => $data['jabatan'],
                    'jenis_perangkat' => $data['jenisHp'],
                    'data' => $data,
                    'synced_to_google' => true,
                ]
            );
            $importedAkun++;
        }

        // Process MikroTik Records
        foreach ($mikrotikList as $idx => $row) {
            $nama = $this->findVal($row, ['Nama Pemilik', 'Nama', 'namaPemilik', 'nama']);
            $username = $this->findVal($row, ['Username', 'username', 'User']);
            if (empty($nama) && empty($username)) continue;

            $tim = $this->findVal($row, ['Tim', 'tim', 'Departemen', 'Divisi']) ?: 'Sales Jaket';
            $password = $this->findVal($row, ['Password', 'password']) ?: (string)rand(100000, 999999);
            $profile = $this->findVal($row, ['Profile Hotspot', 'profileHotspot', 'Profile', 'profile']) ?: 'sales_jaket';
            $id = $this->findVal($row, ['ID', 'id']) ?: 'mkt_import_' . time() . '_' . $idx . '_' . Str::random(4);

            MikrotikAccount::updateOrCreate(
                ['id' => $id],
                [
                    'nama_pemilik' => $nama ?: $username,
                    'tim' => $tim,
                    'jabatan' => $this->findVal($row, ['Jabatan', 'jabatan']) ?: 'Staff',
                    'username' => $username ?: 'MKT' . rand(10, 99),
                    'password' => $password,
                    'profile_hotspot' => $profile,
                    'rate_limit' => $this->findVal($row, ['Rate Limit', 'rateLimit']) ?: '10M/10M',
                    'command_terminal' => $this->findVal($row, ['Terminal', 'commandTerminal']) ?: "/ip hotspot user add name=\"{$username}\" password=\"{$password}\" profile=\"{$profile}\"",
                    'status' => $this->findVal($row, ['Status', 'status']) ?: 'aktif',
                    'created_at_str' => $this->findVal($row, ['Tanggal Dibuat', 'createdAt']) ?: now()->format('Y-m-d H:i:s'),
                ]
            );
            $importedMikrotik++;
        }

        $totalImported = $importedPC + $importedAkun + $importedMikrotik;
        $message = "Berhasil mengimpor {$totalImported} data aset ({$importedPC} PC & Laptop, {$importedAkun} HP Sales, {$importedMikrotik} Voucher MikroTik)!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'counts' => [
                    'pc' => $importedPC,
                    'akun' => $importedAkun,
                    'mikrotik' => $importedMikrotik,
                    'total' => $totalImported,
                ]
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Download CSV template for easy data filling
     */
    public function template(Request $request)
    {
        $type = $request->query('type', 'pc'); // 'pc', 'akun', 'mikrotik'
        
        $headersMap = [
            'pc' => [
                'Nama', 'Departemen', 'Jabatan', 'Jenis Perangkat', 'Computer Name', 'IP', 
                'Password PC', 'Operating System', 'System Manufacturer', 'Processor', 'Memory'
            ],
            'akun' => [
                'Nama Pemegang', 'Kategori Tim/Role', 'Jabatan', 'Jenis Handphone', 'Bekas handphone siapa?', 
                'Akun login di PC', 'Email 1', 'Password Email 1', 'Email 2', 'Password Email 2', 
                'Username IG 1', 'Password IG 1', 'Facebook 1', 'Password FB 1', 'No Telpon / WhatsApp', 'Catatan Khusus / Audit'
            ],
            'mikrotik' => [
                'Nama Pemilik', 'Tim', 'Jabatan', 'Username', 'Password', 'Profile Hotspot', 'Rate Limit', 'Status'
            ],
        ];

        $columns = $headersMap[$type] ?? $headersMap['pc'];
        $filename = "Template_Import_{$type}.csv";

        return response()->stream(function () use ($columns) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($out, $columns);
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Helper to find value from row using multiple possible keys
     */
    private function findVal(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $k) {
            if (isset($row[$k]) && trim((string)$row[$k]) !== '') {
                return trim((string)$row[$k]);
            }
        }

        // Normalized comparison
        $rowKeys = array_keys($row);
        $normMap = [];
        foreach ($rowKeys as $rk) {
            $norm = strtolower(preg_replace('/[^a-z0-9]/', '', $rk));
            $normMap[$norm] = $rk;
        }

        foreach ($possibleKeys as $k) {
            $norm = strtolower(preg_replace('/[^a-z0-9]/', '', $k));
            if (isset($normMap[$norm])) {
                $actualKey = $normMap[$norm];
                if (isset($row[$actualKey]) && trim((string)$row[$actualKey]) !== '') {
                    return trim((string)$row[$actualKey]);
                }
            }
        }

        return '';
    }

    /**
     * Helper to parse CSV file
     */
    private function parseCsv(string $path): array
    {
        $rows = [];
        if (!file_exists($path) || !is_readable($path)) return $rows;

        if (($handle = fopen($path, 'r')) !== false) {
            // Check BOM
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $headers = null;
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                // If comma didn't split, try semicolon
                if (count($data) === 1 && str_contains($data[0], ';')) {
                    $data = str_getcsv($data[0], ';');
                }

                if (!$headers) {
                    $headers = array_map('trim', $data);
                } else {
                    $row = [];
                    foreach ($headers as $i => $colName) {
                        $row[$colName] = $data[$i] ?? '';
                    }
                    $rows[] = $row;
                }
            }
            fclose($handle);
        }

        return $rows;
    }
}
