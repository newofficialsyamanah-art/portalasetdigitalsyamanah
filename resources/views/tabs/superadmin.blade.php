<!-- Tab: Pusat Kontrol Superadmin & RBAC Spatie (CRUD Semua Modul) -->
<div x-show="activeTab === 'superadmin'" x-cloak class="mt-0">
    <!-- Header Utama Pusat Kontrol -->
    <div class="mb-2 sm:mb-2.5 bg-gradient-to-r from-slate-900 via-[#0F1D2E] to-slate-900 rounded-2xl p-2.5 sm:p-3 shadow-sm border border-amber-500/30 text-white flex flex-col md:flex-row items-center justify-between gap-2.5">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-base shadow-xs shrink-0">
                👑
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-black text-xs sm:text-sm text-white tracking-wide">Pusat Kontrol Superadmin</h2>
                    <span class="text-[9px] font-bold px-2 py-0.2 rounded-full bg-amber-500 text-slate-950 uppercase">RBAC Active</span>
                </div>
                <p class="text-[10px] text-slate-300 mt-0.5">Kelola akun pengguna, wewenang RBAC Spatie, master sistem, dan divisi</p>
            </div>
        </div>

        <!-- Sub-Nav Pill Buttons (Fokus Master & RBAC Admin) -->
        <div class="flex flex-wrap items-center gap-1 bg-slate-800/80 p-1 rounded-xl border border-slate-700">
            @if(Auth::user()->hasRole('superadmin') || Auth::user()->can('manage users'))
            <button @click="setSubTab('users')" :class="subTab === 'users' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                👥 Kelola Pengguna
            </button>
            @endif

            @if(Auth::user()->hasRole('superadmin') || Auth::user()->can('manage rbac'))
            <button @click="setSubTab('rbac')" :class="subTab === 'rbac' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                🛡️ Hak Akses &amp; Peran (RBAC)
            </button>
            @endif

            @if(Auth::user()->hasRole('superadmin') || Auth::user()->can('manage systems'))
            <button @click="setSubTab('systems')" :class="subTab === 'systems' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>🖥️ Kelola Sistem &amp; Fitur</span>
            </button>
            @endif

            @if(Auth::user()->hasRole('superadmin') || Auth::user()->can('manage divisions'))
            <button @click="setSubTab('divisions')" :class="subTab === 'divisions' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                🏢 Kelola Divisi &amp; Tim
            </button>
            @endif
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SUB-TAB 1: KELOLA PENGGUNA (User CRUD)                         -->
    <!-- ============================================================== -->
    <div x-show="subTab === 'users'" x-cloak class="space-y-2.5">
        <!-- Add User Box -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-100">
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
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-105 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
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
                                $badgeClass = match($userRole) {
                                    'superadmin' => 'bg-amber-100 text-amber-800 border-amber-300',
                                    'business_analyst' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                                    'manager' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'general_affair' => 'bg-teal-100 text-teal-800 border-teal-300',
                                    default => 'bg-purple-100 text-purple-800 border-purple-300'
                                };
                                $roleIcon = match($userRole) {
                                    'superadmin' => '👑',
                                    'business_analyst' => '💼',
                                    'manager' => '👔',
                                    'general_affair' => '🏢',
                                    default => '🛡️'
                                };
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
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $badgeClass }} inline-flex items-center gap-1">
                                        <span>{{ $roleIcon }}</span>
                                        <span>{{ str_replace('_', ' ', $userRole) }}</span>
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
                                        <button type="button" @click="openEditUser({{ json_encode($u) }})" class="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Pengguna / Reset Password">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        @if(Auth::id() !== $u->id)
                                            <form action="{{ route('superadmin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Pengguna">
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

    <!-- ============================================================== -->
    <!-- SUB-TAB 2: MATRIKS HAK AKSES & PERAN SPATIE RBAC (SCREEN FIT)   -->
    <!-- ============================================================== -->
    <div x-show="subTab === 'rbac'" x-cloak class="space-y-2" x-data="{ selectedRole: 'business_analyst' }">
        @php
            $permissionMeta = $permissionMeta ?? \App\Services\RbacService::getPermissionsDefinition();
            
            // Kategori icons & badge styling
            $categoryMeta = [
                'Inventaris Perangkat' => ['icon' => '💻', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                'Keamanan & Kredensial' => ['icon' => '🛡️', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200'],
                'Jaringan & Hotspot' => ['icon' => '📶', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200'],
                'Dokumentasi & SOP' => ['icon' => '📚', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
                'Master Superadmin' => ['icon' => '🖥️', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200'],
                'Pusat Kontrol Akun' => ['icon' => '👥', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                'Integrasi Sistem' => ['icon' => '⚡', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200'],
                'Olah Data' => ['icon' => '📗', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ];

            // Standar rekomendasi hak akses per peran sistem
            $rolePresets = [
                'superadmin' => array_keys($permissionMeta),
                'business_analyst' => [
                    'view guidebooks',
                    'manage guidebooks',
                    'manage systems',
                    'view credentials',
                    'manage spreadsheet',
                    'manage excel',
                    'manage inventory',
                    'manage divisions',
                ],
                'manager' => [
                    'manage inventory',
                    'view credentials',
                    'manage mikrotik',
                    'view guidebooks',
                    'manage guidebooks',
                    'manage systems',
                    'manage divisions',
                    'manage excel',
                    'delete records',
                ],
                'general_affair' => [
                    'manage inventory',
                    'view guidebooks',
                ],
            ];

            $roleDescriptions = [
                'superadmin' => 'Master Bypass 13/13 izin • Otoritas penuh tak terbatas',
                'business_analyst' => 'DRM SOP, Master Sistem, Olah Data & Kredensial',
                'manager' => 'Manajerial inventaris aset, SOP, divisi & export data',
                'general_affair' => 'Pendataan fisik perangkat PC & HP tanpa kredensial',
            ];
        @endphp

        <!-- Bar Pemilih Peran (Horizontal Compact 1-Bar, Menghilangkan Space Kosong di Atas) -->
        <div class="flex flex-wrap items-center justify-between gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80 shadow-inner">
            <div class="flex items-center gap-1.5 overflow-x-auto">
                @foreach($roles as $r)
                    @php
                        $rIcon = match($r->name) {
                            'superadmin' => '👑',
                            'business_analyst' => '💼',
                            'manager' => '👔',
                            'general_affair' => '🏢',
                            default => '🛡️'
                        };
                        $isSuper = $r->name === 'superadmin';
                        $pCount = $isSuper ? count($permissionMeta) : $r->permissions->count();
                    @endphp
                    <button type="button" 
                            @click="selectedRole = '{{ $r->name }}'"
                            :class="selectedRole === '{{ $r->name }}' 
                                ? 'bg-white border-[#3A7D8A] text-slate-900 shadow-sm font-black ring-2 ring-[#3A7D8A]/20' 
                                : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-semibold border-transparent'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                        <span>{{ $rIcon }}</span>
                        <span class="uppercase tracking-wide text-[11px]">{{ str_replace('_', ' ', $r->name) }}</span>
                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-mono font-bold"
                              :class="selectedRole === '{{ $r->name }}' ? 'bg-[#3A7D8A] text-white' : 'bg-slate-200 text-slate-700'">
                            {{ $pCount }}/13
                        </span>
                    </button>
                @endforeach
            </div>

            <button type="button" @click="showCreateRoleModal = true" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs shrink-0 cursor-pointer">
                <span>➕ Tambah Role Kustom</span>
            </button>
        </div>

        <!-- Matriks Hak Akses Peran Aktif (Full Width 4-Kolom Dense Grid, Langsung Tampil Pas di Layar) -->
        <div>
            @foreach($roles as $r)
                @php
                    $isCoreRole = in_array($r->name, ['superadmin', 'business_analyst', 'manager', 'general_affair']);
                    $isSuperadmin = $r->name === 'superadmin';
                    $rolePermNames = $r->permissions->pluck('name')->toArray();
                    $rolePermCount = count($rolePermNames);
                    $totalPermsCount = count($permissionMeta);
                    $preset = $rolePresets[$r->name] ?? null;
                    $rHeaderIconBg = match($r->name) {
                        'superadmin' => 'bg-amber-100 text-amber-800',
                        'business_analyst' => 'bg-indigo-100 text-indigo-800',
                        'manager' => 'bg-blue-100 text-blue-800',
                        'general_affair' => 'bg-teal-100 text-teal-800',
                        default => 'bg-purple-100 text-purple-800'
                    };
                    $rIcon = match($r->name) {
                        'superadmin' => '👑',
                        'business_analyst' => '💼',
                        'manager' => '👔',
                        'general_affair' => '🏢',
                        default => '🛡️'
                    };
                    $rDesc = $roleDescriptions[$r->name] ?? 'Role kustom operasional sistem';
                @endphp

                <div x-show="selectedRole === '{{ $r->name }}'" x-cloak
                     class="bg-white rounded-2xl p-4 border {{ $isSuperadmin ? 'border-amber-300 shadow-md ring-1 ring-amber-200/50' : ($r->name === 'business_analyst' ? 'border-indigo-300 shadow-md ring-1 ring-indigo-200/50' : 'border-slate-200/90 shadow-sm') }} space-y-3"
                     x-data="{
                         applyPreset(permsArray) {
                             let form = document.getElementById('form-role-{{ $r->name }}');
                             if (!form) return;
                             form.querySelectorAll('input[type=checkbox]').forEach(cb => {
                                 cb.checked = permsArray.includes(cb.value);
                             });
                         },
                         checkAll() {
                             let form = document.getElementById('form-role-{{ $r->name }}');
                             if (!form) return;
                             form.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
                         },
                         uncheckAll() {
                             let form = document.getElementById('form-role-{{ $r->name }}');
                             if (!form) return;
                             form.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
                         }
                     }">
                    
                    <!-- Panel Header: Title + Toolbar + Primary Save Button -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl {{ $rHeaderIconBg }} flex items-center justify-center font-black text-base shrink-0 shadow-xs">
                                {{ $rIcon }}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 uppercase tracking-wide">{{ str_replace('_', ' ', $r->name) }}</h4>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isCoreRole ? 'bg-slate-100 text-slate-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $isCoreRole ? 'Core Role' : 'Kustom' }}
                                    </span>
                                    @if($isSuperadmin)
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                            Master Bypass
                                        </span>
                                    @elseif($r->name === 'business_analyst')
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            DRM &amp; SOP Authority
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                                    {{ $rDesc }} &bull; <strong class="{{ $rolePermCount > 0 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $isSuperadmin ? $totalPermsCount : $rolePermCount }}</strong> dari {{ $totalPermsCount }} izin aktif
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 self-start sm:self-center">
                            @if(!$isSuperadmin)
                                <button type="button" @click="checkAll()" class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-[11px] transition cursor-pointer" title="Centang seluruh izin">
                                    ✓ Semua
                                </button>
                                <button type="button" @click="uncheckAll()" class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-[11px] transition cursor-pointer" title="Kosongkan seluruh izin">
                                    ✕ Kosong
                                </button>
                                @if($preset)
                                    <button type="button" @click="applyPreset({{ json_encode($preset) }})" class="px-2 py-1 rounded-lg border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100 text-indigo-700 font-semibold text-[11px] transition cursor-pointer" title="Kembalikan ke rekomendasi sistem">
                                        🔄 Rekomendasi
                                    </button>
                                @endif

                                <!-- Primary Save Button in top toolbar via HTML5 form attribute -->
                                <button type="submit" form="form-role-{{ $r->name }}" class="px-3.5 py-1 rounded-lg bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Simpan</span>
                                </button>
                            @endif

                            @if(!$isCoreRole)
                                <form action="{{ route('superadmin.roles.destroy', $r->name) }}" method="POST" onsubmit="return confirm('Hapus role kustom {{ $r->name }}? Pengguna dengan role ini akan dialihkan ke General Affair.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition flex items-center cursor-pointer" title="Hapus Role">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($isSuperadmin)
                        <!-- Superadmin Informational Alert -->
                        <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-2 text-xs text-amber-900">
                            <span class="text-sm shrink-0">👑</span>
                            <div>
                                <strong class="font-bold text-xs text-amber-950">Hak Akses Superadmin Permanen (Master Bypass)</strong>
                                <span class="text-[11px] text-amber-800 ml-1">Seluruh 13 wewenang sistem aktif permanen agar administrator sistem tidak pernah terkunci keluar.</span>
                            </div>
                        </div>

                        <!-- Read-only Display of Active Permissions (Dense 4-Kolom Grid) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                            @foreach($permissionMeta as $permName => $meta)
                                @php
                                    $cMeta = $categoryMeta[$meta['category'] ?? ''] ?? ['badge' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <div class="p-2 rounded-xl border border-emerald-200 bg-emerald-50/40 flex items-start gap-2">
                                    <span class="text-emerald-600 font-bold text-xs shrink-0 mt-0.5">✓</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 text-[11px] truncate">{{ $meta['icon'] }} {{ $meta['title'] }}</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 line-clamp-1">{{ $meta['desc'] }}</p>
                                        <div class="mt-1 flex items-center justify-between">
                                            <span class="font-mono text-[8px] text-slate-400">{{ $permName }}</span>
                                            <span class="text-[8px] font-semibold px-1 py-0.2 rounded border {{ $cMeta['badge'] }}">{{ $meta['category'] ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Form Update Permissions for Role (Dense 4-Kolom Grid) -->
                        <form id="form-role-{{ $r->name }}" action="{{ route('superadmin.roles.permissions', $r->name) }}" method="POST" class="space-y-2.5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                                @foreach($permissionMeta as $permName => $meta)
                                    @php
                                        $hasPerm = in_array($permName, $rolePermNames);
                                        $cMeta = $categoryMeta[$meta['category'] ?? ''] ?? ['badge' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                    @endphp
                                    <label class="group relative p-2 rounded-xl border transition-all flex items-start gap-2 cursor-pointer select-none {{ $hasPerm ? 'border-indigo-300 bg-indigo-50/40 ring-1 ring-indigo-200/50' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/40' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $permName }}" {{ $hasPerm ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-0 shrink-0">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-bold text-slate-900 text-[11px] truncate group-hover:text-indigo-950 flex items-center gap-1">
                                                    <span>{{ $meta['icon'] }}</span>
                                                    <span class="truncate">{{ $meta['title'] }}</span>
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5 line-clamp-1">{{ $meta['desc'] }}</p>
                                            <div class="mt-1 flex items-center justify-between">
                                                <span class="font-mono text-[8px] text-slate-400 truncate">{{ $permName }}</span>
                                                <span class="text-[8px] font-semibold px-1 py-0.2 rounded border {{ $cMeta['badge'] }} shrink-0">{{ $meta['category'] ?? '' }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] text-slate-400 font-mono">
                                    💡 Hak akses tersinkronisasi otomatis dengan sesi Laravel.
                                </span>
                                <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-105 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Simpan Hak Akses Role {{ strtoupper(str_replace('_', ' ', $r->name)) }}</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SUB-TAB 3: KELOLA SISTEM & FITUR (AppSystem CRUD)               -->
    <!-- ============================================================== -->
    <div x-show="subTab === 'systems'" x-cloak class="space-y-2.5">
        <!-- Add System Box -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-100">
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

    <!-- ============================================================== -->
    <!-- SUB-TAB 4: KELOLA DIVISI & TIM                                  -->
    <!-- ============================================================== -->
    <div x-show="subTab === 'divisions'" x-cloak class="space-y-2.5">
        <!-- Add New Division Box -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-4.5 border border-slate-100 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-sm text-slate-900">Tambah Divisi / Tim Baru</h3>
                <p class="text-xs text-slate-500">Divisi baru akan otomatis muncul di seluruh formulir input PC, HP, dan MikroTik</p>
            </div>

            <form action="{{ route('divisions.store') }}" method="POST" class="flex gap-2 w-full sm:w-auto">
                @csrf
                <input type="text" name="name" required placeholder="Nama divisi baru..." class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs w-full sm:w-64 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                <button type="submit" class="px-4 py-2 bg-[#3A7D8A] hover:bg-[#2E5190] text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
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
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition cursor-pointer" title="Hapus Divisi">
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

    <!-- ============================================================== -->
    <!-- SUPERADMIN UNIVERSAL MODALS (DILUAR FLOW space-y AGAR RAPI)     -->
    <!-- ============================================================== -->

    <!-- Modal 1: Edit Pengguna & Reset Password -->
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
                <button type="button" @click="showEditUserModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg cursor-pointer">
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
                    <button type="button" @click="showEditUserModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs shadow-md transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Sistem Terdaftar -->
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
                <button type="button" @click="showEditSystemModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg cursor-pointer">
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
                        <input type="text" name="category" x-model="editSystemData.category" list="categoryOptionsModal" placeholder="Contoh: Produksi, Sales, IT, Umum..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                        <datalist id="categoryOptionsModal">
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
                    <button type="button" @click="showEditSystemModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-[#2E5190] text-white font-bold text-xs shadow-md transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Buat Peran (Role) Baru -->
    <div x-show="showCreateRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showCreateRoleModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-xl w-full p-6 text-slate-800 animate-in fade-in zoom-in-95 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-base shadow-xs">
                        🛡️
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Buat Peran (Role) Spatie Baru</h3>
                        <p class="text-xs text-slate-500">Tambahkan tingkatan hak akses kustom sesuai kebutuhan organisasi</p>
                    </div>
                </div>
                <button type="button" @click="showCreateRoleModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('superadmin.roles.store') }}" method="POST" class="space-y-4 text-xs" x-ref="newRoleFormModal">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Peran Baru (Role Name) *</label>
                    <input type="text" name="name" required placeholder="Contoh: auditor, supervisor, data_entry" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    <p class="text-[10px] text-slate-400 mt-1">Nama peran akan otomatis diubah menjadi format slug huruf kecil (contoh: <code>data_entry</code>).</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="font-bold text-slate-800 text-xs">Pilih Hak Akses Awal:</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="$refs.newRoleFormModal.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)" class="px-2 py-0.5 text-[10px] font-semibold text-purple-700 hover:bg-purple-50 rounded border border-purple-200 transition cursor-pointer">
                                ✓ Pilih Semua
                            </button>
                            <button type="button" @click="$refs.newRoleFormModal.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)" class="px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-slate-100 rounded border border-slate-200 transition cursor-pointer">
                                ✕ Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="max-h-56 overflow-y-auto space-y-1 p-2 rounded-2xl bg-slate-50/70 border border-slate-100">
                        @foreach($permissionMeta as $permName => $meta)
                            <label class="p-2 rounded-xl border border-slate-200 hover:border-purple-300 bg-white transition flex items-start gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="permissions[]" value="{{ $permName }}" class="mt-0.5 rounded text-purple-600 focus:ring-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-bold text-slate-800 text-[11px] truncate">{{ $meta['icon'] }} {{ $meta['title'] }}</span>
                                        <span class="text-[9px] font-mono text-slate-400">{{ $permName }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 leading-tight mt-0.5 line-clamp-1">{{ $meta['desc'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showCreateRoleModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs shadow-md transition cursor-pointer">
                        + Simpan Role Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
