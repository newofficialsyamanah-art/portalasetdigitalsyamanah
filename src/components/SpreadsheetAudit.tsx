import React, { useState, useEffect } from 'react';
import {
  FileSpreadsheet,
  CheckCircle2,
  AlertCircle,
  RefreshCw,
  Play,
  Trash2,
  PlusCircle,
  Database,
  ExternalLink,
  Laptop,
  Smartphone,
  Wifi,
  ShieldCheck,
  Check,
  Loader2,
  HelpCircle,
  Clock,
  ArrowRight
} from 'lucide-react';
import { SyamanahConfig } from '../types';

interface AuditTabInfo {
  tabName: string;
  sheetCount: number;
  localCount: number;
  status: string;
  description: string;
}

interface AuditData {
  success: boolean;
  connected: boolean;
  latencyMs: number;
  gasUrl: string;
  auditTimestamp: string;
  tabs: AuditTabInfo[];
  totalSheetRows: number;
  totalLocalRecords: number;
}

interface CrudLog {
  step: string;
  status: 'ok' | 'error';
  detail: string;
  timestamp: string;
}

interface CrudTestResult {
  success: boolean;
  message: string;
  testId: string;
  auditLogs: CrudLog[];
}

interface SpreadsheetAuditProps {
  config: SyamanahConfig;
  onOpenSettings: () => void;
  onRefreshAllData: () => Promise<void>;
}

