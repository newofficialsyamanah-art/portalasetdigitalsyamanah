import React, { useState, useEffect } from 'react';
import {
  X,
  Save,
  Laptop,
  Smartphone,
  CheckCircle2,
  AlertCircle,
  Loader2,
  HardDrive,
  Cpu,
  Layers,
  Shield,
  User,
  Building,
  KeyRound,
  Mail,
  Share2
} from 'lucide-react';
import { SubmissionRecord, FormPC, FormAkun } from '../types';
import { updateSubmission } from '../services/gasService';

interface EditRecordModalProps {
  isOpen: boolean;
  record: SubmissionRecord | null;
  onClose: () => void;
  onSaveSuccess: (updated: SubmissionRecord) => void;
}

export const EditRecordModal: React.FC<EditRecordModalProps> = ({
  isOpen,
  record,
  onClose,
  onSaveSuccess
}) => {
  const [formData, setFormData] = useState<Record<string, unknown>>({});
  const [isSaving, setIsSaving] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  useEffect(() => {
    if (record) {
      setFormData({ ...record.data });
      setErrorMsg(null);
    }
  }, [record]);

  if (!isOpen || !record) return null;

  const isPC = record.type === 'pc';

  const handleChange = (field: string, value: string) => {
    setFormData((prev) => ({
      ...prev,
      [field]: value
    }));
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSaving(true);
    setErrorMsg(null);

    try {
      const updatedRecord: SubmissionRecord = {
        ...record,
        data: isPC ? (formData as unknown as FormPC) : (formData as unknown as FormAkun)
      };

      const res = await updateSubmission(updatedRecord);
      if (res.success) {
        onSaveSuccess(updatedRecord);
        onClose();
      } else {
        setErrorMsg(res.message || 'Gagal menyimpan perubahan');
      }
    } catch (err: unknown) {
      const error = err as Error;
      setErrorMsg(error?.message || 'Terjadi kesalahan saat menyimpan');
    } finally {
      setIsSaving(false);
    }
  };

  const pcData = formData as unknown as Partial<FormPC>;
  const akunData = formData as unknown as Partial<FormAkun>;

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
      <div className="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] my-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white px-6 py-4 flex items-center justify-between shrink-0">
          <div className="flex items-center gap-3">
            <div
              className={`w-10 h-10 rounded-2xl flex items-center justify-center ${
                isPC ? 'bg-emerald-600' : 'bg-teal-600'
              }`}
            >
              {isPC ? <Laptop className="w-5 h-5" /> : <Smartphone className="w-5 h-5" />}
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="font-black text-base">
                  Edit Data {isPC ? 'Perangkat PC & Laptop' : 'Akun Sales & Handphone'}
                </h3>
                <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase">
                  CRUD: Update
                </span>
              </div>
              <p className="text-xs text-slate-300 mt-0.5">
                ID: <span className="font-mono text-emerald-400">{record.id}</span> &bull; Terhubung ke Google Spreadsheet
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

        {/* Scrollable Form Body */}
        <form onSubmit={handleSave} className="overflow-y-auto p-6 space-y-5 flex-1">
          {errorMsg && (
            <div className="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
              <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
              <span>{errorMsg}</span>
            </div>
          )}

          {/* PC & Laptop Fields */}
          {isPC ? (
            <div className="space-y-4 text-xs">
              {/* Section 1: Pemilik & Jabatan */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <User className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Identitas Pemilik &amp; Perangkat</span>
                </span>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Nama Pemilik
                    </label>
                    <input
                      type="text"
                      value={pcData.nama || ''}
                      onChange={(e) => handleChange('nama', e.target.value)}
                      required
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Departemen
                    </label>
                    <input
                      type="text"
                      value={pcData.departemen || ''}
                      onChange={(e) => handleChange('departemen', e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Jabatan
                    </label>
                    <input
                      type="text"
                      value={pcData.jabatan || ''}
                      onChange={(e) => handleChange('jabatan', e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Jenis Perangkat
                    </label>
                    <select
                      value={pcData.jenisPerangkat || 'Laptop'}
                      onChange={(e) => handleChange('jenisPerangkat', e.target.value)}
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white"
                    >
                      <option value="Laptop">Laptop</option>
                      <option value="PC Desktop">PC Desktop</option>
                      <option value="All-in-One PC">All-in-One PC</option>
                    </select>
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Merk / Manufacturer
                    </label>
                    <input
                      type="text"
                      value={pcData.manufacturer || ''}
                      onChange={(e) => handleChange('manufacturer', e.target.value)}
                      placeholder="Contoh: ASUS, Lenovo, HP..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>

              {/* Section 2: Spesifikasi Hardware DxDiag */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <Cpu className="w-3.5 h-3.5 text-blue-600" />
                  <span>Spesifikasi Hardware &amp; Jaringan</span>
                </span>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Computer Name
                    </label>
                    <input
                      type="text"
                      value={pcData.computerName || ''}
                      onChange={(e) => handleChange('computerName', e.target.value)}
                      required
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      IP Address
                    </label>
                    <input
                      type="text"
                      value={pcData.ip || ''}
                      onChange={(e) => handleChange('ip', e.target.value)}
                      placeholder="192.168..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Operating System
                    </label>
                    <input
                      type="text"
                      value={pcData.os || ''}
                      onChange={(e) => handleChange('os', e.target.value)}
                      placeholder="Windows 10 / 11 Pro 64-bit"
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Password Login Windows
                    </label>
                    <input
                      type="text"
                      value={pcData.passwordPc || ''}
                      onChange={(e) => handleChange('passwordPc', e.target.value)}
                      placeholder="Kosongkan jika tanpa password"
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Processor
                    </label>
                    <input
                      type="text"
                      value={pcData.processor || ''}
                      onChange={(e) => handleChange('processor', e.target.value)}
                      placeholder="Intel Core i5 / AMD Ryzen"
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      RAM / Memory
                    </label>
                    <input
                      type="text"
                      value={pcData.memory || ''}
                      onChange={(e) => handleChange('memory', e.target.value)}
                      placeholder="8192MB RAM (8GB)"
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>

              {/* Section 3: Link Screenshot DxDiag di Drive */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <HardDrive className="w-3.5 h-3.5 text-teal-600" />
                  <span>Tautan Google Drive Screenshot DxDiag</span>
                </span>

                <div className="space-y-2">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Link Screenshot Display
                    </label>
                    <input
                      type="text"
                      value={pcData.displayLink || ''}
                      onChange={(e) => handleChange('displayLink', e.target.value)}
                      placeholder="https://drive.google.com/..."
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Link Screenshot Sound
                    </label>
                    <input
                      type="text"
                      value={pcData.soundLink || ''}
                      onChange={(e) => handleChange('soundLink', e.target.value)}
                      placeholder="https://drive.google.com/..."
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Link Screenshot Input
                    </label>
                    <input
                      type="text"
                      value={pcData.inputLink || ''}
                      onChange={(e) => handleChange('inputLink', e.target.value)}
                      placeholder="https://drive.google.com/..."
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>
            </div>
          ) : (
            /* Akun Sales & HP Fields */
            <div className="space-y-4 text-xs">
              {/* Identitas Sales & HP */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <User className="w-3.5 h-3.5 text-teal-600" />
                  <span>Identitas Staf Sales &amp; Handphone</span>
                </span>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Nama Lengkap
                    </label>
                    <input
                      type="text"
                      value={akunData.nama || ''}
                      onChange={(e) => handleChange('nama', e.target.value)}
                      required
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Kategori Tim / Role
                    </label>
                    <input
                      type="text"
                      value={akunData.kategori || ''}
                      onChange={(e) => handleChange('kategori', e.target.value)}
                      placeholder="Sales Jaket 1 / CRM / Eksekutif..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Jabatan
                    </label>
                    <input
                      type="text"
                      value={akunData.jabatan || ''}
                      onChange={(e) => handleChange('jabatan', e.target.value)}
                      placeholder="Staf Sales / Head..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Jenis Handphone (Merk, RAM, ROM)
                    </label>
                    <input
                      type="text"
                      value={akunData.jenisHp || ''}
                      onChange={(e) => handleChange('jenisHp', e.target.value)}
                      placeholder="Samsung A15 8/256GB..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Bekas Handphone Siapa?
                    </label>
                    <input
                      type="text"
                      value={akunData.bekasHp || ''}
                      onChange={(e) => handleChange('bekasHp', e.target.value)}
                      placeholder="Baru / Bekas Staf X..."
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                      Nomor WhatsApp Handphone
                    </label>
                    <input
                      type="text"
                      value={akunData.noWa || ''}
                      onChange={(e) => handleChange('noWa', e.target.value)}
                      placeholder="08123456789"
                      className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>

              {/* Akun Email & Password */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <Mail className="w-3.5 h-3.5 text-blue-600" />
                  <span>Akun Email &amp; Password</span>
                </span>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Email 1</label>
                    <input
                      type="text"
                      value={akunData.email1 || ''}
                      onChange={(e) => handleChange('email1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Password Email 1</label>
                    <input
                      type="text"
                      value={akunData.passEmail1 || ''}
                      onChange={(e) => handleChange('passEmail1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Email 2</label>
                    <input
                      type="text"
                      value={akunData.email2 || ''}
                      onChange={(e) => handleChange('email2', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Password Email 2</label>
                    <input
                      type="text"
                      value={akunData.passEmail2 || ''}
                      onChange={(e) => handleChange('passEmail2', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>

              {/* Akun Instagram & Facebook */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <span className="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                  <Share2 className="w-3.5 h-3.5 text-pink-600" />
                  <span>Akun Sosial Media (Instagram &amp; Facebook)</span>
                </span>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Username IG 1</label>
                    <input
                      type="text"
                      value={akunData.ig1 || ''}
                      onChange={(e) => handleChange('ig1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Password IG 1</label>
                    <input
                      type="text"
                      value={akunData.passIg1 || ''}
                      onChange={(e) => handleChange('passIg1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Akun Facebook 1</label>
                    <input
                      type="text"
                      value={akunData.fb1 || ''}
                      onChange={(e) => handleChange('fb1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-slate-600 mb-1">Password FB 1</label>
                    <input
                      type="text"
                      value={akunData.passFb1 || ''}
                      onChange={(e) => handleChange('passFb1', e.target.value)}
                      className="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                    />
                  </div>
                </div>
              </div>

              {/* Catatan & Akun Lain */}
              <div className="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                <div>
                  <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                    Akun Login di PC
                  </label>
                  <input
                    type="text"
                    value={akunData.akunLoginPc || ''}
                    onChange={(e) => handleChange('akunLoginPc', e.target.value)}
                    placeholder="sales.syamanah@gmail.com..."
                    className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                    Catatan Khusus (Kondisi HP / Akun)
                  </label>
                  <textarea
                    rows={2}
                    value={akunData.catatan || ''}
                    onChange={(e) => handleChange('catatan', e.target.value)}
                    placeholder="Kondisi HP, kelengkapan, dll..."
                    className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"
                  />
                </div>
              </div>
            </div>
          )}

          {/* Footer Actions */}
          <div className="pt-4 border-t border-slate-200 flex items-center justify-between">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
            >
              Batal
            </button>

            <button
              type="submit"
              disabled={isSaving}
              className="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black flex items-center gap-2 shadow-md transition active:scale-95 disabled:opacity-50"
            >
              {isSaving ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin text-white" />
                  <span>Menyimpan ke Spreadsheet...</span>
                </>
              ) : (
                <>
                  <Save className="w-4 h-4" />
                  <span>Simpan Perubahan (Update)</span>
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};
