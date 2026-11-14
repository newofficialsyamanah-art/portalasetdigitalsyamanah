<?php

namespace App\Services;

use App\Models\GuideBook;
use App\Models\AppSystem;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class GuideBookService
{
    /**
     * Seed official built-in guide books for all systems in Syamanah
     */
    public static function seedDefaults(): void
    {
        // Dummy auto-seeding disabled permanently by user request
        return;
        $books = [
            [
                'target_role' => 'Semua Karyawan (Umum)',
                'system_name' => 'Spesifikasi PC & DxDiag',
                'feature_name' => 'Ekspor DxDiag & Spek PC',
                'title' => 'SOP Ekspor Spesifikasi DxDiag & Pengunggahan Bukti Drive',
                'category' => 'Hardware',
                'version' => 'v2.1',
                'author' => 'Tim IT Support Syamanah',
                'summary' => 'Panduan resmi penarikan spesifikasi lengkap komputer/laptop Windows menggunakan tool bawaan DirectX Diagnostic Tool (dxdiag.exe) dan System Report macOS, penamaan file standar, dan penyimpanan bukti di Google Drive.',
                'tags' => ['hardware', 'dxdiag', 'spesifikasi', 'windows', 'macos', 'drive'],
                'is_pinned' => true,
                'document_url' => 'https://drive.google.com/drive/folders/syamanah-assets-dxdiag',
                'content' => <<<'MARKDOWN'
# SOP Ekspor Spesifikasi DxDiag & Pengunggahan Bukti Google Drive

Dokumen ini merupakan panduan standar operasional (SOP) bagi seluruh staf dan admin IT dalam mengambil data spesifikasi teknis komputer kerja (PC Desktop & Laptop) secara presisi tanpa perlu membongkar casing perangkat.

---

### A. Prosedur Ekspor DxDiag pada Sistem Operasi Windows (10 & 11)

1. **Membuka Tool DirectX Diagnostic Tool**:
   - Tekan kombinasi tombol `Windows + R` pada keyboard untuk memunculkan dialog **Run**.
   - Ketik `dxdiag` pada kolom isian, kemudian tekan **Enter** atau klik **OK**.
   - Jika muncul dialog konfirmasi *"Do you want to check if your drivers are digitally signed?"*, klik **Yes**.

2. **Menunggu Proses Pemindaian (Scan)**:
   - Perhatikan indikator progress bar hijau di sudut kiri bawah jendela DirectX Diagnostic Tool.
   - Tunggu sekitar 5–10 detik hingga progress bar menghilang, menandakan seluruh data hardware (System, Display, Sound, Input) telah terbaca lengkap.

3. **Menyimpan Berkas Laporan**:
   - Klik tombol **Save All Information...** (Simpan Semua Informasi) di bagian bawah jendela.
   - Beri nama berkas dengan format baku perusahaan:
     ```text
     DxDiag_[NamaKaryawan]_[NamaKomputer].txt
     Contoh: DxDiag_Mustafa_DEKSTOP-S99SVAT.txt
     ```
   - Simpan berkas tersebut di Desktop atau folder Dokumen Anda.

---

### B. Prosedur Ekspor Spesifikasi pada Apple macOS

1. Klik logo Apple (🍎) di sudut kiri atas layar, lalu pilih **About This Mac**.
2. Klik tombol **More Info...** kemudian gulir ke bawah dan klik **System Report...**.
3. Pada bilah menu atas, pilih **File** > **Save...** (atau tekan `Command + S`).
4. Simpan laporan dengan format teks: `MacReport_[NamaKaryawan]_[TipeMac].txt`.

---

### C. Tata Cara Pengunggahan ke Google Drive Resmi Syamanah

1. Buka folder penyimpanan Google Drive Aset Syamanah.
2. Buat folder per divisi jika belum tersedia (contoh: `Produksi/`, `Marketing/`, `Sales/`).
3. Unggah file hasil ekspor (`.txt` atau screenshot bukti Display/Audio/Input).
4. Klik kanan berkas yang baru diunggah > pilih **Share** (Bagikan) > atur akses umum menjadi **Anyone with the link can view** (Siapa saja yang memiliki tautan dapat melihat).
5. Salin tautan (Copy Link) dan tempelkan ke kolom **Display Link**, **Sound Link**, atau **Input Link** di form input aset portal.
MARKDOWN
            ],
            [
                'target_role' => 'Superadmin / Tim IT',
                'system_name' => 'ERPNext Frappe REST API',
                'feature_name' => 'Integrasi REST API Asset ERP',
                'title' => 'Panduan Integrasi Frappe REST API & Sinkronisasi Asset Management',
                'category' => 'ERP & API',
                'version' => 'v1.5',
                'author' => 'Enterprise System Architect',
                'summary' => 'Petunjuk teknis koneksi REST API dengan ERPNext kantor (syamanah4.sopwer.id), penjelasan modul Fixed Asset vs Item Master, pencegahan LinkValidationError HTTP 417, dan otorisasi token.',
                'tags' => ['erpnext', 'frappe', 'rest-api', 'fixed-asset', 'token'],
                'is_pinned' => true,
                'document_url' => 'https://syamanah4.sopwer.id/api/resource/Asset',
                'content' => <<<'MARKDOWN'
# Panduan Integrasi Frappe REST API & Sinkronisasi Asset Management

Buku panduan ini menjelaskan arsitektur komunikasi dua arah antara **Portal Aset Digital Syamanah** dan server **ERPNext Frappe** yang beralamat di `https://syamanah4.sopwer.id`.

---

### 1. Kredensial & Header Autentikasi API

Komunikasi ke ERPNext menggunakan metode autentikasi **Token-based API Key & Secret**:
```http
POST /api/resource/Asset HTTP/1.1
Host: syamanah4.sopwer.id
Authorization: token 43895e90883a899:3d8c285da00a784
Content-Type: application/json
Accept: application/json
```

> [!NOTE]
> Format header autentikasi Frappe adalah: `token {API_KEY}:{API_SECRET}` tanpa spasi di antara titik dua.

---

### 2. Perbedaan DocType: `Asset` vs `Item`

Portal ini mendukung 2 target DocType di ERPNext yang dapat dipilih pada Pusat Kontrol Superadmin:

| Parameter | DocType `Asset` (Fixed Assets) | DocType `Item` (Master Barang) |
| :--- | :--- | :--- |
| **Fungsi** | Pencatatan aktiva tetap bernilai kapital | Inventaris operasional & master logistik |
| **Depresiasi** | Memiliki kalkulasi penyusutan nilai buku | Bebas syarat depresiasi |
| **Syarat Dokumen** | Memerlukan Item dengan `is_fixed_asset=1`, `location`, dan `gross_purchase_amount` | Hanya butuh `item_code` dan `item_name` |
| **Rekomendasi** | PC Workstation bernilai di atas Rp 5.000.000 | Laptop harian, monitor, mouse, HP Sales |

---

### 3. Penanganan Error Umum & Solusi Teknis

#### A. Error `HTTP 417 LinkValidationError: Could not find Location / Department / Custodian`
- **Penyebab**: ERPNext menerapkan integritas relasi yang ketat. Nilai teks departemen atau pemegang yang dikirim tidak terdaftar di database master ERP.
- **Solusi Portal**: Sistem telah mengimplementasikan *Auto-Sanitize Link Fields*. Portal secara otomatis melakukan pra-kueri ke ERP untuk mengambil nama lokasi dan departemen yang sah sebelum payload dikirimkan.

#### B. Error `HTTP 417 ValidationError: Item must be a Fixed Asset Item`
- **Penyebab**: DocType `Asset` menolak item_code jika flag `is_fixed_asset` bernilai 0.
- **Solusi**: Gunakan pilihan target **Modul Item** di portal, atau pastikan item tersebut di ERPNext dicentang sebagai Fixed Asset.

#### C. Error `HTTP 403 Forbidden`
- **Penyebab**: Akun API belum memiliki Role permission `Asset Manager` atau `Item Manager`.
- **Solusi**: Masuk ke ERPNext sebagai Administrator > menu **Users** > centang role **Asset Manager** dan **Stock User** pada user API terkait.
MARKDOWN
            ],
            [
                'target_role' => 'Superadmin / Tim IT',
                'system_name' => 'MikroTik RouterOS & Hotspot',
                'feature_name' => 'Manajemen Voucher & Router Hotspot',
                'title' => 'Manual Konfigurasi Hotspot MikroTik, Profil Bandwidth & Script Terminal',
                'category' => 'Jaringan',
                'version' => 'v1.4',
                'author' => 'Network Administrator Syamanah',
                'summary' => 'Tata cara pembuatan akun voucher WiFi Hotspot kantor per departemen, manajemen rate-limit kecepatan (10M/10M, 20M/20M), dan eksekusi skrip otomatis di terminal RouterOS WinBox.',
                'tags' => ['mikrotik', 'routeros', 'hotspot', 'wifi', 'rate-limit', 'winbox'],
                'is_pinned' => true,
                'document_url' => 'http://192.168.88.1/webfig',
                'content' => <<<'MARKDOWN'
# Manual Konfigurasi Hotspot MikroTik, Profil Bandwidth & Script Terminal

Buku petunjuk ini menjelaskan tata cara pengoperasian gateway internet kantor Syamanah berbasis routerboard MikroTik RouterOS untuk mengontrol pemakaian kuota dan kestabilan akses WiFi staf.

---

### 1. Struktur Penamaan Akun (Username) & Profil Hotspot

Untuk memudahkan audit pemakaian bandwidth harian, penamaan akun dibagi berdasarkan kode prefix departemen:

- `SJ` : Sales Jaket (contoh: `SJ14`, `SJ22`)
- `SY` : Sales Jersey (contoh: `SY05`, `SY18`)
- `PR` : Produksi Sublim & Bordir (contoh: `PR09`)
- `MC` : Marketing Communication & Advertiser (contoh: `MC01`)
- `FN` : Finance & Accounting (contoh: `FN03`)
- `HR` : Human Resources & Legal (contoh: `HR02`)

---

### 2. Standar Alokasi Kecepatan (Rate-Limit)

| Kategori Profil | Download / Upload | Alokasi Pengguna |
| :--- | :--- | :--- |
| `sales_jaket` | `10M / 10M` | Staf Sales Chat & CRM |
| `sales_jersey` | `10M / 10M` | Staf Follow-Up Pesanan |
| `marketing_ads` | `25M / 25M` | Advertiser & Unggah Media Iklan |
| `produksi_design` | `30M / 30M` | Desain Sublim, Download File Mockup Mentah |
| `manajemen_vip` | `50M / 50M` | Direksi & Kepala Divisi |

---

### 3. Eksekusi Script Terminal di RouterOS / WinBox

Gunakan perintah terminal berikut untuk menambahkan pengguna baru secara instan:
```routeros
/ip hotspot user add name="SJ29" password="482910" profile="sales_jaket" limit-uptime=30d comment="Wilda - Sales Jaket"
```

Jika ingin memutus sesi pengguna yang bermasalah atau habis masa berlaku:
```routeros
/ip hotspot active remove [find user="SJ29"]
```

---

### 4. Prosedur Troubleshooting Koneksi
1. Pastikan IP address perangkat mendapatkan subnet yang sesuai (192.168.x.x).
2. Jika halaman login captive portal tidak muncul otomatis, buka browser dan ketik alamat: `http://hotspot.syamanah.net` atau IP gateway router `http://192.168.88.1`.
3. Masukkan username dan password 6 angka yang tercatat di portal.
MARKDOWN
            ],
            [
                'target_role' => 'Tim Sales & CRM',
                'system_name' => 'Smartphone & Akun Sales',
                'feature_name' => 'Handover HP & Akun WhatsApp Sales',
                'title' => 'SOP Manajemen Handphone Sales, Kredensial Email & 2FA Keamanan Akun',
                'category' => 'Sales & Akun',
                'version' => 'v2.0',
                'author' => 'General Affair & Digital Security',
                'summary' => 'Prosedur baku serah terima unit smartphone operasional sales, pencatatan nomor IMEI & WhatsApp, inventarisasi email akun bisnis, kebijakan sandi, serta pengamanan 2FA.',
                'tags' => ['smartphone', 'imei', 'whatsapp', 'instagram', 'facebook', '2fa', 'keamanan'],
                'is_pinned' => false,
                'document_url' => 'https://drive.google.com/drive/folders/syamanah-assets-phones',
                'content' => <<<'MARKDOWN'
# SOP Manajemen Handphone Sales, Kredensial Email & 2FA Keamanan Akun

Dokumen standar operasional prosedur penanganan aset bergerak berupa unit smartphone Android operasional sales dan seluruh aset digital yang terinstal di dalamnya (Akun WhatsApp Bisnis, Instagram, Facebook, dan Email Google).

---

### 1. Checklist Penerimaan & Pendataan Awal Unit HP

Sebelum perangkat diserahkan kepada staf sales, petugas General Affair (GA) wajib melakukan:
1. **Pemeriksaan Fisik**: Kondisi layar touchscreen, kamera, soket charger Type-C, serta baterai health.
2. **Pengecekan Nomor IMEI**:
   - Buka menu dial telepon, ketik kode: `*#06#`.
   - Catat 15 digit nomor IMEI 1 dan IMEI 2 ke dalam formulir portal.
3. **Pencatatan Nomor WhatsApp**: Pastikan kartu SIM fisik terpasang dan nomor telah terdaftar di database portal.
4. **Pengaturan Akun Google Kantor**: Wajib login menggunakan email inventaris Syamanah (bukan email pribadi staf).

---

### 2. Standar Pengelolaan Kredensial Sosial Media & Email

Setiap HP sales mengelola hingga 3 akun email dan 3 akun media sosial untuk operasional:

- **Kebijakan Sandi**:
  - Dilarang keras menggunakan tanggal lahir atau angka berurutan (`123456`).
  - Gunakan kombinasi minimal 10 karakter: Huruf besar, huruf kecil, angka, dan karakter khusus (`@`, `#`, `!`).
- **Verifikasi 2 Langkah (Two-Factor Authentication / 2FA)**:
  - Seluruh akun WhatsApp Bisnis dan Instagram wajib mengaktifkan 2FA PIN.
  - Email pemulihan diarahkan ke email admin IT Syamanah untuk mencegah terkunci saat pergantian staf (*handover*).

---

### 3. Prosedur Serah Terima (*Offboarding / Handover*)

Jika terjadi mutasi atau pergantian personel sales:
1. Staf lama wajib menyerahkan unit dalam kondisi pola PIN layar terbuka.
2. Admin GA memeriksa kesesuaian seluruh akun email dan sosial media yang tersimpan di portal.
3. Lakukan pencatatan riwayat pada kolom **"Bekas handphone siapa?"** di portal.
4. Perbarui data nama pemegang dan nomor WhatsApp baru di sistem.
MARKDOWN
            ],
            [
                'target_role' => 'Superadmin / Tim IT',
                'system_name' => 'Jaringan Kantor & Pemetaan IP',
                'feature_name' => 'Alokasi Static DHCP & IP Gateway',
                'title' => 'Standar Alokasi IP Address LAN & Manajemen Subnetting Per Divisi',
                'category' => 'Jaringan',
                'version' => 'v1.3',
                'author' => 'Network Operations Center Syamanah',
                'summary' => 'Dokumentasi resmi segmentasi alamat IP lokal (LAN / VLAN) di lingkungan kantor Syamanah untuk memisahkan lalu lintas data divisi Sales, Advertiser, Produksi, dan Finance.',
                'tags' => ['ip-address', 'subnetting', 'vlan', 'lan', 'dhcp', 'gateway'],
                'is_pinned' => false,
                'document_url' => 'http://192.168.1.1',
                'content' => <<<'MARKDOWN'
# Standar Alokasi IP Address LAN & Manajemen Subnetting Per Divisi

Untuk memastikan kinerja transmisi data yang optimal, mencegah bentrokan alamat IP (IP Conflict), serta mengisolasi lalu lintas data divisi sensitif, jaringan lokal CV Generasi Sugih Sejahtera (Syamanah) dibagi ke dalam beberapa segmen subnet `/24`:

---

### 1. Peta Alokasi Subnet Jaringan Kantor

| Segmen Subnet | Divisi / Area Kerja | Netmask | Gateway |
| :--- | :--- | :--- | :--- |
| `192.168.0.0/24` | Server Lokal & Network Attached Storage (NAS) | `255.255.255.0` | `192.168.0.1` |
| `192.168.10.0/24` | Divisi Inventory, Gudang & Logistik | `255.255.255.0` | `192.168.10.1` |
| `192.168.30.0/24` | Divisi Sales Jersey & Admin Penjualan | `255.255.255.0` | `192.168.30.1` |
| `192.168.99.0/24` | Divisi Sales Jaket & Customer Relationship Mgmt (CRM) | `255.255.255.0` | `192.168.99.1` |
| `192.168.167.0/24`| Marketing Communication, Desain Iklan & Advertiser | `255.255.255.0` | `192.168.167.1` |
| `192.168.168.0/24`| Produksi Sublim, Bordir, Cutting, Finance & Accounting | `255.255.255.0` | `192.168.168.1` |

---

### 2. Standar DNS Server & Gateway

- **Primary DNS**: `1.1.1.1` (Cloudflare High-Speed Secure DNS)
- **Secondary DNS**: `8.8.8.8` (Google Public DNS)
- **Domain Lokal**: `syamanah.lan`

---

### 3. Prosedur Reservasi IP Statis (Static DHCP Lease)

Setiap PC desktop atau workstation editing wajib didaftarkan MAC Address-nya pada router agar alamat IP tidak berpindah-pindah saat restart:

```routeros
/ip dhcp-server lease add address=192.168.167.41 mac-address=D8:BB:C1:22:A4:9F comment="Mustafa - Foto Videographer"
```

Setelah didaftarkan, catat alamat IP tersebut pada formulir data aset di portal untuk kemudahan monitoring dan kendali remote desktop (RDP / AnyDesk).
MARKDOWN
            ],
            [
                'target_role' => 'Manager & Supervisor',
                'system_name' => 'Google Spreadsheet & Apps Script',
                'feature_name' => 'Sinkronisasi Webhook GAS & Backup',
                'title' => 'SOP Sinkronisasi Otomatis Google Spreadsheet 1:1 via Google Apps Script (GAS)',
                'category' => 'Integrasi Data',
                'version' => 'v1.2',
                'author' => 'Database Administrator',
                'summary' => 'Langkah-langkah konfigurasi Google Apps Script (GAS) Web App, sinkronisasi dua arah ke Google Sheets master, penyiapan trigger otomatis, dan format ekspor multi-sheet Excel.',
                'tags' => ['google-sheets', 'apps-script', 'gas', 'sync', 'excel', 'backup'],
                'is_pinned' => false,
                'document_url' => 'https://docs.google.com/spreadsheets/d/syamanah-master-ledger',
                'content' => <<<'MARKDOWN'
# SOP Sinkronisasi Otomatis Google Spreadsheet 1:1 via Google Apps Script (GAS)

Dokumen ini menjelaskan mekanisme pencadangan (*real-time cloud backup*) data aset ke Google Spreadsheet menggunakan Google Apps Script Web App.

---

### 1. Arsitektur Komunikasi Sinkronisasi

Portal Aset Digital Syamanah dapat mengirimkan dan menarik data dari Google Sheets melalui Web App URL dengan endpoint POST:
```text
https://script.google.com/macros/s/AKfycbx.../exec
```

Data dikirimkan dalam format JSON yang dipetakan 1:1 ke baris kolom spreadsheet master.

---

### 2. Penyiapan Google Apps Script pada Spreadsheet Master

1. Buka file Google Spreadsheet master inventaris kantor di Google Drive.
2. Pada menu atas, klik **Extensions (Ekstensi)** > **Apps Script**.
3. Buat file script baru `Code.gs` dan tempelkan handler `doPost(e)`:
   ```javascript
   function doPost(e) {
     var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
     var payload = JSON.parse(e.postData.contents);
     sheet.appendRow([
       payload.timestamp,
       payload.nama,
       payload.departemen,
       payload.jabatan,
       payload.computerName,
       payload.ip,
       payload.passwordPc,
       payload.os,
       payload.processor,
       payload.memory
     ]);
     return ContentService.createTextOutput(JSON.stringify({status: 'success'})).setMimeType(ContentService.MimeType.JSON);
   }
   ```
4. Klik tombol **Deploy** di pojok kanan atas > pilih **New deployment**.
5. Pilih tipe **Web app**:
   - *Execute as*: **Me (pemilik spreadsheet)**
   - *Who has access*: **Anyone (siapa saja)**
6. Salin **Web App URL** yang dihasilkan.
7. Buka tab **Pusat Kontrol Superadmin** di portal, lalu simpan URL tersebut pada kolom **Google Apps Script Web App URL**.
MARKDOWN
            ],
            [
                'target_role' => 'Superadmin / Tim IT',
                'system_name' => 'Portal EAM & Matriks Hak Akses (RBAC)',
                'feature_name' => 'Matriks Spatie RBAC & Masking Kredensial GA',
                'title' => 'Panduan Manajemen Pengguna, Peran Spatie RBAC & Audit Kredensial',
                'category' => 'Keamanan',
                'version' => 'v1.1',
                'author' => 'Lead Security Engineer',
                'summary' => 'Tata kelola keamanan sistem, pemisahan wewenang peran (Superadmin, Manager, General Affair), penyembunyian kredensial sensitif (Password Masking), dan audit log kepatuhan aset.',
                'tags' => ['rbac', 'spatie', 'security', 'roles', 'permissions', 'masking', 'audit'],
                'is_pinned' => false,
                'document_url' => 'https://github.com/spatie/laravel-permission',
                'content' => <<<'MARKDOWN'
# Panduan Manajemen Pengguna, Peran Spatie RBAC & Audit Kredensial

Portal Aset Digital Syamanah dibangun dengan standar keamanan berbasis hak akses (*Role-Based Access Control* / RBAC) menggunakan paket industri `spatie/laravel-permission`.

---

### 1. Hierarki & Kewenangan Peran (Role)

Sistem membagi pengguna ke dalam 3 tingkatan peran utama:

1. **👑 Superadmin**:
   - Memiliki wewenang tertinggi (`Permission::all()`).
   - Dapat menambah, mengubah, dan menghapus seluruh data user.
   - Mengatur konfigurasi integrasi ERPNext dan Google Apps Script.
   - Melihat seluruh sandi akun dan mengekspor database secara utuh.

2. **👔 Manager**:
   - Memiliki hak operasional inventaris laptop, PC, HP, dan voucher MikroTik.
   - Dapat mengekspor dan mengimpor file Excel master.
   - Berhak melihat sandi PC dan email bisnis untuk keperluan pengawasan tim.

3. **🏢 General Affair (GA)**:
   - Dikhususkan untuk staf lapangan yang bertugas mendata unit laptop dan smartphone baru.
   - **Fitur Keamanan Masking Kredensial**: Seluruh tampilan sandi PC, password email, dan PIN perangkat secara otomatis ditutup dengan teks `🔒 Terlindung (GA)`.
   - Tidak memiliki akses ke tab Superadmin dan tidak dapat menghapus data pengguna.

---

### 2. Penambahan Peran & Izin Kustom

Jika kantor membutuhkan peran perantara (contoh: *IT Support* atau *Auditor External*):
1. Buka tab **👑 Superadmin** > pilih sub-tab **Matriks Hak Akses & Peran Spatie RBAC**.
2. Masukkan nama peran baru pada form **Buat Peran (Role) Baru** (contoh: `it_support`).
3. Centang izin spesifik yang dikehendaki (contoh: `manage inventory` dan `manage mikrotik` tanpa `view credentials`).
4. Klik **+ Buat Role Baru**.
MARKDOWN
            ],
        ];

        foreach ($books as $b) {
            GuideBook::updateOrCreate(
                ['title' => $b['title']],
                $b
            );
        }
    }

    /**
     * Format raw role name to human readable
     */
    public static function formatRoleName(string $raw): string
    {
        $map = [
            'superadmin' => 'Superadmin / Tim IT',
            'general_affair' => 'General Affair (GA)',
            'manager' => 'Manager & Supervisor',
        ];
        $lower = strtolower(trim($raw));
        if (isset($map[$lower])) {
            return $map[$lower];
        }
        return ucwords(str_replace('_', ' ', $raw));
    }

    /**
     * Get target roles options dynamically managed by Superadmin in Spatie RBAC / Database
     */
    public static function getTargetRoles(): array
    {
        $roles = ['Semua Karyawan (Umum)'];

        // 1. Ambil seluruh role aktif yang dikelola Superadmin di Spatie RBAC
        if (class_exists(Role::class) && Schema::hasTable('roles')) {
            try {
                $dbRoles = Role::orderBy('name')->pluck('name')->toArray();
                foreach ($dbRoles as $r) {
                    $formatted = static::formatRoleName($r);
                    if (!in_array($formatted, $roles)) {
                        $roles[] = $formatted;
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. Sertakan target_role yang sudah pernah digunakan pada dokumen agar tidak hilang
        if (Schema::hasTable('guide_books')) {
            try {
                $savedRoles = GuideBook::distinct()->pluck('target_role')->filter()->toArray();
                foreach ($savedRoles as $sr) {
                    if (!in_array($sr, $roles) && $sr !== 'Kustom / Peran Lainnya') {
                        $roles[] = $sr;
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 3. Fallback jika database roles kosong
        if (count($roles) <= 1) {
            $roles = array_merge($roles, [
                'General Affair (GA)',
                'Manager & Supervisor',
                'Superadmin / Tim IT',
                'Tim Sales & CRM',
                'Tim Marketing & Advertiser',
                'Tim Produksi Sublim & Bordir',
                'Tim Finance & Accounting',
            ]);
        }

        return array_values(array_unique($roles));
    }

    /**
     * Get systems list dynamically managed by Superadmin via AppSystem CRUD
     */
    public static function getSystems(): array
    {
        AppSystem::ensureTableExists();

        // Ambil sistem aktif secara murni dari master AppSystem yang dikelola Superadmin
        try {
            return AppSystem::where('is_active', true)
                ->orderBy('order_num')
                ->orderBy('name')
                ->pluck('name')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get system-to-features suggestions mapping dynamically from AppSystem
     */
    public static function getSystemFeaturesMap(): array
    {
        AppSystem::ensureTableExists();

        $map = [];
        try {
            $systems = AppSystem::where('is_active', true)->orderBy('order_num')->get();
            foreach ($systems as $sys) {
                $features = is_array($sys->features) ? $sys->features : [];
                $map[$sys->name] = array_values(array_filter(array_map('trim', $features)));
            }
        } catch (\Throwable $e) {}

        return $map;
    }

    /**
     * Get unique categories
     */
    public static function getCategories(): array
    {
        AppSystem::ensureTableExists();

        $sysCats = [];
        try {
            $sysCats = AppSystem::distinct()->pluck('category')->filter()->values()->toArray();
        } catch (\Throwable $e) {}

        $dbCategories = Schema::hasTable('guide_books') 
            ? GuideBook::distinct()->pluck('category')->filter()->values()->toArray() 
            : [];

        $defaults = [
            'Hardware',
            'Jaringan',
            'ERP & API',
            'Sales & Akun',
            'Integrasi Data',
            'Keamanan',
            'SOP Operasional',
            'SOP Umum',
        ];
        return array_values(array_unique(array_merge($defaults, $sysCats, $dbCategories)));
    }
}
