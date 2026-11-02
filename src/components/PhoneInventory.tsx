import React, { useState } from 'react';
import {
  Smartphone,
  Search,
  Filter,
  Download,
  Eye,
  Trash2,
  Pencil,
  Plus,
  MessageCircle,
  Mail,
  Instagram,
  Facebook,
  ShieldCheck,
  User,
  Phone,
  Layers,
  Sparkles,
  ExternalLink,
  CheckCircle2,
  Tag,
  ArrowRight,
  Clock,
  EyeOff,
  Building2,
  Lock
} from 'lucide-react';
import { SubmissionRecord, FormAkun } from '../types';
import { deleteSubmission } from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';

interface PhoneInventoryProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenNewPhoneForm: () => void;
}

export const PhoneInventory: React.FC<PhoneInventoryProps> = ({
  records,
  onRefresh,
  onOpenNewPhoneForm
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [teamFilter, setTeamFilter] = useState<string>('all');
  const [brandFilter, setBrandFilter] = useState<string>('all');
  const [viewMode, setViewMode] = useState<'card' | 'table'>('card');
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
  const [showCredentials, setShowCredentials] = useState<Record<string, boolean>>({});

  // Filter ONLY Akun & Smartphone records
  const phoneRecords = records.filter((r) => r.type === 'akun');

  const filteredRecords = phoneRecords.filter((rec) => {
    const data = rec.data as FormAkun;
    const team = (data.kategori || '').toLowerCase();
    const phoneModel = (data.jenisHp || '').toLowerCase();

    if (teamFilter !== 'all' && !team.includes(teamFilter.toLowerCase())) return false;
    if (brandFilter !== 'all' && !phoneModel.includes(brandFilter.toLowerCase())) return false;

    if (!searchTerm.trim()) return true;

    const term = searchTerm.toLowerCase();
    const nameMatch = (data.nama || '').toLowerCase().includes(term);
    const teamMatch = team.includes(term);
    const hpMatch = phoneModel.includes(term);
    const waMatch = String(data.noWa || '').includes(term);
    const emailMatch = (data.email1 || '').toLowerCase().includes(term) || (data.email2 || '').toLowerCase().includes(term);
    const igMatch = (data.ig1 || '').toLowerCase().includes(term) || (data.ig2 || '').toLowerCase().includes(term);

    return nameMatch || teamMatch || hpMatch || waMatch || emailMatch || igMatch;
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

  // Helper to format clean WhatsApp link
  const getWhatsAppLink = (rawWa: string | number) => {
    if (!rawWa) return null;
    let clean = String(rawWa).replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) {
      clean = '62' + clean.slice(1);
    }
    return `https://wa.me/${clean}`;
  };

  // Stats
  const totalPhones = phoneRecords.length;
  const brandSamsung = phoneRecords.filter((r) => ((r.data as FormAkun).jenisHp || '').toLowerCase().includes('samsung')).length;
  const brandRedmi = phoneRecords.filter((r) => ((r.data as FormAkun).jenisHp || '').toLowerCase().includes('redmi') || ((r.data as FormAkun).jenisHp || '').toLowerCase().includes('xiaomi')).length;
  const brandOppo = phoneRecords.filter((r) => ((r.data as FormAkun).jenisHp || '').toLowerCase().includes('oppo')).length;
  const withWa = phoneRecords.filter((r) => Boolean((r.data as FormAkun).noWa)).length;

  const exportPhoneCSV = () => {
    if (phoneRecords.length === 0) {
      alert('Belum ada data smartphone untuk diekspor!');
      return;
    }

    const headers = [
      'Timestamp',
      'Nama Staf',
      'Kategori Tim / Role',
      'Jabatan',
      'Jenis Handphone',
      'Bekas HP Siapa',
      'Nomor WhatsApp',
      'Akun Login di PC',
      'Email 1',
      'Password Email 1',
      'Email 2',
      'Password Email 2',
      'Instagram 1',
      'Password IG 1',
      'Facebook 1',
      'Password FB 1',
      'Catatan'
    ];

    const rows = phoneRecords.map((r) => {
      const d = r.data as FormAkun;
      return [
        r.timestamp,
        d.nama,
        d.kategori,
        d.jabatan || '-',
        d.jenisHp,
        d.bekasHp || 'Baru',
        d.noWa || '-',
        d.akunLoginPc || '-',
        d.email1 || '-',
        d.passEmail1 || '-',
        d.email2 || '-',
        d.passEmail2 || '-',
        d.ig1 || '-',
        d.passIg1 || '-',
        d.fb1 || '-',
        d.passFb1 || '-',
        d.catatan || '-'
      ];
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers, ...rows].map((e) => e.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(',')).join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `Inventaris_Smartphone_Sales_Syamanah_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="space-y-6">
      {/* Syamanah Header / Hero Banner for Smartphones */}
      <div className="bg-gradient-to-br from-[#0B1B3D] via-[#0F2B5C] to-[#08142C] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-amber-500/30">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-2xl">
            <div className="flex items-center gap-2 text-xs text-amber-300 font-semibold uppercase tracking-wider">
              <span>Syamanah Apparel Indonesia</span>
              <span>·</span>
              <span>Divisi Sales &amp; CRM</span>
              <span>·</span>
              <span className="text-white font-bold">{totalPhones} Smartphone Terdaftar</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              Katalog Handphone &amp; Akun Sales Syamanah
            </h2>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              Pencatatan unit smartphone operasional tim Sales Jaket, Jersey, Eksekutif, dan CRM. Dilengkapi rincian merk HP, nomor WhatsApp, akun email, Instagram official, dan riwayat mutasi fisik perangkat.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={onOpenNewPhoneForm}
              className="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs flex items-center gap-2 shadow-lg transition active:scale-95"
            >
              <Plus className="w-4 h-4 stroke-[3]" />
              <span>+ Input Smartphone &amp; Akun Baru</span>
            </button>

            <button
              onClick={exportPhoneCSV}
              className="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs flex items-center gap-2 transition active:scale-95"
            >
              <Download className="w-4 h-4 text-amber-400" />
              <span>Ekspor CSV</span>
            </button>
          </div>
        </div>

        {/* Syamanah Stats Counter Strip */}
        <div className="mt-6 pt-6 border-t border-slate-700/60 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Total Handphone</span>
            <span className="text-2xl font-black text-amber-400 mt-0.5 block">{totalPhones} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Di seluruh tim sales</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Nomor WhatsApp Aktif</span>
            <span className="text-2xl font-black text-emerald-400 mt-0.5 block">{withWa} Kontak</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Tersambung ke pelanggan</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Samsung Galaxy Series</span>
            <span className="text-2xl font-black text-blue-400 mt-0.5 block">{brandSamsung} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Unit standar kantor</span>
          </div>

          <div className="bg-slate-900/60 border border-slate-700/60 rounded-2xl p-3.5">
            <span className="text-slate-400 font-medium block">Xiaomi / Redmi &amp; Oppo</span>
            <span className="text-2xl font-black text-purple-400 mt-0.5 block">{brandRedmi + brandOppo} Unit</span>
            <span className="text-[11px] text-slate-400 mt-0.5 block">Varian staf operasional</span>
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
              placeholder="Cari nama staf sales, nomor WhatsApp, jenis HP, akun email/IG..."
              className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"
            />
          </div>

          {/* Tim Filter Select */}
          <select
            value={teamFilter}
            onChange={(e) => setTeamFilter(e.target.value)}
            className="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold bg-slate-50 text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
          >
            <option value="all">Semua Tim Sales ({totalPhones})</option>
            <option value="jaket">Tim Jaket</option>
            <option value="jersey">Tim Jersey</option>
            <option value="crm">Tim CRM</option>
            <option value="eksekutif">Tim Eksekutif</option>
          </select>

          {/* Merk Filter Select */}
          <select
            value={brandFilter}
            onChange={(e) => setBrandFilter(e.target.value)}
            className="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold bg-slate-50 text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
          >
            <option value="all">Semua Merk HP</option>
            <option value="samsung">Samsung</option>
            <option value="redmi">Redmi / Xiaomi</option>
            <option value="oppo">Oppo</option>
            <option value="vivo">Vivo</option>
          </select>
        </div>

        {/* View Mode Toggle: Cards vs Table */}
        <div className="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-end sm:self-auto border border-slate-200">
          <button
            onClick={() => setViewMode('card')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
              viewMode === 'card'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Grid Smartphone
          </button>
          <button
            onClick={() => setViewMode('table')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition ${
              viewMode === 'table'
                ? 'bg-white text-slate-900 shadow-xs'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Tabel Ringkas
          </button>
        </div>
      </div>

      {/* Main Smartphone List */}
      {filteredRecords.length === 0 ? (
        <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
          <div className="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center">
            <Smartphone className="w-7 h-7" />
          </div>
          <h4 className="text-base font-bold text-slate-900">Tidak Ada Data Smartphone Ditemukan</h4>
          <p className="text-xs text-slate-500 max-w-sm mx-auto">
            {searchTerm
              ? 'Tidak ada perangkat HP yang cocok dengan pencarian Anda.'
              : 'Belum ada data smartphone tim sales. Klik "+ Input Smartphone & Akun Baru" untuk mendaftarkan unit.'}
          </p>
        </div>
      ) : viewMode === 'card' ? (
        /* CARD GRID: Specially designed for Handphones with Phone Frame Aesthetics */
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filteredRecords.map((rec) => {
            const d = rec.data as FormAkun;
            const waUrl = getWhatsAppLink(d.noWa);
            const isCredentialsOpen = Boolean(showCredentials[rec.id]);

            return (
              <div
                key={rec.id}
                className="bg-white rounded-3xl border border-slate-200 hover:border-amber-400/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group"
              >
                {/* Card Phone Top Bar */}
                <div className="p-5 border-b border-slate-100 space-y-3">
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                      <div className="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-700 border border-amber-500/20 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                        <Smartphone className="w-5 h-5" />
                      </div>
                      <div>
                        <h3 className="font-extrabold text-sm text-slate-900 leading-snug">
                          {d.nama}
                        </h3>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                          <span>{d.kategori || 'Tim Sales'}</span>
                          {d.jabatan && (
                            <>
                              <span className="mx-1">·</span>
                              <span>{d.jabatan}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <span className="text-[10px] font-mono text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 shrink-0">
                      ID: {rec.id.replace('syam_form_akun_', '#')}
                    </span>
                  </div>

                  {/* Phone Model Box */}
                  <div className="p-3 bg-slate-50/80 rounded-2xl border border-slate-200/60 space-y-1">
                    <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                      Unit Handphone:
                    </div>
                    <div className="font-bold text-xs text-slate-800 leading-tight">
                      {d.jenisHp || 'Spesifikasi HP Belum Diisi'}
                    </div>
                    {d.bekasHp && (
                      <div className="text-[11px] text-amber-700/90 font-medium">
                        Status: {d.bekasHp}
                      </div>
                    )}
                  </div>
                </div>

                {/* Card Middle: WhatsApp & Accounts */}
                <div className="p-5 space-y-3 flex-1 text-xs">
                  {/* WhatsApp Quick Action */}
                  {d.noWa ? (
                    <div className="flex items-center justify-between p-2.5 bg-emerald-50/60 border border-emerald-200/80 rounded-2xl">
                      <div className="flex items-center gap-2">
                        <MessageCircle className="w-4 h-4 text-emerald-600 shrink-0" />
                        <div>
                          <span className="text-[10px] text-slate-500 block">WhatsApp Sales:</span>
                          <span className="font-mono font-bold text-emerald-950 text-xs">
                            {d.noWa}
                          </span>
                        </div>
                      </div>

                      {waUrl && (
                        <a
                          href={waUrl}
                          target="_blank"
                          rel="noreferrer"
                          className="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[11px] font-bold flex items-center gap-1 shadow-2xs transition active:scale-95"
                        >
                          <span>Chat WA</span>
                          <ExternalLink className="w-3 h-3" />
                        </a>
                      )}
                    </div>
                  ) : (
                    <div className="text-[11px] text-slate-400 italic">
                      Nomor WhatsApp belum tercatat
                    </div>
                  )}

                  {/* Connected Accounts Quick Peek */}
                  <div className="space-y-1.5 pt-1">
                    <div className="flex items-center justify-between">
                      <span className="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                        <Lock className="w-3 h-3 text-slate-400" />
                        <span>Kredensial Akun:</span>
                      </span>
                      <button
                        type="button"
                        onClick={() =>
                          setShowCredentials((prev) => ({
                            ...prev,
                            [rec.id]: !prev[rec.id]
                          }))
                        }
                        className="text-[11px] font-semibold text-amber-600 hover:text-amber-700"
                      >
                        {isCredentialsOpen ? 'Sembunyikan' : 'Buka Kredensial'}
                      </button>
                    </div>

                    {isCredentialsOpen ? (
                      <div className="p-3 bg-slate-900 text-white rounded-2xl font-mono text-[11px] space-y-2 border border-slate-800">
                        {d.email1 && (
                          <div>
                            <span className="text-slate-400 block text-[10px]">Email 1:</span>
                            <span className="text-emerald-300 font-bold">{d.email1}</span>
                            {d.passEmail1 && (
                              <span className="text-slate-300 block text-[10px]">Pass: {d.passEmail1}</span>
                            )}
                          </div>
                        )}
                        {d.ig1 && (
                          <div className="pt-1 border-t border-slate-800">
                            <span className="text-slate-400 block text-[10px]">Instagram:</span>
                            <span className="text-pink-300 font-bold">@{d.ig1}</span>
                            {d.passIg1 && (
                              <span className="text-slate-300 block text-[10px]">Pass: {d.passIg1}</span>
                            )}
                          </div>
                        )}
                        {d.fb1 && (
                          <div className="pt-1 border-t border-slate-800">
                            <span className="text-slate-400 block text-[10px]">Facebook:</span>
                            <span className="text-blue-300 font-bold">{d.fb1}</span>
                          </div>
                        )}
                        {d.akunLoginPc && (
                          <div className="pt-1 border-t border-slate-800">
                            <span className="text-slate-400 block text-[10px]">Login PC:</span>
                            <span className="text-slate-300">{d.akunLoginPc}</span>
                          </div>
                        )}
                      </div>
                    ) : (
                      <div className="text-[11px] text-slate-500 truncate">
                        {d.email1 ? `Email: ${d.email1}` : d.ig1 ? `IG: @${d.ig1}` : 'Belum ada email'}
                      </div>
                    )}
                  </div>
                </div>

                {/* Card Phone Bottom Bar: CRUD Actions */}
                <div className="p-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[10px] text-slate-400 font-medium">
                    Input: {rec.timestamp.split(' ')[0]}
                  </span>

                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={() => setSelectedRecord(rec)}
                      title="Lihat Detail Lengkap (Read)"
                      className="p-2 text-slate-600 hover:text-amber-700 hover:bg-amber-100/50 rounded-xl transition"
                    >
                      <Eye className="w-4 h-4" />
                    </button>

                    <button
                      onClick={() => setEditRecord(rec)}
                      title="Edit Data Smartphone (Update)"
                      className="p-2 text-slate-600 hover:text-blue-700 hover:bg-blue-100/50 rounded-xl transition"
                    >
                      <Pencil className="w-4 h-4" />
                    </button>

                    <button
                      onClick={() => handleDelete(rec.id, d.nama)}
                      title="Hapus Smartphone & Akun (Delete)"
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
        /* TABLE VIEW */
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="bg-slate-50/90 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold text-[11px]">
                  <th className="py-3 px-4">Nama Staf Sales</th>
                  <th className="py-3 px-4">Kategori Tim</th>
                  <th className="py-3 px-4">Merk &amp; Tipe Smartphone</th>
                  <th className="py-3 px-4">Nomor WhatsApp</th>
                  <th className="py-3 px-4">Akun Email &amp; IG</th>
                  <th className="py-3 px-4 text-right">Aksi CRUD</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredRecords.map((rec) => {
                  const d = rec.data as FormAkun;
                  const waUrl = getWhatsAppLink(d.noWa);

                  return (
                    <tr key={rec.id} className="hover:bg-slate-50/80 transition">
                      <td className="py-3 px-4">
                        <span className="font-bold text-slate-900 block">{d.nama}</span>
                        <span className="text-[10px] text-slate-400">{d.jabatan || 'Staf Sales'}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="text-slate-800 font-semibold">{d.kategori}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-medium text-slate-800 block">{d.jenisHp}</span>
                        <span className="text-[10px] text-amber-700">{d.bekasHp || 'Baru'}</span>
                      </td>

                      <td className="py-3 px-4 font-mono">
                        {d.noWa ? (
                          <div className="flex items-center gap-1.5">
                            <span>{d.noWa}</span>
                            {waUrl && (
                              <a
                                href={waUrl}
                                target="_blank"
                                rel="noreferrer"
                                className="text-emerald-600 hover:text-emerald-700 p-1"
                                title="Buka WhatsApp"
                              >
                                <ExternalLink className="w-3.5 h-3.5" />
                              </a>
                            )}
                          </div>
                        ) : (
                          <span className="text-slate-400">-</span>
                        )}
                      </td>

                      <td className="py-3 px-4">
                        <span className="text-slate-700 block truncate max-w-xs">{d.email1 || '-'}</span>
                        {d.ig1 && <span className="text-[11px] text-pink-600 block">@{d.ig1}</span>}
                      </td>

                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => setSelectedRecord(rec)}
                            title="Lihat Detail (Read)"
                            className="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-slate-100 rounded-lg transition"
                          >
                            <Eye className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => setEditRecord(rec)}
                            title="Edit Data (Update)"
                            className="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition"
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
                <div className="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold">
                  <Smartphone className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="font-bold text-base">
                    {(selectedRecord.data as FormAkun).nama}
                  </h3>
                  <p className="text-xs text-amber-300">
                    {(selectedRecord.data as FormAkun).kategori} &bull; ID: {selectedRecord.id}
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
              {/* Unit Info */}
              <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2">
                <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                  Spesifikasi Fisik Handphone
                </div>
                <div className="text-sm font-bold text-slate-900">
                  {(selectedRecord.data as FormAkun).jenisHp}
                </div>
                <div className="text-xs text-slate-600">
                  Status Perolehan: <strong>{(selectedRecord.data as FormAkun).bekasHp || 'Baru'}</strong>
                </div>
                {(selectedRecord.data as FormAkun).noWa && (
                  <div className="text-xs text-slate-600">
                    No WhatsApp: <strong>{(selectedRecord.data as FormAkun).noWa}</strong>
                  </div>
                )}
              </div>

              {/* Credentials */}
              <div className="p-4 bg-slate-900 text-white rounded-2xl space-y-2 font-mono text-[11px]">
                <div className="text-amber-400 font-bold uppercase text-[10px] tracking-wider">
                  Kredensial &amp; Akun Login
                </div>
                {(selectedRecord.data as FormAkun).email1 && (
                  <div>
                    <span className="text-slate-400">Email 1:</span> {(selectedRecord.data as FormAkun).email1} <br />
                    <span className="text-slate-400">Password:</span> {(selectedRecord.data as FormAkun).passEmail1 || '-'}
                  </div>
                )}
                {(selectedRecord.data as FormAkun).email2 && (
                  <div className="pt-2 border-t border-slate-800">
                    <span className="text-slate-400">Email 2:</span> {(selectedRecord.data as FormAkun).email2} <br />
                    <span className="text-slate-400">Password:</span> {(selectedRecord.data as FormAkun).passEmail2 || '-'}
                  </div>
                )}
                {(selectedRecord.data as FormAkun).ig1 && (
                  <div className="pt-2 border-t border-slate-800">
                    <span className="text-slate-400">Instagram:</span> @{(selectedRecord.data as FormAkun).ig1} <br />
                    <span className="text-slate-400">Password:</span> {(selectedRecord.data as FormAkun).passIg1 || '-'}
                  </div>
                )}
                {(selectedRecord.data as FormAkun).fb1 && (
                  <div className="pt-2 border-t border-slate-800">
                    <span className="text-slate-400">Facebook:</span> {(selectedRecord.data as FormAkun).fb1} <br />
                    <span className="text-slate-400">Password:</span> {(selectedRecord.data as FormAkun).passFb1 || '-'}
                  </div>
                )}
                {(selectedRecord.data as FormAkun).akunLoginPc && (
                  <div className="pt-2 border-t border-slate-800">
                    <span className="text-slate-400">Akun di PC:</span> {(selectedRecord.data as FormAkun).akunLoginPc}
                  </div>
                )}
              </div>

              {(selectedRecord.data as FormAkun).catatan && (
                <div className="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs">
                  <span className="font-bold block">Catatan:</span>
                  {(selectedRecord.data as FormAkun).catatan}
                </div>
              )}
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
                <span>Edit Data Ini</span>
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

      {/* Delete Modal */}
      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        itemName={deleteTarget?.name || ''}
        itemType="smartphone milik"
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Edit Modal (CRUD Update) */}
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
