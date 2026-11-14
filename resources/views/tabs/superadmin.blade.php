<!-- Tab: Pusat Kontrol Superadmin & RBAC Spatie (CRUD Semua Modul) -->
<div x-show="activeTab === 'superadmin'" x-cloak class="space-y-6">
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
            <button @click="subTab = 'systems'" :class="subTab === 'systems' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                <span>🖥️ Kelola Sistem &amp; Fitur</span>
            </button>
            <button @click="subTab = 'divisions'" :class="subTab === 'divisions' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                🏢 Kelola Divisi &amp; Tim
            </button>
            <button @click="subTab = 'config'" :class="subTab === 'config' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition">
                ⚙️ Integrasi Spreadsheet
            </button>
            <button @click="subTab = 'erpnext'" :class="subTab === 'erpnext' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                <span>⚡ Integrasi ERPNext</span>
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

    <!-- Modal Edit Sistem Terdaftar -->
    <div x-show="showEditSystemModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showEditSystemModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 text-slate-800 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-base">
                        ✏️
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Perbarui Sistem &amp; Fitur</h3>
                        <p class="text-xs text-slate-500">Edit data master sistem terdaftar di lingkungan kerja</p>
                    </div>
                </div>
                <button type="button" @click="showEditSystemModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/superadmin/systems/' + editSystemData.id" method="POST" class="py-4 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="grid grid-cols-3 gap-2.5">
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Sistem *</label>
                        <input type="text" name="name" x-model="editSystemData.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Icon / Emoji</label>
                        <input type="text" name="icon" x-model="editSystemData.icon" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-center font-emoji focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori (Bebas Ketik)</label>
                        <input type="text" name="category" x-model="editSystemData.category" list="categoryOptions" placeholder="Contoh: Produksi, Sales, IT, Umum..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Urutan Tampilan</label>
                        <input type="number" name="order_num" x-model="editSystemData.order_num" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Daftar Fitur / Modul Rekomendasi (Pisahkan per baris)</label>
                    <textarea name="features" x-model="editSystemData.featuresText" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi Sistem</label>
                    <input type="text" name="description" x-model="editSystemData.description" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-900 text-[11px] leading-relaxed">
                    💡 <strong>Otomatisasi:</strong> Jika nama sistem diubah, seluruh dokumen SOP yang sebelumnya terikat pada sistem ini otomatis ikut diperbarui.
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showEditSystemModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-[#2E5190] text-white font-bold text-xs shadow-md transition">
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
                'manage guidebooks' => [
                    'title' => 'Kelola Buku Panduan & SOP Sistem',
                    'desc' => 'Dapat menambah, mengubah, dan menghapus dokumen SOP serta buku panduan sistem.',
                    'icon' => '📚'
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

    <!-- Sub-Tab: Kelola Sistem & Fitur (AppSystem CRUD) -->
    <div x-show="subTab === 'systems'" x-cloak class="space-y-6">
        
        <!-- Add System Box -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-base">
                        🖥️
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Tambah Sistem Terdaftar Baru</h3>
                        <p class="text-[11px] text-slate-500">Sistem baru akan otomatis menjadi pilihan resmi pada pembuatan Buku Panduan &amp; SOP, modul aset, dan fitur rekomendasi</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('superadmin.systems.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                @csrf
                <div class="lg:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Nama Sistem / Aplikasi *</label>
                    <input type="text" name="name" required placeholder="Contoh: Sistem Absensi & Payroll" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Icon / Emoji</label>
                    <input type="text" name="icon" placeholder="Contoh: 💻, ⏱️, ⚡, 📶, 📱" value="💻" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-center font-emoji focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori Sistem (Bebas Ketik)</label>
                    <input type="text" name="category" list="categoryOptions" placeholder="Contoh: Produksi, Sales, IT, Umum..." value="Operasional" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    <datalist id="categoryOptions">
                        <option value="Operasional">
                        <option value="Produksi &amp; Sablon">
                        <option value="Sales &amp; CS">
                        <option value="Finance &amp; Kasir">
                        <option value="IT &amp; Jaringan">
                        <option value="Gudang &amp; Logistik">
                        <option value="HRD &amp; Personalia">
                        <option value="Umum">
                    </datalist>
                </div>

                <div class="col-span-full">
                    <label class="block font-semibold text-slate-700 mb-1">Daftar Fitur / Modul Rekomendasi (Pisahkan per baris baru atau koma)</label>
                    <textarea name="features" rows="2" placeholder="Contoh:&#10;Presensi Masuk &amp; Pulang GPS&#10;Rekap Lembur Bulanan&#10;Pengajuan Cuti Online" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden font-mono"></textarea>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Fitur ini akan muncul otomatis sebagai tombol rekomendasi sekali-klik (chip) saat menulis panduan SOP</span>
                </div>

                <div class="col-span-full">
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi Singkat Sistem</label>
                    <input type="text" name="description" placeholder="Penjelasan singkat tujuan dan fungsi sistem ini di lingkungan kantor..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                </div>

                <div class="col-span-full flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-105 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <span>+ Daftarkan Sistem Baru</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Systems Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Daftar Master Sistem Terdaftar</h3>
                    <p class="text-[11px] text-slate-500">Seluruh sistem di bawah dikelola langsung oleh Superadmin dan menjadi master dropdown resmi pada modul panduan</p>
                </div>
                <div class="flex items-center gap-2.5">
                    @if($appSystems->count() > 0)
                        <button type="button" 
                                @click="if (confirm('Apakah Anda yakin ingin MENGHAPUS SEMUA master sistem sekaligus? Seluruh data bawaan akan dibersihkan.')) window.location.href = '{{ route('superadmin.systems.clearAll') }}'" 
                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <span>🗑️ Bersihkan Semua Sistem</span>
                        </button>
                    @endif
                    <span class="text-xs font-mono bg-teal-50 text-teal-800 px-3 py-1.5 rounded-xl font-bold border border-teal-200">
                        {{ $appSystems->count() }} Sistem Aktif
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Sistem &amp; Kategori</th>
                            <th class="py-3.5 px-4">Fitur Rekomendasi Terdaftar</th>
                            <th class="py-3.5 px-4 text-center">Dokumen SOP</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($appSystems as $idx => $sys)
                            @php
                                $sopCount = $guideBooks->where('system_name', $sys->name)->count();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-lg shrink-0 border border-slate-200">
                                            {{ $sys->icon ?: '💻' }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-xs">{{ $sys->name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono px-2 py-0.2 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $sys->category }}
                                                </span>
                                                @if($sys->description)
                                                    <span class="text-[10px] text-slate-400 truncate max-w-xs">{{ $sys->description }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="flex flex-wrap gap-1">
                                        @if(is_array($sys->features) && count($sys->features) > 0)
                                            @foreach(array_slice($sys->features, 0, 3) as $f)
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200 font-medium truncate max-w-[140px]">
                                                    {{ $f }}
                                                </span>
                                            @endforeach
                                            @if(count($sys->features) > 3)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-mono">
                                                    +{{ count($sys->features) - 3 }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Belum ada fitur kustom</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono text-xs font-bold {{ $sopCount > 0 ? 'text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full' : 'text-slate-400' }}">
                                        {{ $sopCount }} Dokumen
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $sys->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $sys->is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" @click="openEditSystem({{ json_encode($sys) }})" class="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Sistem">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <button type="button" 
                                                @click="if (confirm('Apakah Anda yakin ingin menghapus sistem \'{{ addslashes($sys->name) }}\'? Tindakan ini permanen.')) window.location.href = '/superadmin/systems/{{ $sys->id }}/delete'" 
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                                                title="Hapus Sistem">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                    Belum ada sistem terdaftar. Silakan tambahkan sistem baru di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

    <!-- Sub-Tab 5: Integrasi ERPNext (Frappe REST API) -->
    <div x-show="subTab === 'erpnext'" x-cloak class="space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm max-w-3xl">
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#3A7D8A] to-[#2E5190] text-white flex items-center justify-center font-black text-lg shadow-md">
                        ⚡
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Integrasi ERPNext (Frappe REST API)</h3>
                        <p class="text-xs text-slate-500">Koneksikan Enterprise Asset Management ke instance ERPNext kantor via REST API</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold {{ !empty($config['erpnextUrl']) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                    <span class="w-2 h-2 rounded-full {{ !empty($config['erpnextUrl']) ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ !empty($config['erpnextUrl']) ? 'Terkonfigurasi' : 'Belum Terhubung' }}
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/60 mb-6 flex items-start gap-3 text-xs text-amber-900">
                <div class="text-base shrink-0 mt-0.5">💡</div>
                <div class="space-y-1">
                    <p class="font-bold">Informasi Kredensial &amp; Autentikasi ERPNext:</p>
                    <p class="text-[11px] text-amber-800">
                        API Key &amp; API Secret kantor Anda sudah dipasang secara otomatis. Silakan masukkan <strong>URL Server ERPNext</strong> kantor Anda di bawah ini, lalu klik <strong>Uji Koneksi</strong> untuk memverifikasi tautan ke server Frappe/ERPNext.
                    </p>
                </div>
            </div>

            <form action="{{ route('erpnext.config') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        URL Server ERPNext <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="erpnextUrlInput" name="erpnextUrl" value="{{ $config['erpnextUrl'] ?? '' }}" placeholder="https://erp.kantor.com atau http://192.168.1.xxx:8000" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    <p class="text-[10px] text-slate-400 mt-1">Masukkan domain publik atau IP port ERPNext kantor Anda (tanpa garis miring di akhir).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            API Key ERPNext <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="erpnextApiKeyInput" name="erpnextApiKey" value="{{ $config['erpnextApiKey'] ?? '43895e90883a899' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            API Secret ERPNext <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="erpnextApiSecretInput" name="erpnextApiSecret" value="{{ $config['erpnextApiSecret'] ?? '3d8c285da00a784' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>

                <!-- Test Connection Live Feedback -->
                <div x-show="erpTestResult" x-cloak class="mt-4 p-4 rounded-2xl border text-xs transition" :class="erpTestResult && erpTestResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <span x-text="erpTestResult && erpTestResult.success ? '✅' : '❌'"></span>
                        <span x-text="erpTestResult && erpTestResult.success ? 'Koneksi Berhasil' : 'Koneksi Gagal'"></span>
                    </div>
                    <p class="text-[11px]" x-text="erpTestResult ? erpTestResult.message : ''"></p>
                </div>

                <div class="pt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100">
                    <button type="button" @click="testErpConnection()" :disabled="erpTesting" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-2 disabled:opacity-50">
                        <svg x-show="erpTesting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="erpTesting ? 'Sedang Menguji...' : '🔌 Uji Koneksi ERPNext'"></span>
                    </button>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-105 text-white font-bold text-xs shadow-sm transition">
                        Simpan Konfigurasi ERPNext
                    </button>
                </div>
            </form>
        </div>

        <!-- Demonstration & Two-Way Sync Proof Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Card 1: Tarik Data (Pull Assets from ERPNext) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base">
                                📥
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">1. Tarik Data dari ERPNext</h3>
                                <p class="text-[11px] text-slate-500 font-mono" x-text="'GET /api/resource/' + (erpDocType || 'Asset')"></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold">GET REST API</span>
                    </div>

                    <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                        Endpoint target panggilan API:
                        <code class="block mt-1 bg-slate-100 p-2 rounded-xl font-mono text-[11px] text-teal-900 font-bold break-all border border-slate-200" x-text="(document.getElementById('erpnextUrlInput')?.value || 'https://erp.kantor.com') + '/api/resource/' + (erpDocType || 'Asset')"></code>
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">DocType ERPNext Target</label>
                            <select x-model="erpDocType" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A]">
                                <option value="Asset">Asset (Modul Aset Tetap / Fixed Assets)</option>
                                <option value="Item">Item (Master Barang &amp; Inventaris IT)</option>
                                <option value="Employee">Employee (Data Karyawan / Pemegang)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Batas Tarik Data (Limit)</label>
                            <select x-model="erpLimit" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A]">
                                <option value="50">50 Data Teratas</option>
                                <option value="100">100 Data</option>
                                <option value="250">250 Data</option>
                                <option value="500">500 Data (Rekomendasi)</option>
                                <option value="1000">1.000 Data (Seluruh Aset)</option>
                            </select>
                        </div>
                        <div class="flex items-end pb-1.5">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-slate-700 font-semibold p-2 rounded-xl bg-slate-50 border border-slate-200 hover:bg-emerald-50/50 transition w-full">
                                <input type="checkbox" x-model="erpAutoImport" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-0">
                                <span>Simpan ke Database Portal</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="button" @click="pullFromErp(false)" :disabled="erpPulling" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <svg x-show="erpPulling" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="erpPulling ? 'Sedang Menarik...' : '📥 Tarik Data Live ERP'"></span>
                        </button>
                        <button type="button" @click="pullFromErp(true)" :disabled="erpPulling" class="py-2.5 px-3 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition" title="Uji alur sinkronisasi dengan data simulasi">
                            <span>🧪 Uji Contoh Tarik</span>
                        </button>
                    </div>
                </div>

                <!-- Proof & Result Box for Pull -->
                <div x-show="erpPullResult" x-cloak class="mt-4 p-4 rounded-2xl border text-xs bg-slate-50 border-slate-200 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                        <div class="flex items-center gap-2 font-bold text-slate-900">
                            <span>Bukti Respon ERPNext:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold" :class="erpPullResult && erpPullResult.success ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" x-text="erpPullResult && erpPullResult.success ? 'HTTP 200 OK' : 'Gagal'"></span>
                        </div>
                        <span class="font-mono text-[10px] text-slate-500" x-text="erpPullResult ? erpPullResult.timestamp : ''"></span>
                    </div>

                    <template x-if="erpPullResult && erpPullResult.success">
                        <div>
                            <div class="text-[11px] text-slate-600 mb-2">
                                Ditemukan <strong class="text-emerald-700" x-text="erpPullResult.count"></strong> aset pada DocType <code class="font-mono text-emerald-800 font-bold" x-text="erpPullResult.docType"></code>
                                <template x-if="erpPullResult.importedCount > 0">
                                    <span class="inline-flex items-center gap-1.5 ml-1">
                                        &bull; <strong class="text-teal-700" x-text="erpPullResult.importedCount"></strong> berhasil disimpan ke inventaris!
                                        <button type="button" @click="window.location.reload()" class="underline text-emerald-800 font-bold ml-1 hover:text-emerald-950">Refresh Halaman</button>
                                    </span>
                                </template>
                            </div>

                            <template x-if="erpPullResult.assets && erpPullResult.assets.length > 0">
                                <div class="max-h-52 overflow-y-auto rounded-xl border border-slate-200 bg-white">
                                    <table class="w-full text-left text-[11px]">
                                        <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200 sticky top-0">
                                            <tr>
                                                <th class="p-2">ID / Name</th>
                                                <th class="p-2">Nama Aset</th>
                                                <th class="p-2">Divisi</th>
                                                <th class="p-2">Pemegang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="item in erpPullResult.assets" :key="item.name">
                                                <tr class="hover:bg-slate-50">
                                                    <td class="p-2 font-mono font-bold text-emerald-700" x-text="item.name"></td>
                                                    <td class="p-2 font-semibold text-slate-800" x-text="item.asset_name || item.item_name || '-'"></td>
                                                    <td class="p-2 text-slate-600" x-text="item.department || item.item_group || '-'"></td>
                                                    <td class="p-2 text-slate-600" x-text="item.custodian || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="erpPullResult && !erpPullResult.success">
                        <div class="space-y-3">
                            <p class="text-rose-600 font-medium" x-text="erpPullResult.message"></p>
                            
                            <!-- Role Guidance for HTTP 403 -->
                            <template x-if="erpPullResult.status_code === 403">
                                <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 space-y-2">
                                    <div class="flex items-center gap-1.5 font-bold text-amber-950">
                                        <span>🔑 Solusi Hak Akses ERPNext:</span>
                                    </div>
                                    <p class="text-[11px] leading-relaxed">
                                        Akun API Anda di ERPNext adalah: <strong class="font-mono text-slate-900" x-text="erpPullResult.logged_user || 'User API'"></strong>.
                                    </p>
                                    <ol class="list-decimal list-inside text-[11px] space-y-1 text-amber-900 pl-1">
                                        <li>Buka ERPNext kantor Anda di browser sebagai <strong>Administrator</strong>.</li>
                                        <li>Masuk ke menu <strong>Users</strong> (Pengguna) &rarr; pilih user <strong class="font-mono text-slate-900" x-text="erpPullResult.logged_user || 'terkait'"></strong>.</li>
                                        <li>Pada tabel <strong>Roles</strong>, centang role <strong class="text-emerald-800" x-text="erpPullResult.required_role || 'Asset Manager'"></strong> atau <strong>System Manager</strong>.</li>
                                        <li>Klik tombol <strong>Save</strong> di ERPNext.</li>
                                    </ol>
                                    <template x-if="erpPullResult.item_accessible">
                                        <div class="mt-2 pt-2 border-t border-amber-200 text-[11px] text-teal-800 font-semibold">
                                            💡 <strong>Tips Cepat:</strong> DocType 'Item' (Master Barang) sudah dapat diakses oleh user ini. Anda bisa langsung mengubah pilihan <strong>DocType Target</strong> di atas menjadi <strong>Item</strong> lalu klik <strong>Tarik Data Live ERP</strong>!
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Card 2: Kirim Data (Push Asset to ERPNext) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-base">
                                🚀
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">2. Kirim Data ke ERPNext</h3>
                                <p class="text-[11px] text-slate-500 font-mono" x-text="'POST /api/resource/' + (erpDocType || 'Asset')"></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200 font-bold">POST REST API</span>
                    </div>

                    <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                        Pilih aset yang ada di portal Anda atau ketik manual spesifikasinya, lalu kirim ke ERPNext untuk membuktikan pengiriman data:
                    </p>

                    <!-- Target DocType Selector for Push -->
                    <div class="mb-4">
                        <label class="block font-semibold text-slate-700 mb-1.5 text-[11px]">Pilih Modul Target di ERPNext:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition text-xs font-semibold" :class="erpDocType === 'Asset' ? 'border-[#3A7D8A] bg-teal-50/60 text-[#0F1D2E] shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-600'">
                                <input type="radio" x-model="erpDocType" value="Asset" class="text-teal-600 focus:ring-0">
                                <div>
                                    <span class="block text-xs font-bold">Modul Asset</span>
                                    <span class="block text-[10px] text-slate-500 font-normal">Fixed Assets (Akuntansi)</span>
                                </div>
                            </label>
                            <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition text-xs font-semibold" :class="erpDocType === 'Item' ? 'border-blue-600 bg-blue-50/60 text-blue-900 shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-600'">
                                <input type="radio" x-model="erpDocType" value="Item" class="text-blue-600 focus:ring-0">
                                <div>
                                    <span class="block text-xs font-bold">Modul Item (Barang)</span>
                                    <span class="block text-[10px] text-blue-700 font-normal">Master Barang &bull; Bebas Syarat</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Quick Pick From Local Submissions -->
                    <div class="space-y-3 mb-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Pilih Dari Aset Terdaftar (Otomatis Isi)</label>
                            <select @change="selectAssetForPush($event)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A]">
                                <option value="">-- Pilih Aset Terdaftar (Total {{ $submissions->count() }} Aset) --</option>
                                @foreach($submissions as $sub)
                                    @php
                                        $d = $sub->data ?? [];
                                        $ipTag = (!empty($d['ip']) && $d['ip'] !== '-') ? ' [IP: ' . $d['ip'] . ']' : '';
                                        $label = $sub->nama . ' - ' . ($d['computerName'] ?? ($d['jenisHp'] ?? $sub->jenis_perangkat)) . $ipTag . ' (' . $sub->departemen . ')';
                                    @endphp
                                    <option value="{{ $sub->id }}" 
                                            data-name="{{ $label }}"
                                            data-dept="{{ $sub->departemen }}"
                                            data-user="{{ $sub->nama }}"
                                            data-code="{{ $d['computerName'] ?? ($d['jenisHp'] ?? 'IT-ASSET') }}">
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[10px] text-slate-500">Form Spesifikasi Aset Target</span>
                            <button type="button" @click="fetchErpMasters()" :disabled="erpLoadingMasters" class="text-[10px] text-teal-700 hover:text-teal-900 font-semibold flex items-center gap-1 transition">
                                <svg x-show="erpLoadingMasters" class="animate-spin h-3 w-3 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="erpLoadingMasters ? 'Mengecek Master...' : '⚡ Cek Master Data ERP (Lokasi &amp; Item)'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div class="col-span-2">
                                <label class="block font-semibold text-slate-600 mb-0.5">Nama Aset (asset_name)</label>
                                <input type="text" x-model="erpPushForm.asset_name" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-0.5">Divisi (department)</label>
                                <input type="text" x-model="erpPushForm.department" list="erpDeptsList" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono">
                                <datalist id="erpDeptsList">
                                    <template x-if="erpMasters && erpMasters.departments">
                                        <template x-for="d in erpMasters.departments" :key="d">
                                            <option :value="d"></option>
                                        </template>
                                    </template>
                                </datalist>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-0.5">Pemegang (custodian)</label>
                                <input type="text" x-model="erpPushForm.custodian" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono" placeholder="Nama / ID Karyawan">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-0.5">Kode Item (item_code)</label>
                                <input type="text" x-model="erpPushForm.item_code" list="erpItemsList" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono" placeholder="Item Code ERP">
                                <datalist id="erpItemsList">
                                    <template x-if="erpMasters && erpMasters.items">
                                        <template x-for="it in erpMasters.items" :key="it.name">
                                            <option :value="it.name" :label="it.item_name || it.name"></option>
                                        </template>
                                    </template>
                                </datalist>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-0.5">Lokasi (location)</label>
                                <input type="text" x-model="erpPushForm.location" list="erpLocationsList" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono" placeholder="Auto-pilih jika kosong">
                                <datalist id="erpLocationsList">
                                    <template x-if="erpMasters && erpMasters.locations">
                                        <template x-for="loc in erpMasters.locations" :key="loc">
                                            <option :value="loc"></option>
                                        </template>
                                    </template>
                                </datalist>
                            </div>
                            <div class="col-span-2">
                                <label class="block font-semibold text-slate-600 mb-0.5">Nilai Perolehan / Harga (gross_purchase_amount)</label>
                                <input type="number" x-model="erpPushForm.gross_purchase_amount" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono" placeholder="Wajib > 0 di ERP (default: 1)">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="button" @click="pushToErp(false)" :disabled="erpPushing" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-[#2E5190] hover:brightness-105 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <svg x-show="erpPushing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="erpPushing ? 'Sedang Mengirim...' : '🚀 Kirim Live ke ERP'"></span>
                        </button>
                        <button type="button" @click="pushToErp(true)" :disabled="erpPushing" class="py-2.5 px-3 rounded-xl border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-900 font-bold text-xs transition" title="Uji alur pengiriman dengan data simulasi">
                            <span>🧪 Uji Contoh Kirim</span>
                        </button>
                    </div>
                </div>

                <!-- Proof & Result Box for Push -->
                <div x-show="erpPushResult" x-cloak class="mt-4 p-4 rounded-2xl border text-xs bg-slate-900 text-slate-100 space-y-3 font-mono">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <div class="flex items-center gap-2 font-bold">
                            <span>📡 Bukti Telemetri HTTP REST API:</span>
                            <span class="px-2 py-0.5 rounded text-[10px]" :class="erpPushResult && erpPushResult.success ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'" x-text="erpPushResult && erpPushResult.telemetry ? ('HTTP ' + erpPushResult.telemetry.http_status) : (erpPushResult && erpPushResult.success ? 'HTTP 200' : 'Error')"></span>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-[11px] text-slate-300">
                        <p><span class="text-slate-500">Endpoint:</span> <span class="text-amber-400" x-text="erpPushResult && erpPushResult.telemetry ? erpPushResult.telemetry.endpoint : 'POST /api/resource/Asset'"></span></p>
                        <p><span class="text-slate-500">Header:</span> <span class="text-teal-400">Authorization: token 43895e90883a899:3d8c••••</span></p>
                        <p><span class="text-slate-500">Pesan:</span> <span :class="erpPushResult && erpPushResult.success ? 'text-emerald-400' : 'text-rose-400'" x-text="erpPushResult ? erpPushResult.message : ''"></span></p>
                    </div>

                    <template x-if="erpPushResult && !erpPushResult.success && erpDocType === 'Asset'">
                        <div class="mt-2 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] font-sans">
                            💡 <strong>Tips Cepat:</strong> Jika ERP kantor belum menyiapkan kategori akuntansi Fixed Asset, Anda bisa klik tombol pilihan <strong>Modul Item (Barang)</strong> di atas lalu klik <strong>Kirim Live ke ERP</strong> untuk langsung menyimpannya ke master barang ERP tanpa syarat akuntansi!
                        </div>
                    </template>

                    <template x-if="erpPushResult && erpPushResult.telemetry && erpPushResult.telemetry.raw_response">
                        <div class="mt-2 pt-2 border-t border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Raw Response Body Dari Server ERP:</span>
                            <pre class="bg-black/60 p-2.5 rounded-xl text-[10px] overflow-x-auto text-emerald-400 max-h-36" x-text="JSON.stringify(erpPushResult.telemetry.raw_response, null, 2)"></pre>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
