<script>
    function formAkunHandler() {
        return {
            showAddDivision: false,
            newDivName: '',
            selectedDept: '{{ $divisions[0] ?? "Sales Jaket" }}',
            divisions: @json(array_values($divisions)),
            isSavingDivision: false,
            statusHp: 'Baru (Pengadaan Kantor)',
            fotoName: '',
            fotoPreview: null,
            accounts: [
                { platform: 'WhatsApp Bisnis', username: '', password: '' },
                { platform: 'Instagram', username: '', password: '' },
                { platform: 'Facebook Page', username: '', password: '' },
                { platform: 'Email Utama', username: '', password: '' }
            ],
            addAccount(platform = 'Instagram') {
                this.accounts.push({ platform: platform, username: '', password: '' });
            },
            removeAccount(index) {
                if (this.accounts.length > 1) {
                    this.accounts.splice(index, 1);
                }
            },
            handleFotoSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.fotoName = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => { this.fotoPreview = ev.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.fotoPreview = null;
                    }
                }
            },
            removeFoto() {
                this.fotoName = '';
                this.fotoPreview = null;
                const input = document.getElementById('tab_akun_foto');
                if (input) input.value = '';
            },
            init() {
                window.addEventListener('division-added', (e) => {
                    if (e.detail && e.detail.name && !this.divisions.includes(e.detail.name)) {
                        this.divisions.push(e.detail.name);
                        const sel = this.$el.querySelector('select[name="departemen"]');
                        if (sel) {
                            const opt = document.createElement('option');
                            opt.value = e.detail.name;
                            opt.textContent = e.detail.name;
                            sel.appendChild(opt);
                        }
                    }
                });
            },
            addNewDivision() {
                const name = this.newDivName.trim();
                if (!name) return;
                this.isSavingDivision = true;

                fetch('{{ route("divisions.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
                .then(({ ok, data }) => {
                    this.isSavingDivision = false;
                    if (!ok || !data.success) {
                        alert(data.message || 'Gagal menambahkan divisi baru.');
                        return;
                    }
                    if (!this.divisions.includes(name)) {
                        this.divisions.push(name);
                        const sel = this.$el.querySelector('select[name="departemen"]');
                        if (sel) {
                            const opt = document.createElement('option');
                            opt.value = name;
                            opt.textContent = name;
                            sel.appendChild(opt);
                        }
                    }
                    this.selectedDept = name;
                    this.showAddDivision = false;
                    this.newDivName = '';

                    // Broadcast ke tab lain tanpa reload
                    window.dispatchEvent(new CustomEvent('division-added', { detail: { name: name } }));
                })
                .catch(err => {
                    this.isSavingDivision = false;
                    alert('Terjadi kesalahan jaringan saat menyimpan divisi baru.');
                });
            }
        };
    }
</script>

