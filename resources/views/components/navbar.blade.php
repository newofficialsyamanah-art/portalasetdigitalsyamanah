@php
    $user = Auth::user();
    $role = $user ? $user->role : 'general_affair';
    $isSuperadmin = $user && $role === 'superadmin';
    $isManager = $user && $role === 'manager';
    $isGA = $user && $role === 'general_affair';

    // Granular capabilities (Spatie permissions with core role bypass)
    $canSpreadsheet = $isSuperadmin || $isManager || ($user && ($user->can('manage spreadsheet') || $user->can('manage excel')));
    $canInventory = $isSuperadmin || $isManager || $isGA || ($user && $user->can('manage inventory'));
    $canPhones = $isSuperadmin || $isManager || $isGA || ($user && $user->can('manage inventory'));
    $canMikrotik = $isSuperadmin || $isManager || ($user && $user->can('manage mikrotik'));
    $canAudit = $isSuperadmin || $isManager || ($user && ($user->can('view credentials') || $user->can('manage rbac') || $user->can('manage users')));
    $canGuides = $isSuperadmin || ($user && ($user->can('view guidebooks') || $user->can('manage guidebooks')));
    $canSuperadmin = $isSuperadmin || ($user && ($user->can('manage users') || $user->can('manage rbac') || $user->can('manage systems') || $user->can('manage divisions')));
    $canExcel = $isSuperadmin || $isManager || ($user && $user->can('manage excel'));

    // Role display styling & labels
    if ($isSuperadmin) {
        $roleLabel = '👑 Superadmin';
        $roleColor = 'text-amber-400';
        $roleBorder = 'border-amber-500/30';
        $roleBg = 'bg-amber-500/10';
        $avatarBg = 'bg-amber-500 text-slate-950';
    } elseif ($isManager) {
        $roleLabel = '👔 Manager';
        $roleColor = 'text-blue-300';
        $roleBorder = 'border-blue-500/30';
        $roleBg = 'bg-blue-500/10';
        $avatarBg = 'bg-blue-600 text-white';
    } elseif ($isGA) {
        $roleLabel = '🏢 General Affair';
        $roleColor = 'text-teal-300';
        $roleBorder = 'border-teal-500/30';
        $roleBg = 'bg-teal-500/10';
        $avatarBg = 'bg-[#3A7D8A] text-white';
    } else {
        $cleanRoleName = ucwords(str_replace('_', ' ', $role));
        $roleLabel = '🛡️ ' . $cleanRoleName;
        $roleColor = 'text-purple-300';
        $roleBorder = 'border-purple-500/30';
        $roleBg = 'bg-purple-500/10';
        $avatarBg = 'bg-purple-600 text-white';
    }
@endphp

<!-- Backdrop Blur Overlay (Mobile & Tablet) -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-cloak 
     class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-30 lg:hidden transition-opacity duration-200">
</div>

