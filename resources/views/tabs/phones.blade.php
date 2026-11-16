<!-- Tab: Smartphone & Handphone Sales Inventory -->
<div x-show="activeTab === 'phones'" x-cloak class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                📱
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Inventaris Smartphone &amp; Akun Sales</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perangkat HP operasional tim sales, nomor WhatsApp bisnis, dan kredensial akun digital</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Search -->
            <div class="relative flex-1 sm:w-64">
                <input type="text" x-model="searchTerm" placeholder="Cari pemegang, tipe HP, WA..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Division Filter -->
            <select x-model="filterDept" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">Semua Tim ({{ count($divisions) }})</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>

            @can('manage excel')
            <button type="button" @click="exportToExcel('akun')" class="px-3 py-2 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Ekspor Data HP & Akun ke Excel">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Ekspor HP</span>
            </button>

            <button type="button" @click="openImportModal = true" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Impor Data Excel">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Impor</span>
            </button>
            @endcan

            <button type="button" @click="openAkunModal = true" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <span>➕ Input HP &amp; Akun Baru</span>
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
            $phoneList = $submissions->filter(function($s) {
                return $s->type === 'akun' || str_contains(strtolower($s->jenis_perangkat ?? ''), 'smartphone') || str_contains(strtolower($s->jenis_perangkat ?? ''), 'hp');
            });
        @endphp

        @forelse($phoneList as $hp)
            <div x-show="(filterDept === 'all' || '{{ $hp->departemen }}' === filterDept) && (searchTerm === '' || '{{ strtolower($hp->nama . ' ' . $hp->departemen . ' ' . ($hp->data['jenisHp'] ?? '') . ' ' . ($hp->data['noWa'] ?? '')) }}'.includes(searchTerm.toLowerCase()))" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition flex flex-col justify-between space-y-4">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base shrink-0">
                                📱
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900 leading-tight">{{ $hp->nama }}</h3>
                                <p class="text-[11px] text-slate-400"><span class="font-semibold text-slate-600">{{ $hp->departemen }}</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $bekasHp = $hp->data['bekasHp'] ?? 'Pengadaan';
                                $bekasSiapa = $hp->data['bekasSiapa'] ?? '';
                                $isBekas = str_contains(strtolower($bekasHp), 'bekas') || !empty($bekasSiapa);
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $isBekas ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-900' }}">
                                {{ $isBekas ? ('Bekas (' . ($bekasSiapa ?: 'Eks Tim Lain') . ')') : $bekasHp }}
                            </span>
                        </div>
                    </div>

                    <!-- Specs Breakdown -->
                    <div class="py-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Tipe Smartphone:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[170px]">{{ $hp->data['jenisHp'] ?? 'Android Sales' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Nomor WhatsApp:</span>
                            <span class="font-mono font-semibold text-emerald-700">{{ $hp->data['noWa'] ?? '-' }}</span>
                        </div>
                        
                        @can('view credentials')
                            @if(!empty($hp->data['accounts']) && is_array($hp->data['accounts']))
                                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Akun Operasional Sales:</div>
                                    @foreach($hp->data['accounts'] as $acc)
                                        <div class="flex items-center justify-between text-[11px] p-1.5 rounded-lg bg-slate-50 border border-slate-100/80">
                                            <span class="text-slate-600 font-semibold truncate max-w-[110px]">{{ $acc['platform'] ?? 'Akun' }}:</span>
                                            <div class="flex items-center gap-1.5 font-mono">
                                                <span class="font-bold text-slate-800 truncate max-w-[110px]" title="{{ $acc['username'] ?? '-' }}">{{ $acc['username'] ?? '-' }}</span>
                                                @if(!empty($acc['password']) && $acc['password'] !== '-')
                                                    <span class="text-[9px] text-amber-800 bg-amber-100/80 px-1 py-0.2 rounded font-mono">({{ $acc['password'] }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-1.5 pt-1 border-t border-slate-100">
                                    @if(!empty($hp->data['email1']))
                                        <div class="flex items-center justify-between text-slate-600">
                                            <span class="text-slate-400">Email:</span>
                                            <span class="font-mono text-slate-800 truncate max-w-[170px]">{{ $hp->data['email1'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($hp->data['ig1']))
                                        <div class="flex items-center justify-between text-slate-600">
                                            <span class="text-slate-400">Instagram:</span>
                                            <span class="font-mono text-slate-800 truncate max-w-[170px]">&#64;{{ $hp->data['ig1'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($hp->data['fb1']))
                                        <div class="flex items-center justify-between text-slate-600">
                                            <span class="text-slate-400">Facebook:</span>
                                            <span class="font-mono text-slate-800 truncate max-w-[170px]">{{ $hp->data['fb1'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-400 italic">
                                🔒 Kredensial akun operasional sales terlindungi
                            </div>
                        @endcan

                        <!-- Foto Fisik HP Attachment -->
                        @if(!empty($hp->data['foto']))
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 text-[11px] font-medium flex items-center gap-1.5">
                                    <span>📷</span>
                                    <span>Foto Unit HP:</span>
                                </span>
                                <a href="{{ $hp->data['foto'] }}" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] transition flex items-center gap-1 shadow-2xs">
                                    <span>Lihat Foto ↗</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400 font-mono">{{ $hp->timestamp }}</span>
                    <div class="flex items-center gap-2.5">
                        @can('manage erpnext')
                            <button type="button" @click="openErpPushFor('{{ $hp->id }}', '{{ addslashes($hp->nama . ' - ' . ($hp->data['jenisHp'] ?? 'Smartphone')) }}', '{{ addslashes($hp->departemen) }}', '{{ addslashes($hp->nama) }}', '{{ addslashes($hp->data['jenisHp'] ?? 'IT-PHONE') }}')" class="text-amber-600 hover:text-amber-800 font-semibold text-[11px] transition flex items-center gap-1">
                                <span>⚡ Ke ERP</span>
                            </button>
                        @endcan
                        @can('delete records')
                            <button @click="deleteTarget = { name: '{{ addslashes($hp->nama) }}', url: '{{ route('submissions.destroy', $hp->id) }}' }; openDeleteModal = true" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] transition">
                                Hapus
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100">
                Belum ada data Smartphone Sales yang terdaftar.
            </div>
        @endforelse
    </div>
</div>

