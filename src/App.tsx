import React, { useState, useEffect, useCallback } from 'react';
import { Navbar, TabType } from './components/Navbar';
import { DeviceInventory } from './components/DeviceInventory';
import { SuperadminSummary } from './components/SuperadminSummary';
import { FormPC } from './components/FormPC';
import { FormAkun } from './components/FormAkun';
import { HistoryTable } from './components/HistoryTable';
import { GasGuideTab } from './components/GasGuideTab';
import { GasSetupModal } from './components/GasSetupModal';
import { DxDiagGuideModal } from './components/DxDiagGuideModal';
import { ImportSpreadsheetModal } from './components/ImportSpreadsheetModal';
import { MikrotikGenerator } from './components/MikrotikGenerator';
import { SpreadsheetAudit } from './components/SpreadsheetAudit';
import { DriveConnectGuideModal } from './components/DriveConnectGuideModal';
import { PhoneInventory } from './components/PhoneInventory';
import { SpreadsheetView } from './components/SpreadsheetView';
import { AlertState, SubmissionRecord, SyamanahConfig, MikrotikAccount } from './types';
import {
  getConfig,
  getSubmissions,
  saveConfig,
  fetchAllUnifiedData,
  pullDataFromGoogleSpreadsheet
} from './services/gasService';
import { validateFirestoreConnection } from './services/firestoreService';
import {
  CheckCircle2,
  AlertCircle,
  X,
  ExternalLink,
  ShieldCheck,
  Building,
  Heart,
  FileSpreadsheet,
  RefreshCw,
  Globe,
  Database,
  FolderOpen,
  Laptop,
  Smartphone,
  PlusCircle,
  Wifi,
  FileText,
  Monitor
} from 'lucide-react';

