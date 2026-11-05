import React, { useState, useEffect } from 'react';
import {
  X,
  Save,
  Wifi,
  CheckCircle2,
  AlertCircle,
  Loader2,
  Terminal,
  User,
  Shield,
  KeyRound,
  Copy,
  Check
} from 'lucide-react';
import { MikrotikAccount } from '../types';
import { updateMikrotikAccount, generateMikrotikCommand, MIKROTIK_TEAMS } from '../services/mikrotikService';
import { getDivisions, fetchDivisions } from '../services/divisionService';

interface EditMikrotikModalProps {
  isOpen: boolean;
  account: MikrotikAccount | null;
  onClose: () => void;
  onSaveSuccess: (updated: MikrotikAccount) => void;
}

export const EditMikrotikModal: React.FC<EditMikrotikModalProps> = ({
  isOpen,
  account,
  onClose,
  onSaveSuccess
}) => {
  const [formData, setFormData] = useState<Partial<MikrotikAccount>>({});
  const [isSaving, setIsSaving] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [copiedCmd, setCopiedCmd] = useState(false);
  const [divisionList, setDivisionList] = useState<string[]>(getDivisions());

  useEffect(() => {
    fetchDivisions().then((list) => {
      if (list && list.length > 0) setDivisionList(list);
    });
  }, []);

  useEffect(() => {
    if (account) {
      setFormData({ ...account });
      setErrorMsg(null);
    }
  }, [account]);

  if (!isOpen || !account) return null;

  const handleChange = (field: keyof MikrotikAccount, value: string) => {
    setFormData((prev) => {
      const updated = { ...prev, [field]: value };
      if (field === 'tim') {
        const teamInfo = MIKROTIK_TEAMS.find((t) => t.id === value);
        if (teamInfo) {
          updated.profileHotspot = teamInfo.profile;
          updated.rateLimit = teamInfo.rateLimit;
        }
      }
      return updated;
    });
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSaving(true);
    setErrorMsg(null);

    try {
      const teamInfo = MIKROTIK_TEAMS.find((t) => t.id === formData.tim);
      const generatedCmd = generateMikrotikCommand(
        formData.username || account.username,
        formData.password || account.password,
        formData.profileHotspot || teamInfo?.profile || 'sales',
        formData.rateLimit || teamInfo?.rateLimit || '10M/10M',
        formData.namaPemilik || account.namaPemilik,
        formData.jabatan || account.jabatan,
        teamInfo?.label || formData.tim || 'Sales'
      );

      const updatedAccount: MikrotikAccount = {
        ...account,
        namaPemilik: formData.namaPemilik || account.namaPemilik,
        tim: (formData.tim as MikrotikAccount['tim']) || account.tim,
        jabatan: formData.jabatan || account.jabatan,
        username: formData.username || account.username,
        password: formData.password || account.password,
        profileHotspot: formData.profileHotspot || account.profileHotspot,
        rateLimit: formData.rateLimit || account.rateLimit,
        commandTerminal: generatedCmd,
        catatan: formData.catatan || account.catatan,
        status: (formData.status as MikrotikAccount['status']) || account.status
      };

      const res = await updateMikrotikAccount(updatedAccount);
      if (res.success) {
        onSaveSuccess(updatedAccount);
        onClose();
      } else {
        setErrorMsg(res.message || 'Gagal menyimpan perubahan akun MikroTik');
      }
    } catch (err: unknown) {
      const error = err as Error;
      setErrorMsg(error?.message || 'Terjadi kesalahan saat menyimpan');
    } finally {
      setIsSaving(false);
    }
  };

  const currentCmd = generateMikrotikCommand(
    formData.username || account.username,
    formData.password || account.password,
    formData.profileHotspot || 'sales',
    formData.rateLimit || '10M/10M',
    formData.namaPemilik || account.namaPemilik,
    formData.jabatan || account.jabatan,
    MIKROTIK_TEAMS.find((t) => t.id === formData.tim)?.label || formData.tim || 'Sales'
  );

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
      <div className="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] my-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-blue-950 via-slate-900 to-blue-950 text-white px-6 py-4 flex items-center justify-between shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white">
              <Wifi className="w-5 h-5" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="font-black text-base">Edit Akun MikroTik Hotspot</h3>
                <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-400/20 text-blue-300 border border-blue-400/30 uppercase">
                  CRUD: Update
                </span>
              </div>
              <p className="text-xs text-slate-300 mt-0.5">
                Username: <span className="font-mono text-blue-400 font-bold">{account.username}</span> &bull; Tab &quot;Akun MikroTik Hotspot&quot;
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

        {/* Form Body */}
        <form onSubmit={handleSave} className="overflow-y-auto p-6 space-y-4 flex-1 text-xs">
          {errorMsg && (
            <div className="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
              <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
              <span>{errorMsg}</span>
            </div>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                Nama Pemilik Akun
              </label>
              <input
                type="text"
                value={formData.namaPemilik || ''}
                onChange={(e) => handleChange('namaPemilik', e.target.value)}
                required
                className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                Tim Sales / Divisi
              </label>
              <select
                value={formData.tim || 'jaket1'}
                onChange={(e) => handleChange('tim', e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none bg-white font-semibold"
              >
                <optgroup label="Preset Tim Hotspot">
                  {MIKROTIK_TEAMS.map((t) => (
                    <option key={t.id} value={t.id}>
                      {t.label} (Profile: {t.profile})
                    </option>
                  ))}
                </optgroup>
                <optgroup label="Divisi & Tim Perusahaan">
                  {divisionList
                    .filter((d) => !MIKROTIK_TEAMS.some((t) => t.id === d))
                    .map((d) => (
                      <option key={d} value={d}>
                        {d}
                      </option>
                    ))}
                </optgroup>
                {formData.tim &&
                  !MIKROTIK_TEAMS.some((t) => t.id === formData.tim) &&
                  !divisionList.includes(formData.tim) && (
                    <option value={formData.tim}>{formData.tim}</option>
                  )}
              </select>
            </div>

            <div>
              <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                Jabatan
              </label>
              <input
                type="text"
                value={formData.jabatan || ''}
                onChange={(e) => handleChange('jabatan', e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                Status Akun
              </label>
              <select
                value={formData.status || 'aktif'}
                onChange={(e) => handleChange('status', e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white font-semibold"
              >
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
              </select>
            </div>
          </div>

          {/* Kredensial MikroTik */}
          <div className="bg-blue-50/50 p-4 rounded-2xl border border-blue-200/60 space-y-3">
            <span className="font-bold text-blue-900 flex items-center gap-1.5 text-xs">
              <KeyRound className="w-3.5 h-3.5 text-blue-600" />
              <span>Kredensial Login Hotspot</span>
            </span>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                  Username MikroTik (Format Singkat: SJ01, CRM01, dll)
                </label>
                <input
                  type="text"
                  value={formData.username || ''}
                  onChange={(e) => handleChange('username', e.target.value)}
                  required
                  className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold text-blue-900 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-[11px] font-semibold text-slate-700 mb-1">
                  Password (Maksimal 6 Digit)
                </label>
                <input
                  type="text"
                  maxLength={6}
                  value={formData.password || ''}
                  onChange={(e) => handleChange('password', e.target.value)}
                  required
                  className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
              </div>
            </div>
          </div>

          {/* RouterOS Terminal Script Preview */}
          <div className="bg-slate-900 text-emerald-300 p-3.5 rounded-xl font-mono text-[11px] space-y-1.5">
            <div className="flex items-center justify-between text-slate-400 text-[10px] font-bold uppercase">
              <span>Perintah Winbox Terminal Terkini:</span>
              <button
                type="button"
                onClick={() => {
                  navigator.clipboard.writeText(currentCmd);
                  setCopiedCmd(true);
                  setTimeout(() => setCopiedCmd(false), 2000);
                }}
                className="text-white hover:text-emerald-400 flex items-center gap-1 text-[10px]"
              >
                {copiedCmd ? <Check className="w-3 h-3 text-emerald-400" /> : <Copy className="w-3 h-3" />}
                <span>{copiedCmd ? 'Tersalin' : 'Salin'}</span>
              </button>
            </div>
            <div className="break-all text-xs text-emerald-300">{currentCmd}</div>
          </div>

          <div>
            <label className="block text-[11px] font-semibold text-slate-700 mb-1">
              Catatan / Batas Kuota (Opsional)
            </label>
            <input
              type="text"
              value={formData.catatan || ''}
              onChange={(e) => handleChange('catatan', e.target.value)}
              placeholder="Contoh: Hotspot HP Utama Sales..."
              className="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          {/* Footer */}
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
              className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black flex items-center gap-2 shadow-md transition active:scale-95 disabled:opacity-50"
            >
              {isSaving ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin text-white" />
                  <span>Menyimpan ke Spreadsheet...</span>
                </>
              ) : (
                <>
                  <Save className="w-4 h-4" />
                  <span>Simpan Perubahan Akun</span>
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};
