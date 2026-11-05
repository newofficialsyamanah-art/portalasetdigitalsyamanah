<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Portal Aset Digital Syamanah') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        syamanah: {
                            navy: '#0F1D2E',
                            teal: '#3A7D8A',
                            royal: '#2E5190',
                            amber: '#D9730D',
                            bg: '#F8F7F5',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F7F5] text-slate-800 min-h-screen flex flex-col antialiased" x-data="portalApp()">

    <!-- Include Header / Navbar -->
    @include('components.navbar')

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full space-y-6">
        
        <!-- Flash Alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl border bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm flex items-start justify-between gap-3 transition-all">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5">Berhasil!</h4>
                        <p class="text-xs leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl border bg-rose-50 border-rose-300 text-rose-900 shadow-sm flex items-start justify-between gap-3 transition-all">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5">Peringatan / Error</h4>
                        <p class="text-xs leading-relaxed">{{ session('error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <!-- General Affair Banner Notice -->
        @if(Auth::check() && Auth::user()->role === 'general_affair')
            <div class="bg-gradient-to-r from-teal-500/10 via-teal-600/5 to-transparent border-l-4 border-[#3A7D8A] p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-slate-700 text-xs shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#3A7D8A]/10 text-[#3A7D8A] flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold text-[#0F1D2E] text-sm">Mode Akses General Affair (GA)</span>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Menampilkan inventaris fisik, nama pengguna, dan spesifikasi hardware (Laptop, PC Desktop, HP Android). Seluruh data akun &amp; kredensial perusahaan terlindungi.
                        </p>
                    </div>
                </div>
                <button @click="openLoginModal = true" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-[#0F1D2E] hover:bg-slate-50 font-bold text-xs shrink-0 transition shadow-2xs self-start sm:self-auto">
                    Ganti Akun
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Include Footer -->
    @include('components.footer')

    <!-- Include Universal Modals -->
    @include('components.modals')

    <script>
        function portalApp() {
            return {
                activeTab: '{{ $activeTab ?? 'spreadsheet' }}',
                searchTerm: '',
                filterDept: 'all',
                openLoginModal: false,
                openQuickAdd: false,
                openMobileMenu: false,
                openProfileDropdown: false,
                openDxDiagModal: false,
                openGasModal: false,
                openDriveModal: false,
                openEditModal: false,
                openDeleteModal: false,
                editItem: {},
                deleteTarget: {},
                copiedKey: null,
                copyToClipboard(text, key) {
                    navigator.clipboard.writeText(text);
                    this.copiedKey = key;
                    setTimeout(() => { this.copiedKey = null; }, 2000);
                },
                setTab(tab) {
                    this.activeTab = tab;
                    window.history.replaceState(null, '', '?tab=' + tab);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    </script>
</body>
</html>

