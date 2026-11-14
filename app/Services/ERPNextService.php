<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ERPNextService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null, ?string $apiSecret = null)
    {
        $this->baseUrl = rtrim($baseUrl ?: AppConfig::get('erpnextUrl', ''), '/');
        $this->apiKey = $apiKey ?: AppConfig::get('erpnextApiKey', '43895e90883a899');
        $this->apiSecret = $apiSecret ?: AppConfig::get('erpnextApiSecret', '3d8c285da00a784');
    }

    /**
     * Get authorization headers for Frappe / ERPNext REST API
     */
    public function getHeaders(): array
    {
        return [
            'Authorization' => "token {$this->apiKey}:{$this->apiSecret}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get Base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the logged in user's email/username from Frappe
     */
    public function getLoggedUser(): ?string
    {
        if (empty($this->baseUrl)) return null;

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(8)
                ->get("{$this->baseUrl}/api/method/frappe.auth.get_logged_user");

            if ($response->successful()) {
                return $response->json('message');
            }
        } catch (\Exception $e) {
            // ignore
        }
        return null;
    }

    /**
     * Test connection to ERPNext instance
     */
    public function testConnection(): array
    {
        if (empty($this->baseUrl)) {
            return [
                'success' => false,
                'message' => 'URL Server ERPNext belum diatur. Harap masukkan URL lengkap di kolom formulir (contoh: https://erp.kantor.com).'
            ];
        }

        try {
            $endpoint = "{$this->baseUrl}/api/method/frappe.auth.get_logged_user";
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(12)
                ->get($endpoint);

            if ($response->successful()) {
                $user = $response->json('message');
                return [
                    'success' => true,
                    'message' => "Berhasil terhubung ke ERPNext! Pengguna terautentikasi: {$user}",
                    'user' => $user,
                    'status_code' => $response->status(),
                    'endpoint' => $endpoint
                ];
            }

            if ($response->status() === 401) {
                return [
                    'success' => false,
                    'message' => 'Gagal autentikasi (HTTP 401 Unauthorized): API Key atau Secret tidak cocok di sistem Frappe/ERPNext.',
                    'status_code' => 401,
                    'endpoint' => $endpoint
                ];
            }

            $errMsg = $response->json('message') ?? ($response->body() ?: ('Status ' . $response->status()));
            return [
                'success' => false,
                'message' => "Gagal terhubung ke ERPNext (Status {$response->status()}): {$errMsg}",
                'status_code' => $response->status(),
                'endpoint' => $endpoint
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server ERPNext (' . $this->baseUrl . '): ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Assets / Items / Employees with detailed telemetry & role diagnostics
     */
    public function getAssetsDetailed(int $limit = 50, string $doctype = 'Asset'): array
    {
        if (empty($this->baseUrl)) {
            return [
                'success' => false,
                'status_code' => 0,
                'message' => 'URL Server ERPNext belum diatur. Silakan isi URL ERP kantor Anda pada kolom di atas.',
                'assets' => [],
                'docType' => $doctype,
                'endpoint' => ''
            ];
        }

        $fieldsMap = [
            'Asset' => ['name', 'asset_name', 'item_code', 'department', 'status', 'custodian', 'creation'],
            'Item' => ['name', 'item_name', 'item_group', 'stock_uom', 'disabled', 'creation'],
            'Employee' => ['name', 'employee_name', 'department', 'designation', 'status', 'creation'],
        ];

        $fields = $fieldsMap[$doctype] ?? $fieldsMap['Asset'];

        try {
            $endpoint = "{$this->baseUrl}/api/resource/{$doctype}";
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(25)
                ->get($endpoint, [
                    'limit_page_length' => $limit,
                    'fields' => json_encode($fields)
                ]);

            if ($response->successful()) {
                $data = $response->json('data', []);
                return [
                    'success' => true,
                    'status_code' => $response->status(),
                    'message' => "Berhasil menarik " . count($data) . " data dari DocType '{$doctype}' ERPNext!",
                    'assets' => $data,
                    'docType' => $doctype,
                    'endpoint' => $endpoint
                ];
            }

            // If 403 Forbidden on Asset, provide exact role fix instructions and check Item
            if ($response->status() === 403) {
                $loggedUser = $this->getLoggedUser() ?: 'User API';

                // Check if Item DocType can be read
                $itemAccessible = false;
                $itemCount = 0;
                if ($doctype === 'Asset') {
                    try {
                        $checkItem = Http::withHeaders($this->getHeaders())
                            ->timeout(6)
                            ->get("{$this->baseUrl}/api/resource/Item", [
                                'limit_page_length' => 10,
                                'fields' => json_encode(['name', 'item_name', 'item_group'])
                            ]);
                        if ($checkItem->successful()) {
                            $itemAccessible = true;
                            $itemCount = count($checkItem->json('data', []));
                        }
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                $msg = "Akses ditolak (HTTP 403 Forbidden): User '{$loggedUser}' belum memiliki role permission untuk membaca DocType '{$doctype}' di ERPNext.";
                if ($itemAccessible) {
                    $msg .= " Namun, DocType 'Item' (Master Barang) DAPAT diakses ({$itemCount} item ditemukan). Anda dapat memilih 'Item' pada pilihan DocType di atas!";
                }

                return [
                    'success' => false,
                    'status_code' => 403,
                    'message' => $msg,
                    'logged_user' => $loggedUser,
                    'required_role' => $doctype === 'Asset' ? 'Asset Manager / Asset User' : ($doctype === 'Item' ? 'Stock User / Item Manager' : 'HR User'),
                    'item_accessible' => $itemAccessible,
                    'assets' => [],
                    'docType' => $doctype,
                    'endpoint' => $endpoint
                ];
            }

            // Fallback to Item DocType if Asset returns 404
            if ($response->status() === 404 && $doctype === 'Asset') {
                $itemEndpoint = "{$this->baseUrl}/api/resource/Item";
                $itemRes = Http::withHeaders($this->getHeaders())
                    ->timeout(12)
                    ->get($itemEndpoint, [
                        'limit_page_length' => $limit,
                        'fields' => json_encode(['name', 'item_name', 'item_group', 'stock_uom', 'disabled'])
                    ]);

                if ($itemRes->successful()) {
                    $itemData = $itemRes->json('data', []);
                    return [
                        'success' => true,
                        'status_code' => $itemRes->status(),
                        'message' => "DocType 'Asset' tidak aktif di ERP, namun berhasil membaca " . count($itemData) . " master barang dari DocType 'Item'!",
                        'assets' => $itemData,
                        'docType' => 'Item',
                        'endpoint' => $itemEndpoint
                    ];
                }
            }

            $rawMsg = $response->json('message') ?? ($response->json('_server_messages') ?? $response->body());
            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => "ERPNext merespon (HTTP {$response->status()}): {$rawMsg}",
                'assets' => [],
                'docType' => $doctype,
                'endpoint' => $endpoint
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status_code' => 500,
                'message' => "Gagal menghubungi server ERPNext ({$this->baseUrl}): " . $e->getMessage(),
                'assets' => [],
                'docType' => $doctype,
                'endpoint' => "{$this->baseUrl}/api/resource/{$doctype}"
            ];
        }
    }

    /**
     * Get valid master records from ERPNext (Locations, Departments, Employees, Items, Companies)
     */
    public function getMasterData(): array
    {
        $masters = [
            'locations' => [],
            'departments' => [],
            'employees' => [],
            'items' => [],
            'item_groups' => [],
            'company' => null
        ];

        if (empty($this->baseUrl)) return $masters;

        // 1. Fetch company
        try {
            $compRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Company", ['limit_page_length' => 1]);
            if ($compRes->successful()) {
                $compData = $compRes->json('data', []);
                if (!empty($compData)) {
                    $masters['company'] = $compData[0]['name'] ?? null;
                }
            }
        } catch (\Exception $e) {}

        // 2. Fetch locations
        try {
            $locRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Location", ['limit_page_length' => 50]);
            if ($locRes->successful()) {
                $masters['locations'] = array_column($locRes->json('data', []), 'name');
            }
        } catch (\Exception $e) {}

        // 3. Fetch departments
        try {
            $deptRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Department", ['limit_page_length' => 100]);
            if ($deptRes->successful()) {
                $masters['departments'] = array_column($deptRes->json('data', []), 'name');
            }
        } catch (\Exception $e) {}

        // 4. Fetch employees
        try {
            $empRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Employee", [
                'limit_page_length' => 100,
                'fields' => json_encode(['name', 'employee_name'])
            ]);
            if ($empRes->successful()) {
                $masters['employees'] = $empRes->json('data', []);
            }
        } catch (\Exception $e) {}

        // 5. Fetch items
        try {
            $itemRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Item", [
                'limit_page_length' => 50,
                'fields' => json_encode(['name', 'item_name', 'is_fixed_asset', 'item_group'])
            ]);
            if ($itemRes->successful()) {
                $masters['items'] = $itemRes->json('data', []);
            }
        } catch (\Exception $e) {}

        // 5b. Fetch specifically items where is_fixed_asset = 1
        $masters['fixed_asset_items'] = [];
        try {
            $faRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Item", [
                'filters' => json_encode([['is_fixed_asset', '=', 1]]),
                'limit_page_length' => 20,
                'fields' => json_encode(['name', 'item_name', 'is_fixed_asset', 'asset_category'])
            ]);
            if ($faRes->successful()) {
                $masters['fixed_asset_items'] = $faRes->json('data', []);
            }
        } catch (\Exception $e) {}

        // 6. Fetch item groups
        try {
            $igRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Item%20Group", [
                'limit_page_length' => 20
            ]);
            if ($igRes->successful()) {
                $masters['item_groups'] = array_column($igRes->json('data', []), 'name');
            }
        } catch (\Exception $e) {}

        // 7. Fetch asset categories
        try {
            $catRes = Http::withHeaders($this->getHeaders())->timeout(6)->get("{$this->baseUrl}/api/resource/Asset%20Category", [
                'limit_page_length' => 10
            ]);
            if ($catRes->successful()) {
                $masters['asset_categories'] = array_column($catRes->json('data', []), 'name');
            }
        } catch (\Exception $e) {}

        return $masters;
    }

    /**
     * Push or create an Asset in ERPNext with intelligent link-resolution & full HTTP telemetry proof
     */
    public function createAsset(array $assetData, string $doctype = 'Asset'): array
    {
        if (empty($this->baseUrl)) {
            return [
                'success' => false,
                'message' => 'URL ERPNext belum dikonfigurasi.',
                'telemetry' => null
            ];
        }

        // Auto-resolve Frappe Link Fields to prevent LinkValidationError (HTTP 417)
        if ($doctype === 'Asset') {
            $masters = $this->getMasterData();

            // 1. Resolve Company
            if (empty($assetData['company']) && !empty($masters['company'])) {
                $assetData['company'] = $masters['company'];
            }

            // 2. Resolve Location (Mandatory in Frappe Asset)
            if (!empty($masters['locations'])) {
                if (empty($assetData['location']) || !in_array($assetData['location'], $masters['locations'])) {
                    // Use first valid Location that actually exists in ERPNext
                    $assetData['location'] = $masters['locations'][0];
                }
            } else {
                // If 'Bandung Office' was hardcoded and doesn't exist, remove or default
                if (isset($assetData['location']) && $assetData['location'] === 'Bandung Office') {
                    unset($assetData['location']);
                }
            }

            // 3. Resolve Custodian (Link to Employee)
            if (!empty($assetData['custodian'])) {
                $matchedEmp = null;
                foreach ($masters['employees'] as $emp) {
                    if (strcasecmp($emp['name'], $assetData['custodian']) === 0 || 
                        (isset($emp['employee_name']) && strcasecmp($emp['employee_name'], $assetData['custodian']) === 0)) {
                        $matchedEmp = $emp['name'];
                        break;
                    }
                }

                if ($matchedEmp) {
                    $assetData['custodian'] = $matchedEmp;
                } else {
                    // Do NOT send non-existent custodian ID as Frappe will throw LinkValidationError!
                    $custName = $assetData['custodian'];
                    unset($assetData['custodian']);
                    if (!str_contains($assetData['asset_name'], $custName)) {
                        $assetData['asset_name'] .= " ({$custName})";
                    }
                }
            }

            // 4. Resolve Department (Link to Department)
            if (!empty($assetData['department'])) {
                $matchedDept = null;
                foreach ($masters['departments'] as $dept) {
                    if (strcasecmp($dept, $assetData['department']) === 0) {
                        $matchedDept = $dept;
                        break;
                    }
                }
                if ($matchedDept) {
                    $assetData['department'] = $matchedDept;
                } else {
                    // Do NOT send non-existent department string to prevent LinkValidationError!
                    unset($assetData['department']);
                }
            }

            // 5. Resolve Item Code (Must be a Fixed Asset Item in ERPNext)
            $resolvedItemCode = null;

            // Check if user's item_code matches any fixed asset item
            if (!empty($assetData['item_code']) && !empty($masters['fixed_asset_items'])) {
                foreach ($masters['fixed_asset_items'] as $fa) {
                    if (strcasecmp($fa['name'], $assetData['item_code']) === 0) {
                        $resolvedItemCode = $fa['name'];
                        break;
                    }
                }
            }

            // If not matched, use first existing fixed asset item
            if (!$resolvedItemCode && !empty($masters['fixed_asset_items'])) {
                $resolvedItemCode = $masters['fixed_asset_items'][0]['name'];
            }

            // If still no fixed asset item exists in ERPNext, attempt to auto-create one!
            if (!$resolvedItemCode) {
                try {
                    $newFixedItemCode = 'IT-ASSET';
                    $createItemPayload = [
                        'item_code' => $newFixedItemCode,
                        'item_name' => 'Aset Kantor & IT Hardware',
                        'item_group' => !empty($masters['item_groups']) ? $masters['item_groups'][0] : 'All Item Groups',
                        'stock_uom' => 'Nos',
                        'is_stock_item' => 0,
                        'is_fixed_asset' => 1,
                        'auto_create_assets' => 0,
                    ];
                    if (!empty($masters['asset_categories'])) {
                        $createItemPayload['asset_category'] = $masters['asset_categories'][0];
                    }

                    $createItemRes = Http::withHeaders($this->getHeaders())
                        ->timeout(10)
                        ->post("{$this->baseUrl}/api/resource/Item", $createItemPayload);

                    if ($createItemRes->successful()) {
                        $resolvedItemCode = $newFixedItemCode;
                    }
                } catch (\Exception $e) {}
            }

            if ($resolvedItemCode) {
                $assetData['item_code'] = $resolvedItemCode;
            }

            // 6. Resolve Asset Category
            if (empty($assetData['asset_category']) && !empty($masters['asset_categories'])) {
                $assetData['asset_category'] = $masters['asset_categories'][0];
            }

            // Ensure mandatory ERPNext Asset fields
            $assetData['is_existing_asset'] = 1;
            $assetData['available_for_use_date'] = $assetData['available_for_use_date'] ?? date('Y-m-d');
            $assetData['purchase_date'] = $assetData['purchase_date'] ?? date('Y-m-d');
            $assetData['gross_purchase_amount'] = (!empty($assetData['gross_purchase_amount']) && (float)$assetData['gross_purchase_amount'] > 0)
                ? (float)$assetData['gross_purchase_amount']
                : 1.0;
        } elseif ($doctype === 'Item') {
            $masters = $this->getMasterData();
            $displayName = $assetData['asset_name'] ?? ($assetData['item_name'] ?? ($assetData['item_code'] ?? 'Aset Baru'));
            $cleanItemCode = !empty($assetData['item_code']) 
                ? preg_replace('/[^A-Za-z0-9_\-\.]/', '-', $assetData['item_code']) 
                : Str::slug($displayName);
            $assetData = [
                'item_code' => $cleanItemCode,
                'item_name' => $displayName,
                'asset_name' => $displayName,
                'item_group' => !empty($masters['item_groups']) ? $masters['item_groups'][0] : 'All Item Groups',
                'stock_uom' => 'Nos',
                'is_stock_item' => 0,
                'description' => $assetData['description'] ?? ('Aset inventaris: ' . $displayName . (!empty($assetData['custodian']) ? " | Pemegang: {$assetData['custodian']}" : '') . (!empty($assetData['department']) ? " | Divisi: {$assetData['department']}" : ''))
            ];
        }

        $endpoint = "{$this->baseUrl}/api/resource/{$doctype}";
        $headers = $this->getHeaders();

        try {
            $response = Http::withHeaders($headers)
                ->timeout(20)
                ->post($endpoint, $assetData);

            $telemetry = [
                'endpoint' => $endpoint,
                'method' => 'POST',
                'headers_sent' => [
                    'Authorization' => "token {$this->apiKey}:" . substr($this->apiSecret, 0, 4) . '••••',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'payload_sent' => $assetData,
                'http_status' => $response->status(),
                'raw_response' => $response->json() ?: $response->body(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            if ($response->successful()) {
                $newId = $response->json('data.name') ?? ($response->json('data.item_code') ?? ($response->json('data.asset_name') ?? 'OK'));
                $label = $assetData['asset_name'] ?? ($assetData['item_name'] ?? $newId);
                return [
                    'success' => true,
                    'message' => "Berhasil! {$doctype} '{$label}' sukses terdaftar di ERPNext (ID: {$newId})!",
                    'data' => $response->json('data'),
                    'telemetry' => $telemetry
                ];
            }

            if ($response->status() === 403) {
                $loggedUser = $this->getLoggedUser() ?: 'User API';
                return [
                    'success' => false,
                    'message' => "Akses ditolak (HTTP 403): User '{$loggedUser}' tidak memiliki role 'Asset Manager' / 'System Manager' untuk membuat Dokumen di ERPNext.",
                    'telemetry' => $telemetry
                ];
            }

            $rawMsg = $response->json('exception') ?? ($response->json('_server_messages') ?? $response->body());
            $cleanMsg = null;
            if ($serverMessages = $response->json('_server_messages')) {
                $decoded = is_string($serverMessages) ? json_decode($serverMessages, true) : $serverMessages;
                if (is_array($decoded) && !empty($decoded)) {
                    $first = is_string($decoded[0]) ? json_decode($decoded[0], true) : $decoded[0];
                    $cleanMsg = $first['message'] ?? null;
                }
            }
            if (!$cleanMsg && is_string($rawMsg)) {
                $cleanMsg = str_contains($rawMsg, ':') ? trim(substr($rawMsg, strpos($rawMsg, ':') + 1)) : $rawMsg;
            }
            if (!$cleanMsg) {
                $cleanMsg = $response->json('message') ?: "Status {$response->status()}";
            }

            if ($response->status() === 417) {
                return [
                    'success' => false,
                    'message' => "Validasi Dokumen ERP (HTTP 417): {$cleanMsg}",
                    'raw_error' => $rawMsg,
                    'telemetry' => $telemetry
                ];
            }

            return [
                'success' => false,
                'message' => "ERPNext merespon (HTTP {$response->status()}): {$cleanMsg}",
                'telemetry' => $telemetry
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi error: ' . $e->getMessage(),
                'telemetry' => [
                    'endpoint' => $endpoint,
                    'method' => 'POST',
                    'payload_sent' => $assetData,
                    'http_status' => isset($response) ? $response->status() : 500,
                    'error' => $e->getMessage(),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ];
        }
    }
}
