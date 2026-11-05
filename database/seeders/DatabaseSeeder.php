<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Submission;
use App\Models\MikrotikAccount;
use App\Models\AppConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Granular Spatie Permissions
        $permissions = [
            'view credentials',
            'manage users',
            'manage divisions',
            'manage inventory',
            'manage mikrotik',
            'delete records',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Create Spatie Roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $gaRole = Role::firstOrCreate(['name' => 'general_affair', 'guard_name' => 'web']);

        // Assign Permissions to Roles
        $superadminRole->syncPermissions(Permission::all());
        $managerRole->syncPermissions([
            'view credentials',
            'manage inventory',
            'manage mikrotik',
            'delete records',
        ]);
        $gaRole->syncPermissions([
            'manage inventory',
        ]);

        // 3. Seed Default Users with Spatie Roles
        $defaultUsers = [
            [
                'name' => 'Super Admin Syamanah',
                'username' => 'superadmin',
                'email' => 'superadmin@syamanah.com',
                'role' => 'superadmin',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Manager Operasional',
                'username' => 'manager',
                'email' => 'manager@syamanah.com',
                'role' => 'manager',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'General Affair Officer',
                'username' => 'ga',
                'email' => 'ga@syamanah.com',
                'role' => 'general_affair',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($defaultUsers as $u) {
            $user = User::updateOrCreate(
                ['username' => $u['username']],
                $u
            );
            $user->syncRoles([$u['role']]);
        }

        // 4. Read from .data/db.json if available
        $jsonPath = base_path('.data/db.json');
        if (File::exists($jsonPath)) {
            $jsonContent = File::get($jsonPath);
            $db = json_decode($jsonContent, true);

            // Seed Divisions
            $divisions = $db['divisions'] ?? [
                'Sales Jaket',
                'Sales Jersey',
                'Sales Eksekutif',
                'Sales Seragam & Kaos Polo',
                'CRM (Customer Relationship Management)',
                'Marketing Communication & Ads',
                'Desain Grafis & Sublimasi',
                'Produksi & Quality Control',
                'Finance & Accounting',
                'General Affair & Fasilitas',
                'IT Infrastructure & Network',
                'HRD & Manajemen',
                'Procurement & Gudang'
            ];

            foreach ($divisions as $divName) {
                Division::updateOrCreate(['name' => trim($divName)]);
            }

            // Seed App Config
            if (!empty($db['config'])) {
                foreach ($db['config'] as $key => $val) {
                    AppConfig::set($key, $val);
                }
            }

            // Seed Submissions
            if (!empty($db['submissions']) && is_array($db['submissions'])) {
                foreach ($db['submissions'] as $sub) {
                    $id = $sub['id'] ?? ('sub_' . uniqid());
                    $type = $sub['type'] ?? 'pc';
                    $data = $sub['data'] ?? [];
                    
                    $nama = $data['nama'] ?? $data['namaLengkap'] ?? ($data['computerName'] ?? 'Aset Syamanah');
                    $departemen = $data['departemen'] ?? $data['kategori'] ?? 'Umum';
                    $jabatan = $data['jabatan'] ?? '-';
                    $jenis = $data['jenisPerangkat'] ?? $data['jenisHp'] ?? ($type === 'pc' ? 'Laptop' : 'Smartphone');

                    Submission::updateOrCreate(
                        ['id' => $id],
                        [
                            'type' => $type,
                            'timestamp' => $sub['timestamp'] ?? now()->toDateTimeString(),
                            'nama' => $nama,
                            'departemen' => $departemen,
                            'jabatan' => $jabatan,
                            'jenis_perangkat' => $jenis,
                            'data' => $data,
                            'synced_to_google' => !empty($sub['syncedToGoogle']),
                        ]
                    );
                }
            }

            // Seed Mikrotik Accounts
            if (!empty($db['mikrotik']) && is_array($db['mikrotik'])) {
                foreach ($db['mikrotik'] as $m) {
                    $id = $m['id'] ?? ('mkt_' . uniqid());
                    MikrotikAccount::updateOrCreate(
                        ['id' => $id],
                        [
                            'nama_pemilik' => $m['namaPemilik'] ?? '-',
                            'tim' => $m['tim'] ?? 'Umum',
                            'jabatan' => $m['jabatan'] ?? 'Staff',
                            'username' => $m['username'] ?? 'USER',
                            'password' => $m['password'] ?? '123456',
                            'profile_hotspot' => $m['profileHotspot'] ?? 'default',
                            'rate_limit' => $m['rateLimit'] ?? '10M/10M',
                            'command_terminal' => $m['commandTerminal'] ?? '',
                            'status' => $m['status'] ?? 'aktif',
                            'catatan' => $m['catatan'] ?? '-',
                            'created_at_str' => (string)($m['createdAt'] ?? now()->toDateTimeString()),
                        ]
                    );
                }
            }
        }
    }
}
