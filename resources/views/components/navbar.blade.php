<!-- Top Navbar Component -->
<header class="bg-[#0F1D2E]/95 backdrop-blur-md text-white border-b border-slate-800/90 sticky top-0 z-30 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
        
        <!-- Brand Identity -->
        <div @click="setTab('{{ Auth::check() && Auth::user()->role === 'general_affair' ? 'inventory' : 'spreadsheet' }}')" class="flex items-center gap-2.5 cursor-pointer select-none group shrink-0">
            <div class="flex items-center bg-white px-2 py-1.5 rounded-xl shadow-xs border border-white/20 group-hover:scale-105 transition-transform duration-200">
                <img src="/logo-syamanah.png" alt="Logo Syamanah" class="h-6 w-auto object-contain" onerror="this.style.display='none'">
            
            </div>

            <div class="hidden sm:flex flex-col">
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-white tracking-wider text-xs">
                        PORTAL ASET DIGITAL
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white">
                        Syamanah
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 font-medium">
                    CV Generasi Sugih Sejahtera
                </span>
            </div>
        </div>

        <!-- Center Navigation - Segmented Pills (Desktop lg:flex) -->
        <nav class="hidden lg:flex items-center bg-slate-900/90 p-1 rounded-full border border-slate-800 shadow-inner">
            @if(Auth::check() && Auth::user()->role === 'general_affair')
                <!-- General Affair Nav -->
                <button @click="setTab('inventory')" :class="activeTab === 'inventory' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Laptop &amp; PC</span>
                    <span class="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">{{ $deviceCount ?? 0 }}</span>
                </button>

                <button @click="setTab('phones')" :class="activeTab === 'phones' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>HP Android</span>
                    <span class="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">{{ $phoneCount ?? 0 }}</span>
                </button>

                <button @click="setTab('pc')" :class="activeTab === 'pc' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Input PC</span>
                </button>

                <button @click="openDxDiagModal = true" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 text-slate-300 hover:text-white hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>DxDiag</span>
                </button>
            @else
                <!-- Superadmin & Manager Nav -->
                <button @click="setTab('spreadsheet')" :class="activeTab === 'spreadsheet' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Spreadsheet</span>
                    <span class="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">{{ $totalSubmissions ?? 0 }}</span>
                </button>

                <button @click="setTab('inventory')" :class="activeTab === 'inventory' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Laptop &amp; PC</span>
                    <span class="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">{{ $deviceCount ?? 0 }}</span>
                </button>

                <button @click="setTab('phones')" :class="activeTab === 'phones' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>HP Sales</span>
                    <span class="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">{{ $phoneCount ?? 0 }}</span>
                </button>

                <button @click="setTab('mikrotik')" :class="activeTab === 'mikrotik' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    <span>MikroTik</span>
                </button>

                <button @click="setTab('audit')" :class="activeTab === 'audit' ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20' : 'text-slate-300 hover:text-white hover:bg-white/10'" class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Audit</span>
                </button>

                @if(Auth::check() && (Auth::user()->role === 'superadmin' || Auth::user()->can('manage users')))
                    <button @click="setTab('superadmin')" :class="activeTab === 'superadmin' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-sm ring-1 ring-amber-300/50' : 'text-amber-400 hover:text-amber-300 hover:bg-amber-500/10'" class="px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        <span>👑 Superadmin &amp; RBAC</span>
                    </button>
                @endif
            @endif
        </nav>

        <!-- Right Action: Quick Add + Profile Badge -->
        <div class="flex items-center gap-2 sm:gap-2.5">
            
            <!-- Quick Add Button (Desktop) -->
            @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                <div class="relative hidden md:block" @click.away="openQuickAdd = false">
                    <button @click="openQuickAdd = !openQuickAdd" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#3A7D8A]/30 hover:bg-[#3A7D8A]/50 border border-[#3A7D8A]/40 text-teal-200 hover:text-white text-xs font-semibold transition" title="Tambah Data Baru">
                        <svg class="w-3.5 h-3.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Tambah</span>
                        <svg class="w-3 h-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Quick Add Dropdown Menu -->
                    <div x-show="openQuickAdd" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50 text-slate-800 animate-in fade-in zoom-in-95">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pilih Formulir Input</p>
                        </div>
                        <div class="py-1 space-y-1">
                            <button @click="setTab('pc'); openQuickAdd = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <div>
                                    <div class="font-bold">Input Laptop &amp; PC</div>
                                    <div class="text-[10px] text-slate-400">Workstation &amp; DxDiag</div>
                                </div>
                            </button>

                            <button @click="setTab('akun'); openQuickAdd = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <div class="font-bold">Input HP &amp; Akun Sales</div>
                                    <div class="text-[10px] text-slate-400">IMEI, Email &amp; Sosmed</div>
                                </div>
                            </button>

                            <button @click="setTab('mikrotik'); openQuickAdd = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                <div>
                                    <div class="font-bold">Generate Hotspot MikroTik</div>
                                    <div class="text-[10px] text-slate-400">Voucher &amp; User Profile</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Dropdown -->
            @if(Auth::check())
                <div class="relative" @click.away="openProfileDropdown = false">
                    <button @click="openProfileDropdown = !openProfileDropdown" class="flex items-center gap-2 px-2.5 py-1.5 rounded-full border transition-all duration-200 {{ Auth::user()->role === 'superadmin' ? 'bg-amber-500/10 border-amber-500/30 hover:bg-amber-500/20' : (Auth::user()->role === 'manager' ? 'bg-blue-500/10 border-blue-500/30 hover:bg-blue-500/20' : 'bg-teal-500/10 border-teal-500/30 hover:bg-teal-500/20') }}">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center font-extrabold text-[11px] uppercase shadow-xs shrink-0 {{ Auth::user()->role === 'superadmin' ? 'bg-amber-500 text-slate-950' : (Auth::user()->role === 'manager' ? 'bg-blue-600 text-white' : 'bg-[#3A7D8A] text-white') }}">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>

                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-bold text-white leading-tight truncate max-w-[110px]">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-[9px] font-semibold leading-tight">
                                @if(Auth::user()->role === 'superadmin')
                                    <span class="text-amber-400 font-bold">👑 Superadmin</span>
                                @elseif(Auth::user()->role === 'manager')
                                    <span class="text-blue-300">👔 Manager</span>
                                @else
                                    <span class="text-teal-300">🏢 General Affair</span>
                                @endif
                            </span>
                        </div>

                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div x-show="openProfileDropdown" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 p-3 z-50 text-slate-800 animate-in fade-in zoom-in-95">
                        <div class="p-2 border-b border-slate-100 pb-3">
                            <p class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">&#64;{{ Auth::user()->username }}</p>
                            <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                PERAN: {{ strtoupper(Auth::user()->role) }}
                            </div>
                        </div>

                        <div class="py-2 space-y-1">
                            @if(Auth::user()->role === 'superadmin' || Auth::user()->can('manage users'))
                                <button @click="setTab('superadmin'); openProfileDropdown = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold hover:bg-amber-50 flex items-center gap-2.5 text-amber-900 transition">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    <span>👑 Pusat Kontrol Superadmin &amp; RBAC</span>
                                </button>
                            @endif

                            <button @click="openLoginModal = true; openProfileDropdown = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                <span>Ganti Akun / Peran</span>
                            </button>

                            <button @click="openGasModal = true; openProfileDropdown = false" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Setup Google Apps Script</span>
                            </button>
                        </div>

                        <div class="border-t border-slate-100 pt-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2.5 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <button @click="openLoginModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition">
                    <span>Masuk</span>
                </button>
            @endif

            <!-- Mobile Menu Button (Hamburger) -->
            <button @click="openMobileMenu = true" class="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 border border-white/10 transition" aria-label="Buka menu navigasi">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div x-show="openMobileMenu" x-cloak class="fixed inset-0 z-50 flex justify-end">
    <div @click="openMobileMenu = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

    <aside class="relative w-full max-w-xs sm:max-w-sm bg-[#0F1D2E] border-l border-slate-800 text-white shadow-2xl flex flex-col h-full z-10 animate-in slide-in-from-right duration-200">
        <!-- Drawer Header -->
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="bg-white px-2 py-1 rounded-lg">
                    <span class="font-black text-[#0F1D2E] text-xs font-sans">SYAMANAH</span>
                </div>
                <span class="font-bold text-sm text-white">Menu Navigasi</span>
            </div>
            <button @click="openMobileMenu = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Role Notice -->
        <div class="p-4 bg-slate-900/80 border-b border-slate-800 text-xs">
            <p class="text-slate-400 text-[11px]">Sedang Masuk Sebagai:</p>
            <p class="font-bold text-white text-sm mt-0.5">{{ Auth::user()->name ?? 'Tamu' }}</p>
            <div class="mt-1.5">
                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase">
                    👑 {{ Auth::user()->role ?? 'TAMU' }}
                </span>
            </div>
        </div>

        <!-- Drawer Links -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 text-xs">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">Inventaris &amp; Modul</p>
                <div class="space-y-1">
                    <button @click="setTab('spreadsheet'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>📊 Data Spreadsheet 1:1</span>
                        <span class="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">{{ $totalSubmissions ?? 0 }}</span>
                    </button>
                    <button @click="setTab('inventory'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>💻 Laptop &amp; PC Workstation</span>
                        <span class="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">{{ $deviceCount ?? 0 }}</span>
                    </button>
                    <button @click="setTab('phones'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>📱 HP Android Sales</span>
                        <span class="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">{{ $phoneCount ?? 0 }}</span>
                    </button>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">Jaringan &amp; Audit</p>
                <div class="space-y-1">
                    <button @click="setTab('mikrotik'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>📶 Hotspot Jaringan MikroTik</span>
                    </button>
                    <button @click="setTab('audit'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>🛡️ Audit Kredensial Perusahaan</span>
                    </button>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">Input &amp; Tambah Aset</p>
                <div class="space-y-1">
                    <button @click="setTab('pc'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>➕ Form Input Laptop &amp; PC</span>
                    </button>
                    <button @click="setTab('akun'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition">
                        <span>➕ Form Input HP &amp; Akun Sales</span>
                    </button>
                </div>
            </div>

            @if(Auth::check() && (Auth::user()->role === 'superadmin' || Auth::user()->can('manage users')))
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400/80 px-2 mb-1.5">Kontrol Superadmin &amp; RBAC</p>
                    <div class="space-y-1">
                        <button @click="setTab('superadmin'); openMobileMenu = false" class="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-bold bg-amber-500/20 text-amber-300 transition">
                            <span>👑 Pusat Kontrol &amp; RBAC</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Drawer Bottom Action -->
        <div class="p-4 border-t border-slate-800 bg-slate-900/60 flex items-center justify-between">
            <button @click="openLoginModal = true; openMobileMenu = false" class="flex items-center gap-2 text-xs font-bold text-slate-300 hover:text-white">
                <span>Ganti Akun</span>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 text-xs font-bold text-red-400 hover:text-red-300">
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>
</div>

