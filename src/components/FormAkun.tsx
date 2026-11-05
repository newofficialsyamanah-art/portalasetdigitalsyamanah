import React, { useState, useEffect, useRef } from 'react';
import {
  Smartphone,
  User,
  Users,
  Mail,
  Instagram,
  Facebook,
  Phone,
  AppWindow,
  AlertTriangle,
  Send,
  RotateCcw,
  Sparkles,
  Eye,
  EyeOff,
  ShieldAlert,
  FileCheck2,
  AlertCircle,
  Upload,
  Camera,
  X,
  Plus,
  Trash2,
  CheckCircle2,
  Share2,
  Globe,
  Briefcase
} from 'lucide-react';
import { FormAkun as FormAkunType } from '../types';
import { submitToGoogleAppsScript, DRAFT_AKUN_KEY } from '../services/gasService';
import { getDivisions, fetchDivisions, addDivision } from '../services/divisionService';

interface FormAkunProps {
  onSuccess: (message: string) => void;
  onError: (message: string) => void;
  onOpenSettings: () => void;
  hasGasUrl: boolean;
}

export type PlatformType = 'Email' | 'Instagram' | 'Facebook' | 'TikTok' | 'Canva' | 'Lainnya';

export interface DynamicAccountItem {
  id: string;
  platform: PlatformType;
  username: string;
  password: string;
}

const INITIAL_FORM: FormAkunType = {
  nama: '',
  kategori: '',
  jabatan: '',
  jenisHp: '',
  bekasHp: '',
  fotoHp: '',
  fotoHpName: '',
  akunLoginPc: '',
  email1: '',
  passEmail1: '',
  email2: '',
  passEmail2: '',
  email3: '',
  passEmail3: '',
  ig1: '',
  passIg1: '',
  ig2: '',
  passIg2: '',
  ig3: '',
  passIg3: '',
  fb1: '',
  passFb1: '',
  fb2: '',
  passFb2: '',
  fb3: '',
  passFb3: '',
  noWa: '',
  akunLain: '',
  softwareLain: '',
  catatan: ''
};

const DRAFT_ACCOUNTS_KEY = 'syamanah_draft_accounts_list';

