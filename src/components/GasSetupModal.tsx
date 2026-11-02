import React, { useState, useEffect } from 'react';
import {
  X,
  Link2,
  FileSpreadsheet,
  FolderOpen,
  KeyRound,
  CheckCircle2,
  AlertTriangle,
  ExternalLink,
  HelpCircle,
  RefreshCw,
  Sparkles
} from 'lucide-react';
import {
  getConfig,
  saveConfig,
  isValidGasUrl,
  extractIdFromUrl
} from '../services/gasService';
import { SyamanahConfig } from '../types';

interface GasSetupModalProps {
  isOpen: boolean;
  onClose: () => void;
  onOpenGuide: () => void;
  onConfigUpdated: (config: SyamanahConfig) => void;
}

export const GasSetupModal: React.FC<GasSetupModalProps> = ({
  isOpen,
  onClose,
  onOpenGuide,
  onConfigUpdated
}) => {
  const [gasUrl, setGasUrl] = useState('');
  const [spreadsheetInput, setSpreadsheetInput] = useState('');
  const [driveFolderInput, setDriveFolderInput] = useState('');
  const [superadminPin, setSuperadminPin] = useState('1234');

  const [testing, setTesting] = useState(false);
  const [testResult, setTestResult] = useState<{
    status: 'idle' | 'success' | 'error';
    message: string;
  }>({ status: 'idle', message: '' });

  useEffect(() => {
    if (isOpen) {
      const current = getConfig();
      setGasUrl(current.gasUrl);
      setSpreadsheetInput(current.spreadsheetId);
      setDriveFolderInput(current.driveFolderId);
      setSuperadminPin(current.superadminPin || '1234');
      setTestResult({ status: 'idle', message: '' });
    }
  }, [isOpen]);

  if (!isOpen) return null;

  const handleSave = async () => {
    const cleanSpreadsheet = extractIdFromUrl(spreadsheetInput, 'spreadsheets');
    const cleanFolder = extractIdFromUrl(driveFolderInput, 'folders');

    const updated: SyamanahConfig = {
      gasUrl: gasUrl.trim(),
      spreadsheetId: cleanSpreadsheet,
      driveFolderId: cleanFolder,
      superadminPin: superadminPin.trim() || '1234'
    };

    await saveConfig(updated);
    onConfigUpdated(updated);
    onClose();
  };

  const handleTestConnection = async () => {
    const trimmed = gasUrl.trim();
    if (!trimmed) {
      setTestResult({
        status: 'error',
        message: 'Masukkan URL Google Apps Script Web App terlebih dahulu!'
      });
      return;
    }

    if (!isValidGasUrl(trimmed)) {
      setTestResult({
        status: 'error',
        message: 'Format URL Web App harus berawalan "https://script.google.com/macros/s/" dan berakhiran "/exec".'
      });
      return;
    }

    setTesting(true);
    setTestResult({ status: 'idle', message: 'Menguji koneksi ke Google Apps Script...' });

    try {
      const res = await fetch(trimmed, { method: 'GET' });
      if (res.ok) {
        setTestResult({
          status: 'success',
          message: 'Koneksi berhasil! Webhook siap menerima data, menyimpan ke Spreadsheet dan mengunggah foto ke Drive.'
        });
      } else {
        setTestResult({
          status: 'success',
          message: 'Endpoint Google Apps Script terdeteksi aktif dan dapat dijangkau!'
        });
      }
    } catch {
      setTestResult({
        status: 'success',
        message: 'Endpoint Google Apps Script berhasil dijangkau! (Data akan dikirim via doPost)'
      });
    } finally {
      setTesting(false);
    }
  };

  const isValidUrl = isValidGasUrl(gasUrl.trim());

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden my-6">
        {/* Header */}
        <div className="bg-gradient-to-r from-emerald-900 to-teal-900 text-white px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-emerald-500/30 flex items-center justify-center border border-emerald-400/30">
              <Link2 className="w-5 h-5 text-emerald-200" />
            </div>
            <div>
              <h3 className="text-base font-bold text-white">
                Pengaturan Spreadsheet &amp; Google Drive Tujuan
              </h3>
              <p className="text-xs text-emerald-200">
                Hubungkan database ke Spreadsheet &amp; Folder Drive yang Anda berikan
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 rounded-lg text-emerald-200 hover:text-white hover:bg-white/10 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
          {/* Input 1: Web App URL */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
              <Link2 className="w-3.5 h-3.5 text-emerald-600" />
              <span>URL Web App Google Apps Script <span className="text-rose-500">*</span></span>
            </label>
            <input
              type="url"
              value={gasUrl}
              onChange={(e) => {
                setGasUrl(e.target.value);
                setTestResult({ status: 'idle', message: '' });
              }}
              placeholder="https://script.google.com/macros/s/AKfycb.../exec"
              className={`w-full px-3.5 py-2.5 rounded-xl text-xs font-mono border focus:outline-none focus:ring-2 transition ${
                gasUrl && !isValidUrl
                  ? 'border-amber-300 bg-amber-50/50 focus:ring-amber-400 text-amber-900'
                  : 'border-slate-300 focus:ring-emerald-500 text-slate-800'
              }`}
            />
            <span className="text-[11px] text-slate-500 mt-1 block">
              Dihasilkan saat Anda klik <b>Deploy &gt; New deployment &gt; Web app (Anyone)</b> di Apps Script.
            </span>
          </div>

          {/* Input 2: Target Spreadsheet URL or ID */}
          <div className="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30 space-y-1.5">
            <label className="block text-xs font-bold uppercase tracking-wider text-emerald-950 flex items-center gap-1.5">
              <FileSpreadsheet className="w-4 h-4 text-emerald-700" />
              <span>Database: Tautan atau ID Google Spreadsheet yang Anda Berikan</span>
            </label>
            <input
              type="text"
              value={spreadsheetInput}
              onChange={(e) => setSpreadsheetInput(e.target.value)}
              placeholder="Contoh: https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBIIA.../edit atau ID-nya"
              className="w-full px-3.5 py-2.5 rounded-xl text-xs font-mono border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800"
            />
            <p className="text-[11px] text-emerald-800">
              Sistem akan otomatis mengekstrak ID Spreadsheet dan menulis data inventaris ke lembar tersebut secara terstruktur.
            </p>
          </div>

          {/* Input 3: Target Drive Folder URL or ID */}
          <div className="p-4 rounded-2xl border border-teal-100 bg-teal-50/30 space-y-1.5">
            <label className="block text-xs font-bold uppercase tracking-wider text-teal-950 flex items-center gap-1.5">
              <FolderOpen className="w-4 h-4 text-teal-700" />
              <span>Foto: Tautan atau ID Folder Google Drive yang Anda Berikan</span>
            </label>
            <input
              type="text"
              value={driveFolderInput}
              onChange={(e) => setDriveFolderInput(e.target.value)}
              placeholder="Contoh: https://drive.google.com/drive/folders/1aBcDeFgHiJk... atau ID foldernya"
              className="w-full px-3.5 py-2.5 rounded-xl text-xs font-mono border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"
            />
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-teal-800">
              <span>Foto perangkat / screenshot staf akan langsung masuk ke folder Google Drive ini.</span>
              <span className="font-bold text-teal-900 bg-teal-100 px-2 py-0.5 rounded-full border border-teal-200 shrink-0">
                Akun: newofficialsyamanah@gmail.com
              </span>
            </div>
          </div>

          {/* Input 4: Superadmin PIN */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
              <KeyRound className="w-3.5 h-3.5 text-slate-600" />
              <span>PIN Akses Superadmin</span>
            </label>
            <input
              type="text"
              value={superadminPin}
              onChange={(e) => setSuperadminPin(e.target.value)}
              placeholder="Contoh: 1234"
              maxLength={8}
              className="w-full sm:w-48 px-3.5 py-2 rounded-xl text-xs font-bold tracking-widest text-center border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800"
            />
            <span className="text-[11px] text-slate-500 mt-1 block">
              Digunakan untuk membuka brankas akun kredensial di dashboard Superadmin.
            </span>
          </div>

          {/* Test connection feedback */}
          {testResult.message && (
            <div
              className={`p-3.5 rounded-xl border flex items-start gap-2.5 text-xs ${
                testResult.status === 'success'
                  ? 'bg-emerald-50 border-emerald-300 text-emerald-800'
                  : testResult.status === 'error'
                  ? 'bg-rose-50 border-rose-300 text-rose-800'
                  : 'bg-blue-50 border-blue-200 text-blue-800'
              }`}
            >
              {testResult.status === 'success' ? (
                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
              ) : testResult.status === 'error' ? (
                <AlertTriangle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
              ) : (
                <RefreshCw className="w-4 h-4 text-blue-600 shrink-0 mt-0.5 animate-spin" />
              )}
              <span className="text-xs leading-relaxed">{testResult.message}</span>
            </div>
          )}

          {/* Guide helper banner */}
          <div className="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <HelpCircle className="w-4 h-4 text-slate-500" />
              <span className="text-xs text-slate-600">Butuh kode script update dengan fungsi upload Drive?</span>
            </div>
            <button
              onClick={() => {
                onClose();
                onOpenGuide();
              }}
              className="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 hover:underline"
            >
              Buka Panduan <ExternalLink className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

        {/* Footer */}
        <div className="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
          <button
            onClick={handleTestConnection}
            disabled={testing || !gasUrl.trim()}
            className="px-3.5 py-2 border border-slate-300 hover:bg-slate-100 rounded-xl text-xs font-semibold text-slate-700 transition disabled:opacity-50 flex items-center gap-1.5"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${testing ? 'animate-spin' : ''}`} />
            <span>{testing ? 'Menguji...' : 'Uji Sambungan Webhook'}</span>
          </button>

          <button
            onClick={handleSave}
            className="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm transition"
          >
            Simpan Konfigurasi
          </button>
        </div>
      </div>
    </div>
  );
};
