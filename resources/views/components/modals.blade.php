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

<!-- 7. Modal Terpadu Integrasi & Push ERPNext (Frappe REST API) -->
<div x-show="openErpModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openErpModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-4xl w-full p-6 text-slate-800 animate-in fade-in zoom-in-95 max-h-[92vh] overflow-y-auto space-y-6">
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#3A7D8A] to-[#2E5190] text-white flex items-center justify-center font-black text-lg shadow-md shrink-0">
                    ⚡
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Integrasi &amp; Sinkronisasi ERPNext</h3>
                    <p class="text-xs text-slate-500">Konfigurasikan koneksi REST API dan kirim atau tarik data aset kantor secara langsung</p>
                </div>
            </div>
            <button type="button" @click="openErpModal = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Section 1: Konfigurasi Endpoint & Uji Koneksi -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-3 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                    <span>🔌</span>
                    <span>Pengaturan Server &amp; Token API ERPNext:</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ !empty($config['erpnextUrl']) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ !empty($config['erpnextUrl']) ? 'Terhubung: ' . parse_url($config['erpnextUrl'], PHP_URL_HOST) : 'Belum Dikonfigurasi' }}
                </span>
            </div>

            <form action="{{ route('erpnext.config') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-1">
                        <label class="block font-semibold text-slate-700 mb-1 text-[11px]">URL Server ERPNext</label>
                        <input type="text" id="erpnextUrlInput" name="erpnextUrl" value="{{ $config['erpnextUrl'] ?? '' }}" placeholder="https://syamanah4.sopwer.id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1 text-[11px]">API Key</label>
                        <input type="text" id="erpnextApiKeyInput" name="erpnextApiKey" value="{{ $config['erpnextApiKey'] ?? '43895e90883a899' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1 text-[11px]">API Secret</label>
                        <input type="password" id="erpnextApiSecretInput" name="erpnextApiSecret" value="{{ $config['erpnextApiSecret'] ?? '3d8c285da00a784' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A]">
                    </div>
                </div>

                <!-- Test Connection Feedback -->
                <div x-show="erpTestResult" x-cloak class="p-3 rounded-xl border text-xs" :class="erpTestResult && erpTestResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                    <span class="font-bold" x-text="erpTestResult && erpTestResult.success ? '✅ Koneksi Sukses: ' : '❌ Gagal: '"></span>
                    <span x-text="erpTestResult ? erpTestResult.message : ''"></span>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <button type="button" @click="testErpConnection()" :disabled="erpTesting" class="px-3.5 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 disabled:opacity-50">
                        <span x-text="erpTesting ? 'Menguji...' : '🔌 Uji Koneksi Live'"></span>
                    </button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-[#3A7D8A] text-white font-bold text-xs shadow-xs hover:bg-[#2E5190] transition">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Kirim Aset ke ERP (Push) & Tarik Data (Pull) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            
            <!-- Kirim Aset (Push) -->
            <div class="p-4 rounded-2xl border border-blue-200 bg-blue-50/30 space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-blue-100">
                        <span class="font-bold text-blue-950 flex items-center gap-1.5">
                            <span>🚀</span>
                            <span>Kirim Aset ke ERP (Push)</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">POST API</span>
                    </div>

                    <div class="space-y-2 mt-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Nama Aset</label>
                            <input type="text" x-model="erpPushForm.asset_name" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Departemen</label>
                                <input type="text" x-model="erpPushForm.department" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Pemegang</label>
                                <input type="text" x-model="erpPushForm.custodian" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Kode Item</label>
                                <input type="text" x-model="erpPushForm.item_code" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Modul Target</label>
                                <select x-model="erpDocType" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold">
                                    <option value="Item">Item (Master Barang)</option>
                                    <option value="Asset">Asset (Aset Tetap)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div x-show="erpPushResult" x-cloak class="mt-2 p-2.5 rounded-xl border text-[11px]" :class="erpPushResult && erpPushResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                        <span x-text="erpPushResult ? erpPushResult.message : ''"></span>
                    </div>
                </div>

                <button type="button" @click="pushToErp(false)" :disabled="erpPushing" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-[#2E5190] hover:brightness-105 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5 disabled:opacity-50">
                    <span x-text="erpPushing ? 'Mengirim ke ERP...' : '🚀 Kirim Sekarang ke ERP'"></span>
                </button>
            </div>

            <!-- Tarik Data Aset (Pull) -->
            <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/30 space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                        <span class="font-bold text-emerald-950 flex items-center gap-1.5">
                            <span>📥</span>
                            <span>Tarik Data dari ERPNext (Pull)</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET API</span>
                    </div>

                    <div class="space-y-2.5 mt-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">DocType ERP Target</label>
                            <select x-model="erpDocType" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold">
                                <option value="Item">Item (Master Barang)</option>
                                <option value="Asset">Asset (Aset Tetap)</option>
                                <option value="Employee">Employee (Karyawan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5 text-[11px]">Maksimal Data</label>
                            <select x-model="erpLimit" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold">
                                <option value="50">50 Data</option>
                                <option value="100">100 Data</option>
                                <option value="500">500 Data</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer text-slate-700 text-[11px] pt-1">
                            <input type="checkbox" x-model="erpAutoImport" class="rounded text-emerald-600 focus:ring-0">
                            <span>Simpan langsung ke database portal</span>
                        </label>
                    </div>

                    <div x-show="erpPullResult" x-cloak class="mt-2 p-2.5 rounded-xl border text-[11px]" :class="erpPullResult && erpPullResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                        <span x-text="erpPullResult ? (erpPullResult.count + ' aset ditemukan. ' + (erpPullResult.importedCount ? (erpPullResult.importedCount + ' disimpan!') : '')) : ''"></span>
                    </div>
                </div>

                <button type="button" @click="pullFromErp(false)" :disabled="erpPulling" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5 disabled:opacity-50">
                    <span x-text="erpPulling ? 'Menarik...' : '📥 Tarik Data Live ERP'"></span>
                </button>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="button" @click="openErpModal = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function modalPcHandler() {
        return {
            dragOver: false,
            dxdiagLoaded: false,
            showAddDivision: false,
            newDivName: '',
            isSavingDivision: false,
            fotoName: '',
            fotoPreview: null,
            displayFileName: '',
            displayFilePreview: null,
            soundFileName: '',
            soundFilePreview: null,
            inputFileName: '',
            inputFilePreview: null,
            handleAttachment(type, e) {
                const file = e.target.files[0];
                if (file) {
                    this[type + 'FileName'] = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => { this[type + 'FilePreview'] = ev.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this[type + 'FilePreview'] = null;
                    }
                }
            },
            removeAttachment(type, id) {
                this[type + 'FileName'] = '';
                this[type + 'FilePreview'] = null;
                const input = document.getElementById(id);
                if (input) input.value = '';
            },
            handleFotoSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.fotoName = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => { this.fotoPreview = ev.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.fotoPreview = null;
                    }
                }
            },
            removeFoto() {
                this.fotoName = '';
                this.fotoPreview = null;
                const input = document.getElementById('modal_pc_foto');
                if (input) input.value = '';
            },
            handleDrop(e) {
                this.dragOver = false;
                const file = e.dataTransfer.files[0];
                if (file) this.parseDxDiag(file);
            },
            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) this.parseDxDiag(file);
            },
            parseDxDiag(file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const text = event.target.result;
                    const getField = (pattern) => {
                        const match = text.match(pattern);
                        return match ? match[1].trim() : '';
                    };
                    const compName = getField(/Machine name:\s*([^\r\n]+)/i);
                    const os = getField(/Operating System:\s*([^\r\n]+)/i);
                    const mfg = getField(/System Manufacturer:\s*([^\r\n]+)/i);
                    const cpu = getField(/Processor:\s*([^\r\n]+)/i);
                    const mem = getField(/Memory:\s*([^\r\n]+)/i);

                    if (compName) { const el = document.getElementById('modal_pc_computerName'); if (el) el.value = compName; }
                    if (os) { const el = document.getElementById('modal_pc_os'); if (el) el.value = os; }
                    if (mfg) { const el = document.getElementById('modal_pc_manufacturer'); if (el) el.value = mfg; }
                    if (cpu) { const el = document.getElementById('modal_pc_processor'); if (el) el.value = cpu; }
                    if (mem) { const el = document.getElementById('modal_pc_memory'); if (el) el.value = mem; }

                    this.dxdiagLoaded = true;
                };
                reader.readAsText(file);
            },
            addNewDivision() {
                const name = this.newDivName.trim();
                if (!name) return;
                this.isSavingDivision = true;
                const tokenEl = document.querySelector('meta[name="csrf-token"]');
                const token = tokenEl ? tokenEl.content : '';

                fetch('{{ route('divisions.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
                .then(({ ok, data }) => {
                    this.isSavingDivision = false;
                    if (!ok || !data.success) {
                        alert(data.message || 'Gagal menambahkan divisi baru.');
                        return;
                    }
                    const selects = document.querySelectorAll('select[name="departemen"]');
                    selects.forEach(sel => {
                        const opt = document.createElement('option');
                        opt.value = name;
                        opt.textContent = name;
                        opt.selected = true;
                        sel.appendChild(opt);
                    });
                    this.showAddDivision = false;
                    this.newDivName = '';
                })
                .catch(() => {
                    this.isSavingDivision = false;
                    alert('Terjadi kesalahan jaringan saat menyimpan divisi baru.');
                });
            }
        };
    }

    function modalAkunHandler() {
        return {
            showAddDivision: false,
            newDivName: '',
            isSavingDivision: false,
            statusHp: 'Baru (Pengadaan Kantor)',
            fotoName: '',
            fotoPreview: null,
            accounts: [
                { platform: 'WhatsApp Bisnis', username: '', password: '' },
                { platform: 'Instagram', username: '', password: '' },
                { platform: 'Facebook Page', username: '', password: '' },
                { platform: 'Email Utama', username: '', password: '' }
            ],
            addAccount(platform = 'Instagram') {
                this.accounts.push({ platform: platform, username: '', password: '' });
            },
            removeAccount(index) {
                if (this.accounts.length > 1) {
                    this.accounts.splice(index, 1);
                }
            },
            handleFotoSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.fotoName = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => { this.fotoPreview = ev.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.fotoPreview = null;
                    }
                }
            },
            removeFoto() {
                this.fotoName = '';
                this.fotoPreview = null;
                const input = document.getElementById('modal_akun_foto');
                if (input) input.value = '';
            },
            addNewDivision() {
                const name = this.newDivName.trim();
                if (!name) return;
                this.isSavingDivision = true;
                const tokenEl = document.querySelector('meta[name="csrf-token"]');
                const token = tokenEl ? tokenEl.content : '';

                fetch('{{ route('divisions.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
                .then(({ ok, data }) => {
                    this.isSavingDivision = false;
                    if (!ok || !data.success) {
                        alert(data.message || 'Gagal menambahkan divisi baru.');
                        return;
                    }
                    const selects = document.querySelectorAll('select[name="departemen"]');
                    selects.forEach(sel => {
                        const opt = document.createElement('option');
                        opt.value = name;
                        opt.textContent = name;
                        opt.selected = true;
                        sel.appendChild(opt);
                    });
                    this.showAddDivision = false;
                    this.newDivName = '';
                })
                .catch(() => {
                    this.isSavingDivision = false;
                    alert('Terjadi kesalahan jaringan saat menyimpan divisi baru.');
                });
            }
        };
    }
