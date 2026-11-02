import React, { useState } from 'react';
import {
  History,
  Search,
  Filter,
  Download,
  Trash2,
  RefreshCw,
  ExternalLink,
  Eye,
  CheckCircle2,
  Clock,
  AlertCircle,
  FileSpreadsheet,
  X,
  Copy,
  Check,
  Monitor,
  Smartphone,
  ChevronRight,
  Pencil
} from 'lucide-react';
import { SubmissionRecord, FormPC, FormAkun } from '../types';
import {
  deleteSubmission,
  clearAllSubmissions,
  submitToGoogleAppsScript,
  updateSubmissionStatus,
  syncAllAkunToGoogleSheet,
  syncAllPCToGoogleSheet
} from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';

interface HistoryTableProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenSettings: () => void;
  hasGasUrl: boolean;
}

export const HistoryTable: React.FC<HistoryTableProps> = ({
  records,
  onRefresh,
  onOpenSettings,
  hasGasUrl
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterType, setFilterType] = useState<'all' | 'pc' | 'akun'>('all');
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [syncingId, setSyncingId] = useState<string | null>(null);
  const [copiedId, setCopiedId] = useState<string | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [isClearAllOpen, setIsClearAllOpen] = useState(false);

  // Filter records
  const filteredRecords = records.filter((rec) => {
    if (filterType !== 'all' && rec.type !== filterType) return false;

    if (!searchTerm.trim()) return true;

    const term = searchTerm.toLowerCase();
    const data = rec.data as unknown as Record<string, string>;
    const nameMatch = (data.nama || '').toLowerCase().includes(term);
    const deptMatch = (data.departemen || '').toLowerCase().includes(term);
    const roleMatch = (data.kategori || data.jabatan || '').toLowerCase().includes(term);
    const pcMatch = (data.computerName || '').toLowerCase().includes(term);
    const phoneMatch = (data.noWa || '').toLowerCase().includes(term);

    return nameMatch || deptMatch || roleMatch || pcMatch || phoneMatch;
  });

  const handleDelete = (id: string, name: string) => {
    setDeleteTarget({ id, name });
  };

  const confirmSingleDelete = () => {
    if (deleteTarget) {
      deleteSubmission(deleteTarget.id);
      onRefresh();
      if (selectedRecord?.id === deleteTarget.id) {
        setSelectedRecord(null);
      }
      setDeleteTarget(null);
    }
  };

  const handleClearAll = () => {
    setIsClearAllOpen(true);
  };

  const confirmClearAll = () => {
    clearAllSubmissions();
    onRefresh();
    setSelectedRecord(null);
    setIsClearAllOpen(false);
  };

  const [syncingAllAkun, setSyncingAllAkun] = useState(false);
  const [syncingAllPC, setSyncingAllPC] = useState(false);
  const [syncFeedback, setSyncFeedback] = useState<string | null>(null);

  const handleSyncAllAkun = async () => {
    setSyncingAllAkun(true);
    setSyncFeedback(null);
    try {
      const res = await syncAllAkunToGoogleSheet();
      setSyncFeedback(res.message);
      onRefresh();
      setTimeout(() => setSyncFeedback(null), 8000);
    } finally {
      setSyncingAllAkun(false);
    }
  };

  const handleSyncAllPC = async () => {
    setSyncingAllPC(true);
    setSyncFeedback(null);
    try {
      const res = await syncAllPCToGoogleSheet();
      setSyncFeedback(res.message);
      onRefresh();
      setTimeout(() => setSyncFeedback(null), 8000);
    } finally {
      setSyncingAllPC(false);
    }
  };

  const handleReSync = async (rec: SubmissionRecord) => {
    setSyncingId(rec.id);
    try {
      const res = await submitToGoogleAppsScript(rec.type, rec.data);
      updateSubmissionStatus(rec.id, res.success, res.message);
      onRefresh();
    } finally {
      setSyncingId(null);
    }
  };

  const exportToCSV = (typeToExport: 'pc' | 'akun' | 'all') => {
    let rows: string[][] = [];
    let filename = 'syamanah_export.csv';

    if (typeToExport === 'pc' || typeToExport === 'all') {
      const pcRecords = records.filter(r => r.type === 'pc');
      if (pcRecords.length > 0) {
        const headers = [
          "Timestamp", "Nama", "Departemen", "Jabatan", "Computer Name", 
          "IP", "Password PC", "Operating System", "System Manufacturer", 
          "Processor", "Memory", "Display Link", "Sound Link", "Input Link"
        ];
        rows.push(headers);
        pcRecords.forEach(r => {
          const d = r.data as FormPC;
          rows.push([
            r.timestamp, d.nama, d.departemen, d.jabatan, d.computerName,
            d.ip, d.passwordPc || '-', d.os, d.manufacturer || '-',
            d.processor, d.memory, d.displayLink || '-', d.soundLink || '-', d.inputLink || '-'
          ]);
        });
        filename = `syamanah_spesifikasi_pc_${new Date().toISOString().slice(0, 10)}.csv`;
      }
    }

    if (typeToExport === 'akun' && rows.length === 0) {
      const akunRecords = records.filter(r => r.type === 'akun');
      const headers = [
        "Timestamp", "Nama", "Kategori Tim", "Jenis HP", "Bekas HP",
        "Akun Login PC", "Email 1", "Password Email 1", "Email 2", "Password Email 2",
        "Email 3", "Password Email 3", "IG 1", "Pass IG 1", "IG 2", "Pass IG 2",
        "IG 3", "Pass IG 3", "FB 1", "Pass FB 1", "FB 2", "Pass FB 2",
        "FB 3", "Pass FB 3", "No WhatsApp", "Akun Lain", "Software Lain", "Catatan"
      ];
      rows.push(headers);
      akunRecords.forEach(r => {
        const d = r.data as FormAkun;
        rows.push([
          r.timestamp, d.nama, d.kategori, d.jenisHp || '-', d.bekasHp || '-',
          d.akunLoginPc || '-', d.email1 || '-', d.passEmail1 || '-', d.email2 || '-', d.passEmail2 || '-',
          d.email3 || '-', d.passEmail3 || '-', d.ig1 || '-', d.passIg1 || '-', d.ig2 || '-', d.passIg2 || '-',
          d.ig3 || '-', d.passIg3 || '-', d.fb1 || '-', d.passFb1 || '-', d.fb2 || '-', d.passFb2 || '-',
          d.fb3 || '-', d.passFb3 || '-', d.noWa, d.akunLain || '-', d.softwareLain || '-', d.catatan || '-'
        ]);
      });
      filename = `syamanah_akun_sales_${new Date().toISOString().slice(0, 10)}.csv`;
    }

    if (rows.length === 0) {
      alert('Tidak ada data yang sesuai untuk diekspor!');
      return;
    }

    const csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(val => `"${String(val).replace(/"/g, '""')}"`).join(",")).join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const copyRecordJson = (rec: SubmissionRecord) => {
    navigator.clipboard.writeText(JSON.stringify(rec, null, 2));
    setCopiedId(rec.id);
    setTimeout(() => setCopiedId(null), 2000);
  };

  const pcCount = records.filter(r => r.type === 'pc').length;
  const akunCount = records.filter(r => r.type === 'akun').length;
  const syncedCount = records.filter(r => r.syncedToGoogle).length;

  return (
    <div className="space-y-6">
      {/* Overview Cards */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
          <span className="text-xs text-slate-500 font-medium block">Total Catatan</span>
          <span className="text-2xl font-extrabold text-slate-900 mt-1 block">{records.length}</span>
          <span className="text-[11px] text-slate-400 mt-0.5 block">Tersimpan di sistem</span>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
          <span className="text-xs text-emerald-700 font-medium block">Spesifikasi PC</span>
          <span className="text-2xl font-extrabold text-emerald-700 mt-1 block">{pcCount}</span>
          <span className="text-[11px] text-emerald-600/70 mt-0.5 block">Perangkat terdata</span>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
          <span className="text-xs text-teal-700 font-medium block">Akun &amp; Device</span>
          <span className="text-2xl font-extrabold text-teal-700 mt-1 block">{akunCount}</span>
          <span className="text-[11px] text-teal-600/70 mt-0.5 block">Kredensial staf</span>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
          <span className="text-xs text-blue-700 font-medium block">Tersinkron Spreadsheet</span>
          <span className="text-2xl font-extrabold text-blue-700 mt-1 block">{syncedCount}</span>
          <span className="text-[11px] text-blue-600/70 mt-0.5 block">
            {records.length > 0 ? `${Math.round((syncedCount / records.length) * 100)}% dari total` : '0%'}
          </span>
        </div>
      </div>

      {/* Control Bar */}
      <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        {/* Search & Filter */}
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Cari berdasarkan nama staf, departemen, role, computer name..."
              className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
            />
          </div>

          <div className="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 shrink-0">
            <button
              onClick={() => setFilterType('all')}
              className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                filterType === 'all'
                  ? 'bg-white text-slate-800 shadow-2xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Semua ({records.length})
            </button>
            <button
              onClick={() => setFilterType('pc')}
              className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                filterType === 'pc'
                  ? 'bg-emerald-600 text-white shadow-2xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              PC ({pcCount})
            </button>
            <button
              onClick={() => setFilterType('akun')}
              className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                filterType === 'akun'
                  ? 'bg-teal-600 text-white shadow-2xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Akun ({akunCount})
            </button>
          </div>
        </div>

        {/* Export and Clear Actions */}
        <div className="flex flex-wrap items-center gap-2 shrink-0">
          {/* Sync PC / Laptop Button */}
          <div className="relative inline-block">
            <button
              onClick={handleSyncAllPC}
              disabled={syncingAllPC || pcCount === 0}
              className="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold flex items-center gap-1.5 transition disabled:opacity-50 cursor-pointer shadow-2xs"
              title="Sinkronkan seluruh data spesifikasi PC/laptop ke tab Catatan Spesifikasi Perangkat PC di Spreadsheet"
            >
              <RefreshCw className={`w-3.5 h-3.5 ${syncingAllPC ? 'animate-spin' : ''}`} />
              <span>{syncingAllPC ? 'Menyinkronkan PC...' : `Sinkronkan ${pcCount} PC ke Sheet`}</span>
            </button>
          </div>

          {/* Sync Akun Button */}
          <div className="relative inline-block">
            <button
              onClick={handleSyncAllAkun}
              disabled={syncingAllAkun || akunCount === 0}
              className="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold flex items-center gap-1.5 transition disabled:opacity-50 cursor-pointer shadow-2xs"
              title="Sinkronkan seluruh data akun sales ke tab Form Pencatatan Akun di Spreadsheet"
            >
              <RefreshCw className={`w-3.5 h-3.5 ${syncingAllAkun ? 'animate-spin' : ''}`} />
              <span>{syncingAllAkun ? 'Menyinkronkan Akun...' : `Sinkronkan ${akunCount} Akun ke Sheet`}</span>
            </button>
          </div>

          <div className="relative inline-block">
            <button
              onClick={() => exportToCSV(filterType)}
              disabled={filteredRecords.length === 0}
              className="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-semibold flex items-center gap-1.5 transition disabled:opacity-50"
            >
              <Download className="w-3.5 h-3.5 text-emerald-700" />
              <span>Ekspor CSV</span>
            </button>
          </div>

          {records.length > 0 && (
            <button
              onClick={handleClearAll}
              className="px-3 py-2 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 text-xs font-semibold flex items-center gap-1.5 transition"
            >
              <Trash2 className="w-3.5 h-3.5 text-rose-600" />
              <span>Kosongkan Riwayat</span>
            </button>
          )}
        </div>
      </div>

      {syncFeedback && (
        <div className="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs flex items-center justify-between">
          <div className="flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4 text-teal-600 shrink-0" />
            <span>{syncFeedback}</span>
          </div>
          <button
            onClick={() => setSyncFeedback(null)}
            className="text-xs text-teal-700 hover:underline font-semibold"
          >
            Tutup
          </button>
        </div>
      )}

      {/* Records Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {filteredRecords.length === 0 ? (
          <div className="p-12 text-center space-y-3">
            <div className="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
              <History className="w-6 h-6" />
            </div>
            <h4 className="text-base font-bold text-slate-800">Belum Ada Data</h4>
            <p className="text-xs text-slate-500 max-w-sm mx-auto">
              {searchTerm
                ? 'Tidak ada data yang cocok dengan kata kunci pencarian Anda.'
                : 'Data yang dikirim melalui formulir Spesifikasi PC dan formulir Akun & Handphone akan otomatis terarsip di sini.'}
            </p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="bg-slate-50/80 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold text-[11px]">
                  <th className="py-3 px-4">Tipe &amp; Staf</th>
                  <th className="py-3 px-4">Departemen / Role</th>
                  <th className="py-3 px-4">Detail Perangkat</th>
                  <th className="py-3 px-4">Status Sinkronisasi</th>
                  <th className="py-3 px-4">Waktu</th>
                  <th className="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredRecords.map((rec) => {
                  const isPC = rec.type === 'pc';
                  const d = rec.data as FormPC & FormAkun;

                  return (
                    <tr key={rec.id} className="hover:bg-slate-50/70 transition">
                      {/* Name & Type */}
                      <td className="py-3.5 px-4">
                        <div className="flex items-center gap-2.5">
                          <div
                            className={`w-7 h-7 rounded-lg flex items-center justify-center shrink-0 ${
                              isPC
                                ? 'bg-emerald-100 text-emerald-800'
                                : 'bg-teal-100 text-teal-800'
                            }`}
                          >
                            {isPC ? <Monitor className="w-4 h-4" /> : <Smartphone className="w-4 h-4" />}
                          </div>
                          <div>
                            <span className="font-bold text-slate-900 block">{d.nama || '-'}</span>
                            <span className="text-[10px] text-slate-500 uppercase tracking-wider">
                              {isPC ? 'Spesifikasi PC' : 'Akun & Device'}
                            </span>
                          </div>
                        </div>
                      </td>

                      {/* Department / Role */}
                      <td className="py-3.5 px-4">
                        <span className="font-medium text-slate-700 block">
                          {isPC ? d.departemen || '-' : d.kategori || '-'}
                        </span>
                        <span className="text-[11px] text-slate-500">
                          {isPC ? d.jabatan || '-' : d.noWa || '-'}
                        </span>
                      </td>

                      {/* Device Detail */}
                      <td className="py-3.5 px-4">
                        {isPC ? (
                          <div>
                            <span className="font-mono text-slate-800 font-semibold block">
                              {d.computerName || '-'}
                            </span>
                            <span className="text-[11px] text-slate-500">
                              {d.os || d.processor || '-'}
                            </span>
                          </div>
                        ) : (
                          <div>
                            <span className="text-slate-800 font-medium block">
                              {d.jenisHp || 'Handphone Kantor'}
                            </span>
                            <span className="text-[11px] text-slate-500">
                              {d.email1 ? `Email: ${d.email1}` : d.akunLoginPc ? 'Ada akun login PC' : 'Tidak ada email'}
                            </span>
                          </div>
                        )}
                      </td>

                      {/* Sync Status */}
                      <td className="py-3.5 px-4">
                        {rec.syncedToGoogle ? (
                          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <CheckCircle2 className="w-3.5 h-3.5" />
                            <span>Spreadsheet</span>
                          </span>
                        ) : (
                          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            <Clock className="w-3.5 h-3.5" />
                            <span>Lokal</span>
                          </span>
                        )}
                      </td>

                      {/* Timestamp */}
                      <td className="py-3.5 px-4 text-slate-500 text-[11px]">
                        {rec.timestamp}
                      </td>

                      {/* Actions */}
                      <td className="py-3.5 px-4 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => setSelectedRecord(rec)}
                            title="Lihat Detail Lengkap (Read)"
                            className="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-slate-100 rounded-lg transition"
                          >
                            <Eye className="w-4 h-4" />
                          </button>

                          <button
                            onClick={() => setEditRecord(rec)}
                            title="Edit Data (Update)"
                            className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                          >
                            <Pencil className="w-4 h-4" />
                          </button>

                          {!rec.syncedToGoogle && (
                            <button
                              onClick={() => handleReSync(rec)}
                              disabled={syncingId === rec.id}
                              title="Sinkronkan ke Google Spreadsheet"
                              className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                            >
                              <RefreshCw
                                className={`w-4 h-4 ${syncingId === rec.id ? 'animate-spin' : ''}`}
                              />
                            </button>
                          )}

                          <button
                            onClick={() => handleDelete(rec.id, d.nama)}
                            title="Hapus Catatan"
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
        )}
      </div>

      {/* Record Detail Modal */}
      {selectedRecord && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden max-h-[85vh] flex flex-col">
            {/* Modal Header */}
            <div className="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div
                  className={`w-9 h-9 rounded-xl flex items-center justify-center ${
                    selectedRecord.type === 'pc'
                      ? 'bg-emerald-600 text-white'
                      : 'bg-teal-600 text-white'
                  }`}
                >
                  {selectedRecord.type === 'pc' ? (
                    <Monitor className="w-5 h-5" />
                  ) : (
                    <Smartphone className="w-5 h-5" />
                  )}
                </div>
                <div>
                  <h3 className="font-bold text-sm text-white">
                    Detail Data {selectedRecord.type === 'pc' ? 'Spesifikasi PC' : 'Akun & Handphone'}
                  </h3>
                  <p className="text-xs text-slate-400">
                    ID: {selectedRecord.id} &bull; {selectedRecord.timestamp}
                  </p>
                </div>
              </div>
              <button
                onClick={() => setSelectedRecord(null)}
                className="p-1.5 text-slate-400 hover:text-white rounded-lg transition"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Modal Body */}
            <div className="p-6 overflow-y-auto space-y-4 text-xs">
              <div className="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                <div>
                  <span className="text-slate-500 block text-[11px]">Status Sinkronisasi</span>
                  <span className="font-semibold text-slate-800">
                    {selectedRecord.syncedToGoogle
                      ? 'Tersinkron ke Google Spreadsheet'
                      : 'Tersimpan di Memori Lokal'}
                  </span>
                  {selectedRecord.syncMessage && (
                    <span className="text-[11px] text-slate-500 block mt-0.5">
                      {selectedRecord.syncMessage}
                    </span>
                  )}
                </div>
                <button
                  onClick={() => copyRecordJson(selectedRecord)}
                  className="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 flex items-center gap-1.5"
                >
                  {copiedId === selectedRecord.id ? (
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                  ) : (
                    <Copy className="w-3.5 h-3.5" />
                  )}
                  <span>{copiedId === selectedRecord.id ? 'Tersalin' : 'Salin JSON'}</span>
                </button>
              </div>

              {/* Data Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {Object.entries(selectedRecord.data).map(([key, val]) => {
                  if (!val) return null;
                  return (
                    <div
                      key={key}
                      className="p-2.5 rounded-xl border border-slate-100 bg-slate-50/50"
                    >
                      <span className="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">
                        {key}
                      </span>
                      <span className="text-xs text-slate-800 font-medium break-all select-all block mt-0.5">
                        {String(val)}
                      </span>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Modal Footer */}
            <div className="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
              <button
                onClick={() => setSelectedRecord(null)}
                className="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Confirm Delete Single Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        title="Konfirmasi Hapus Catatan"
        itemName={deleteTarget?.name || ''}
        itemType="catatan riwayat"
        onConfirm={confirmSingleDelete}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Confirm Clear All Modal */}
      <ConfirmDeleteModal
        isOpen={isClearAllOpen}
        title="Kosongkan Semua Data Riwayat"
        itemName="Seluruh Catatan Inventaris Lokal"
        itemType="seluruh koleksi"
        onConfirm={confirmClearAll}
        onCancel={() => setIsClearAllOpen(false)}
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
