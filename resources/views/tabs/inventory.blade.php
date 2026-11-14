<!-- Tab: Laptop & PC Workstation Inventory -->
<div x-show="activeTab === 'inventory'" x-cloak class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                💻
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Inventaris Laptop &amp; PC Workstation</h2>
                <p class="text-xs text-slate-500 mt-0.5">Katalog perangkat kerja kantor, spesifikasi hardware, OS, dan status penggunaan</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Search -->
            <div class="relative flex-1 sm:w-64">
                <input type="text" x-model="searchTerm" placeholder="Cari nama, IP, CPU, OS, host..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Division Filter -->
            <select x-model="filterDept" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">Semua Divisi ({{ count($divisions) }})</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>

            <a href="{{ route('inventory.seed-csv') }}" class="px-3 py-2 rounded-xl border border-teal-200 bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Muat / Sinkronkan Ulang 73 Data Perangkat CSV">
                <span>🔄 Sync Data (73)</span>
            </a>

            <button type="button" @click="exportToExcel('pc')" class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Ekspor Data PC & Laptop ke Excel">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Ekspor PC</span>
            </button>

            <button type="button" @click="openImportModal = true" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Impor Data Excel">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Impor</span>
            </button>

            <button @click="setTab('pc')" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <span>+ Input PC Baru</span>
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
            $pcList = $submissions->filter(function($s) {
                return $s->type === 'pc' || str_contains(strtolower($s->jenis_perangkat ?? ''), 'laptop') || str_contains(strtolower($s->jenis_perangkat ?? ''), 'pc');
            });
        @endphp

        @forelse($pcList as $pc)
            <div x-show="(filterDept === 'all' || '{{ $pc->departemen }}' === filterDept) && (searchTerm === '' || '{{ strtolower($pc->nama . ' ' . $pc->departemen . ' ' . ($pc->data['processor'] ?? '') . ' ' . ($pc->data['computerName'] ?? '') . ' ' . ($pc->data['ip'] ?? '') . ' ' . ($pc->data['os'] ?? '')) }}'.includes(searchTerm.toLowerCase()))" 
                 x-data="{ showPass: false, showLinks: false }"
                 class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition flex flex-col justify-between space-y-4">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base shrink-0">
                                {{ str_contains(strtolower($pc->jenis_perangkat ?? ''), 'laptop') ? '💻' : '🖥️' }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900 leading-tight">{{ $pc->nama }}</h3>
                                <p class="text-[11px] text-slate-400">{{ $pc->jabatan ?: 'Staff' }} &middot; <span class="font-semibold text-slate-600">{{ $pc->departemen }}</span></p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            {{ $pc->jenis_perangkat ?: 'Laptop' }}
                        </span>
                    </div>

                    <!-- Network & Host Info Banner -->
                    <div class="mt-3 p-2.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs font-mono">
                        <div class="flex items-center gap-1.5 truncate">
                            <span class="text-teal-600 font-bold">🌐 IP:</span>
                            <span class="font-bold text-slate-800 truncate" title="{{ $pc->data['ip'] ?? '-' }}">{{ $pc->data['ip'] ?? '-' }}</span>
                        </div>
                        @if(!empty($pc->data['ip']) && $pc->data['ip'] !== '-')
                            <button type="button" @click="copyToClipboard('{{ $pc->data['ip'] }}', 'ip_{{ $pc->id }}')" class="p-1 text-slate-400 hover:text-teal-700 transition shrink-0" title="Salin Alamat IP">
                                <span x-show="copiedKey !== 'ip_{{ $pc->id }}'">📋</span>
                                <span x-show="copiedKey === 'ip_{{ $pc->id }}'" x-cloak class="text-[10px] font-sans text-emerald-600 font-bold">Tersalin!</span>
                            </button>
                        @endif
                    </div>

                    <!-- Specs Breakdown -->
                    <div class="py-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Komputer / Host:</span>
                            <span class="font-mono font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['computerName'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Sistem Operasi (OS):</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]" title="{{ $pc->data['os'] ?? '-' }}">
                                {{ str_contains(strtolower($pc->data['os'] ?? ''), 'mac') ? '🍎' : '🪟' }} {{ $pc->data['os'] ?? '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Processor:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]" title="{{ $pc->data['processor'] ?? '-' }}">{{ $pc->data['processor'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">RAM:</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $pc->data['memory'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Manufaktur:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['manufacturer'] ?? '-' }}</span>
                        </div>

                        <!-- Password PC (Masked for GA) -->
                        <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-50">
                            <span class="text-slate-400">Sandi PC:</span>
                            @if(Auth::check() && Auth::user()->role === 'general_affair')
                                <span class="italic text-slate-400 text-[11px]">🔒 Terlindung</span>
                            @else
                                <div class="flex items-center gap-1.5 font-mono">
                                    <span x-show="!showPass" class="text-slate-500">••••••••</span>
                                    <span x-show="showPass" x-cloak class="font-bold text-amber-800">{{ $pc->data['passwordPc'] ?? '-' }}</span>
                                    <button type="button" @click="showPass = !showPass" class="text-slate-400 hover:text-slate-700 p-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- DxDiag Links Section -->
                    @php
                        $hasLinks = (!empty($pc->data['displayLink']) && $pc->data['displayLink'] !== '-') ||
                                    (!empty($pc->data['soundLink']) && $pc->data['soundLink'] !== '-') ||
                                    (!empty($pc->data['inputLink']) && $pc->data['inputLink'] !== '-');
                    @endphp
                    @if($hasLinks)
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button" @click="showLinks = !showLinks" class="w-full py-1 px-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-[11px] font-semibold text-slate-600 flex items-center justify-between transition">
                                <span class="flex items-center gap-1.5">
                                    <span>📄 Bukti DxDiag &amp; Spesifikasi Lengkap</span>
                                </span>
                                <span x-text="showLinks ? '▲' : '▼'" class="text-[10px] text-slate-400"></span>
                            </button>

                            <div x-show="showLinks" x-cloak class="mt-2 p-2 bg-slate-50/70 rounded-xl space-y-1.5 text-[11px]">
                                @if(!empty($pc->data['displayLink']) && $pc->data['displayLink'] !== '-')
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Display / GPU:</span>
                                        <a href="{{ $pc->data['displayLink'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 font-semibold underline flex items-center gap-0.5">
                                            <span>Buka Drive ↗</span>
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($pc->data['soundLink']) && $pc->data['soundLink'] !== '-')
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Audio / Sound:</span>
                                        <a href="{{ $pc->data['soundLink'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 font-semibold underline flex items-center gap-0.5">
                                            <span>Buka Drive ↗</span>
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($pc->data['inputLink']) && $pc->data['inputLink'] !== '-')
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Input Devices:</span>
                                        <a href="{{ $pc->data['inputLink'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 font-semibold underline flex items-center gap-0.5">
                                            <span>Buka Drive ↗</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Card -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400 font-mono">{{ $pc->timestamp }}</span>
                    <div class="flex items-center gap-2.5">
                        @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                            <button type="button" @click="openErpPushFor('{{ $pc->id }}', '{{ addslashes($pc->nama . ' - ' . ($pc->data['computerName'] ?? 'Laptop/PC')) }}', '{{ addslashes($pc->departemen) }}', '{{ addslashes($pc->nama) }}', '{{ addslashes($pc->data['computerName'] ?? 'IT-ASSET') }}')" class="text-blue-600 hover:text-blue-800 font-semibold text-[11px] transition flex items-center gap-1">
                                <span>⚡ Ke ERP</span>
                            </button>
                            <button @click="deleteTarget = { name: '{{ addslashes($pc->nama) }}', url: '{{ route('submissions.destroy', $pc->id) }}' }; openDeleteModal = true" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] transition">
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100">
                Belum ada data PC atau Laptop yang terdaftar.
            </div>
        @endforelse
    </div>
</div>