<!-- ============================================================ -->
<!-- UNIFIED SLIM & CLEAN LEFT SIDEBAR (Desktop & Mobile Drawer)  -->
<!-- ============================================================ -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 w-64 bg-[#0F1D2E] text-white border-r border-slate-800/90 z-40 flex flex-col shadow-2xl transition-transform duration-200 ease-in-out select-none">
    
    <!-- Sidebar Header: Brand & App Identity -->
    <div class="px-4 py-3.5 border-b border-slate-800/90 bg-slate-950 flex items-center justify-between gap-2.5">
        <div @click="setTab('{{ $canSpreadsheet ? 'spreadsheet' : 'inventory' }}')" class="flex items-center gap-3 cursor-pointer group flex-1 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-white p-1.5 flex items-center justify-center shadow-md border border-white/20 group-hover:scale-105 transition-transform duration-200 shrink-0">
                <img src="/logo-syamanah.png" alt="Logo Syamanah" class="h-7 w-auto object-contain" onerror="this.style.display='none'">
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-white text-sm tracking-tight uppercase leading-tight">
                        EAM
                    </span>
                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-[#3A7D8A] text-white shrink-0 leading-none">
                        v2.4
                    </span>
                </div>
                <div class="font-extrabold text-[11px] text-teal-300 tracking-wide leading-tight mt-0.5">
                    Enterprise Asset Management
                </div>
                <div class="text-[9px] text-slate-400 font-medium leading-tight mt-0.5">
                    CV Generasi Sugih Sejahtera
                </div>
            </div>
        </div>

        <!-- Mobile Only Close Button (✕) - Bukan Hamburger! Sembunyi di Desktop -->
        <button @click="sidebarOpen = false" type="button" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer shrink-0" title="Tutup Menu">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Clean Navigation Links (Scrollable) -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-4 text-xs scrollbar-thin scrollbar-thumb-slate-800">
        
        <!-- SECTION 1: MASTER DATA & INVENTARIS -->
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2.5 mb-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#3A7D8A]"></span>
                <span>Menu Utama</span>
            </p>
            <div class="space-y-1">
                {{-- Spreadsheet 1:1 --}}
                @if($canSpreadsheet)
                    <button @click="setTab('spreadsheet')" 
                            :class="activeTab === 'spreadsheet' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                            class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-all duration-150 cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="truncate">Spreadsheet 1:1</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-black/40 text-slate-200 shrink-0">{{ $totalSubmissions ?? 0 }}</span>
                    </button>
                @endif

                {{-- Laptop & PC (dengan Submenu Input PC & Panduan DxDiag) --}}
                @if($canInventory)
                    <div x-data="{ open: true }" class="space-y-0.5">
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    @click="setTab('inventory')" 
                                    :class="['inventory', 'pc'].includes(activeTab) ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                                    class="flex-1 text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-all duration-150 cursor-pointer">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="truncate">Laptop &amp; PC</span>
                                </div>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-black/40 text-slate-200 shrink-0">{{ $deviceCount ?? 0 }}</span>
                            </button>
                            <button type="button" 
                                    @click="open = !open" 
                                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition cursor-pointer shrink-0" 
                                    title="Tampilkan / Sembunyikan Submenu">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <!-- Submenu: Data Perangkat, Input PC Baru, Panduan DxDiag -->
                        <div x-show="open" x-cloak class="pl-4 pr-1 py-1 space-y-0.5 border-l-2 border-slate-700/60 ml-3.5 transition-all">
                            <button type="button" 
                                    @click="setTab('inventory')" 
                                    :class="activeTab === 'inventory' ? 'text-teal-300 font-bold bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'" 
                                    class="w-full text-left px-2.5 py-1.5 rounded-lg flex items-center gap-2 transition cursor-pointer text-xs">
                                <span class="text-xs">📋</span>
                                <span class="truncate">Daftar Perangkat</span>
                            </button>

                            <button type="button" 
                                    @click="openPcModal = true; if(window.innerWidth < 1024) sidebarOpen = false;" 
                                    class="w-full text-left px-2.5 py-1.5 rounded-lg flex items-center justify-between gap-2 text-emerald-400/90 hover:text-emerald-300 hover:bg-slate-800/50 font-semibold transition cursor-pointer text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xs">➕</span>
                                    <span class="truncate">Form Input PC</span>
                                </div>
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 font-bold">Pop-up</span>
                            </button>

                            <button type="button" 
                                    @click="openDxDiagModal = true" 
                                    class="w-full text-left px-2.5 py-1.5 rounded-lg flex items-center gap-2 text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium transition cursor-pointer text-xs">
                                <span class="text-xs">📘</span>
                                <span class="truncate">Panduan DxDiag</span>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- HP Android / Akun Sales (dengan Submenu Input Akun) --}}
                @if($canPhones)
                    <div x-data="{ open: true }" class="space-y-0.5">
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    @click="setTab('phones')" 
                                    :class="['phones', 'akun'].includes(activeTab) ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                                    class="flex-1 text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-all duration-150 cursor-pointer">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span class="truncate">HP {{ $isGA ? 'Android' : 'Sales' }}</span>
                                </div>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-black/40 text-slate-200 shrink-0">{{ $phoneCount ?? 0 }}</span>
                            </button>
                            <button type="button" 
                                    @click="open = !open" 
                                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition cursor-pointer shrink-0" 
                                    title="Tampilkan / Sembunyikan Submenu">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <!-- Submenu: Data HP & Akun, Input HP & Akun -->
                        <div x-show="open" x-cloak class="pl-4 pr-1 py-1 space-y-0.5 border-l-2 border-slate-700/60 ml-3.5 transition-all">
                            <button type="button" 
                                    @click="setTab('phones')" 
                                    :class="activeTab === 'phones' ? 'text-amber-300 font-bold bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'" 
                                    class="w-full text-left px-2.5 py-1.5 rounded-lg flex items-center gap-2 transition cursor-pointer text-xs">
                                <span class="text-xs">📱</span>
                                <span class="truncate">Daftar HP &amp; Akun</span>
                            </button>

                            <button type="button" 
                                    @click="openAkunModal = true; if(window.innerWidth < 1024) sidebarOpen = false;" 
                                    class="w-full text-left px-2.5 py-1.5 rounded-lg flex items-center justify-between gap-2 text-amber-400/90 hover:text-amber-300 hover:bg-slate-800/50 font-semibold transition cursor-pointer text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xs">➕</span>
                                    <span class="truncate">Form Input Akun</span>
                                </div>
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 font-bold">Pop-up</span>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Hotspot MikroTik --}}
                @if($canMikrotik)
                    <button @click="setTab('mikrotik')" 
                            :class="activeTab === 'mikrotik' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                            class="w-full text-left px-3 py-2.5 rounded-xl flex items-center gap-2.5 transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                        <span class="truncate">Hotspot MikroTik</span>
                    </button>
                @endif

                {{-- Audit Kredensial --}}
                @if($canAudit)
                    <button @click="setTab('audit')" 
                            :class="activeTab === 'audit' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                            class="w-full text-left px-3 py-2.5 rounded-xl flex items-center gap-2.5 transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="truncate">Audit Kredensial</span>
                    </button>
                @endif

                {{-- Guide Book & SOP --}}
                @if($canGuides)
                    <button @click="setTab('guides')" 
                            :class="activeTab === 'guides' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium'" 
                            class="w-full text-left px-3 py-2.5 rounded-xl flex items-center justify-between transition-all duration-150 cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span class="truncate">Guide Book &amp; SOP</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-black/40 text-slate-200 shrink-0">{{ count($guideBooks ?? []) }}</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- SECTION 2: OLAH DATA EXCEL -->
        @if($canExcel)
            <div class="pt-2 border-t border-slate-800/60">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/90 px-2.5 mb-1.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Olah Data Excel</span>
                </p>
                <div class="space-y-1">
                    <button type="button" @click="openExportModal = true" 
                            class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium transition cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate">Ekspor Excel (.xlsx)</span>
                    </button>

                    <button type="button" @click="openImportModal = true" 
                            class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 text-slate-300 hover:text-white hover:bg-slate-800/80 font-medium transition cursor-pointer">
                        <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span class="truncate">Impor Data Excel</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- SECTION 3: KONTROL SUPERADMIN -->
        @if($canSuperadmin)
            <div class="pt-2 border-t border-slate-800/60">
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400 px-2.5 mb-1.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>Administrasi</span>
                </p>
                <div class="space-y-1">
                    <button @click="setTab('superadmin')" 
                            :class="activeTab === 'superadmin' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-black shadow-lg ring-1 ring-amber-300/40' : 'text-amber-300 hover:text-amber-200 hover:bg-amber-500/10 font-bold'" 
                            class="w-full text-left px-3 py-2.5 rounded-xl flex items-center gap-2.5 transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        <span class="truncate">👑 Pusat Kontrol &amp; RBAC</span>
                    </button>
                </div>
            </div>
        @endif

    </div>

    <!-- Sidebar Bottom: User Profile & Logout -->
    <div class="p-3.5 border-t border-slate-800 bg-slate-950/90 shrink-0 space-y-2.5">
        <!-- User Profile Badge (Diletakkan di atas tombol Logout) -->
        @if(Auth::check())
            <div class="px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-2.5 shadow-xs">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs uppercase shadow-xs shrink-0 {{ $avatarBg }}">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-white truncate leading-tight">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="text-[10px] {{ $roleColor }} font-semibold truncate leading-tight mt-0.5">
                        {{ $roleLabel }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-2 px-3 rounded-xl text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 flex items-center justify-center gap-2 transition border border-rose-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>
