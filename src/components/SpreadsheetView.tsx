import React, { useState, useMemo } from 'react';
import {
  FileSpreadsheet,
  Search,
  Filter,
  Download,
  Trash2,
  RefreshCw,
  ExternalLink,
  Eye,
  PlusCircle,
  Pencil,
  Check,
  Copy,
  Laptop,
  Smartphone,
  Wifi,
  ChevronLeft,
  ChevronRight,
  ShieldAlert,
  ArrowUpDown,
  PhoneCall,
  Link2
} from 'lucide-react';
import { SubmissionRecord, FormPC, FormAkun, MikrotikAccount } from '../types';
import {
  deleteSubmission,
  pullDataFromGoogleSpreadsheet,
  clearCategorySubmissions,
  wipeCompleteDatabase
} from '../services/gasService';
import { clearAllFirestoreMikrotik } from '../services/firestoreService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';
import { EditMikrotikModal } from './EditMikrotikModal';
import { ClearAllModal } from './ClearAllModal';

interface SpreadsheetViewProps {
  records: SubmissionRecord[];
  mikrotikAccounts: MikrotikAccount[];
  onRefresh: () => Promise<void> | void;
  onOpenPcForm: () => void;
  onOpenAkunForm: () => void;
  onOpenMikrotikForm: () => void;
  onOpenSettings: () => void;
  onOpenImportModal: () => void;
  initialSheet?: 'pc' | 'akun' | 'mikrotik';
}

