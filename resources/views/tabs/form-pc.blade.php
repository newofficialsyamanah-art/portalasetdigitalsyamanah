<script>
    function formPcHandler() {
        return {
            dragOver: false,
            dxdiagLoaded: false,
            showAddDivision: false,
            newDivName: '',
            isSavingDivision: false,
            fotoName: '',
            fotoPreview: null,
            displayFileName: '',
            displayFilePreview: null,
            soundFileName: '',
            soundFilePreview: null,
            inputFileName: '',
            inputFilePreview: null,
            handleAttachment(type, e) {
                const file = e.target.files[0];
                if (file) {
                    this[type + 'FileName'] = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => { this[type + 'FilePreview'] = ev.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this[type + 'FilePreview'] = null;
                    }
                }
            },
            removeAttachment(type, id) {
                this[type + 'FileName'] = '';
                this[type + 'FilePreview'] = null;
                const input = document.getElementById(id);
                if (input) input.value = '';
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
                const input = document.getElementById('tab_pc_foto');
                if (input) input.value = '';
            },
            divisions: @json(array_values($divisions)),
            formData: {
                nama: '',
                departemen: '{{ $divisions[0] ?? "Sales Jaket" }}',
                jabatan: '',
                jenisPerangkat: 'Laptop',
                computerName: '',
                manufacturer: '',
                processor: '',
                memory: '',
                os: '',
                ip: '',
                passwordPc: '-',
                displayLink: '',
                soundLink: '',
                inputLink: '',
                catatan: ''
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
            handleDrop(e) {
                this.dragOver = false;
                const file = e.dataTransfer.files[0];
                if (file) this.parseDxDiag(file);
            },
            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) this.parseDxDiag(file);
            },
            parseDxDiag(file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const text = event.target.result;
                    
                    const getField = (pattern) => {
                        const match = text.match(pattern);
                        return match ? match[1].trim() : '';
                    };

                    const compName = getField(/Machine name:\s*([^\r\n]+)/i);
                    const os = getField(/Operating System:\s*([^\r\n]+)/i);
                    const mfg = getField(/System Manufacturer:\s*([^\r\n]+)/i);
                    const cpu = getField(/Processor:\s*([^\r\n]+)/i);
                    const mem = getField(/Memory:\s*([^\r\n]+)/i);

                    if (compName) this.formData.computerName = compName;
                    if (os) this.formData.os = os;
                    if (mfg) this.formData.manufacturer = mfg;
                    if (cpu) this.formData.processor = cpu;
                    if (mem) this.formData.memory = mem;

                    this.dxdiagLoaded = true;
                };
                reader.readAsText(file);
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
                    this.formData.departemen = name;
                    this.showAddDivision = false;
                    this.newDivName = '';

                    // Broadcast ke tab lain tanpa reload halaman
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

<!-- Tab: Form Input Laptop / PC Workstation -->
<div x-show="activeTab === 'pc'" class="space-y-6" x-data="formPcHandler()">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                💻
            </div>
            <div>
                <h2 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Formulir Pendaftaran Laptop &amp; PC Workstation</h2>
                <p class="text-xs text-slate-500 mt-0.5">Input data spesifikasi perangkat baru atau impor otomatis dari file DxDiag Windows</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="setTab('inventory')" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>← Kembali ke Laptop &amp; PC</span>
            </button>
            <button type="button" @click="openDxDiagModal = true" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>❓ Panduan Ekspor DxDiag</span>
            </button>
        </div>
    </div>

    <!-- Drag & Drop DxDiag File Box -->
    <div class="bg-gradient-to-r from-blue-50/60 to-indigo-50/60 rounded-3xl p-6 border-2 border-dashed border-blue-300 text-center space-y-2 cursor-pointer hover:border-blue-500 transition relative"
         @dragover.prevent="dragOver = true"
         @dragleave.prevent="dragOver = false"
         @drop.prevent="handleDrop($event)">
        <input type="file" accept=".txt" @change="handleFileSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
        <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center mx-auto text-xl shadow-md">
            ⚡
        </div>
        <h3 class="font-bold text-sm text-slate-900">Seret &amp; Lepas File DxDiag.txt di Sini (Auto-Fill)</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Sistem akan secara instan membaca Processor, RAM, OS, Manufaktur, dan Nama Komputer tanpa perlu ketik manual.
        </p>
        <span x-show="dxdiagLoaded" x-cloak class="inline-block mt-2 px-3 py-1 bg-emerald-500 text-white font-bold text-xs rounded-full shadow-sm animate-pulse">
            ✓ Spesifikasi DxDiag Berhasil Diekstrak!
        </span>
    </div>

    <!-- Form PC Body -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf
            <input type="hidden" name="type" value="pc">

            <!-- Section 1: Identitas Pengguna & Tim -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#3A7D8A]"></span>
                    1. Identitas Pengguna &amp; Divisi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pemegang / Pengguna <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required x-model="formData.nama" placeholder="Contoh: Wilda" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Divisi / Tim <span class="text-rose-500">*</span></label>
                            <button type="button" @click="showAddDivision = !showAddDivision" class="text-[10px] font-bold text-teal-700 hover:underline">
                                + Tambah Tim Baru
                            </button>
                        </div>
                        
                        <div x-show="!showAddDivision">
                            <select name="departemen" required x-model="formData.departemen" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
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
                        <label class="block font-semibold text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" x-model="formData.jabatan" placeholder="Contoh: Staff CRM / Desainer" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Hardware & Sistem Operasi -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    2. Spesifikasi Hardware &amp; Sistem Operasi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jenis Perangkat</label>
                        <select name="jenisPerangkat" x-model="formData.jenisPerangkat" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                            <option value="Laptop">Laptop Workstation</option>
                            <option value="PC Desktop">PC Desktop / Rakitan</option>
                            <option value="All-in-One">PC All-in-One</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Komputer (Computer Name)</label>
                        <input type="text" name="computerName" x-model="formData.computerName" placeholder="Contoh: DESKTOP-D0N0ANQ" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Brand / Manufaktur</label>
                        <input type="text" name="manufacturer" x-model="formData.manufacturer" placeholder="Contoh: Asus, Lenovo, Gigabyte" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Processor (CPU)</label>
                        <input type="text" name="processor" x-model="formData.processor" placeholder="Contoh: Intel Core i5-12400F" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kapasitas RAM</label>
                        <input type="text" name="memory" x-model="formData.memory" placeholder="Contoh: 8192MB RAM / 16GB DDR4" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Sistem Operasi (OS)</label>
                        <input type="text" name="os" x-model="formData.os" placeholder="Contoh: Windows 11 Pro 64-bit / MacOS" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 3: Jaringan, Perangkat Lunak & Lampiran Bukti DxDiag -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    3. Jaringan, Perangkat Lunak &amp; Lampiran Bukti DxDiag
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat IP Komputer (IP Address)</label>
                        <input type="text" name="ip" x-model="formData.ip" placeholder="Contoh: 192.168.99.79" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Windows / PC</label>
                        <input type="text" name="passwordPc" x-model="formData.passwordPc" placeholder="Tulis '-' jika tidak ada sandi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan / Software Khusus</label>
                        <input type="text" name="catatan" x-model="formData.catatan" placeholder="Contoh: Adobe CC, CorelDRAW, Office 2021" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <!-- 3 Dedicated Attachment Boxes: Bukti Display, Bukti Sound, Bukti Input -->
                    <div class="md:col-span-3 pt-2">
                        <label class="block font-bold text-slate-800 mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-xs">
                                <span>📎</span>
                                <span>Lampiran File Bukti DxDiag (Display, Sound &amp; Input)</span>
                            </span>
                            <span class="text-[10px] text-teal-700 font-normal">Format: Foto/Screenshot (PNG/JPG) atau PDF (Maks 5 MB)</span>
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <!-- 1. Bukti Display (GPU) -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-3.5 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[150px]">
                                <input type="file" id="tab_pc_file_display" name="file_display" accept="image/*,.pdf" @change="handleAttachment('display', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1.5 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>🖥️</span> Bukti Display (GPU)
                                    </span>
                                    <span class="text-[9px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded-full">Display</span>
                                </div>

                                <div x-show="!displayFilePreview && !displayFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Display</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Display DxDiag</div>
                                </div>

                                <div x-show="displayFilePreview" x-cloak class="my-auto">
                                    <img :src="displayFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="displayFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('display', 'tab_pc_file_display')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!displayFilePreview && displayFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="displayFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('display', 'tab_pc_file_display')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Bukti Sound (Audio) -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-purple-500 rounded-2xl p-3.5 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[150px]">
                                <input type="file" id="tab_pc_file_sound" name="file_sound" accept="image/*,.pdf" @change="handleAttachment('sound', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1.5 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>🔊</span> Bukti Sound (Audio)
                                    </span>
                                    <span class="text-[9px] bg-purple-100 text-purple-700 font-bold px-1.5 py-0.5 rounded-full">Sound</span>
                                </div>

                                <div x-show="!soundFilePreview && !soundFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Sound</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Sound DxDiag</div>
                                </div>

                                <div x-show="soundFilePreview" x-cloak class="my-auto">
                                    <img :src="soundFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="soundFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('sound', 'tab_pc_file_sound')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!soundFilePreview && soundFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="soundFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('sound', 'tab_pc_file_sound')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Bukti Input Devices -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-3.5 bg-slate-50/70 text-center relative transition group cursor-pointer flex flex-col justify-between min-h-[150px]">
                                <input type="file" id="tab_pc_file_input" name="file_input" accept="image/*,.pdf" @change="handleAttachment('input', $event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                                
                                <div class="text-left mb-1.5 flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-800 flex items-center gap-1">
                                        <span>⌨️</span> Bukti Input Devices
                                    </span>
                                    <span class="text-[9px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded-full">Input</span>
                                </div>

                                <div x-show="!inputFilePreview && !inputFileName" class="my-auto py-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-sm group-hover:scale-110 transition-transform mb-1 shadow-2xs">
                                        📎
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-700">Attach Bukti Input</div>
                                    <div class="text-[9px] text-slate-400">Screenshot Tab Input DxDiag</div>
                                </div>

                                <div x-show="inputFilePreview" x-cloak class="my-auto">
                                    <img :src="inputFilePreview" class="h-14 mx-auto object-cover rounded-lg border border-slate-200 shadow-2xs mb-1">
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="inputFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('input', 'tab_pc_file_input')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>

                                <div x-show="!inputFilePreview && inputFileName" x-cloak class="my-auto">
                                    <div class="text-2xl mb-1">📄</div>
                                    <div class="font-bold text-[10px] text-slate-800 truncate px-1" x-text="inputFileName"></div>
                                    <button type="button" @click.stop="removeAttachment('input', 'tab_pc_file_input')" class="text-[10px] text-rose-500 hover:underline font-bold mt-1 z-20 relative cursor-pointer">
                                        ✕ Hapus File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Fisik Laptop / PC (Opsional) -->
                    <div class="md:col-span-3 pt-1">
                        <label class="block font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                <span>📷</span> Foto Fisik Unit / Kelengkapan (Opsional)
                            </span>
                            <span class="text-[10px] text-slate-400 font-normal">Foto unit laptop/PC tampak depan/stiker</span>
                        </label>
                        <div class="border border-dashed border-slate-300 hover:border-[#3A7D8A] rounded-xl p-3 bg-white text-center relative transition group cursor-pointer">
                            <input type="file" id="tab_pc_foto" name="foto" accept="image/*,.pdf" @change="handleFotoSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                            
                            <div x-show="!fotoPreview && !fotoName" class="py-1">
                                <span class="text-xs font-semibold text-slate-600">📷 Pilih atau seret foto fisik laptop / PC ke sini</span>
                                <span class="text-[10px] text-slate-400 ml-2">JPG, PNG, PDF (Maksimal 5 MB)</span>
                            </div>

                            <div x-show="fotoPreview" x-cloak class="flex items-center justify-between gap-3 p-1.5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center gap-2">
                                    <img :src="fotoPreview" class="w-8 h-8 object-cover rounded border border-slate-200 shadow-2xs">
                                    <div class="text-left font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="text-rose-500 hover:bg-rose-50 px-2 py-0.5 rounded text-xs font-bold z-20 cursor-pointer">Hapus</button>
                            </div>

                            <div x-show="!fotoPreview && fotoName" x-cloak class="flex items-center justify-between gap-3 p-1.5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center gap-2 text-left">
                                    <span class="text-base">📄</span>
                                    <div class="font-bold text-xs text-slate-800 truncate max-w-xs" x-text="fotoName"></div>
                                </div>
                                <button type="button" @click.stop="removeFoto()" class="text-rose-500 hover:bg-rose-50 px-2 py-0.5 rounded text-xs font-bold z-20 cursor-pointer">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="setTab('inventory')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <span>💾 Simpan Inventaris PC</span>
                </button>
            </div>
        </form>
    </div>
</div>
