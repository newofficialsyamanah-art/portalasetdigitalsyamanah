import React, { useState, useEffect } from 'react';
import {
  Wifi,
  Router,
  User,
  Users,
  Briefcase,
  KeyRound,
  Copy,
  Check,
  Eye,
  EyeOff,
  Sparkles,
  Download,
  Terminal,
  Printer,
  Trash2,
  RefreshCw,
  Search,
  CheckCircle2,
  AlertCircle,
  ShieldCheck,
  Send,
  Sliders,
  FileSpreadsheet,
  ExternalLink,
  Loader2,
  Pencil
} from 'lucide-react';
import { MikrotikAccount, MikrotikTeam } from '../types';
import {
  MIKROTIK_TEAMS,
  getMikrotikAccounts,
  fetchMikrotikAccounts,
  addMikrotikAccount,
  deleteMikrotikAccount,
  resetMikrotikSeed,
  syncAllMikrotikToSpreadsheet,
  generateShortCodeUsername,
  generate6DigitPassword,
  generateMikrotikCommand
} from '../services/mikrotikService';
import { pullDataFromGoogleSpreadsheet } from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditMikrotikModal } from './EditMikrotikModal';

interface MikrotikGeneratorProps {
  onSuccess: (message: string) => void;
}

export const MikrotikGenerator: React.FC<MikrotikGeneratorProps> = ({ onSuccess }) => {
  const [accounts, setAccounts] = useState<MikrotikAccount[]>(getMikrotikAccounts());
  const [editAccount, setEditAccount] = useState<MikrotikAccount | null>(null);

  // Generator form state
  const [namaPemilik, setNamaPemilik] = useState('');
  const [tim, setTim] = useState<MikrotikTeam>('jaket1');
  const [jabatan, setJabatan] = useState('');
  const [catatan, setCatatan] = useState('');
  const [formError, setFormError] = useState<string | null>(null);

  // Auto generated values
  const [generatedUsername, setGeneratedUsername] = useState('');
  const [generatedPassword, setGeneratedPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);

  // Filter & Search
  const [teamFilter, setTeamFilter] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [copiedKey, setCopiedKey] = useState<string | null>(null);
  const [copiedBatch, setCopiedBatch] = useState(false);
  const [visiblePasswords, setVisiblePasswords] = useState<Record<string, boolean>>({});

  // Async saving & sync indicators
  const [isSaving, setIsSaving] = useState(false);
  const [isSyncingAll, setIsSyncingAll] = useState(false);
  const [isPulling, setIsPulling] = useState(false);
  const [lastSyncSuccessMessage, setLastSyncSuccessMessage] = useState<string | null>(null);

  // Deletion and reset modal state
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; username: string } | null>(null);
  const [isResetSeedOpen, setIsResetSeedOpen] = useState(false);

  // Fetch from central server & periodic auto-poll
  useEffect(() => {
    fetchMikrotikAccounts().then((latest) => {
      if (Array.isArray(latest)) setAccounts(latest);
    });

    const interval = setInterval(() => {
      fetchMikrotikAccounts().then((latest) => {
        if (Array.isArray(latest)) setAccounts(latest);
      });
    }, 12000);

    return () => clearInterval(interval);
  }, []);

  // Auto-generate short code username (contoh: SJ01, SJ02, SR01, SE01)
  useEffect(() => {
    const autoUser = generateShortCodeUsername(tim, accounts);
    setGeneratedUsername(autoUser);
  }, [tim, accounts]);

  useEffect(() => {
    // Generate initial password maksimal 6 digit (contoh: 749201)
    const autoPass = generate6DigitPassword();
    setGeneratedPassword(autoPass);
  }, [tim]);

  const handleRegeneratePassword = () => {
    setGeneratedPassword(generate6DigitPassword());
  };

  const handleRegenerateUsername = () => {
    setGeneratedUsername(generateShortCodeUsername(tim, accounts));
  };

  const handleCopyText = (text: string, key: string) => {
    navigator.clipboard.writeText(text);
    setCopiedKey(key);
    setTimeout(() => setCopiedKey(null), 2000);
  };

  const handleSaveAccount = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError(null);
    setLastSyncSuccessMessage(null);

    if (!namaPemilik.trim()) {
      setFormError('Nama pemilik atau staf wajib diisi!');
      return;
    }
    if (!jabatan.trim()) {
      setFormError('Jabatan staf wajib diisi!');
      return;
    }
    if (generatedPassword.length > 6) {
      setFormError('Password MikroTik maksimal 6 digit!');
      return;
    }

    setIsSaving(true);

    try {
      const teamConfig = MIKROTIK_TEAMS.find((t) => t.id === tim) || MIKROTIK_TEAMS[0];
      const command = generateMikrotikCommand(
        generatedUsername,
        generatedPassword,
        teamConfig.profile,
        teamConfig.rateLimit,
        namaPemilik,
        jabatan,
        teamConfig.label
      );

      const newAccount: MikrotikAccount = {
        id: 'mkt_' + Date.now(),
        namaPemilik: namaPemilik.trim(),
        jabatan: jabatan.trim(),
        tim,
        username: generatedUsername,
        password: generatedPassword,
        profileHotspot: teamConfig.profile,
        rateLimit: teamConfig.rateLimit,
        commandTerminal: command,
        createdAt: new Date().toLocaleString('id-ID'),
        status: 'aktif',
        catatan: catatan.trim() || undefined
      };

      const result = await addMikrotikAccount(newAccount);
      const updatedAccounts = await fetchMikrotikAccounts();
      setAccounts(updatedAccounts);

      const successNotice = result.gasSynced
        ? `Akun MikroTik ${generatedUsername} (${namaPemilik}) BERHASIL DISIMPAN ke Google Spreadsheet pada lembar (tab): "${result.sheetName || 'Akun MikroTik Hotspot'}"!`
        : `Akun MikroTik ${generatedUsername} (${namaPemilik}) berhasil disimpan ke sistem!`;

      setLastSyncSuccessMessage(successNotice);
      onSuccess(successNotice);

      // Reset form for next entry
      setNamaPemilik('');
      setJabatan('');
      setCatatan('');
      setFormError(null);
      setGeneratedPassword(generate6DigitPassword());
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Gagal menyimpan akun MikroTik';
      setFormError(msg);
    } finally {
      setIsSaving(false);
    }
  };

  const handleSyncAllToSpreadsheet = async () => {
    setIsSyncingAll(true);
    setFormError(null);
    try {
      const res = await syncAllMikrotikToSpreadsheet();
      if (res.success) {
        onSuccess(res.message);
        setLastSyncSuccessMessage(res.message);
        const latest = await fetchMikrotikAccounts();
        setAccounts(latest);
      } else {
        setFormError(res.message || 'Gagal menyinkronkan akun ke Spreadsheet');
      }
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Gagal menyinkronkan data ke Spreadsheet';
      setFormError(msg);
    } finally {
      setIsSyncingAll(false);
    }
  };

  const handlePullFromSpreadsheet = async () => {
    setIsPulling(true);
    setFormError(null);
    try {
      const res = await pullDataFromGoogleSpreadsheet();
      onSuccess(res.message);
      setLastSyncSuccessMessage(res.message);
      const latest = await fetchMikrotikAccounts();
      setAccounts(latest);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Gagal menarik data dari Spreadsheet';
      setFormError(msg);
    } finally {
      setIsPulling(false);
    }
  };

  const handleDelete = (id: string, username: string) => {
    setDeleteTarget({ id, username });
  };

  const confirmSingleDelete = () => {
    if (deleteTarget) {
      deleteMikrotikAccount(deleteTarget.id);
      setAccounts(getMikrotikAccounts());
      setDeleteTarget(null);
    }
  };

  const handleResetSeed = () => {
    setIsResetSeedOpen(true);
  };

  const confirmResetSeed = () => {
    resetMikrotikSeed();
    setAccounts(getMikrotikAccounts());
    onSuccess('Data akun MikroTik 6 tim berhasil dimuat ulang!');
    setIsResetSeedOpen(false);
  };

  const handleCopyAllRouterOsCommands = () => {
    const commands = accounts.map((a) => a.commandTerminal).join('\n');
    navigator.clipboard.writeText(commands);
    setCopiedBatch(true);
    setTimeout(() => setCopiedBatch(false), 3000);
  };

  const exportCsv = () => {
    const headers = [
      'ID',
      'Nama Pemilik',
      'Tim',
      'Jabatan',
      'Username MikroTik',
      'Password',
      'Profile Hotspot',
      'Bandwidth Limit',
      'Perintah Winbox',
      'Tanggal Dibuat'
    ];
    const rows = accounts.map((a) => [
      a.id,
      a.namaPemilik,
      a.tim,
      a.jabatan,
      a.username,
      a.password,
      a.profileHotspot,
      a.rateLimit,
      a.commandTerminal,
      a.createdAt
    ]);

    const csvContent =
      'data:text/csv;charset=utf-8,' +
      [headers, ...rows]
        .map((r) => r.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `akun_mikrotik_sales_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const handleFillSample = (sampleTeam: MikrotikTeam, sampleName: string, sampleJabatan: string) => {
    setTim(sampleTeam);
    setNamaPemilik(sampleName);
    setJabatan(sampleJabatan);
  };

  const filteredAccounts = accounts.filter((acc) => {
    if (teamFilter !== 'all' && acc.tim !== teamFilter) return false;
    if (!searchTerm.trim()) return true;
    const term = searchTerm.toLowerCase();
    return (
      acc.namaPemilik.toLowerCase().includes(term) ||
      acc.username.toLowerCase().includes(term) ||
      acc.jabatan.toLowerCase().includes(term) ||
      acc.tim.toLowerCase().includes(term)
    );
  });

  const getTeamBadgeColor = (t: MikrotikTeam) => {
    switch (t) {
      case 'jaket1':
        return 'bg-amber-100 text-amber-900 border-amber-300';
      case 'jaket2':
        return 'bg-orange-100 text-orange-900 border-orange-300';
      case 'jersey1':
        return 'bg-emerald-100 text-emerald-900 border-emerald-300';
      case 'jersey2':
        return 'bg-teal-100 text-teal-900 border-teal-300';
      case 'eksekutif':
        return 'bg-purple-100 text-purple-900 border-purple-300';
      case 'crm':
        return 'bg-blue-100 text-blue-900 border-blue-300';
      default:
        return 'bg-slate-100 text-slate-800 border-slate-300';
    }
  };

  return (
    <div className="space-y-8">
      {/* Top Banner */}
      <div className="bg-gradient-to-r from-blue-950 via-slate-900 to-emerald-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-blue-900/40 relative overflow-hidden">
        <div className="relative z-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold mb-3 border border-blue-400/20">
            <Router className="w-3.5 h-3.5" />
            MikroTik Hotspot &amp; Network User Manager
          </div>
          <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Generator Akun MikroTik Tim Sales (Kode Pendek &amp; Maks 6 Digit)
          </h2>
          <p className="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
            Alur otomatis pembuatan akun hotspot router MikroTik: masukkan <b>Nama Pemilik</b>, pilih <b>Tim</b> (Jaket 1, Jaket 2, Jersey 1, Jersey 2, Eksekutif, CRM), isi <b>Jabatan</b>. Sistem langsung menghasilkan <b>Username Kode Pendek (misal: SJ01)</b> dan <b>Password Maksimal 6 Digit</b> yang praktis diketik saat login hotspot!
          </p>

          <div className="mt-5 flex flex-wrap gap-2.5">
            <button
              onClick={handleSyncAllToSpreadsheet}
              disabled={isSyncingAll}
              className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-md transition active:scale-95"
            >
              {isSyncingAll ? <Loader2 className="w-4 h-4 animate-spin" /> : <FileSpreadsheet className="w-4 h-4" />}
              <span>{isSyncingAll ? 'Menyinkronkan ke Spreadsheet...' : 'Kirim Semua Akun ke Spreadsheet'}</span>
            </button>
            <button
              onClick={handlePullFromSpreadsheet}
              disabled={isPulling}
              className="px-4 py-2 bg-white/10 hover:bg-white/20 disabled:opacity-60 text-white border border-white/20 rounded-xl text-xs font-semibold flex items-center gap-2 transition"
            >
              {isPulling ? <Loader2 className="w-4 h-4 animate-spin" /> : <RefreshCw className="w-4 h-4" />}
              <span>{isPulling ? 'Menarik...' : 'Tarik Data dari Spreadsheet'}</span>
            </button>
            <button
              onClick={handleCopyAllRouterOsCommands}
              className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-md transition active:scale-95"
            >
              {copiedBatch ? <Check className="w-4 h-4" /> : <Terminal className="w-4 h-4" />}
              <span>{copiedBatch ? 'Perintah Winbox Tersalin!' : 'Salin Semua Script Winbox Terminal'}</span>
            </button>
            <button
              onClick={exportCsv}
              className="px-4 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl text-xs font-semibold flex items-center gap-2 transition"
            >
              <Download className="w-4 h-4" />
              <span>Ekspor CSV Akun</span>
            </button>
            <button
              onClick={handleResetSeed}
              className="px-4 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl text-xs font-semibold flex items-center gap-2 transition"
            >
              <RefreshCw className="w-4 h-4" />
              <span>Muat Ulang 6 Tim Bawaan</span>
            </button>
          </div>
        </div>
      </div>

      {/* Target Tab in Spreadsheet Explanatory Banner */}
      <div className="bg-emerald-50/90 border border-emerald-200 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs shadow-2xs">
        <div className="flex items-start gap-3.5">
          <div className="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs">
            <FileSpreadsheet className="w-5 h-5" />
          </div>
          <div>
            <div className="flex items-center gap-2">
              <span className="font-extrabold text-emerald-950 text-sm">
                Tersinkron Langsung ke Google Spreadsheet Anda
              </span>
              <span className="px-2 py-0.5 rounded-full bg-emerald-200/80 text-emerald-900 font-bold text-[10px]">
                Real-Time CRUD
              </span>
            </div>
            <p className="text-emerald-800 text-xs mt-1 leading-relaxed">
              Setiap kali Anda menekan <b>&quot;Simpan &amp; Terbitkan Akun MikroTik&quot;</b>, baris baru langsung ditulis ke tab lembar: <span className="bg-emerald-100 text-emerald-950 font-bold px-2 py-0.5 rounded border border-emerald-300 font-mono">Akun MikroTik Hotspot</span> (buka tab di bagian paling bawah Google Spreadsheet Anda).
            </p>
          </div>
        </div>
        <div className="flex items-center gap-2 shrink-0">
          <button
            type="button"
            onClick={handleSyncAllToSpreadsheet}
            disabled={isSyncingAll}
            className="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-60 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-xs"
          >
            {isSyncingAll ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <RefreshCw className="w-3.5 h-3.5" />}
            <span>{isSyncingAll ? 'Menyinkronkan...' : 'Sinkronkan Semua Sekarang'}</span>
          </button>
        </div>
      </div>

      {/* Main Generator Form Card */}
      <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        {lastSyncSuccessMessage && (
          <div className="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-center justify-between gap-3 text-xs text-emerald-950 shadow-xs animate-in fade-in">
            <div className="flex items-center gap-2.5">
              <CheckCircle2 className="w-5 h-5 shrink-0 text-emerald-600" />
              <span className="font-bold text-xs sm:text-sm">{lastSyncSuccessMessage}</span>
            </div>
            <button
              type="button"
              onClick={() => setLastSyncSuccessMessage(null)}
              className="text-emerald-700 hover:text-emerald-950 font-black text-sm px-2 py-1 rounded"
            >
              &times;
            </button>
          </div>
        )}

        <div className="flex items-center justify-between pb-4 border-b border-slate-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-200">
              <Wifi className="w-5 h-5" />
            </div>
            <div>
              <h3 className="font-bold text-slate-900 text-base">
                Formulir Generator Akun MikroTik Otomatis
              </h3>
              <p className="text-xs text-slate-500">
                Alur: Masukkan Nama Staf &gt; Pilih Tim &gt; Tulis Jabatan &gt; Username &amp; Password Otomatis Terbuat
              </p>
            </div>
          </div>

          <div className="hidden sm:flex items-center gap-2 text-xs">
            <span className="text-slate-400">Contoh Cepat:</span>
            <button
              type="button"
              onClick={() => handleFillSample('jaket1', 'Devia Rahmawati', 'Sales Officer')}
              className="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium"
            >
              Jaket 1
            </button>
            <button
              type="button"
              onClick={() => handleFillSample('jersey1', 'Wahdah Zulfa', 'Head Jersey')}
              className="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium"
            >
              Jersey 1
            </button>
            <button
              type="button"
              onClick={() => handleFillSample('crm', 'Wilda Nurul', 'Staf CRM')}
              className="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium"
            >
              CRM
            </button>
          </div>
        </div>

        <form onSubmit={handleSaveAccount} className="space-y-6">
          {formError && (
            <div className="p-3.5 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-2.5 text-xs text-rose-700 animate-in fade-in">
              <AlertCircle className="w-4 h-4 shrink-0 text-rose-500" />
              <span className="font-medium">{formError}</span>
            </div>
          )}

          {/* Step 1: Input Fields */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            {/* Nama Pemilik */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                <User className="w-3.5 h-3.5 text-blue-600" />
                <span>Nama Pemilik / Staf <span className="text-rose-500">*</span></span>
              </label>
              <input
                type="text"
                required
                value={namaPemilik}
                onChange={(e) => setNamaPemilik(e.target.value)}
                placeholder="Contoh: Teh Devia, Hesti, Wilda"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
              />
              <span className="text-[11px] text-slate-400 mt-1 block">
                Nama staf pemilik akun MikroTik
              </span>
            </div>

            {/* Tim Sales (jaket1, jaket2, jersey1, jersey2, eksekutif, crm) */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                <Users className="w-3.5 h-3.5 text-blue-600" />
                <span>Pilih Tim Sales <span className="text-rose-500">*</span></span>
              </label>
              <select
                value={tim}
                onChange={(e) => setTim(e.target.value as MikrotikTeam)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white transition"
              >
                <option value="jaket1">Tim Jaket 1 (jaket1)</option>
                <option value="jaket2">Tim Jaket 2 (jaket2)</option>
                <option value="jersey1">Tim Jersey 1 (jersey1)</option>
                <option value="jersey2">Tim Jersey 2 (jersey2)</option>
                <option value="eksekutif">Sales Eksekutif (eksekutif)</option>
                <option value="crm">Tim CRM (crm)</option>
              </select>
              <span className="text-[11px] text-slate-400 mt-1 block">
                Menentukan prefix username &amp; profil bandwidth
              </span>
            </div>

            {/* Jabatan */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                <Briefcase className="w-3.5 h-3.5 text-blue-600" />
                <span>Jabatan Staf <span className="text-rose-500">*</span></span>
              </label>
              <input
                type="text"
                required
                value={jabatan}
                onChange={(e) => setJabatan(e.target.value)}
                placeholder="Contoh: Sales Officer, Admin CRM, Head Jersey"
                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
              />
              <span className="text-[11px] text-slate-400 mt-1 block">
                Akan dicatat pada comment router
              </span>
            </div>
          </div>

          {/* Quick Chip Selector for Teams */}
          <div className="flex flex-wrap items-center gap-2 pt-1">
            <span className="text-xs font-semibold text-slate-500">Pilih Tim Cepat:</span>
            {MIKROTIK_TEAMS.map((t) => (
              <button
                key={t.id}
                type="button"
                onClick={() => setTim(t.id)}
                className={`px-3 py-1.5 rounded-xl text-xs font-bold border transition ${
                  tim === t.id
                    ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                }`}
              >
                {t.label}
              </button>
            ))}
          </div>

          {/* Step 2: Live Auto-Generated Box */}
          <div className="p-5 rounded-2xl bg-gradient-to-br from-blue-50/70 via-indigo-50/40 to-slate-50 border border-blue-200 space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-xs font-extrabold uppercase tracking-wider text-blue-900 flex items-center gap-1.5">
                <Sparkles className="w-4 h-4 text-blue-600" />
                Hasil Akun MikroTik yang Dihasilkan Otomatis
              </span>
              <span className="text-[11px] text-blue-700 font-semibold bg-white px-2.5 py-0.5 rounded-full border border-blue-200">
                Live Generated
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {/* Generated Short Code Username */}
              <div className="bg-white p-3.5 rounded-xl border border-blue-100 shadow-2xs space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[11px] font-bold text-slate-500 uppercase flex items-center gap-1.5">
                    <span>Kode Username Pendek</span>
                    <span className="px-1.5 py-0.2 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded">
                      Contoh: SJ01
                    </span>
                  </span>
                  <button
                    type="button"
                    onClick={handleRegenerateUsername}
                    className="text-[11px] text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
                  >
                    <RefreshCw className="w-3 h-3" /> Reset Kode
                  </button>
                </div>

                <div className="flex items-center justify-between bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                  <input
                    type="text"
                    value={generatedUsername}
                    onChange={(e) => setGeneratedUsername(e.target.value.toUpperCase())}
                    placeholder="Contoh: SJ01"
                    maxLength={10}
                    className="font-mono text-base font-black tracking-wider text-blue-900 bg-transparent border-none focus:outline-none w-full uppercase"
                  />
                  <button
                    type="button"
                    onClick={() => handleCopyText(generatedUsername, 'user_preview')}
                    className="p-1 text-slate-400 hover:text-blue-600 shrink-0"
                  >
                    {copiedKey === 'user_preview' ? (
                      <Check className="w-4 h-4 text-emerald-600" />
                    ) : (
                      <Copy className="w-4 h-4" />
                    )}
                  </button>
                </div>

                {/* Quick Code Presets */}
                <div className="flex items-center gap-1.5 pt-1 overflow-x-auto scrollbar-none">
                  <span className="text-[10px] text-slate-400 font-semibold shrink-0">Pilihan Cepat:</span>
                  {['SJ01', 'SJ02', 'SR01', 'SR02', 'SE01', 'SC01'].map((code) => (
                    <button
                      key={code}
                      type="button"
                      onClick={() => setGeneratedUsername(code)}
                      className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold transition shrink-0 ${
                        generatedUsername === code
                          ? 'bg-blue-600 text-white shadow-2xs'
                          : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                      }`}
                    >
                      {code}
                    </button>
                  ))}
                </div>
              </div>

              {/* Generated Password (Max 6 Digits) */}
              <div className="bg-white p-3.5 rounded-xl border border-blue-100 shadow-2xs space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[11px] font-bold text-slate-500 uppercase flex items-center gap-1.5">
                    <span>Password MikroTik</span>
                    <span className="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded">
                      Maksimal 6 Digit ({generatedPassword.length}/6)
                    </span>
                  </span>
                  <button
                    type="button"
                    onClick={handleRegeneratePassword}
                    className="text-[11px] text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
                  >
                    <RefreshCw className="w-3 h-3" /> Acak 6 Digit Baru
                  </button>
                </div>

                <div className="flex items-center justify-between gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    value={generatedPassword}
                    maxLength={6}
                    onChange={(e) => setGeneratedPassword(e.target.value.slice(0, 6))}
                    placeholder="6 digit"
                    className="font-mono text-base font-black tracking-widest text-emerald-900 bg-transparent border-none focus:outline-none w-full"
                  />
                  <div className="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="p-1 text-slate-400 hover:text-slate-700"
                    >
                      {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                    <button
                      type="button"
                      onClick={() => handleCopyText(generatedPassword, 'pass_preview')}
                      className="p-1 text-slate-400 hover:text-blue-600"
                    >
                      {copiedKey === 'pass_preview' ? (
                        <Check className="w-4 h-4 text-emerald-600" />
                      ) : (
                        <Copy className="w-4 h-4" />
                      )}
                    </button>
                  </div>
                </div>

                <p className="text-[10px] text-slate-400">
                  Format 6 digit angka simpel &amp; cepat diketik staf di layar hotspot HP/Laptop.
                </p>
              </div>
            </div>

            {/* Generated RouterOS Command Preview */}
            <div className="bg-slate-900 text-emerald-300 p-3.5 rounded-xl font-mono text-[11px] flex items-center justify-between gap-3 overflow-x-auto">
              <div className="truncate">
                <span className="text-slate-400 block text-[10px] uppercase font-bold">
                  Perintah Terminal MikroTik Winbox (RouterOS):
                </span>
                <span className="text-xs">
                  {generateMikrotikCommand(
                    generatedUsername,
                    generatedPassword,
                    MIKROTIK_TEAMS.find((t) => t.id === tim)?.profile || 'sales',
                    MIKROTIK_TEAMS.find((t) => t.id === tim)?.rateLimit || '10M/10M',
                    namaPemilik || 'NamaStaf',
                    jabatan || 'Sales',
                    MIKROTIK_TEAMS.find((t) => t.id === tim)?.label || tim
                  )}
                </span>
              </div>
              <button
                type="button"
                onClick={() =>
                  handleCopyText(
                    generateMikrotikCommand(
                      generatedUsername,
                      generatedPassword,
                      MIKROTIK_TEAMS.find((t) => t.id === tim)?.profile || 'sales',
                      MIKROTIK_TEAMS.find((t) => t.id === tim)?.rateLimit || '10M/10M',
                      namaPemilik || 'NamaStaf',
                      jabatan || 'Sales',
                      MIKROTIK_TEAMS.find((t) => t.id === tim)?.label || tim
                    ),
                    'cmd_preview'
                  )
                }
                className="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold shrink-0 flex items-center gap-1"
              >
                {copiedKey === 'cmd_preview' ? (
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                ) : (
                  <Copy className="w-3.5 h-3.5" />
                )}
                <span>Salin Script</span>
              </button>
            </div>
          </div>

          {/* Catatan Tambahan (Opsional) */}
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">
              Catatan / Batas Kuota (Opsional)
            </label>
            <input
              type="text"
              value={catatan}
              onChange={(e) => setCatatan(e.target.value)}
              placeholder="Contoh: Digunakan untuk HP Sales & WhatsApp Web di Laptop"
              className="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          {/* Submit Action */}
          <div className="pt-2 flex justify-end">
            <button
              type="submit"
              disabled={isSaving}
              className="px-8 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 disabled:opacity-60 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg flex items-center gap-2 transition active:scale-95 cursor-pointer"
            >
              {isSaving ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  <span>Menyimpan ke Google Spreadsheet...</span>
                </>
              ) : (
                <>
                  <Send className="w-4 h-4" />
                  <span>Simpan &amp; Terbitkan Akun MikroTik</span>
                </>
              )}
            </button>
          </div>
        </form>
      </div>

      {/* Accounts List Section */}
      <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
          <div>
            <div className="flex items-center gap-2">
              <h3 className="font-extrabold text-slate-900 text-base">
                Daftar Akun MikroTik Tim Sales
              </h3>
              <span className="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                {accounts.length} Akun
              </span>
            </div>
            <p className="text-xs text-slate-500 mt-0.5">
              Kelola kredensial Wi-Fi hotspot router MikroTik untuk staf sales jaket, jersey, eksekutif, dan crm
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            <button
              onClick={handleCopyAllRouterOsCommands}
              className="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition shadow-xs"
            >
              <Terminal className="w-3.5 h-3.5 text-emerald-400" />
              <span>Salin Semua Script Winbox</span>
            </button>
            <button
              onClick={exportCsv}
              className="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-slate-300"
            >
              <Download className="w-3.5 h-3.5 text-blue-600" />
              <span>Ekspor CSV</span>
            </button>
          </div>
        </div>

        {/* Filters */}
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Cari berdasarkan nama staf, username, tim, atau jabatan..."
              className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          <div className="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 overflow-x-auto scrollbar-none shrink-0">
            <button
              onClick={() => setTeamFilter('all')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition shrink-0 ${
                teamFilter === 'all'
                  ? 'bg-white text-slate-900 shadow-2xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Semua ({accounts.length})
            </button>
            {MIKROTIK_TEAMS.map((t) => {
              const count = accounts.filter((a) => a.tim === t.id).length;
              return (
                <button
                  key={t.id}
                  onClick={() => setTeamFilter(t.id)}
                  className={`px-2.5 py-1.5 rounded-lg text-xs font-bold transition shrink-0 ${
                    teamFilter === t.id
                      ? 'bg-blue-600 text-white shadow-2xs'
                      : 'text-slate-600 hover:text-slate-900'
                  }`}
                >
                  {t.label} ({count})
                </button>
              );
            })}
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto border border-slate-200 rounded-2xl">
          <table className="w-full text-left border-collapse text-xs">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                <th className="py-3 px-4">Nama Staf &amp; Jabatan</th>
                <th className="py-3 px-4">Tim Sales</th>
                <th className="py-3 px-4">Username MikroTik</th>
                <th className="py-3 px-4">Password</th>
                <th className="py-3 px-4">Profile &amp; Bandwidth</th>
                <th className="py-3 px-4">Perintah Winbox</th>
                <th className="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredAccounts.length === 0 ? (
                <tr>
                  <td colSpan={7} className="py-8 text-center text-slate-400">
                    Tidak ada akun MikroTik yang cocok dengan filter.
                  </td>
                </tr>
              ) : (
                filteredAccounts.map((acc) => {
                  const isVisible = visiblePasswords[acc.id] || false;
                  return (
                    <tr key={acc.id} className="hover:bg-slate-50/70 transition">
                      {/* Nama & Jabatan */}
                      <td className="py-3 px-4">
                        <span className="font-bold text-slate-900 block text-xs">
                          {acc.namaPemilik}
                        </span>
                        <span className="text-[11px] text-slate-500 block">
                          {acc.jabatan}
                        </span>
                      </td>

                      {/* Tim */}
                      <td className="py-3 px-4">
                        <span
                          className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider ${getTeamBadgeColor(
                            acc.tim
                          )}`}
                        >
                          {acc.tim}
                        </span>
                      </td>

                      {/* Username */}
                      <td className="py-3 px-4">
                        <div className="flex items-center gap-1.5">
                          <code className="font-mono font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                            {acc.username}
                          </code>
                          <button
                            type="button"
                            onClick={() => handleCopyText(acc.username, `user_${acc.id}`)}
                            title="Salin Username"
                            className="p-1 text-slate-400 hover:text-blue-600"
                          >
                            {copiedKey === `user_${acc.id}` ? (
                              <Check className="w-3.5 h-3.5 text-emerald-600" />
                            ) : (
                              <Copy className="w-3.5 h-3.5" />
                            )}
                          </button>
                        </div>
                      </td>

                      {/* Password */}
                      <td className="py-3 px-4">
                        <div className="flex items-center gap-1.5">
                          <code className="font-mono text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                            {isVisible ? acc.password : '••••••••'}
                          </code>
                          <button
                            type="button"
                            onClick={() =>
                              setVisiblePasswords((prev) => ({
                                ...prev,
                                [acc.id]: !prev[acc.id]
                              }))
                            }
                            className="p-1 text-slate-400 hover:text-slate-600"
                          >
                            {isVisible ? (
                              <EyeOff className="w-3.5 h-3.5" />
                            ) : (
                              <Eye className="w-3.5 h-3.5" />
                            )}
                          </button>
                          <button
                            type="button"
                            onClick={() => handleCopyText(acc.password, `pass_${acc.id}`)}
                            title="Salin Password"
                            className="p-1 text-slate-400 hover:text-blue-600"
                          >
                            {copiedKey === `pass_${acc.id}` ? (
                              <Check className="w-3.5 h-3.5 text-emerald-600" />
                            ) : (
                              <Copy className="w-3.5 h-3.5" />
                            )}
                          </button>
                        </div>
                      </td>

                      {/* Profile & Bandwidth */}
                      <td className="py-3 px-4">
                        <span className="font-mono text-[11px] text-slate-700 block">
                          {acc.profileHotspot}
                        </span>
                        <span className="text-[10px] text-emerald-600 font-bold block">
                          Limit: {acc.rateLimit}
                        </span>
                      </td>

                      {/* Command Winbox */}
                      <td className="py-3 px-4">
                        <button
                          type="button"
                          onClick={() => handleCopyText(acc.commandTerminal, `cmd_${acc.id}`)}
                          className="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] border border-slate-300 transition"
                        >
                          {copiedKey === `cmd_${acc.id}` ? (
                            <>
                              <Check className="w-3 h-3 text-emerald-600" />
                              <span className="text-emerald-700 font-bold">Tersalin</span>
                            </>
                          ) : (
                            <>
                              <Terminal className="w-3 h-3 text-blue-600" />
                              <span>Salin Script Winbox</span>
                            </>
                          )}
                        </button>
                      </td>

                      {/* Actions */}
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-1">
                          <button
                            type="button"
                            onClick={() => setEditAccount(acc)}
                            title="Edit Akun MikroTik (Update)"
                            className="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                          >
                            <Pencil className="w-4 h-4" />
                          </button>
                          <button
                            type="button"
                            onClick={() => handleDelete(acc.id, acc.username)}
                            title="Hapus Akun MikroTik (Delete)"
                            className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Confirm Delete Single Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        title="Konfirmasi Hapus Akun MikroTik"
        itemName={deleteTarget?.username || ''}
        itemType="akun MikroTik"
        onConfirm={confirmSingleDelete}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Confirm Reset Seed Modal */}
      <ConfirmDeleteModal
        isOpen={isResetSeedOpen}
        title="Muat Ulang Akun Standar 6 Tim"
        itemName="Akun Standar Jaket 1, Jaket 2, Jersey 1, Jersey 2, Eksekutif, CRM"
        itemType="seluruh konfigurasi awal"
        onConfirm={confirmResetSeed}
        onCancel={() => setIsResetSeedOpen(false)}
      />

      {/* Edit Mikrotik Modal (CRUD: Update) */}
      <EditMikrotikModal
        isOpen={Boolean(editAccount)}
        account={editAccount}
        onClose={() => setEditAccount(null)}
        onSaveSuccess={(updated) => {
          setAccounts((prev) => prev.map((a) => (a.id === updated.id ? updated : a)));
          onSuccess(`Akun MikroTik ${updated.username} berhasil diperbarui!`);
          setEditAccount(null);
        }}
      />
    </div>
  );
};
