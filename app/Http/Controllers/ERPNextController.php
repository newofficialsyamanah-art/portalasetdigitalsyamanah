<?php

namespace App\Http\Controllers;

use App\Models\AppConfig;
use App\Models\Submission;
use App\Models\Division;
use App\Services\ERPNextService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ERPNextController extends Controller
{
    /**
     * Save ERPNext API credentials & settings
     */
    public function saveConfig(Request $request)
    {
        $request->validate([
            'erpnextUrl' => 'required|string',
            'erpnextApiKey' => 'required|string',
            'erpnextApiSecret' => 'required|string',
        ]);

        $cleanUrl = rtrim(trim($request->erpnextUrl), '/');
        AppConfig::set('erpnextUrl', $cleanUrl);
        AppConfig::set('erpnextApiKey', trim($request->erpnextApiKey));
        AppConfig::set('erpnextApiSecret', trim($request->erpnextApiSecret));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi integrasi ERPNext berhasil disimpan!'
            ]);
        }

        return back()->with('success', 'Konfigurasi integrasi ERPNext berhasil disimpan!');
    }

    /**
     * Test connection to ERPNext
     */
    public function testConnection(Request $request)
    {
        $url = $request->input('erpnextUrl') ?: AppConfig::get('erpnextUrl');
        $key = $request->input('erpnextApiKey') ?: AppConfig::get('erpnextApiKey', '43895e90883a899');
        $secret = $request->input('erpnextApiSecret') ?: AppConfig::get('erpnextApiSecret', '3d8c285da00a784');

        $service = new ERPNextService($url, $key, $secret);
        $result = $service->testConnection();

        return response()->json($result);
    }

    /**
     * Fetch list of assets from ERPNext (Tarik Data dari ERPNext)
     */
    public function fetchAssets(Request $request)
    {
        $isDemo = $request->boolean('isDemo');

        // Handle Demonstration / Simulation Mode
        if ($isDemo) {
            $demoAssets = [
                [
                    'name' => 'AST-2026-0001',
                    'asset_name' => 'Laptop Lenovo ThinkPad T14 Gen 3',
                    'item_code' => 'IT-NB-001',
                    'department' => 'Sales Jaket',
                    'custodian' => 'Wilda',
                    'status' => 'Submitted',
                    'creation' => now()->subDays(2)->format('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'AST-2026-0002',
                    'asset_name' => 'PC Desktop Workstation Core i5',
                    'item_code' => 'IT-PC-002',
                    'department' => 'Sales Jersey',
                    'custodian' => 'Ahmad Fauzi',
                    'status' => 'Submitted',
                    'creation' => now()->subDay()->format('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'AST-2026-0003',
                    'asset_name' => 'Samsung Galaxy A15 Operasional',
                    'item_code' => 'IT-HP-003',
                    'department' => 'Sales Seragam',
                    'custodian' => 'Siti Rahma',
                    'status' => 'Draft',
                    'creation' => now()->format('Y-m-d H:i:s'),
                ],
            ];

            $importedCount = 0;
            if ($request->boolean('importToDb')) {
                foreach ($demoAssets as $a) {
                    Division::firstOrCreate(['name' => $a['department']]);
                    Submission::updateOrCreate(
                        ['id' => 'erp_' . strtolower(str_replace('-', '_', $a['name']))],
                        [
                            'type' => 'pc',
                            'timestamp' => $a['creation'],
                            'nama' => $a['custodian'],
                            'departemen' => $a['department'],
                            'jabatan' => 'ERP Asset Holder',
                            'jenis_perangkat' => 'Aset ERPNext',
                            'data' => [
                                'erpnext_id' => $a['name'],
                                'nama' => $a['custodian'],
                                'departemen' => $a['department'],
                                'asset_name' => $a['asset_name'],
                                'item_code' => $a['item_code'],
                                'status' => $a['status'],
                                'source' => 'ERPNext REST API (Demo Sync)',
                            ],
                            'synced_to_google' => true,
                        ]
                    );
                    $importedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'isDemo' => true,
                'docType' => 'Asset (Simulasi)',
                'count' => count($demoAssets),
                'assets' => $demoAssets,
                'importedCount' => $importedCount,
                'endpoint' => 'GET https://erp.kantor.com/api/resource/Asset',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'message' => 'Simulasi penarikan data aset ERPNext berhasil! Ditemukan 3 aset.'
            ]);
        }

        // Live Mode: query real ERPNext server
        $url = $request->input('erpnextUrl') ?: AppConfig::get('erpnextUrl');
        $key = $request->input('erpnextApiKey') ?: AppConfig::get('erpnextApiKey', '43895e90883a899');
        $secret = $request->input('erpnextApiSecret') ?: AppConfig::get('erpnextApiSecret', '3d8c285da00a784');
        $doctype = $request->input('doctype', 'Asset');
        $limit = max(1, min((int) $request->input('limit', 500), 5000));

        $service = new ERPNextService($url, $key, $secret);
        $result = $service->getAssetsDetailed($limit, $doctype);

        if (!$result['success']) {
            return response()->json($result);
        }

        $assets = $result['assets'];
        $importedCount = 0;

        // Auto-save to local inventory if requested
        if ($request->boolean('importToDb') && !empty($assets)) {
            foreach ($assets as $a) {
                $name = $a['asset_name'] ?? ($a['item_name'] ?? ($a['employee_name'] ?? ($a['name'] ?? 'Aset ERPNext')));
                $dept = $a['department'] ?? ($a['item_group'] ?? 'General');
                $custodian = $a['custodian'] ?? ($a['employee_name'] ?? $name);
                Division::firstOrCreate(['name' => $dept]);

                Submission::updateOrCreate(
                    ['id' => 'erp_' . ($a['name'] ?? uniqid())],
                    [
                        'type' => 'pc',
                        'timestamp' => $a['creation'] ?? now()->toDateTimeString(),
                        'nama' => $custodian,
                        'departemen' => $dept,
                        'jabatan' => 'ERPNext Asset Holder',
                        'jenis_perangkat' => 'Aset Kantor ERP',
                        'data' => [
                            'erpnext_id' => $a['name'] ?? '',
                            'nama' => $custodian,
                            'departemen' => $dept,
                            'asset_name' => $name,
                            'item_code' => $a['item_code'] ?? '-',
                            'status' => $a['status'] ?? 'Active',
                            'source' => "ERPNext REST API ({$doctype})",
                        ],
                        'synced_to_google' => true,
                    ]
                );
                $importedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'isDemo' => false,
            'docType' => $result['docType'] ?? 'Asset',
            'count' => count($assets),
            'assets' => $assets,
            'importedCount' => $importedCount,
            'endpoint' => $result['endpoint'] ?? '',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'message' => $result['message']
        ]);
    }

    /**
     * Push asset to ERPNext (Kirim Data ke ERPNext)
     */
    public function pushAsset(Request $request)
    {
        $isDemo = $request->boolean('isDemo');

        // Handle Demonstration / Simulation Mode
        if ($isDemo) {
            $name = $request->input('asset_name', 'Laptop ASUS ExpertBook - Wilda');
            $newId = 'AST-' . date('Y') . '-' . rand(1000, 9999);
            return response()->json([
                'success' => true,
                'isDemo' => true,
                'message' => "Aset '{$name}' berhasil didaftarkan ke ERPNext (Simulasi ID: {$newId})!",
                'data' => [
                    'name' => $newId,
                    'asset_name' => $name,
                    'department' => $request->input('department', 'Sales Jaket'),
                    'custodian' => $request->input('custodian', 'Wilda'),
                    'status' => 'Submitted'
                ],
                'telemetry' => [
                    'endpoint' => 'POST https://erp.kantor.com/api/resource/Asset',
                    'method' => 'POST',
                    'headers_sent' => [
                        'Authorization' => 'token 43895e90883a899:3d8c••••',
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'payload_sent' => [
                        'asset_name' => $name,
                        'item_code' => $request->input('item_code', 'IT-ASSET'),
                        'department' => $request->input('department', 'Sales Jaket'),
                        'custodian' => $request->input('custodian', 'Wilda'),
                        'is_existing_asset' => 1
                    ],
                    'http_status' => 201,
                    'raw_response' => [
                        'data' => [
                            'name' => $newId,
                            'asset_name' => $name,
                            'docstatus' => 1,
                            'status' => 'Submitted'
                        ]
                    ],
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        }

        // Live Mode: push to ERPNext server
        $url = $request->input('erpnextUrl') ?: AppConfig::get('erpnextUrl');
        $key = $request->input('erpnextApiKey') ?: AppConfig::get('erpnextApiKey', '43895e90883a899');
        $secret = $request->input('erpnextApiSecret') ?: AppConfig::get('erpnextApiSecret', '3d8c285da00a784');

        $service = new ERPNextService($url, $key, $secret);

        $submissionId = $request->input('submission_id');
        $payload = [];

        if ($submissionId) {
            $sub = Submission::find($submissionId);
            if ($sub) {
                $d = $sub->data ?? [];
                $specDetails = [];
                if (!empty($d['ip']) && $d['ip'] !== '-') $specDetails[] = "IP: {$d['ip']}";
                if (!empty($d['os']) && $d['os'] !== '-') $specDetails[] = "OS: {$d['os']}";
                if (!empty($d['processor']) && $d['processor'] !== '-') $specDetails[] = "CPU: {$d['processor']}";
                if (!empty($d['memory']) && $d['memory'] !== '-') $specDetails[] = "RAM: {$d['memory']}";

                $payload = [
                    'asset_name' => $sub->nama . ' - ' . ($d['computerName'] ?? ($d['jenisHp'] ?? $sub->jenis_perangkat)),
                    'item_code' => $d['computerName'] ?? ($d['jenisHp'] ?? 'IT-ASSET'),
                    'department' => $sub->departemen,
                    'custodian' => $sub->nama,
                    'gross_purchase_amount' => $request->filled('gross_purchase_amount') ? (float)$request->input('gross_purchase_amount') : 1,
                    'is_existing_asset' => 1,
                    'description' => 'Aset: ' . ($d['computerName'] ?? $sub->nama) . (!empty($specDetails) ? ' (' . implode(', ', $specDetails) . ')' : '')
                ];
            }
        }

        if ($request->filled('asset_name')) {
            $payload['asset_name'] = $request->input('asset_name');
        }
        if ($request->filled('department')) {
            $payload['department'] = $request->input('department');
        }
        if ($request->filled('custodian')) {
            $payload['custodian'] = $request->input('custodian');
        }
        if ($request->filled('item_code')) {
            $payload['item_code'] = $request->input('item_code');
        }
        if ($request->filled('location')) {
            $payload['location'] = $request->input('location');
        }
        if ($request->filled('gross_purchase_amount')) {
            $payload['gross_purchase_amount'] = (float)$request->input('gross_purchase_amount');
        }

        if (empty($payload)) {
            $payload = [
                'asset_name' => 'Laptop ThinkPad X1 - Budi Santoso',
                'item_code' => 'IT-PC-001',
                'department' => 'Sales Jaket',
                'custodian' => 'Budi Santoso',
                'gross_purchase_amount' => 1,
                'is_existing_asset' => 1,
            ];
        }

        $doctype = $request->input('doctype', 'Asset');
        $result = $service->createAsset($payload, $doctype);

        return response()->json($result);
    }

    /**
     * Fetch master data (Locations, Departments, Employees, Items, Companies) from ERPNext
     */
    public function getMasters(Request $request)
    {
        $url = $request->input('erpnextUrl') ?: AppConfig::get('erpnextUrl');
        $key = $request->input('erpnextApiKey') ?: AppConfig::get('erpnextApiKey', '43895e90883a899');
        $secret = $request->input('erpnextApiSecret') ?: AppConfig::get('erpnextApiSecret', '3d8c285da00a784');

        $service = new ERPNextService($url, $key, $secret);
        $masters = $service->getMasterData();

        return response()->json([
            'success' => true,
            'masters' => $masters
        ]);
    }
}
