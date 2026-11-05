<!-- Tab: MikroTik Hotspot Generator & List -->
<div x-show="activeTab === 'mikrotik'" x-cloak class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-lg shrink-0">
                📶
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Generator Akun MikroTik Hotspot</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pembuatan voucher WiFi kantor otomatis, pembatasan kecepatan, dan perintah terminal RouterOS</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Search -->
            <div class="relative flex-1 sm:w-64">
                <input type="text" x-model="searchTerm" placeholder="Cari username, pemilik, tim..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Generator Form & Quick Add -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 space-y-4" x-data="{
        namaPemilik: '',
        tim: 'Sales Jaket',
        jabatan: 'Staff',
        username: '',
        password: Math.floor(100000 + Math.random() * 900000).toString(),
        rateLimit: '10M/10M',
        profile: 'sales_jaket',
        generateCredentials() {
            const prefix = this.tim.substring(0, 2).toUpperCase();
            const rand = Math.floor(10 + Math.random() * 89);
            this.username = prefix + rand;
            this.password = Math.floor(100000 + Math.random() * 900000).toString();
        }
    }" x-init="generateCredentials()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                Buat Akun Hotspot Baru
            </h3>
            <button type="button" @click="generateCredentials()" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 flex items-center gap-1">
                <span>🔄 Acak Ulang Kredensial</span>
            </button>
        </div>

        <form action="{{ route('mikrotik.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik Akun</label>
                <input type="text" name="namaPemilik" x-model="namaPemilik" required placeholder="Contoh: Teh Silvi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Divisi / Tim Sales</label>
                <select name="tim" x-model="tim" @change="generateCredentials()" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    @foreach($divisions as $div)
                        <option value="{{ $div }}">{{ $div }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Username MikroTik</label>
                <input type="text" name="username" x-model="username" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-teal-800 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Password</label>
                <input type="text" name="password" x-model="password" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
            </div>

            <div class="col-span-full pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                    <span>⚡ Generate &amp; Simpan Akun</span>
                </button>
            </div>
        </form>
    </div>

    <!-- MikroTik Accounts Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pemilik &amp; Tim</th>
                        <th class="py-3.5 px-4">Username &amp; Password</th>
                        <th class="py-3.5 px-4">Profile &amp; Rate Limit</th>
                        <th class="py-3.5 px-4">Command Terminal RouterOS</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mikrotikAccounts as $index => $m)
                        <tr x-show="searchTerm === '' || '{{ strtolower($m->nama_pemilik . ' ' . $m->tim . ' ' . $m->username) }}'.includes(searchTerm.toLowerCase())" class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $index + 1 }}</td>
                            
                            <!-- Nama & Tim -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $m->nama_pemilik }}</div>
                                <div class="text-[11px] text-slate-500">{{ $m->tim }} &middot; <span class="text-slate-400">{{ $m->jabatan }}</span></div>
                            </td>

                            <!-- Credentials -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-teal-800">{{ $m->username }}</div>
                                <div class="font-mono text-slate-500 text-[11px]">Pass: {{ $m->password }}</div>
                            </td>

                            <!-- Profile & Rate Limit -->
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded-md font-mono text-[10px] bg-slate-100 text-slate-700 font-semibold">
                                    {{ $m->profile_hotspot }}
                                </span>
                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $m->rate_limit }}</div>
                            </td>

                            <!-- Command Terminal -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <code class="bg-slate-900 text-teal-300 px-2.5 py-1 rounded-lg text-[10px] font-mono truncate max-w-[200px] block">
                                        {{ $m->command_terminal }}
                                    </code>
                                    <button @click="copyToClipboard('{{ addslashes($m->command_terminal) }}', 'cmd_{{ $m->id }}')" class="p-1 text-slate-400 hover:text-teal-600 transition" title="Salin Perintah RouterOS">
                                        <span x-show="copiedKey !== 'cmd_{{ $m->id }}'">📋</span>
                                        <span x-show="copiedKey === 'cmd_{{ $m->id }}'" x-cloak class="text-emerald-600 font-bold">✓</span>
                                    </button>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $m->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ ucfirst($m->status) }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('mikrotik.destroy', $m->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun MikroTik ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus Akun">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                Belum ada akun MikroTik yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