export default function App() {
  // Default to spreadsheet tab so the user immediately sees the exact 1:1 Google Spreadsheet table
  const [activeTab, setActiveTab] = useState<TabType>('spreadsheet');
  const [config, setConfigState] = useState<SyamanahConfig>(getConfig());
  const [records, setRecords] = useState<SubmissionRecord[]>(getSubmissions());
  const [mikrotikAccounts, setMikrotikAccounts] = useState<MikrotikAccount[]>(getMikrotikAccounts());
  const [isGasModalOpen, setIsGasModalOpen] = useState(false);
  const [isDxDiagModalOpen, setIsDxDiagModalOpen] = useState(false);
  const [isImportModalOpen, setIsImportModalOpen] = useState(false);
  const [isDriveGuideOpen, setIsDriveGuideOpen] = useState(false);
  const [isSyncing, setIsSyncing] = useState(false);
  const [lastSyncedAt, setLastSyncedAt] = useState<string | null>(null);

  const [alert, setAlert] = useState<AlertState>({
    show: false,
    type: 'success',
    message: ''
  });

  const loadData = useCallback(async () => {
    try {
      const unified = await fetchAllUnifiedData();
      setConfigState(unified.config);
      setRecords(unified.submissions);
      if (Array.isArray(unified.mikrotik)) {
        setMikrotikAccounts(unified.mikrotik);
      }
      if (unified.lastSyncedAt) {
        setLastSyncedAt(unified.lastSyncedAt);
      }
    } catch {
      setConfigState(getConfig());
      setRecords(getSubmissions());
    }
  }, []);

  useEffect(() => {
    loadData();

    // Verify Firestore connection on startup
    validateFirestoreConnection().then((connected) => {
      if (connected) {
        console.log('Google Cloud Firestore connected successfully (Permanent Database)');
      }
    });

    // Auto-poll every 15 seconds so all users across different devices see changes in real-time
    const interval = setInterval(() => {
      loadData();
    }, 15000);

    return () => clearInterval(interval);
  }, [loadData]);

  const handleManualSync = async () => {
    setIsSyncing(true);
    try {
      const res = await pullDataFromGoogleSpreadsheet();
      await loadData();
      showAlert(res.message, 'success', 'Sinkronisasi Spreadsheet Berhasil');
    } catch (err: unknown) {
      const error = err as Error;
      showAlert(error?.message || 'Gagal sinkronisasi data dari Spreadsheet', 'error', 'Pemberitahuan');
    } finally {
      setIsSyncing(false);
    }
  };

  const showAlert = (
    message: string,
    type: 'success' | 'error' | 'info' | 'warning' = 'success',
    title?: string
  ) => {
    setAlert({
      show: true,
      type,
      title,
      message
    });

    if (type === 'success' || type === 'info') {
      setTimeout(() => {
        setAlert((prev) => ({ ...prev, show: false }));
      }, 7000);
    }
  };

  const handleFormSuccess = (message: string) => {
    loadData();
    showAlert(message, 'success', 'Data & Foto Berhasil Dikirim!');
  };

  const handleFormError = (message: string) => {
    loadData();
    showAlert(message, 'error', 'Pemberitahuan');
  };

  const handleConfigUpdated = (newConfig: SyamanahConfig) => {
    setConfigState(newConfig);
    showAlert('Pengaturan Google Spreadsheet & Google Drive berhasil diperbarui!', 'success');
  };

  const deviceCount = records.filter((r) => r.type === 'pc').length;
  const phoneCount = records.filter((r) => r.type === 'akun').length;

  return (
    <div className="min-h-screen flex flex-col bg-slate-50 text-slate-800">
      {/* Top Navigation */}
      <Navbar
        activeTab={activeTab}
        onTabChange={(tab) => {
          if (tab === 'dxdiag_guide') {
            setIsDxDiagModalOpen(true);
          } else {
            setActiveTab(tab);
          }
        }}
        onOpenSettings={() => setIsGasModalOpen(true)}
        onOpenDriveGuide={() => setIsDriveGuideOpen(true)}
        hasGasUrl={Boolean(config.gasUrl)}
        hasSpreadsheet={Boolean(config.spreadsheetId)}
        hasDriveFolder={Boolean(config.driveFolderId)}
        deviceCount={deviceCount}
        phoneCount={phoneCount}
        totalSubmissions={records.length + mikrotikAccounts.length}
      />

      {/* Main Container */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full space-y-6">
        {/* Floating/Top Alert */}
        {alert.show && (
          <div
            className={`p-4 rounded-2xl border shadow-sm flex items-start justify-between gap-3 transition-all ${
              alert.type === 'success'
                ? 'bg-emerald-50 border-emerald-300 text-emerald-900'
                : alert.type === 'error'
                ? 'bg-rose-50 border-rose-300 text-rose-900'
                : alert.type === 'warning'
                ? 'bg-amber-50 border-amber-300 text-amber-900'
                : 'bg-blue-50 border-blue-300 text-blue-900'
            }`}
          >
            <div className="flex items-start gap-3">
              {alert.type === 'success' ? (
                <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
              ) : (
                <AlertCircle
                  className={`w-5 h-5 shrink-0 mt-0.5 ${
                    alert.type === 'error'
                      ? 'text-rose-600'
                      : alert.type === 'warning'
                      ? 'text-amber-600'
                      : 'text-blue-600'
                  }`}
                />
              )}
              <div>
                {alert.title && <h4 className="font-bold text-sm mb-0.5">{alert.title}</h4>}
                <p className="text-xs leading-relaxed">{alert.message}</p>
                {alert.type === 'success' && activeTab !== 'inventory' && (
                  <button
                    onClick={() => setActiveTab('inventory')}
                    className="mt-2 text-xs font-bold text-emerald-800 hover:text-emerald-950 underline inline-flex items-center gap-1"
                  >
                    Buka Katalog Laptop &amp; Perangkat &rarr;
                  </button>
                )}
              </div>
            </div>
            <button
              onClick={() => setAlert((prev) => ({ ...prev, show: false }))}
              className="text-slate-400 hover:text-slate-700 p-1 rounded-lg"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        )}

        {/* Sub-Header for Form and Catalog Modes */}
        {(activeTab === 'pc' || activeTab === 'akun' || activeTab === 'mikrotik') ? (
          <div className="bg-white rounded-xl border border-slate-200 p-3 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div className="flex items-center gap-2">
              <button
                onClick={() => setActiveTab('spreadsheet')}
                className="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
              >
                <span>&larr;</span>
                <span>Kembali ke Spreadsheet</span>
              </button>
            </div>

            <div className="flex items-center gap-1.5 self-end sm:self-auto">
              <button
                onClick={() => setActiveTab('pc')}
                className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                  activeTab === 'pc' ? 'bg-[#0B1B3D] text-white' : 'text-slate-600 hover:bg-slate-100'
                }`}
              >
                Form PC
              </button>
              <button
                onClick={() => setActiveTab('akun')}
                className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                  activeTab === 'akun' ? 'bg-[#0B1B3D] text-white' : 'text-slate-600 hover:bg-slate-100'
                }`}
              >
                Form Akun Sales
              </button>
              <button
                onClick={() => setActiveTab('mikrotik')}
                className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                  activeTab === 'mikrotik' ? 'bg-[#0B1B3D] text-white' : 'text-slate-600 hover:bg-slate-100'
                }`}
              >
                Form MikroTik
              </button>
            </div>
          </div>
        ) : (activeTab === 'inventory' || activeTab === 'phones') ? (
          <div className="bg-white rounded-xl border border-slate-200 p-3 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div className="flex items-center gap-2">
              <button
                onClick={() => setActiveTab('spreadsheet')}
                className="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
              >
                <span>&larr;</span>
                <span>Spreadsheet</span>
              </button>
            </div>

            <div className="inline-flex rounded-lg bg-slate-100 p-1">
              <button
                onClick={() => setActiveTab('inventory')}
                className={`px-3 py-1.5 rounded-md text-xs font-semibold transition flex items-center gap-1.5 ${
                  activeTab === 'inventory'
                    ? 'bg-white text-slate-900 shadow-xs'
                    : 'text-slate-600 hover:text-slate-900'
                }`}
              >
                <Laptop className="w-3.5 h-3.5" />
                <span>Laptop &amp; PC ({deviceCount})</span>
              </button>

              <button
                onClick={() => setActiveTab('phones')}
                className={`px-3 py-1.5 rounded-md text-xs font-semibold transition flex items-center gap-1.5 ${
                  activeTab === 'phones'
                    ? 'bg-white text-slate-900 shadow-xs'
                    : 'text-slate-600 hover:text-slate-900'
                }`}
              >
                <Smartphone className="w-3.5 h-3.5" />
                <span>HP Sales ({phoneCount})</span>
              </button>
            </div>
          </div>
        ) : null}

        {/* Tab Utama: Database Spreadsheet 1:1 & CRUD Lengkap */}
        {activeTab === 'spreadsheet' && (
          <SpreadsheetView
            records={records}
            mikrotikAccounts={mikrotikAccounts}
            onRefresh={loadData}
            onOpenPcForm={() => setActiveTab('pc')}
            onOpenAkunForm={() => setActiveTab('akun')}
            onOpenMikrotikForm={() => setActiveTab('mikrotik')}
            onOpenSettings={() => setIsGasModalOpen(true)}
            onOpenImportModal={() => setIsImportModalOpen(true)}
          />
        )}

        {/* Tab Audit: Cek Semua Spreadsheet & Live CRUD */}
        {activeTab === 'audit' && (
          <SpreadsheetAudit
            config={config}
            onOpenSettings={() => setIsGasModalOpen(true)}
            onRefreshAllData={loadData}
          />
        )}

        {/* Tab 1: Hardware & Laptop Inventory (CLEAN & NON-CREDENTIAL) */}
        {activeTab === 'inventory' && (
          <DeviceInventory
            records={records}
            onRefresh={loadData}
            onOpenNewPcForm={() => setActiveTab('pc')}
            onOpenImportModal={() => setIsImportModalOpen(true)}
          />
        )}

        {/* Tab 2: Smartphone & Handphone Sales Inventory (DISTINCTIVE MOBILE VIEW) */}
        {activeTab === 'phones' && (
          <PhoneInventory
            records={records}
            onRefresh={loadData}
            onOpenNewPhoneForm={() => setActiveTab('akun')}
          />
        )}

        {/* Tab MikroTik: Generator Akun Sales */}
        {activeTab === 'mikrotik' && (
          <MikrotikGenerator
            onSuccess={(msg) => showAlert(msg, 'success', 'Akun MikroTik Berhasil!')}
          />
        )}

        {/* Tab 2: Superadmin Executive Summary & Dashboard */}
        {activeTab === 'superadmin' && (
          <SuperadminSummary
            records={records}
            onRefresh={loadData}
            onOpenSettings={() => setIsGasModalOpen(true)}
          />
        )}

        {/* Tab 3: Form Input Laptop / PC */}
        {activeTab === 'pc' && (
          <FormPC
            onSuccess={handleFormSuccess}
            onError={handleFormError}
            onOpenDxDiagGuide={() => setIsDxDiagModalOpen(true)}
            onOpenSettings={() => setIsGasModalOpen(true)}
            hasGasUrl={Boolean(config.gasUrl)}
          />
        )}

        {/* Tab 4: Form Input Akun & HP */}
        {activeTab === 'akun' && (
          <FormAkun
            onSuccess={handleFormSuccess}
            onError={handleFormError}
            onOpenSettings={() => setIsGasModalOpen(true)}
            hasGasUrl={Boolean(config.gasUrl)}
          />
        )}

        {/* Tab 5: Master History Ledger */}
        {activeTab === 'history' && (
          <HistoryTable
            records={records}
            onRefresh={loadData}
            onOpenSettings={() => setIsGasModalOpen(true)}
            hasGasUrl={Boolean(config.gasUrl)}
          />
        )}

        {/* Tab 6: Google Apps Script & Drive Guide */}
        {activeTab === 'gas_guide' && (
          <GasGuideTab onOpenSettingsModal={() => setIsGasModalOpen(true)} />
        )}
      </main>

      {/* Modals */}
      <GasSetupModal
        isOpen={isGasModalOpen}
        onClose={() => setIsGasModalOpen(false)}
        onOpenGuide={() => setActiveTab('gas_guide')}
        onConfigUpdated={handleConfigUpdated}
      />

      <DxDiagGuideModal
        isOpen={isDxDiagModalOpen}
        onClose={() => setIsDxDiagModalOpen(false)}
      />

      <ImportSpreadsheetModal
        isOpen={isImportModalOpen}
        onClose={() => setIsImportModalOpen(false)}
        records={records}
        mikrotikAccounts={mikrotikAccounts}
        onRefresh={loadData}
        onDataImported={(msg) => {
          loadData();
          showAlert(msg, 'success', 'Sinkronisasi Data Berhasil!');
        }}
      />

      {/* Panduan Khusus Hubungkan Drive Akun newofficialsyamanah@gmail.com */}
      <DriveConnectGuideModal
        isOpen={isDriveGuideOpen}
        onClose={() => setIsDriveGuideOpen(false)}
        config={config}
        onConfigUpdated={handleConfigUpdated}
      />

      {/* Footer Berstandar Syamanah.com */}
      <footer className="bg-[#0B1B3D] text-slate-300 border-t border-[#1C2C54] mt-12 py-8 text-xs">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="flex flex-col md:flex-row items-center justify-between gap-6 pb-6 border-b border-white/10">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-[#071328] flex items-center justify-center font-extrabold text-lg shadow-md shadow-amber-500/20">
                S
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <span className="font-extrabold tracking-wider text-white text-sm">SYAMANAH</span>
                  <span className="text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-widest bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    Garment &amp; Apparel Bandung
                  </span>
                </div>
                <p className="text-slate-400 text-xs mt-0.5">
                  Portal Resmi Manajemen Laptop / PC Workstation, Smartphone Sales &amp; MikroTik Hotspot
                </p>
              </div>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-4 text-xs font-medium">
              <button
                onClick={() => setActiveTab('inventory')}
                className="text-slate-300 hover:text-amber-400 transition"
              >
                💻 Katalog Laptop &amp; PC ({deviceCount})
              </button>
              <span className="text-slate-600">·</span>
              <button
                onClick={() => setActiveTab('phones')}
                className="text-slate-300 hover:text-amber-400 transition"
              >
                📱 Katalog HP &amp; Akun ({phoneCount})
              </button>
              <span className="text-slate-600">·</span>
              <button
                onClick={() => setActiveTab('mikrotik')}
                className="text-slate-300 hover:text-amber-400 transition"
              >
                📶 Akun MikroTik Sales
              </button>
              <span className="text-slate-600">·</span>
              <button
                onClick={() => setIsDriveGuideOpen(true)}
                className="text-amber-300 hover:text-amber-200 transition underline underline-offset-4"
              >
                📂 Hubungkan Drive (newofficialsyamanah@gmail.com)
              </button>
            </div>
          </div>

          <div className="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-400 text-[11px]">
            <div>
              &copy; {new Date().getFullYear()} CV Syamanah Berkah Abadi &middot; Jl. Maleber Utara No. 528 / 187B, Kota Bandung &middot;{' '}
              <a href="https://syamanah.com" target="_blank" rel="noreferrer" className="text-amber-400 hover:underline">
                syamanah.com
              </a>
            </div>
            <div className="flex items-center gap-3">
              <span className="inline-flex items-center gap-1.5 text-emerald-400">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Database Spreadsheet Terkoneksi 1:1
              </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
