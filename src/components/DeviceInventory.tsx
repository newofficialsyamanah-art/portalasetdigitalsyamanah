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
  FileSpreadsheet
} from 'lucide-react';
import { SubmissionRecord, FormPC } from '../types';
import { deleteSubmission } from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';

interface DeviceInventoryProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenNewPcForm: () => void;
  onOpenImportModal: () => void;
}

export const DeviceInventory: React.FC<DeviceInventoryProps> = ({
  records,
  onRefresh,
  onOpenNewPcForm,
  onOpenImportModal
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [typeFilter, setTypeFilter] = useState<string>('all');
  const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [activePhotoModal, setActivePhotoModal] = useState<string | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);

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

  const exportDeviceCSV = () => {
    if (deviceRecords.length === 0) {
      alert('Belum ada data perangkat untuk diekspor!');
      return;
    }

    const headers = [
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
    ];

    const rows = deviceRecords.map((r) => {
      const d = r.data as FormPC;
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
    });

    const csvContent =
      'data:text/csv;charset=utf-8,' +
      [headers, ...rows]
        .map((row) => row.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `inventaris_laptop_pc_syamanah_${new Date().toISOString().slice(0, 10)}.csv`);
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
      {/* Syamanah Official Workstation Banner */}
      <div className="bg-gradient-to-br from-[#0B1B3D] via-[#0F2D60] to-[#0A1628] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-blue-500/30">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-2xl">
            <div className="flex items-center gap-2 text-xs text-blue-300 font-semibold uppercase tracking-wider">
              <span>Syamanah Apparel Indonesia</span>
              <span>·</span>
              <span>Infrastruktur IT &amp; Desain</span>
              <span>·</span>
              <span className="text-white font-bold">{deviceRecords.length} Unit Workstation Terdaftar</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              Katalog Laptop &amp; PC Workstation Syamanah
            </h2>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              Inventarisasi hardware komputer staf operasional kantor, desainer jersey/jaket, dan administrasi. Dilengkapi spesifikasi DxDiag, prosesor, memory RAM, Windows OS, dan tautan screenshot Google Drive.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={onOpenNewPcForm}
              className="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs flex items-center gap-2 shadow-lg transition active:scale-95"
            >
              <Plus className="w-4 h-4 stroke-[3]" />
              <span>+ Input Laptop / PC Baru</span>
            </button>

            <button
              onClick={exportDeviceCSV}
              className="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs flex items-center gap-2 transition active:scale-95"
            >
              <Download className="w-4 h-4 text-blue-300" />
              <span>Ekspor CSV</span>
            </button>
          </div>
        </div>

        {/* Syamanah Hardware Stats Strip */}
        <div className="mt-6 pt-6 border-t border-slate-700/60 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Total Komputer &amp; Laptop</span>
            <span className="text-2xl font-black text-blue-400 mt-0.5 block">{deviceRecords.length} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Workstation &amp; portabel</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Laptop Kantor</span>
            <span className="text-2xl font-black text-emerald-400 mt-0.5 block">{totalLaptop} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Mobilitas staf &amp; sales</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">PC Desktop Meja</span>
            <span className="text-2xl font-black text-cyan-400 mt-0.5 block">{totalPC} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Desain sublimasi &amp; CRM</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">All-in-One / Mini PC</span>
            <span className="text-2xl font-black text-amber-400 mt-0.5 block">{totalAIO} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Komputer kasir &amp; display</span>
          </div>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Cari nama staf, computer name, jenis perangkat, prosesor, RAM..."
              className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          <div className="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 shrink-0">
            <button
              onClick={() => setTypeFilter('all')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'all'
                  ? 'bg-white text-slate-900 shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Semua ({deviceRecords.length})
            </button>
            <button
              onClick={() => setTypeFilter('Laptop')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'Laptop'
                  ? 'bg-blue-600 text-white shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Laptop ({totalLaptop})
            </button>
            <button
              onClick={() => setTypeFilter('PC Desktop')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
                typeFilter === 'PC Desktop'
                  ? 'bg-cyan-600 text-white shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              PC Desktop ({totalPC})
            </button>
          </div>
        </div>

        {/* View Mode Toggle: Grid Cards vs Dense Table */}
        <div className="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-end sm:self-auto border border-slate-200">
          <button
            onClick={() => setViewMode('grid')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
              viewMode === 'grid'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Grid Workstation
          </button>
          <button
            onClick={() => setViewMode('table')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
              viewMode === 'table'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Tabel Spesifikasi
          </button>
        </div>
      </div>

      {/* Main Laptop & PC Content */}
      {filteredRecords.length === 0 ? (
        <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
          <div className="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
            <Laptop className="w-7 h-7" />
          </div>
          <h4 className="text-base font-bold text-slate-900">Tidak Ada Komputer Ditemukan</h4>
          <p className="text-xs text-slate-500 max-w-sm mx-auto">
            {searchTerm
              ? 'Tidak ada perangkat yang cocok dengan pencarian Anda.'
              : 'Belum ada data PC atau laptop. Klik "+ Input Laptop / PC Baru" untuk mendaftarkan unit.'}
          </p>
        </div>
      ) : viewMode === 'grid' ? (
        /* WORKSTATION GRID: Specially designed for Laptop & PC Hardware */
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filteredRecords.map((rec) => {
            const d = rec.data as FormPC;
            const isLaptop = (d.jenisPerangkat || 'Laptop') === 'Laptop';

            return (
              <div
                key={rec.id}
                className="bg-white rounded-3xl border border-slate-200 hover:border-blue-400/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group"
              >
                {/* Workstation Card Header */}
                <div className="p-5 border-b border-slate-100 space-y-3">
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                      <div
                        className={`w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border ${
                          isLaptop
                            ? 'bg-blue-50 text-blue-700 border-blue-200 group-hover:bg-blue-600 group-hover:text-white'
                            : 'bg-cyan-50 text-cyan-700 border-cyan-200 group-hover:bg-cyan-600 group-hover:text-white'
                        } transition-colors`}
                      >
                        {isLaptop ? <Laptop className="w-5 h-5" /> : <Monitor className="w-5 h-5" />}
                      </div>
                      <div>
                        <h3 className="font-extrabold text-sm text-slate-900 leading-snug">
                          {d.nama}
                        </h3>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                          <span>{d.departemen || 'Operasional'}</span>
                          {d.jabatan && (
                            <>
                              <span className="mx-1">·</span>
                              <span>{d.jabatan}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                      {d.jenisPerangkat || 'Laptop'}
                    </span>
                  </div>

                  {/* Computer Name & IP Badge */}
                  <div className="p-3 bg-slate-900 text-white rounded-2xl font-mono text-[11px] space-y-1">
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Device ID / Host:</span>
                      <span className="text-cyan-300 font-bold">{d.computerName}</span>
                    </div>
                    {d.ip && (
                      <div className="flex items-center justify-between text-slate-300 text-[10px] pt-1 border-t border-slate-800">
                        <span className="text-slate-400">IP Jaringan:</span>
                        <span className="text-emerald-400">{d.ip}</span>
                      </div>
                    )}
                  </div>
                </div>

                {/* Specs Box */}
                <div className="p-5 space-y-3 flex-1 text-xs">
                  <div className="space-y-1.5">
                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Cpu className="w-3.5 h-3.5 text-blue-600" />
                        <span>Prosesor:</span>
                      </span>
                      <span className="font-semibold text-slate-800 text-right truncate max-w-[170px]" title={d.processor}>
                        {d.processor || 'Intel/AMD Standard'}
                      </span>
                    </div>

                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Layers className="w-3.5 h-3.5 text-indigo-600" />
                        <span>RAM / Memori:</span>
                      </span>
                      <span className="font-semibold text-slate-800">{d.memory || '8GB RAM'}</span>
                    </div>

                    <div className="flex items-center justify-between text-[11px]">
                      <span className="text-slate-500 flex items-center gap-1.5">
                        <Server className="w-3.5 h-3.5 text-teal-600" />
                        <span>Sistem Operasi:</span>
                      </span>
                      <span className="font-semibold text-slate-800 text-right truncate max-w-[170px]" title={d.os}>
                        {d.os || 'Windows 10 Pro'}
                      </span>
                    </div>
                  </div>

                  {/* Screenshot Drive Links */}
                  <div className="pt-2 border-t border-slate-100 flex items-center gap-2 text-[10px]">
                    <span className="text-slate-400">DxDiag Drive:</span>
                    {d.displayLink && d.displayLink !== '-' ? (
                      <a
                        href={d.displayLink}
                        target="_blank"
                        rel="noreferrer"
                        className="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold flex items-center gap-1"
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
                        className="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold flex items-center gap-1"
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
                        className="px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-700 hover:bg-cyan-100 font-semibold flex items-center gap-1"
                      >
                        <span>Input</span>
                        <ExternalLink className="w-2.5 h-2.5" />
                      </a>
                    ) : null}
                  </div>
                </div>

                {/* Card Bottom Bar: CRUD Actions */}
                <div className="p-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] text-slate-400 font-medium">
                    Input: {rec.timestamp.split(' ')[0]}
                  </span>

                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={() => setSelectedRecord(rec)}
                      title="Lihat Detail Lengkap (Read)"
                      className="p-2 text-slate-600 hover:text-blue-700 hover:bg-blue-100/50 rounded-xl transition"
                    >
                      <Eye className="w-4 h-4" />
                    </button>

                    <button
                      onClick={() => setEditRecord(rec)}
                      title="Edit Data Hardware (Update)"
                      className="p-2 text-slate-600 hover:text-amber-700 hover:bg-amber-100/50 rounded-xl transition"
                    >
                      <Pencil className="w-4 h-4" />
                    </button>

                    <button
                      onClick={() => handleDelete(rec.id, d.nama)}
                      title="Hapus dari inventaris & Spreadsheet (Delete)"
                      className="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-100/50 rounded-xl transition"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      ) : (
        /* DENSE HARDWARE TABLE */
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold text-[11px]">
                  <th className="py-3 px-4">Nama Pemilik</th>
                  <th className="py-3 px-4">Jenis Perangkat &amp; Merk</th>
                  <th className="py-3 px-4">Departemen &amp; Jabatan</th>
                  <th className="py-3 px-4">Spesifikasi Inti (CPU / RAM)</th>
                  <th className="py-3 px-4">Computer Name &amp; IP</th>
                  <th className="py-3 px-4 text-center">DxDiag Drive</th>
                  <th className="py-3 px-4 text-right">Aksi CRUD</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredRecords.map((rec) => {
                  const d = rec.data as FormPC;
                  const devType = d.jenisPerangkat || 'Laptop';
                  const isLaptop = devType === 'Laptop';

                  return (
                    <tr key={rec.id} className="hover:bg-slate-50/80 transition">
                      <td className="py-3 px-4">
                        <span className="font-bold text-slate-900 block">{d.nama}</span>
                        <span className="text-[10px] text-slate-400">{d.jabatan || 'Staf'}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-semibold text-slate-800 block">{d.manufacturer || 'Standar OEM'}</span>
                        <span className="text-[10px] text-blue-600 font-bold">{devType}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="text-slate-800 font-medium block">{d.departemen}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-semibold text-slate-800 block truncate max-w-xs">{d.processor || '-'}</span>
                        <span className="text-[10px] text-indigo-700 font-bold">{d.memory || '8GB RAM'}</span>
                      </td>

                      <td className="py-3 px-4 font-mono text-[11px]">
                        <span className="font-bold text-slate-900 block">{d.computerName}</span>
                        <span className="text-[10px] text-emerald-600">{d.ip || '-'}</span>
                      </td>

                      <td className="py-3 px-4 text-center">
                        {d.displayLink && d.displayLink !== '-' ? (
                          <a
                            href={d.displayLink}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline"
                          >
                            <span>Screenshot</span>
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
                            title="Lihat Detail (Read)"
                            className="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition"
                          >
                            <Eye className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => setEditRecord(rec)}
                            title="Edit Data (Update)"
                            className="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-slate-100 rounded-lg transition"
                          >
                            <Pencil className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => handleDelete(rec.id, d.nama)}
                            title="Hapus Data (Delete)"
                            className="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                          >
                            <Trash2 className="w-4 h-4" />
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
      )}

      {/* Detail Modal */}
      {selectedRecord && (
        <div className="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden max-h-[85vh] flex flex-col">
            <div className="bg-[#0B1B3D] text-white px-6 py-4 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold">
                  <Laptop className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="font-bold text-base">
                    {(selectedRecord.data as FormPC).computerName}
                  </h3>
                  <p className="text-xs text-blue-300">
                    Pemilik: {(selectedRecord.data as FormPC).nama} &bull; ID: {selectedRecord.id}
                  </p>
                </div>
              </div>
              <button
                onClick={() => setSelectedRecord(null)}
                className="p-1.5 text-slate-400 hover:text-white rounded-xl"
              >
                ✕
              </button>
            </div>

            <div className="overflow-y-auto p-6 space-y-4 text-xs">
              <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 grid grid-cols-2 gap-3">
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Nama Pemilik:</span>
                  <div className="font-bold text-slate-900">{(selectedRecord.data as FormPC).nama}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Departemen / Jabatan:</span>
                  <div className="font-bold text-slate-900">
                    {(selectedRecord.data as FormPC).departemen} · {(selectedRecord.data as FormPC).jabatan}
                  </div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Jenis &amp; Merk:</span>
                  <div className="font-bold text-slate-900">
                    {(selectedRecord.data as FormPC).jenisPerangkat} ({(selectedRecord.data as FormPC).manufacturer || 'OEM'})
                  </div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Operating System:</span>
                  <div className="font-bold text-slate-900">{(selectedRecord.data as FormPC).os}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Prosesor:</span>
                  <div className="font-bold text-slate-900">{(selectedRecord.data as FormPC).processor}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">RAM / Memory:</span>
                  <div className="font-bold text-slate-900">{(selectedRecord.data as FormPC).memory}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">IP Address:</span>
                  <div className="font-bold font-mono text-emerald-700">{(selectedRecord.data as FormPC).ip || '-'}</div>
                </div>
                <div>
                  <span className="text-[10px] text-slate-400 font-bold uppercase">Password Windows:</span>
                  <div className="font-bold font-mono text-slate-700">{(selectedRecord.data as FormPC).passwordPc || 'Tanpa Password'}</div>
                </div>
              </div>

              {/* Tautan Screenshot DxDiag di Drive */}
              <div className="p-4 bg-slate-900 text-white rounded-2xl space-y-2">
                <span className="text-blue-300 font-bold text-xs">Tautan Google Drive Screenshot DxDiag:</span>
                <div className="space-y-1 text-[11px] font-mono">
                  {(selectedRecord.data as FormPC).displayLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Display:</span>
                      <a href={(selectedRecord.data as FormPC).displayLink} target="_blank" rel="noreferrer" className="text-cyan-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).displayLink}
                      </a>
                    </div>
                  )}
                  {(selectedRecord.data as FormPC).soundLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Sound:</span>
                      <a href={(selectedRecord.data as FormPC).soundLink} target="_blank" rel="noreferrer" className="text-cyan-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).soundLink}
                      </a>
                    </div>
                  )}
                  {(selectedRecord.data as FormPC).inputLink && (
                    <div className="flex items-center justify-between">
                      <span className="text-slate-400">Input:</span>
                      <a href={(selectedRecord.data as FormPC).inputLink} target="_blank" rel="noreferrer" className="text-cyan-400 underline truncate max-w-sm">
                        {(selectedRecord.data as FormPC).inputLink}
                      </a>
                    </div>
                  )}
                </div>
              </div>
            </div>

            <div className="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
              <button
                onClick={() => {
                  setEditRecord(selectedRecord);
                  setSelectedRecord(null);
                }}
                className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition"
              >
                <Pencil className="w-3.5 h-3.5" />
                <span>Edit Spesifikasi Komputer</span>
              </button>

              <button
                onClick={() => setSelectedRecord(null)}
                className="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl"
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
