import React, { useState } from 'react';
import {
  Laptop,
  Monitor,
  Search,
  Filter,
  Download,
  Eye,
  CheckCircle2,
  Clock,
  ExternalLink,
  ShieldAlert,
  Image as ImageIcon,
  Cpu,
  Layers,
  HardDrive,
  User,
  Building2,
  Tag,
  Trash2,
  RefreshCw,
  X,
  Pencil,
  Plus,
  Server,
  FileSpreadsheet,
  Lock,
  Copy,
  Check
} from 'lucide-react';
import { SubmissionRecord, FormPC, UserAccount } from '../types';
import { deleteSubmission } from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';

interface DeviceInventoryProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenNewPcForm: () => void;
  onOpenImportModal: () => void;
  currentUser: UserAccount | null;
}

export const DeviceInventory: React.FC<DeviceInventoryProps> = ({
  records,
  onRefresh,
  onOpenNewPcForm,
  onOpenImportModal,
  currentUser
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [typeFilter, setTypeFilter] = useState<string>('all');
  const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [copiedId, setCopiedId] = useState<string | null>(null);

  const isGA = currentUser?.role === 'general_affair';

  // STRICT PRIVACY FILTER: Only include physical PC/Laptop hardware records in this inventory!
  const deviceRecords = records.filter((r) => r.type === 'pc');

  const filteredRecords = deviceRecords.filter((rec) => {
    const data = rec.data as FormPC;
    const deviceType = data.jenisPerangkat || 'Laptop';

    if (typeFilter !== 'all' && deviceType !== typeFilter) return false;

    if (!searchTerm.trim()) return true;

    const term = searchTerm.toLowerCase();
    const nameMatch = (data.nama || '').toLowerCase().includes(term);
    const deptMatch = (data.departemen || '').toLowerCase().includes(term);
    const compMatch = (data.computerName || '').toLowerCase().includes(term);
    const procMatch = (data.processor || '').toLowerCase().includes(term);
    const brandMatch = (data.manufacturer || '').toLowerCase().includes(term);
    const typeMatch = deviceType.toLowerCase().includes(term);

    return nameMatch || deptMatch || compMatch || procMatch || brandMatch || typeMatch;
  });

  const handleDelete = (id: string, name: string) => {
    if (isGA) return;
    setDeleteTarget({ id, name });
  };

  const handleConfirmDelete = async () => {
    if (deleteTarget) {
      await deleteSubmission(deleteTarget.id);
      onRefresh();
      if (selectedRecord?.id === deleteTarget.id) {
        setSelectedRecord(null);
      }
      setDeleteTarget(null);
    }
  };

  const handleCopy = (text: string, id: string) => {
    navigator.clipboard.writeText(text);
    setCopiedId(id);
    setTimeout(() => setCopiedId(null), 2000);
  };

  const exportDeviceCSV = () => {
    if (deviceRecords.length === 0) {
      alert('Belum ada data perangkat untuk diekspor!');
      return;
    }

    const headers = isGA
      ? [
          'Timestamp',
          'Nama Pemilik',
          'Departemen',
          'Jabatan',
          'Jenis Perangkat',
          'Merk / Manufacturer',
          'Computer Name',
          'Operating System',
          'Processor',
          'RAM / Memory',
          'IP Address',
          'Foto Display DxDiag',
          'Foto Sound DxDiag',
          'Foto Input DxDiag'
        ]
      : [
          'Timestamp',
          'Nama Pemilik',
          'Departemen',
          'Jabatan',
          'Jenis Perangkat',
          'Merk / Manufacturer',
          'Computer Name',
          'Password Windows',
          'Operating System',
          'Processor',
          'RAM / Memory',
          'IP Address',
          'Foto Display DxDiag',
          'Foto Sound DxDiag',
          'Foto Input DxDiag'
        ];

    const rows = deviceRecords.map((r) => {
      const d = r.data as FormPC;
      if (isGA) {
        return [
          r.timestamp,
          d.nama,
          d.departemen,
          d.jabatan,
          d.jenisPerangkat || 'Laptop',
          d.manufacturer || '-',
          d.computerName,
          d.os,
          d.processor,
          d.memory,
          d.ip,
          d.displayLink || '-',
          d.soundLink || '-',
          d.inputLink || '-'
        ];
      }
      return [
        r.timestamp,
        d.nama,
        d.departemen,
        d.jabatan,
        d.jenisPerangkat || 'Laptop',
        d.manufacturer || '-',
        d.computerName,
        d.passwordPc || '-',
        d.os,
        d.processor,
        d.memory,
        d.ip,
        d.displayLink || '-',
        d.soundLink || '-',
        d.inputLink || '-'
      ];
    });

    const csvContent =
      'data:text/csv;charset=utf-8,\uFEFF' +
      [headers, ...rows]
        .map((row) => row.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `inventaris_hardware_syamanah_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const totalLaptop = deviceRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'Laptop' || !(r.data as FormPC).jenisPerangkat
  ).length;
  const totalPC = deviceRecords.filter((r) => (r.data as FormPC).jenisPerangkat === 'PC Desktop').length;
  const totalAIO = deviceRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'All-in-One' || (r.data as FormPC).jenisPerangkat === 'Mini PC'
  ).length;

  return (
    <div className="space-y-6">
      {/* Modern Unified Hero Banner with Syamanah Colors */}
      <div className="bg-gradient-to-r from-[#0F1D2E] via-[#163654] to-[#2E5190] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-2xl">
            <div className="flex items-center gap-2 text-xs text-[#3A7D8A] font-bold uppercase tracking-wider">
              <span className="bg-white/10 text-teal-300 px-2.5 py-0.5 rounded-full">
                {isGA ? '🏢 Divisi General Affair' : 'Infrastruktur IT & Desain'}
              </span>
              <span>·</span>
              <span className="text-white font-medium">{deviceRecords.length} Unit Workstation Terdata</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              Katalog Laptop &amp; PC Workstation
            </h1>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              {isGA
                ? 'Inventarisasi fisik perangkat komputer staf kantor, divisi desain jersey/jaket, dan administrasi. Menampilkan nama pemakai, tipe unit, CPU, RAM, OS, dan foto DxDiag.'
                : 'Inventarisasi hardware komputer staf kantor, desainer jersey/jaket, dan administrasi. Dilengkapi spesifikasi DxDiag, prosesor, memory RAM, Windows OS, dan kredensial perangkat.'}
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={onOpenNewPcForm}
              className="px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs flex items-center gap-2 shadow-md transition active:scale-95"
            >
              <Plus className="w-4 h-4" />
              <span>+ Input Laptop / PC</span>
            </button>

            <button
              onClick={exportDeviceCSV}
              className="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs flex items-center gap-2 transition active:scale-95"
            >
              <Download className="w-4 h-4 text-teal-300" />
              <span>Ekspor CSV</span>
            </button>
          </div>
        </div>

        {/* Clean Translucent Metric Cards */}
        <div className="mt-6 pt-6 border-t border-white/10 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Total Komputer</span>
            <span className="text-2xl font-black text-white mt-1 block">{deviceRecords.length} Unit</span>
            <span className="text-[10px] text-teal-300 mt-0.5 block">Semua workstation</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Laptop Staf</span>
            <span className="text-2xl font-black text-emerald-400 mt-1 block">{totalLaptop} Unit</span>
            <span className="text-[10px] text-emerald-200 mt-0.5 block">Portabel operasional</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">PC Desktop Meja</span>
            <span className="text-2xl font-black text-cyan-300 mt-1 block">{totalPC} Unit</span>
            <span className="text-[10px] text-cyan-200 mt-0.5 block">Desain &amp; sublimasi</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">All-in-One / Mini PC</span>
            <span className="text-2xl font-black text-amber-300 mt-1 block">{totalAIO} Unit</span>
            <span className="text-[10px] text-amber-200 mt-0.5 block">Display &amp; kasir</span>
          </div>
        </div>
      </div>

      {/* Seamless Search and Segmented Filter Bar */}
      <div className="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Cari nama pemakai, jenis perangkat, host, prosesor, RAM..."
              className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
            />
            {searchTerm && (
              <button
                onClick={() => setSearchTerm('')}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          <div className="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200/80 shrink-0">
            <button
              onClick={() => setTypeFilter('all')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'all'
                  ? 'bg-[#0F1D2E] text-white shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Semua ({deviceRecords.length})
            </button>
            <button
              onClick={() => setTypeFilter('Laptop')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'Laptop'
                  ? 'bg-[#0F1D2E] text-white shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Laptop ({totalLaptop})
            </button>
            <button
              onClick={() => setTypeFilter('PC Desktop')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'PC Desktop'
                  ? 'bg-[#0F1D2E] text-white shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              PC Meja ({totalPC})
            </button>
          </div>
        </div>

        <div className="flex items-center gap-1.5 self-end md:self-auto shrink-0">
          <button
            onClick={() => setViewMode('grid')}
            className={`p-2 rounded-xl text-xs font-semibold flex items-center gap-1 transition ${
              viewMode === 'grid'
                ? 'bg-[#0F1D2E] text-white shadow-xs'
                : 'text-slate-600 hover:bg-slate-100 border border-slate-200'
            }`}
            title="Tampilan Grid Card"
          >
            <Layers className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Grid</span>
          </button>
          <button
            onClick={() => setViewMode('table')}
            className={`p-2 rounded-xl text-xs font-semibold flex items-center gap-1 transition ${
              viewMode === 'table'
                ? 'bg-[#0F1D2E] text-white shadow-xs'
                : 'text-slate-600 hover:bg-slate-100 border border-slate-200'
            }`}
            title="Tampilan Tabel Rinci"
          >
            <FileSpreadsheet className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Tabel</span>
          </button>
        </div>
      </div>

      {/* Hardware Content Area */}
      {filteredRecords.length === 0 ? (
        <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 space-y-3">
          <div className="w-12 h-12 rounded-2xl bg-teal-50 text-[#3A7D8A] mx-auto flex items-center justify-center">
            <Laptop className="w-6 h-6" />
          </div>
          <h4 className="text-base font-bold text-slate-900">Tidak Ada Perangkat Ditemukan</h4>
          <p className="text-xs text-slate-500 max-w-sm mx-auto">
            {searchTerm
              ? 'Tidak ada perangkat yang cocok dengan kata kunci pencarian Anda.'
              : 'Belum ada data PC atau laptop. Klik "+ Input Laptop / PC" untuk mendaftarkan unit.'}
          </p>
        </div>
      ) : viewMode === 'grid' ? (
        /* WORKSTATION CARDS: Clean, Modern, Uncluttered */
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filteredRecords.map((rec) => {
            const d = rec.data as FormPC;
            const devType = d.jenisPerangkat || 'Laptop';
            const isLaptop = devType === 'Laptop';

            return (
              <div
                key={rec.id}
                className="bg-white rounded-2xl border border-slate-200/80 hover:border-[#3A7D8A]/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between overflow-hidden group"
              >
                {/* Card Top: User & Device Category */}
                <div className="p-5 border-b border-slate-100 space-y-3">
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                      <div
                        className={`w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border transition-colors ${
                          isLaptop
                            ? 'bg-teal-50 text-[#3A7D8A] border-teal-200/80 group-hover:bg-[#3A7D8A] group-hover:text-white'
                            : 'bg-blue-50 text-[#2E5190] border-blue-200/80 group-hover:bg-[#2E5190] group-hover:text-white'
                        }`}
                      >
                        {isLaptop ? <Laptop className="w-5 h-5" /> : <Monitor className="w-5 h-5" />}
                      </div>
                      <div>
                        <h3 className="font-extrabold text-sm text-[#0F1D2E] leading-snug group-hover:text-[#3A7D8A] transition-colors">
                          {d.nama}
                        </h3>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                          <span>{d.departemen || 'Operasional'}</span>
                          {d.jabatan && (
                            <>
                              <span className="mx-1 text-slate-300">·</span>
                              <span>{d.jabatan}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                      {devType}
                    </span>
                  </div>

                  {/* Device Host & IP Strip */}
                  <div className="p-2.5 bg-slate-50/80 border border-slate-200/60 rounded-xl flex items-center justify-between text-xs font-mono">
                    <div className="flex items-center gap-1.5 truncate max-w-[170px]" title={d.computerName}>
                      <span className="text-[10px] text-slate-400 font-bold uppercase">Host:</span>
                      <span className="font-bold text-[#0F1D2E] truncate">{d.computerName}</span>
                    </div>
                    {d.ip ? (
                      <span className="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/50">
                        {d.ip}
                      </span>
                    ) : (
                      <span className="text-[10px] text-slate-400">DHCP</span>
                    )}
                  </div>
                </div>

                {/* Specs Section: Light, Airy, High Legibility */}
                <div className="p-5 space-y-3 flex-1 text-xs">
                  <div className="bg-slate-50/70 rounded-xl p-3 border border-slate-100 space-y-2">
                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Cpu className="w-3.5 h-3.5 text-[#3A7D8A]" />
                        <span>Prosesor:</span>
                      </span>
                      <span className="font-semibold text-slate-800 text-right truncate max-w-[170px]" title={d.processor}>
                        {d.processor || 'Intel/AMD Standard'}
                      </span>
                    </div>

                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <HardDrive className="w-3.5 h-3.5 text-[#2E5190]" />
                        <span>RAM / Memory:</span>
                      </span>
                      <span className="font-bold text-slate-800">{d.memory || '8GB RAM'}</span>
                    </div>

                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Server className="w-3.5 h-3.5 text-slate-600" />
                        <span>Sistem Operasi:</span>
                      </span>
                      <span className="font-semibold text-slate-800 text-right truncate max-w-[170px]" title={d.os}>
                        {d.os || 'Windows 10 Pro'}
                      </span>
                    </div>

                    {/* Windows Password: Protected for General Affair */}
                    <div className="flex items-center justify-between text-[11px] pt-1.5 border-t border-slate-200/60">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Lock className="w-3.5 h-3.5 text-slate-400" />
                        <span>Password PC:</span>
                      </span>
                      {isGA ? (
                        <span className="text-amber-800 bg-amber-50 px-2 py-0.5 rounded text-[10px] font-semibold flex items-center gap-1 border border-amber-200/60">
                          <Lock className="w-2.5 h-2.5 text-amber-600" />
                          <span>Terproteksi (GA)</span>
                        </span>
                      ) : (
                        <div className="flex items-center gap-1">
                          <span className="font-mono font-bold text-slate-800">
                            {d.passwordPc || 'Tanpa Password'}
                          </span>
                          {d.passwordPc && (
                            <button
                              onClick={() => handleCopy(d.passwordPc!, `pwd_${rec.id}`)}
                              className="text-slate-400 hover:text-slate-600 p-0.5"
                              title="Salin Password"
                            >
                              {copiedId === `pwd_${rec.id}` ? (
                                <Check className="w-3 h-3 text-emerald-600" />
                              ) : (
                                <Copy className="w-3 h-3" />
                              )}
                            </button>
                          )}
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Screenshot DxDiag Drive Links */}
                  <div className="flex items-center gap-1.5 text-[10px] flex-wrap">
                    <span className="text-slate-400 font-medium">DxDiag:</span>
                    {d.displayLink && d.displayLink !== '-' ? (
                      <a
                        href={d.displayLink}
                        target="_blank"
                        rel="noreferrer"
                        className="px-2 py-0.5 rounded-md bg-teal-50 text-[#3A7D8A] hover:bg-teal-100 font-semibold flex items-center gap-1 border border-teal-200/50"
                      >
                        <span>Display</span>
                        <ExternalLink className="w-2.5 h-2.5" />
                      </a>
                    ) : null}
                    {d.soundLink && d.soundLink !== '-' ? (
                      <a
                        href={d.soundLink}
                        target="_blank"
                        rel="noreferrer"
                        className="px-2 py-0.5 rounded-md bg-blue-50 text-[#2E5190] hover:bg-blue-100 font-semibold flex items-center gap-1 border border-blue-200/50"
                      >
                        <span>Sound</span>
                        <ExternalLink className="w-2.5 h-2.5" />
                      </a>
                    ) : null}
                    {d.inputLink && d.inputLink !== '-' ? (
                      <a
                        href={d.inputLink}
                        target="_blank"
                        rel="noreferrer"
                        className="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold flex items-center gap-1 border border-slate-200"
                      >
                        <span>Input</span>
                        <ExternalLink className="w-2.5 h-2.5" />
                      </a>
                    ) : null}
                  </div>
                </div>

                {/* Card Bottom Bar: Actions */}
                <div className="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] text-slate-400 font-medium">
                    Input: {rec.timestamp.split(' ')[0]}
                  </span>

                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={() => setSelectedRecord(rec)}
                      title="Lihat Detail Lengkap"
                      className="px-2.5 py-1 text-slate-600 hover:text-[#3A7D8A] hover:bg-teal-50 rounded-lg text-xs font-semibold transition flex items-center gap-1 border border-slate-200/60"
                    >
                      <Eye className="w-3.5 h-3.5" />
                      <span>Detail</span>
                    </button>

                    {/* Only Superadmin & Manager can edit or delete */}
                    {!isGA && (
                      <>
                        <button
                          onClick={() => setEditRecord(rec)}
                          title="Edit Data Hardware"
                          className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                        >
                          <Pencil className="w-3.5 h-3.5" />
                        </button>

                        <button
                          onClick={() => handleDelete(rec.id, d.nama)}
                          title="Hapus dari inventaris"
                          className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </>
                    )}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      ) : (
        /* TABLE VIEW */
        <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="bg-slate-50/90 border-b border-slate-200/80 text-slate-600 uppercase tracking-wider font-semibold text-[11px]">
                  <th className="py-3 px-4">Nama Pemakai</th>
                  <th className="py-3 px-4">Perangkat &amp; Merk</th>
                  <th className="py-3 px-4">Departemen</th>
                  <th className="py-3 px-4">Spesifikasi (CPU / RAM)</th>
                  <th className="py-3 px-4">Host &amp; IP</th>
                  {!isGA && <th className="py-3 px-4">Password PC</th>}
                  <th className="py-3 px-4 text-center">DxDiag</th>
                  <th className="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredRecords.map((rec) => {
                  const d = rec.data as FormPC;
                  const devType = d.jenisPerangkat || 'Laptop';

                  return (
                    <tr key={rec.id} className="hover:bg-slate-50/80 transition">
                      <td className="py-3 px-4">
                        <span className="font-bold text-[#0F1D2E] block">{d.nama}</span>
                        <span className="text-[10px] text-slate-400">{d.jabatan || 'Staf'}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-semibold text-slate-800 block">{d.manufacturer || 'Standar OEM'}</span>
                        <span className="text-[10px] text-[#3A7D8A] font-bold">{devType}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="text-slate-800 font-medium block">{d.departemen}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-semibold text-slate-800 block truncate max-w-xs">{d.processor || '-'}</span>
                        <span className="text-[10px] text-[#2E5190] font-bold">{d.memory || '8GB RAM'}</span>
                      </td>

                      <td className="py-3 px-4 font-mono text-[11px]">
                        <span className="font-bold text-slate-900 block">{d.computerName}</span>
                        <span className="text-[10px] text-emerald-700">{d.ip || 'DHCP'}</span>
                      </td>

                      {!isGA && (
                        <td className="py-3 px-4 font-mono text-xs">
                          {d.passwordPc || '-'}
                        </td>
                      )}

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
                          <span className="text-slate-400 text-[10px]">-</span>
                        )}
                      </td>

                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => setSelectedRecord(rec)}
                            title="Lihat Detail"
                            className="p-1.5 text-slate-500 hover:text-[#3A7D8A] hover:bg-slate-100 rounded-lg transition"
                          >
                            <Eye className="w-4 h-4" />
                          </button>
                          {!isGA && (
                            <>
                              <button
                                onClick={() => setEditRecord(rec)}
                                title="Edit Data"
                                className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition"
                              >
                                <Pencil className="w-4 h-4" />
                              </button>
                              <button
                                onClick={() => handleDelete(rec.id, d.nama)}
                                title="Hapus Data"
                                className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                              >
                                <Trash2 className="w-4 h-4" />
                              </button>
                            </>
                          )}
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Detail Modal */}
      {selectedRecord && (
        <div className="fixed inset-0 z-50 bg-[#0F1D2E]/80 backdrop-blur-xs flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden max-h-[85vh] flex flex-col animate-in fade-in zoom-in-95 duration-200">
            {/* Modal Header */}
            <div className="bg-gradient-to-r from-[#0F1D2E] via-[#163654] to-[#2E5190] text-white px-6 py-4 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-white/10 text-teal-300 flex items-center justify-center font-bold border border-white/10">
                  <Laptop className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="font-bold text-base">
                    {(selectedRecord.data as FormPC).computerName}
                  </h3>
                  <p className="text-xs text-slate-300">
                    Pemakai: {(selectedRecord.data as FormPC).nama} &bull; ID: {selectedRecord.id}
                  </p>
                </div>
              </div>
              <button
                onClick={() => setSelectedRecord(null)}
                className="p-1.5 text-slate-300 hover:text-white rounded-xl hover:bg-white/10 transition"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="overflow-y-auto p-6 space-y-4 text-xs">
              <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 grid grid-cols-2 gap-3.5">
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nama Pemakai:</span>
                  <div className="font-bold text-[#0F1D2E] text-sm mt-0.5">{(selectedRecord.data as FormPC).nama}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Departemen / Jabatan:</span>
                  <div className="font-bold text-slate-800 mt-0.5">
                    {(selectedRecord.data as FormPC).departemen} · {(selectedRecord.data as FormPC).jabatan || '-'}
                  </div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jenis &amp; Merk:</span>
                  <div className="font-bold text-slate-800 mt-0.5">
                    {(selectedRecord.data as FormPC).jenisPerangkat} ({(selectedRecord.data as FormPC).manufacturer || 'Standar OEM'})
                  </div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Operating System:</span>
                  <div className="font-bold text-slate-800 mt-0.5">{(selectedRecord.data as FormPC).os}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Prosesor:</span>
                  <div className="font-bold text-slate-800 mt-0.5">{(selectedRecord.data as FormPC).processor}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">RAM / Memory:</span>
                  <div className="font-bold text-slate-800 mt-0.5">{(selectedRecord.data as FormPC).memory}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">IP Address:</span>
                  <div className="font-bold font-mono text-emerald-700 mt-0.5">{(selectedRecord.data as FormPC).ip || 'DHCP'}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Password Windows:</span>
                  {isGA ? (
                    <div className="text-amber-800 bg-amber-50 px-2 py-0.5 rounded text-xs font-semibold flex items-center gap-1 border border-amber-200/60 mt-0.5">
                      <Lock className="w-3 h-3 text-amber-600" />
                      <span>•••••••• (Akses Terbatas: GA)</span>
                    </div>
                  ) : (
                    <div className="font-bold font-mono text-slate-800 mt-0.5">
                      {(selectedRecord.data as FormPC).passwordPc || 'Tanpa Password'}
                    </div>
                  )}
                </div>
              </div>

              {/* Tautan Screenshot DxDiag di Drive */}
              <div className="p-4 bg-slate-900 text-white rounded-2xl space-y-2">
                <span className="text-teal-300 font-bold text-xs flex items-center gap-1.5">
                  <ImageIcon className="w-3.5 h-3.5" />
                  <span>Tautan Google Drive Screenshot DxDiag:</span>
                </span>
                <div className="space-y-1.5 text-[11px] font-mono">
                  {(selectedRecord.data as FormPC).displayLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Display:</span>
                      <a href={(selectedRecord.data as FormPC).displayLink} target="_blank" rel="noreferrer" className="text-teal-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).displayLink}
                      </a>
                    </div>
                  )}
                  {(selectedRecord.data as FormPC).soundLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Sound:</span>
                      <a href={(selectedRecord.data as FormPC).soundLink} target="_blank" rel="noreferrer" className="text-teal-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).soundLink}
                      </a>
                    </div>
                  )}
                  {(selectedRecord.data as FormPC).inputLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Input:</span>
                      <a href={(selectedRecord.data as FormPC).inputLink} target="_blank" rel="noreferrer" className="text-teal-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).inputLink}
                      </a>
                    </div>
                  )}
                </div>
              </div>
            </div>

            <div className="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
              {!isGA ? (
                <button
                  onClick={() => {
                    setEditRecord(selectedRecord);
                    setSelectedRecord(null);
                  }}
                  className="px-4 py-2 bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition"
                >
                  <Pencil className="w-3.5 h-3.5" />
                  <span>Edit Spesifikasi</span>
                </button>
              ) : (
                <div className="text-[11px] text-slate-500">
                  Mode Hanya-Lihat (General Affair)
                </div>
              )}

              <button
                onClick={() => setSelectedRecord(null)}
                className="px-4 py-2 bg-[#0F1D2E] text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Delete Confirmation Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        itemName={deleteTarget?.name || ''}
        itemType="perangkat milik"
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Edit Record Modal (CRUD: Update) */}
      <EditRecordModal
        isOpen={Boolean(editRecord)}
        record={editRecord}
        onClose={() => setEditRecord(null)}
        onSaveSuccess={() => {
          onRefresh();
          setEditRecord(null);
        }}
      />
    </div>
  );
};
