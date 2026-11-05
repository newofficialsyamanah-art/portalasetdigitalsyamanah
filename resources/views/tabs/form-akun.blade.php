<!-- Tab: Form Input HP & Akun Sales -->
<div x-show="activeTab === 'akun'" x-cloak class="space-y-6" x-data="{
    showAddDivision: false,
    newDivName: '',
    selectedDept: 'Sales Jaket',
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
            this.selectedDept = this.newDivName;
            this.showAddDivision = false;
            this.newDivName = '';
            window.location.reload();
        });
    }
}">
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
    </div>

    <!-- Form Akun Body -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('submissions.store') }}" method="POST" class="space-y-6 text-xs">
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
                            <button type="button" @click="showAddDivision = !showAddDivision" class="text-[10px] font-bold text-teal-700 hover:underline">
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
                        <div x-show="showAddDivision" x-cloak class="flex gap-1.5">
                            <input type="text" x-model="newDivName" placeholder="Nama divisi baru..." class="flex-1 px-3 py-1.5 rounded-xl border border-teal-300 text-xs focus:outline-hidden">
                            <button type="button" @click="addNewDivision()" class="px-3 py-1.5 bg-[#3A7D8A] text-white rounded-xl font-bold text-xs">Simpan</button>
                            <button type="button" @click="showAddDivision = false" class="px-2 py-1.5 bg-slate-200 text-slate-700 rounded-xl text-xs">Batal</button>
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
                        <select name="bekasHp" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                            <option value="Baru (Pengadaan Kantor)">Baru (Pengadaan Kantor)</option>
                            <option value="Bekas (Eks Sales Lain)">Bekas (Eks Sales Lain)</option>
                            <option value="Perangkat Pribadi / Reimburse">Perangkat Pribadi / Reimburse</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">PIN / Pola Kunci Layar HP</label>
                        <input type="text" name="pinPolaHp" placeholder="Contoh: 123456 / Pola L" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 3: Akun Email & Media Sosial -->
            <div>
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    3. Kredensial Email &amp; Akun Digital Sales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email Utama Sales</label>
                        <input type="email" name="email1" placeholder="silvi.jaket@syamanah.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Email Utama</label>
                        <input type="text" name="passEmail1" placeholder="••••••••" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Akun Instagram Sales (Username)</label>
                        <input type="text" name="ig1" placeholder="Contoh: jaket_syamanah_official" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Instagram</label>
                        <input type="text" name="passIg1" placeholder="••••••••" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Akun Facebook / Halaman</label>
                        <input type="text" name="fb1" placeholder="Katalog Jaket Syamanah" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password Facebook</label>
                        <input type="text" name="passFb1" placeholder="••••••••" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-[#3A7D8A] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="setTab('phones')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:brightness-110 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-2">
                    <span>💾 Simpan Inventaris HP</span>
                </button>
            </div>
        </form>
    </div>
</div>

