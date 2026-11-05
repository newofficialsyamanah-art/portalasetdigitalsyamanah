import React, { useState, useEffect } from 'react';
import {
  ShieldCheck,
  Lock,
  Unlock,
  KeyRound,
  FileSpreadsheet,
  Printer,
  Download,
  Laptop,
  Monitor,
  Smartphone,
  Users,
  AlertTriangle,
  Eye,
  EyeOff,
  CheckCircle2,
  PieChart,
  HardDrive,
  Cpu,
  Mail,
  Instagram,
  Facebook,
  Search,
  Building2,
  Calendar,
  Layers,
  Plus,
  Pencil,
  Trash2,
  Copy,
  Check,
  Wifi,
  ExternalLink,
  Edit2,
  Tag,
  Briefcase,
  FolderOpen,
  Terminal
} from 'lucide-react';
import {
  SubmissionRecord,
  FormPC,
  FormAkun,
  MikrotikAccount,
  UserAccount,
  SyamanahConfig,
  UserRole
} from '../types';
import { deleteSubmission, getConfig } from '../services/gasService';
import {
  getDivisions,
  fetchDivisions,
  addDivision,
  updateDivision,
  deleteDivision
} from '../services/divisionService';
import {
  deleteMikrotikAccount,
  fetchMikrotikAccounts
} from '../services/mikrotikService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';
import { EditMikrotikModal } from './EditMikrotikModal';
import { UserManagement } from './UserManagement';

interface SuperadminSummaryProps {
  records: SubmissionRecord[];
  mikrotikAccounts?: MikrotikAccount[];
  onRefresh: () => void;
  onOpenSettings: () => void;
  onOpenPcForm: () => void;
  onOpenAkunForm: () => void;
  onOpenMikrotikForm: () => void;
  currentUser: UserAccount | null;
}

type SuperadminTab = 'divisions' | 'pc' | 'akun' | 'mikrotik' | 'users' | 'analytics';

