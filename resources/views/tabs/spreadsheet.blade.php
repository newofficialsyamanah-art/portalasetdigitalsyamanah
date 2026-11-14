<!-- Tab: Spreadsheet Master Ledger 1:1 -->
<div x-show="activeTab === 'spreadsheet'" x-cloak class="space-y-6">
    <!-- Top Filter & Search Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
                📊
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Master Database Spreadsheet 1:1</h2>
                <p class="text-xs text-slate-500 mt-0.5">Seluruh rekaman aset fisik, kredensial akun sales, dan perangkat workstation</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Search -->
            <div class="relative flex-1 sm:w-64">
                <input type="text" x-model="searchTerm" placeholder="Cari nama, IP, komputer, OS..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Division Filter -->
            <select x-model="filterDept" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">Semua Divisi ({{ count($divisions) }})</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>

            <!-- Excel Export & Import Actions -->
            <button type="button" @click="openExportModal = true" class="px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Ekspor Data ke Excel (.xlsx)">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Ekspor Excel</span>
            </button>

            <button type="button" @click="openImportModal = true" class="px-3 py-2 rounded-xl border border-teal-300 bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs" title="Impor Data dari Excel (.xlsx / .csv)">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Impor Excel</span>
            </button>

            <!-- Refresh / Sync Button -->
            <form action="{{ route('sync.spreadsheet') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Sinkronkan dengan Google Spreadsheet">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Spreadsheet Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Lengkap &amp; Jabatan</th>
                        <th class="py-3.5 px-4">Divisi / Tim</th>
                        <th class="py-3.5 px-4">Tipe Aset &amp; Model</th>
                        <th class="py-3.5 px-4">Spesifikasi / Detail</th>
                        <th class="py-3.5 px-4">Kredensial &amp; Sandi</th>
                        <th class="py-3.5 px-4 text-center">Waktu Input</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $index => $sub)
                        <tr x-show="(filterDept === 'all' || '{{ $sub->departemen }}' === filterDept) && (searchTerm === '' || '{{ strtolower($sub->nama . ' ' . $sub->departemen . ' ' . ($sub->data['computerName'] ?? '') . ' ' . ($sub->data['jenisHp'] ?? '') . ' ' . ($sub->data['ip'] ?? '') . ' ' . ($sub->data['os'] ?? '') . ' ' . ($sub->data['processor'] ?? '')) }}'.includes(searchTerm.toLowerCase()))" class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $index + 1 }}</td>
                            
                            <!-- Nama & Jabatan -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $sub->nama }}</div>
                                <div class="text-[11px] text-slate-400">{{ $sub->jabatan ?: '-' }}</div>
                            </td>

                            <!-- Divisi -->
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded-md font-semibold text-[10px] bg-slate-100 text-slate-700">
                                    {{ $sub->departemen }}
                                </span>
                            </td>

                            <!-- Tipe Aset & Model -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-2">
                                    @if($sub->type === 'pc')
                                        <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            {{ str_contains(strtolower($sub->jenis_perangkat ?? ''), 'laptop') ? '💻' : '🖥️' }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $sub->jenis_perangkat }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">{{ $sub->data['computerName'] ?? ($sub->data['manufacturer'] ?? '-') }}</div>
                                            @if(!empty($sub->data['ip']) && $sub->data['ip'] !== '-')
                                                <div class="inline-flex items-center gap-1 font-mono text-[10px] text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded-md mt-1 border border-teal-200">
                                                    <span>🌐 {{ $sub->data['ip'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">📱</span>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $sub->data['jenisHp'] ?? 'Smartphone Sales' }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">WA: {{ $sub->data['noWa'] ?? '-' }}</div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Spesifikasi / Detail -->
                            <td class="py-3.5 px-4 max-w-xs">
                                @if($sub->type === 'pc')
                                    <div class="text-[11px] text-slate-600 line-clamp-1" title="{{ $sub->data['processor'] ?? '-' }}"><strong>CPU:</strong> {{ $sub->data['processor'] ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500"><strong>RAM:</strong> {{ $sub->data['memory'] ?? '-' }} &middot; <strong>OS:</strong> {{ $sub->data['os'] ?? '-' }}</div>
                                    @php
                                        $hasLinks = (!empty($sub->data['displayLink']) && $sub->data['displayLink'] !== '-') ||
                                                    (!empty($sub->data['soundLink']) && $sub->data['soundLink'] !== '-') ||
                                                    (!empty($sub->data['inputLink']) && $sub->data['inputLink'] !== '-');
                                    @endphp
                                    @if($hasLinks)
                                        <div class="flex items-center gap-2 mt-1">
                                            @if(!empty($sub->data['displayLink']) && $sub->data['displayLink'] !== '-')
                                                <a href="{{ $sub->data['displayLink'] }}" target="_blank" rel="noopener noreferrer" class="text-[10px] text-blue-600 hover:text-blue-800 underline font-semibold inline-flex items-center gap-0.5">
                                                    <span>🖥️ DxDiag</span>
                                                </a>
                                            @endif
                                            @if(!empty($sub->data['soundLink']) && $sub->data['soundLink'] !== '-')
                                                <a href="{{ $sub->data['soundLink'] }}" target="_blank" rel="noopener noreferrer" class="text-[10px] text-slate-500 hover:text-blue-600 underline inline-flex items-center gap-0.5">
                                                    <span>🔊 Audio</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="text-[11px] text-slate-600 line-clamp-1"><strong>Akun:</strong> {{ $sub->data['akunLoginPc'] ?? ($sub->data['email1'] ?? '-') }}</div>
                                    <div class="text-[11px] text-slate-500 line-clamp-1"><strong>Sosmed:</strong> IG: {{ $sub->data['ig1'] ?? '-' }} &middot; FB: {{ $sub->data['fb1'] ?? '-' }}</div>
                                @endif
                            </td>

                            <!-- Kredensial & Sandi (Masked for GA) -->
                            <td class="py-3.5 px-4" x-data="{ showPass: false }">
                                @if(Auth::check() && Auth::user()->role === 'general_affair')
                                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 font-mono italic">
                                        🔒 Terlindung (GA)
                                    </span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded text-slate-700">
                                            <span x-show="!showPass">••••••••</span>
                                            <span x-show="showPass" x-cloak class="text-amber-800 font-bold">
                                                {{ $sub->data['passwordPc'] ?? ($sub->data['passEmail1'] ?? ($sub->data['pinPolaHp'] ?? '-')) }}
                                            </span>
                                        </span>
                                        <button @click="showPass = !showPass" class="text-slate-400 hover:text-slate-700 p-1" title="Lihat/Sembunyikan Sandi">
                                            <svg x-show="!showPass" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <svg x-show="showPass" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- Waktu Input -->
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $sub->timestamp }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                                        <button type="button" @click="openErpPushFor('{{ $sub->id }}', '{{ addslashes($sub->nama . ' - ' . ($sub->data['computerName'] ?? ($sub->data['jenisHp'] ?? $sub->jenis_perangkat))) }}', '{{ addslashes($sub->departemen) }}', '{{ addslashes($sub->nama) }}', '{{ addslashes($sub->data['computerName'] ?? ($sub->data['jenisHp'] ?? 'IT-ASSET')) }}')" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="Kirim Aset ini ke ERPNext">
                                            <span class="text-xs font-bold">⚡ ERP</span>
                                        </button>
                                        <button @click="deleteTarget = { name: '{{ addslashes($sub->nama) }}', url: '{{ route('submissions.destroy', $sub->id) }}' }; openDeleteModal = true" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus Aset">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                Belum ada rekaman data inventaris. Klik "+ Tambah" untuk mulai input.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

