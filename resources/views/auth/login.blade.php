<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal Aset &amp; Kredensial Digital Syamanah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0F1D2E] text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-[#3A7D8A] selection:text-white relative overflow-hidden" x-data="{ showPass: false }">

    <!-- Decorative Gradient Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#3A7D8A]/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#2E5190]/25 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-slate-900/90 backdrop-blur-xl rounded-3xl p-8 border border-slate-800/90 shadow-2xl space-y-6 relative z-10">
        
        <!-- Header Brand (Syamanah Identity) -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center bg-white px-3 py-1.5 rounded-2xl shadow-sm border border-white/20">
                <img src="/logo-syamanah.png" alt="Logo Syamanah" class="h-8 w-auto object-contain" onerror="this.style.display='none'">
              
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-wide">PORTAL ASET &amp; KREDENSIAL</h1>
                <p class="text-xs text-slate-400 mt-1">Sistem Otentikasi Terpusat Berbasis RBAC Spatie</p>
            </div>
        </div>

        <!-- Flash & Validation Error Alerts -->
        @if($errors->any())
            <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs flex items-center gap-2.5">
                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 bg-blue-500/10 border border-blue-500/30 rounded-2xl text-blue-300 text-xs flex items-center gap-2.5">
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-300 text-xs flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Standard Secure Login Form -->
        <form action="{{ url('/login') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">Username Akun</label>
                <div class="relative">
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username Anda..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700/80 text-white placeholder-slate-500 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:border-transparent focus:outline-hidden transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="Masukkan password Anda..." class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700/80 text-white placeholder-slate-500 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:border-transparent focus:outline-hidden transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-200 transition" tabindex="-1">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none text-[11px] text-slate-400">
                    <input type="checkbox" name="remember" checked class="w-3.5 h-3.5 rounded bg-slate-800 border-slate-700 text-[#3A7D8A] focus:ring-0">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-lg shadow-teal-900/30 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                <span>Masuk ke Sistem Portal</span>
            </button>
        </form>

        <!-- Footer Notice -->
        <div class="pt-4 text-center text-[11px] text-slate-500 border-t border-slate-800/80">
            &copy; {{ date('Y') }} PT Generasi Sugih Sejahtera &middot; CV Syamanah Berkah Abadi<br>
            <a href="https://syamanah.com" target="_blank" rel="noreferrer" class="text-amber-400 hover:text-amber-300 font-semibold hover:underline mt-0.5 inline-block">syamanah.com</a>
        </div>
    </div>
</body>
</html>
