import React from 'react';
import { Trash2, AlertTriangle, X } from 'lucide-react';

interface ConfirmDeleteModalProps {
  isOpen: boolean;
  title?: string;
  itemName: string;
  itemType?: string;
  onConfirm: () => void;
  onCancel: () => void;
}

export const ConfirmDeleteModal: React.FC<ConfirmDeleteModalProps> = ({
  isOpen,
  title = 'Konfirmasi Hapus Data',
  itemName,
  itemType = 'data',
  onConfirm,
  onCancel
}) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div className="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div className="p-6 text-center space-y-4">
          <div className="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 mx-auto flex items-center justify-center">
            <Trash2 className="w-7 h-7" />
          </div>

          <div>
            <h3 className="font-extrabold text-slate-900 text-lg">{title}</h3>
            <p className="text-xs text-slate-600 mt-2 leading-relaxed">
              Apakah Anda yakin ingin menghapus {itemType}{' '}
              <span className="font-bold text-slate-900">&quot;{itemName}&quot;</span>?
            </p>
            <p className="text-[11px] text-rose-600 font-medium mt-1">
              Data yang dihapus akan dikeluarkan dari daftar inventaris.
            </p>
          </div>

          <div className="pt-2 flex items-center justify-center gap-3">
            <button
              type="button"
              onClick={onCancel}
              className="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex-1 cursor-pointer"
            >
              Batal
            </button>
            <button
              type="button"
              onClick={() => {
                onConfirm();
                onCancel();
              }}
              className="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md transition flex-1 cursor-pointer"
            >
              Ya, Hapus Data
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
