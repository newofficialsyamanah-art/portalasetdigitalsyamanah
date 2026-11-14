<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Enterprise Asset Management') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        syamanah: {
                            navy: '#0F1D2E',
                            teal: '#3A7D8A',
                            royal: '#2E5190',
                            amber: '#D9730D',
                            bg: '#F8F7F5',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- SheetJS (xlsx) CDN for Excel Import & Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ANTI-PRINT HARD SECURITY SHIELD */
        @media print {
            body * {
                display: none !important;
                visibility: hidden !important;
            }
            #printBlockerNotice {
                display: flex !important;
                visibility: visible !important;
                position: fixed !important;
                top: 0 !important; left: 0 !important;
                width: 100vw !important; height: 100vh !important;
                background: #0F1D2E !important;
                color: #ffffff !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 40px !important;
                box-sizing: border-box !important;
                z-index: 9999999 !important;
            }
            #printBlockerNotice h1 {
                font-size: 28px !important;
                font-weight: 900 !important;
                color: #ef4444 !important;
                margin-bottom: 12px !important;
            }
            #printBlockerNotice p {
                font-size: 14px !important;
                color: #cbd5e1 !important;
            }
        }
    </style>
</head>
<body class="bg-[#F8F7F5] text-slate-800 min-h-screen flex flex-col antialiased" x-data="portalApp()">

    <!-- Include Header / Navbar -->
    @include('components.navbar')

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full space-y-6">
        
        <!-- Flash Alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl border bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm flex items-start justify-between gap-3 transition-all">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5">Berhasil!</h4>
                        <p class="text-xs leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl border bg-rose-50 border-rose-300 text-rose-900 shadow-sm flex items-start justify-between gap-3 transition-all">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5">Peringatan / Error</h4>
                        <p class="text-xs leading-relaxed">{{ session('error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl border bg-rose-50 border-rose-300 text-rose-900 shadow-sm flex items-start justify-between gap-3 transition-all">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm mb-0.5">Validasi Formulir Gagal:</h4>
                        <ul class="text-xs list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <!-- General Affair Banner Notice -->
        @if(Auth::check() && Auth::user()->role === 'general_affair')
            <div class="bg-gradient-to-r from-teal-500/10 via-teal-600/5 to-transparent border-l-4 border-[#3A7D8A] p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-slate-700 text-xs shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#3A7D8A]/10 text-[#3A7D8A] flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold text-[#0F1D2E] text-sm">Mode Akses General Affair (GA)</span>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Menampilkan inventaris fisik, nama pengguna, dan spesifikasi hardware (Laptop, PC Desktop, HP Android). Seluruh data akun &amp; kredensial perusahaan terlindungi.
                        </p>
                    </div>
                </div>
                <button @click="openLoginModal = true" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-[#0F1D2E] hover:bg-slate-50 font-bold text-xs shrink-0 transition shadow-2xs self-start sm:self-auto">
                    Ganti Akun
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Include Footer -->
    @include('components.footer')

    <!-- Include Universal Modals (Including Excel Import & Export) -->
    @include('components.modals')

    <script>
        function portalApp() {
            return {
                activeTab: '{{ $activeTab ?? 'spreadsheet' }}',
                searchTerm: '',
                filterDept: 'all',
                openLoginModal: false,
                openQuickAdd: false,
                openMobileMenu: false,
                openProfileDropdown: false,
                openDxDiagModal: false,
                openGasModal: false,
                openDriveModal: false,
                openEditModal: false,
                openDeleteModal: false,
                
                // Excel Export & Import States
                openExportModal: false,
                openImportModal: false,
                isExporting: false,
                isImporting: false,
                isParsingFile: false,
                importError: null,
                importSuccessMsg: null,
                importPreview: null,

                editItem: {},
                deleteTarget: {},
                copiedKey: null,

                // Superadmin Sub-Tab & User Management
                subTab: '{{ request()->query("subTab", "users") }}',
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
                },

                // Superadmin Master System Management
                showEditSystemModal: false,
                editSystemData: {
                    id: '',
                    name: '',
                    icon: '💻',
                    category: 'SOP Operasional',
                    description: '',
                    featuresText: '',
                    order_num: 1,
                    is_active: true
                },
                openEditSystem(s) {
                    this.editSystemData = {
                        id: s.id,
                        name: s.name,
                        icon: s.icon || '💻',
                        category: s.category || 'SOP Operasional',
                        description: s.description || '',
                        featuresText: Array.isArray(s.features) ? s.features.join('\n') : (s.features || ''),
                        order_num: s.order_num || 1,
                        is_active: Boolean(s.is_active)
                    };
                    this.showEditSystemModal = true;
                },

                // ERPNext Integration States & Methods
                erpTesting: false,
                erpTestResult: null,
                erpPulling: false,
                erpPullResult: null,
                erpPushing: false,
                erpPushResult: null,
                erpAutoImport: false,
                erpDocType: 'Asset',
                erpLimit: '500',
                erpPushForm: {
                    submission_id: '',
                    asset_name: 'Laptop ASUS ExpertBook - Wilda',
                    department: 'Sales Jaket',
                    custodian: 'Wilda',
                    item_code: 'IT-ASSET-01',
                    location: '',
                    gross_purchase_amount: '1'
                },
                erpMasters: null,
                erpLoadingMasters: false,

                async fetchErpMasters() {
                    this.erpLoadingMasters = true;
                    try {
                        const urlInput = document.getElementById('erpnextUrlInput')?.value || '';
                        const keyInput = document.getElementById('erpnextApiKeyInput')?.value || '';
                        const secretInput = document.getElementById('erpnextApiSecretInput')?.value || '';
                        const res = await fetch('{{ route('erpnext.masters') }}?erpnextUrl=' + encodeURIComponent(urlInput) + '&erpnextApiKey=' + encodeURIComponent(keyInput) + '&erpnextApiSecret=' + encodeURIComponent(secretInput));
                        const json = await res.json();
                        if (json.success) {
                            this.erpMasters = json.masters;
                            if (json.masters.locations && json.masters.locations.length > 0 && !this.erpPushForm.location) {
                                this.erpPushForm.location = json.masters.locations[0];
                            }
                        }
                    } catch (e) {
                        console.error('Failed to fetch ERP masters:', e);
                    } finally {
                        this.erpLoadingMasters = false;
                    }
                },

                openErpPushFor(id, name, dept, user, code) {
                    this.setTab('superadmin');
                    this.subTab = 'erpnext';
                    this.erpPushForm.submission_id = id || '';
                    this.erpPushForm.asset_name = name || 'Aset Kantor';
                    this.erpPushForm.department = dept || 'Sales Jaket';
                    this.erpPushForm.custodian = user || '';
                    this.erpPushForm.item_code = code || 'IT-ASSET';
                },

                async testErpConnection() {
                    this.erpTesting = true;
                    this.erpTestResult = null;
                    try {
                        const urlInput = document.getElementById('erpnextUrlInput')?.value;
                        const keyInput = document.getElementById('erpnextApiKeyInput')?.value;
                        const secretInput = document.getElementById('erpnextApiSecretInput')?.value;

                        if (!urlInput) {
                            this.erpTestResult = { success: false, message: 'Harap masukkan URL Server ERPNext kantor terlebih dahulu!' };
                            this.erpTesting = false;
                            return;
                        }

                        const res = await fetch('{{ route('erpnext.test') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                erpnextUrl: urlInput,
                                erpnextApiKey: keyInput,
                                erpnextApiSecret: secretInput
                            })
                        });
                        this.erpTestResult = await res.json();
                    } catch (err) {
                        this.erpTestResult = { success: false, message: 'Gagal menghubungi server: ' + err.message };
                    } finally {
                        this.erpTesting = false;
                    }
                },

                async pullFromErp(isDemo = false) {
                    this.erpPulling = true;
                    this.erpPullResult = null;
                    try {
                        const urlInput = document.getElementById('erpnextUrlInput')?.value || '';
                        const keyInput = document.getElementById('erpnextApiKeyInput')?.value || '';
                        const secretInput = document.getElementById('erpnextApiSecretInput')?.value || '';

                        const params = new URLSearchParams({
                            importToDb: this.erpAutoImport ? '1' : '0',
                            erpnextUrl: urlInput,
                            erpnextApiKey: keyInput,
                            erpnextApiSecret: secretInput,
                            doctype: this.erpDocType || 'Asset',
                            limit: this.erpLimit || '500',
                            isDemo: isDemo ? '1' : '0'
                        });

                        const res = await fetch('{{ route('erpnext.assets') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json' }
                        });
                        this.erpPullResult = await res.json();
                    } catch (err) {
                        this.erpPullResult = { success: false, message: 'Gagal menarik data: ' + err.message };
                    } finally {
                        this.erpPulling = false;
                    }
                },

                async pushToErp(isDemo = false) {
                    this.erpPushing = true;
                    this.erpPushResult = null;
                    try {
                        const urlInput = document.getElementById('erpnextUrlInput')?.value || '';
                        const keyInput = document.getElementById('erpnextApiKeyInput')?.value || '';
                        const secretInput = document.getElementById('erpnextApiSecretInput')?.value || '';

                        const payload = Object.assign({}, this.erpPushForm, {
                            erpnextUrl: urlInput,
                            erpnextApiKey: keyInput,
                            erpnextApiSecret: secretInput,
                            doctype: this.erpDocType || 'Asset',
                            isDemo: isDemo ? 1 : 0
                        });

                        const res = await fetch('{{ route('erpnext.push') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });
                        this.erpPushResult = await res.json();
                    } catch (err) {
                        this.erpPushResult = { success: false, message: 'Gagal mengirim data: ' + err.message };
                    } finally {
                        this.erpPushing = false;
                    }
                },

                selectAssetForPush(event) {
                    const val = event.target.value;
                    this.erpPushForm.submission_id = val;
                    if (!val) return;
                    const selectedOpt = event.target.options[event.target.selectedIndex];
                    if (selectedOpt) {
                        this.erpPushForm.asset_name = selectedOpt.dataset.name || '';
                        this.erpPushForm.department = selectedOpt.dataset.dept || 'Sales Jaket';
                        this.erpPushForm.custodian = selectedOpt.dataset.user || '';
                        this.erpPushForm.item_code = selectedOpt.dataset.code || 'IT-ASSET';
                    }
                },

                copyToClipboard(text, key) {
                    navigator.clipboard.writeText(text);
                    this.copiedKey = key;
                    setTimeout(() => { this.copiedKey = null; }, 2000);
                },

                setTab(tab) {
                    this.activeTab = tab;
                    window.history.replaceState(null, '', '?tab=' + tab);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                // EXPORT TO EXCEL (.xlsx)
                async exportToExcel(type = 'all') {
                    this.isExporting = true;
                    try {
                        const res = await fetch('{{ route("excel.export") }}?type=' + type, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        if (!json.success) throw new Error(json.message || 'Gagal mengambil data ekspor');

                        if (typeof XLSX === 'undefined') {
                            throw new Error('Library SheetJS belum termuat. Silakan periksa koneksi internet Anda.');
                        }

                        const wb = XLSX.utils.book_new();

                        // 1. Sheet PC & Laptop
                        if ((type === 'all' || type === 'pc') && json.sheets['Spesifikasi PC & Laptop']) {
                            const wsPC = XLSX.utils.json_to_sheet(json.sheets['Spesifikasi PC & Laptop']);
                            XLSX.utils.book_append_sheet(wb, wsPC, 'Spesifikasi PC & Laptop');
                        }

                        // 2. Sheet Akun Sales & HP
                        if ((type === 'all' || type === 'akun') && json.sheets['Akun Sales & HP']) {
                            const wsAkun = XLSX.utils.json_to_sheet(json.sheets['Akun Sales & HP']);
                            XLSX.utils.book_append_sheet(wb, wsAkun, 'Akun Sales & HP');
                        }

                        // 3. Sheet MikroTik
                        if ((type === 'all' || type === 'mikrotik') && json.sheets['Akun MikroTik']) {
                            const wsMkt = XLSX.utils.json_to_sheet(json.sheets['Akun MikroTik']);
                            XLSX.utils.book_append_sheet(wb, wsMkt, 'Akun MikroTik');
                        }

                        const dateStr = new Date().toISOString().slice(0, 10);
                        const filename = type === 'all' 
                            ? `Database_Aset_Syamanah_${dateStr}.xlsx` 
                            : `Aset_Syamanah_${type.toUpperCase()}_${dateStr}.xlsx`;

                        XLSX.writeFile(wb, filename);
                        this.openExportModal = false;
                    } catch (err) {
                        alert('Gagal mengekspor data ke Excel: ' + err.message);
                    } finally {
                        this.isExporting = false;
                    }
                },

                // PARSE EXCEL / CSV FILE IN BROWSER
                async handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.isParsingFile = true;
                    this.importError = null;
                    this.importSuccessMsg = null;
                    this.importPreview = null;

                    try {
                        if (typeof XLSX === 'undefined') {
                            throw new Error('Library SheetJS belum termuat. Silakan muat ulang halaman.');
                        }

                        const data = await file.arrayBuffer();
                        const workbook = XLSX.read(new Uint8Array(data), { type: 'array' });

                        let pcList = [];
                        let akunList = [];
                        let mikrotikList = [];

                        for (const sheetName of workbook.SheetNames) {
                            const ws = workbook.Sheets[sheetName];
                            if (!ws) continue;
                            const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
                            if (!rows || rows.length === 0) continue;

                            const nameLower = sheetName.toLowerCase();
                            const sampleKeys = Object.keys(rows[0] || {}).map(k => k.toLowerCase().replace(/[^a-z0-9]/g, ''));

                            const isMikrotik = nameLower.includes('mikrotik') || nameLower.includes('hotspot') || sampleKeys.some(k => k.includes('profilehotspot') || k.includes('commandterminal') || (k.includes('username') && k.includes('ratelimit')));
                            const isPC = nameLower.includes('pc') || nameLower.includes('laptop') || sampleKeys.some(k => k.includes('computername') || k.includes('processor') || k.includes('dxdiag') || k.includes('ram'));
                            const isAkun = nameLower.includes('akun') || nameLower.includes('hp') || nameLower.includes('sales') || sampleKeys.some(k => k.includes('jenishp') || k.includes('email1') || k.includes('ig1') || k.includes('nowa') || k.includes('kategoritimrole'));

                            if (isMikrotik) {
                                mikrotikList.push(...rows);
                            } else if (isPC) {
                                pcList.push(...rows);
                            } else if (isAkun) {
                                akunList.push(...rows);
                            } else {
                                if (sampleKeys.length >= 10) {
                                    akunList.push(...rows);
                                } else {
                                    pcList.push(...rows);
                                }
                            }
                        }

                        const total = pcList.length + akunList.length + mikrotikList.length;
                        if (total === 0) {
                            throw new Error('File tidak berisi data baris yang dapat dibaca.');
                        }

                        this.importPreview = {
                            fileName: file.name,
                            fileSize: (file.size / 1024).toFixed(1) + ' KB',
                            sheetNames: workbook.SheetNames,
                            pcCount: pcList.length,
                            akunCount: akunList.length,
                            mikrotikCount: mikrotikList.length,
                            totalCount: total,
                            pcRecords: pcList,
                            akunRecords: akunList,
                            mikrotikRecords: mikrotikList
                        };
                    } catch (e) {
                        this.importError = e.message;
                    } finally {
                        this.isParsingFile = false;
                    }
                },

                // SUBMIT IMPORT TO SERVER
                async submitImport() {
                    if (!this.importPreview || this.importPreview.totalCount === 0) {
                        alert('Silakan pilih file Excel yang valid terlebih dahulu.');
                        return;
                    }

                    this.isImporting = true;
                    this.importError = null;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('{{ route("excel.import") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                pcRecords: this.importPreview.pcRecords,
                                akunRecords: this.importPreview.akunRecords,
                                mikrotikRecords: this.importPreview.mikrotikRecords
                            })
                        });

                        const result = await res.json();
                        if (result.success) {
                            this.importSuccessMsg = result.message;
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            throw new Error(result.message || 'Gagal menyimpan data impor.');
                        }
                    } catch (err) {
                        this.importError = 'Gagal melakukan impor: ' + err.message;
                    } finally {
                        this.isImporting = false;
                    }
                },

                // DOWNLOAD SAMPLE EXCEL TEMPLATES
                downloadTemplateExcel(type = 'all') {
                    if (typeof XLSX === 'undefined') {
                        window.location.href = '{{ route("excel.template") }}?type=' + type;
                        return;
                    }

                    const wb = XLSX.utils.book_new();

                    if (type === 'all' || type === 'pc') {
                        const samplePC = [{
                            'Nama': 'Ahmad Fauzi',
                            'Departemen': 'Sales Jaket',
                            'Jabatan': 'Staff Admin',
                            'Jenis Perangkat': 'PC Desktop',
                            'Computer Name': 'PC-AHMAD',
                            'IP': '192.168.1.105',
                            'Password PC': 'admin123',
                            'Operating System': 'Windows 11 Pro 64-bit',
                            'System Manufacturer': 'ASUS',
                            'Processor': 'Intel Core i5-11400',
                            'Memory': '16 GB',
                            'Display Link': 'https://drive.google.com/...',
                            'Sound Link': '-',
                            'Input Link': '-'
                        }];
                        const wsPC = XLSX.utils.json_to_sheet(samplePC);
                        XLSX.utils.book_append_sheet(wb, wsPC, 'Spesifikasi PC & Laptop');
                    }

                    if (type === 'all' || type === 'akun') {
                        const sampleAkun = [{
                            'Nama Pemegang': 'Siti Rahma',
                            'Kategori Tim/Role': 'Sales Jersey',
                            'Jabatan': 'Staff Sales',
                            'Jenis Handphone': 'Samsung Galaxy A15',
                            'Bekas handphone siapa?': 'Baru',
                            'Akun login di PC': 'siti.sales',
                            'Email 1': 'siti.syamanah@gmail.com',
                            'Password Email 1': 'siti12345',
                            'Email 2': '-',
                            'Password Email 2': '-',
                            'Email 3': '-',
                            'Password Email 3': '-',
                            'Username IG 1': '@syamanah.jersey',
                            'Password IG 1': 'igpass123',
                            'Username IG 2': '-',
                            'Password IG 2': '-',
                            'Username IG 3': '-',
                            'Password IG 3': '-',
                            'Facebook 1': 'Syamanah Official',
                            'Password FB 1': 'fbpass123',
                            'Facebook 2': '-',
                            'Password FB 2': '-',
                            'Facebook 3': '-',
                            'Password FB 3': '-',
                            'No Telpon / WhatsApp': '081234567890',
                            'Akun lain terhubung aset Syamanah': '-',
                            'Akun software lain': 'Canva Pro',
                            'Catatan Khusus / Audit': 'Kondisi mulus'
                        }];
                        const wsAkun = XLSX.utils.json_to_sheet(sampleAkun);
                        XLSX.utils.book_append_sheet(wb, wsAkun, 'Akun Sales & HP');
                    }

                    if (type === 'all' || type === 'mikrotik') {
                        const sampleMkt = [{
                            'Nama Pemilik': 'Budi Santoso',
                            'Tim': 'Sales Seragam',
                            'Jabatan': 'Staff',
                            'Username': 'SS45',
                            'Password': '654321',
                            'Profile Hotspot': 'sales_seragam',
                            'Rate Limit': '10M/10M',
                            'Status': 'aktif'
                        }];
                        const wsMkt = XLSX.utils.json_to_sheet(sampleMkt);
                        XLSX.utils.book_append_sheet(wb, wsMkt, 'Akun MikroTik');
                    }

                    XLSX.writeFile(wb, `Template_Import_Aset_Syamanah.xlsx`);
                }
            }
        }
    </script>

    <!-- Hard Print Blocker Notice (Revealed only when user triggers print) -->
    <div id="printBlockerNotice" style="display:none;">
        <div>
            <h1>🔒 DOKUMEN SISTEM INI DILINDUNGI (DRM ACTIVE)</h1>
            <p>Pencetakan fisik (Print / Save to PDF) dilarang keras berdasarkan Kebijakan Keamanan Informasi CV Generasi Sugih Sejahtera.</p>
            <p style="margin-top: 10px; font-family: monospace; font-size: 11px; color: #94a3b8;">Kode Keamanan: DRM-SEC-SYAMANAH-BLOCKED</p>
        </div>
    </div>
</body>
</html>
