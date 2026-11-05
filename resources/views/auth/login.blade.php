<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal Aset Digital Syamanah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0F1D2E] text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900/90 rounded-3xl p-7 border border-slate-800 shadow-2xl space-y-6">
        
        <!-- Header Brand -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center bg-white px-3 py-1.5 rounded-2xl shadow-sm border border-white/20">
                <img src="/logo-syamanah.png" alt="Logo Syamanah" class="h-8 w-auto object-contain" onerror="this.style.display='none'">
             
            </div>
            <h1 class="text-lg font-black text-white tracking-wide">PORTAL ASET &amp; KREDENSIAL</h1>
            <p class="text-xs text-slate-400">CV Generasi Sugih Sejahtera</p>
        </div>

        @if($errors->any())
            <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 bg-blue-500/10 border border-blue-500/30 rounded-2xl text-blue-300 text-xs">
                {{ session('info') }}
            </div>
        @endif

        <!-- Quick 1-Click Role Access -->
        <div class="space-y-2.5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilih Cepat Peran Akses:</p>
            
            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="superadmin">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-xs">👑</div>
                        <div>
                            <div class="font-bold text-xs text-white">Superadmin</div>
                            <div class="text-[10px] text-amber-300">Akses penuh CRUD seluruh modul &amp; divisi</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-amber-400 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>

            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="manager">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-xs">👔</div>
                        <div>
                            <div class="font-bold text-xs text-white">Manager Operasional</div>
                            <div class="text-[10px] text-blue-300">Lihat semua akun, inventaris &amp; kredensial</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-400 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>

            <form action="{{ route('auth.switch-role') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="general_affair">
                <button type="submit" class="w-full text-left p-3 rounded-2xl border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#3A7D8A] text-white font-bold flex items-center justify-center text-xs">🏢</div>
                        <div>
                            <div class="font-bold text-xs text-white">General Affair (GA)</div>
                            <div class="text-[10px] text-teal-300">Spesifikasi fisik hardware, sandi terlindungi</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-teal-400 group-hover:translate-x-1 transition-transform">&rarr;</span>
                </button>
            </form>
        </div>

        <div class="relative flex py-1 items-center">
            <div class="grow border-t border-slate-800"></div>
            <span class="shrink mx-3 text-[10px] font-bold uppercase text-slate-500">atau login manual</span>
            <div class="grow border-t border-slate-800"></div>
        </div>

        <!-- Manual Login Form -->
        <form action="{{ url('/login') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Username</label>
                <input type="text" name="username" required placeholder="superadmin / manager / ga" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Password</label>
                <input type="password" name="password" required placeholder="password123" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition">
                Masuk ke Portal
            </button>
        </form>

        <div class="pt-2 text-center text-[11px] text-slate-500 border-t border-slate-800">
            &copy; {{ date('Y') }} CV Generasi Sugih Sejahtera &middot; <a href="https://syamanah.com" target="_blank" class="text-amber-400 hover:underline">syamanah.com</a>
        </div>
    </div>
</body>
</html>