<!-- Tab: Form Input HP & Akun Sales -->
<div x-show="activeTab === 'akun'" class="space-y-6" x-data="formAkunHandler()">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                📱
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Formulir Pendaftaran Smartphone &amp; Akun Sales</h2>
                <p class="text-xs text-slate-500 mt-0.5">Input data nomor WhatsApp bisnis, fisik smartphone, email operasional, dan akun media sosial</p>
            </div>
        </div>

        <button type="button" @click="setTab('phones')" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
            <span>← Kembali ke HP &amp; Akun</span>
        </button>
    </div>

    <!-- Form Akun Body -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf
            <input type="hidden" name="type" value="akun">

            <!-- Section 1: Identitas Sales & Tim -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    1. Identitas Sales &amp; Divisi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Sales / Pemegang HP <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: Teh Silvi / Kang Ridwan" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Divisi / Tim <span class="text-rose-500">*</span></label>
                            <button type="button" @click="showAddDivision = !showAddDivision" class="text-[10px] font-bold text-teal-700 hover:underline cursor-pointer">
                                + Tambah Tim Baru
                            </button>
                        </div>
                        
                        <div x-show="!showAddDivision">
                            <select name="departemen" required x-model="selectedDept" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                                @foreach($divisions as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Inline Add Division -->
                        <div x-show="showAddDivision" x-cloak class="flex gap-1.5 items-center">
                            <input type="text" x-model="newDivName" @keydown.enter.prevent="addNewDivision()" placeholder="Nama divisi baru..." class="flex-1 px-3 py-1.5 rounded-xl border border-teal-300 text-xs focus:outline-hidden">
                            <button type="button" @click="addNewDivision()" :disabled="isSavingDivision" class="px-3 py-1.5 bg-[#3A7D8A] hover:bg-[#2E5190] text-white rounded-xl font-bold text-xs disabled:opacity-50 flex items-center gap-1 cursor-pointer">
                                <span x-show="!isSavingDivision">Simpan</span>
                                <span x-show="isSavingDivision" x-cloak>Menyimpan...</span>
                            </button>
                            <button type="button" @click="showAddDivision = false" :disabled="isSavingDivision" class="px-2 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs cursor-pointer">Batal</button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Bisnis <span class="text-rose-500">*</span></label>
                        <input type="text" name="noWa" required placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: Fisik Smartphone -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    2. Data Fisik Perangkat Smartphone
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tipe Smartphone &amp; RAM/Storage</label>
                        <input type="text" name="jenisHp" placeholder="Contoh: Xiaomi Redmi Note 13 8GB/256GB" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Pengadaan HP</label>
                        <select name="bekasHp" x-model="statusHp" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                            <option value="Baru (Pengadaan Kantor)">Baru (Pengadaan Kantor)</option>
                            <option value="Bekas (Eks Sales Lain)">Bekas (Eks Sales Lain)</option>
                            <option value="Perangkat Pribadi / Reimburse">Perangkat Pribadi / Reimburse</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">PIN / Pola Kunci Layar HP</label>
                        <input type="text" name="pinPolaHp" placeholder="Contoh: 123456 / Pola L" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <!-- Input Bekas Siapa (Muncul Otomatis Jika Status HP Bekas) -->
                    <div x-show="statusHp.includes('Bekas')" x-cloak class="md:col-span-3 p-3.5 rounded-2xl bg-amber-50/90 border border-amber-300 space-y-1.5 transition-all">
                        <label class="block font-bold text-amber-950 text-xs flex items-center gap-1.5">
                            <span>👤</span>
                            <span>Nama Pemegang Sebelumnya (Bekas Siapa?)</span>
                            <span class="text-rose-600 font-extrabold">*</span>
                        </label>
                        <input type="text" name="bekasSiapa" :required="statusHp.includes('Bekas')" placeholder="Contoh: Teh Silvi (Eks CRM Jaket) / Kang Asep" class="w-full px-3.5 py-2 rounded-xl border border-amber-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <span class="text-[10px] text-amber-800 font-medium">Wajib diisi jika perangkat berstatus bekas agar riwayat mutasi aset terlacak dengan jelas.</span>
                    </div>

                    <!-- File Attachment Upload: Foto Fisik HP -->
                    <div class="md:col-span-3 pt-1">
                        <label class="block font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Foto Fisik Smartphone / Kondisi Unit (Attachment File)</span>
                            <span class="text-[10px] text-amber-700 font-normal">Mendukung format gambar &amp; PDF</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-4 bg-slate-50/60 text-center relative transition group cursor-pointer">
                            <input type="file" id="tab_akun_foto" name="foto" accept="image/*,.pdf" @change="handleFotoSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                            
                            <div x-show="!fotoPreview && !fotoName" class="space-y-1 py-1">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-base group-hover:scale-110 transition-transform">
                                    📷
                                </div>
                                <div class="font-bold text-slate-700 text-xs">Pilih atau Seret Foto Fisik HP ke Sini</div>
                                <div class="text-[10px] text-slate-400">JPG, PNG, WEBP, atau berkas PDF (Maksimal 5 MB)</div>
                            </div>

                            <div x-show="fotoPreview" x-cloak class="flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-slate-200">
                                <div class="flex items-center gap-3">
                                    <img :src="fotoPreview" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                    <div class="text-left">
                                        <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">✓ Foto siap diunggah</div>
                                    </div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 text-xs font-bold z-20 cursor-pointer">
                                    Hapus
                                </button>
                            </div>

                            <div x-show="!fotoPreview && fotoName" x-cloak class="flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-slate-200">
                                <div class="flex items-center gap-2 text-left">
                                    <span class="text-xl">📄</span>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">✓ Berkas siap diunggah</div>
                                    </div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 text-xs font-bold z-20 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Akun Email & Media Sosial (Dinamis dengan Tombol Tambah Akun) -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        3. Kredensial Akun Digital Sales &amp; Media Sosial
                    </h3>
                    <button type="button" @click="addAccount('Instagram')" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <span>➕ Tambah Akun</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(acc, index) in accounts" :key="index">
                        <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2 relative group hover:border-amber-300 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span x-text="'Akun #' + (index + 1) + ' (' + acc.platform + ')'"></span>
                                </span>
                                <button type="button" @click="removeAccount(index)" class="text-rose-500 hover:text-rose-700 font-bold text-xs flex items-center gap-1 cursor-pointer" x-show="accounts.length > 1" title="Hapus Akun Ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Platform / Layanan</label>
                                    <select :name="'accounts[' + index + '][platform]'" x-model="acc.platform" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A]">
                                        <option value="WhatsApp Bisnis">WhatsApp Bisnis</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="Facebook Page">Facebook Page</option>
                                        <option value="Email Utama">Email Utama</option>
                                        <option value="TikTok Shop">TikTok Shop</option>
                                        <option value="Shopee">Shopee</option>
                                        <option value="Tokopedia">Tokopedia</option>
                                        <option value="Telegram">Telegram</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Username / Email / ID</label>
                                    <input type="text" :name="'accounts[' + index + '][username]'" x-model="acc.username" placeholder="Contoh: @sales_jaket" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Password / PIN</label>
                                    <input type="text" :name="'accounts[' + index + '][password]'" x-model="acc.password" placeholder="••••••••" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A]">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Quick Add Platform Buttons -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                        <span class="text-slate-400 font-semibold mr-1 text-[11px]">Tambah Cepat:</span>
                        <button type="button" @click="addAccount('Instagram')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Instagram</button>
                        <button type="button" @click="addAccount('TikTok Shop')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ TikTok</button>
                        <button type="button" @click="addAccount('Facebook Page')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Facebook</button>
                        <button type="button" @click="addAccount('Email Utama')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Email</button>
                        <button type="button" @click="addAccount('Shopee')" class="px-2.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium transition cursor-pointer text-[11px]">+ Shopee</button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="setTab('phones')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <span>💾 Simpan Inventaris HP</span>
                </button>
            </div>
        </form>
    </div>
</div>
