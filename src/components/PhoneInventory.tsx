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
  Lock,
  Copy,
  Check,
  X
} from 'lucide-react';
import { SubmissionRecord, FormAkun, UserAccount } from '../types';
import { deleteSubmission } from '../services/gasService';
import { ConfirmDeleteModal } from './ConfirmDeleteModal';
import { EditRecordModal } from './EditRecordModal';

interface PhoneInventoryProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenNewPhoneForm: () => void;
  currentUser: UserAccount | null;
}

export const PhoneInventory: React.FC<PhoneInventoryProps> = ({
  records,
  onRefresh,
  onOpenNewPhoneForm,
  currentUser
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [teamFilter, setTeamFilter] = useState<string>('all');
  const [brandFilter, setBrandFilter] = useState<string>('all');
  const [viewMode, setViewMode] = useState<'card' | 'table'>('card');
  const [selectedRecord, setSelectedRecord] = useState<SubmissionRecord | null>(null);
  const [editRecord, setEditRecord] = useState<SubmissionRecord | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
  const [showCredentials, setShowCredentials] = useState<Record<string, boolean>>({});
  const [copiedId, setCopiedId] = useState<string | null>(null);

  const isGA = currentUser?.role === 'general_affair';

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
    const emailMatch = !isGA && ((data.email1 || '').toLowerCase().includes(term) || (data.email2 || '').toLowerCase().includes(term));
    const igMatch = !isGA && ((data.ig1 || '').toLowerCase().includes(term) || (data.ig2 || '').toLowerCase().includes(term));

    return nameMatch || teamMatch || hpMatch || waMatch || emailMatch || igMatch;
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

    const headers = isGA
      ? [
          'Timestamp',
          'Nama Staf',
          'Kategori Tim / Divisi',
          'Jabatan',
          'Jenis Handphone',
          'Status Perolehan (Baru/Bekas)',
          'Nomor WhatsApp',
          'Catatan Fisik'
        ]
      : [
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
      if (isGA) {
        return [
          r.timestamp,
          d.nama,
          d.kategori,
          d.jabatan || '-',
          d.jenisHp,
          d.bekasHp || 'Baru',
          d.noWa || '-',
          d.catatan || '-'
        ];
      }
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

    const csvContent =
      'data:text/csv;charset=utf-8,\uFEFF' +
      [headers, ...rows]
        .map((e) => e.map((val) => `"${String(val).replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `Inventaris_Smartphone_${isGA ? 'GA' : 'Sales'}_Syamanah_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="space-y-6">
      {/* Modern Unified Hero Banner with Syamanah Colors */}
      <div className="bg-gradient-to-r from-[#0F1D2E] via-[#163654] to-[#2E5190] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div className="space-y-2 max-w-2xl">
            <div className="flex items-center gap-2 text-xs text-[#3A7D8A] font-bold uppercase tracking-wider">
              <span className="bg-white/10 text-teal-300 px-2.5 py-0.5 rounded-full">
                {isGA ? '🏢 Divisi General Affair' : 'Divisi Sales & CRM'}
              </span>
              <span>·</span>
              <span className="text-white font-medium">{totalPhones} Smartphone Terdaftar</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
              {isGA ? 'Katalog Handphone & Aset Smartphone' : 'Katalog Handphone & Akun Sales'}
            </h1>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              {isGA
                ? 'Pencatatan aset fisik smartphone operasional tim Sales Jaket, Jersey, Eksekutif, dan CRM. Menampilkan tipe unit HP, nama pengguna, kondisi fisik perangkat, dan tim.'
                : 'Pencatatan smartphone operasional tim Sales Jaket, Jersey, Eksekutif, dan CRM. Dilengkapi rincian merk HP, nomor WhatsApp, akun email, Instagram official, dan kredensial perusahaan.'}
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            {!isGA && (
              <button
                onClick={onOpenNewPhoneForm}
                className="px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs flex items-center gap-2 shadow-md transition active:scale-95"
              >
                <Plus className="w-4 h-4" />
                <span>+ Input Smartphone &amp; Akun</span>
              </button>
            )}

            <button
              onClick={exportPhoneCSV}
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
            <span className="text-slate-300 font-medium block text-[11px]">Total Smartphone</span>
            <span className="text-2xl font-black text-white mt-1 block">{totalPhones} Unit</span>
            <span className="text-[10px] text-teal-300 mt-0.5 block">Di seluruh tim</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">WhatsApp Aktif</span>
            <span className="text-2xl font-black text-emerald-400 mt-1 block">{withWa} Kontak</span>
            <span className="text-[10px] text-emerald-200 mt-0.5 block">Hotline konsumen</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Samsung Galaxy</span>
            <span className="text-2xl font-black text-cyan-300 mt-1 block">{brandSamsung} Unit</span>
            <span className="text-[10px] text-cyan-200 mt-0.5 block">Standar sales</span>
          </div>

          <div className="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 hover:bg-white/15 transition">
            <span className="text-slate-300 font-medium block text-[11px]">Xiaomi / Redmi &amp; Oppo</span>
            <span className="text-2xl font-black text-amber-300 mt-1 block">{brandRedmi + brandOppo} Unit</span>
            <span className="text-[10px] text-amber-200 mt-0.5 block">Unit operasional</span>
          </div>
        </div>
      </div>

      {/* Seamless Search and Filters Bar */}
      <div className="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder={isGA ? "Cari nama pemakai, jenis HP, nomor WhatsApp..." : "Cari nama staf sales, WhatsApp, jenis HP, email, IG..."}
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

          {/* Tim Filter Select */}
          <select
            value={teamFilter}
            onChange={(e) => setTeamFilter(e.target.value)}
            className="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-800 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
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
            className="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-800 focus:ring-2 focus:ring-[#3A7D8A] focus:outline-none"
          >
            <option value="all">Semua Merk HP</option>
            <option value="samsung">Samsung</option>
            <option value="redmi">Redmi / Xiaomi</option>
            <option value="oppo">Oppo</option>
            <option value="vivo">Vivo</option>
          </select>
        </div>

        {/* View Toggle */}
        <div className="flex items-center gap-1.5 self-end md:self-auto shrink-0">
          <button
            onClick={() => setViewMode('card')}
            className={`p-2 rounded-xl text-xs font-semibold flex items-center gap-1 transition ${
              viewMode === 'card'
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
            <Smartphone className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Tabel</span>
          </button>
        </div>
      </div>

      {/* Main Smartphone List */}
      {filteredRecords.length === 0 ? (
        <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 space-y-3">
          <div className="w-12 h-12 rounded-2xl bg-teal-50 text-[#3A7D8A] mx-auto flex items-center justify-center">
            <Smartphone className="w-6 h-6" />
          </div>
          <h4 className="text-base font-bold text-slate-900">Tidak Ada Smartphone Ditemukan</h4>
          <p className="text-xs text-slate-500 max-w-sm mx-auto">
            {searchTerm
              ? 'Tidak ada perangkat HP yang cocok dengan kata kunci pencarian Anda.'
              : 'Belum ada data smartphone tim sales.'}
          </p>
        </div>
      ) : viewMode === 'card' ? (
        /* CARD GRID */
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filteredRecords.map((rec) => {
            const d = rec.data as FormAkun;
            const waUrl = getWhatsAppLink(d.noWa);
            const isCredentialsOpen = Boolean(showCredentials[rec.id]);

            return (
              <div
                key={rec.id}
                className="bg-white rounded-2xl border border-slate-200/80 hover:border-[#3A7D8A]/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between overflow-hidden group"
              >
                {/* Card Top: Staff & Sales Team */}
                <div className="p-5 border-b border-slate-100 space-y-3">
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-xl bg-teal-50 text-[#3A7D8A] border border-teal-200/80 flex items-center justify-center shrink-0 group-hover:bg-[#3A7D8A] group-hover:text-white transition-colors">
                        <Smartphone className="w-5 h-5" />
                      </div>
                      <div>
                        <h3 className="font-extrabold text-sm text-[#0F1D2E] leading-snug group-hover:text-[#3A7D8A] transition-colors">
                          {d.nama}
                        </h3>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                          <span>{d.kategori || 'Tim Sales'}</span>
                          {d.jabatan && (
                            <>
                              <span className="mx-1 text-slate-300">·</span>
                              <span>{d.jabatan}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <span className="text-[10px] font-mono text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 shrink-0">
                      #{rec.id.slice(-4)}
                    </span>
                  </div>

                  {/* Phone Model Strip */}
                  <div className="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/60 space-y-0.5">
                    <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                      Unit Perangkat:
                    </div>
                    <div className="font-bold text-xs text-[#0F1D2E] leading-tight">
                      {d.jenisHp || 'Spesifikasi HP Belum Diisi'}
                    </div>
                    {d.bekasHp && (
                      <div className="text-[11px] text-teal-800 font-medium">
                        Status: {d.bekasHp}
                      </div>
                    )}
                  </div>
                </div>

                {/* Card Middle: WhatsApp & Accounts */}
                <div className="p-5 space-y-3 flex-1 text-xs">
                  {/* WhatsApp Quick Action */}
                  {d.noWa ? (
                    <div className="flex items-center justify-between p-2.5 bg-emerald-50/60 border border-emerald-200/80 rounded-xl">
                      <div className="flex items-center gap-2">
                        <MessageCircle className="w-4 h-4 text-emerald-600 shrink-0" />
                        <div>
                          <span className="text-[10px] text-slate-500 block">WhatsApp:</span>
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
                          className="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 shadow-2xs transition active:scale-95"
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

                  {/* Connected Accounts Section: Strictly Hidden / Protected for General Affair */}
                  {isGA ? (
                    <div className="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between text-xs">
                      <div className="flex items-center gap-2 text-slate-500">
                        <Lock className="w-3.5 h-3.5 text-slate-400" />
                        <span>Kredensial Akun</span>
                      </div>
                      <span className="text-[10px] text-amber-800 bg-amber-50 px-2 py-0.5 rounded font-semibold border border-amber-200/60">
                        Terproteksi (GA)
                      </span>
                    </div>
                  ) : (
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
                          className="text-[11px] font-semibold text-[#3A7D8A] hover:underline"
                        >
                          {isCredentialsOpen ? 'Sembunyikan' : 'Buka Kredensial'}
                        </button>
                      </div>

                      {isCredentialsOpen ? (
                        <div className="p-3 bg-slate-900 text-white rounded-xl font-mono text-[11px] space-y-2 border border-slate-800">
                          {d.email1 && (
                            <div>
                              <span className="text-slate-400 block text-[10px]">Email 1:</span>
                              <div className="flex items-center justify-between">
                                <span className="text-emerald-300 font-bold truncate max-w-[170px]">{d.email1}</span>
                                <button
                                  onClick={() => handleCopy(d.email1!, `em_${rec.id}`)}
                                  className="text-slate-400 hover:text-slate-200 p-0.5"
                                  title="Salin Email"
                                >
                                  {copiedId === `em_${rec.id}` ? (
                                    <Check className="w-3 h-3 text-emerald-400" />
                                  ) : (
                                    <Copy className="w-3 h-3" />
                                  )}
                                </button>
                              </div>
                              {d.passEmail1 && (
                                <span className="text-slate-300 block text-[10px]">Pass: {d.passEmail1}</span>
                              )}
                            </div>
                          )}
                          {d.ig1 && (
                            <div className="pt-1.5 border-t border-slate-800">
                              <span className="text-slate-400 block text-[10px]">Instagram:</span>
                              <span className="text-pink-300 font-bold">@{d.ig1}</span>
                              {d.passIg1 && (
                                <span className="text-slate-300 block text-[10px]">Pass: {d.passIg1}</span>
                              )}
                            </div>
                          )}
                          {d.akunLoginPc && (
                            <div className="pt-1.5 border-t border-slate-800">
                              <span className="text-slate-400 block text-[10px]">Login di PC:</span>
                              <span className="text-slate-300">{d.akunLoginPc}</span>
                            </div>
                          )}
                        </div>
                      ) : (
                        <div className="p-2.5 bg-slate-50 border border-slate-200/60 rounded-xl text-slate-500 text-[11px] truncate">
                          {d.email1 ? `Email: ${d.email1}` : d.ig1 ? `IG: @${d.ig1}` : 'Kredensial belum tercatat'}
                        </div>
                      )}
                    </div>
                  )}

                  {/* Notes / Catatan Fisik */}
                  {d.catatan && (
                    <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-200/60 text-[11px] text-slate-600">
                      <span className="font-semibold text-slate-800">Catatan: </span>
                      <span className="italic">{d.catatan}</span>
                    </div>
                  )}
                </div>

                {/* Card Bottom Bar */}
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

                    {!isGA && (
                      <>
                        <button
                          onClick={() => setEditRecord(rec)}
                          title="Edit Data"
                          className="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                        >
                          <Pencil className="w-3.5 h-3.5" />
                        </button>

                        <button
                          onClick={() => handleDelete(rec.id, d.nama)}
                          title="Hapus Data"
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
                  <th className="py-3 px-4">Nama Staf Sales</th>
                  <th className="py-3 px-4">Kategori Tim</th>
                  <th className="py-3 px-4">Merk &amp; Tipe Smartphone</th>
                  <th className="py-3 px-4">Nomor WhatsApp</th>
                  {isGA ? (
                    <th className="py-3 px-4">Catatan Fisik</th>
                  ) : (
                    <th className="py-3 px-4">Akun Email &amp; IG</th>
                  )}
                  <th className="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredRecords.map((rec) => {
                  const d = rec.data as FormAkun;
                  const waUrl = getWhatsAppLink(d.noWa);

                  return (
                    <tr key={rec.id} className="hover:bg-slate-50/80 transition">
                      <td className="py-3 px-4">
                        <span className="font-bold text-[#0F1D2E] block">{d.nama}</span>
                        <span className="text-[10px] text-slate-400">{d.jabatan || 'Staf Sales'}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="text-slate-800 font-semibold">{d.kategori}</span>
                      </td>

                      <td className="py-3 px-4">
                        <span className="font-medium text-slate-800 block">{d.jenisHp}</span>
                        <span className="text-[10px] text-teal-700 font-semibold">{d.bekasHp || 'Baru'}</span>
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

                      {isGA ? (
                        <td className="py-3 px-4 text-slate-600 italic">
                          {d.catatan || 'Kondisi fisik normal'}
                        </td>
                      ) : (
                        <td className="py-3 px-4">
                          <span className="text-slate-700 block truncate max-w-xs">{d.email1 || '-'}</span>
                          {d.ig1 && <span className="text-[11px] text-pink-600 block">@{d.ig1}</span>}
                        </td>
                      )}

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
                  <Smartphone className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="font-bold text-base">
                    {(selectedRecord.data as FormAkun).nama}
                  </h3>
                  <p className="text-xs text-slate-300">
                    {(selectedRecord.data as FormAkun).kategori} &bull; ID: #{selectedRecord.id.slice(-4)}
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
              {/* Unit Info */}
              <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5">
                <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                  Spesifikasi Fisik Handphone
                </div>
                <div className="text-sm font-bold text-[#0F1D2E]">
                  {(selectedRecord.data as FormAkun).jenisHp}
                </div>
                <div className="text-xs text-slate-600">
                  Status Perolehan: <strong className="text-slate-800">{(selectedRecord.data as FormAkun).bekasHp || 'Baru'}</strong>
                </div>
                {(selectedRecord.data as FormAkun).noWa && (
                  <div className="text-xs text-slate-600">
                    Nomor WhatsApp: <strong className="text-emerald-700">{(selectedRecord.data as FormAkun).noWa}</strong>
                  </div>
                )}
              </div>

              {/* Credentials Section - Protected for General Affair */}
              {isGA ? (
                <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-center py-6 space-y-2">
                  <div className="w-10 h-10 rounded-full bg-amber-50 text-amber-600 mx-auto flex items-center justify-center border border-amber-200">
                    <Lock className="w-5 h-5" />
                  </div>
                  <h4 className="font-bold text-slate-800 text-xs">Kredensial Akun Terproteksi</h4>
                  <p className="text-[11px] text-slate-500 max-w-sm mx-auto">
                    Data email perusahaan, password, dan akun sosial media dirahasiakan untuk peran General Affair sesuai kebijakan keamanan IT Syamanah.
                  </p>
                </div>
              ) : (
                <div className="p-4 bg-slate-900 text-white rounded-2xl space-y-2.5 font-mono text-[11px]">
                  <div className="text-teal-300 font-bold uppercase text-[10px] tracking-wider">
                    Kredensial &amp; Akun Login Perusahaan
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
              )}

              {(selectedRecord.data as FormAkun).catatan && (
                <div className="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-slate-800 text-xs">
                  <span className="font-bold block text-[11px] text-slate-500 uppercase tracking-wider mb-0.5">Catatan Tambahan:</span>
                  {(selectedRecord.data as FormAkun).catatan}
                </div>
              )}
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
                  <span>Edit Data Smartphone</span>
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
