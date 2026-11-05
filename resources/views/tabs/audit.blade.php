<!-- Tab: Audit & Analitik Kredensial Perusahaan -->
<div x-show="activeTab === 'audit'" x-cloak class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                🛡️
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Audit Kredensial &amp; Analitik Aset</h2>
                <p class="text-xs text-slate-500 mt-0.5">Postur keamanan sandi, distribusi spesifikasi hardware, dan audit aset digital perusahaan</p>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                💻
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold">Total PC &amp; Laptop</span>
                <div class="text-2xl font-black text-slate-900 leading-none mt-1">{{ $deviceCount ?? 0 }}</div>
                <span class="text-[10px] text-emerald-600 font-bold mt-1 inline-block">100% Terdaftar</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                📱
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold">Smartphone Sales</span>
                <div class="text-2xl font-black text-slate-900 leading-none mt-1">{{ $phoneCount ?? 0 }}</div>
                <span class="text-[10px] text-amber-600 font-bold mt-1 inline-block">Aktif Operasional</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shrink-0">
                📶
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold">Hotspot MikroTik</span>
                <div class="text-2xl font-black text-slate-900 leading-none mt-1">{{ count($mikrotikAccounts) }}</div>
                <span class="text-[10px] text-teal-600 font-bold mt-1 inline-block">Voucher Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                🏢
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold">Divisi &amp; Tim</span>
                <div class="text-2xl font-black text-slate-900 leading-none mt-1">{{ count($divisions) }}</div>
                <span class="text-[10px] text-emerald-600 font-bold mt-1 inline-block">Master Terorganisir</span>
            </div>
        </div>
    </div>

    <!-- Security & Hardware Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Division Breakdown -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#3A7D8A]"></span>
                Alokasi Aset per Divisi
            </h3>
            
            <div class="space-y-3 text-xs">
                @foreach($divisions as $div)
                    @php
                        $c = $submissions->where('departemen', $div)->count();
                        $pct = $totalSubmissions > 0 ? round(($c / $totalSubmissions) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold text-slate-700">{{ $div }}</span>
                            <span class="font-mono text-slate-500">{{ $c }} Unit ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Security Posture -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Matriks Keamanan Kredensial
            </h3>

            <div class="space-y-3.5 text-xs text-slate-600">
                <div class="p-3.5 bg-emerald-50/60 rounded-2xl border border-emerald-100 flex items-start gap-3">
                    <span class="text-emerald-600 text-lg">🛡️</span>
                    <div>
                        <h4 class="font-bold text-emerald-950">Isolasi Hak Akses Role (RBAC) Aktif</h4>
                        <p class="text-[11px] text-emerald-800 mt-0.5">Role General Affair secara otomatis dibatasi dan tidak dapat melihat password PC, email perusahaan, maupun kredensial sosial media sales.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-blue-50/60 rounded-2xl border border-blue-100 flex items-start gap-3">
                    <span class="text-blue-600 text-lg">🔒</span>
                    <div>
                        <h4 class="font-bold text-blue-950">Bcrypt Password Hashing</h4>
                        <p class="text-[11px] text-blue-800 mt-0.5">Sandi akun pengguna portal dienkripsi dengan standar industri Bcrypt 12 rounds di database Laravel.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-teal-50/60 rounded-2xl border border-teal-100 flex items-start gap-3">
                    <span class="text-teal-600 text-lg">☁️</span>
                    <div>
                        <h4 class="font-bold text-teal-950">Sinkronisasi Google Spreadsheet 1:1</h4>
                        <p class="text-[11px] text-teal-800 mt-0.5">Data terhubung dengan Google Apps Script Web App untuk cadangan spreadsheet terpusat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

