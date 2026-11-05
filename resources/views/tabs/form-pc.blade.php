<!-- Tab: Form Input Laptop / PC Workstation -->
<div x-show="activeTab === 'pc'" x-cloak class="space-y-6" x-data="formPcHandler()">
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

        <button type="button" @click="openDxDiagModal = true" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
            <span>❓ Panduan Ekspor DxDiag</span>
        </button>
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
        <form action="{{ route('submissions.store') }}" method="POST" class="space-y-6 text-xs">
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
                        <div x-show="showAddDivision" x-cloak class="flex gap-1.5">
                            <input type="text" x-model="newDivName" placeholder="Nama divisi baru..." class="flex-1 px-3 py-1.5 rounded-xl border border-teal-300 text-xs focus:outline-hidden">
                            <button type="button" @click="addNewDivision()" class="px-3 py-1.5 bg-[#3A7D8A] text-white rounded-xl font-bold text-xs">Simpan</button>
                            <button type="button" @click="showAddDivision = false" class="px-2 py-1.5 bg-slate-200 text-slate-700 rounded-xl text-xs">Batal</button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" x-model="formData.jabatan" placeholder="Contoh: Staff CRM / Desainer" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Hardware -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    2. Spesifikasi Hardware &amp; Sistem
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
                        <input type="text" name="os" x-model="formData.os" placeholder="Contoh: Windows 11 Pro 64-bit" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat IP Komputer (Opsional)</label>
                        <input type="text" name="ip" x-model="formData.ip" placeholder="Contoh: 192.168.99.79" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Windows / PC</label>
                        <input type="text" name="passwordPc" x-model="formData.passwordPc" placeholder="Tulis '-' jika tidak ada sandi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <input type="text" name="catatan" x-model="formData.catatan" placeholder="Kondisi fisik, kelengkapan, dll." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="setTab('inventory')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <span>💾 Simpan Inventaris PC</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function formPcHandler() {
        return {
            dragOver: false,
            dxdiagLoaded: false,
            showAddDivision: false,
            newDivName: '',
            formData: {
                nama: '',
                departemen: 'Sales Jaket',
                jabatan: '',
                jenisPerangkat: 'Laptop',
                computerName: '',
                manufacturer: '',
                processor: '',
                memory: '',
                os: '',
                ip: '',
                passwordPc: '-',
                catatan: ''
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
                    
                    // Simple regex parser for dxdiag fields
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
                if (!this.newDivName.trim()) return;
                fetch('{{ route("divisions.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newDivName })
                })
                .then(res => res.json())
                .then(data => {
                    this.formData.departemen = this.newDivName;
                    this.showAddDivision = false;
                    this.newDivName = '';
                    window.location.reload();
                });
            }
        }
    }
</script>

