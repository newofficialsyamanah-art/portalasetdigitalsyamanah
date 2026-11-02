import React, { useState } from 'react';
import {
  X,
  FolderOpen,
  Mail,
  Copy,
  Check,
  ExternalLink,
  ShieldCheck,
  CheckCircle2,
  AlertCircle,
  HelpCircle,
  ArrowRight,
  HardDrive,
  Sparkles,
  Loader2,
  Play
} from 'lucide-react';
import { GAS_SCRIPT_CODE, extractIdFromUrl, saveConfig, getConfig } from '../services/gasService';
import { SyamanahConfig } from '../types';

interface DriveConnectGuideModalProps {
  isOpen: boolean;
  onClose: () => void;
  config: SyamanahConfig;
  onConfigUpdated: (config: SyamanahConfig) => void;
}

export const DriveConnectGuideModal: React.FC<DriveConnectGuideModalProps> = ({
  isOpen,
  onClose,
  config,
  onConfigUpdated
}) => {
  const [copiedCode, setCopiedCode] = useState(false);
  const [folderInput, setFolderInput] = useState(config.driveFolderId || '');
  const [gasUrlInput, setGasUrlInput] = useState(config.gasUrl || '');
  const [isSaving, setIsSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);

  // Drive test upload
  const [isTestingDrive, setIsTestingDrive] = useState(false);
  const [testResult, setTestResult] = useState<{
    status: 'idle' | 'success' | 'error';
    message: string;
    driveUrl?: string;
  }>({ status: 'idle', message: '' });

  if (!isOpen) return null;

  const handleCopyGasCode = () => {
    navigator.clipboard.writeText(GAS_SCRIPT_CODE);
    setCopiedCode(true);
    setTimeout(() => setCopiedCode(false), 3000);
  };

  const handleSaveSettings = async () => {
    setIsSaving(true);
    const cleanFolderId = extractIdFromUrl(folderInput, 'folders');
    const updated: SyamanahConfig = {
      ...config,
      driveFolderId: cleanFolderId,
      gasUrl: gasUrlInput.trim() || config.gasUrl
    };

    await saveConfig(updated);
    onConfigUpdated(updated);
    setIsSaving(false);
    setSaveSuccess(true);
    setTimeout(() => setSaveSuccess(false), 3000);
  };

  const handleTestDriveUpload = async () => {
    setIsTestingDrive(true);
    setTestResult({ status: 'idle', message: '' });

    try {
      // Create a 1x1 pixel base64 test image
      const tinyBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

      const res = await fetch(config.gasUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'text/plain;charset=utf-8' },
        body: JSON.stringify({
          action: 'create',
          formType: 'pc',
          id: `test_drive_${Date.now()}`,
          timestamp: new Date().toLocaleString('id-ID'),
          driveFolderId: folderInput.trim() || config.driveFolderId,
          data: {
            nama: 'TEST_DRIVE_ACCOUNT',
            departemen: 'Sales CRM',
            jabatan: 'Verifikasi Drive',
            computerName: 'PING-DRIVE-TEST',
            fotoPerangkat: tinyBase64,
            os: 'Windows 11',
            ip: '127.0.0.1'
          }
        })
      });

      const json = await res.json();
      if (json.status === 'success' || json.photoUrl) {
        setTestResult({
          status: 'success',
          message: 'Foto tes berhasil terunggah ke Google Drive!',
          driveUrl: json.photoUrl || json.displayUrl
        });

        // Clean up dummy test row from sheet
        fetch(config.gasUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'text/plain;charset=utf-8' },
          body: JSON.stringify({
            action: 'delete',
            formType: 'pc',
            id: `test_drive_${Date.now()}`
          })
        }).catch(() => {});
      } else {
        setTestResult({
          status: 'error',
          message: json.message || 'Gagal mengunggah foto ke Google Drive'
        });
      }
    } catch (err: unknown) {
      const error = err as Error;
      setTestResult({
        status: 'error',
        message: 'Gagal menghubungi Google Apps Script: ' + (error?.message || String(err))
      });
    } finally {
      setIsTestingDrive(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
      <div className="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] my-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-emerald-950 via-teal-950 to-slate-900 text-white px-6 py-4 flex items-center justify-between shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300">
              <FolderOpen className="w-5 h-5" />
            </div>
            <div>
              <h3 className="font-black text-base">
                Panduan Menghubungkan Google Drive Akun Syamanah
              </h3>
              <p className="text-xs text-teal-200/80 mt-0.5 flex items-center gap-1.5">
                <Mail className="w-3.5 h-3.5 text-teal-300" />
                <span>Akun Tujuan: <strong className="text-white font-mono">newofficialsyamanah@gmail.com</strong></span>
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 text-slate-400 hover:text-white hover:bg-white/10 rounded-xl transition"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content Body */}
        <div className="overflow-y-auto p-6 space-y-6 text-xs text-slate-700">
          {/* Rangkuman Inti Cara Kerja */}
          <div className="bg-teal-50/70 border border-teal-200 rounded-2xl p-4 space-y-2">
            <div className="flex items-center gap-2 text-teal-950 font-extrabold text-sm">
              <Sparkles className="w-4 h-4 text-teal-600" />
              <span>Prinsip Utama Penyimpanan Google Drive di Google Apps Script:</span>
            </div>
            <p className="text-slate-700 text-xs leading-relaxed">
              Google Apps Script <strong>secara otomatis menyimpan seluruh file &amp; foto</strong> ke Google Drive milik <strong>akun Google yang mendeploy Web App tersebut</strong>. Oleh karena itu, agar data &amp; foto tersimpan di Google Drive <strong className="text-teal-900">newofficialsyamanah@gmail.com</strong>, ada 2 langkah mudah di bawah:
            </p>
          </div>

          {/* 3 Langkah Praktis */}
          <div className="space-y-4">
            <h4 className="font-extrabold text-slate-900 text-sm">
              3 Langkah Praktis Menghubungkan ke Akun newofficialsyamanah@gmail.com:
            </h4>

            {/* Langkah 1 */}
            <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
              <div className="flex items-center gap-2 font-bold text-slate-900 text-xs">
                <span className="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0 font-black">
                  1
                </span>
                <span>Buat Folder Khusus di Google Drive newofficialsyamanah@gmail.com</span>
              </div>
              <p className="text-slate-600 pl-8 leading-relaxed">
                1. Login ke Google Drive dengan akun <code className="text-emerald-700 font-mono font-bold bg-emerald-50 px-1.5 py-0.5 rounded">newofficialsyamanah@gmail.com</code>.<br />
                2. Buat folder baru dengan nama: <strong>&quot;Foto Aset &amp; Screenshot Syamanah&quot;</strong>.<br />
                3. Buka folder tersebut, lalu salin link URL browser Anda:<br />
                <code className="text-[11px] font-mono text-slate-700 bg-white border border-slate-200 px-2 py-1 rounded-lg block mt-1 break-all">
                  https://drive.google.com/drive/folders/<strong>1ABCxyz987654321_FOLDER_ID</strong>
                </code>
              </p>
            </div>

            {/* Langkah 2 */}
            <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
              <div className="flex items-center gap-2 font-bold text-slate-900 text-xs">
                <span className="w-6 h-6 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs shrink-0 font-black">
                  2
                </span>
                <span>Deploy Google Apps Script dengan Akun newofficialsyamanah@gmail.com</span>
              </div>
              <p className="text-slate-600 pl-8 leading-relaxed">
                1. Buka <strong>script.google.com</strong> saat sedang login di akun <code className="text-teal-700 font-mono font-bold bg-teal-50 px-1.5 py-0.5 rounded">newofficialsyamanah@gmail.com</code>.<br />
                2. Tempelkan seluruh kode script aplikasi (klik tombol salin di bawah).<br />
                3. Klik <strong>Deploy &gt; New Deployment</strong>.<br />
                4. Pada pilihan <i>Execute as</i>: pilih <strong>Me (newofficialsyamanah@gmail.com)</strong>.<br />
                5. Pada pilihan <i>Who has access</i>: pilih <strong>Anyone</strong> (agar form staf bisa mengirim data).<br />
                6. Klik <strong>Deploy</strong> &amp; salin URL Web App yang dihasilkan.
              </p>
              <div className="pl-8 pt-1">
                <button
                  type="button"
                  onClick={handleCopyGasCode}
                  className="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95"
                >
                  {copiedCode ? <Check className="w-3.5 h-3.5 text-white" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiedCode ? 'Kode Berhasil Disalin!' : 'Salin Kode Google Apps Script'}</span>
                </button>
              </div>
            </div>

            {/* Langkah 3 */}
            <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
              <div className="flex items-center gap-2 font-bold text-slate-900 text-xs">
                <span className="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs shrink-0 font-black">
                  3
                </span>
                <span>Masukkan Folder ID &amp; URL Web App ke Pengaturan Aplikasi</span>
              </div>

              <div className="pl-8 space-y-3">
                <div>
                  <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                    Google Drive Folder ID (newofficialsyamanah@gmail.com)
                  </label>
                  <input
                    type="text"
                    value={folderInput}
                    onChange={(e) => setFolderInput(e.target.value)}
                    placeholder="Contoh: 1vX8Z9qLw0abcdef... atau tempel URL lengkap folder Drive"
                    className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white"
                  />
                  <p className="text-[10px] text-slate-500 mt-1">
                    Anda bisa langsung menempel link lengkap folder Google Drive dari akun newofficialsyamanah@gmail.com di atas.
                  </p>
                </div>

                <div>
                  <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                    URL Google Apps Script Web App (Aktif)
                  </label>
                  <input
                    type="text"
                    value={gasUrlInput}
                    onChange={(e) => setGasUrlInput(e.target.value)}
                    placeholder="https://script.google.com/macros/s/.../exec"
                    className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white"
                  />
                </div>

                <div className="flex items-center justify-between pt-1">
                  <span className="text-xs text-emerald-700 font-bold">
                    {saveSuccess && 'Pengaturan Drive berhasil disimpan!'}
                  </span>
                  <button
                    type="button"
                    onClick={handleSaveSettings}
                    disabled={isSaving}
                    className="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition active:scale-95 disabled:opacity-50"
                  >
                    {isSaving ? 'Menyimpan...' : 'Simpan ID Folder Drive'}
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* Test Drive Upload Section */}
          <div className="p-4 rounded-2xl bg-slate-900 text-white border border-slate-800 space-y-3">
            <div className="flex items-center justify-between">
              <div>
                <h5 className="font-extrabold text-xs text-teal-300">
                  Uji Coba Langsung Upload Foto ke Google Drive
                </h5>
                <p className="text-[11px] text-slate-400 mt-0.5">
                  Mengirim foto sampel ke Google Apps Script dan memeriksa tautan Google Drive yang dihasilkan.
                </p>
              </div>

              <button
                type="button"
                onClick={handleTestDriveUpload}
                disabled={isTestingDrive}
                className="px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs flex items-center gap-1.5 transition active:scale-95 disabled:opacity-50 shrink-0"
              >
                {isTestingDrive ? (
                  <>
                    <Loader2 className="w-3.5 h-3.5 animate-spin text-slate-950" />
                    <span>Menguji Drive...</span>
                  </>
                ) : (
                  <>
                    <Play className="w-3.5 h-3.5 fill-slate-950" />
                    <span>Tes Upload ke Drive</span>
                  </>
                )}
              </button>
            </div>

            {testResult.status !== 'idle' && (
              <div
                className={`p-3 rounded-xl text-xs font-mono flex items-start gap-2 ${
                  testResult.status === 'success'
                    ? 'bg-emerald-950/70 border border-emerald-500/40 text-emerald-300'
                    : 'bg-rose-950/70 border border-rose-500/40 text-rose-300'
                }`}
              >
                {testResult.status === 'success' ? (
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                ) : (
                  <AlertCircle className="w-4 h-4 text-rose-400 shrink-0 mt-0.5" />
                )}
                <div>
                  <div className="font-bold">{testResult.message}</div>
                  {testResult.driveUrl && (
                    <a
                      href={testResult.driveUrl}
                      target="_blank"
                      rel="noreferrer"
                      className="inline-flex items-center gap-1 text-teal-300 underline mt-1 hover:text-white"
                    >
                      <ExternalLink className="w-3 h-3" />
                      <span>Lihat File di Google Drive</span>
                    </a>
                  )}
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Footer */}
        <div className="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
          <button
            type="button"
            onClick={onClose}
            className="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition"
          >
            Selesai
          </button>
        </div>
      </div>
    </div>
  );
};
