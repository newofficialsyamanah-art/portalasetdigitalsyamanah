import React, { useState } from 'react';
import {
  X,
  FileSpreadsheet,
  DownloadCloud,
  Upload,
  CheckCircle2,
  AlertCircle,
  FileText,
  RefreshCw,
  Download
} from 'lucide-react';
import {
  pullDataFromGoogleSpreadsheet,
  importSpreadsheetCsv
} from '../services/gasService';
import { parseExcelOrCsvFile, exportDatabaseToExcel } from '../utils/excelHelper';
import { bulkMigrateToFirestore } from '../services/firestoreService';
import { SubmissionRecord, MikrotikAccount } from '../types';

interface ImportSpreadsheetModalProps {
  isOpen: boolean;
  onClose: () => void;
  onDataImported: (message: string) => void;
  records: SubmissionRecord[];
  mikrotikAccounts: MikrotikAccount[];
  onRefresh: () => void;
}

export const ImportSpreadsheetModal: React.FC<ImportSpreadsheetModalProps> = ({
  isOpen,
  onClose,
  onDataImported,
  records,
  mikrotikAccounts,
  onRefresh
}) => {
  const [activeTab, setActiveTab] = useState<'excel' | 'pull' | 'paste'>('excel');
  const [loading, setLoading] = useState(false);
  const [pastedText, setPastedText] = useState('');
  const [errorMsg, setErrorMsg] = useState('');
  const [successMsg, setSuccessMsg] = useState('');
  const [isDragging, setIsDragging] = useState(false);

  if (!isOpen) return null;

  const pcRecords = records.filter((r) => r.type === 'pc');
  const akunRecords = records.filter((r) => r.type === 'akun');

  // Common file processing logic for both input select and drag-and-drop
  const processFile = async (file: File) => {
    setLoading(true);
    setErrorMsg('');
    setSuccessMsg('');

    try {
      const parsed = await parseExcelOrCsvFile(file);
      if (parsed.totalCount === 0) {
        setErrorMsg('Tidak ditemukan baris data pada berkas tersebut. Pastikan berkas memiliki kolom data.');
        setLoading(false);
        return;
      }

      const allSubmissions = [...parsed.pcRecords, ...parsed.akunRecords];

      // 1. Save directly to Cloud Firestore (Permanent)
      bulkMigrateToFirestore(allSubmissions, parsed.mikrotikAccounts).catch(console.warn);

      // 2. Send to server API to merge / save
      const res = await fetch('/api/database/import', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          submissions: allSubmissions,
          mikrotik: parsed.mikrotikAccounts
        })
      });

      if (!res.ok) {
        throw new Error('Gagal menyimpan data ke database server');
      }

      onRefresh();
      onDataImported(`Berhasil mengimpor ${parsed.totalCount} baris data dari "${file.name}"! (${parsed.pcRecords.length} PC, ${parsed.akunRecords.length} Akun Sales, ${parsed.mikrotikAccounts.length} MikroTik)`);
      onClose();
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Gagal memproses berkas Excel.';
      setErrorMsg(msg);
    } finally {
      setLoading(false);
    }
  };

  // Handle Excel file upload via file input
  const handleExcelUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      await processFile(file);
    }
    if (e.target) e.target.value = '';
  };

  // Handle Drag & Drop
  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(false);
  };

  const handleDrop = async (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(false);
    const file = e.dataTransfer.files?.[0];
    if (file) {
      await processFile(file);
    }
  };

  // Export database to multi-sheet Excel
  const handleExportFullExcel = async () => {
    try {
      const res = await fetch('/api/database/export-excel');
      if (!res.ok) throw new Error('Gagal mengunduh berkas dari server');

      const blob = await res.blob();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.style.display = 'none';
      link.href = url;
      link.setAttribute('download', `Database_Syamanah_${new Date().toISOString().slice(0, 10)}.xlsx`);
      document.body.appendChild(link);
      link.click();
      setTimeout(() => {
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      }, 500);

      setSuccessMsg('Berkas Excel lengkap berhasil diunduh!');
      setTimeout(() => setSuccessMsg(''), 4000);
    } catch (err: unknown) {
      setErrorMsg('Gagal mengunduh berkas Excel: ' + (err instanceof Error ? err.message : String(err)));
    }
  };

  // Pull from Google Spreadsheet
  const handlePullRemote = async () => {
    setLoading(true);
    setErrorMsg('');
    try {
      const res = await pullDataFromGoogleSpreadsheet();
      if (res.success) {
        onDataImported(res.message);
        onClose();
      } else {
        setErrorMsg(res.message);
      }
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Gagal menghubungi server.';
      setErrorMsg(msg);
    } finally {
      setLoading(false);
    }
  };

  // Paste text import
  const handleImportPasted = () => {
    if (!pastedText.trim()) {
      setErrorMsg('Harap tempelkan data teks atau baris tabel terlebih dahulu!');
      return;
    }
    const result = importSpreadsheetCsv(pastedText);
    if (result.count > 0) {
      onDataImported(`Berhasil mengimpor ${result.count} data perangkat dari tabel spreadsheet!`);
      onClose();
    } else {
      setErrorMsg(result.error || 'Gagal memproses baris data.');
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden my-6">
        {/* Header */}
        <div className="bg-[#0B1B3D] text-white px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
              <FileSpreadsheet className="w-5 h-5" />
            </div>
            <div>
              <h3 className="text-base font-bold text-white">
                Kelola Database (Excel / CSV)
              </h3>
              <p className="text-xs text-slate-300">
                Ekspor dan impor data inventaris tanpa ketergantungan link luar
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Tab Selection */}
        <div className="border-b border-slate-200 bg-slate-50 px-6 pt-3 flex gap-4 text-xs font-semibold">
          <button
            onClick={() => {
              setActiveTab('excel');
              setErrorMsg('');
            }}
            className={`pb-2.5 border-b-2 transition ${
              activeTab === 'excel'
                ? 'border-amber-500 text-slate-900 font-bold'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            Impor &amp; Ekspor Excel (.xlsx)
          </button>
          <button
            onClick={() => {
              setActiveTab('pull');
              setErrorMsg('');
            }}
            className={`pb-2.5 border-b-2 transition ${
              activeTab === 'pull'
                ? 'border-amber-500 text-slate-900 font-bold'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            Tarik Google Spreadsheet
          </button>
          <button
            onClick={() => {
              setActiveTab('paste');
              setErrorMsg('');
            }}
            className={`pb-2.5 border-b-2 transition ${
              activeTab === 'paste'
                ? 'border-amber-500 text-slate-900 font-bold'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            Salin / Tempel Teks
          </button>
        </div>

        {/* Body Content */}
        <div className="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          {errorMsg && (
            <div className="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2">
              <AlertCircle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
              <div className="flex-1 leading-relaxed">{errorMsg}</div>
            </div>
          )}

          {successMsg && (
            <div className="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
              <div className="flex-1 leading-relaxed">{successMsg}</div>
            </div>
          )}

          {/* Tab 1: Excel Import & Export */}
          {activeTab === 'excel' && (
            <div className="space-y-4">
              {/* Export Box */}
              <div className="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <h4 className="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                    <Download className="w-4 h-4 text-emerald-600" />
                    <span>Cadangkan / Ekspor ke Excel (.xlsx)</span>
                  </h4>
                  <p className="text-[11px] text-slate-500 mt-0.5">
                    Mengunduh 1 berkas Excel dengan 3 sheet lengkap: PC &amp; Laptop ({pcRecords.length}), Akun Sales ({akunRecords.length}), MikroTik ({mikrotikAccounts.length}).
                  </p>
                </div>
                <button
                  onClick={handleExportFullExcel}
                  className="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shrink-0"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Unduh Excel</span>
                </button>
              </div>

              {/* Import Box with Native Drag & Drop */}
              <div
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                className={`p-6 rounded-2xl border-2 border-dashed transition-all text-center space-y-3 ${
                  isDragging
                    ? 'border-blue-600 bg-blue-50/80 scale-[1.01]'
                    : 'border-amber-300 bg-amber-50/40 hover:bg-amber-50/70'
                }`}
              >
                <div className={`w-12 h-12 rounded-2xl mx-auto flex items-center justify-center transition ${
                  isDragging ? 'bg-blue-600 text-white' : 'bg-amber-100 text-amber-700'
                }`}>
                  <Upload className="w-6 h-6" />
                </div>
                <div>
                  <h4 className="font-extrabold text-sm text-slate-900">
                    {isDragging ? 'Lepaskan Berkas Excel di Sini!' : 'Tarik & Lepas (Drag & Drop) atau Pilih Berkas Excel'}
                  </h4>
                  <p className="text-[11px] text-slate-600 mt-1 max-w-sm mx-auto">
                    Mendukung semua format <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b>. Seluruh data langsung masuk ke tabel &amp; Cloud database.
                  </p>
                </div>

                <div className="pt-2">
                  <input
                    id="excel-file-upload-input"
                    type="file"
                    accept=".xlsx,.xls,.csv"
                    onChange={handleExcelUpload}
                    disabled={loading}
                    className="hidden"
                  />
                  <label
                    htmlFor="excel-file-upload-input"
                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0B1B3D] hover:bg-[#132A5E] text-white rounded-xl font-bold text-xs cursor-pointer shadow-md transition active:scale-95"
                  >
                    <Upload className="w-4 h-4 text-amber-400" />
                    <span>{loading ? 'Sedang Memproses Berkas...' : 'Pilih Berkas Excel / CSV'}</span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {/* Tab 2: Remote Pull */}
          {activeTab === 'pull' && (
            <div className="space-y-4">
              <div className="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-2">
                <h4 className="font-bold flex items-center gap-1.5 text-slate-900">
                  <RefreshCw className="w-4 h-4 text-slate-600" />
                  <span>Tarik dari Google Spreadsheet:</span>
                </h4>
                <p className="text-slate-500 leading-relaxed text-[11px]">
                  Aplikasi akan membaca seluruh baris data dari URL Apps Script yang saat ini terpasang di sistem.
                </p>
              </div>

              <button
                type="button"
                onClick={handlePullRemote}
                disabled={loading}
                className="w-full py-2.5 bg-[#0B1B3D] hover:bg-[#132A5E] text-white font-semibold text-xs rounded-xl shadow-xs flex items-center justify-center gap-2 transition disabled:opacity-50"
              >
                {loading ? (
                  <>
                    <RefreshCw className="w-4 h-4 animate-spin text-amber-400" />
                    <span>Sedang Menghubungi Spreadsheet...</span>
                  </>
                ) : (
                  <>
                    <DownloadCloud className="w-4 h-4 text-amber-400" />
                    <span>Tarik Data Sekarang</span>
                  </>
                )}
              </button>
            </div>
          )}

          {/* Tab 3: Paste */}
          {activeTab === 'paste' && (
            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-slate-700 mb-1">
                  Salin &amp; Tempel Baris Tabel:
                </label>
                <textarea
                  rows={6}
                  value={pastedText}
                  onChange={(e) => setPastedText(e.target.value)}
                  placeholder="Salin baris dari tabel Anda (Ctrl+C) lalu tempel (Ctrl+V) di sini..."
                  className="w-full p-3 font-mono text-[11px] rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                />
              </div>

              <button
                type="button"
                onClick={handleImportPasted}
                className="w-full py-2.5 bg-[#0B1B3D] hover:bg-[#132A5E] text-white font-semibold text-xs rounded-xl transition shadow-xs"
              >
                Impor Data Teks
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
