<!-- Tab: Pusat Kontrol Superadmin & RBAC Spatie (CRUD Semua Modul) -->
<div x-show="activeTab === 'superadmin'" x-cloak class="space-y-6" x-data="{
    subTab: 'users',
    showEditUserModal: false,
    editUserData: {
        id: '',
        name: '',
        username: '',
        email: '',
        role: 'general_affair',
        password: ''
    },
    openEditUser(u) {
        this.editUserData = {
            id: u.id,
            name: u.name,
            username: u.username,
            email: u.email || '',
            role: u.role || (u.roles && u.roles[0] ? u.roles[0].name : 'general_affair'),
            password: ''
        };
        this.showEditUserModal = true;
    }
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
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 uppercase">RBAC Spatie Active</span>
                </div>
                <p class="text-xs text-slate-300 mt-1">Kelola master divisi/tim, hak akses pengguna, integrasi Google Apps Script, dan CRUD penuh seluruh modul</p>
            </div>
        </div>

        <!-- Sub-Nav Pill Buttons -->
        <div class="flex flex-wrap items-center gap-1.5 bg-slate-800/80 p-1.5 rounded-2xl border border-slate-700">
            <button @click="subTab = 'users'" :class="subTab === 'users' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                👥 Kelola Pengguna
            </button>
            <button @click="subTab = 'rbac'" :class="subTab === 'rbac' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                🛡️ Hak Akses &amp; Peran (RBAC)
            </button>
            <button @click="subTab = 'divisions'" :class="subTab === 'divisions' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                🏢 Kelola Divisi &amp; Tim
            </button>
            <button @click="subTab = 'config'" :class="subTab === 'config' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                ⚙️ Integrasi Spreadsheet
            </button>
        </div>
    </div>

    <!-- Sub-Tab 1: Kelola Pengguna (User CRUD) -->
    <div x-show="subTab === 'users'" class="space-y-6">
        <!-- Add User Box -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                        ➕
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Daftarkan Pengguna / Admin Baru</h3>
                        <p class="text-[11px] text-slate-500">Buat akun login baru dengan peran dan kredensial yang ditentukan</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('superadmin.users.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" required placeholder="budis" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="budi@syamanah.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required placeholder="Min 4 karakter" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Peran (Role RBAC)</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ strtoupper(str_replace('_', ' ', $r->name)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-full flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-105 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Daftarkan Akun Pengguna</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Daftar Akun Pengguna Aktif</h3>
                    <p class="text-[11px] text-slate-500">Klik tombol Edit untuk memperbarui nama, username, email, peran, atau mereset kata sandi</p>
                </div>
                <span class="text-xs font-mono bg-slate-100 text-slate-700 px-3 py-1 rounded-full font-bold">
                    {{ $users->count() }} Pengguna Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Nama Pengguna</th>
                            <th class="py-3.5 px-4">Username Login</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4 text-center">Peran (Role)</th>
                            <th class="py-3.5 px-4 text-center">Hak Akses Spatie</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $idx => $u)
                            @php
                                $userRole = $u->roles->first() ? $u->roles->first()->name : $u->role;
                                $permCount = $u->getAllPermissions()->count();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $u->name }}
                                    @if(Auth::id() === $u->id)
                                        <span class="ml-1.5 text-[9px] px-1.5 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold uppercase">Akun Anda</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">&#64;{{ $u->username }}</td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono">{{ $u->email ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $userRole === 'superadmin' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($userRole === 'manager' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-teal-100 text-teal-800 border border-teal-300') }}">
                                        {{ str_replace('_', ' ', $userRole) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-700">
                                        🛡️ {{ $permCount }} Permissions
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Edit & Reset Password -->
                                        <button type="button" @click="openEditUser({{ json_encode($u) }})" class="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="Edit Pengguna / Reset Password">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        @if(Auth::id() !== $u->id)
                                            <form action="{{ route('superadmin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Pengguna">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pengguna & Reset Password -->
    <div x-show="showEditUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showEditUserModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        ✏️
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Edit Pengguna &amp; Password</h3>
                        <p class="text-xs text-slate-500">Perbarui data profil, peran, atau kata sandi</p>
                    </div>
                </div>
                <button @click="showEditUserModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/superadmin/users/' + editUserData.id" method="POST" class="py-4 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editUserData.name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" x-model="editUserData.username" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" x-model="editUserData.email" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Peran (Role RBAC)</label>
                    <select name="role" x-model="editUserData.role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ strtoupper(str_replace('_', ' ', $r->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <label class="block font-semibold text-slate-700 mb-1">Reset Password Baru (Opsional)</label>
                    <input type="password" name="password" x-model="editUserData.password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    <span class="text-[10px] text-slate-400 mt-1 block">Biarkan kosong jika tetap menggunakan password saat ini</span>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showEditUserModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub-Tab 2: Matriks Hak Akses & Peran Spatie RBAC -->
    <div x-show="subTab === 'rbac'" x-cloak class="space-y-6">
        @php
            $permissionMeta = [
                'view credentials' => [
                    'title' => 'Melihat Kredensial & Sandi Akun',
                    'desc' => 'Dapat melihat password email perusahaan, media sosial, dan akun rahasia.',
                    'icon' => '👁️'
                ],
                'manage users' => [
                    'title' => 'Kelola Pengguna & Hak Akses',
                    'desc' => 'Menambah user baru, mengubah peran (role), dan mereset password akun.',
                    'icon' => '👥'
                ],
                'manage divisions' => [
                    'title' => 'Kelola Master Divisi / Tim',
                    'desc' => 'Menambah dan menghapus master departemen operasional Syamanah.',
                    'icon' => '🏢'
                ],
                'manage inventory' => [
                    'title' => 'Kelola Inventaris Laptop, PC & HP',
                    'desc' => 'Input spesifikasi hardware baru, mengubah aset, dan import DxDiag.',
                    'icon' => '💻'
                ],
                'manage mikrotik' => [
                    'title' => 'Kelola Hotspot MikroTik',
                    'desc' => 'Generate voucher hotspot, kelola limit bandwidth, dan akun internet.',
                    'icon' => '📡'
                ],
                'delete records' => [
                    'title' => 'Hapus Data & Submisi',
                    'desc' => 'Dapat menghapus baris data laptop, PC, HP, dan akun voucher secara permanen.',
                    'icon' => '🗑️'
                ],
            ];
        @endphp

        <!-- Add Custom Role Box -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                        🛡️
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Buat Peran (Role) Baru</h3>
                        <p class="text-[11px] text-slate-500">Tambahkan tingkatan hak akses kustom sesuai kebutuhan struktur organisasi</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('superadmin.roles.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Peran Baru (Role Name)</label>
                        <input type="text" name="name" required placeholder="Contoh: it_support, supervisor, auditor" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div class="self-end">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-105 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                            <span>+ Buat Role Baru</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-2">Pilih Hak Akses Awal untuk Role Baru:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($allPermissions as $perm)
                            @php
                                $meta = $permissionMeta[$perm->name] ?? [
                                    'title' => $perm->name,
                                    'desc' => 'Izin akses sistem ' . $perm->name,
                                    'icon' => '🔑'
                                ];
                            @endphp
                            <label class="p-3 rounded-2xl border border-slate-200 hover:border-purple-300 bg-slate-50/50 hover:bg-purple-50/30 transition flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="mt-0.5 rounded text-purple-600 focus:ring-0">
                                <div>
                                    <div class="font-bold text-slate-800 text-[11px]">{{ $meta['icon'] }} {{ $meta['title'] }}</div>
                                    <div class="text-[10px] text-slate-500 leading-tight mt-0.5">{{ $meta['desc'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Role Permissions Matrix Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Matriks Hak Akses Spatie per Peran</h3>
                    <p class="text-[11px] text-slate-500">Centang atau hilangkan centang hak akses untuk setiap peran, lalu klik tombol simpan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @foreach($roles as $r)
                    @php
                        $isCoreRole = in_array($r->name, ['superadmin', 'manager', 'general_affair']);
                        $rolePermNames = $r->permissions->pluck('name')->toArray();
                    @endphp
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl {{ $r->name === 'superadmin' ? 'bg-amber-100 text-amber-800' : ($r->name === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-teal-100 text-teal-800') }} flex items-center justify-center font-black text-base shrink-0">
                                    {{ $r->name === 'superadmin' ? '👑' : ($r->name === 'manager' ? '👔' : '🏢') }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wide">{{ str_replace('_', ' ', $r->name) }}</h4>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isCoreRole ? 'bg-slate-100 text-slate-600' : 'bg-purple-100 text-purple-700' }}">
                                            {{ $isCoreRole ? 'Sistem Bawaan' : 'Role Kustom' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 font-mono">Total Hak Akses: {{ count($rolePermNames) }} dari {{ $allPermissions->count() }} izin aktif</p>
                                </div>
                            </div>

                            @if(!$isCoreRole)
                                <form action="{{ route('superadmin.roles.destroy', $r->name) }}" method="POST" onsubmit="return confirm('Hapus role {{ $r->name }}? Pengguna dengan role ini akan dialihkan ke General Affair.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Hapus Role</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Form Update Permissions for Role -->
                        <form action="{{ route('superadmin.roles.permissions', $r->name) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($allPermissions as $perm)
                                    @php
                                        $hasPerm = in_array($perm->name, $rolePermNames);
                                        $meta = $permissionMeta[$perm->name] ?? [
                                            'title' => $perm->name,
                                            'desc' => 'Izin akses sistem ' . $perm->name,
                                            'icon' => '🔑'
                                        ];
                                    @endphp
                                    <label class="p-3 rounded-2xl border {{ $hasPerm ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200 bg-slate-50/30 opacity-70' }} hover:opacity-100 transition flex items-start gap-2.5 cursor-pointer select-none">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" {{ $hasPerm ? 'checked' : '' }} class="mt-0.5 rounded text-amber-600 focus:ring-0">
                                        <div>
                                            <div class="font-bold text-slate-800 text-[11px]">{{ $meta['icon'] }} {{ $meta['title'] }}</div>
                                            <div class="text-[10px] text-slate-500 leading-tight mt-0.5">{{ $meta['desc'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="px-4 py-2 bg-[#3A7D8A] hover:bg-[#2E5190] text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Simpan Hak Akses Role {{ strtoupper(str_replace('_', ' ', $r->name)) }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sub-Tab 3: Kelola Divisi & Tim -->
    <div x-show="subTab === 'divisions'" x-cloak class="space-y-4">
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

    <!-- Sub-Tab 4: Integrasi Google Apps Script -->
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
