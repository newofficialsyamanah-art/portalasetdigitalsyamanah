import React, { useState } from 'react';
import {
  Code2,
  Copy,
  Check,
  FileSpreadsheet,
  Settings,
  HelpCircle,
  ExternalLink,
  ShieldCheck,
  Table,
  CheckCircle2,
  FolderOpen,
  Camera
} from 'lucide-react';
import { GAS_SCRIPT_CODE } from '../services/gasService';

interface GasGuideTabProps {
  onOpenSettingsModal: () => void;
}

export const GasGuideTab: React.FC<GasGuideTabProps> = ({ onOpenSettingsModal }) => {
  const [copied, setCopied] = useState(false);

  const handleCopyCode = () => {
    navigator.clipboard.writeText(GAS_SCRIPT_CODE);
    setCopied(true);
    setTimeout(() => setCopied(false), 3000);
  };

  return (
    <div className="space-y-8 max-w-5xl mx-auto">
      {/* Hero Banner */}
      <div className="bg-gradient-to-br from-emerald-950 via-teal-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-700/30">
        <div className="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" />
        <div className="relative z-10 max-w-3xl">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold mb-3 border border-emerald-400/20">
            <FileSpreadsheet className="w-3.5 h-3.5" />
            Integrasi Langsung Google Spreadsheet &amp; Google Drive
          </div>
          <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Panduan Google Apps Script (Database &amp; Upload Foto Drive)
          </h2>
          <p className="text-emerald-100/90 text-xs sm:text-sm mt-2 leading-relaxed">
            Data form inventaris langsung terkirim ke <b>Google Spreadsheet yang Anda berikan</b>, dan foto perangkat/laptop otomatis diunggah ke <b>Folder Google Drive yang Anda tentukan</b> tanpa perlu langganan cloud hosting berbayar!
          </p>

          <div className="mt-5 flex flex-wrap gap-3">
            <button
              onClick={handleCopyCode}
              className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-lg transition active:scale-95"
            >
              {copied ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
              <span>{copied ? 'Kode Berhasil Disalin!' : 'Salin Kode Google Apps Script'}</span>
            </button>
            <button
              onClick={onOpenSettingsModal}
              className="px-4 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl text-xs font-semibold flex items-center gap-2 transition"
            >
              <Settings className="w-4 h-4" />
              <span>Masukkan Spreadsheet &amp; Drive ID</span>
            </button>
          </div>
        </div>
      </div>

      {/* 2 Target Highlights */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs flex items-start gap-3.5">
          <div className="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
            <FileSpreadsheet className="w-5 h-5 text-emerald-700" />
          </div>
          <div>
            <h4 className="font-bold text-slate-900 text-sm">1. Database ke Spreadsheet Anda</h4>
            <p className="text-xs text-slate-600 mt-1 leading-relaxed">
              Anda cukup menempelkan link Google Spreadsheet yang Anda miliki di menu <b>Koneksi Sheet &amp; Drive</b>. Script otomatis membuat lembar <i>&quot;Spesifikasi PC &amp; Laptop&quot;</i> dan <i>&quot;Akun Sales &amp; HP&quot;</i> lengkap dengan header berwarna.
            </p>
          </div>
        </div>

        <div className="bg-white p-5 rounded-2xl border border-teal-200 shadow-xs flex items-start gap-3.5">
          <div className="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
            <FolderOpen className="w-5 h-5 text-teal-700" />
          </div>
          <div>
            <h4 className="font-bold text-slate-900 text-sm">2. Foto Masuk ke Google Drive Anda</h4>
            <p className="text-xs text-slate-600 mt-1 leading-relaxed">
              Buat folder di Google Drive (misal: <i>&quot;Foto Aset Laptop Syamanah&quot;</i>), salin link foldernya, dan masukkan di menu pengaturan. Setiap foto yang diunggah staf akan disimpan langsung di folder tersebut dan link view-nya dicatat di Spreadsheet.
            </p>
          </div>
        </div>
      </div>

      {/* Step by Step Cards */}
      <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
        {/* Step 1 */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
          <div>
            <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-3">
              1
            </div>
            <h4 className="font-bold text-slate-800 text-sm mb-1">Buka Spreadsheet</h4>
            <p className="text-xs text-slate-600 leading-relaxed">
              Buka Google Spreadsheet tempat Anda ingin menyimpan data aset kantor.
            </p>
          </div>
          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-medium">
            sheets.google.com
          </div>
        </div>

        {/* Step 2 */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
          <div>
            <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-3">
              2
            </div>
            <h4 className="font-bold text-slate-800 text-sm mb-1">Buka Apps Script</h4>
            <p className="text-xs text-slate-600 leading-relaxed">
              Di menu atas Google Spreadsheet, klik <b>Ekstensi (Extensions)</b> lalu pilih <b>Apps Script</b>.
            </p>
          </div>
          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-medium">
            Menu: Ekstensi &gt; Apps Script
          </div>
        </div>

        {/* Step 3 */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
          <div>
            <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-3">
              3
            </div>
            <h4 className="font-bold text-slate-800 text-sm mb-1">Tempel Kode</h4>
            <p className="text-xs text-slate-600 leading-relaxed">
              Hapus semua teks default, lalu tempel kode lengkap yang telah disediakan di bawah (sudah mendukung Google Drive).
            </p>
          </div>
          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-medium">
            Ctrl+A lalu Ctrl+V
          </div>
        </div>

        {/* Step 4 */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
          <div>
            <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-3">
              4
            </div>
            <h4 className="font-bold text-slate-800 text-sm mb-1">Deploy Web App</h4>
            <p className="text-xs text-slate-600 leading-relaxed">
              Klik tombol biru <b>Deploy</b> &gt; <b>New deployment</b>. Pilih tipe: <b>Web app</b>.
            </p>
          </div>
          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-medium">
            Type: Web app
          </div>
        </div>

        {/* Step 5 */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
          <div>
            <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mb-3">
              5
            </div>
            <h4 className="font-bold text-slate-800 text-sm mb-1">Setel &quot;Anyone&quot;</h4>
            <p className="text-xs text-slate-600 leading-relaxed">
              Pada <b>Who has access</b>, pilih <b>Anyone (Siapa saja)</b>. Salin Web App URL dan tempel ke form pengaturan.
            </p>
          </div>
          <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-emerald-700 font-medium">
            Akses: Siapa saja
          </div>
        </div>
      </div>

      {/* Code Block */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div className="bg-slate-900 text-slate-200 px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
          <div className="flex items-center gap-2.5">
            <Code2 className="w-5 h-5 text-emerald-400" />
            <div>
              <span className="font-bold text-sm text-white">Kode Google Apps Script (Mendukung Spreadsheet &amp; Drive)</span>
              <span className="text-xs text-slate-400 block">Menangani doPost, pembuatan file di folder Drive yang diberikan, dan logging Spreadsheet</span>
            </div>
          </div>
          <button
            onClick={handleCopyCode}
            className="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold flex items-center gap-1.5 transition active:scale-95"
          >
            {copied ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
            <span>{copied ? 'Tersalin!' : 'Salin Kode'}</span>
          </button>
        </div>

        <div className="p-4 bg-slate-950 font-mono text-xs text-emerald-300 overflow-x-auto max-h-96 leading-relaxed">
          <pre>{GAS_SCRIPT_CODE}</pre>
        </div>
      </div>

      {/* Security & Access Notice */}
      <div className="p-5 rounded-2xl bg-amber-50/80 border border-amber-200 flex items-start gap-4">
        <ShieldCheck className="w-6 h-6 text-amber-700 shrink-0 mt-0.5" />
        <div className="space-y-1.5 text-xs text-amber-900">
          <h4 className="font-bold text-sm">Izin Akses Google Drive:</h4>
          <p className="leading-relaxed">
            Saat pertama kali mendeploy Apps Script yang memiliki akses ke Google Drive, Google akan meminta persetujuan izin (Authorization Required). Klik <b>Review Permissions</b> &gt; pilih akun Google Anda &gt; klik <b>Advanced</b> &gt; klik <b>Go to project (unsafe)</b> &gt; klik <b>Allow</b>.
          </p>
        </div>
      </div>
    </div>
  );
};