export const SuperadminSummary: React.FC<SuperadminSummaryProps> = ({
  records,
  mikrotikAccounts = [],
  onRefresh,
  onOpenSettings,
  onOpenPcForm,
  onOpenAkunForm,
  onOpenMikrotikForm,
  currentUser
}) => {
  const [activeTab, setActiveTab] = useState<SuperadminTab>('divisions');
  const [config] = useState<SyamanahConfig>(getConfig());
  const [divisions, setDivisions] = useState<string[]>(getDivisions());

  // Division CRUD state
  const [newDivName, setNewDivName] = useState('');
  const [editingDiv, setEditingDiv] = useState<{ oldName: string; newName: string } | null>(null);
  const [deleteDivTarget, setDeleteDivTarget] = useState<string | null>(null);

  // Search & Filter state
  const [searchTerm, setSearchTerm] = useState('');
  const [filterDept, setFilterDept] = useState('all');
  const [revealedPasswords, setRevealedPasswords] = useState<Record<string, boolean>>({});
  const [copiedKey, setCopiedKey] = useState<string | null>(null);

  // Modals for CRUD on records
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string; type: 'pc' | 'akun' | 'mikrotik' } | null>(null);
  const [editMikrotik, setEditMikrotik] = useState<MikrotikAccount | null>(null);

  // PIN fallback if not logged in
  const isSuperLoggedIn = currentUser?.role === 'superadmin';
  const [pinInput, setPinInput] = useState('');
  const [isUnlocked, setIsUnlocked] = useState(isSuperLoggedIn);
  const [pinError, setPinError] = useState('');

  useEffect(() => {
    if (isSuperLoggedIn) {
      setIsUnlocked(true);
    }
  }, [isSuperLoggedIn]);

  useEffect(() => {
    fetchDivisions().then((list) => {
      if (list && list.length > 0) setDivisions(list);
    });
  }, []);

  const pcRecords = records.filter((r) => r.type === 'pc');
  const akunRecords = records.filter((r) => r.type === 'akun');

  // Breakdown Calculations
  const totalLaptops = pcRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'Laptop' || !(r.data as FormPC).jenisPerangkat
  ).length;
  const totalPCDesktop = pcRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'PC Desktop'
  ).length;
  const totalSmartphones = akunRecords.length;

  // Division metrics
  const getDivisionStats = (divName: string) => {
    const pcCount = pcRecords.filter((r) => {
      const d = (r.data as FormPC).departemen || '';
      return d.toLowerCase() === divName.toLowerCase();
    }).length;

    const hpCount = akunRecords.filter((r) => {
      const d = (r.data as FormAkun).kategori || '';
      return d.toLowerCase().includes(divName.toLowerCase()) || divName.toLowerCase().includes(d.toLowerCase());
    }).length;

    const mktCount = mikrotikAccounts.filter((m) => {
      return (m.tim || '').toLowerCase().includes(divName.toLowerCase()) || divName.toLowerCase().includes((m.tim || '').toLowerCase());
    }).length;

    return { pcCount, hpCount, mktCount, total: pcCount + hpCount + mktCount };
  };

  const handleCopy = (text: string, key: string) => {
    navigator.clipboard.writeText(text);
    setCopiedKey(key);
    setTimeout(() => setCopiedKey(null), 2000);
  };

  const toggleReveal = (key: string) => {
    setRevealedPasswords((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const handleUnlock = (e: React.FormEvent) => {
    e.preventDefault();
    const targetPin = config.superadminPin || '1234';
    if (pinInput === targetPin) {
      setIsUnlocked(true);
      setPinError('');
    } else {
      setPinError('PIN Superadmin salah! (Default PIN: 1234)');
    }
  };

  // Division CRUD actions
  const handleAddDivision = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newDivName.trim()) return;
    const res = await addDivision(newDivName.trim());
    setDivisions(res.divisions);
    setNewDivName('');
  };

  const handleSaveEditDivision = async () => {
    if (!editingDiv || !editingDiv.newName.trim()) return;
    const res = await updateDivision(editingDiv.oldName, editingDiv.newName.trim());
    setDivisions(res.divisions);
    setEditingDiv(null);
    onRefresh();
  };

  const handleConfirmDeleteDivision = async () => {
    if (!deleteDivTarget) return;
    const res = await deleteDivision(deleteDivTarget);
    setDivisions(res.divisions);
    setDeleteDivTarget(null);
  };

  // Record CRUD actions
  const handleConfirmDeleteRecord = async () => {
    if (!deleteTarget) return;
    if (deleteTarget.type === 'pc' || deleteTarget.type === 'akun') {
      await deleteSubmission(deleteTarget.id);
    } else if (deleteTarget.type === 'mikrotik') {
      deleteMikrotikAccount(deleteTarget.id);
    }
    setDeleteTarget(null);
    onRefresh();
  };

  // Filtered lists for each module
  const filteredPc = pcRecords.filter((r) => {
    const d = r.data as FormPC;
    if (filterDept !== 'all' && d.departemen !== filterDept) return false;
    if (!searchTerm.trim()) return true;
    const term = searchTerm.toLowerCase();
    return (
      (d.nama || '').toLowerCase().includes(term) ||
      (d.computerName || '').toLowerCase().includes(term) ||
      (d.processor || '').toLowerCase().includes(term) ||
      (d.departemen || '').toLowerCase().includes(term)
    );
  });

  const filteredAkun = akunRecords.filter((r) => {
    const d = r.data as FormAkun;
    if (filterDept !== 'all' && d.kategori !== filterDept) return false;
    if (!searchTerm.trim()) return true;
    const term = searchTerm.toLowerCase();
    return (
      (d.nama || '').toLowerCase().includes(term) ||
      (d.kategori || '').toLowerCase().includes(term) ||
      (d.jenisHp || '').toLowerCase().includes(term) ||
      (d.email1 || '').toLowerCase().includes(term) ||
      (d.noWa || '').toLowerCase().includes(term)
    );
  });

  const filteredMikrotik = mikrotikAccounts.filter((m) => {
    if (filterDept !== 'all' && m.tim !== filterDept) return false;
    if (!searchTerm.trim()) return true;
    const term = searchTerm.toLowerCase();
    return (
      (m.namaPemilik || '').toLowerCase().includes(term) ||
      (m.username || '').toLowerCase().includes(term) ||
      (m.tim || '').toLowerCase().includes(term)
    );
  });

  if (!isUnlocked) {
    return (
      <div className="bg-white rounded-3xl p-8 sm:p-12 text-center max-w-md mx-auto space-y-4 shadow-xl border border-slate-100">
        <div className="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center border border-amber-200">
          <KeyRound className="w-7 h-7" />
        </div>
        <div>
          <h2 className="font-extrabold text-slate-900 text-lg">Pusat Otoritas Superadmin</h2>
          <p className="text-xs text-slate-500 mt-1 leading-relaxed">
            Halaman ini memuat akses CRUD lengkap untuk seluruh modul (PC, HP, MikroTik, Akun Pengguna, dan Divisi/Tim). Masukkan PIN untuk membuka.
          </p>
        </div>

        <form onSubmit={handleUnlock} className="space-y-3">
          <input
            type="password"
            value={pinInput}
            onChange={(e) => setPinInput(e.target.value)}
            placeholder="PIN Superadmin (Default: 1234)..."
            className="w-full text-center tracking-widest text-lg font-bold py-2.5 px-4 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
            maxLength={8}
            autoFocus
          />
          {pinError && <p className="text-xs text-rose-600 font-semibold">{pinError}</p>}
          <button
            type="submit"
            className="w-full py-2.5 bg-gradient-to-r from-[#0F1D2E] to-[#3A7D8A] text-white font-bold text-xs rounded-xl shadow-md transition"
          >
            Buka Pusat Kontrol
          </button>
        </form>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Superadmin Header Banner */}
      <div className="bg-gradient-to-r from-[#0F1D2E] via-[#163654] to-[#2E5190] text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-white/10 relative overflow-hidden">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold border border-amber-400/30">
              <ShieldCheck className="w-4 h-4" />
              <span>👑 Pusat Kontrol &amp; Otoritas Penuh Superadmin</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              Manajemen CRUD Seluruh Modul &amp; Divisi
            </h1>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              Kelola penambahan, pembaruan, dan penghapusan data secara terpusat: Divisi &amp; Tim Perusahaan, Laptop/PC, Handphone &amp; Akun Sales, Hotspot MikroTik, serta Akun Pengguna Portal.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-2.5">
            <button
              onClick={onOpenPcForm}
              className="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 border border-white/20 transition shadow-xs"
            >
              <Plus className="w-3.5 h-3.5 text-teal-300" />
              <span>+ Input PC</span>
            </button>

            <button
              onClick={onOpenAkunForm}
              className="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 border border-white/20 transition shadow-xs"
            >
              <Plus className="w-3.5 h-3.5 text-amber-300" />
              <span>+ Input HP</span>
            </button>

            <button
              onClick={onOpenMikrotikForm}
              className="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 border border-white/20 transition shadow-xs"
            >
              <Plus className="w-3.5 h-3.5 text-blue-300" />
              <span>+ MikroTik</span>
            </button>

            <button
              onClick={onOpenSettings}
              className="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 transition shadow-sm"
            >
              <FileSpreadsheet className="w-3.5 h-3.5" />
              <span>Spreadsheet</span>
            </button>
          </div>
        </div>

        {/* Global Executive KPIs */}
        <div className="mt-6 pt-6 border-t border-white/10 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Total Laptop &amp; PC</span>
            <span className="text-2xl font-black text-white mt-1 block">{pcRecords.length} Unit</span>
            <span className="text-[10px] text-teal-300 mt-0.5 block">{totalLaptops} Laptop &bull; {totalPCDesktop} PC</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Total HP &amp; Akun Sales</span>
            <span className="text-2xl font-black text-white mt-1 block">{totalSmartphones} Unit</span>
            <span className="text-[10px] text-emerald-300 mt-0.5 block">Smartphone operasional</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Akun MikroTik Hotspot</span>
            <span className="text-2xl font-black text-white mt-1 block">{mikrotikAccounts.length} Akun</span>
            <span className="text-[10px] text-cyan-300 mt-0.5 block">Router sales hotspot</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Master Divisi &amp; Tim</span>
            <span className="text-2xl font-black text-amber-300 mt-1 block">{divisions.length} Tim</span>
            <span className="text-[10px] text-amber-200 mt-0.5 block">Terhubung ke semua form</span>
          </div>
        </div>
      </div>

      {/* Segmented Sub-Navbar for Superadmin Modules */}
      <div className="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-1.5 overflow-x-auto scrollbar-none">
        <button
          onClick={() => setActiveTab('divisions')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'divisions'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <Building2 className="w-3.5 h-3.5 text-amber-400" />
          <span>1. Kelola Divisi &amp; Tim</span>
          <span className="text-[10px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
            {divisions.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('pc')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'pc'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <Laptop className="w-3.5 h-3.5 text-teal-400" />
          <span>2. CRUD Laptop &amp; PC</span>
          <span className="text-[10px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
            {pcRecords.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('akun')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'akun'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <Smartphone className="w-3.5 h-3.5 text-blue-400" />
          <span>3. CRUD HP &amp; Akun Sales</span>
          <span className="text-[10px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
            {akunRecords.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('mikrotik')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'mikrotik'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <Wifi className="w-3.5 h-3.5 text-cyan-400" />
          <span>4. CRUD MikroTik Hotspot</span>
          <span className="text-[10px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
            {mikrotikAccounts.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('users')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'users'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <Users className="w-3.5 h-3.5 text-purple-400" />
          <span>5. Kelola Pengguna &amp; Role</span>
        </button>

        <button
          onClick={() => setActiveTab('analytics')}
          className={`px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shrink-0 ${
            activeTab === 'analytics'
              ? 'bg-[#0F1D2E] text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          }`}
        >
          <PieChart className="w-3.5 h-3.5 text-emerald-400" />
          <span>6. Audit &amp; Analitik</span>
        </button>
      </div>

      {/* ===================================================== */}
      {/* TAB 1: KELOLA MASTER DIVISI & TIM                     */}
      {/* ===================================================== */}
      {activeTab === 'divisions' && (
        <div className="space-y-6 animate-in fade-in duration-200">
          {/* Add Division Form */}
          <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <h3 className="font-extrabold text-sm text-[#0F1D2E] mb-1 flex items-center gap-2">
              <Building2 className="w-4 h-4 text-[#3A7D8A]" />
              <span>Tambah Divisi atau Tim Baru</span>
            </h3>
            <p className="text-xs text-slate-500 mb-4">
              Divisi atau tim yang ditambahkan di sini akan otomatis muncul sebagai opsi di seluruh formulir: Input PC, Input HP, MikroTik, dan filter pencarian.
            </p>

            <form onSubmit={handleAddDivision} className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
              <input
                type="text"
                required
                value={newDivName}
                onChange={(e) => setNewDivName(e.target.value)}
                placeholder="Ketik nama divisi/tim (misal: Tim Sales Online, Desain Grafis)..."
                className="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
              />
              <button
                type="submit"
                className="px-5 py-2.5 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2 shrink-0 active:scale-95"
              >
                <Plus className="w-4 h-4" />
                <span>+ Tambah ke Sistem</span>
              </button>
            </form>
          </div>

          {/* Division List Table */}
          <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div className="p-4 border-b border-slate-100 flex items-center justify-between">
              <div>
                <h4 className="font-bold text-sm text-slate-900">Daftar Divisi &amp; Tim Terdaftar ({divisions.length})</h4>
                <p className="text-[11px] text-slate-500">Alokasi aset dan akun di setiap divisi perusahaan</p>
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                    <th className="py-3 px-4">Nama Divisi / Tim</th>
                    <th className="py-3 px-4 text-center">Unit Laptop &amp; PC</th>
                    <th className="py-3 px-4 text-center">Unit HP &amp; Akun</th>
                    <th className="py-3 px-4 text-center">Akun MikroTik</th>
                    <th className="py-3 px-4 text-center">Total Aset</th>
                    <th className="py-3 px-4 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {divisions.map((divName) => {
                    const stats = getDivisionStats(divName);
                    const isEditingThis = editingDiv?.oldName === divName;

                    return (
                      <tr key={divName} className="hover:bg-slate-50/70 transition">
                        <td className="py-3 px-4">
                          {isEditingThis ? (
                            <div className="flex items-center gap-2">
                              <input
                                type="text"
                                value={editingDiv.newName}
                                onChange={(e) => setEditingDiv({ ...editingDiv, newName: e.target.value })}
                                className="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
                                autoFocus
                              />
                              <button
                                onClick={handleSaveEditDivision}
                                className="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold"
                              >
                                Simpan
                              </button>
                              <button
                                onClick={() => setEditingDiv(null)}
                                className="px-2 py-1 text-slate-500 hover:bg-slate-200 rounded-lg text-xs"
                              >
                                Batal
                              </button>
                            </div>
                          ) : (
                            <div className="flex items-center gap-2">
                              <span className="w-2 h-2 rounded-full bg-[#3A7D8A]" />
                              <span className="font-bold text-[#0F1D2E] text-sm">{divName}</span>
                            </div>
                          )}
                        </td>

                        <td className="py-3 px-4 text-center">
                          <span className="font-semibold text-slate-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200/50">
                            {stats.pcCount} unit
                          </span>
                        </td>

                        <td className="py-3 px-4 text-center">
                          <span className="font-semibold text-slate-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/50">
                            {stats.hpCount} unit
                          </span>
                        </td>

                        <td className="py-3 px-4 text-center">
                          <span className="font-semibold text-slate-700 bg-cyan-50 px-2 py-0.5 rounded-full border border-cyan-200/50">
                            {stats.mktCount} akun
                          </span>
                        </td>

                        <td className="py-3 px-4 text-center font-bold text-slate-900">
                          {stats.total}
                        </td>

                        <td className="py-3 px-4 text-right">
                          <div className="flex items-center justify-end gap-1.5">
                            <button
                              onClick={() => setEditingDiv({ oldName: divName, newName: divName })}
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition"
                              title="Edit Nama Divisi"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setDeleteDivTarget(divName)}
                              className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                              title="Hapus Divisi"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* ===================================================== */}
      {/* TAB 2: CRUD MODUL LAPTOP & PC                         */}
      {/* ===================================================== */}
      {activeTab === 'pc' && (
        <div className="space-y-4 animate-in fade-in duration-200">
          {/* Top Bar Filter */}
          <div className="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
              <div className="relative flex-1">
                <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  placeholder="Cari nama staf, host, prosesor, RAM..."
                  className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
                />
              </div>

              <select
                value={filterDept}
                onChange={(e) => setFilterDept(e.target.value)}
                className="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-700"
              >
                <option value="all">Semua Departemen ({pcRecords.length})</option>
                {divisions.map((d) => (
                  <option key={d} value={d}>
                    {d}
                  </option>
                ))}
              </select>
            </div>

            <button
              onClick={onOpenPcForm}
              className="px-4 py-2 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs rounded-xl shadow-xs flex items-center justify-center gap-1.5 shrink-0"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>+ Input Laptop / PC Baru</span>
            </button>
          </div>

          {/* PC Table */}
          <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                    <th className="py-3 px-4">Nama Staf</th>
                    <th className="py-3 px-4">Perangkat &amp; Merk</th>
                    <th className="py-3 px-4">Departemen / Jabatan</th>
                    <th className="py-3 px-4">Host &amp; IP</th>
                    <th className="py-3 px-4">Spesifikasi Inti</th>
                    <th className="py-3 px-4">Password Windows</th>
                    <th className="py-3 px-4 text-center">DxDiag</th>
                    <th className="py-3 px-4 text-right">Aksi Superadmin</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {filteredPc.map((r) => {
                    const d = r.data as FormPC;
                    const isRevealed = Boolean(revealedPasswords[`pc_${r.id}`]);

                    return (
                      <tr key={r.id} className="hover:bg-slate-50/70 transition">
                        <td className="py-3 px-4">
                          <span className="font-bold text-[#0F1D2E] block">{d.nama}</span>
                          <span className="text-[10px] text-slate-400">ID: #{r.id.slice(-4)}</span>
                        </td>

                        <td className="py-3 px-4">
                          <span className="font-semibold text-slate-800 block">{d.jenisPerangkat || 'Laptop'}</span>
                          <span className="text-[10px] text-slate-500">{d.manufacturer || 'OEM'}</span>
                        </td>

                        <td className="py-3 px-4">
                          <span className="font-semibold text-slate-800 block">{d.departemen}</span>
                          <span className="text-[10px] text-slate-400">{d.jabatan || 'Staf'}</span>
                        </td>

                        <td className="py-3 px-4 font-mono text-[11px]">
                          <span className="font-bold text-slate-900 block">{d.computerName}</span>
                          <span className="text-[10px] text-emerald-700">{d.ip || 'DHCP'}</span>
                        </td>

                        <td className="py-3 px-4">
                          <span className="font-semibold text-slate-800 block truncate max-w-[150px]" title={d.processor}>
                            {d.processor || '-'}
                          </span>
                          <span className="text-[10px] text-slate-500">{d.memory || '8GB RAM'} &bull; {d.os || 'Win 10'}</span>
                        </td>

                        <td className="py-3 px-4 font-mono">
                          <div className="flex items-center gap-1.5">
                            <span className="text-slate-800 font-bold">
                              {isRevealed ? d.passwordPc || 'Tanpa Password' : '••••••••'}
                            </span>
                            <button
                              type="button"
                              onClick={() => toggleReveal(`pc_${r.id}`)}
                              className="text-slate-400 hover:text-slate-700 p-0.5"
                            >
                              {isRevealed ? <EyeOff className="w-3 h-3" /> : <Eye className="w-3 h-3" />}
                            </button>
                            {d.passwordPc && (
                              <button
                                onClick={() => handleCopy(d.passwordPc!, `pc_cp_${r.id}`)}
                                className="text-slate-400 hover:text-slate-700 p-0.5"
                                title="Salin Password"
                              >
                                {copiedKey === `pc_cp_${r.id}` ? (
                                  <Check className="w-3 h-3 text-emerald-600" />
                                ) : (
                                  <Copy className="w-3 h-3" />
                                )}
                              </button>
                            )}
                          </div>
                        </td>

                        <td className="py-3 px-4 text-center">
                          {d.displayLink && d.displayLink !== '-' ? (
                            <a
                              href={d.displayLink}
                              target="_blank"
                              rel="noreferrer"
                              className="inline-flex items-center gap-1 text-[11px] font-semibold text-[#3A7D8A] hover:underline"
                            >
                              <span>Drive</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>

                        <td className="py-3 px-4 text-right">
                          <div className="flex items-center justify-end gap-1.5">
                            <button
                              onClick={() => setEditRecord(r)}
                              title="Edit Data Hardware (Superadmin)"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ id: r.id, name: `${d.nama} (${d.computerName})`, type: 'pc' })}
                              title="Hapus Data (Superadmin)"
                              className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* ===================================================== */}
      {/* TAB 3: CRUD MODUL HP & AKUN SALES                     */}
      {/* ===================================================== */}
      {activeTab === 'akun' && (
        <div className="space-y-4 animate-in fade-in duration-200">
          {/* Top Bar Filter */}
          <div className="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
              <div className="relative flex-1">
                <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  placeholder="Cari nama staf, handphone, email, IG, WA..."
                  className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
                />
              </div>

              <select
                value={filterDept}
                onChange={(e) => setFilterDept(e.target.value)}
                className="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-700"
              >
                <option value="all">Semua Tim ({akunRecords.length})</option>
                {divisions.map((d) => (
                  <option key={d} value={d}>
                    {d}
                  </option>
                ))}
              </select>
            </div>

            <button
              onClick={onOpenAkunForm}
              className="px-4 py-2 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs rounded-xl shadow-xs flex items-center justify-center gap-1.5 shrink-0"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>+ Input Smartphone &amp; Akun</span>
            </button>
          </div>

          {/* Akun Table */}
          <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                    <th className="py-3 px-4">Nama Staf &amp; Tim</th>
                    <th className="py-3 px-4">Unit HP &amp; Status</th>
                    <th className="py-3 px-4">WhatsApp</th>
                    <th className="py-3 px-4">Email 1 &amp; Password</th>
                    <th className="py-3 px-4">Akun Instagram</th>
                    <th className="py-3 px-4 text-right">Aksi Superadmin</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {filteredAkun.map((r) => {
                    const d = r.data as FormAkun;
                    const isRevEm = Boolean(revealedPasswords[`em_${r.id}`]);

                    return (
                      <tr key={r.id} className="hover:bg-slate-50/70 transition">
                        <td className="py-3 px-4">
                          <span className="font-bold text-[#0F1D2E] block">{d.nama}</span>
                          <span className="text-[11px] text-slate-500">{d.kategori}</span>
                        </td>

                        <td className="py-3 px-4">
                          <span className="font-semibold text-slate-800 block">{d.jenisHp}</span>
                          <span className="text-[10px] text-teal-700 font-semibold">{d.bekasHp || 'Baru'}</span>
                        </td>

                        <td className="py-3 px-4 font-mono text-emerald-700 font-semibold">
                          {d.noWa || '-'}
                        </td>

                        <td className="py-3 px-4 font-mono">
                          {d.email1 ? (
                            <div>
                              <div className="flex items-center gap-1">
                                <span className="font-semibold text-slate-800 truncate max-w-[160px]">{d.email1}</span>
                                <button
                                  onClick={() => handleCopy(d.email1!, `em_c_${r.id}`)}
                                  className="text-slate-400 hover:text-slate-600"
                                >
                                  {copiedKey === `em_c_${r.id}` ? <Check className="w-3 h-3 text-emerald-600" /> : <Copy className="w-3 h-3" />}
                                </button>
                              </div>
                              {d.passEmail1 && (
                                <div className="flex items-center gap-1 text-[11px] text-slate-500">
                                  <span>Pass: {isRevEm ? d.passEmail1 : '••••••••'}</span>
                                  <button onClick={() => toggleReveal(`em_${r.id}`)}>
                                    {isRevEm ? <EyeOff className="w-3 h-3 text-slate-400" /> : <Eye className="w-3 h-3 text-slate-400" />}
                                  </button>
                                </div>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>

                        <td className="py-3 px-4">
                          {d.ig1 ? (
                            <div>
                              <span className="font-semibold text-pink-700">@{d.ig1}</span>
                              {d.passIg1 && (
                                <span className="text-[10px] text-slate-400 block font-mono">
                                  Pass: {isRevEm ? d.passIg1 : '••••••••'}
                                </span>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>

                        <td className="py-3 px-4 text-right">
                          <div className="flex items-center justify-end gap-1.5">
                            <button
                              onClick={() => setEditRecord(r)}
                              title="Edit Data Smartphone & Akun"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ id: r.id, name: `${d.nama} (${d.jenisHp})`, type: 'akun' })}
                              title="Hapus Data (Superadmin)"
                              className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* ===================================================== */}
      {/* TAB 4: CRUD MODUL MIKROTIK HOTSPOT                    */}
      {/* ===================================================== */}
      {activeTab === 'mikrotik' && (
        <div className="space-y-4 animate-in fade-in duration-200">
          <div className="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div className="relative flex-1">
              <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder="Cari akun MikroTik (nama, username, tim)..."
                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
              />
            </div>

            <button
              onClick={onOpenMikrotikForm}
              className="px-4 py-2 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs rounded-xl shadow-xs flex items-center justify-center gap-1.5 shrink-0"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>+ Generate Akun MikroTik Baru</span>
            </button>
          </div>

          <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                    <th className="py-3 px-4">Nama Staf &amp; Jabatan</th>
                    <th className="py-3 px-4">Tim / Divisi</th>
                    <th className="py-3 px-4">Username</th>
                    <th className="py-3 px-4">Password</th>
                    <th className="py-3 px-4">Profile Hotspot</th>
                    <th className="py-3 px-4 text-center">Status</th>
                    <th className="py-3 px-4 text-right">Aksi Superadmin</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {filteredMikrotik.map((m) => {
                    return (
                      <tr key={m.id} className="hover:bg-slate-50/70 transition">
                        <td className="py-3 px-4">
                          <span className="font-bold text-[#0F1D2E] block">{m.namaPemilik}</span>
                          <span className="text-[10px] text-slate-400">{m.jabatan || 'Staf'}</span>
                        </td>

                        <td className="py-3 px-4 font-semibold text-slate-800">
                          {m.tim}
                        </td>

                        <td className="py-3 px-4 font-mono font-bold text-[#2E5190]">
                          {m.username}
                        </td>

                        <td className="py-3 px-4 font-mono text-slate-700 font-bold">
                          {m.password}
                        </td>

                        <td className="py-3 px-4 text-slate-700">
                          <span className="bg-slate-100 px-2 py-0.5 rounded font-mono text-[11px]">
                            {m.profileHotspot}
                          </span>
                        </td>

                        <td className="py-3 px-4 text-center">
                          <span
                            className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                              m.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'
                            }`}
                          >
                            {m.status || 'aktif'}
                          </span>
                        </td>

                        <td className="py-3 px-4 text-right">
                          <div className="flex items-center justify-end gap-1.5">
                            <button
                              onClick={() => handleCopy(m.commandTerminal, `cmd_${m.id}`)}
                              title="Salin Command Terminal MikroTik"
                              className="p-1.5 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition"
                            >
                              {copiedKey === `cmd_${m.id}` ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Terminal className="w-3.5 h-3.5" />}
                            </button>
                            <button
                              onClick={() => setEditMikrotik(m)}
                              title="Edit Akun MikroTik"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ id: m.id, name: `${m.namaPemilik} (${m.username})`, type: 'mikrotik' })}
                              title="Hapus Akun MikroTik"
                              className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* ===================================================== */}
      {/* TAB 5: KELOLA PENGGUNA & ROLE                         */}
      {/* ===================================================== */}
      {activeTab === 'users' && (
        <div className="animate-in fade-in duration-200">
          <UserManagement currentUser={currentUser} />
        </div>
      )}

      {/* ===================================================== */}
      {/* TAB 6: AUDIT & ANALITIK EKSEKUTIF                    */}
      {/* ===================================================== */}
      {activeTab === 'analytics' && (
        <div className="space-y-6 animate-in fade-in duration-200">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Department Breakdown */}
            <div className="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
              <h3 className="font-extrabold text-sm text-slate-900 mb-4 flex items-center gap-2">
                <Building2 className="w-4 h-4 text-[#3A7D8A]" />
                <span>Distribusi Aset Komputer per Divisi</span>
              </h3>
              <div className="space-y-3">
                {divisions.map((divName) => {
                  const stats = getDivisionStats(divName);
                  const pct = Math.round((stats.pcCount / (pcRecords.length || 1)) * 100);
                  return (
                    <div key={divName} className="space-y-1">
                      <div className="flex justify-between text-xs font-semibold">
                        <span className="text-slate-700">{divName}</span>
                        <span className="text-slate-900">{stats.pcCount} unit ({pct}%)</span>
                      </div>
                      <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div className="h-full bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] rounded-full" style={{ width: `${pct}%` }} />
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* RAM Specs */}
            <div className="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
              <h3 className="font-extrabold text-sm text-slate-900 mb-4 flex items-center gap-2">
                <Cpu className="w-4 h-4 text-[#2E5190]" />
                <span>Audit Kapasitas RAM Workstation</span>
              </h3>
              <div className="space-y-2.5">
                {['16 GB', '8 GB', '4 GB', '32 GB'].map((ramLabel) => {
                  const count = pcRecords.filter((r) => {
                    const mem = ((r.data as FormPC).memory || '').toUpperCase();
                    return mem.includes(ramLabel.split(' ')[0]);
                  }).length;
                  const pct = Math.round((count / (pcRecords.length || 1)) * 100);

                  return (
                    <div key={ramLabel} className="space-y-1">
                      <div className="flex justify-between text-xs font-semibold">
                        <span className="text-slate-700">{ramLabel} RAM</span>
                        <span className="text-slate-900">{count} unit ({pct}%)</span>
                      </div>
                      <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div className="h-full bg-emerald-600 rounded-full" style={{ width: `${pct}%` }} />
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* MODALS */}
      {/* Delete Record Confirmation Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        itemName={deleteTarget?.name || ''}
        itemType={`data ${deleteTarget?.type === 'pc' ? 'Laptop/PC' : deleteTarget?.type === 'akun' ? 'HP Sales' : 'MikroTik'}`}
        onConfirm={handleConfirmDeleteRecord}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Delete Division Confirmation Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteDivTarget)}
        itemName={deleteDivTarget || ''}
        itemType="divisi / tim resmi"
        onConfirm={handleConfirmDeleteDivision}
        onCancel={() => setDeleteDivTarget(null)}
      />

      {/* Edit Record Modal (PC / Akun) */}
      <EditRecordModal
        isOpen={Boolean(editRecord)}
        record={editRecord}
        onClose={() => setEditRecord(null)}
        onSaveSuccess={() => {
          onRefresh();
          setEditRecord(null);
        }}
      />

      {/* Edit MikroTik Modal */}
      <EditMikrotikModal
        isOpen={Boolean(editMikrotik)}
        account={editMikrotik}
        onClose={() => setEditMikrotik(null)}
        onSaveSuccess={() => {
          onRefresh();
          setEditMikrotik(null);
        }}
      />
    </div>
  );
};