export const SpreadsheetView: React.FC<SpreadsheetViewProps> = ({
  records,
  mikrotikAccounts,
  onRefresh,
  onOpenPcForm,
  onOpenAkunForm,
  onOpenMikrotikForm,
  onOpenSettings,
  onOpenImportModal,
  initialSheet = 'pc'
}) => {
  const [activeSheet, setActiveSheet] = useState<'pc' | 'akun' | 'mikrotik'>(initialSheet);
  const [searchTerm, setSearchTerm] = useState('');
  const [teamFilter, setTeamFilter] = useState('all');
  const [isPulling, setIsPulling] = useState(false);
  const [pullMessage, setPullMessage] = useState<string | null>(null);

  // Pagination
  const [page, setPage] = useState(1);
  const pageSize = 25;

  // Modals
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [editMikrotik, setEditMikrotik] = useState<MikrotikAccount | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string; type: 'record' | 'mikrotik' } | null>(null);
  const [isClearModalOpen, setIsClearModalOpen] = useState(false);

  const pcRecords = useMemo(() => records.filter((r) => r.type === 'pc'), [records]);
  const akunRecords = useMemo(() => records.filter((r) => r.type === 'akun'), [records]);

  // Extract distinct departments / teams for filter
  const teams = useMemo(() => {
    const set = new Set<string>();
    if (activeSheet === 'pc') {
      pcRecords.forEach((r) => {
        const d = (r.data as FormPC).departemen;
        if (d) set.add(d);
      });
    } else if (activeSheet === 'akun') {
      akunRecords.forEach((r) => {
        const k = (r.data as FormAkun).kategori;
        if (k) set.add(k);
      });
    } else {
      mikrotikAccounts.forEach((m) => {
        if (m.tim) set.add(m.tim);
      });
    }
    return Array.from(set).sort();
  }, [activeSheet, pcRecords, akunRecords, mikrotikAccounts]);

  // Filtered rows
  const filteredData = useMemo(() => {
    const term = searchTerm.toLowerCase().trim();

    if (activeSheet === 'pc') {
      return pcRecords.filter((r) => {
        const d = r.data as FormPC;
        if (teamFilter !== 'all' && d.departemen !== teamFilter) return false;
        if (!term) return true;
        return (
          (d.nama || '').toLowerCase().includes(term) ||
          (d.departemen || '').toLowerCase().includes(term) ||
          (d.jabatan || '').toLowerCase().includes(term) ||
          (d.computerName || '').toLowerCase().includes(term) ||
          (d.ip || '').toLowerCase().includes(term) ||
          (d.processor || '').toLowerCase().includes(term)
        );
      });
    } else if (activeSheet === 'akun') {
      return akunRecords.filter((r) => {
        const d = r.data as FormAkun;
        if (teamFilter !== 'all' && d.kategori !== teamFilter) return false;
        if (!term) return true;
        return (
          (d.nama || '').toLowerCase().includes(term) ||
          (d.kategori || '').toLowerCase().includes(term) ||
          (d.jenisHp || '').toLowerCase().includes(term) ||
          (d.noWa || '').toLowerCase().includes(term) ||
          (d.email1 || '').toLowerCase().includes(term) ||
          (d.ig1 || '').toLowerCase().includes(term)
        );
      });
    } else {
      return mikrotikAccounts.filter((m) => {
        if (teamFilter !== 'all' && m.tim !== teamFilter) return false;
        if (!term) return true;
        return (
          (m.namaPemilik || '').toLowerCase().includes(term) ||
          (m.username || '').toLowerCase().includes(term) ||
          (m.tim || '').toLowerCase().includes(term) ||
          (m.profileHotspot || '').toLowerCase().includes(term)
        );
      });
    }
  }, [activeSheet, pcRecords, akunRecords, mikrotikAccounts, searchTerm, teamFilter]);

  // Paginated data
  const totalPages = Math.ceil(filteredData.length / pageSize) || 1;
  const currentPageData = useMemo(() => {
    const start = (page - 1) * pageSize;
    return filteredData.slice(start, start + pageSize);
  }, [filteredData, page, pageSize]);

  // Handle Pull from Google Spreadsheet
  const handlePullFromSheet = async () => {
    setIsPulling(true);
    setPullMessage(null);
    try {
      const res = await pullDataFromGoogleSpreadsheet();
      await onRefresh();
      setPullMessage(res.message);
      setTimeout(() => setPullMessage(null), 5000);
    } catch (err: unknown) {
      const e = err as Error;
      setPullMessage('Gagal: ' + (e?.message || String(err)));
    } finally {
      setIsPulling(false);
    }
  };

  // Export CSV
  const handleExportCsv = () => {
    let headers: string[] = [];
    let rows: string[][] = [];
    let filename = 'syamanah_spreadsheet.csv';

    if (activeSheet === 'pc') {
      filename = 'Catatan_Spesifikasi_Perangkat_PC_Syamanah.csv';
      headers = [
        'Timestamp',
        'Nama',
        'Departemen',
        'Jabatan',
        'Computer Name',
        'IP',
        'Password PC',
        'Operating System',
        'System Manufacturer',
        'Processor',
        'Memory',
        'Display',
        'Sound',
        'Input'
      ];
      rows = pcRecords.map((r) => {
        const d = r.data as FormPC;
        return [
          r.timestamp || '',
          d.nama || '',
          d.departemen || '',
          d.jabatan || '',
          d.computerName || '',
          d.ip || '',
          d.passwordPc || '',
          d.os || '',
          d.manufacturer || '',
          d.processor || '',
          d.memory || '',
          d.displayLink || '',
          d.soundLink || '',
          d.inputLink || ''
        ];
      });
    } else if (activeSheet === 'akun') {
      filename = 'Form_Pencatatan_Akun_Sales_Syamanah_2026.csv';
      headers = [
        'Timestamp',
        'Nama',
        'Kategori Tim/Role',
        'Jenis Handphone',
        'Bekas Handphone Siapa',
        'Akun Login PC',
        'Email 1',
        'Password Email 1',
        'Email 2',
        'Password Email 2',
        'Email 3',
        'Password Email 3',
        'Username IG 1',
        'Password IG 1',
        'Username IG 2',
        'Password IG 2',
        'Username IG 3',
        'Password IG 3',
        'Facebook 1',
        'Password FB 1',
        'Facebook 2',
        'Password FB 2',
        'Facebook 3',
        'Password FB 3',
        'No Telpon / WhatsApp',
        'Akun Lain Terhubung',
        'Software Lain',
        'Catatan',
        'Jabatan'
      ];
      rows = akunRecords.map((r) => {
        const d = r.data as FormAkun;
        return [
          r.timestamp || '',
          d.nama || '',
          d.kategori || '',
          d.jenisHp || '',
          d.bekasHp || '',
          d.akunLoginPc || '',
          d.email1 || '',
          d.passEmail1 || '',
          d.email2 || '',
          d.passEmail2 || '',
          d.email3 || '',
          d.passEmail3 || '',
          d.ig1 || '',
          d.passIg1 || '',
          d.ig2 || '',
          d.passIg2 || '',
          d.ig3 || '',
          d.passIg3 || '',
          d.fb1 || '',
          d.passFb1 || '',
          d.fb2 || '',
          d.passFb2 || '',
          d.fb3 || '',
          d.passFb3 || '',
          d.noWa || '',
          d.akunLain || '',
          d.softwareLain || '',
          d.catatan || '',
          d.jabatan || ''
        ];
      });
    } else {
      filename = 'Akun_MikroTik_Hotspot_Sales.csv';
      headers = ['Timestamp', 'Nama Sales', 'Tim', 'Jabatan', 'Username', 'Password', 'Profile Hotspot', 'Rate Limit', 'Status'];
      rows = mikrotikAccounts.map((m) => [
        m.createdAt || '',
        m.namaPemilik || '',
        m.tim || '',
        m.jabatan || '',
        m.username || '',
        m.password || '',
        m.profileHotspot || '',
        m.rateLimit || '',
        m.status || 'aktif'
      ]);
    }

    const csvText =
      '\uFEFF' +
      [headers.join(','), ...rows.map((e) => e.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(','))].join('\r\n');
    const blob = new Blob([csvText], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.style.display = 'none';
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }, 500);
  };

  // Confirm delete
  const confirmDelete = async () => {
    if (!deleteTarget) return;
    if (deleteTarget.type === 'record') {
      await deleteSubmission(deleteTarget.id);
    } else {
      try {
        await fetch(`/api/mikrotik/${deleteTarget.id}`, { method: 'DELETE' });
      } catch (err) {
        console.error(err);
      }
    }
    await onRefresh();
    setDeleteTarget(null);
  };

  // Confirm clear all
  const handleConfirmClearAll = async (scope: 'current' | 'all') => {
    if (scope === 'all') {
      await wipeCompleteDatabase();
    } else {
      if (activeSheet === 'pc' || activeSheet === 'akun') {
        await clearCategorySubmissions(activeSheet);
      } else {
        // Clear all MikroTik
        await clearAllFirestoreMikrotik();
        try {
          await fetch('/api/mikrotik/all', { method: 'DELETE' });
        } catch (err) {
          console.error(err);
        }
      }
    }
    await onRefresh();
  };

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
        <div>
          <h1 className="text-xl font-bold text-slate-900 tracking-tight">
            Data Inventaris &amp; Akun
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Database perangkat PC, handphone sales, dan akun MikroTik Syamanah Garment
          </p>
        </div>

        <div className="flex items-center gap-2 self-start sm:self-auto flex-wrap">
          <button
            onClick={onOpenImportModal}
            className="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            title="Impor atau Ekspor berkas Excel (.xlsx) / CSV lengkap"
          >
            <FileSpreadsheet className="w-3.5 h-3.5 text-emerald-700" />
            <span>Ekspor / Impor Excel</span>
          </button>

          <button
            onClick={onOpenSettings}
            className="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            title="Ganti tautan atau ID Google Spreadsheet baru"
          >
            <Link2 className="w-3.5 h-3.5 text-amber-700" />
            <span>Ganti Spreadsheet</span>
          </button>

          <button
            onClick={handlePullFromSheet}
            disabled={isPulling}
            className="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs disabled:opacity-50"
            title="Tarik data terbaru dari Google Spreadsheet"
          >
            <RefreshCw className={`w-3.5 h-3.5 text-slate-500 ${isPulling ? 'animate-spin' : ''}`} />
            <span>{isPulling ? 'Menyinkronkan...' : 'Sinkronkan'}</span>
          </button>

          <button
            onClick={handleExportCsv}
            className="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            title="Download berkas CSV"
          >
            <Download className="w-3.5 h-3.5 text-slate-500" />
            <span>Unduh CSV</span>
          </button>

          <button
            onClick={() => setIsClearModalOpen(true)}
            className="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            title="Hapus semua data pada tabel atau seluruh database"
          >
            <Trash2 className="w-3.5 h-3.5 text-rose-600" />
            <span>Hapus Semua</span>
          </button>

          {activeSheet === 'pc' && (
            <button
              onClick={onOpenPcForm}
              className="px-3.5 py-2 bg-[#0B1B3D] hover:bg-[#132A5E] text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            >
              <PlusCircle className="w-3.5 h-3.5 text-amber-400" />
              <span>Tambah PC</span>
            </button>
          )}

          {activeSheet === 'akun' && (
            <button
              onClick={onOpenAkunForm}
              className="px-3.5 py-2 bg-[#0B1B3D] hover:bg-[#132A5E] text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            >
              <PlusCircle className="w-3.5 h-3.5 text-amber-400" />
              <span>Tambah Akun</span>
            </button>
          )}

          {activeSheet === 'mikrotik' && (
            <button
              onClick={onOpenMikrotikForm}
              className="px-3.5 py-2 bg-[#0B1B3D] hover:bg-[#132A5E] text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            >
              <PlusCircle className="w-3.5 h-3.5 text-amber-400" />
              <span>Tambah MikroTik</span>
            </button>
          )}
        </div>
      </div>

      {pullMessage && (
        <div className="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center gap-2">
          <Check className="w-4 h-4 text-emerald-600 shrink-0" />
          <span>{pullMessage}</span>
        </div>
      )}

      {/* Clean Category Tabs */}
      <div className="border-b border-slate-200 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none">
        <div className="flex items-center space-x-1 sm:space-x-2">
          <button
            onClick={() => {
              setActiveSheet('pc');
              setPage(1);
            }}
            className={`px-3 py-2.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition shrink-0 ${
              activeSheet === 'pc'
                ? 'border-amber-500 text-slate-900'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            <Laptop className="w-4 h-4 text-blue-600" />
            <span>Spesifikasi Laptop &amp; PC</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-bold ${
                activeSheet === 'pc' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600'
              }`}
            >
              {pcRecords.length}
            </span>
          </button>

          <button
            onClick={() => {
              setActiveSheet('akun');
              setPage(1);
            }}
            className={`px-3 py-2.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition shrink-0 ${
              activeSheet === 'akun'
                ? 'border-amber-500 text-slate-900'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            <Smartphone className="w-4 h-4 text-amber-600" />
            <span>Akun Sales 2026</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-bold ${
                activeSheet === 'akun' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600'
              }`}
            >
              {akunRecords.length}
            </span>
          </button>

          <button
            onClick={() => {
              setActiveSheet('mikrotik');
              setPage(1);
            }}
            className={`px-3 py-2.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition shrink-0 ${
              activeSheet === 'mikrotik'
                ? 'border-amber-500 text-slate-900'
                : 'border-transparent text-slate-500 hover:text-slate-800'
            }`}
          >
            <Wifi className="w-4 h-4 text-teal-600" />
            <span>Akun MikroTik</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-bold ${
                activeSheet === 'mikrotik' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600'
              }`}
            >
              {mikrotikAccounts.length}
            </span>
          </button>
        </div>

        <div className="text-xs text-slate-400 font-medium px-1 hidden md:block shrink-0">
          Total {records.length + mikrotikAccounts.length} data
        </div>
      </div>

      {/* Controls: Search, Team Filter, and Row Counter */}
      <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <div className="flex items-center gap-3 w-full sm:w-auto flex-1 max-w-lg">
          <div className="relative flex-1">
            <Search className="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                setPage(1);
              }}
              placeholder={
                activeSheet === 'pc'
                  ? 'Cari nama, computer name, IP, processor...'
                  : activeSheet === 'akun'
                  ? 'Cari nama, nomor WA, kategori tim, email, jenis HP...'
                  : 'Cari username mikrotik, nama sales...'
              }
              className="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500"
            />
          </div>

          {teams.length > 0 && (
            <div className="shrink-0">
              <select
                value={teamFilter}
                onChange={(e) => {
                  setTeamFilter(e.target.value);
                  setPage(1);
                }}
                className="py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-700 font-semibold focus:outline-hidden focus:ring-2 focus:ring-blue-500"
              >
                <option value="all">Semua Tim / Divisi ({filteredData.length})</option>
                {teams.map((t) => (
                  <option key={t} value={t}>
                    {t}
                  </option>
                ))}
              </select>
            </div>
          )}
        </div>

        <div className="text-xs text-slate-500 font-medium flex items-center gap-3">
          <span>
            Menampilkan <b>{currentPageData.length}</b> dari <b>{filteredData.length}</b> baris
          </span>
          {searchTerm && (
            <button
              onClick={() => {
                setSearchTerm('');
                setTeamFilter('all');
              }}
              className="text-rose-600 hover:underline font-bold"
            >
              Reset Filter
            </button>
          )}
        </div>
      </div>

      {/* =========================================================================
          TABLE 1: Catatan Spesifikasi Perangkat PC Syamanah (14 Columns 1:1)
         ========================================================================= */}
      {activeSheet === 'pc' && (
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs text-slate-700 divide-y divide-slate-200 font-sans">
              <thead className="bg-[#0B1B3D] text-slate-200 uppercase tracking-wider text-[11px] font-bold sticky top-0 z-10">
                <tr>
                  <th className="px-3 py-3 text-center w-12 border-r border-[#1C2C54]">No</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Timestamp</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54] text-amber-300">Nama</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Departemen</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Jabatan</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54] text-blue-300 font-mono">Computer Name</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54] font-mono">IP</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54]">Password PC</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Operating System</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Manufacturer</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Processor</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54]">Memory</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54] text-center">Display</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54] text-center">Sound</th>
                  <th className="px-3 py-3 border-r border-[#1C2C54] text-center">Input</th>
                  <th className="px-3 py-3 text-center sticky right-0 bg-[#0B1B3D] shadow-l">Aksi CRUD</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 bg-white">
                {currentPageData.length === 0 ? (
                  <tr>
                    <td colSpan={16} className="px-6 py-12 text-center text-slate-400">
                      Tidak ada data yang cocok dengan pencarian.
                    </td>
                  </tr>
                ) : (
                  currentPageData.map((recItem, idx) => {
                    const rec = recItem as SubmissionRecord;
                    const d = rec.data as FormPC;
                    const rowNum = (page - 1) * pageSize + idx + 1;

                    return (
                      <tr key={rec.id} className="hover:bg-slate-50 transition-colors">
                        <td className="px-3 py-3 text-center font-bold text-slate-400 border-r border-slate-100">
                          {rowNum}
                        </td>
                        <td className="px-3.5 py-3 text-slate-500 whitespace-nowrap border-r border-slate-100 font-mono text-[11px]">
                          {rec.timestamp}
                        </td>
                        <td className="px-4 py-3 font-bold text-slate-900 border-r border-slate-100 whitespace-nowrap">
                          {d.nama || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-700 border-r border-slate-100 whitespace-nowrap">
                          {d.departemen || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-600 border-r border-slate-100 whitespace-nowrap">
                          {d.jabatan || '-'}
                        </td>
                        <td className="px-3.5 py-3 font-mono font-bold text-blue-700 border-r border-slate-100 whitespace-nowrap">
                          {d.computerName || '-'}
                        </td>
                        <td className="px-3 py-3 font-mono text-slate-600 border-r border-slate-100 whitespace-nowrap">
                          {d.ip || '-'}
                        </td>
                        <td className="px-3 py-3 text-slate-500 border-r border-slate-100">
                          {d.passwordPc || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-700 border-r border-slate-100 whitespace-nowrap">
                          {d.os || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-600 border-r border-slate-100 whitespace-nowrap">
                          {d.manufacturer || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-800 border-r border-slate-100 whitespace-nowrap">
                          {d.processor || '-'}
                        </td>
                        <td className="px-3 py-3 font-semibold text-slate-700 border-r border-slate-100 whitespace-nowrap">
                          {d.memory || '-'}
                        </td>
                        <td className="px-3 py-3 text-center border-r border-slate-100 whitespace-nowrap">
                          {d.displayLink && d.displayLink !== '-' ? (
                            <a
                              href={d.displayLink}
                              target="_blank"
                              rel="noreferrer"
                              className="text-blue-600 hover:text-blue-800 underline font-semibold text-[11px] inline-flex items-center gap-1"
                            >
                              <span>Drive</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-3 py-3 text-center border-r border-slate-100 whitespace-nowrap">
                          {d.soundLink && d.soundLink !== '-' ? (
                            <a
                              href={d.soundLink}
                              target="_blank"
                              rel="noreferrer"
                              className="text-blue-600 hover:text-blue-800 underline font-semibold text-[11px] inline-flex items-center gap-1"
                            >
                              <span>Drive</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-3 py-3 text-center border-r border-slate-100 whitespace-nowrap">
                          {d.inputLink && d.inputLink !== '-' ? (
                            <a
                              href={d.inputLink}
                              target="_blank"
                              rel="noreferrer"
                              className="text-blue-600 hover:text-blue-800 underline font-semibold text-[11px] inline-flex items-center gap-1"
                            >
                              <span>Drive</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-3 py-3 text-center sticky right-0 bg-white/95 backdrop-blur-xs shadow-l whitespace-nowrap">
                          <div className="flex items-center justify-center gap-1.5">
                            <button
                              onClick={() => setSelectedRecord(rec)}
                              title="Lihat Detail"
                              className="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                            >
                              <Eye className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setEditRecord(rec)}
                              title="Edit Data"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() =>
                                setDeleteTarget({
                                  id: rec.id,
                                  name: d.computerName || d.nama,
                                  type: 'record'
                                })
                              }
                              title="Hapus Baris"
                              className="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
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
      )}

      {/* =========================================================================
          TABLE 2: Form Pencatatan Akun Sales Syamanah 2026 (29 Columns 1:1)
         ========================================================================= */}
      {activeSheet === 'akun' && (
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs text-slate-700 divide-y divide-slate-200 font-sans">
              <thead className="bg-[#0B1B3D] text-slate-200 uppercase tracking-wider text-[11px] font-bold sticky top-0 z-10">
                <tr>
                  <th className="px-3 py-3 text-center w-12 border-r border-[#1C2C54]">No</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Timestamp</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54] text-amber-300">Nama</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Kategori Tim/Role</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54] text-blue-300">Jenis Handphone</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Bekas HP Siapa?</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">No WA / Telepon</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54]">Email 1 &amp; Password</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54]">Email 2 &amp; Password</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54]">Instagram 1</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54]">Facebook 1</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Akun Login di PC</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Akun Lain Terhubung</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Catatan</th>
                  <th className="px-3 py-3 text-center sticky right-0 bg-[#0B1B3D] shadow-l">Aksi CRUD</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 bg-white">
                {currentPageData.length === 0 ? (
                  <tr>
                    <td colSpan={15} className="px-6 py-12 text-center text-slate-400">
                      Tidak ada data yang cocok dengan pencarian.
                    </td>
                  </tr>
                ) : (
                  currentPageData.map((recItem, idx) => {
                    const rec = recItem as SubmissionRecord;
                    const d = rec.data as FormAkun;
                    const rowNum = (page - 1) * pageSize + idx + 1;

                    return (
                      <tr key={rec.id} className="hover:bg-slate-50 transition-colors">
                        <td className="px-3 py-3 text-center font-bold text-slate-400 border-r border-slate-100">
                          {rowNum}
                        </td>
                        <td className="px-3.5 py-3 text-slate-500 whitespace-nowrap border-r border-slate-100 font-mono text-[11px]">
                          {rec.timestamp}
                        </td>
                        <td className="px-4 py-3 font-bold text-slate-900 border-r border-slate-100 whitespace-nowrap">
                          {d.nama || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-700 border-r border-slate-100 whitespace-nowrap font-medium">
                          {d.kategori || '-'}
                        </td>
                        <td className="px-3.5 py-3 font-semibold text-amber-700 border-r border-slate-100 whitespace-nowrap">
                          {d.jenisHp || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-600 border-r border-slate-100 whitespace-nowrap">
                          {d.bekasHp || '-'}
                        </td>
                        <td className="px-3.5 py-3 border-r border-slate-100 whitespace-nowrap">
                          {d.noWa ? (
                            <a
                              href={`https://wa.me/${d.noWa.replace(/\D/g, '').replace(/^0/, '62')}`}
                              target="_blank"
                              rel="noreferrer"
                              className="font-mono text-emerald-700 hover:text-emerald-900 font-bold inline-flex items-center gap-1"
                            >
                              <PhoneCall className="w-3 h-3" />
                              <span>{d.noWa}</span>
                            </a>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-4 py-3 border-r border-slate-100 whitespace-nowrap">
                          {d.email1 ? (
                            <div>
                              <div className="font-semibold text-slate-800">{d.email1}</div>
                              {d.passEmail1 && (
                                <div className="text-[10px] text-slate-400 font-mono">
                                  pass: {d.passEmail1}
                                </div>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-4 py-3 border-r border-slate-100 whitespace-nowrap">
                          {d.email2 ? (
                            <div>
                              <div className="font-semibold text-slate-800">{d.email2}</div>
                              {d.passEmail2 && (
                                <div className="text-[10px] text-slate-400 font-mono">
                                  pass: {d.passEmail2}
                                </div>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-4 py-3 border-r border-slate-100 whitespace-nowrap">
                          {d.ig1 ? (
                            <div>
                              <div className="font-semibold text-pink-700">@{d.ig1}</div>
                              {d.passIg1 && (
                                <div className="text-[10px] text-slate-400 font-mono">
                                  pass: {d.passIg1}
                                </div>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-4 py-3 border-r border-slate-100 whitespace-nowrap">
                          {d.fb1 ? (
                            <div>
                              <div className="font-semibold text-blue-700">{d.fb1}</div>
                              {d.passFb1 && (
                                <div className="text-[10px] text-slate-400 font-mono">
                                  pass: {d.passFb1}
                                </div>
                              )}
                            </div>
                          ) : (
                            <span className="text-slate-300">-</span>
                          )}
                        </td>
                        <td className="px-3.5 py-3 text-slate-600 border-r border-slate-100 max-w-[200px] truncate" title={d.akunLoginPc}>
                          {d.akunLoginPc || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-600 border-r border-slate-100 max-w-[200px] truncate" title={d.akunLain}>
                          {d.akunLain || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-500 border-r border-slate-100 max-w-[150px] truncate" title={d.catatan}>
                          {d.catatan || '-'}
                        </td>
                        <td className="px-3 py-3 text-center sticky right-0 bg-white/95 backdrop-blur-xs shadow-l whitespace-nowrap">
                          <div className="flex items-center justify-center gap-1.5">
                            <button
                              onClick={() => setSelectedRecord(rec)}
                              title="Lihat Detail"
                              className="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                            >
                              <Eye className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => setEditRecord(rec)}
                              title="Edit Data"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() =>
                                setDeleteTarget({
                                  id: rec.id,
                                  name: d.nama + ' (' + (d.jenisHp || 'HP') + ')',
                                  type: 'record'
                                })
                              }
                              title="Hapus Baris"
                              className="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
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
      )}

      {/* =========================================================================
          TABLE 3: Akun MikroTik Hotspot Sales (1:1 with MikroTik Sheet)
         ========================================================================= */}
      {activeSheet === 'mikrotik' && (
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs text-slate-700 divide-y divide-slate-200 font-sans">
              <thead className="bg-[#0B1B3D] text-slate-200 uppercase tracking-wider text-[11px] font-bold sticky top-0 z-10">
                <tr>
                  <th className="px-3 py-3 text-center w-12 border-r border-[#1C2C54]">No</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Waktu Dibuat</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54] text-amber-300">Nama Sales</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Tim</th>
                  <th className="px-4 py-3 border-r border-[#1C2C54] text-teal-300 font-mono">Username Hotspot</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54] font-mono">Password</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Profile Hotspot</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54]">Rate Limit Bandwidth</th>
                  <th className="px-3.5 py-3 border-r border-[#1C2C54] text-center">Status</th>
                  <th className="px-3 py-3 text-center sticky right-0 bg-[#0B1B3D] shadow-l">Aksi CRUD</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 bg-white">
                {currentPageData.length === 0 ? (
                  <tr>
                    <td colSpan={10} className="px-6 py-12 text-center text-slate-400">
                      Tidak ada akun MikroTik yang cocok dengan pencarian.
                    </td>
                  </tr>
                ) : (
                  currentPageData.map((item, idx) => {
                    const m = item as MikrotikAccount;
                    const rowNum = (page - 1) * pageSize + idx + 1;

                    return (
                      <tr key={m.id} className="hover:bg-slate-50 transition-colors">
                        <td className="px-3 py-3 text-center font-bold text-slate-400 border-r border-slate-100">
                          {rowNum}
                        </td>
                        <td className="px-3.5 py-3 text-slate-500 whitespace-nowrap border-r border-slate-100 font-mono text-[11px]">
                          {m.createdAt}
                        </td>
                        <td className="px-4 py-3 font-bold text-slate-900 border-r border-slate-100 whitespace-nowrap">
                          {m.namaPemilik || '-'}
                        </td>
                        <td className="px-3.5 py-3 text-slate-700 border-r border-slate-100 whitespace-nowrap uppercase font-semibold">
                          {m.tim || '-'}
                        </td>
                        <td className="px-4 py-3 font-mono font-bold text-teal-700 border-r border-slate-100 whitespace-nowrap">
                          {m.username}
                        </td>
                        <td className="px-3.5 py-3 font-mono text-slate-600 border-r border-slate-100 whitespace-nowrap">
                          {m.password}
                        </td>
                        <td className="px-3.5 py-3 text-slate-700 border-r border-slate-100 whitespace-nowrap">
                          {m.profileHotspot || 'sales_jaket'}
                        </td>
                        <td className="px-3.5 py-3 font-semibold text-slate-800 border-r border-slate-100 whitespace-nowrap">
                          {m.rateLimit || '10M/10M'}
                        </td>
                        <td className="px-3.5 py-3 text-center border-r border-slate-100 whitespace-nowrap">
                          <span
                            className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                              m.status === 'aktif'
                                ? 'bg-emerald-100 text-emerald-800'
                                : 'bg-slate-100 text-slate-700'
                            }`}
                          >
                            {m.status || 'aktif'}
                          </span>
                        </td>
                        <td className="px-3 py-3 text-center sticky right-0 bg-white/95 backdrop-blur-xs shadow-l whitespace-nowrap">
                          <div className="flex items-center justify-center gap-1.5">
                            <button
                              onClick={() => setEditMikrotik(m)}
                              title="Edit Data MikroTik"
                              className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                            >
                              <Pencil className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() =>
                                setDeleteTarget({
                                  id: m.id,
                                  name: m.username + ' (' + m.namaPemilik + ')',
                                  type: 'mikrotik'
                                })
                              }
                              title="Hapus Akun MikroTik"
                              className="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
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
      )}

      {/* Pagination Controls */}
      {totalPages > 1 && (
        <div className="flex items-center justify-between bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-xs text-xs">
          <div className="text-slate-500 font-medium">
            Halaman <b>{page}</b> dari <b>{totalPages}</b>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setPage((p) => Math.max(p - 1, 1))}
              disabled={page === 1}
              className="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-40 font-semibold flex items-center gap-1"
            >
              <ChevronLeft className="w-4 h-4" />
              <span>Sebelumnya</span>
            </button>
            <button
              onClick={() => setPage((p) => Math.min(p + 1, totalPages))}
              disabled={page === totalPages}
              className="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-40 font-semibold flex items-center gap-1"
            >
              <span>Selanjutnya</span>
              <ChevronRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      )}

      {/* Detail Modal */}
      {selectedRecord && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
            <div className="flex items-center justify-between pb-4 border-b border-slate-100">
              <h3 className="font-bold text-slate-900 text-base">
                Detail Baris Spreadsheet ({selectedRecord.type === 'pc' ? 'Laptop / PC' : 'Akun Sales'})
              </h3>
              <button
                onClick={() => setSelectedRecord(null)}
                className="text-slate-400 hover:text-slate-600 font-bold text-lg"
              >
                &times;
              </button>
            </div>

            <div className="mt-4 space-y-3 text-xs">
              {Object.entries((selectedRecord.data as unknown) as Record<string, string>).map(([k, v]) => {
                if (!v || v === '-') return null;
                const isUrl = String(v).startsWith('http');
                return (
                  <div key={k} className="flex flex-col sm:flex-row sm:items-center justify-between py-1.5 border-b border-slate-100 gap-1">
                    <span className="font-bold text-slate-600 uppercase tracking-wide text-[10px]">{k}:</span>
                    {isUrl ? (
                      <a href={v} target="_blank" rel="noreferrer" className="text-blue-600 hover:underline break-all">
                        {v}
                      </a>
                    ) : (
                      <span className="font-semibold text-slate-900 break-all">{v}</span>
                    )}
                  </div>
                );
              })}
            </div>

            <div className="mt-6 flex justify-end gap-2">
              <button
                onClick={() => {
                  setEditRecord(selectedRecord);
                  setSelectedRecord(null);
                }}
                className="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs"
              >
                Edit Baris Ini
              </button>
              <button
                onClick={() => setSelectedRecord(null)}
                className="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Edit Record Modal */}
      {editRecord && (
        <EditRecordModal
          record={editRecord}
          isOpen={Boolean(editRecord)}
          onClose={() => setEditRecord(null)}
          onSaveSuccess={() => {
            onRefresh();
            setEditRecord(null);
          }}
        />
      )}

      {/* Edit Mikrotik Modal */}
      {editMikrotik && (
        <EditMikrotikModal
          account={editMikrotik}
          isOpen={Boolean(editMikrotik)}
          onClose={() => setEditMikrotik(null)}
          onSaveSuccess={() => {
            onRefresh();
            setEditMikrotik(null);
          }}
        />
      )}

      {/* Delete Confirmation Modal */}
      {deleteTarget && (
        <ConfirmDeleteModal
          isOpen={Boolean(deleteTarget)}
          itemName={deleteTarget.name}
          onConfirm={confirmDelete}
          onCancel={() => setDeleteTarget(null)}
        />
      )}

      {/* Clear All Modal */}
      <ClearAllModal
        isOpen={isClearModalOpen}
        onClose={() => setIsClearModalOpen(false)}
        onConfirmClear={handleConfirmClearAll}
        currentSheetName={
          activeSheet === 'pc'
            ? 'Spesifikasi PC & Laptop'
            : activeSheet === 'akun'
            ? 'Akun Sales 2026'
            : 'Akun MikroTik'
        }
        currentCount={
          activeSheet === 'pc'
            ? pcRecords.length
            : activeSheet === 'akun'
            ? akunRecords.length
            : mikrotikAccounts.length
        }
        totalAllCount={records.length + mikrotikAccounts.length}
      />
    </div>
  );
};
