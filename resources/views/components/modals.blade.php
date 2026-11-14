<!-- Universal Interactive Modals Component -->

<!-- 1. Modal Login / Ganti Akun -->
<div x-show="openLoginModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openLoginModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white flex items-center justify-center font-bold">
                    🔐
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Autentikasi Akun</h3>
                    <p class="text-xs text-slate-500">Masuk menggunakan kredensial akun Anda</p>
                </div>
            </div>
            <button @click="openLoginModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Login with credentials -->
        <div class="py-4">
            <form action="{{ url('/login') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Username Akun</label>
                    <input type="text" name="username" required placeholder="Contoh: superadmin / manager / ga" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition text-center">
                        Masuk ke Akun
                    </button>
                </div>
            </form>

            @auth
                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Aktif: <strong class="text-slate-800">{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold hover:underline">
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- 2. Modal Panduan DxDiag -->
<div x-show="openDxDiagModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openDxDiagModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 text-slate-800 animate-in fade-in zoom-in-95 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                    💻
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Panduan Ekspor DxDiag Windows</h3>
                    <p class="text-xs text-slate-500">Cara cepat mendapatkan spesifikasi laptop/PC</p>
                </div>
            </div>
            <button @click="openDxDiagModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="py-4 space-y-4 text-xs">
            <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="w-6 h-6 rounded-full bg-[#3A7D8A] text-white flex items-center justify-center font-bold text-xs shrink-0">1</div>
                <div>
                    <h4 class="font-bold text-slate-900">Buka DirectX Diagnostic Tool</h4>
                    <p class="text-slate-600 mt-0.5">Tekan tombol <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-[11px]">Win + R</code> di keyboard, ketik <strong class="text-teal-700 font-mono">dxdiag</strong>, lalu tekan <strong>Enter</strong>.</p>
                </div>
            </div>

            <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="w-6 h-6 rounded-full bg-[#3A7D8A] text-white flex items-center justify-center font-bold text-xs shrink-0">2</div>
                <div>
                    <h4 class="font-bold text-slate-900">Simpan Informasi (Save All Information)</h4>
                    <p class="text-slate-600 mt-0.5">Tunggu loading selesai di pojok kiri bawah, lalu klik tombol <strong class="text-teal-700">"Save All Information..."</strong> di bagian bawah jendela DxDiag.</p>
                </div>
            </div>

            <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="w-6 h-6 rounded-full bg-[#3A7D8A] text-white flex items-center justify-center font-bold text-xs shrink-0">3</div>
                <div>
                    <h4 class="font-bold text-slate-900">Drag &amp; Drop File ke Form Input PC</h4>
                    <p class="text-slate-600 mt-0.5">Simpan file <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono text-[11px]">DxDiag.txt</code> di Desktop atau folder Anda, lalu buka tab <strong>"Input PC"</strong> dan seret file tersebut ke area kotak upload. Seluruh spek (Processor, RAM, OS, GPU) akan terisi otomatis!</p>
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button @click="openDxDiagModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<!-- 3. Modal Setup Google Apps Script -->
<div x-show="openGasModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openGasModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                    ☁️
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Integrasi Google Apps Script &amp; Sheet</h3>
                    <p class="text-xs text-slate-500">Konfigurasi sinkronisasi 1:1 database online</p>
                </div>
            </div>
            <button @click="openGasModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('superadmin.config.update') }}" method="POST" class="py-4 space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Web App Exec URL (Google Apps Script)</label>
                <input type="url" name="gasUrl" value="{{ $config['gasUrl'] ?? '' }}" placeholder="https://script.google.com/macros/s/.../exec" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <span class="text-[10px] text-slate-400">URL deployment Web App Google Apps Script (Who has access: Anyone)</span>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Google Spreadsheet ID</label>
                <input type="text" name="spreadsheetId" value="{{ $config['spreadsheetId'] ?? '' }}" placeholder="1i5ukfdNkfSTBtm8DLD8OV-xAQNkgSeVPfgy8hhse8Os" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Google Drive Folder ID (Backup Foto &amp; Dokumen)</label>
                <input type="text" name="driveFolderId" value="{{ $config['driveFolderId'] ?? '' }}" placeholder="15WVc13tfoh3M4i3ANbKRy5H4fy4LCv2E" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <form action="{{ route('sync.spreadsheet') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-xs transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Sinkronkan Sekarang</span>
                    </button>
                </form>

                <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-sm transition">
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Modal Konfirmasi Hapus Data -->
<div x-show="openDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openDeleteModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-sm w-full p-6 text-slate-800 animate-in fade-in zoom-in-95 text-center">
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 font-bold text-xl">
            🗑️
        </div>
        <h3 class="font-bold text-base text-slate-900 mb-1">Konfirmasi Hapus Data</h3>
        <p class="text-xs text-slate-500 mb-4">
            Apakah Anda yakin ingin menghapus aset <strong x-text="deleteTarget.name" class="text-slate-800"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <form :action="deleteTarget.url" method="POST" class="flex gap-2.5 justify-center">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <button type="button" @click="openDeleteModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition">
                Ya, Hapus Data
            </button>
        </form>
    </div>
</div>

<!-- 5. Modal Ekspor Database ke Excel (.xlsx) -->
<div x-show="openExportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openExportModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    📗
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Ekspor Data ke Excel (.xlsx)</h3>
                    <p class="text-xs text-slate-500">Unduh data aset, akun, dan voucher ke file Microsoft Excel</p>
                </div>
            </div>
            <button @click="openExportModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="py-4 space-y-3.5 text-xs">
            @if(Auth::check() && Auth::user()->role === 'general_affair')
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 text-[11px] flex items-center gap-2">
                    <span>🔒</span>
                    <span>Mode General Affair aktif: Kolom kata sandi dan kredensial otomatis disamarkan (***TERLINDUNGI***) pada file ekspor.</span>
                </div>
            @endif

            <p class="font-bold text-slate-700">Pilih Data yang Ingin Diekspor:</p>

            <div class="grid grid-cols-1 gap-2.5">
                <!-- Option 1: All Modules -->
                <button type="button" @click="exportToExcel('all')" :disabled="isExporting" class="w-full text-left p-3.5 rounded-2xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/60 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            📊
                        </div>
                        <div>
                            <div class="font-bold text-xs text-emerald-950">Seluruh Master Database (Multi-Sheet)</div>
                            <div class="text-[10px] text-emerald-800">Termasuk Sheet PC &amp; Laptop, Sheet HP &amp; Akun Sales, serta Sheet MikroTik</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition-transform">&darr; Unduh</span>
                </button>

                <!-- Option 2: PC Only -->
                <button type="button" @click="exportToExcel('pc')" :disabled="isExporting" class="w-full text-left p-3 rounded-2xl border border-blue-200 bg-blue-50/40 hover:bg-blue-100/50 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                            💻
                        </div>
                        <div>
                            <div class="font-bold text-xs text-blue-950">Inventaris Laptop &amp; PC Workstation Saja</div>
                            <div class="text-[10px] text-blue-800">Spesifikasi hardware, DxDiag, CPU, RAM, OS, dan nama komputer</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-700 group-hover:translate-x-1 transition-transform">&darr; Unduh</span>
                </button>

                <!-- Option 3: Phones & Accounts Only -->
                <button type="button" @click="exportToExcel('akun')" :disabled="isExporting" class="w-full text-left p-3 rounded-2xl border border-amber-200 bg-amber-50/40 hover:bg-amber-100/50 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-xs shadow-xs">
                            📱
                        </div>
                        <div>
                            <div class="font-bold text-xs text-amber-950">Inventaris HP Android &amp; Akun Sales Saja</div>
                            <div class="text-[10px] text-amber-800">Data tipe smartphone, nomor WhatsApp bisnis, email, IG &amp; FB</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-amber-800 group-hover:translate-x-1 transition-transform">&darr; Unduh</span>
                </button>

                <!-- Option 4: MikroTik Only -->
                <button type="button" @click="exportToExcel('mikrotik')" :disabled="isExporting" class="w-full text-left p-3 rounded-2xl border border-teal-200 bg-teal-50/40 hover:bg-teal-100/50 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#3A7D8A] text-white font-bold flex items-center justify-center text-xs shadow-xs">
                            📶
                        </div>
                        <div>
                            <div class="font-bold text-xs text-teal-950">Daftar Akun Voucher Hotspot MikroTik Saja</div>
                            <div class="text-[10px] text-teal-800">Username, password voucher, profil tim, dan rate limit bandwidth</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-teal-700 group-hover:translate-x-1 transition-transform">&darr; Unduh</span>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div x-show="isExporting" class="pt-2 text-center text-xs font-semibold text-emerald-700 flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>Sedang menyusun dan mengunduh berkas Excel...</span>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button @click="openExportModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- 6. Modal Impor Data dari Excel (.xlsx / .xls / .csv) -->
<div x-show="openImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openImportModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 text-slate-800 animate-in fade-in zoom-in-95 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-lg">
                    📥
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Impor Data dari Excel</h3>
                    <p class="text-xs text-slate-500">Unggah berkas spreadsheet (.xlsx, .xls, .csv) secara massal</p>
                </div>
            </div>
            <button @click="openImportModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="py-4 space-y-4 text-xs">
            <!-- Download Template Banner -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h4 class="font-bold text-slate-900 text-xs">Belum punya format Excel?</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Unduh template siap pakai agar susunan kolom sesuai dengan sistem</p>
                </div>
                <button type="button" @click="downloadTemplateExcel('all')" class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-800 font-bold text-[11px] shrink-0 transition flex items-center gap-1.5 shadow-2xs">
                    <span>📄 Unduh Template</span>
                </button>
            </div>

            <!-- Upload Dropzone -->
            <div class="border-2 border-dashed border-slate-300 hover:border-[#3A7D8A] bg-slate-50/60 rounded-3xl p-6 text-center transition group relative">
                <input type="file" accept=".xlsx, .xls, .csv" @change="handleFileSelect($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-2 text-xl group-hover:scale-110 transition-transform">
                    📁
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Klik atau Seret Berkas ke Sini</h4>
                <p class="text-[11px] text-slate-500 mt-1">Mendukung format Microsoft Excel (<strong>.xlsx</strong>, <strong>.xls</strong>) dan <strong>.csv</strong></p>
            </div>

            <!-- Parsing Spinner -->
            <div x-show="isParsingFile" class="p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>Sedang menganalisis struktur lembar kerja spreadsheet...</span>
            </div>

            <!-- Error Alert -->
            <div x-show="importError" x-cloak class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs flex items-start gap-2.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="importError"></span>
            </div>

            <!-- Success Alert -->
            <div x-show="importSuccessMsg" x-cloak class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-start gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="importSuccessMsg"></span>
            </div>

            <!-- Data Preview Box -->
            <div x-show="importPreview" x-cloak class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/40 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-emerald-200/60">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h4 class="font-bold text-xs text-emerald-950" x-text="'Berkas: ' + (importPreview ? importPreview.fileName : '')"></h4>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-800" x-text="importPreview ? importPreview.fileSize : ''"></span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2.5 bg-white rounded-xl border border-emerald-100 shadow-2xs">
                        <div class="font-extrabold text-sm text-blue-700" x-text="importPreview ? importPreview.pcCount : 0"></div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-0.5">PC &amp; Laptop</div>
                    </div>
                    <div class="p-2.5 bg-white rounded-xl border border-emerald-100 shadow-2xs">
                        <div class="font-extrabold text-sm text-amber-700" x-text="importPreview ? importPreview.akunCount : 0"></div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-0.5">HP &amp; Akun</div>
                    </div>
                    <div class="p-2.5 bg-white rounded-xl border border-emerald-100 shadow-2xs">
                        <div class="font-extrabold text-sm text-teal-700" x-text="importPreview ? importPreview.mikrotikCount : 0"></div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-0.5">MikroTik</div>
                    </div>
                </div>

                <div class="text-[11px] text-emerald-900 font-medium">
                    Total: <strong x-text="importPreview ? importPreview.totalCount : 0"></strong> baris data siap dimasukkan ke database sistem.
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" @click="openImportModal = false; importPreview = null; importError = null; importSuccessMsg = null" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                Batal
            </button>
            <button type="button" @click="submitImport()" :disabled="!importPreview || isImporting" :class="(!importPreview || isImporting) ? 'opacity-50 cursor-not-allowed' : 'hover:brightness-110'" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <span x-show="!isImporting">🚀 Mulai Impor Data</span>
                <span x-show="isImporting" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Menyimpan ke Database...</span>
                </span>
            </button>
        </div>
    </div>
</div>
