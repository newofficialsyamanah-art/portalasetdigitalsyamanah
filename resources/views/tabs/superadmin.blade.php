<!-- Tab: Pusat Kontrol Superadmin (CRUD Semua Modul) -->
<div x-show="activeTab === 'superadmin'" x-cloak class="space-y-6" x-data="{
    subTab: 'divisions',
    newDivisionName: '',
    editDivisionData: null,
    editingUser: null,
    showNewUserModal: false
}">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-[#0F1D2E] to-slate-900 rounded-3xl p-6 sm:p-7 shadow-xl border border-amber-500/30 text-white flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-lg shrink-0">
                👑
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-black text-lg sm:text-xl text-white tracking-wide">Pusat Kontrol Superadmin</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 uppercase">Master Admin</span>
                </div>
                <p class="text-xs text-slate-300 mt-1">Kelola master divisi/tim, hak akses pengguna, integrasi Google Apps Script, dan CRUD penuh seluruh modul</p>
            </div>
        </div>

        <!-- Sub-Nav Pill Buttons -->
        <div class="flex flex-wrap items-center gap-1.5 bg-slate-800/80 p-1.5 rounded-2xl border border-slate-700">
            <button @click="subTab = 'divisions'" :class="subTab === 'divisions' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                🏢 Kelola Divisi &amp; Tim
            </button>
            <button @click="subTab = 'users'" :class="subTab === 'users' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                👥 Kelola Pengguna &amp; Role
            </button>
            <button @click="subTab = 'config'" :class="subTab === 'config' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                ⚙️ Integrasi Google Apps Script
            </button>
        </div>
    </div>

    <!-- Sub-Tab 1: Kelola Divisi & Tim -->
    <div x-show="subTab === 'divisions'" class="space-y-4">
        <!-- Add New Division Box -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-sm text-slate-900">Tambah Divisi / Tim Baru</h3>
                <p class="text-xs text-slate-500">Divisi baru akan otomatis muncul di seluruh formulir input PC, HP, dan MikroTik</p>
            </div>

            <form action="{{ route('divisions.store') }}" method="POST" class="flex gap-2 w-full sm:w-auto">
                @csrf
                <input type="text" name="name" required placeholder="Nama divisi baru..." class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs w-full sm:w-64 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <button type="submit" class="px-4 py-2 bg-[#3A7D8A] hover:bg-[#2E5190] text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0">
                    + Tambah Divisi
                </button>
            </form>
        </div>

        <!-- Divisions Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Divisi / Tim</th>
                        <th class="py-3.5 px-4 text-center">Total Unit PC</th>
                        <th class="py-3.5 px-4 text-center">Total HP Sales</th>
                        <th class="py-3.5 px-4 text-center">Voucher MikroTik</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($divisions as $idx => $divName)
                        @php
                            $pcCount = $submissions->where('departemen', $divName)->where('type', 'pc')->count();
                            $hpCount = $submissions->where('departemen', $divName)->where('type', 'akun')->count();
                            $mktCount = $mikrotikAccounts->where('tim', $divName)->count();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $divName }}</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-600">{{ $pcCount }} Unit</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-600">{{ $hpCount }} Unit</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-600">{{ $mktCount }} Akun</td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('divisions.destroy', urlencode($divName)) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $divName }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus Divisi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sub-Tab 2: Kelola Pengguna & Role -->
    <div x-show="subTab === 'users'" x-cloak class="space-y-4">
        <!-- Add User Box -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <h3 class="font-bold text-sm text-slate-900 mb-3">Daftarkan Pengguna / Admin Baru</h3>
            <form action="{{ route('superadmin.users.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" required placeholder="budis" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Minimal 4 karakter" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Peran / Role</label>
                    <select name="role" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-hidden">
                        <option value="manager">Manager Operasional</option>
                        <option value="general_affair">General Affair (GA)</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="col-span-full flex justify-end pt-1">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition">
                        + Daftarkan Pengguna
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Username Login</th>
                        <th class="py-3.5 px-4 text-center">Peran / Hak Akses</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $idx => $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $u->name }}</td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">&#64;{{ $u->username }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $u->role === 'superadmin' ? 'bg-amber-100 text-amber-800' : ($u->role === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-teal-100 text-teal-800') }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if(Auth::id() !== $u->id)
                                    <form action="{{ route('superadmin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus Pengguna">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Akun Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sub-Tab 3: Integrasi Google Apps Script -->
    <div x-show="subTab === 'config'" x-cloak class="space-y-4">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm max-w-2xl">
            <h3 class="font-bold text-sm text-slate-900 mb-1">Pengaturan Integrasi Spreadsheet &amp; Drive</h3>
            <p class="text-xs text-slate-500 mb-4">Pastikan URL Web App Google Apps Script sudah diset ke "Anyone" agar sinkronisasi 1:1 berjalan lancar.</p>

            <form action="{{ route('superadmin.config.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Google Apps Script Web App URL</label>
                    <input type="url" name="gasUrl" value="{{ $config['gasUrl'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Spreadsheet ID</label>
                    <input type="text" name="spreadsheetId" value="{{ $config['spreadsheetId'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Drive Folder ID</label>
                    <input type="text" name="driveFolderId" value="{{ $config['driveFolderId'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">PIN Rahasia Superadmin</label>
                    <input type="text" name="superadminPin" value="{{ $config['superadminPin'] ?? '1234' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white font-bold text-xs shadow-sm transition">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

