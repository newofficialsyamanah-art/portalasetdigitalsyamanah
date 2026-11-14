<!-- Tab: Guide Book & SOP Sistem -->
<div x-show="activeTab === 'guides'" x-cloak class="space-y-6" x-data="guideBookHandler()">

    <!-- Top Header & Filter Controls -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                📚
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Guide Book &amp; SOP Sistem (PDF)</h2>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1">
                        <span>🔒 DRM Terproteksi</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Dokumentasi operasional &amp; manual teknis berformat PDF terenkripsi (Anti-Download, Anti-Print &amp; Anti-Screenshot)</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Search Input -->
            <div class="relative flex-1 sm:w-60">
                <input type="text" x-model="guideSearch" placeholder="Cari SOP, divisi, fitur, sistem..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Division / Tim Filter -->
            <select x-model="selectedDivision" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">🏢 Semua Divisi / Tim</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>

            <!-- System Filter (Dikelola di Superadmin) -->
            <select x-model="selectedSystem" @change="selectedFeature = 'all'" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <option value="all">🖥️ Semua Sistem (Superadmin)</option>
                @foreach($appSystems as $sys)
                    <option value="{{ $sys->name }}">{{ $sys->icon }} {{ $sys->name }}</option>
                @endforeach
            </select>

            <!-- Feature Filter Dropdown (Dikelola di Superadmin) -->
            <select x-model="selectedFeature" x-show="availableFeatures.length > 0" class="px-3 py-2 rounded-xl border border-indigo-200 bg-indigo-50/50 text-xs font-semibold text-indigo-900 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                <option value="all">⚡ Semua Fitur</option>
                <template x-for="feat in availableFeatures" :key="feat">
                    <option :value="feat" x-text="'⚡ ' + feat"></option>
                </template>
            </select>

            <!-- Add Guide (Accessible to users) -->
            <button type="button" @click="openAdd()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <span>+ Buat / Unggah PDF</span>
            </button>
        </div>
    </div>

    <!-- Security & DRM Policy Banner -->
    <div class="p-4 rounded-2xl bg-slate-900 text-white border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-base shrink-0">
                🛡️
            </div>
            <div>
                <span class="font-bold text-white text-xs sm:text-sm">Kebijakan Keamanan Dokumen CONFIDENTIAL Perusahaan (DRM Active)</span>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Seluruh dokumen panduan sistem berstatus CONFIDENTIAL di bawah kewenangan Tim Business Analyst. Dilarang keras mengunduh, mencetak, atau mengambil tangkapan layar (anti-screenshot aktif).
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
            <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg bg-rose-950/80 text-rose-300 border border-rose-800/60">
                <span>🚫 No Download</span>
            </span>
            <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg bg-rose-950/80 text-rose-300 border border-rose-800/60">
                <span>🚫 No Print</span>
            </span>
            <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg bg-rose-950/80 text-rose-300 border border-rose-800/60">
                <span>🚫 Anti-Capture</span>
            </span>
        </div>
    </div>

    <!-- Quick Division / Tim Filter Pills (Dikelola Superadmin) -->
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <span>🏢 Filter Cepat Berdasarkan Divisi / Tim:</span>
            </span>
            <span class="text-[11px]" x-text="'Menampilkan: ' + filteredCount + ' Guide Book'"></span>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
            <button type="button" @click="selectedDivision = 'all'" :class="selectedDivision === 'all' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-3.5 py-1.5 rounded-full transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                <span>🌟 Semua Divisi</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedDivision === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ count($guideBooks) }}</span>
            </button>

            @foreach($divisions as $div)
                @php
                    $divCount = $guideBooks->filter(function($g) use ($div) {
                        $targetDiv = strtolower($g->target_division ?? '');
                        $targetRole = strtolower($g->target_role ?? '');
                        $title = strtolower($g->title ?? '');
                        $tags = strtolower(is_array($g->tags) ? implode(' ', $g->tags) : ($g->tags ?? ''));
                        $d = strtolower($div);
                        return $targetDiv === $d || str_contains($targetDiv, $d) || $targetRole === $d || str_contains($targetRole, $d) || str_contains($title, $d) || str_contains($tags, $d);
                    })->count();
                @endphp
                <button type="button" @click="selectedDivision = '{{ addslashes($div) }}'" :class="selectedDivision === '{{ addslashes($div) }}' ? 'bg-gradient-to-r from-amber-600 to-amber-700 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-3 py-1.5 rounded-full transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span>{{ $div }}</span>
                    @if($divCount > 0)
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedDivision === '{{ addslashes($div) }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $divCount }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- Quick System Category Filter Pills (Berdasarkan Kelola Sistem di Superadmin) -->
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <span>🖥️ Filter Cepat Berdasarkan Sistem (Superadmin):</span>
            </span>
            <span class="text-[11px]" x-show="selectedSystem !== 'all'" x-cloak>
                <button type="button" @click="selectedSystem = 'all'; selectedFeature = 'all'" class="text-[#3A7D8A] hover:underline font-bold cursor-pointer">
                    ✕ Reset Sistem
                </button>
            </span>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
            <button type="button" @click="selectedSystem = 'all'; selectedFeature = 'all'" :class="selectedSystem === 'all' ? 'bg-slate-800 text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-3.5 py-1.5 rounded-full transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                <span>🖥️ Semua Sistem</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedSystem === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ count($guideBooks) }}</span>
            </button>

            @foreach($appSystems as $sys)
                @php
                    $sysCount = $guideBooks->where('system_name', $sys->name)->count();
                @endphp
                <button type="button" @click="selectedSystem = '{{ addslashes($sys->name) }}'; selectedFeature = 'all'" :class="selectedSystem === '{{ addslashes($sys->name) }}' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white font-bold shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-3 py-1.5 rounded-full transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span>{{ $sys->icon ?: '🖥️' }}</span>
                    <span>{{ $sys->name }}</span>
                    @if($sysCount > 0)
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedSystem === '{{ addslashes($sys->name) }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $sysCount }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- Quick Features Filter Pills (Muncul Dinamis Berdasarkan Fitur yang Dikelola di Superadmin) -->
    <div x-show="availableFeatures.length > 0" x-cloak class="p-3 bg-gradient-to-r from-indigo-50/80 via-blue-50/50 to-indigo-50/80 rounded-2xl border border-indigo-100 space-y-2 animate-in fade-in">
        <div class="flex items-center justify-between text-xs text-indigo-900">
            <span class="font-bold flex items-center gap-1.5">
                <span>⚡ Filter Fitur Terdaftar di Superadmin:</span>
                <span class="text-[11px] font-semibold text-indigo-700 bg-white/80 px-2 py-0.5 rounded-md border border-indigo-200" x-text="selectedSystem === 'all' ? 'Seluruh Sistem' : selectedSystem"></span>
            </span>
            <button type="button" @click="selectedFeature = 'all'" x-show="selectedFeature !== 'all'" class="text-[11px] text-indigo-700 hover:text-indigo-900 hover:underline font-bold cursor-pointer">
                ✕ Reset Filter Fitur
            </button>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" @click="selectedFeature = 'all'" :class="selectedFeature === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white text-indigo-900 border border-indigo-200 hover:bg-indigo-100/70'" class="px-3 py-1 rounded-xl text-xs transition cursor-pointer">
                🌟 Semua Fitur
            </button>
            <template x-for="feat in availableFeatures" :key="feat">
                <button type="button" @click="selectedFeature = feat" :class="selectedFeature === feat ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-bold shadow-xs' : 'bg-white text-indigo-900 border border-indigo-200 hover:bg-indigo-100/70'" class="px-3 py-1 rounded-xl text-xs transition cursor-pointer flex items-center gap-1">
                    <span>⚡</span>
                    <span x-text="feat"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Guide Books Grid (PDF Card View) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($guideBooks as $guide)
            @php
                $matchedSys = $appSystems->firstWhere('name', $guide->system_name);
                $sysIcon = $matchedSys ? ($matchedSys->icon ?: '🖥️') : '📄';
                $sysBg = 'bg-slate-100 text-slate-800 border-slate-200';
                if ($matchedSys) {
                    $c = strtolower($matchedSys->category ?? '');
                    if (str_contains($c, 'operasional')) $sysBg = 'bg-teal-50 text-teal-800 border-teal-200';
                    elseif (str_contains($c, 'produksi') || str_contains($c, 'sablon')) $sysBg = 'bg-amber-50 text-amber-900 border-amber-200';
                    elseif (str_contains($c, 'sales') || str_contains($c, 'cs')) $sysBg = 'bg-rose-50 text-rose-800 border-rose-200';
                    elseif (str_contains($c, 'finance') || str_contains($c, 'kasir')) $sysBg = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    elseif (str_contains($c, 'it') || str_contains($c, 'jaringan')) $sysBg = 'bg-blue-50 text-blue-800 border-blue-200';
                    elseif (str_contains($c, 'gudang')) $sysBg = 'bg-orange-50 text-orange-800 border-orange-200';
                    elseif (str_contains($c, 'hrd')) $sysBg = 'bg-purple-50 text-purple-800 border-purple-200';
                }
            @endphp

            <div x-show="matchesFilterById({{ $guide->id }})"
                 class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 flex flex-col justify-between group relative overflow-hidden">
                
                @if($guide->is_pinned)
                    <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none">
                        <div class="bg-gradient-to-l from-amber-500 to-amber-400 text-slate-950 font-black text-[9px] py-1 text-center rotate-45 transform translate-x-4 translate-y-2 shadow-xs">
                            PINNED
                        </div>
                    </div>
                @endif

                <div>
                    <!-- Top System & PDF Badges -->
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold border {{ $sysBg }}">
                            <span>{{ $sysIcon }}</span>
                            <span>{{ $guide->system_name }}</span>
                        </span>

                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                PDF
                            </span>
                            <span class="font-mono text-[10px] font-semibold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100">
                                {{ $guide->version ?: 'v1.0' }}
                            </span>
                        </div>
                    </div>

                    <!-- Target Division & Specific Feature Badges -->
                    <div class="flex flex-wrap items-center gap-1.5 mb-2.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            <span>🏢</span>
                            <span>Divisi: {{ $guide->target_division ?: ($guide->target_role ?: 'Semua Divisi') }}</span>
                        </span>

                        @if(!empty($guide->feature_name))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                <span>⚡</span>
                                <span>Fitur: {{ $guide->feature_name }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-[#3A7D8A] transition leading-snug mb-2">
                        {{ $guide->title }}
                    </h3>

                    <!-- Author & Views Info -->
                    <div class="flex items-center gap-3 text-[11px] text-slate-400 mb-3">
                        <span class="flex items-center gap-1">
                            <span>✍️</span>
                            <span class="truncate max-w-[130px]">{{ $guide->author }}</span>
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1">
                            <span>👁️</span>
                            <span>{{ $guide->views_count }}x dilihat</span>
                        </span>
                    </div>

                    <!-- Summary -->
                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                        {{ $guide->summary }}
                    </p>

                    <!-- Tags -->
                    @if(!empty($guide->tags))
                        <div class="flex flex-wrap items-center gap-1 mb-4">
                            @foreach((array)$guide->tags as $t)
                                <span class="text-[10px] font-mono px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md">
                                    #{{ $t }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Footer Card Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <button type="button" @click="openReaderById({{ $guide->id }})" class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-700 to-slate-900 hover:brightness-110 text-white font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <span>🔒 Buka Dokumen PDF</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="openEditById({{ $guide->id }})" class="p-2 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-xl transition cursor-pointer" title="Edit Metadata & File PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>

                        <button type="button" @click="confirmDeleteById({{ $guide->id }})" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Hapus Dokumen PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-100 p-8 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl font-bold">
                    📚
                </div>
                <h3 class="font-bold text-sm text-slate-800">Belum Ada Buku Panduan &amp; SOP</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Seluruh dokumen dummy telah dibersihkan. Anda dapat menambahkan buku panduan atau SOP sistem resmi baru kapan saja melalui tombol di bawah.
                </p>
                <div class="pt-2">
                    <button type="button" @click="openAdd()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition inline-flex items-center gap-2 cursor-pointer">
                        <span>+ Buat / Unggah SOP Baru (PDF)</span>
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ============================================================ -->
    <!-- SECURE CANVAS-BASED PDF VIEWER WITH ANTI-LEAK / ANTI-DRM    -->
    <!-- (No Download, No Print, Anti-Screenshot Blur, Watermarked)  -->
    <!-- ============================================================ -->
    <div x-show="openReaderModal" 
         x-cloak 
         class="fixed inset-0 z-[100] overflow-hidden flex flex-col bg-slate-950/95 backdrop-blur-md select-none"
         style="-webkit-touch-callout: none; -webkit-user-select: none; user-select: none;"
         @contextmenu.prevent=""
         @keydown.window="handleGlobalKeydown($event)">

        <!-- Viewer Top Navbar -->
        <div class="bg-slate-900 border-b border-slate-800 text-white px-4 py-2.5 flex items-center justify-between gap-3 shrink-0 shadow-lg select-none">
            
            <!-- Left: Document Title & Protection Badge -->
            <div class="flex items-center gap-3 truncate">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-sm shrink-0">
                    🔒
                </div>
                <div class="truncate">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xs sm:text-sm text-white truncate max-w-xs sm:max-w-md" x-text="currentGuide ? currentGuide.title : 'Dokumen PDF'"></span>
                        <span class="text-[9px] font-mono px-2 py-0.5 rounded bg-rose-900/60 text-rose-300 border border-rose-800/80 shrink-0">
                            CONFIDENTIAL • DRM
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate" x-text="currentGuide ? ((currentGuide.target_role ? ('👥 ' + currentGuide.target_role + ' • ') : '') + currentGuide.system_name + ' • ' + (currentGuide.version || 'v1.0') + ' • Penulis: ' + currentGuide.author) : ''"></p>
                </div>
            </div>

            <!-- Right: Close Button -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="closeReader()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl transition cursor-pointer flex items-center gap-1.5 text-xs font-semibold" title="Tutup Viewer (Esc)">
                    <span>✕ Tutup (Esc)</span>
                </button>
            </div>
        </div>

        <!-- Viewer Main Area -->
        <div class="flex-1 overflow-hidden p-2 sm:p-4 md:p-6 flex justify-center items-center relative bg-slate-950" id="pdfViewerScrollArea">
            
            <!-- Protected Document Frame Wrapper with Security Watermark Layer -->
            <div class="relative w-[1000px] max-w-full h-[85vh] shadow-2xl rounded-2xl overflow-hidden bg-slate-900 border border-slate-700/50 flex flex-col items-center justify-center transition-all duration-75 select-none"
                 style="-webkit-touch-callout: none; -webkit-user-select: none; user-select: none;"
                 :class="isScreenMasked ? 'filter blur-3xl opacity-0 scale-95 pointer-events-none' : ''"
                 ondragstart="return false;"
                 onselectstart="return false;">
                
                <!-- The Direct High-Compatibility Protected PDF Frame -->
                <iframe :src="currentPdfUrl ? (currentPdfUrl + '#toolbar=0&navpanes=0') : ''" 
                        class="w-full h-full border-0 select-none bg-slate-100"
                        title="Dokumen PDF Terproteksi">
                </iframe>

                <!-- Dense Forensic Watermark Grid Pattern (Tiled over 100% of the surface) -->
                <div class="absolute inset-0 pointer-events-none select-none z-20 overflow-hidden"
                     style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'340\' height=\'200\' viewBox=\'0 0 340 200\'><g transform=\'rotate(-28 170 100)\' fill=\'%23b91c1c\' fill-opacity=\'0.20\' font-family=\'sans-serif\' font-weight=\'bold\' font-size=\'11\' text-anchor=\'middle\'><text x=\'170\' y=\'65\'>CV GENERASI SUGIH SEJAHTERA</text><text x=\'170\' y=\'85\'>CONFIDENTIAL &bull; {{ Auth::check() ? strtoupper(Auth::user()->name) : "USER SYAMANAH" }}</text><text x=\'170\' y=\'105\'>DILARANG SCREENSHOT / FOTO</text></g></svg>'); background-repeat: repeat; opacity: 1;">
                </div>
            </div>

            <!-- ANTI-SCREENSHOT & BLUR PRIVACY SHIELD OVERLAY (Blinds screen during Snipping Tool / Loss of Focus / Mobile Gestures) -->
            <div x-show="isScreenMasked" 
                 x-cloak 
                 class="fixed inset-0 z-[120] bg-slate-950/98 backdrop-blur-3xl flex flex-col items-center justify-center p-6 text-center text-white space-y-5 animate-in fade-in duration-75 select-none"
                 style="-webkit-touch-callout: none; -webkit-user-select: none; user-select: none;">
                
                <div class="relative">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-rose-600/20 text-rose-500 border border-rose-500/50 flex items-center justify-center font-black text-4xl sm:text-5xl shadow-2xl animate-pulse">
                        🚫
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/50 flex items-center justify-center text-sm shadow-md">
                        🔒
                    </div>
                </div>

                <div class="space-y-3 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-rose-950 text-rose-300 border border-rose-800 text-[10px] font-mono font-bold tracking-wider uppercase">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        CONFIDENTIAL • SISTEM KEAMANAN DOKUMEN AKTIF
                    </div>
                    
                    <h3 class="text-lg sm:text-2xl font-black text-white tracking-wide uppercase text-rose-400">
                        Akses Screenshot Ditolak!
                    </h3>
                    
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800/80 text-left space-y-2">
                        <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-medium">
                            Pengambilan tangkapan layar (screenshot/rekaman layar) tidak diizinkan untuk dokumen ini. <span class="text-amber-400 font-bold">Kewenangan dan hak akses dokumen sepenuhnya berada di Tim Business Analyst</span> CV Generasi Sugih Sejahtera.
                        </p>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Jika Anda membutuhkan materi panduan, salinan resmi, atau izin khusus untuk operasional kerja, silakan ajukan permohonan secara langsung kepada <strong class="text-slate-200">Tim Business Analyst</strong>.
                        </p>
                    </div>

                    <p class="text-[10px] text-slate-500 font-mono">
                        Security Tag: DRM-BA-CONFIDENTIAL • User: {{ Auth::check() ? strtoupper(Auth::user()->name) : 'GUEST' }}
                    </p>
                </div>

                <button type="button" 
                        @click="isScreenMasked = false" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:brightness-110 text-white font-bold text-xs shadow-lg transition cursor-pointer flex items-center gap-2">
                    <span>👁️ Saya Mengerti &amp; Buka Kembali Dokumen PDF</span>
                </button>
            </div>
        </div>

        <!-- Viewer Bottom Security Status Footer -->
        <div class="bg-slate-900/90 border-t border-slate-800 text-slate-400 px-4 py-2 flex items-center justify-between text-[11px] font-mono shrink-0 select-none">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>DRM ENGINE ACTIVE: High-Compatibility Secure Frame</span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-slate-500">
                <span>DOWNLOAD: BLOCKED 🚫</span>
                <span>PRINT: BLOCKED 🚫</span>
                <span>WATERMARK: ACTIVE 🔒</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL FORM UNGGAH / TAMBAH / EDIT BUKU PANDUAN PDF           -->
    <!-- ALUR TERSTRUKTUR: ROLE -> SISTEM -> FITUR -> JUDUL OTOMATIS  -->
    <!-- ============================================================ -->
    <div x-show="openFormModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-3 sm:p-6 bg-slate-950/70 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95">
            <!-- Form Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 border border-amber-200/60 flex items-center justify-center font-bold text-base">
                        📑
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-slate-900" x-text="isEditing ? 'Perbarui Buku Panduan / SOP PDF' : 'Tambah Buku Panduan / SOP Baru (PDF)'"></h3>
                        <p class="text-[11px] text-slate-500">Alur bertahap: Tentukan Target Role &rarr; Pilih Sistem &rarr; Fitur Spesifik &rarr; Judul Otomatis</p>
                    </div>
                </div>
                <button type="button" @click="openFormModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Form Content with enctype multipart -->
            <form id="guideBookForm"
                  :action="(isEditing && formData.id) ? ('/guide-books/' + formData.id) : '/guide-books'" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  @submit.prevent="doSaveGuide()"
                  novalidate
                  class="flex-1 flex flex-col overflow-hidden text-xs">
                @csrf
                <input type="hidden" name="_method" :value="(isEditing && formData.id) ? 'PUT' : 'POST'">

                <div class="p-6 overflow-y-auto flex-1 space-y-5" id="guideFormScrollArea">
                    
                    <!-- Alert Error Box if submission fails -->
                    <div x-show="formError" x-cloak class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 flex items-start gap-2.5 animate-in fade-in">
                        <span class="text-base shrink-0">⚠️</span>
                        <div class="flex-1">
                            <h4 class="font-bold text-xs">Gagal Menyimpan Panduan:</h4>
                            <p class="text-[11px] text-rose-700 leading-relaxed font-semibold" x-text="formError"></p>
                        </div>
                        <button type="button" @click="formError = null" class="text-rose-400 hover:text-rose-700 cursor-pointer">✕</button>
                    </div>
                    
                    <!-- STEP 1: PILIH TARGET DIVISI / TIM (DIKELOLA SUPERADMIN) -->
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center text-[10px] font-mono">1</span>
                                <span>Target Divisi / Tim Sasaran <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] text-amber-700 font-bold">Dikelola di Superadmin</span>
                        </div>

                        <select name="target_division" x-model="formData.target_division" @change="onFieldChange()" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden shadow-2xs">
                            <option value="Semua Divisi">🌟 Semua Divisi (Umum Perusahaan)</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div }}">{{ $div }}</option>
                            @endforeach
                            <option value="Kustom / Divisi Lainnya">Kustom / Divisi Lainnya (Ketik Sendiri)</option>
                        </select>

                        <div x-show="formData.target_division === 'Kustom / Divisi Lainnya'" x-cloak class="pt-1">
                            <input type="text" name="custom_division" x-model="formData.custom_division" @input="onFieldChange()" placeholder="Ketik nama divisi/tim sasaran..." class="w-full px-3.5 py-2 rounded-xl border border-amber-200 text-xs bg-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        </div>

                        <!-- Secondary Role / Jabatan helper -->
                        <div class="pt-2 border-t border-amber-200/60 flex items-center gap-2">
                            <span class="text-[10px] text-amber-900 font-semibold shrink-0">Peran Spesifik:</span>
                            <select name="target_role" x-model="formData.target_role" @change="onFieldChange()" class="flex-1 px-2.5 py-1.5 rounded-lg border border-amber-200 text-[11px] text-slate-700 bg-white focus:ring-1 focus:ring-amber-500">
                                @foreach($guideRoles as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- STEP 2: PILIH SISTEM TERKAIT (DIKELOLA SUPERADMIN) -->
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-mono">2</span>
                                <span>Pilih Sistem / Modul Terkait <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] text-blue-700 font-bold">Dikelola di Superadmin</span>
                        </div>

                        <select name="system_name" x-model="formData.system_name" @change="onSystemChange()" class="w-full px-3.5 py-2.5 rounded-xl border border-blue-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden shadow-2xs">
                            @foreach($appSystems as $s)
                                <option value="{{ $s->name }}">{{ $s->icon }} {{ $s->name }}</option>
                            @endforeach
                            <option value="Kustom / Sistem Lainnya">Kustom / Sistem Lainnya (Ketik Sendiri)</option>
                        </select>

                        <div x-show="formData.system_name === 'Kustom / Sistem Lainnya'" x-cloak class="pt-1">
                            <input type="text" name="custom_system" x-model="formData.custom_system" @input="onFieldChange()" placeholder="Ketik nama sistem/modul (contoh: Server NAS Cloud)..." class="w-full px-3.5 py-2 rounded-xl border border-blue-200 text-xs bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                        </div>
                    </div>

                    <!-- STEP 3: FITUR YANG SEDANG DIJELASKAN -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-mono">3</span>
                                <span>Fitur yang Sedang Dijelaskan <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] text-emerald-700 font-bold">Aksi / Modul Spesifik</span>
                        </div>

                        <input type="text" name="feature_name" x-model="formData.feature_name" @input="onFieldChange()" placeholder="Contoh: Ekspor DxDiag Windows, Buat Voucher Hotspot, dll" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden shadow-2xs">

                        <!-- Interactive Chip Suggestions -->
                        <div class="pt-1" x-show="currentFeatureSuggestions.length > 0">
                            <span class="text-[10px] text-slate-500 font-semibold mb-1 block">💡 Rekomendasi Cepat Fitur (Klik untuk memilih):</span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <template x-for="chip in currentFeatureSuggestions" :key="chip">
                                    <button type="button" @click="selectFeatureChip(chip)" 
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition flex items-center gap-1 border cursor-pointer"
                                            :class="formData.feature_name === chip ? 'bg-emerald-600 text-white border-emerald-700 shadow-2xs font-bold' : 'bg-white hover:bg-emerald-100 text-emerald-900 border-emerald-200'">
                                        <span x-text="'+ ' + chip"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: JUDUL BUKU PANDUAN / SOP (OTOMATIS DIHASILKAN) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-mono">4</span>
                                <span>Judul Buku Panduan / SOP <span class="text-rose-500">*</span></span>
                            </label>
                            <button type="button" @click="regenerateTitle()" class="text-[10px] text-[#3A7D8A] hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                <span>✨ Reset Judul Otomatis</span>
                            </button>
                        </div>

                        <input type="text" name="title" x-model="formData.title" @input="isManualTitle = true" placeholder="Judul terisi otomatis berdasarkan Role, Sistem & Fitur..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-white focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden shadow-2xs">
                        <p class="text-[10px] text-slate-500">
                            Sistem secara cerdas menyusun judul baku standar: <span class="font-mono text-slate-700 font-semibold">SOP [Fitur] - [Sistem] ([Role])</span>
                        </p>
                    </div>

                    <!-- DEDICATED PDF FILE UPLOAD BOX -->
                    <div class="p-4 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-slate-800 text-xs">
                                5. Unggah Berkas PDF (Opsional / PDF Kustom)
                            </label>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">DRM Protected</span>
                        </div>
                        <input type="file" name="pdf_file" accept="application/pdf" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3A7D8A]/10 file:text-[#3A7D8A] hover:file:bg-[#3A7D8A]/20">
                        <p class="text-[10px] text-slate-400 leading-relaxed">
                            💡 <strong>Info:</strong> Jika Anda mengunggah file PDF (maksimal 30 MB), sistem akan menyimpannya secara terenkripsi. Jika dikosongkan, sistem akan otomatis menghasilkan PDF standar ber-watermark dari ringkasan &amp; teks di bawah.
                        </p>
                    </div>

                    <!-- METADATA TAMBAHAN: VERSI, PENYUSUN, RINGKASAN & TEKS ISI -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Versi Dokumen</label>
                            <input type="text" name="version" x-model="formData.version" placeholder="Contoh: v1.0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penulis / Unit Pembuat</label>
                            <input type="text" name="author" x-model="formData.author" placeholder="Contoh: Tim IT Syamanah" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tautan Drive (Opsional)</label>
                            <input type="url" name="document_url" x-model="formData.document_url" placeholder="https://drive.google.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Ringkasan Singkat (Summary)</label>
                        <textarea name="summary" rows="2" x-model="formData.summary" placeholder="Ringkasan 1-2 kalimat tentang tujuan panduan ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Teks Isi Instruksi SOP (Untuk PDF Otomatis)</label>
                            <span class="text-[10px] text-slate-400">Gunakan # untuk judul bab, - untuk poin instruksi</span>
                        </div>
                        <textarea name="content" rows="6" x-model="formData.content" placeholder="# Langkah-langkah SOP&#10;1. Pertama...&#10;2. Kedua...&#10;&#10;Catatan penting disini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-xs leading-relaxed focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tagar (Pisahkan dengan koma)</label>
                            <input type="text" name="tags" x-model="formData.tags" placeholder="Contoh: dxdiag, hardware, windows" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono">
                        </div>

                        <div class="pt-5">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_pinned" value="1" x-model="formData.is_pinned" class="rounded text-[#3A7D8A] focus:ring-0 w-4 h-4 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-800">Sematkan di Atas (Pin to Top) ⭐</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Footer -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-slate-50/50 rounded-b-3xl">
                    <button type="button" @click="openFormModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" 
                            @click="doSaveGuide()"
                            :disabled="formSubmitting"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 active:scale-95 disabled:opacity-60 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                        <span x-show="!formSubmitting">💾 Simpan &amp; Enkripsi Dokumen PDF</span>
                        <span x-show="formSubmitting" x-cloak class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengenkripsi &amp; Menyimpan PDF...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
</div>

<script>
    function guideBookHandler() {
        return {
            guideSearch: '',
            selectedDivision: 'all',
            selectedRole: 'all',
            selectedSystem: 'all',
            selectedFeature: 'all',
            selectedCategory: 'all',
            openReaderModal: false,
            openFormModal: false,
            isEditing: false,
            isManualTitle: false,
            formSubmitting: false,
            formError: null,
            currentGuide: null,
            
            // All guides dictionary keyed by numeric ID
            allGuides: @json($guideBooks->keyBy('id')),

            // Feature suggestions mapping per system (Murni dari Master AppSystem Superadmin)
            featuresMap: @json($systemFeaturesMap),

            // Protected PDF Viewer State
            currentPdfUrl: '',
            isScreenMasked: false,

            formData: {
                id: '',
                target_division: 'Semua Divisi',
                custom_division: '',
                target_role: 'Semua Karyawan (Umum)',
                custom_role: '',
                system_name: (@json(count($guideSystems)) > 0) ? @json($guideSystems[0] ?? '') : 'Kustom / Sistem Lainnya',
                custom_system: '',
                feature_name: '',
                title: '',
                category: 'SOP Operasional',
                version: 'v1.0',
                author: '{{ Auth::check() ? Auth::user()->name : "Tim IT Syamanah" }}',
                summary: '',
                content: '',
                document_url: '',
                tags: '',
                is_pinned: false
            },

            init() {
                // 1. Anti-Snipping Tool: When user triggers Win+Shift+S or external capture tool, browser loses focus
                window.addEventListener('blur', () => {
                    if (this.openReaderModal) {
                        this.isScreenMasked = true;
                        this.clearClipboard();
                    }
                });

                // 2. Tab switch or window minimization
                document.addEventListener('visibilitychange', () => {
                    if (this.openReaderModal && document.visibilityState === 'hidden') {
                        this.isScreenMasked = true;
                        this.clearClipboard();
                    }
                });

                // 3. Return focus to browser window: unmask automatically
                window.addEventListener('focus', () => {
                    if (this.openReaderModal) {
                        setTimeout(() => {
                            this.isScreenMasked = false;
                        }, 120);
                    }
                });

                // 4. Mobile Multi-Touch Anti-Screenshot: Detect 3-finger swipe screenshot (Xiaomi/MIUI, Oppo/Realme, Vivo, Samsung)
                window.addEventListener('touchstart', (e) => {
                    if (this.openReaderModal && e.touches && e.touches.length >= 3) {
                        this.isScreenMasked = true;
                        this.clearClipboard();
                    }
                }, { passive: true });
            },

            get availableFeatures() {
                if (this.selectedSystem !== 'all') {
                    return this.featuresMap[this.selectedSystem] || [];
                }
                const all = [];
                for (const sys in this.featuresMap) {
                    if (Array.isArray(this.featuresMap[sys])) {
                        for (const f of this.featuresMap[sys]) {
                            if (f && !all.includes(f)) all.push(f);
                        }
                    }
                }
                return all;
            },

            get currentFeatureSuggestions() {
                const sys = this.getEffectiveSystem();
                return this.featuresMap[sys] || [
                    'Operasional Standar ' + sys,
                    'Konfigurasi Pengguna',
                    'Pencegahan Kendala',
                    'Pencatatan Aset'
                ];
            },

            get filteredCount() {
                const guideIds = Object.keys(this.allGuides);
                return guideIds.filter(id => this.matchesFilterById(id)).length;
            },

            getEffectiveDivision() {
                if (this.formData.target_division === 'Kustom / Divisi Lainnya') {
                    return (this.formData.custom_division || '').trim();
                }
                return (this.formData.target_division || 'Semua Divisi').trim();
            },

            getEffectiveRole() {
                if (this.formData.target_role === 'Kustom / Peran Lainnya') {
                    return (this.formData.custom_role || '').trim();
                }
                return (this.formData.target_role || '').trim();
            },

            getEffectiveSystem() {
                if (this.formData.system_name === 'Kustom / Sistem Lainnya') {
                    return (this.formData.custom_system || '').trim();
                }
                return (this.formData.system_name || '').trim();
            },

            onFieldChange() {
                if (!this.isManualTitle) {
                    this.updateTitleAuto();
                }
            },

            onSystemChange() {
                const sys = this.getEffectiveSystem();
                const suggestions = this.featuresMap[sys];
                if (suggestions && suggestions.length > 0) {
                    this.formData.feature_name = suggestions[0];
                }
                this.updateTitleAuto();
            },

            selectFeatureChip(chipName) {
                this.formData.feature_name = chipName;
                this.updateTitleAuto();
            },

            updateTitleAuto() {
                const div = this.getEffectiveDivision() || 'Semua Divisi';
                const sys = this.getEffectiveSystem() || 'Sistem Kantor';
                const feat = (this.formData.feature_name && this.formData.feature_name.trim()) ? this.formData.feature_name.trim() : 'Operasional';

                this.formData.title = 'SOP ' + feat + ' - ' + sys + ' (' + div + ')';
                if (!this.formData.summary || this.formData.summary.startsWith('SOP ') || this.formData.summary.startsWith('Panduan ') || this.formData.summary.startsWith('Guide Book ')) {
                    this.formData.summary = 'Guide Book operasional modul ' + feat + ' pada sistem ' + sys + ' untuk divisi ' + div + '.';
                }
            },

            regenerateTitle() {
                this.isManualTitle = false;
                this.updateTitleAuto();
            },

            matchesFilterById(id) {
                const guide = this.allGuides[id];
                if (!guide) return true;

                // 1. Division Filter (Superadmin)
                let divMatch = (this.selectedDivision === 'all');
                if (!divMatch) {
                    const sel = this.selectedDivision.toLowerCase().trim();
                    const gDiv = (guide.target_division || '').toLowerCase();
                    const gRole = (guide.target_role || '').toLowerCase();
                    const gTitle = (guide.title || '').toLowerCase();
                    const gTags = (Array.isArray(guide.tags) ? guide.tags.join(' ') : (guide.tags || '')).toLowerCase();
                    divMatch = (gDiv === sel) || gDiv.includes(sel) || (gRole === sel) || gRole.includes(sel) || gTitle.includes(sel) || gTags.includes(sel);
                }

                // 2. System Filter (Superadmin)
                const systemMatch = (this.selectedSystem === 'all') || (guide.system_name === this.selectedSystem);

                // 3. Feature Filter (Superadmin)
                let featureMatch = (this.selectedFeature === 'all');
                if (!featureMatch) {
                    const selF = this.selectedFeature.toLowerCase().trim();
                    const gFeat = (guide.feature_name || '').toLowerCase();
                    const gTitle = (guide.title || '').toLowerCase();
                    const gSummary = (guide.summary || '').toLowerCase();
                    featureMatch = (gFeat === selF) || gFeat.includes(selF) || gTitle.includes(selF) || gSummary.includes(selF);
                }

                const roleMatch = (this.selectedRole === 'all') || (guide.target_role === this.selectedRole);
                const baseMatch = divMatch && systemMatch && featureMatch && roleMatch;
                if (!this.guideSearch) return baseMatch;

                const term = this.guideSearch.toLowerCase().trim();
                const searchable = (
                    (guide.title || '') + ' ' +
                    (guide.system_name || '') + ' ' +
                    (guide.target_division || '') + ' ' +
                    (guide.target_role || '') + ' ' +
                    (guide.feature_name || '') + ' ' +
                    (guide.summary || '') + ' ' +
                    (Array.isArray(guide.tags) ? guide.tags.join(' ') : (guide.tags || ''))
                ).toLowerCase();

                return baseMatch && searchable.includes(term);
            },

            matchesFilter(role, system, searchContent) {
                const roleMatch = (this.selectedRole === 'all') || (role === this.selectedRole);
                const systemMatch = (this.selectedSystem === 'all') || (system === this.selectedSystem);
                const searchMatch = !this.guideSearch || searchContent.includes(this.guideSearch.toLowerCase().trim());
                return roleMatch && systemMatch && searchMatch;
            },

            openReaderById(id) {
                const guide = this.allGuides[id] || { id: id, title: 'Dokumen PDF', system_name: '', author: '' };
                this.openReader(guide);
            },

            openReader(guide) {
                this.currentGuide = guide;
                this.currentPdfUrl = '/guide-books/' + guide.id + '/pdf';
                this.openReaderModal = true;
                this.isScreenMasked = false;

                // Track view count in background
                fetch('/guide-books/' + guide.id, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success && data.guide) {
                            guide.views_count = data.guide.views_count;
                            if (this.allGuides[guide.id]) {
                                this.allGuides[guide.id].views_count = data.guide.views_count;
                            }
                        }
                    })
                    .catch(() => {});
            },

            openEditById(id) {
                const guide = this.allGuides[id];
                if (guide) {
                    this.openEdit(guide);
                } else {
                    fetch('/guide-books/' + id, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success && data.guide) {
                                this.openEdit(data.guide);
                            }
                        })
                        .catch(err => alert('Gagal memuat data panduan: ' + err.message));
                }
            },

            confirmDeleteById(id) {
                const guide = this.allGuides[id];
                const name = guide ? guide.title : 'Dokumen PDF';
                if (confirm('Apakah Anda yakin ingin menghapus buku panduan:\n"' + name + '"?\n\nTindakan ini tidak dapat dibatalkan.')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/guide-books/' + id, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: new URLSearchParams({ '_method': 'DELETE' })
                    })
                    .then(res => res.json())
                    .then(() => {
                        window.location.href = '/?tab=guides';
                    })
                    .catch(err => alert('Gagal menghapus dokumen: ' + err.message));
                }
            },

            closeReader() {
                this.openReaderModal = false;
                this.currentGuide = null;
                this.currentPdfUrl = '';
                this.isScreenMasked = false;
            },

            clearClipboard() {
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText('⚠️ AKSES SCREENSHOT DITOLAK: Dokumen ini berstatus CONFIDENTIAL. Kewenangan dan hak distribusi dokumen sepenuhnya berada di Tim Business Analyst CV Generasi Sugih Sejahtera.');
                    }
                } catch (_) {}
            },

            handleGlobalKeydown(e) {
                if (!this.openReaderModal) return;

                // 1. Anti-Screenshot: PrintScreen key detection
                if (e.key === 'PrintScreen' || e.keyCode === 44 || e.key === 'Snapshot') {
                    e.preventDefault();
                    e.stopPropagation();
                    this.isScreenMasked = true;
                    this.clearClipboard();
                    alert('⚠️ AKSES SCREENSHOT DITOLAK!\n\nPengambilan tangkapan layar dinonaktifkan untuk dokumen ini.\nKewenangan dan hak akses dokumen sepenuhnya berada di Tim Business Analyst CV Generasi Sugih Sejahtera.');
                    return false;
                }

                // 2. Anti-Snipping Tool: Windows Key / Win+Shift+S / Meta key detection
                if (e.key === 'Meta' || e.keyCode === 91 || e.keyCode === 92 || e.metaKey) {
                    this.isScreenMasked = true;
                    this.clearClipboard();
                }

                // 3. Browser screenshot shortcuts: Ctrl+Shift+S (Edge Web Capture / Firefox)
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'S' || e.key === 's')) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.isScreenMasked = true;
                    this.clearClipboard();
                    alert('⚠️ AKSES SCREENSHOT DITOLAK!\n\nPengambilan tangkapan layar dinonaktifkan untuk dokumen ini.\nKewenangan dan hak akses dokumen sepenuhnya berada di Tim Business Analyst CV Generasi Sugih Sejahtera.');
                    return false;
                }

                // 4. Anti-Print: Block Ctrl+P and Cmd+P
                if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P' || e.keyCode === 80)) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('⚠️ Peringatan: Dokumen PDF ini dilindungi (DRM). Fitur cetak (Print) dinonaktifkan.');
                    return false;
                }

                // 5. Anti-Download / Save: Block Ctrl+S and Cmd+S
                if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S' || e.keyCode === 83)) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('⚠️ Peringatan: Dokumen PDF ini dilindungi (DRM). Pengunduhan berkas dinonaktifkan.');
                    return false;
                }

                // 6. Block DevTools inspection keys (F12, Ctrl+Shift+I, Ctrl+Shift+C, Ctrl+U)
                if (e.key === 'F12' || 
                    ((e.ctrlKey || e.metaKey) && (e.key === 'u' || e.key === 'U')) ||
                    ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'C' || e.key === 'J'))) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Escape key closes reader modal
                if (e.key === 'Escape') {
                    this.closeReader();
                }
            },

            doSaveGuide() {
                if (this.formSubmitting) return;
                this.formError = null;

                // Step 1: Validate Target Division
                const targetDivision = this.getEffectiveDivision();
                if (!targetDivision || targetDivision === 'Kustom / Divisi Lainnya') {
                    this.formError = 'Langkah 1: Silakan pilih Target Divisi / Tim (atau ketik divisi kustom).';
                    document.getElementById('guideFormScrollArea')?.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                // Secondary Target Role
                const targetRole = this.getEffectiveRole() || 'Semua Karyawan (Umum)';

                // Step 2: Validate System
                const systemName = this.getEffectiveSystem();
                if (!systemName || systemName === 'Kustom / Sistem Lainnya') {
                    this.formError = 'Langkah 2: Silakan pilih Sistem Terkait (atau ketik sistem kustom).';
                    document.getElementById('guideFormScrollArea')?.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                // Step 3: Validate Feature Name
                const featName = (this.formData.feature_name || '').trim();
                if (!featName) {
                    this.formError = 'Langkah 3: Silakan isi Fitur yang Sedang Dijelaskan (atau klik rekomendasi chip).';
                    document.getElementById('guideFormScrollArea')?.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                // Step 4: Ensure Title
                if (!this.formData.title || !this.formData.title.trim()) {
                    this.updateTitleAuto();
                }

                this.formSubmitting = true;

                try {
                    const formEl = document.getElementById('guideBookForm');
                    const fd = formEl ? new FormData(formEl) : new FormData();

                    // Explicitly set verified fields
                    fd.set('target_division', targetDivision);
                    fd.set('target_role', targetRole);
                    fd.set('system_name', systemName);
                    fd.set('feature_name', featName);
                    fd.set('title', this.formData.title.trim());
                    fd.set('category', this.formData.category || '');
                    fd.set('version', this.formData.version || 'v1.0');
                    fd.set('author', this.formData.author || 'Tim IT Syamanah');
                    fd.set('summary', this.formData.summary || '');
                    fd.set('content', this.formData.content || '');
                    fd.set('document_url', this.formData.document_url || '');
                    fd.set('tags', this.formData.tags || '');
                    if (this.formData.is_pinned) {
                        fd.set('is_pinned', '1');
                    } else {
                        fd.set('is_pinned', '0');
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const baseUrl = '/guide-books';
                    const targetUrl = (this.isEditing && this.formData.id) ? (baseUrl + '/' + this.formData.id) : baseUrl;
                    fd.set('_method', (this.isEditing && this.formData.id) ? 'PUT' : 'POST');

                    fetch(targetUrl, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(async (response) => {
                        let data = null;
                        try {
                            data = await response.json();
                        } catch (_) {}

                        if (!response.ok) {
                            let msg = 'Gagal menyimpan (Status ' + response.status + ' ' + response.statusText + ')';
                            if (data && data.message) {
                                msg = data.message;
                            }
                            if (data && data.errors) {
                                const errList = Object.values(data.errors).flat().join(', ');
                                msg += ': ' + errList;
                            }
                            throw new Error(msg);
                        }
                        return data;
                    })
                    .then((data) => {
                        this.formSubmitting = false;
                        this.openFormModal = false;
                        // Redirect to the guides tab with fresh load
                        window.location.href = '/?tab=guides';
                    })
                    .catch((err) => {
                        this.formSubmitting = false;
                        this.formError = err.message || 'Gagal menyimpan dan mengenkripsi dokumen PDF.';
                        document.getElementById('guideFormScrollArea')?.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                } catch (syncErr) {
                    this.formSubmitting = false;
                    this.formError = 'Kesalahan formulir: ' + (syncErr.message || syncErr);
                    document.getElementById('guideFormScrollArea')?.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            submitGuideForm(e) {
                if (e && e.preventDefault) e.preventDefault();
                this.doSaveGuide();
            },

            openAdd() {
                this.isEditing = false;
                this.isManualTitle = false;
                this.formSubmitting = false;
                this.formError = null;
                this.isScreenMasked = false;

                const availableSystems = @json($guideSystems);
                const defaultSys = availableSystems.length > 0 ? availableSystems[0] : 'Kustom / Sistem Lainnya';
                const suggestions = this.featuresMap[defaultSys] || [];
                const defaultFeat = suggestions.length > 0 ? suggestions[0] : '';

                this.formData = {
                    id: '',
                    target_division: 'Semua Divisi',
                    custom_division: '',
                    target_role: 'Semua Karyawan (Umum)',
                    custom_role: '',
                    system_name: defaultSys,
                    custom_system: '',
                    feature_name: defaultFeat,
                    title: '',
                    category: 'SOP Operasional',
                    version: 'v1.0',
                    author: '{{ Auth::check() ? Auth::user()->name : "Tim IT Syamanah" }}',
                    summary: '',
                    content: '',
                    document_url: '',
                    tags: '',
                    is_pinned: false
                };
                if (defaultFeat) {
                    this.updateTitleAuto();
                }
                this.openFormModal = true;
            },

            openEdit(guide) {
                this.isEditing = true;
                this.isManualTitle = true;
                this.formSubmitting = false;
                this.formError = null;
                this.isScreenMasked = false;

                const standardDivisions = @json($divisions);
                const isCustomDiv = guide.target_division && !standardDivisions.includes(guide.target_division) && guide.target_division !== 'Semua Divisi';

                const standardRoles = @json($guideRoles);
                const isCustomRole = guide.target_role && !standardRoles.includes(guide.target_role);

                const standardSystems = @json($guideSystems);
                const isCustomSystem = guide.system_name && !standardSystems.includes(guide.system_name);

                this.formData = {
                    id: guide.id,
                    target_division: isCustomDiv ? 'Kustom / Divisi Lainnya' : (guide.target_division || 'Semua Divisi'),
                    custom_division: isCustomDiv ? guide.target_division : '',
                    target_role: isCustomRole ? 'Kustom / Peran Lainnya' : (guide.target_role || 'Semua Karyawan (Umum)'),
                    custom_role: isCustomRole ? guide.target_role : '',
                    system_name: isCustomSystem ? 'Kustom / Sistem Lainnya' : guide.system_name,
                    custom_system: isCustomSystem ? guide.system_name : '',
                    feature_name: guide.feature_name || '',
                    title: guide.title,
                    category: guide.category || 'Hardware',
                    version: guide.version || 'v1.0',
                    author: guide.author || 'Tim IT Syamanah',
                    summary: guide.summary || '',
                    content: guide.content || '',
                    document_url: guide.document_url || '',
                    tags: Array.isArray(guide.tags) ? guide.tags.join(', ') : (guide.tags || ''),
                    is_pinned: Boolean(guide.is_pinned)
                };
                this.openFormModal = true;
            }
        };
    }
</script>