</script>

<!-- 8. Modal Pop-up Input Laptop & PC Workstation -->
<div x-show="openPcModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openPcModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-3xl w-full p-5 sm:p-6 text-slate-800 animate-in fade-in zoom-in-95 max-h-[92vh] overflow-y-auto space-y-5" x-data="modalPcHandler()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shadow-2xs shrink-0">
                    💻
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900 leading-tight">Formulir Input Laptop &amp; PC Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tambah data spesifikasi laptop/PC atau seret file DxDiag.txt untuk auto-fill</p>
                </div>
            </div>
            <button type="button" @click="openPcModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition cursor-pointer" title="Tutup Formulir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Drag & Drop DxDiag File Box -->
        <div class="bg-gradient-to-r from-blue-50/60 to-indigo-50/60 rounded-2xl p-4 sm:p-5 border-2 border-dashed border-blue-300 text-center space-y-1.5 cursor-pointer hover:border-blue-500 transition relative"
             @dragover.prevent="dragOver = true"
             @dragleave.prevent="dragOver = false"
             @drop.prevent="handleDrop($event)">
            <input type="file" accept=".txt" @change="handleFileSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
            <div class="w-9 h-9 rounded-xl bg-blue-500 text-white flex items-center justify-center mx-auto text-base shadow-sm">
                ⚡
            </div>
            <h4 class="font-bold text-xs text-slate-900">Seret &amp; Lepas File DxDiag.txt di Sini (Auto-Fill)</h4>
            <p class="text-[11px] text-slate-500 max-w-md mx-auto">
                Sistem akan secara instan membaca Processor, RAM, OS, Manufaktur, dan Nama Komputer tanpa perlu ketik manual.
            </p>
            <span x-show="dxdiagLoaded" x-cloak class="inline-block mt-1 px-3 py-0.5 bg-emerald-500 text-white font-bold text-[10px] rounded-full shadow-sm animate-pulse">
                ✓ Spesifikasi DxDiag Berhasil Diekstrak ke Kolom Input!
            </span>
        </div>

        <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf
            <input type="hidden" name="type" value="pc">

            <!-- Section 1: Identitas -->
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-1.5 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#3A7D8A]"></span>
                    1. Identitas Pengguna &amp; Divisi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pemegang / Pengguna <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: Wilda" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Divisi / Tim <span class="text-rose-500">*</span></label>
                            <button type="button" @click="showAddDivision = !showAddDivision" class="text-[10px] font-bold text-teal-700 hover:underline cursor-pointer">
                                + Tambah Tim Baru
                            </button>
                        </div>
                        <div x-show="!showAddDivision">
                            <select name="departemen" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                                @foreach($divisions ?? ['Sales Jaket', 'Sales Gamis', 'CS', 'Advertiser', 'Gudang', 'Office'] as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="showAddDivision" x-cloak class="flex gap-1.5 items-center">
                            <input type="text" x-model="newDivName" @keydown.enter.prevent="addNewDivision()" placeholder="Nama divisi baru..." class="flex-1 px-3 py-1.5 rounded-xl border border-teal-300 text-xs focus:outline-hidden">
                            <button type="button" @click="addNewDivision()" :disabled="isSavingDivision" class="px-3 py-1.5 bg-[#3A7D8A] hover:bg-[#2E5190] text-white rounded-xl font-bold text-xs disabled:opacity-50 flex items-center gap-1 cursor-pointer">
                                <span x-show="!isSavingDivision">Simpan</span>
                                <span x-show="isSavingDivision" x-cloak>...</span>
                            </button>
                            <button type="button" @click="showAddDivision = false" class="px-2 py-1.5 bg-slate-200 text-slate-700 rounded-xl text-xs cursor-pointer">Batal</button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" placeholder="Contoh: Staff CRM / Desainer" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Hardware -->
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-1.5 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    2. Spesifikasi Hardware &amp; Sistem Operasi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jenis Perangkat</label>
                        <select name="jenisPerangkat" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                            <option value="Laptop">Laptop Workstation</option>
                            <option value="PC Desktop">PC Desktop / Rakitan</option>
                            <option value="All-in-One">PC All-in-One</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Komputer (Computer Name)</label>
                        <input type="text" id="modal_pc_computerName" name="computerName" placeholder="Contoh: DESKTOP-D0N0ANQ" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Brand / Manufaktur</label>
                        <input type="text" id="modal_pc_manufacturer" name="manufacturer" placeholder="Contoh: Asus, Lenovo, Gigabyte" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Processor (CPU)</label>
                        <input type="text" id="modal_pc_processor" name="processor" placeholder="Contoh: Intel Core i5-12400F" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kapasitas RAM</label>
                        <input type="text" id="modal_pc_memory" name="memory" placeholder="Contoh: 16 GB DDR4" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Sistem Operasi (OS)</label>
                        <input type="text" id="modal_pc_os" name="os" placeholder="Contoh: Windows 11 Pro 64-bit" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 3: Jaringan, Kredensial & Lampiran Bukti DxDiag -->
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-1.5 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    3. Jaringan, Perangkat Lunak &amp; Lampiran Bukti DxDiag
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat IP Komputer</label>
                        <input type="text" name="ip" placeholder="Contoh: 192.168.99.79" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Windows / PC</label>
                        <input type="text" name="passwordPc" placeholder="Tulis '-' jika tidak ada sandi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan / Software Khusus</label>
                        <input type="text" name="catatan" placeholder="Contoh: Adobe CC, CorelDRAW, Office 2021" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <!-- 3 Dedicated Attachment Boxes: Bukti Display, Bukti Sound, Bukti Input -->
                    <div class="md:col-span-3 pt-2">
                        <label class="block font-semibold text-slate-800 mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                                <span>📎</span>
                                <span>Lampiran File Bukti DxDiag (Display, Sound &amp; Input)</span>
                            </span>
                            <span class="text-[10px] text-teal-700 font-normal">Format: Foto/Screenshot (PNG/JPG) atau PDF (Maks 5 MB)</span>
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- 1. Bukti Display (GPU) -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-3 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[145px]">
                                <input type="file" id="modal_pc_file_display" name="file_display" accept="image/*,.pdf" @change="handleAttachment('display', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>🖥️</span> Bukti Display (GPU)
                                    </span>
                                    <span class="text-[9px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded-full">Display</span>
                                </div>

                                <div x-show="!displayFilePreview && !displayFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Display</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Display DxDiag</div>
                                </div>

                                <div x-show="displayFilePreview" x-cloak class="my-auto">
                                    <img :src="displayFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="displayFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('display', 'modal_pc_file_display')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!displayFilePreview && displayFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="displayFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('display', 'modal_pc_file_display')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Bukti Sound (Audio) -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-purple-500 rounded-2xl p-3 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[145px]">
                                <input type="file" id="modal_pc_file_sound" name="file_sound" accept="image/*,.pdf" @change="handleAttachment('sound', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>🔊</span> Bukti Sound (Audio)
                                    </span>
                                    <span class="text-[9px] bg-purple-100 text-purple-700 font-bold px-1.5 py-0.5 rounded-full">Sound</span>
                                </div>

                                <div x-show="!soundFilePreview && !soundFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Sound</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Sound DxDiag</div>
                                </div>

                                <div x-show="soundFilePreview" x-cloak class="my-auto">
                                    <img :src="soundFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="soundFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('sound', 'modal_pc_file_sound')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!soundFilePreview && soundFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="soundFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('sound', 'modal_pc_file_sound')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Bukti Input Devices -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-3 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[145px]">
                                <input type="file" id="modal_pc_file_input" name="file_input" accept="image/*,.pdf" @change="handleAttachment('input', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>⌨️</span> Bukti Input Devices
                                    </span>
                                    <span class="text-[9px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded-full">Input</span>
                                </div>

                                <div x-show="!inputFilePreview && !inputFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Input</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Input DxDiag</div>
                                </div>

                                <div x-show="inputFilePreview" x-cloak class="my-auto">
                                    <img :src="inputFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="inputFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('input', 'modal_pc_file_input')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!inputFilePreview && inputFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="inputFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('input', 'modal_pc_file_input')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Fisik Laptop / PC (Opsional) -->
                    <div class="md:col-span-3 pt-1">
                        <label class="block font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                <span>📷</span> Foto Fisik Unit / Kelengkapan (Opsional)
                            </span>
                            <span class="text-[10px] text-slate-400 font-normal">Foto unit laptop/PC tampak depan/stiker</span>
                        </label>
                        <div class="border border-dashed border-slate-300 hover:border-[#3A7D8A] rounded-xl p-3 bg-white text-center relative transition group cursor-pointer">
                            <input type="file" id="modal_pc_foto" name="foto" accept="image/*,.pdf" @change="handleFotoSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                            
                            <div x-show="!fotoPreview && !fotoName" class="py-1">
                                <span class="text-xs font-semibold text-slate-600">📷 Pilih atau seret foto fisik laptop / PC ke sini</span>
                                <span class="text-[10px] text-slate-400 ml-2">JPG, PNG, PDF (Maksimal 5 MB)</span>
                            </div>

                            <div x-show="fotoPreview" x-cloak class="flex items-center justify-between gap-3 p-1.5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center gap-2">
                                    <img :src="fotoPreview" class="w-8 h-8 object-cover rounded border border-slate-200 shadow-2xs">
                                    <div class="text-left font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="text-rose-500 hover:bg-rose-50 px-2 py-0.5 rounded text-xs font-bold z-20 cursor-pointer">Hapus</button>
                            </div>

                            <div x-show="!fotoPreview && fotoName" x-cloak class="flex items-center justify-between gap-3 p-1.5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center gap-2 text-left">
                                    <span class="text-base">📄</span>
                                    <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="text-rose-500 hover:bg-rose-50 px-2 py-0.5 rounded text-xs font-bold z-20 cursor-pointer">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="openPcModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <span>💾 Simpan Inventaris PC</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 9. Modal Pop-up Input Smartphone & Akun Sales -->
<div x-show="openAkunModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openAkunModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-3xl w-full p-5 sm:p-6 text-slate-800 animate-in fade-in zoom-in-95 max-h-[92vh] overflow-y-auto space-y-5" x-data="modalAkunHandler()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-2xs shrink-0">
                    📱
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900 leading-tight">Formulir Input Smartphone &amp; Akun Sales Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tambah data HP operasional tim sales, nomor WA bisnis, email &amp; media sosial</p>
                </div>
            </div>
            <button type="button" @click="openAkunModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition cursor-pointer" title="Tutup Formulir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf
            <input type="hidden" name="type" value="akun">

            <!-- Section 1: Identitas Sales & Tim -->
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-1.5 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    1. Identitas Sales &amp; Divisi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Sales / Pemegang HP <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: Teh Silvi / Kang Ridwan" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Divisi / Tim <span class="text-rose-500">*</span></label>
                            <button type="button" @click="showAddDivision = !showAddDivision" class="text-[10px] font-bold text-teal-700 hover:underline cursor-pointer">
                                + Tambah Tim Baru
                            </button>
                        </div>
                        <div x-show="!showAddDivision">
                            <select name="departemen" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                                @foreach($divisions ?? ['Sales Jaket', 'Sales Gamis', 'CS', 'Advertiser', 'Gudang', 'Office'] as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="showAddDivision" x-cloak class="flex gap-1.5 items-center">
                            <input type="text" x-model="newDivName" @keydown.enter.prevent="addNewDivision()" placeholder="Nama divisi baru..." class="flex-1 px-3 py-1.5 rounded-xl border border-teal-300 text-xs focus:outline-hidden">
                            <button type="button" @click="addNewDivision()" :disabled="isSavingDivision" class="px-3 py-1.5 bg-[#3A7D8A] hover:bg-[#2E5190] text-white rounded-xl font-bold text-xs disabled:opacity-50 flex items-center gap-1 cursor-pointer">
                                <span x-show="!isSavingDivision">Simpan</span>
                                <span x-show="isSavingDivision" x-cloak>...</span>
                            </button>
                            <button type="button" @click="showAddDivision = false" class="px-2 py-1.5 bg-slate-200 text-slate-700 rounded-xl text-xs cursor-pointer">Batal</button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Bisnis <span class="text-rose-500">*</span></label>
                        <input type="text" name="noWa" required placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: Fisik Smartphone -->
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 pb-1.5 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    2. Data Fisik Perangkat Smartphone
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tipe Smartphone &amp; RAM/Storage</label>
                        <input type="text" name="jenisHp" placeholder="Contoh: Xiaomi Redmi Note 13 8GB/256GB" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Pengadaan HP</label>
                        <select name="bekasHp" x-model="statusHp" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                            <option value="Baru (Pengadaan Kantor)">Baru (Pengadaan Kantor)</option>
                            <option value="Bekas (Eks Sales Lain)">Bekas (Eks Sales Lain)</option>
                            <option value="Perangkat Pribadi / Reimburse">Perangkat Pribadi / Reimburse</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">PIN / Pola Kunci Layar HP</label>
                        <input type="text" name="pinPolaHp" placeholder="Contoh: 123456 / Pola L" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <!-- Input Bekas Siapa (Muncul Otomatis Jika Status HP Bekas) -->
                    <div x-show="statusHp.includes('Bekas')" x-cloak class="md:col-span-3 p-3.5 rounded-2xl bg-amber-50/90 border border-amber-300 space-y-1.5 transition-all">
                        <label class="block font-bold text-amber-950 text-xs flex items-center gap-1.5">
                            <span>👤</span>
                            <span>Nama Pemegang Sebelumnya (Bekas Siapa?)</span>
                            <span class="text-rose-600 font-extrabold">*</span>
                        </label>
                        <input type="text" name="bekasSiapa" :required="statusHp.includes('Bekas')" placeholder="Contoh: Teh Silvi (Eks CRM Jaket) / Kang Asep" class="w-full px-3.5 py-2 rounded-xl border border-amber-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <span class="text-[10px] text-amber-800 font-medium">Wajib diisi jika perangkat berstatus bekas agar riwayat mutasi aset terlacak dengan jelas.</span>
                    </div>

                    <!-- File Attachment Upload: Foto Fisik HP -->
                    <div class="md:col-span-3 pt-1">
                        <label class="block font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Foto Fisik Smartphone / Kondisi Unit (Attachment File)</span>
                            <span class="text-[10px] text-amber-700 font-normal">Mendukung format gambar &amp; PDF</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-4 bg-slate-50/60 text-center relative transition group cursor-pointer">
                            <input type="file" id="modal_akun_foto" name="foto" accept="image/*,.pdf" @change="handleFotoSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                            
                            <div x-show="!fotoPreview && !fotoName" class="space-y-1 py-1">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-base group-hover:scale-110 transition-transform">
                                    📷
                                </div>
                                <div class="font-bold text-slate-700 text-xs">Pilih atau Seret Foto Fisik HP ke Sini</div>
                                <div class="text-[10px] text-slate-400">JPG, PNG, WEBP, atau berkas PDF (Maksimal 5 MB)</div>
                            </div>

                            <div x-show="fotoPreview" x-cloak class="flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-slate-200">
                                <div class="flex items-center gap-3">
                                    <img :src="fotoPreview" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                    <div class="text-left">
                                        <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">✓ Foto siap diunggah</div>
                                    </div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 text-xs font-bold z-20 cursor-pointer">
                                    Hapus
                                </button>
                            </div>

                            <div x-show="!fotoPreview && fotoName" x-cloak class="flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-slate-200">
                                <div class="flex items-center gap-2 text-left">
                                    <span class="text-xl">📄</span>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">✓ Berkas siap diunggah</div>
                                    </div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 text-xs font-bold z-20 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Akun Email & Media Sosial (Dinamis dengan Tombol Tambah Akun) -->
            <div>
                <div class="flex items-center justify-between pb-1.5 border-b border-slate-100 mb-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        3. Kredensial Akun Digital Sales &amp; Media Sosial
                    </h4>
                    <button type="button" @click="addAccount('Instagram')" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <span>➕ Tambah Akun</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(acc, index) in accounts" :key="index">
                        <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2 relative group hover:border-amber-300 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span x-text="'Akun #' + (index + 1) + ' (' + acc.platform + ')'"></span>
                                </span>
                                <button type="button" @click="removeAccount(index)" class="text-rose-500 hover:text-rose-700 font-bold text-xs flex items-center gap-1 cursor-pointer" x-show="accounts.length > 1" title="Hapus Akun Ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Platform / Layanan</label>
                                    <select :name="'accounts[' + index + '][platform]'" x-model="acc.platform" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A]">
                                        <option value="WhatsApp Bisnis">WhatsApp Bisnis</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="Facebook Page">Facebook Page</option>
                                        <option value="Email Utama">Email Utama</option>
                                        <option value="TikTok Shop">TikTok Shop</option>
                                        <option value="Shopee">Shopee</option>
                                        <option value="Tokopedia">Tokopedia</option>
                                        <option value="Telegram">Telegram</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Username / Email / ID</label>
                                    <input type="text" :name="'accounts[' + index + '][username]'" x-model="acc.username" placeholder="Contoh: @sales_jaket" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Password / PIN</label>
                                    <input type="text" :name="'accounts[' + index + '][password]'" x-model="acc.password" placeholder="••••••••" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A]">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Quick Add Platform Buttons -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                        <span class="text-slate-400 font-semibold mr-1 text-[11px]">Tambah Cepat:</span>
                        <button type="button" @click="addAccount('Instagram')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Instagram</button>
                        <button type="button" @click="addAccount('TikTok Shop')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ TikTok</button>
                        <button type="button" @click="addAccount('Facebook Page')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Facebook</button>
                        <button type="button" @click="addAccount('Email Utama')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Email</button>
                        <button type="button" @click="addAccount('Shopee')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Shopee</button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="openAkunModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <span>💾 Simpan Inventaris HP</span>
                </button>
            </div>
        </form>
    </div>
</div>
