import React, { useState, useEffect, useRef } from 'react';
import {
  Monitor,
  Laptop,
  User,
  Building2,
  Briefcase,
  Wifi,
  Lock,
  Eye,
  EyeOff,
  Cpu,
  Layers,
  HardDrive,
  Link,
  Send,
  RotateCcw,
  Sparkles,
  HelpCircle,
  FileCheck2,
  AlertCircle,
  Upload,
  Camera,
  X,
  CheckCircle2,
  Volume2,
  Keyboard,
  Image as ImageIcon
} from 'lucide-react';
import { FormPC as FormPCType, DeviceType } from '../types';
import { submitToGoogleAppsScript, DRAFT_PC_KEY } from '../services/gasService';

interface FormPCProps {
  onSuccess: (message: string) => void;
  onError: (message: string) => void;
  onOpenDxDiagGuide: () => void;
  onOpenSettings: () => void;
  hasGasUrl: boolean;
}

const INITIAL_FORM: FormPCType = {
  nama: '',
  departemen: '',
  jabatan: '',
  jenisPerangkat: 'Laptop',
  computerName: '',
  ip: '',
  passwordPc: '',
  os: '',
  manufacturer: '',
  processor: '',
  memory: '',
  fotoPerangkat: '',
  fotoPerangkatName: '',
  displayLink: '',
  soundLink: '',
  inputLink: '',
  displayPhoto: '',
  displayPhotoName: '',
  soundPhoto: '',
  soundPhotoName: '',
  inputPhoto: '',
  inputPhotoName: ''
};

