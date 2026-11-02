import React, { useState } from 'react';
import { Trash2, AlertTriangle, X, ShieldAlert } from 'lucide-react';

interface ClearAllModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirmClear: (scope: 'current' | 'all') => Promise<void> | void;
  currentSheetName: string;
  currentCount: number;
  totalAllCount: number;
}

export const ClearAllModal: React.FC<ClearAllModalProps> = ({
  isOpen,
  onClose,
  onConfirmClear,
  currentSheetName,
  currentCount,
  totalAllCount
}) => {
  const [scope, setScope] = useState<'current' | 'all'>('current');
  const [confirmText, setConfirmText] = useState('');
  const [loading, setLoading] = useState(false);

  if (!isOpen) return null;

  const isMatched = confirmText.trim().toUpperCase() === 'HAPUS';

  const handleExecute = async () => {
    if (!isMatched) return;
    setLoading(true);
    try {
      await onConfirmClear(scope);
      onClose();
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-rose-200 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        {/* Header */}
        <div className="bg-rose-900 text-white px-5 py-4 flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-300 flex items-center justify-center border border-rose-400/30">
              <Trash2 className="w-4 h-4" />
            </div>
            <h3 className="font-bold text-sm text-white">
              Konfirmasi Hapus Semua Data
            </h3>
          </div>
          <button
            onClick={onClose}
            className="p-1 rounded-md text-rose-300 hover:text-white hover:bg-white/10 transition"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Content */}
        <div className="p-5 space-y-4 text-xs">
          <div className="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 space-y-1">
            <div className="font-bold flex items-center gap-1.5 text-rose-700">
              <ShieldAlert className="w-4 h-4" />
              <span>Tindakan Berbahaya</span>
            </div>
            <p className="text-[11px] leading-relaxed text-rose-800">
              Data yang dihapus akan dibersihkan dari server lokal dan Google Cloud Firestore.
            </p>
          </div>

          {/* Scope Selection */}
          <div className="space-y-2">
            <label className="font-bold text-slate-800 block">Pilih Cakupan Penghapusan:</label>

            <label className="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
              <input
                type="radio"
                name="scope"
                value="current"
                checked={scope === 'current'}
                onChange={() => setScope('current')}
                className="mt-0.5 text-rose-600 focus:ring-rose-500"
              />
              <div>
                <span className="font-bold text-slate-900 block">
                  Hanya Lembar Aktif: {currentSheetName}
                </span>
                <span className="text-[11px] text-slate-500">
                  Akan menghapus {currentCount} baris data pada kategori ini saja.
                </span>
              </div>
            </label>

            <label className="flex items-start gap-3 p-3 rounded-xl border border-rose-200 bg-rose-50/30 hover:bg-rose-50 cursor-pointer transition">
              <input
                type="radio"
                name="scope"
                value="all"
                checked={scope === 'all'}
                onChange={() => setScope('all')}
                className="mt-0.5 text-rose-600 focus:ring-rose-500"
              />
              <div>
                <span className="font-bold text-rose-900 block">
                  Hapus Seluruh Database (Semua Kategori)
                </span>
                <span className="text-[11px] text-rose-700">
                  Akan menghapus seluruh {totalAllCount} baris data (PC, Akun Sales &amp; MikroTik).
                </span>
              </div>
            </label>
          </div>

          {/* Safety Confirm Input */}
          <div>
            <label className="block text-[11px] font-semibold text-slate-700 mb-1">
              Ketik kata <span className="font-mono font-bold text-rose-600">HAPUS</span> untuk konfirmasi:
            </label>
            <input
              type="text"
              value={confirmText}
              onChange={(e) => setConfirmText(e.target.value)}
              placeholder="Ketik HAPUS..."
              className="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono uppercase text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"
            />
          </div>

          {/* Actions */}
          <div className="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
            <button
              type="button"
              onClick={onClose}
              disabled={loading}
              className="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
            >
              Batal
            </button>
            <button
              type="button"
              onClick={handleExecute}
              disabled={!isMatched || loading}
              className="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition disabled:opacity-40 disabled:cursor-not-allowed"
            >
              {loading ? 'Menghapus...' : 'Ya, Hapus Sekarang'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