export const FormAkun: React.FC<FormAkunProps> = ({
  onSuccess,
  onError,
  onOpenSettings,
  hasGasUrl
}) => {
  const [formData, setFormData] = useState<FormAkunType>(() => {
    try {
      const draft = localStorage.getItem(DRAFT_AKUN_KEY);
      if (draft) return JSON.parse(draft);
    } catch {
      // ignore
    }
    return INITIAL_FORM;
  });

  const [divisionList, setDivisionList] = useState<string[]>(getDivisions());
  const [isAddingDivision, setIsAddingDivision] = useState(false);
  const [newDivisionInput, setNewDivisionInput] = useState('');

  useEffect(() => {
    fetchDivisions().then((list) => {
      if (list && list.length > 0) setDivisionList(list);
    });
  }, []);

  const handleAddNewDivision = async () => {
    if (!newDivisionInput.trim()) return;
    const res = await addDivision(newDivisionInput.trim());
    setDivisionList(res.divisions);
    handleChange('kategori', newDivisionInput.trim());
    setNewDivisionInput('');
    setIsAddingDivision(false);
  };

  // Dynamic account list starts with EXACTLY ONE account item (as requested by user)
  const [accountList, setAccountList] = useState<DynamicAccountItem[]>(() => {
    try {
      const draftAcc = localStorage.getItem(DRAFT_ACCOUNTS_KEY);
      if (draftAcc) {
        const parsed = JSON.parse(draftAcc);
        if (Array.isArray(parsed) && parsed.length > 0) return parsed;
      }
    } catch {
      // ignore
    }
    return [{ id: 'acc_1', platform: 'Email', username: '', password: '' }];
  });

  const [visiblePasswords, setVisiblePasswords] = useState<Record<string, boolean>>({});
  const [loading, setLoading] = useState(false);
  const [lastSubmittedName, setLastSubmittedName] = useState<string | null>(null);
  const phoneFileInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    try {
      localStorage.setItem(DRAFT_AKUN_KEY, JSON.stringify(formData));
      localStorage.setItem(DRAFT_ACCOUNTS_KEY, JSON.stringify(accountList));
    } catch {
      // ignore
    }
  }, [formData, accountList]);

  const handleChange = (field: keyof FormAkunType, value: any) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  // Dynamic accounts handlers
  const handleAddAccount = (platform: PlatformType = 'Email') => {
    const newId = `acc_${Date.now()}_${accountList.length + 1}`;
    setAccountList((prev) => [
      ...prev,
      { id: newId, platform, username: '', password: '' }
    ]);
  };

  const handleRemoveAccount = (id: string) => {
    if (accountList.length <= 1) return;
    setAccountList((prev) => prev.filter((acc) => acc.id !== id));
  };

  const handleUpdateAccount = (id: string, field: 'platform' | 'username' | 'password', val: string) => {
    setAccountList((prev) =>
      prev.map((acc) => (acc.id === id ? { ...acc, [field]: val } : acc))
    );
  };

  const togglePasswordVisibility = (key: string) => {
    setVisiblePasswords((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const handlePhoneFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > 8 * 1024 * 1024) {
      onError('Ukuran foto terlalu besar. Maksimal 8MB!');
      return;
    }

    const reader = new FileReader();
    reader.onload = () => {
      const base64 = reader.result as string;
      setFormData((prev) => ({
        ...prev,
        fotoHp: base64,
        fotoHpName: file.name
      }));
    };
    reader.readAsDataURL(file);
  };

  const handleRemovePhonePhoto = () => {
    setFormData((prev) => ({
      ...prev,
      fotoHp: '',
      fotoHpName: ''
    }));
    if (phoneFileInputRef.current) {
      phoneFileInputRef.current.value = '';
    }
  };

  const handleReset = () => {
    if (window.confirm('Kosongkan semua isian form Pencatatan Akun & Handphone?')) {
      setFormData(INITIAL_FORM);
      setAccountList([{ id: 'acc_1', platform: 'Email', username: '', password: '' }]);
      localStorage.removeItem(DRAFT_AKUN_KEY);
      localStorage.removeItem(DRAFT_ACCOUNTS_KEY);
      if (phoneFileInputRef.current) {
        phoneFileInputRef.current.value = '';
      }
    }
  };

  const handleFillSample = () => {
    setFormData({
      nama: 'Hesti Puspitasari',
      kategori: 'Sales Eksekutif (Teh Hesti)',
      jabatan: 'Head of Sales Eksekutif',
      jenisHp: 'Samsung Galaxy A15 8GB / 256GB Blue Black',
      bekasHp: 'Baru (Pengadaan Kantor Januari 2026)',
      akunLoginPc: 'sales.syamanah01@gmail.com, hesti.syamanah@gmail.com, syamanah.crm@gmail.com',
      email1: 'hesti.sales@syamanah.com',
      passEmail1: 'HestiSyamanah#2026',
      email2: 'cs.syamanahjersey@gmail.com',
      passEmail2: 'JerseySyamanah!99',
      email3: '',
      passEmail3: '',
      ig1: 'syamanah_official',
      passIg1: 'InstaSyamanah*2026',
      ig2: '',
      passIg2: '',
      ig3: '',
      passIg3: '',
      fb1: 'Syamanah Apparel Indonesia',
      passFb1: 'FbSyamanahOfficial#26',
      fb2: '',
      passFb2: '',
      fb3: '',
      passFb3: '',
      noWa: '085189930491',
      softwareLain: 'Canva Pro Tim Syamanah, Trello CRM, Meta Ads Manager, WhatsApp Business Cloud',
      akunLain: 'Akun Spotify tim ruang sales (spotify.sales@syamanah.com), TikTok Shop Syamanah',
      catatan: 'Handphone dalam kondisi prima dengan tempered glass dan case kantor. Tidak ada aplikasi pihak ketiga berbahaya.'
    });

    setAccountList([
      { id: 'acc_1', platform: 'Email', username: 'hesti.sales@syamanah.com', password: 'HestiSyamanah#2026' },
      { id: 'acc_2', platform: 'Email', username: 'cs.syamanahjersey@gmail.com', password: 'JerseySyamanah!99' },
      { id: 'acc_3', platform: 'Instagram', username: 'syamanah_official', password: 'InstaSyamanah*2026' },
      { id: 'acc_4', platform: 'Facebook', username: 'Syamanah Apparel Indonesia', password: 'FbSyamanahOfficial#26' }
    ]);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!formData.nama.trim()) {
      onError('Nama staf wajib diisi!');
      return;
    }
    if (!formData.kategori.trim()) {
      onError('Kategori Tim / Role wajib diisi!');
      return;
    }
    if (!formData.noWa.trim()) {
      onError('Nomor WhatsApp aktif wajib diisi!');
      return;
    }

    // Pack dynamic accountList into formData fields
    const emails = accountList.filter((a) => a.platform === 'Email' && a.username.trim());
    const igs = accountList.filter((a) => a.platform === 'Instagram' && a.username.trim());
    const fbs = accountList.filter((a) => a.platform === 'Facebook' && a.username.trim());
    const others = accountList.filter(
      (a) => !['Email', 'Instagram', 'Facebook'].includes(a.platform) && a.username.trim()
    );

    const submissionPayload: FormAkunType = {
      ...formData,
      // Emails
      email1: emails[0]?.username || '',
      passEmail1: emails[0]?.password || '',
      email2: emails[1]?.username || '',
      passEmail2: emails[1]?.password || '',
      email3: emails[2]?.username || '',
      passEmail3: emails[2]?.password || '',
      // Instagrams
      ig1: igs[0]?.username || '',
      passIg1: igs[0]?.password || '',
      ig2: igs[1]?.username || '',
      passIg2: igs[1]?.password || '',
      ig3: igs[2]?.username || '',
      passIg3: igs[2]?.password || '',
      // Facebooks
      fb1: fbs[0]?.username || '',
      passFb1: fbs[0]?.password || '',
      fb2: fbs[1]?.username || '',
      passFb2: fbs[1]?.password || '',
      fb3: fbs[2]?.username || '',
      passFb3: fbs[2]?.password || ''
    };

    // If there are other accounts or overflow beyond 3 slots, append them to akunLain or softwareLain
    const extraAccounts: string[] = [];
    if (emails.length > 3) {
      emails.slice(3).forEach((e, idx) => {
        extraAccounts.push(`Email ${idx + 4}: ${e.username} (${e.password ? 'pass: ' + e.password : 'tanpa password'})`);
      });
    }
    if (igs.length > 3) {
      igs.slice(3).forEach((i, idx) => {
        extraAccounts.push(`IG ${idx + 4}: ${i.username} (${i.password ? 'pass: ' + i.password : 'tanpa password'})`);
      });
    }
    if (fbs.length > 3) {
      fbs.slice(3).forEach((f, idx) => {
        extraAccounts.push(`FB ${idx + 4}: ${f.username} (${f.password ? 'pass: ' + f.password : 'tanpa password'})`);
      });
    }
    others.forEach((o) => {
      extraAccounts.push(`${o.platform}: ${o.username} (${o.password ? 'pass: ' + o.password : 'tanpa password'})`);
    });

    if (extraAccounts.length > 0) {
      const currentAkunLain = submissionPayload.akunLain ? submissionPayload.akunLain + ' | ' : '';
      submissionPayload.akunLain = currentAkunLain + extraAccounts.join(' | ');
    }

    setLoading(true);

    try {
      const result = await submitToGoogleAppsScript('akun', submissionPayload);
      if (result.success) {
        setLastSubmittedName(formData.nama);
        onSuccess(result.message);
        setFormData(INITIAL_FORM);
        setAccountList([{ id: 'acc_1', platform: 'Email', username: '', password: '' }]);
        localStorage.removeItem(DRAFT_AKUN_KEY);
        localStorage.removeItem(DRAFT_ACCOUNTS_KEY);
      } else {
        onError(result.message);
      }
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Terjadi kendala saat mengirimkan data akun.';
      onError(msg);
    } finally {
      setLoading(false);
    }
  };

  const getPlatformIcon = (platform: PlatformType) => {
    switch (platform) {
      case 'Email':
        return <Mail className="w-3.5 h-3.5 text-rose-500" />;
      case 'Instagram':
        return <Instagram className="w-3.5 h-3.5 text-pink-600" />;
      case 'Facebook':
        return <Facebook className="w-3.5 h-3.5 text-blue-600" />;
      case 'TikTok':
        return <Share2 className="w-3.5 h-3.5 text-slate-800" />;
      case 'Canva':
        return <AppWindow className="w-3.5 h-3.5 text-teal-600" />;
      default:
        return <Globe className="w-3.5 h-3.5 text-amber-600" />;
    }
  };

  const getPlaceholderByPlatform = (platform: PlatformType) => {
    switch (platform) {
      case 'Email':
        return 'Alamat Email (contoh: sales.syamanah@gmail.com)';
      case 'Instagram':
        return 'Username Instagram (contoh: @syamanah_official)';
      case 'Facebook':
        return 'Nama Akun / Fanpage Facebook (contoh: Syamanah Bandung)';
      case 'TikTok':
        return 'Username TikTok (contoh: @syamanah.shop)';
      case 'Canva':
        return 'Email Login Canva Pro';
      default:
        return 'Nama Akun / Username';
    }
  };

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      {/* Form Top Banner */}
      <div className="p-6 md:p-8 border-b border-slate-100 bg-gradient-to-r from-teal-50/50 via-white to-emerald-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2.5">
            <div className="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-sm">
              <Smartphone className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-slate-800 tracking-tight">
                Formulir Pencatatan Akun &amp; Handphone
              </h2>
              <p className="text-xs text-slate-500 mt-0.5">
                Sinkron langsung ke spreadsheet Form Pencatatan Akun &amp; Sales Syamanah 2026. Input akun dinamis (tambah akun bertahap sesuai kebutuhan).
              </p>
            </div>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <button
            type="button"
            onClick={handleFillSample}
            className="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 flex items-center gap-1.5 shadow-2xs transition cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5 text-amber-500" />
            <span>Muat Contoh Data</span>
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-500 text-xs font-medium hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center gap-1.5 transition cursor-pointer"
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span>Reset</span>
          </button>
        </div>
      </div>

      {/* Gas URL Status notice */}
      {!hasGasUrl && (
        <div className="mx-6 md:mx-8 mt-6 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start justify-between gap-3">
          <div className="flex items-start gap-2.5">
            <AlertCircle className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
            <div>
              <span className="font-bold">Mode Penyimpanan Lokal Aktif:</span> URL Google Apps Script belum diatur. Formulir akan tetap menyimpan data Anda dengan aman di Riwayat &amp; Database lokal, dan Anda dapat menyinkronkannya ke Spreadsheet kapan saja.
            </div>
          </div>
          <button
            onClick={onOpenSettings}
            className="shrink-0 px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-[11px] font-semibold transition cursor-pointer"
          >
            Atur URL
          </button>
        </div>
      )}

      {lastSubmittedName && (
        <div className="mx-6 md:mx-8 mt-4 p-3 rounded-xl bg-teal-50 border border-teal-200 text-xs text-teal-800 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <FileCheck2 className="w-4 h-4 text-teal-600" />
            <span>
              Data akun untuk <b>{lastSubmittedName}</b> baru saja tersimpan ke database &amp; Spreadsheet! Anda dapat memasukkan data akun staf lainnya di bawah ini.
            </span>
          </div>
          <button
            onClick={() => setLastSubmittedName(null)}
            className="text-xs text-teal-700 hover:underline cursor-pointer"
          >
            Tutup
          </button>
        </div>
      )}

      {/* Main Form */}
      <form onSubmit={handleSubmit} className="p-6 md:p-8 space-y-8">
        {/* Section 1: Informasi Pemegang, Jabatan & Device */}
        <div>
          <div className="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
            <User className="w-4 h-4 text-teal-600" />
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-700">
              1. Informasi Pemegang &amp; Device Handphone
            </h3>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Nama Staf <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.nama}
                onChange={(e) => handleChange('nama', e.target.value)}
                placeholder="Contoh: Nita, Seni, Rahma, Livia, Hesti"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
              />
            </div>

            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className="block text-xs font-semibold text-slate-700">
                  Kategori Tim / Divisi <span className="text-rose-500">*</span>
                </label>
                <button
                  type="button"
                  onClick={() => setIsAddingDivision(!isAddingDivision)}
                  className="text-[11px] text-[#3A7D8A] hover:underline font-bold flex items-center gap-1"
                >
                  <Plus className="w-3 h-3" />
                  <span>{isAddingDivision ? 'Batal' : '+ Tambah Tim Baru'}</span>
                </button>
              </div>

              {isAddingDivision && (
                <div className="flex items-center gap-2 mb-2 animate-in fade-in duration-150">
                  <input
                    type="text"
                    value={newDivisionInput}
                    onChange={(e) => setNewDivisionInput(e.target.value)}
                    placeholder="Nama tim baru (misal: Sales Online)"
                    className="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
                    autoFocus
                    onKeyDown={(e) => {
                      if (e.key === 'Enter') {
                        e.preventDefault();
                        handleAddNewDivision();
                      }
                    }}
                  />
                  <button
                    type="button"
                    onClick={handleAddNewDivision}
                    className="px-3.5 py-2 bg-[#3A7D8A] hover:bg-[#2e646f] text-white rounded-xl text-xs font-bold shrink-0 transition shadow-xs"
                  >
                    Simpan
                  </button>
                </div>
              )}

              <input
                type="text"
                required
                list="roles-list"
                value={formData.kategori}
                onChange={(e) => handleChange('kategori', e.target.value)}
                placeholder="Pilih atau ketik kategori tim..."
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#3A7D8A] focus:border-[#3A7D8A] focus:outline-none transition"
              />
              <datalist id="roles-list">
                {divisionList.map((div) => (
                  <option key={div} value={div} />
                ))}
              </datalist>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1">
                <Briefcase className="w-3.5 h-3.5 text-teal-600" />
                <span>Jabatan / Posisi Kerja</span>
              </label>
              <input
                type="text"
                value={formData.jabatan || ''}
                onChange={(e) => handleChange('jabatan', e.target.value)}
                placeholder="Contoh: Sales Officer, CRM Staff, Admin"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Jenis Handphone (Merk, RAM, Memori Internal)
              </label>
              <input
                type="text"
                value={formData.jenisHp}
                onChange={(e) => handleChange('jenisHp', e.target.value)}
                placeholder="Contoh: Samsung A07 8GB/256GB, Galaxy A06 5G 6/128"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Bekas Handphone Siapa?
              </label>
              <input
                type="text"
                value={formData.bekasHp}
                onChange={(e) => handleChange('bekasHp', e.target.value)}
                placeholder="Baru / Teh Aas / Fitri / Kantor"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
                <Phone className="w-3.5 h-3.5 text-emerald-600" />
                <span>No Telepon / WhatsApp Aktif <span className="text-rose-500">*</span></span>
              </label>
              <input
                type="text"
                required
                value={formData.noWa}
                onChange={(e) => handleChange('noWa', e.target.value)}
                placeholder="Contoh: 085189930491"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
              />
            </div>
          </div>

          {/* Upload Foto HP ke Drive */}
          <div className="mt-4 p-4 rounded-xl border border-teal-200 bg-teal-50/40">
            <div className="flex items-center justify-between mb-2">
              <span className="text-xs font-bold text-teal-900 flex items-center gap-1.5">
                <Camera className="w-4 h-4 text-teal-700" />
                Foto Fisik Smartphone (Otomatis Masuk Google Drive)
              </span>
              <span className="text-[11px] text-teal-700">Maks 8MB (Kamera/Galeri)</span>
            </div>

            <div className="flex flex-col sm:flex-row items-center gap-3">
              <input
                type="file"
                ref={phoneFileInputRef}
                accept="image/*"
                onChange={handlePhoneFileChange}
                className="hidden"
                id="phone-photo-input"
              />
              <label
                htmlFor="phone-photo-input"
                className="cursor-pointer border border-teal-300 hover:border-teal-500 bg-white px-4 py-2 rounded-xl text-xs font-bold text-teal-800 flex items-center gap-2 shadow-2xs hover:bg-teal-50 transition"
              >
                <Upload className="w-4 h-4 text-teal-600" />
                <span>Pilih Foto HP</span>
              </label>

              {formData.fotoHp ? (
                <div className="flex items-center gap-3 bg-white px-3 py-1.5 rounded-xl border border-teal-200">
                  <img
                    src={formData.fotoHp}
                    alt="Pratinjau HP"
                    className="w-10 h-10 rounded-lg object-cover border border-slate-200"
                  />
                  <span className="text-xs font-medium text-slate-800 truncate max-w-[150px]">
                    {formData.fotoHpName || 'Foto HP'}
                  </span>
                  <button
                    type="button"
                    onClick={handleRemovePhonePhoto}
                    className="text-rose-600 hover:text-rose-700 text-xs font-semibold p-1 cursor-pointer"
                  >
                    <X className="w-3.5 h-3.5" />
                  </button>
                </div>
              ) : (
                <span className="text-[11px] text-slate-500">
                  Opsional: Ambil foto tampak depan smartphone atau info perangkat
                </span>
              )}
            </div>
          </div>
        </div>

        {/* Section 2: Akun yang Login di PC */}
        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1.5">
            Daftar Akun yang Login di Komputer / Browser PC
          </label>
          <textarea
            rows={2}
            value={formData.akunLoginPc}
            onChange={(e) => handleChange('akunLoginPc', e.target.value)}
            placeholder="Tuliskan daftar email Google, browser profile, atau akun kerja yang aktif di komputer ini..."
            className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
          />
          <span className="text-[11px] text-slate-400 mt-1 block">
            Pisahkan dengan koma jika terdapat lebih dari satu akun (misal: cicisyamanah@gmail.com, WA kantor: 085189930491)
          </span>
        </div>

        {/* ========================================================================= */}
        {/* Section 3: INPUT AKUN DINAMIS (MULAI DARI 1 AKUN + TOMBOL TAMBAH AKUN)     */}
        {/* Sesuai instruksi: "jangan tiga gitu tapi satu aja cuma bisa tambah akun    */}
        {/* terus keluar lagi inputannya gitu itu aja dulu"                            */}
        {/* ========================================================================= */}
        <div className="border border-teal-200 rounded-2xl p-5 md:p-6 bg-gradient-to-b from-teal-50/30 to-white shadow-2xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-teal-100 gap-2">
            <div>
              <div className="flex items-center gap-2">
                <span className="px-2.5 py-0.5 rounded-full bg-teal-600 text-white text-[11px] font-bold">
                  Input Dinamis
                </span>
                <h3 className="text-sm font-bold text-slate-800">
                  Daftar Akun &amp; Kredensial Kerja (Email / Medsos)
                </h3>
              </div>
              <p className="text-xs text-slate-500 mt-1">
                Dimulai dari 1 input akun. Klik tombol <span className="font-semibold text-teal-700">+ Tambah Akun</span> di bawah jika Anda ingin memasukkan akun berikutnya.
              </p>
            </div>

            <div className="flex items-center gap-2">
              <span className="text-xs font-semibold text-teal-800 bg-teal-100/70 px-2.5 py-1 rounded-lg">
                Total Akun: {accountList.length}
              </span>
            </div>
          </div>

          {/* List of Dynamic Account Inputs */}
          <div className="space-y-4">
            {accountList.map((item, index) => {
              const passKey = `dynamic_pass_${item.id}`;
              const isVisible = visiblePasswords[passKey] || false;

              return (
                <div
                  key={item.id}
                  className="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:border-teal-300 transition"
                >
                  {/* Account Header: Number, Platform Selector & Delete Button */}
                  <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div className="flex items-center gap-2">
                      <span className="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200">
                        #{index + 1}
                      </span>
                      <span className="text-xs font-semibold text-slate-600">Pilih Jenis Akun:</span>
                    </div>

                    {/* Platform Pills */}
                    <div className="flex flex-wrap items-center gap-1.5">
                      {(['Email', 'Instagram', 'Facebook', 'TikTok', 'Canva', 'Lainnya'] as PlatformType[]).map(
                        (plat) => (
                          <button
                            key={plat}
                            type="button"
                            onClick={() => handleUpdateAccount(item.id, 'platform', plat)}
                            className={`px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer ${
                              item.platform === plat
                                ? 'bg-teal-600 text-white shadow-2xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                          >
                            {getPlatformIcon(plat)}
                            <span>{plat}</span>
                          </button>
                        )
                      )}

                      {/* Remove Button (Only visible if > 1 account) */}
                      {accountList.length > 1 && (
                        <button
                          type="button"
                          onClick={() => handleRemoveAccount(item.id)}
                          className="ml-2 px-2.5 py-1 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
                          title="Hapus baris akun ini"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                          <span>Hapus</span>
                        </button>
                      )}
                    </div>
                  </div>

                  {/* Input Fields: Username & Password */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                        Username / Alamat {item.platform}
                      </label>
                      <div className="relative">
                        <div className="absolute left-3 top-1/2 -translate-y-1/2">
                          {getPlatformIcon(item.platform)}
                        </div>
                        <input
                          type="text"
                          value={item.username}
                          onChange={(e) => handleUpdateAccount(item.id, 'username', e.target.value)}
                          placeholder={getPlaceholderByPlatform(item.platform)}
                          className="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50/30 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                        Kata Sandi / Password {item.platform}
                      </label>
                      <div className="relative">
                        <input
                          type={isVisible ? 'text' : 'password'}
                          value={item.password}
                          onChange={(e) => handleUpdateAccount(item.id, 'password', e.target.value)}
                          placeholder="Password akun (opsional atau jika ada)"
                          className="w-full px-3.5 py-2 pr-10 rounded-xl border border-slate-300 text-xs bg-slate-50/30 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                        />
                        <button
                          type="button"
                          onClick={() => togglePasswordVisibility(passKey)}
                          className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
                        >
                          {isVisible ? <EyeOff className="w-3.5 h-3.5" /> : <Eye className="w-3.5 h-3.5" />}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>

          {/* "+ Tambah Akun" Action Panel */}
          <div className="mt-5 pt-4 border-t border-teal-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <button
              type="button"
              onClick={() => handleAddAccount('Email')}
              className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
            >
              <Plus className="w-4 h-4" />
              <span>+ Tambah Akun</span>
            </button>

            {/* Quick-add shortcuts */}
            <div className="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
              <span className="text-[11px]">Tambah langsung:</span>
              <button
                type="button"
                onClick={() => handleAddAccount('Email')}
                className="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
              >
                <Mail className="w-3 h-3 text-rose-500" />
                <span>+ Email</span>
              </button>
              <button
                type="button"
                onClick={() => handleAddAccount('Instagram')}
                className="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
              >
                <Instagram className="w-3 h-3 text-pink-600" />
                <span>+ Instagram</span>
              </button>
              <button
                type="button"
                onClick={() => handleAddAccount('Facebook')}
                className="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
              >
                <Facebook className="w-3 h-3 text-blue-600" />
                <span>+ Facebook</span>
              </button>
              <button
                type="button"
                onClick={() => handleAddAccount('TikTok')}
                className="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
              >
                <Share2 className="w-3 h-3 text-slate-800" />
                <span>+ TikTok</span>
              </button>
            </div>
          </div>
        </div>

        {/* Section 4: Akun Software & Akun Lain */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
              <AppWindow className="w-3.5 h-3.5 text-teal-600" />
              <span>Isi Jika Ada Akun Software Lain Milik Syamanah</span>
            </label>
            <input
              type="text"
              value={formData.softwareLain}
              onChange={(e) => handleChange('softwareLain', e.target.value)}
              placeholder="Contoh: Canva Pro, Trello, CorelDraw, Meta Ads, CapCut"
              className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1.5">
              Akun Lain yang Terhubung ke Aset Syamanah
            </label>
            <input
              type="text"
              value={formData.akunLain}
              onChange={(e) => handleChange('akunLain', e.target.value)}
              placeholder="Sebutkan email pribadi pelacak HP, ChatGPT, Spotify, threads, dsb..."
              className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
            />
          </div>
        </div>

        {/* Section 5: Catatan Khusus / Hal Janggal */}
        <div className="p-4 rounded-2xl border border-rose-200 bg-rose-50/50">
          <label className="block text-xs font-bold text-rose-800 mb-1.5 flex items-center gap-1.5">
            <ShieldAlert className="w-4 h-4 text-rose-600" />
            <span>Catatan (Isi jika terdapat sesuatu yang janggal)</span>
          </label>
          <input
            type="text"
            value={formData.catatan}
            onChange={(e) => handleChange('catatan', e.target.value)}
            placeholder="Contoh: Ada portofolio bisnis lain, password akun lama tidak diketahui, aplikasi bajakan/crack, dsb..."
            className="w-full px-3.5 py-2.5 rounded-xl border border-rose-300 bg-white text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none text-slate-800 placeholder:text-rose-300 transition"
          />
        </div>

        {/* Submit Actions */}
        <div className="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="text-xs text-slate-500 flex items-center gap-2">
            <span className="w-2 h-2 rounded-full bg-teal-500 inline-block animate-pulse" />
            <span>Data otomatis tersimpan ke Google Spreadsheet tab &quot;Form Pencatatan Akun &amp; Sales Syamanah 2026&quot;</span>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-teal-600 to-emerald-700 hover:from-teal-700 hover:to-emerald-800 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg flex items-center justify-center gap-2.5 transition active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {loading ? (
              <>
                <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Menyimpan ke Spreadsheet...</span>
              </>
            ) : (
              <>
                <Send className="w-4 h-4" />
                <span>Simpan Data Akun Sales</span>
              </>
            )}
          </button>
        </div>
      </form>
    </div>
  );
};