export const FormPC: React.FC<FormPCProps> = ({
  onSuccess,
  onError,
  onOpenDxDiagGuide,
  onOpenSettings,
  hasGasUrl
}) => {
  const [formData, setFormData] = useState<FormPCType>(() => {
    try {
      const draft = localStorage.getItem(DRAFT_PC_KEY);
      if (draft) return JSON.parse(draft);
    } catch {
      // fallback
    }
    return INITIAL_FORM;
  });

  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [lastSubmittedName, setLastSubmittedName] = useState<string | null>(null);
  const [showManualUrl, setShowManualUrl] = useState(false);

  // Refs for file inputs
  const physicalPhotoRef = useRef<HTMLInputElement>(null);
  const displayPhotoRef = useRef<HTMLInputElement>(null);
  const soundPhotoRef = useRef<HTMLInputElement>(null);
  const inputPhotoRef = useRef<HTMLInputElement>(null);

  // Auto-save draft
  useEffect(() => {
    try {
      localStorage.setItem(DRAFT_PC_KEY, JSON.stringify(formData));
    } catch {
      // ignore
    }
  }, [formData]);

  const handleChange = (field: keyof FormPCType, value: any) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  // Upload handler for screenshots and physical photo
  const handleFileUpload = (
    fieldPrefix: 'fotoPerangkat' | 'display' | 'sound' | 'input',
    e: React.ChangeEvent<HTMLInputElement>
  ) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
      onError('Ukuran gambar terlalu besar. Maksimal 10MB!');
      return;
    }

    const reader = new FileReader();
    reader.onload = () => {
      const base64 = reader.result as string;
      if (fieldPrefix === 'fotoPerangkat') {
        setFormData((prev) => ({
          ...prev,
          fotoPerangkat: base64,
          fotoPerangkatName: file.name
        }));
      } else {
        const photoKey = `${fieldPrefix}Photo` as keyof FormPCType;
        const nameKey = `${fieldPrefix}PhotoName` as keyof FormPCType;
        setFormData((prev) => ({
          ...prev,
          [photoKey]: base64,
          [nameKey]: file.name
        }));
      }
    };
    reader.readAsDataURL(file);
  };

  const handleRemovePhoto = (fieldPrefix: 'fotoPerangkat' | 'display' | 'sound' | 'input') => {
    if (fieldPrefix === 'fotoPerangkat') {
      setFormData((prev) => ({
        ...prev,
        fotoPerangkat: '',
        fotoPerangkatName: ''
      }));
      if (physicalPhotoRef.current) physicalPhotoRef.current.value = '';
    } else {
      const photoKey = `${fieldPrefix}Photo` as keyof FormPCType;
      const nameKey = `${fieldPrefix}PhotoName` as keyof FormPCType;
      setFormData((prev) => ({
        ...prev,
        [photoKey]: '',
        [nameKey]: ''
      }));
      if (fieldPrefix === 'display' && displayPhotoRef.current) displayPhotoRef.current.value = '';
      if (fieldPrefix === 'sound' && soundPhotoRef.current) soundPhotoRef.current.value = '';
      if (fieldPrefix === 'input' && inputPhotoRef.current) inputPhotoRef.current.value = '';
    }
  };

  const handleReset = () => {
    if (window.confirm('Kosongkan semua isian form Spesifikasi PC & Laptop?')) {
      setFormData(INITIAL_FORM);
      localStorage.removeItem(DRAFT_PC_KEY);
      if (physicalPhotoRef.current) physicalPhotoRef.current.value = '';
      if (displayPhotoRef.current) displayPhotoRef.current.value = '';
      if (soundPhotoRef.current) soundPhotoRef.current.value = '';
      if (inputPhotoRef.current) inputPhotoRef.current.value = '';
    }
  };

  const handleFillSample = () => {
    setFormData({
      nama: 'Pak Ari',
      departemen: 'Sales & CRM',
      jabatan: 'Manager',
      jenisPerangkat: 'Laptop',
      computerName: 'LAPTOP-V4QV6H8M',
      ip: '192.168.99.141',
      passwordPc: '-',
      os: 'Windows 11 Home Single Language 64 bit',
      manufacturer: 'Asustek Computer Inc',
      processor: '12th Gen Intel Core i3-1215U (8 CPUs)',
      memory: '8192MB RAM',
      fotoPerangkat: '',
      fotoPerangkatName: '',
      displayLink: 'https://drive.google.com/open?id=18MEUxFCv1GSZ_lmaavcw5qc8v34lkJOm',
      soundLink: 'https://drive.google.com/open?id=1MOQOgz_hg7LcaaQR1x9RIUm3kAXPAA7g',
      inputLink: 'https://drive.google.com/open?id=1GUrX4WLXj6wxNb_erbSneR0X--_1pjCA',
      displayPhoto: '',
      displayPhotoName: '',
      soundPhoto: '',
      soundPhotoName: '',
      inputPhoto: '',
      inputPhotoName: ''
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!formData.nama.trim()) {
      onError('Nama staf pemilik perangkat wajib diisi!');
      return;
    }
    if (!formData.departemen) {
      onError('Departemen wajib dipilih!');
      return;
    }
    if (!formData.computerName.trim()) {
      onError('Computer Name wajib diisi!');
      return;
    }

    setLoading(true);

    try {
      const result = await submitToGoogleAppsScript('pc', formData);
      if (result.success) {
        setLastSubmittedName(formData.nama);
        onSuccess(result.message);
        setFormData(INITIAL_FORM);
        localStorage.removeItem(DRAFT_PC_KEY);
        if (physicalPhotoRef.current) physicalPhotoRef.current.value = '';
        if (displayPhotoRef.current) displayPhotoRef.current.value = '';
        if (soundPhotoRef.current) soundPhotoRef.current.value = '';
        if (inputPhotoRef.current) inputPhotoRef.current.value = '';
      } else {
        onError(result.message);
      }
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Terjadi kesalahan sistem saat mengirim data.';
      onError(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      {/* Form Top Banner */}
      <div className="p-6 md:p-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/50 via-white to-teal-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2.5">
            <div className="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
              <Laptop className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-slate-800 tracking-tight">
                Formulir Spesifikasi Perangkat &amp; Laptop
              </h2>
              <p className="text-xs text-slate-500 mt-0.5">
                Sinkron langsung ke tab &quot;Catatan Spesifikasi Perangkat PC Syamanah&quot;. Unggah foto screenshot DxDiag langsung dari perangkat ke Google Drive.
              </p>
            </div>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <button
            type="button"
            onClick={onOpenDxDiagGuide}
            className="px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-semibold hover:bg-emerald-100 flex items-center gap-1.5 transition cursor-pointer"
          >
            <HelpCircle className="w-3.5 h-3.5 text-emerald-600" />
            <span>Panduan DxDiag</span>
          </button>
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
              <span className="font-bold">Mode Penyimpanan Lokal Aktif:</span> Belum ada URL Google Apps Script. Data &amp; foto akan tetap aman di inventaris perangkat lokal, dan Anda dapat menyinkronkannya ke Spreadsheet kapan saja di menu Pengaturan.
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
        <div className="mx-6 md:mx-8 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <FileCheck2 className="w-4 h-4 text-emerald-600" />
            <span>
              Data perangkat untuk <b>{lastSubmittedName}</b> baru saja tersimpan ke Spreadsheet &amp; Google Drive! Anda dapat memasukkan data perangkat lainnya di bawah ini.
            </span>
          </div>
          <button
            onClick={() => setLastSubmittedName(null)}
            className="text-xs text-emerald-700 hover:underline cursor-pointer"
          >
            Tutup
          </button>
        </div>
      )}

      {/* Main Form */}
      <form onSubmit={handleSubmit} className="p-6 md:p-8 space-y-8">
        {/* Section 1: Informasi Pemilik */}
        <div>
          <div className="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
            <User className="w-4 h-4 text-emerald-600" />
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-700">
              1. Informasi Pemilik Perangkat
            </h3>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Nama Lengkap Pemilik <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.nama}
                onChange={(e) => handleChange('nama', e.target.value)}
                placeholder="Contoh: Pak Ari, Wilda, Hesti, Rosa"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Departemen <span className="text-rose-500">*</span>
              </label>
              <select
                required
                value={formData.departemen}
                onChange={(e) => handleChange('departemen', e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition bg-white"
              >
                <option value="">Pilih Departemen...</option>
                <option value="Sales & CRM">Sales &amp; CRM</option>
                <option value="Marketing Communication & Advertiser">Marketing Communication &amp; Advertiser</option>
                <option value="Finance">Finance</option>
                <option value="Accounting">Accounting</option>
                <option value="Operasional Kantor">Operasional Kantor</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Jabatan / Peran Kerja <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.jabatan}
                onChange={(e) => handleChange('jabatan', e.target.value)}
                placeholder="Contoh: Manager, Staf CRM, Head Jacket, Desain Grafis"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>
          </div>
        </div>

        {/* Section 2: Identifikasi Perangkat */}
        <div>
          <div className="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
            <Monitor className="w-4 h-4 text-emerald-600" />
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-700">
              2. Identifikasi Perangkat
            </h3>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Jenis Perangkat <span className="text-rose-500">*</span>
              </label>
              <select
                value={formData.jenisPerangkat}
                onChange={(e) => handleChange('jenisPerangkat', e.target.value as DeviceType)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition bg-white"
              >
                <option value="Laptop">Laptop / Notebook</option>
                <option value="PC Desktop">PC Desktop / Tower</option>
                <option value="All-in-One">All-in-One PC (AIO)</option>
                <option value="Mini PC">Mini PC</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Computer Name <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.computerName}
                onChange={(e) => handleChange('computerName', e.target.value)}
                placeholder="Contoh: LAPTOP-V4QV6H8M, DESKTOP-D0N0ANQ"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                IP Address <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.ip}
                onChange={(e) => handleChange('ip', e.target.value)}
                placeholder="Contoh: 192.168.99.141"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Password PC (Opsional)
              </label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  value={formData.passwordPc}
                  onChange={(e) => handleChange('passwordPc', e.target.value)}
                  placeholder="Isi jika ada password login Windows"
                  className="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>
          </div>

          {/* Upload Foto Fisik Perangkat */}
          <div className="mt-4 p-4 rounded-xl border border-emerald-200 bg-emerald-50/40">
            <div className="flex items-center justify-between mb-2">
              <span className="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                <Camera className="w-4 h-4 text-emerald-700" />
                Foto Fisik Laptop / PC (Otomatis Masuk Google Drive)
              </span>
              <span className="text-[11px] text-emerald-700">Maks 10MB</span>
            </div>

            <div className="flex flex-col sm:flex-row items-center gap-3">
              <input
                type="file"
                ref={physicalPhotoRef}
                accept="image/*"
                onChange={(e) => handleFileUpload('fotoPerangkat', e)}
                className="hidden"
                id="pc-physical-photo"
              />
              <label
                htmlFor="pc-physical-photo"
                className="cursor-pointer border border-emerald-300 hover:border-emerald-500 bg-white px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 flex items-center gap-2 shadow-2xs hover:bg-emerald-50 transition"
              >
                <Upload className="w-4 h-4 text-emerald-600" />
                <span>Pilih Foto Fisik Perangkat</span>
              </label>

              {formData.fotoPerangkat ? (
                <div className="flex items-center gap-3 bg-white px-3 py-1.5 rounded-xl border border-emerald-200">
                  <img
                    src={formData.fotoPerangkat}
                    alt="Pratinjau PC"
                    className="w-10 h-10 rounded-lg object-cover border border-slate-200"
                  />
                  <span className="text-xs font-medium text-slate-800 truncate max-w-[150px]">
                    {formData.fotoPerangkatName || 'Foto Fisik'}
                  </span>
                  <button
                    type="button"
                    onClick={() => handleRemovePhoto('fotoPerangkat')}
                    className="text-rose-600 hover:text-rose-700 text-xs font-semibold p-1 cursor-pointer"
                  >
                    <X className="w-3.5 h-3.5" />
                  </button>
                </div>
              ) : (
                <span className="text-[11px] text-slate-500">
                  Opsional: Foto tampak depan laptop atau casing PC
                </span>
              )}
            </div>
          </div>
        </div>

        {/* Section 3: Spesifikasi Sistem (DxDiag) */}
        <div>
          <div className="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100">
            <Cpu className="w-4 h-4 text-emerald-600" />
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-700">
              3. Spesifikasi Sistem (Dari DxDiag System Information)
            </h3>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Operating System <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.os}
                onChange={(e) => handleChange('os', e.target.value)}
                placeholder="Contoh: Windows 11 Home / Windows 10 Pro"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                System Manufacturer / Merk Laptop
              </label>
              <input
                type="text"
                value={formData.manufacturer}
                onChange={(e) => handleChange('manufacturer', e.target.value)}
                placeholder="Contoh: Asustek, Lenovo, Gigabyte, MSI, Dell"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Processor <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.processor}
                onChange={(e) => handleChange('processor', e.target.value)}
                placeholder="Contoh: 12th Gen Intel Core i3-1215U / Core i5-3570"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                Memory / RAM <span className="text-rose-500">*</span>
              </label>
              <input
                type="text"
                required
                value={formData.memory}
                onChange={(e) => handleChange('memory', e.target.value)}
                placeholder="Contoh: 8192MB RAM (8GB) / 16GB"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
              />
            </div>
          </div>
        </div>

        {/* ========================================================================= */}
        {/* Section 4: UPLOAD FOTO SCREENSHOT DXDIAG (DISPLAY, SOUND, INPUT)          */}
        {/* Sesuai instruksi: "yang screenshoot itu jangan link yang di input fotonya  */}
        {/* tapi tetep masuknya ke drive itu"                                          */}
        {/* ========================================================================= */}
        <div className="border border-emerald-200 rounded-2xl p-5 md:p-6 bg-gradient-to-b from-emerald-50/40 via-white to-slate-50/30 shadow-2xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-emerald-100 gap-2">
            <div>
              <div className="flex items-center gap-2">
                <span className="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[11px] font-bold">
                  Upload Foto Langsung
                </span>
                <h3 className="text-sm font-bold text-slate-800">
                  Foto Screenshot DxDiag (Display, Sound, Input)
                </h3>
              </div>
              <p className="text-xs text-slate-500 mt-1">
                Pilih atau ambil foto screenshot DxDiag di bawah ini. Foto akan <b>otomatis diunggah ke Google Drive</b> dan tautannya tercatat rapi di Spreadsheet!
              </p>
            </div>

            <button
              type="button"
              onClick={onOpenDxDiagGuide}
              className="px-3 py-1.5 rounded-lg border border-emerald-300 bg-white hover:bg-emerald-50 text-emerald-800 text-xs font-semibold flex items-center gap-1.5 transition self-start sm:self-auto cursor-pointer"
            >
              <HelpCircle className="w-3.5 h-3.5 text-emerald-600" />
              <span>Cara Screenshot DxDiag</span>
            </button>
          </div>

          {/* 3 Screenshot Upload Cards */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            {/* 1. Screenshot Tab Display */}
            <div className="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:border-emerald-300 transition flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <Monitor className="w-4 h-4 text-emerald-600" />
                    <span>Tab Display DxDiag</span>
                  </span>
                  <span className="text-[10px] uppercase font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    Display
                  </span>
                </div>
                <p className="text-[11px] text-slate-500 mb-3">
                  Screenshot informasi GPU, VGA, resolusi, dan display driver.
                </p>
              </div>

              <div>
                <input
                  type="file"
                  ref={displayPhotoRef}
                  accept="image/*"
                  onChange={(e) => handleFileUpload('display', e)}
                  className="hidden"
                  id="screenshot-display-upload"
                />

                {formData.displayPhoto ? (
                  <div className="p-3 bg-emerald-50/60 rounded-xl border border-emerald-200 space-y-2">
                    <div className="flex items-center gap-3">
                      <img
                        src={formData.displayPhoto}
                        alt="Display DxDiag"
                        className="w-14 h-14 rounded-lg object-cover border border-emerald-300 shrink-0"
                      />
                      <div className="overflow-hidden flex-1">
                        <span className="text-xs font-bold text-slate-800 block truncate">
                          {formData.displayPhotoName || 'Screenshot Display'}
                        </span>
                        <span className="text-[11px] text-emerald-700 font-medium flex items-center gap-1 mt-0.5">
                          <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                          Siap masuk Drive
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemovePhoto('display')}
                        className="text-rose-600 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer"
                        title="Ganti atau hapus foto"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                ) : (
                  <label
                    htmlFor="screenshot-display-upload"
                    className="cursor-pointer border-2 border-dashed border-slate-300 hover:border-emerald-500 hover:bg-emerald-50/50 p-4 rounded-xl flex flex-col items-center justify-center text-center transition group"
                  >
                    <div className="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mb-1.5 group-hover:scale-105 transition">
                      <Upload className="w-4 h-4" />
                    </div>
                    <span className="text-xs font-bold text-slate-700 group-hover:text-emerald-700">
                      Pilih Foto Display
                    </span>
                    <span className="text-[10px] text-slate-400 mt-0.5">
                      Klik dari galeri / kamera (Maks 10MB)
                    </span>
                  </label>
                )}
              </div>
            </div>

            {/* 2. Screenshot Tab Sound */}
            <div className="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:border-emerald-300 transition flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <Volume2 className="w-4 h-4 text-blue-600" />
                    <span>Tab Sound DxDiag</span>
                  </span>
                  <span className="text-[10px] uppercase font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">
                    Sound
                  </span>
                </div>
                <p className="text-[11px] text-slate-500 mb-3">
                  Screenshot informasi audio chip, speaker, dan headset hardware.
                </p>
              </div>

              <div>
                <input
                  type="file"
                  ref={soundPhotoRef}
                  accept="image/*"
                  onChange={(e) => handleFileUpload('sound', e)}
                  className="hidden"
                  id="screenshot-sound-upload"
                />

                {formData.soundPhoto ? (
                  <div className="p-3 bg-blue-50/60 rounded-xl border border-blue-200 space-y-2">
                    <div className="flex items-center gap-3">
                      <img
                        src={formData.soundPhoto}
                        alt="Sound DxDiag"
                        className="w-14 h-14 rounded-lg object-cover border border-blue-300 shrink-0"
                      />
                      <div className="overflow-hidden flex-1">
                        <span className="text-xs font-bold text-slate-800 block truncate">
                          {formData.soundPhotoName || 'Screenshot Sound'}
                        </span>
                        <span className="text-[11px] text-blue-700 font-medium flex items-center gap-1 mt-0.5">
                          <CheckCircle2 className="w-3 h-3 text-blue-600" />
                          Siap masuk Drive
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemovePhoto('sound')}
                        className="text-rose-600 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer"
                        title="Ganti atau hapus foto"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                ) : (
                  <label
                    htmlFor="screenshot-sound-upload"
                    className="cursor-pointer border-2 border-dashed border-slate-300 hover:border-blue-500 hover:bg-blue-50/50 p-4 rounded-xl flex flex-col items-center justify-center text-center transition group"
                  >
                    <div className="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center mb-1.5 group-hover:scale-105 transition">
                      <Upload className="w-4 h-4" />
                    </div>
                    <span className="text-xs font-bold text-slate-700 group-hover:text-blue-700">
                      Pilih Foto Sound
                    </span>
                    <span className="text-[10px] text-slate-400 mt-0.5">
                      Klik dari galeri / kamera (Maks 10MB)
                    </span>
                  </label>
                )}
              </div>
            </div>

            {/* 3. Screenshot Tab Input */}
            <div className="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:border-emerald-300 transition flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <Keyboard className="w-4 h-4 text-purple-600" />
                    <span>Tab Input DxDiag</span>
                  </span>
                  <span className="text-[10px] uppercase font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                    Input
                  </span>
                </div>
                <p className="text-[11px] text-slate-500 mb-3">
                  Screenshot perangkat keyboard, mouse, USB, dan peripheral input.
                </p>
              </div>

              <div>
                <input
                  type="file"
                  ref={inputPhotoRef}
                  accept="image/*"
                  onChange={(e) => handleFileUpload('input', e)}
                  className="hidden"
                  id="screenshot-input-upload"
                />

                {formData.inputPhoto ? (
                  <div className="p-3 bg-purple-50/60 rounded-xl border border-purple-200 space-y-2">
                    <div className="flex items-center gap-3">
                      <img
                        src={formData.inputPhoto}
                        alt="Input DxDiag"
                        className="w-14 h-14 rounded-lg object-cover border border-purple-300 shrink-0"
                      />
                      <div className="overflow-hidden flex-1">
                        <span className="text-xs font-bold text-slate-800 block truncate">
                          {formData.inputPhotoName || 'Screenshot Input'}
                        </span>
                        <span className="text-[11px] text-purple-700 font-medium flex items-center gap-1 mt-0.5">
                          <CheckCircle2 className="w-3 h-3 text-purple-600" />
                          Siap masuk Drive
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemovePhoto('input')}
                        className="text-rose-600 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer"
                        title="Ganti atau hapus foto"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                ) : (
                  <label
                    htmlFor="screenshot-input-upload"
                    className="cursor-pointer border-2 border-dashed border-slate-300 hover:border-purple-500 hover:bg-purple-50/50 p-4 rounded-xl flex flex-col items-center justify-center text-center transition group"
                  >
                    <div className="w-9 h-9 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center mb-1.5 group-hover:scale-105 transition">
                      <Upload className="w-4 h-4" />
                    </div>
                    <span className="text-xs font-bold text-slate-700 group-hover:text-purple-700">
                      Pilih Foto Input
                    </span>
                    <span className="text-[10px] text-slate-400 mt-0.5">
                      Klik dari galeri / kamera (Maks 10MB)
                    </span>
                  </label>
                )}
              </div>
            </div>
          </div>

          {/* Toggle for manual link (optional fallback) */}
          <div className="mt-4 pt-3 border-t border-slate-100">
            <button
              type="button"
              onClick={() => setShowManualUrl(!showManualUrl)}
              className="text-xs text-slate-500 hover:text-emerald-700 font-semibold flex items-center gap-1.5 cursor-pointer"
            >
              <Link className="w-3.5 h-3.5 text-slate-400" />
              <span>{showManualUrl ? 'Sembunyikan Input Link Manual' : 'Sudah ada link Google Drive? Klik di sini untuk tempel URL'}</span>
            </button>

            {showManualUrl && (
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                  <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                    Display Link Manual
                  </label>
                  <input
                    type="url"
                    value={formData.displayLink}
                    onChange={(e) => handleChange('displayLink', e.target.value)}
                    placeholder="https://drive.google.com/..."
                    className="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-mono bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                    Sound Link Manual
                  </label>
                  <input
                    type="url"
                    value={formData.soundLink}
                    onChange={(e) => handleChange('soundLink', e.target.value)}
                    placeholder="https://drive.google.com/..."
                    className="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-mono bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                    Input Link Manual
                  </label>
                  <input
                    type="url"
                    value={formData.inputLink}
                    onChange={(e) => handleChange('inputLink', e.target.value)}
                    placeholder="https://drive.google.com/..."
                    className="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-mono bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500"
                  />
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Submit Actions */}
        <div className="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="text-xs text-slate-500 flex items-center gap-2">
            <span className="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse" />
            <span>Foto screenshot &amp; data otomatis masuk ke Google Drive &amp; Spreadsheet &quot;Catatan Spesifikasi Perangkat PC Syamanah&quot;</span>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg flex items-center justify-center gap-2.5 transition active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {loading ? (
              <>
                <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Mengunggah Foto ke Drive &amp; Menyimpan...</span>
              </>
            ) : (
              <>
                <Send className="w-4 h-4" />
                <span>Simpan &amp; Unggah ke Drive</span>
              </>
            )}
          </button>
        </div>
      </form>
    </div>
  );
};