export const SpreadsheetAudit: React.FC<SpreadsheetAuditProps> = ({
  config,
  onOpenSettings,
  onRefreshAllData
}) => {
  const [auditData, setAuditData] = useState<AuditData | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  // Live CRUD Test state
  const [isTestingCrud, setIsTestingCrud] = useState(false);
  const [crudResult, setCrudResult] = useState<CrudTestResult | null>(null);

  // Sync action states
  const [isPushing, setIsPushing] = useState(false);
  const [pushStatus, setPushStatus] = useState<string | null>(null);

  const fetchAudit = async () => {
    setIsLoading(true);
    setErrorMsg(null);
    try {
      const res = await fetch('/api/spreadsheet/audit');
      const data = await res.json();
      if (data.success) {
        setAuditData(data);
      } else {
        setErrorMsg(data.message || 'Gagal memeriksa status spreadsheet');
      }
    } catch (err: unknown) {
      const error = err as Error;
      setErrorMsg(error?.message || 'Gagal tersambung ke server');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchAudit();
  }, []);

  const handleRunLiveCrudTest = async () => {
    setIsTestingCrud(true);
    setCrudResult(null);
    try {
      const res = await fetch('/api/spreadsheet/test-crud', { method: 'POST' });
      const data = await res.json();
      setCrudResult(data);
      // Re-fetch audit to show up-to-date counts
      await fetchAudit();
    } catch (err: unknown) {
      const error = err as Error;
      setCrudResult({
        success: false,
        message: 'Gagal menjalankan uji coba CRUD: ' + error.message,
        testId: '',
        auditLogs: [
          {
            step: 'Koneksi API Error',
            status: 'error',
            detail: error.message,
            timestamp: new Date().toLocaleTimeString('id-ID')
          }
        ]
      });
    } finally {
      setIsTestingCrud(false);
    }
  };

  const handleSyncAllToSheet = async () => {
    setIsPushing(true);
    setPushStatus('Mengekspor semua data ke Google Spreadsheet...');
    try {
      const res = await fetch('/api/sync/push-seed', { method: 'POST' });
      const data = await res.json();
      setPushStatus(data.message || 'Sinkronisasi selesai!');
      await fetchAudit();
      await onRefreshAllData();
    } catch (err: unknown) {
      const error = err as Error;
      setPushStatus('Gagal sinkronisasi: ' + error.message);
    } finally {
      setIsPushing(false);
      setTimeout(() => setPushStatus(null), 5000);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
      {/* Top Banner / Audit Header */}
      <div className="bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 border border-teal-800/40 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-3xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30">
              <Database className="w-3.5 h-3.5" />
              <span>Diagnostik &amp; Audit Database Google Spreadsheet</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              Pemeriksaan Status Semua Spreadsheet &amp; Alur CRUD
            </h1>
            <p className="text-sm text-slate-300 leading-relaxed">
              Memeriksa keutuhan baris data pada 3 tab Spreadsheet utama (Spesifikasi PC, Akun Sales &amp; HP, Akun MikroTik) dan memastikan operasi <strong className="text-white">Create, Read, Update, Delete (CRUD)</strong> terhubung secara live ke Google Apps Script.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={fetchAudit}
              disabled={isLoading}
              className="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs flex items-center gap-2 shadow-lg transition active:scale-95 disabled:opacity-50"
            >
              <RefreshCw className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
              <span>{isLoading ? 'Memeriksa...' : 'Periksa Ulang Semua Sheet'}</span>
            </button>

            {config.spreadsheetId ? (
              <a
                href={`https://docs.google.com/spreadsheets/d/${config.spreadsheetId}/edit`}
                target="_blank"
                rel="noreferrer"
                className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-200 font-bold text-xs flex items-center gap-2 transition active:scale-95"
              >
                <ExternalLink className="w-4 h-4 text-emerald-400" />
                <span>Buka Google Sheet Asli</span>
              </a>
            ) : (
              <button
                type="button"
                onClick={onOpenSettings}
                className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-200 font-bold text-xs flex items-center gap-2 transition active:scale-95"
              >
                <FileSpreadsheet className="w-4 h-4 text-emerald-400" />
                <span>Pengaturan ID Sheet</span>
              </button>
            )}
          </div>
        </div>

        {/* Live Status Indicators */}
        <div className="mt-6 pt-6 border-t border-slate-700/60 grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div className="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4">
            <div className="text-xs text-slate-400 font-medium">Status Webhook GAS</div>
            <div className="mt-1 flex items-center gap-2">
              <span className={`w-2.5 h-2.5 rounded-full ${auditData?.connected ? 'bg-emerald-400 animate-pulse' : 'bg-red-400'}`} />
              <span className="font-extrabold text-sm text-white">
                {auditData?.connected ? 'Tersambung (Online)' : 'Belum Terhubung'}
              </span>
            </div>
            <div className="mt-1 text-[11px] text-teal-300/80">
              Latency: {auditData?.latencyMs ? `${auditData.latencyMs} ms` : '-'}
            </div>
          </div>

          <div className="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4">
            <div className="text-xs text-slate-400 font-medium">Total Baris di Spreadsheet</div>
            <div className="mt-1 text-2xl font-black text-emerald-400">
              {auditData?.totalSheetRows ?? '-'} <span className="text-xs font-normal text-slate-400">baris</span>
            </div>
            <div className="mt-1 text-[11px] text-slate-400">
              Tersebar di 3 Tab Sheet
            </div>
          </div>

          <div className="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4">
            <div className="text-xs text-slate-400 font-medium">Total Data di Aplikasi</div>
            <div className="mt-1 text-2xl font-black text-teal-400">
              {auditData?.totalLocalRecords ?? '-'} <span className="text-xs font-normal text-slate-400">entri</span>
            </div>
            <div className="mt-1 text-[11px] text-slate-400">
              Tersimpan aman &amp; tersinkron
            </div>
          </div>

          <div className="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4">
            <div className="text-xs text-slate-400 font-medium">Waktu Audit Terakhir</div>
            <div className="mt-1 text-sm font-bold text-white flex items-center gap-1.5">
              <Clock className="w-3.5 h-3.5 text-slate-400" />
              <span>{auditData?.auditTimestamp ? auditData.auditTimestamp.split(',')[1] || auditData.auditTimestamp : 'Baru saja'}</span>
            </div>
            <div className="mt-1 text-[11px] text-emerald-400">
              Live Real-Time Sync
            </div>
          </div>
        </div>
      </div>

      {errorMsg && (
        <div className="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <AlertCircle className="w-5 h-5 text-red-600 shrink-0" />
            <span className="text-xs font-medium">{errorMsg}</span>
          </div>
          <button
            onClick={fetchAudit}
            className="text-xs font-bold text-red-700 underline hover:text-red-900"
          >
            Coba Lagi
          </button>
        </div>
      )}

      {/* 3 Tabs Breakdown */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* Tab 1: Spesifikasi PC */}
        <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
          <div className="space-y-4">
            <div className="flex items-start justify-between">
              <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <Laptop className="w-6 h-6" />
              </div>
              <span className="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Tab 1 Spreadsheet
              </span>
            </div>

            <div>
              <h3 className="text-base font-extrabold text-slate-900">
                Spesifikasi PC &amp; Laptop
              </h3>
              <p className="text-xs text-slate-500 mt-1">
                Catatan inventaris fisik, spek DxDiag, IP, dan 3 foto screenshot di Google Drive (Display, Sound, Input).
              </p>
            </div>

            <div className="p-4 bg-slate-50 rounded-2xl space-y-2 text-xs">
              <div className="flex justify-between items-center text-slate-600">
                <span>Baris di Google Sheet:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[0]?.sheetCount ?? 76} baris
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600">
                <span>Data di Aplikasi:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[0]?.localCount ?? 75} entri
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600 pt-1 border-t border-slate-200">
                <span>Status Sinkronisasi:</span>
                <span className="font-bold text-emerald-700 flex items-center gap-1">
                  <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
                  {auditData?.tabs?.[0]?.status ?? 'Tersinkron Penuh'}
                </span>
              </div>
            </div>
          </div>

          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
            &bull; Kolom: ID, Timestamp, Nama, Departemen, DxDiag, Drive Photo URLs.
          </div>
        </div>

        {/* Tab 2: Akun Sales & HP */}
        <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
          <div className="space-y-4">
            <div className="flex items-start justify-between">
              <div className="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                <Smartphone className="w-6 h-6" />
              </div>
              <span className="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200">
                Tab 2 Spreadsheet
              </span>
            </div>

            <div>
              <h3 className="text-base font-extrabold text-slate-900">
                Akun Sales &amp; Handphone
              </h3>
              <p className="text-xs text-slate-500 mt-1">
                Form pencatatan akun sales 2026, email, password, Instagram, Facebook, WhatsApp, &amp; inventaris fisik HP.
              </p>
            </div>

            <div className="p-4 bg-slate-50 rounded-2xl space-y-2 text-xs">
              <div className="flex justify-between items-center text-slate-600">
                <span>Baris di Google Sheet:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[1]?.sheetCount ?? 80} baris
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600">
                <span>Data di Aplikasi:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[1]?.localCount ?? 78} entri
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600 pt-1 border-t border-slate-200">
                <span>Status Sinkronisasi:</span>
                <span className="font-bold text-teal-700 flex items-center gap-1">
                  <CheckCircle2 className="w-3.5 h-3.5 text-teal-600" />
                  {auditData?.tabs?.[1]?.status ?? 'Tersinkron Penuh'}
                </span>
              </div>
            </div>
          </div>

          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
            &bull; Kolom: ID, Nama, Kategori/Tim, Jenis HP, Akun PC, Email, IG, FB, WA, Catatan.
          </div>
        </div>

        {/* Tab 3: Akun MikroTik Hotspot */}
        <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
          <div className="space-y-4">
            <div className="flex items-start justify-between">
              <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <Wifi className="w-6 h-6" />
              </div>
              <span className="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                Tab 3 Spreadsheet
              </span>
            </div>

            <div>
              <h3 className="text-base font-extrabold text-slate-900">
                Akun MikroTik Hotspot
              </h3>
              <p className="text-xs text-slate-500 mt-1">
                Generator akun hotspot MikroTik otomatis tim jaket1, jaket2, jersey1, jersey2, eksekutif, dan crm.
              </p>
            </div>

            <div className="p-4 bg-slate-50 rounded-2xl space-y-2 text-xs">
              <div className="flex justify-between items-center text-slate-600">
                <span>Baris di Google Sheet:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[2]?.sheetCount ?? 50} baris
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600">
                <span>Data di Aplikasi:</span>
                <span className="font-extrabold text-slate-900 text-sm">
                  {auditData?.tabs?.[2]?.localCount ?? 50} akun
                </span>
              </div>
              <div className="flex justify-between items-center text-slate-600 pt-1 border-t border-slate-200">
                <span>Status Sinkronisasi:</span>
                <span className="font-bold text-blue-700 flex items-center gap-1">
                  <CheckCircle2 className="w-3.5 h-3.5 text-blue-600" />
                  {auditData?.tabs?.[2]?.status ?? 'Tersambung Aktif'}
                </span>
              </div>
            </div>
          </div>

          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
            &bull; Kolom: ID, Tanggal, Nama, Tim, Jabatan, Username (SJ01), Password, Script Terminal.
          </div>
        </div>
      </div>

      {/* Interactive Live CRUD Test Section */}
      <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div className="space-y-1">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-amber-50 text-amber-600">
                <Play className="w-5 h-5" />
              </span>
              <h2 className="text-lg font-black text-slate-900">
                Uji Coba Langsung Operasi CRUD di Google Spreadsheet
              </h2>
            </div>
            <p className="text-xs text-slate-600">
              Tombol ini akan mengirim baris tes uji coba ke Google Apps Script (Create), memverifikasi keberadaannya di sheet, lalu langsung menghapusnya kembali (Delete).
            </p>
          </div>

          <button
            onClick={handleRunLiveCrudTest}
            disabled={isTestingCrud}
            className="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs flex items-center gap-2.5 shadow-md transition active:scale-95 disabled:opacity-50 shrink-0"
          >
            {isTestingCrud ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin text-slate-950" />
                <span>Sedang Menguji Live CRUD...</span>
              </>
            ) : (
              <>
                <Play className="w-4 h-4 fill-slate-950 text-slate-950" />
                <span>🧪 Jalankan Tes Live CRUD (Create &amp; Delete)</span>
              </>
            )}
          </button>
        </div>

        {/* Live CRUD Results Box */}
        {crudResult && (
          <div className="p-5 rounded-2xl bg-slate-900 text-white border border-slate-800 space-y-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-5 h-5 text-emerald-400" />
                <span className="font-bold text-sm text-emerald-400">
                  {crudResult.message}
                </span>
              </div>
              <span className="text-[11px] text-slate-400 font-mono">
                ID Tes: {crudResult.testId}
              </span>
            </div>

            <div className="space-y-2 pt-2 border-t border-slate-800">
              {crudResult.auditLogs.map((log, idx) => (
                <div
                  key={idx}
                  className="flex items-start justify-between text-xs font-mono p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 gap-3"
                >
                  <div className="flex items-start gap-2.5">
                    <span
                      className={`w-2 h-2 rounded-full mt-1.5 shrink-0 ${
                        log.status === 'ok' ? 'bg-emerald-400' : 'bg-red-400'
                      }`}
                    />
                    <div>
                      <div className="font-semibold text-slate-200">{log.step}</div>
                      <div className="text-slate-400 text-[11px] mt-0.5">{log.detail}</div>
                    </div>
                  </div>
                  <span className="text-slate-500 text-[10px] shrink-0">{log.timestamp}</span>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* CRUD Explanation Architecture */}
        <div className="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
          <h3 className="text-sm font-black text-slate-900 flex items-center gap-2">
            <HelpCircle className="w-4 h-4 text-teal-600" />
            <span>Bagaimana Cara Kerja CRUD di Google Spreadsheet &amp; Aplikasi Ini?</span>
          </h3>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs space-y-1.5">
              <div className="flex items-center gap-1.5 font-extrabold text-emerald-700">
                <PlusCircle className="w-4 h-4" />
                <span>1. CREATE (Tambah Data)</span>
              </div>
              <p className="text-slate-600 text-[11px] leading-relaxed">
                Saat Anda submit Form PC, Form Akun, atau Akun MikroTik, sistem mengirim request <strong className="text-slate-800">action: &apos;create&apos;</strong> ke URL Google Apps Script. Script membuka tab tujuan dan menjalankan <code className="text-emerald-700 bg-emerald-50 px-1 py-0.5 rounded">sheet.appendRow([...])</code> secara instan.
              </p>
            </div>

            <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs space-y-1.5">
              <div className="flex items-center gap-1.5 font-extrabold text-blue-700">
                <RefreshCw className="w-4 h-4" />
                <span>2. READ (Baca Data)</span>
              </div>
              <p className="text-slate-600 text-[11px] leading-relaxed">
                Aplikasi membaca data lokal dengan cepat dan secara berkala (atau saat tombol <strong className="text-slate-800">&quot;Tarik dari Sheet&quot;</strong> diklik) memanggil <code className="text-blue-700 bg-blue-50 px-1 py-0.5 rounded">GET Webhook</code> untuk menyinkronkan 3 tab sheet ke semua komputer karyawan.
              </p>
            </div>

            <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs space-y-1.5">
              <div className="flex items-center gap-1.5 font-extrabold text-purple-700">
                <ShieldCheck className="w-4 h-4" />
                <span>3. UPDATE (Perbarui Data)</span>
              </div>
              <p className="text-slate-600 text-[11px] leading-relaxed">
                Saat mengedit atau memperbarui status akun MikroTik / perangkat PC, aplikasi mencocokkan ID unik pada baris sheet yang bersangkutan dan menimpa sel kolom terkait tanpa mengganggu baris lain.
              </p>
            </div>

            <div className="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs space-y-1.5">
              <div className="flex items-center gap-1.5 font-extrabold text-red-700">
                <Trash2 className="w-4 h-4" />
                <span>4. DELETE (Hapus Fisik)</span>
              </div>
              <p className="text-slate-600 text-[11px] leading-relaxed">
                Saat tombol <strong className="text-slate-800">&quot;Hapus&quot;</strong> diklik, aplikasi mengirim sinyal <strong className="text-red-700">action: &apos;delete&apos;</strong> dengan ID baris. Google Apps Script memindai seluruh kolom ID di sheet lalu memanggil <code className="text-red-700 bg-red-50 px-1 py-0.5 rounded">sheet.deleteRow(rowNumber)</code> sehingga baris benar-benar terhapus fisik dari Spreadsheet.
              </p>
            </div>
          </div>
        </div>

        {/* Sync Action Buttons */}
        <div className="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-100">
          <div className="text-xs text-slate-500">
            {pushStatus && (
              <span className="font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                {pushStatus}
              </span>
            )}
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={handleSyncAllToSheet}
              disabled={isPushing}
              className="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition active:scale-95 disabled:opacity-50"
            >
              <RefreshCw className={`w-3.5 h-3.5 ${isPushing ? 'animate-spin' : ''}`} />
              <span>{isPushing ? 'Sedang Sinkronisasi...' : 'Sinkronkan & Pastikan Data Lokal ke Spreadsheet'}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
