import React from 'react';
import { X, Terminal, Monitor, Cpu, HardDrive, CheckCircle2, ExternalLink } from 'lucide-react';

interface DxDiagGuideModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const DxDiagGuideModal: React.FC<DxDiagGuideModalProps> = ({ isOpen, onClose }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        {/* Header */}
        <div className="bg-gradient-to-r from-emerald-800 to-teal-800 text-white px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-lg bg-emerald-500/30 flex items-center justify-center border border-emerald-400/30">
              <Terminal className="w-5 h-5 text-emerald-200" />
            </div>
            <div>
              <h3 className="text-base font-bold">Panduan Cek Spesifikasi PC (DxDiag)</h3>
              <p className="text-xs text-emerald-200">Cara cepat & akurat melihat spesifikasi lengkap Windows Anda</p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 rounded-lg text-emerald-100 hover:text-white hover:bg-white/10 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 space-y-6 max-h-[75vh] overflow-y-auto text-sm text-slate-700">
          {/* Step 1 */}
          <div className="flex gap-4">
            <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-sm">
              1
            </div>
            <div className="space-y-1">
              <h4 className="font-semibold text-slate-900">Buka Menu Run Windows</h4>
              <p className="text-slate-600 text-xs">
                Tekan tombol <kbd className="px-2 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-xs">Windows</kbd> + <kbd className="px-2 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-xs">R</kbd> secara bersamaan di keyboard komputer Anda.
              </p>
            </div>
          </div>

          {/* Step 2 */}
          <div className="flex gap-4">
            <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-sm">
              2
            </div>
            <div className="space-y-1.5">
              <h4 className="font-semibold text-slate-900">Ketik Perintah DxDiag</h4>
              <p className="text-slate-600 text-xs">
                Ketikkan kata <code className="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-mono font-bold text-xs">dxdiag</code> pada kotak input yang muncul, lalu tekan <b>Enter</b> atau klik <b>OK</b>.
              </p>
              <div className="p-3 bg-slate-900 text-emerald-400 font-mono text-xs rounded-lg border border-slate-700 flex items-center justify-between">
                <span>Run: dxdiag</span>
                <span className="text-slate-400 text-[11px]">DirectX Diagnostic Tool</span>
              </div>
            </div>
          </div>

          {/* Step 3 */}
          <div className="flex gap-4">
            <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-sm">
              3
            </div>
            <div className="space-y-2">
              <h4 className="font-semibold text-slate-900">Lihat Tab &quot;System&quot; (Untuk Form Hardware)</h4>
              <p className="text-slate-600 text-xs">
                Pada tab pertama yang terbuka (<b>System</b>), Anda akan menemukan seluruh data yang dibutuhkan:
              </p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div className="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center gap-2">
                  <Monitor className="w-4 h-4 text-emerald-600 shrink-0" />
                  <div>
                    <span className="font-medium text-slate-800 block">Computer Name</span>
                    <span className="text-[11px] text-slate-500">Contoh: DESKTOP-D0N0ANQ</span>
                  </div>
                </div>
                <div className="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <div>
                    <span className="font-medium text-slate-800 block">Operating System</span>
                    <span className="text-[11px] text-slate-500">Contoh: Windows 10 Pro 64-bit</span>
                  </div>
                </div>
                <div className="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center gap-2">
                  <Cpu className="w-4 h-4 text-emerald-600 shrink-0" />
                  <div>
                    <span className="font-medium text-slate-800 block">Processor</span>
                    <span className="text-[11px] text-slate-500">Contoh: Intel Core i5-3470</span>
                  </div>
                </div>
                <div className="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center gap-2">
                  <HardDrive className="w-4 h-4 text-emerald-600 shrink-0" />
                  <div>
                    <span className="font-medium text-slate-800 block">Memory (RAM)</span>
                    <span className="text-[11px] text-slate-500">Contoh: 8192MB RAM (8GB)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Step 4 */}
          <div className="flex gap-4">
            <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-sm">
              4
            </div>
            <div className="space-y-1.5">
              <h4 className="font-semibold text-slate-900">Cek IP Address PC</h4>
              <p className="text-slate-600 text-xs">
                Tekan <kbd className="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-[11px]">Win + R</kbd>, ketik <code className="text-emerald-700 font-bold">cmd</code>, tekan Enter. Lalu ketik <code className="text-emerald-700 font-bold">ipconfig</code> dan tekan Enter. Cari baris bertuliskan <b>IPv4 Address</b> (contoh: <code>192.168.99.79</code>).
              </p>
            </div>
          </div>

          {/* Step 5 */}
          <div className="flex gap-4">
            <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-sm">
              5
            </div>
            <div className="space-y-1.5">
              <h4 className="font-semibold text-slate-900">Upload Screenshot ke Google Drive (Opsional)</h4>
              <p className="text-slate-600 text-xs">
                Ambil tangkapan layar (screenshot) pada tab <b>Display</b>, <b>Sound</b>, dan <b>Input</b> pada DxDiag. Unggah ke folder Google Drive tim Anda dan pastikan setelan tautan adalah <i>&quot;Siapa saja yang memiliki link dapat melihat&quot;</i>, lalu tempel tautannya pada form.
              </p>
            </div>
          </div>
        </div>

        {/* Footer */}
        <div className="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
          <button
            onClick={onClose}
            className="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-xs transition"
          >
            Mengerti & Tutup
          </button>
        </div>
      </div>
    </div>
  );
};
