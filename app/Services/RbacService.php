<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RbacService
{
    /**
     * Definisi lengkap 13 izin granular RBAC sistem Enterprise Asset Management
     */
    public static function getPermissionsDefinition(): array
    {
        return [
            // 1. Modul Inventaris & Perangkat
            'manage inventory' => [
                'title' => 'Kelola Inventaris Laptop, PC & HP',
                'desc' => 'Dapat melakukan input spesifikasi, edit data aset, dan import spesifikasi perangkat.',
                'icon' => '💻',
                'category' => 'Inventaris Perangkat'
            ],

            // 2. Keamanan & Kredensial
            'view credentials' => [
                'title' => 'Melihat Kredensial & Sandi Akun',
                'desc' => 'Dapat melihat password email perusahaan, media sosial, dan akun rahasia di tab Audit & HP.',
                'icon' => '👁️',
                'category' => 'Keamanan & Kredensial'
            ],
            'delete records' => [
                'title' => 'Hapus Data & Submisi Aset',
                'desc' => 'Wewenang menghapus baris data PC, laptop, smartphone, dan akun voucher secara permanen.',
                'icon' => '🗑️',
                'category' => 'Keamanan & Kredensial'
            ],

            // 3. Jaringan & Hotspot
            'manage mikrotik' => [
                'title' => 'Kelola Hotspot MikroTik',
                'desc' => 'Generate voucher hotspot, kelola limit bandwidth, dan buat user profile internet.',
                'icon' => '📶',
                'category' => 'Jaringan & Hotspot'
            ],

            // 4. Dokumentasi & SOP
            'view guidebooks' => [
                'title' => 'Melihat Guide Book & SOP Sistem (View-Only)',
                'desc' => 'Dapat membuka tab panduan, mencari SOP, dan membaca berkas dokumen PDF resmi tanpa izin mengedit/menghapus.',
                'icon' => '📖',
                'category' => 'Dokumentasi & SOP'
            ],
            'manage guidebooks' => [
                'title' => 'Kelola Guide Book & SOP Sistem',
                'desc' => 'Dapat membuat, mengunggah PDF, mengubah metadata/isi, dan menghapus dokumen Guide Book DRM.',
                'icon' => '📚',
                'category' => 'Dokumentasi & SOP'
            ],

            // 5. Master Superadmin
            'manage systems' => [
                'title' => 'Kelola Master Sistem & Fitur',
                'desc' => 'Menambah, mengubah, mengatur urutan, dan menghapus master sistem serta fitur terdaftar.',
                'icon' => '🖥️',
                'category' => 'Master Superadmin'
            ],
            'manage divisions' => [
                'title' => 'Kelola Master Divisi / Tim',
                'desc' => 'Menambah, mengubah, dan menghapus master divisi dan tim kerja operasional kantor.',
                'icon' => '🏢',
                'category' => 'Master Superadmin'
            ],

            // 6. Pusat Kontrol Akun
            'manage users' => [
                'title' => 'Kelola Pengguna & Akun',
                'desc' => 'Mendaftarkan pengguna baru, mengubah profil, mengganti peran (role), dan mereset kata sandi.',
                'icon' => '👥',
                'category' => 'Pusat Kontrol Akun'
            ],
            'manage rbac' => [
                'title' => 'Kelola Hak Akses & Peran (RBAC)',
                'desc' => 'Membuat peran (role) baru, mengatur matriks perizinan Spatie, dan menghapus peran kustom.',
                'icon' => '🛡️',
                'category' => 'Pusat Kontrol Akun'
            ],

            // 7. Integrasi Sistem & Olah Data
            'manage erpnext' => [
                'title' => 'Kelola Integrasi ERPNext',
                'desc' => 'Mengatur token API Frappe, menguji koneksi, serta sinkronisasi aset ke ERPNext kantor.',
                'icon' => '⚡',
                'category' => 'Integrasi Sistem'
            ],
            'manage spreadsheet' => [
                'title' => 'Kelola Integrasi Google Apps Script',
                'desc' => 'Mengonfigurasi URL Webhook Google Apps Script dan Folder ID Google Drive.',
                'icon' => '⚙️',
                'category' => 'Integrasi Sistem'
            ],
            'manage excel' => [
                'title' => 'Ekspor & Impor Data Excel',
                'desc' => 'Dapat mengekspor master database (.xlsx) dan mengunggah berkas massal (.xlsx / .csv).',
                'icon' => '📗',
                'category' => 'Olah Data'
            ],
        ];
    }

    /**
     * Memastikan seluruh izin (permissions) dan peran inti Spatie terdaftar otomatis di database
     */
    public static function ensurePermissionsExist(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        try {
            // 1. Reset cached roles and permissions
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // 2. Daftarkan 13 izin granular
            $definitions = static::getPermissionsDefinition();
            foreach (array_keys($definitions) as $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }

            // 3. Pastikan peran sistem inti tersedia
            $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
            $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
            $generalAffair = Role::firstOrCreate(['name' => 'general_affair', 'guard_name' => 'web']);

            // 4. Superadmin selalu memiliki SEMUA izin (Bypass Master)
            $allPerms = Permission::all();
            $superadmin->syncPermissions($allPerms);

            // 5. Manager memiliki izin manajerial & operasional (termasuk melihat & kelola guide book)
            if ($manager->permissions()->count() === 0) {
                $manager->syncPermissions([
                    'manage inventory',
                    'view credentials',
                    'manage mikrotik',
                    'view guidebooks',
                    'manage guidebooks',
                    'manage systems',
                    'manage divisions',
                    'manage excel',
                    'delete records',
                ]);
            } else {
                if (!$manager->hasPermissionTo('view guidebooks')) {
                    $manager->givePermissionTo('view guidebooks');
                }
            }

            // 6. General Affair memiliki izin operasional inventaris & melihat panduan (view-only)
            if ($generalAffair->permissions()->count() === 0) {
                $generalAffair->syncPermissions([
                    'manage inventory',
                    'view guidebooks',
                ]);
            } else {
                if (!$generalAffair->hasPermissionTo('view guidebooks')) {
                    $generalAffair->givePermissionTo('view guidebooks');
                }
            }

            // 7. Sinkronkan roles user yang belum sinkron
            $users = User::all();
            foreach ($users as $user) {
                if ($user->role && !$user->hasRole($user->role)) {
                    $user->syncRoles([$user->role]);
                }
            }

            // Reset cache kembali
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // Fail safely jika tabel belum termigrasi
        }
    }
}
