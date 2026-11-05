<!-- Universal Interactive Modals Component -->

<!-- 1. Modal Login / Ganti Role -->
<div x-show="openLoginModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
    <div @click.away="openLoginModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white flex items-center justify-center font-bold">
                    🔐
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Autentikasi &amp; Ganti Role</h3>
                    <p class="text-xs text-slate-500">Pilih peran akses atau login manual</p>
                </div>
            </div>
            <button @click="openLoginModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Quick 1-Click Role Switch -->
        <div class="py-4 space-y-2.5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilih Cepat Peran (Bypass Demo / Internal):</p>
            
            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="superadmin">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-amber-200 bg-amber-50/50 hover:bg-amber-100/60 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-xs">👑</div>
                        <div>
                            <div class="font-bold text-xs text-amber-950">Superadmin</div>
                            <div class="text-[10px] text-amber-800">Akses penuh CRUD seluruh modul &amp; divisi</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-amber-700 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>

            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="manager">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-blue-200 bg-blue-50/50 hover:bg-blue-100/60 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-xs">👔</div>
                        <div>
                            <div class="font-bold text-xs text-blue-950">Manager Operasional</div>
                            <div class="text-[10px] text-blue-800">Lihat semua akun, inventaris &amp; kredensial</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-700 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>

            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="general_affair">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-teal-200 bg-teal-50/50 hover:bg-teal-100/60 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#3A7D8A] text-white font-bold flex items-center justify-center text-xs">🏢</div>
                        <div>
                            <div class="font-bold text-xs text-teal-950">General Affair (GA)</div>
                            <div class="text-[10px] text-teal-800">Spesifikasi fisik hardware, sandi terlindungi</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-teal-700 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>
        </div>

        <!-- Or Login with credentials -->
        <div class="pt-3 border-t border-slate-100">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5">Atau Masuk dengan Kredensial:</p>
            <form action="{{ url('/login') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                    <input type="text" name="username" required placeholder="superadmin / manager / ga" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition">
                    Masuk ke Sistem
                </button>
            </form>
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

