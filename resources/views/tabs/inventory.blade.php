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
                <input type="text" x-model="searchTerm" placeholder="Cari nama, CPU, model..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Division Filter -->
            <select x-model="filterDept" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">Semua Divisi ({{ count($divisions) }})</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>

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
            <div x-show="(filterDept === 'all' || '{{ $pc->departemen }}' === filterDept) && (searchTerm === '' || '{{ strtolower($pc->nama . ' ' . $pc->departemen . ' ' . ($pc->data['processor'] ?? '') . ' ' . ($pc->data['computerName'] ?? '')) }}'.includes(searchTerm.toLowerCase()))" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition flex flex-col justify-between space-y-4">
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

                    <!-- Specs Breakdown -->
                    <div class="py-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Komputer / Host:</span>
                            <span class="font-mono font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['computerName'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Processor:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['processor'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">RAM:</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $pc->data['memory'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Sistem Operasi:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['os'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Manufaktur:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $pc->data['manufacturer'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400 font-mono">{{ $pc->timestamp }}</span>
                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                        <button @click="deleteTarget = { name: '{{ addslashes($pc->nama) }}', url: '{{ route('submissions.destroy', $pc->id) }}' }; openDeleteModal = true" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] transition">
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100">
                Belum ada data PC atau Laptop yang terdaftar.
            </div>
        @endforelse
    </div>
</div>

