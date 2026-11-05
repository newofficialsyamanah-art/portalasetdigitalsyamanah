<!-- Multi-Column Syamanah Footer Component -->
<footer class="bg-[#0F1D2E] text-slate-300 border-t border-slate-800 mt-20 pt-14 pb-8 text-xs relative">
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#3A7D8A] via-[#2E5190] to-amber-500"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 pb-12 border-b border-slate-800/80">
            
            <!-- Column 1: Brand & Perusahaan -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-white px-2.5 py-1.5 rounded-xl shadow-xs border border-white/20">
                        <img src="/logo-syamanah.png" alt="Logo Syamanah" class="h-7 w-auto object-contain" onerror="this.style.display='none'">
                    
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-white text-sm tracking-wide">
                            PORTAL ASET &amp; KREDENSIAL
                        </span>
                    </div>
                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white uppercase tracking-wider">
                        CV GENERASI SUGIH SEJAHTERA
                    </span>
                    <p class="text-slate-400 text-xs leading-relaxed pt-1">
                        Sistem inventarisasi terpusat berbasis web untuk PC Workstation, Smartphone Operasional, Akun Digital, dan Hotspot Jaringan MikroTik.
                    </p>
                </div>

                <div class="pt-2 text-slate-400 text-[11px] space-y-1.5 border-t border-slate-800/60">
                    <div class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-[#3A7D8A] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Jl. Maleber Utara No. 528 / 187B, Kota Bandung, Jawa Barat</span>
                    </div>
                    <div>
                        <a href="https://syamanah.com" target="_blank" rel="noreferrer" class="inline-flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-semibold hover:underline">
                            <span>Kunjungi syamanah.com</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 2: Navigasi Modul -->
            <div class="space-y-3.5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#3A7D8A]"></span>
                    Katalog &amp; Modul Aset
                </h4>
                <ul class="space-y-2 text-xs">
                    <li>
                        <button @click="setTab('inventory')" class="w-full text-left flex items-center justify-between py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                            <span>💻 Laptop &amp; PC Desktop</span>
                            <span class="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">{{ $deviceCount ?? 0 }} Unit</span>
                        </button>
                    </li>
                    <li>
                        <button @click="setTab('phones')" class="w-full text-left flex items-center justify-between py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                            <span>📱 HP Android Sales</span>
                            <span class="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">{{ $phoneCount ?? 0 }} Unit</span>
                        </button>
                    </li>
                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                        <li>
                            <button @click="setTab('spreadsheet')" class="w-full text-left flex items-center justify-between py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                                <span>📊 Master Spreadsheet 1:1</span>
                                <span class="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">{{ $totalSubmissions ?? 0 }}</span>
                            </button>
                        </li>
                        <li>
                            <button @click="setTab('mikrotik')" class="w-full text-left flex items-center justify-between py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                                <span>📶 Hotspot MikroTik</span>
                            </button>
                        </li>
                        <li>
                            <button @click="setTab('audit')" class="w-full text-left flex items-center justify-between py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                                <span>🛡️ Audit Kredensial</span>
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Column 3: Pusat Input & Panduan -->
            <div class="space-y-3.5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Pusat Input &amp; Panduan
                </h4>
                <ul class="space-y-2 text-xs">
                    <li>
                        <button @click="setTab('pc')" class="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition">
                            <span class="text-[#3A7D8A]">➕</span>
                            <span>Input Laptop / PC Baru</span>
                        </button>
                    </li>
                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                        <li>
                            <button @click="setTab('akun')" class="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition">
                                <span class="text-amber-500">➕</span>
                                <span>Input HP &amp; Akun Sales</span>
                            </button>
                        </li>
                    @endif
                    <li>
                        <button @click="openDxDiagModal = true" class="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition">
                            <span class="text-blue-400">❓</span>
                            <span>Panduan Cek DxDiag Windows</span>
                        </button>
                    </li>
                    @if(!Auth::check() || Auth::user()->role !== 'general_affair')
                        <li>
                            <button @click="openGasModal = true" class="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition">
                                <span class="text-teal-400">☁️</span>
                                <span>Setup Web App GAS &amp; Sheet</span>
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Column 4: Keamanan & Status -->
             @role('superadmin')
            <div class="space-y-3.5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Status Sistem &amp; Akun
                </h4>

                <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Database Engine:</span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-400 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Laravel SQLite 1:1
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Privasi Sandi:</span>
                        <span class="inline-flex items-center gap-1 text-slate-300 font-semibold">
                            🔒 RBAC Terisolasi
                        </span>
                    </div>
                    @endrole

                    <div class="pt-2 border-t border-slate-800 text-[11px]">
                        <span class="text-slate-400 block mb-1">Masuk Sebagai:</span>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white truncate max-w-[120px]">
                                {{ Auth::user()->name ?? 'Tamu' }}
                            </span>
                            <button @click="openLoginModal = true" class="text-[10px] font-bold text-amber-400 hover:text-amber-300 hover:underline flex items-center gap-1">
                                <span>Ganti Akun</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-1">
                    <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="w-full py-2 px-3 rounded-xl bg-slate-800/80 hover:bg-slate-750 text-slate-300 hover:text-white font-medium text-[11px] flex items-center justify-center gap-2 transition border border-slate-700/50">
                        <span>↑ Kembali ke Atas</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="center pt-6 flex flex-col sm:flex-row items-center justify-center gap-4 text-slate-400 text-[11px] text-center">
    <div class="flex flex-wrap items-center justify-center gap-2">
        <span>
            © {{ date('Y') }} <strong>CV Generasi Sugih Sejahtera</strong>
        </span>
        <span class="hidden sm:inline">·</span>
        <span>Hak Cipta Dilindungi Undang-Undang.</span>
    </div>

    <!-- Hapus atau sembunyikan pembatas dot jika ingin benar-benar sentris, atau biarkan jika ingin tetap ada -->
    <div class="flex items-center justify-center gap-3">
        <a href="https://syamanah.com" target="_blank" rel="noreferrer" class="text-[#3A7D8A] hover:text-teal-300 font-semibold hover:underline">
            syamanah.com →
        </a>
    </div>
    
</div>
    </div>
</footer>

