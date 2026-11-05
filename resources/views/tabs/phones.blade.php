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

            <button @click="setTab('akun')" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <span>+ Input HP &amp; Akun Baru</span>
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
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            {{ $hp->data['bekasHp'] ?? 'Pengadaan' }}
                        </span>
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
                        
                        @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Email Utama:</span>
                                <span class="font-mono text-slate-800 truncate max-w-[170px]">{{ $hp->data['email1'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Akun Instagram:</span>
                                <span class="font-mono text-slate-800 truncate max-w-[170px]">&#64;{{ $hp->data['ig1'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Akun Facebook:</span>
                                <span class="font-mono text-slate-800 truncate max-w-[170px]">{{ $hp->data['fb1'] ?? '-' }}</span>
                            </div>
                        @else
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-400 italic">
                                🔒 Kredensial email &amp; akun sosmed terlindungi (Peran General Affair)
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400 font-mono">{{ $hp->timestamp }}</span>
                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                        <button @click="deleteTarget = { name: '{{ addslashes($hp->nama) }}', url: '{{ route('submissions.destroy', $hp->id) }}' }; openDeleteModal = true" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] transition">
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100">
                Belum ada data Smartphone Sales yang terdaftar.
            </div>
        @endforelse
    </div>
</div>

