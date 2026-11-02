import React, { useState } from 'react';
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
  Layers
} from 'lucide-react';
import { SubmissionRecord, FormPC, FormAkun, SyamanahConfig } from '../types';
import { getConfig, saveConfig } from '../services/gasService';

interface SuperadminSummaryProps {
  records: SubmissionRecord[];
  onRefresh: () => void;
  onOpenSettings: () => void;
}

export const SuperadminSummary: React.FC<SuperadminSummaryProps> = ({
  records,
  onRefresh,
  onOpenSettings
}) => {
  const [config, setConfigState] = useState<SyamanahConfig>(getConfig());
  const [pinInput, setPinInput] = useState('');
  const [isUnlocked, setIsUnlocked] = useState(false);
  const [pinError, setPinError] = useState('');
  const [searchVault, setSearchVault] = useState('');
  const [revealedPasswords, setRevealedPasswords] = useState<Record<string, boolean>>({});

  const pcRecords = records.filter((r) => r.type === 'pc');
  const akunRecords = records.filter((r) => r.type === 'akun');

  // Breakdown calculations
  const totalLaptops = pcRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'Laptop' || !(r.data as FormPC).jenisPerangkat
  ).length;
  const totalPCDesktop = pcRecords.filter(
    (r) => (r.data as FormPC).jenisPerangkat === 'PC Desktop'
  ).length;
  const totalSmartphones = akunRecords.length;

  // Department Breakdown
  const deptCounts: Record<string, number> = {};
  pcRecords.forEach((r) => {
    const d = (r.data as FormPC).departemen || 'Lainnya';
    deptCounts[d] = (deptCounts[d] || 0) + 1;
  });

  // RAM Breakdown
  const ramCounts: Record<string, number> = {};
  pcRecords.forEach((r) => {
    const mem = ((r.data as FormPC).memory || '').toUpperCase();
    if (mem.includes('16') || mem.includes('16384')) {
      ramCounts['16 GB'] = (ramCounts['16 GB'] || 0) + 1;
    } else if (mem.includes('8') || mem.includes('8192')) {
      ramCounts['8 GB'] = (ramCounts['8 GB'] || 0) + 1;
    } else if (mem.includes('4') || mem.includes('4096')) {
      ramCounts['4 GB'] = (ramCounts['4 GB'] || 0) + 1;
    } else if (mem.includes('32')) {
      ramCounts['32 GB'] = (ramCounts['32 GB'] || 0) + 1;
    } else {
      ramCounts['Lainnya'] = (ramCounts['Lainnya'] || 0) + 1;
    }
  });

  // Audit alerts (any notes or irregularities)
  const auditAlerts = akunRecords.filter((r) => {
    const d = r.data as FormAkun;
    return d.catatan && d.catatan.trim().length > 0;
  });

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

  const toggleReveal = (key: string) => {
    setRevealedPasswords((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const handlePrintSummary = () => {
    window.print();
  };

  const filteredVaultRecords = akunRecords.filter((rec) => {
    if (!searchVault.trim()) return true;
    const term = searchVault.toLowerCase();
    const d = rec.data as FormAkun;
    return (
      (d.nama || '').toLowerCase().includes(term) ||
      (d.kategori || '').toLowerCase().includes(term) ||
      (d.email1 || '').toLowerCase().includes(term) ||
      (d.noWa || '').toLowerCase().includes(term)
    );
  });

  return (
    <div className="space-y-8">
      {/* Executive Header Banner */}
      <div className="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-900/40 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold mb-3 border border-emerald-400/20">
            <ShieldCheck className="w-3.5 h-3.5" />
            Executive IT &amp; Asset Management Dashboard
          </div>
          <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Ringkasan Eksekutif Superadmin
          </h2>
          <p className="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
            Ringkasan total perangkat fisik (Laptop, PC Desktop, Smartphone), audit alokasi per divisi, status sinkronisasi spreadsheet, dan brankas kredensial berkeamanan tinggi.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <button
            onClick={handlePrintSummary}
            className="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold flex items-center gap-2 transition"
          >
            <Printer className="w-4 h-4" />
            <span>Cetak / Cetak PDF</span>
          </button>
          <button
            onClick={onOpenSettings}
            className="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 transition shadow-md"
          >
            <FileSpreadsheet className="w-4 h-4" />
            <span>Koneksi Spreadsheet</span>
          </button>
        </div>
      </div>

      {/* Top Level Metric Cards */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {/* Total Laptop */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
            <Laptop className="w-6 h-6" />
          </div>
          <div>
            <span className="text-xs text-slate-500 font-semibold block">Total Laptop</span>
            <span className="text-2xl font-black text-slate-900 block">{totalLaptops}</span>
            <span className="text-[11px] text-emerald-600 font-medium">Unit portabel aktif</span>
          </div>
        </div>

        {/* Total PC Desktop */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200">
            <Monitor className="w-6 h-6" />
          </div>
          <div>
            <span className="text-xs text-slate-500 font-semibold block">Total PC Desktop</span>
            <span className="text-2xl font-black text-slate-900 block">{totalPCDesktop}</span>
            <span className="text-[11px] text-blue-600 font-medium">Workstation kantor</span>
          </div>
        </div>

        {/* Total Smartphone */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center shrink-0 border border-teal-200">
            <Smartphone className="w-6 h-6" />
          </div>
          <div>
            <span className="text-xs text-slate-500 font-semibold block">Total Smartphone / Akun</span>
            <span className="text-2xl font-black text-slate-900 block">{totalSmartphones}</span>
            <span className="text-[11px] text-teal-600 font-medium">Device sales &amp; staf</span>
          </div>
        </div>

        {/* Audit Alerts */}
        <div className="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200">
            <AlertTriangle className="w-6 h-6" />
          </div>
          <div>
            <span className="text-xs text-slate-500 font-semibold block">Catatan Khusus / Audit</span>
            <span className="text-2xl font-black text-slate-900 block">{auditAlerts.length}</span>
            <span className="text-[11px] text-rose-600 font-medium">Perlu perhatian</span>
          </div>
        </div>
      </div>

      {/* Analysis Grid: Departemen & RAM Specs */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Distribusi Departemen */}
        <div className="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-2.5">
              <Building2 className="w-5 h-5 text-emerald-600" />
              <h3 className="font-bold text-slate-900 text-sm">Alokasi Perangkat per Departemen</h3>
            </div>
            <span className="text-xs text-slate-400">Total {pcRecords.length} unit</span>
          </div>

          <div className="space-y-3">
            {Object.keys(deptCounts).length === 0 ? (
              <p className="text-xs text-slate-400 py-4 text-center">Belum ada data alokasi perangkat.</p>
            ) : (
              Object.entries(deptCounts).map(([dept, count]) => {
                const pct = Math.round((count / (pcRecords.length || 1)) * 100);
                return (
                  <div key={dept} className="space-y-1">
                    <div className="flex justify-between text-xs font-semibold">
                      <span className="text-slate-700">{dept}</span>
                      <span className="text-slate-900">{count} unit ({pct}%)</span>
                    </div>
                    <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div
                        className="h-full bg-emerald-600 rounded-full"
                        style={{ width: `${pct}%` }}
                      />
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </div>

        {/* Distribusi RAM Hardware */}
        <div className="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-2.5">
              <Cpu className="w-5 h-5 text-blue-600" />
              <h3 className="font-bold text-slate-900 text-sm">Distribusi Kapasitas RAM Komputer</h3>
            </div>
            <span className="text-xs text-slate-400">Audit performa</span>
          </div>

          <div className="space-y-3">
            {Object.keys(ramCounts).length === 0 ? (
              <p className="text-xs text-slate-400 py-4 text-center">Belum ada data hardware RAM.</p>
            ) : (
              Object.entries(ramCounts).map(([ram, count]) => {
                const pct = Math.round((count / (pcRecords.length || 1)) * 100);
                return (
                  <div key={ram} className="space-y-1">
                    <div className="flex justify-between text-xs font-semibold">
                      <span className="text-slate-700">{ram}</span>
                      <span className="text-slate-900">{count} unit ({pct}%)</span>
                    </div>
                    <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div
                        className="h-full bg-blue-600 rounded-full"
                        style={{ width: `${pct}%` }}
                      />
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </div>
      </div>

      {/* Audit Warnings Section */}
      {auditAlerts.length > 0 && (
        <div className="bg-rose-50/70 rounded-2xl p-6 border border-rose-200 shadow-xs">
          <div className="flex items-center gap-2.5 mb-3 text-rose-900">
            <AlertTriangle className="w-5 h-5 text-rose-600" />
            <h3 className="font-bold text-sm">Catatan Audit &amp; Temuan Khusus Superadmin</h3>
          </div>
          <div className="space-y-2">
            {auditAlerts.map((r) => {
              const d = r.data as FormAkun;
              return (
                <div
                  key={r.id}
                  className="bg-white p-3.5 rounded-xl border border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs"
                >
                  <div>
                    <span className="font-bold text-slate-900">{d.nama}</span>
                    <span className="text-slate-500 ml-2">({d.kategori})</span>
                    <p className="text-rose-700 font-medium mt-0.5">{d.catatan}</p>
                  </div>
                  <span className="text-slate-400 text-[11px] shrink-0">{r.timestamp}</span>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* Protected Credential Vault Section */}
      <div className="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        {/* Vault Header */}
        <div className="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-slate-800 flex items-center justify-center border border-slate-700">
              {isUnlocked ? (
                <Unlock className="w-5 h-5 text-emerald-400" />
              ) : (
                <Lock className="w-5 h-5 text-amber-400" />
              )}
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="font-bold text-base text-white">Brankas Kredensial &amp; Akun Superadmin</h3>
                <span
                  className={`text-[10px] px-2 py-0.5 rounded-full font-bold uppercase ${
                    isUnlocked
                      ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                      : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                  }`}
                >
                  {isUnlocked ? 'Terbuka' : 'Terkunci'}
                </span>
              </div>
              <p className="text-xs text-slate-400 mt-0.5">
                Area terbatas hanya untuk Superadmin. Kredensial email &amp; akun media sosial diisolasi di sini.
              </p>
            </div>
          </div>

          {isUnlocked && (
            <button
              onClick={() => setIsUnlocked(false)}
              className="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700"
            >
              Kunci Kembali
            </button>
          )}
        </div>

        {/* Vault Body */}
        {!isUnlocked ? (
          <div className="p-8 sm:p-12 text-center max-w-md mx-auto space-y-4">
            <div className="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
              <KeyRound className="w-7 h-7 text-emerald-700" />
            </div>
            <div>
              <h4 className="font-bold text-slate-900 text-base">Buka Kunci Brankas Akun</h4>
              <p className="text-xs text-slate-500 mt-1">
                Masukkan PIN Superadmin untuk melihat kata sandi email resmi, Instagram, dan Facebook staf. (Default: <code>1234</code>)
              </p>
            </div>

            <form onSubmit={handleUnlock} className="space-y-3">
              <div className="relative">
                <input
                  type="password"
                  value={pinInput}
                  onChange={(e) => setPinInput(e.target.value)}
                  placeholder="Masukkan 4 digit PIN Superadmin..."
                  className="w-full text-center tracking-widest text-lg font-bold py-2.5 px-4 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                  maxLength={8}
                />
              </div>

              {pinError && <p className="text-xs text-rose-600 font-semibold">{pinError}</p>}

              <button
                type="submit"
                className="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-sm transition"
              >
                Buka Brankas Kredensial
              </button>
            </form>
          </div>
        ) : (
          <div className="p-6 space-y-4">
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
              <div className="relative flex-1">
                <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={searchVault}
                  onChange={(e) => setSearchVault(e.target.value)}
                  placeholder="Cari kredensial nama staf, role, atau email..."
                  className="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                />
              </div>
              <span className="text-xs text-slate-500 font-medium">
                Ditemukan {filteredVaultRecords.length} akun staf
              </span>
            </div>

            <div className="overflow-x-auto border border-slate-200 rounded-2xl">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                    <th className="py-3 px-4">Nama Staf &amp; Role</th>
                    <th className="py-3 px-4">Akun Email &amp; Password</th>
                    <th className="py-3 px-4">Instagram Sales</th>
                    <th className="py-3 px-4">Facebook Sales</th>
                    <th className="py-3 px-4">WhatsApp &amp; HP</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {filteredVaultRecords.map((r) => {
                    const d = r.data as FormAkun;
                    return (
                      <tr key={r.id} className="hover:bg-slate-50/70">
                        <td className="py-3 px-4">
                          <span className="font-bold text-slate-900 block">{d.nama}</span>
                          <span className="text-[11px] text-slate-500">{d.kategori}</span>
                        </td>

                        {/* Email */}
                        <td className="py-3 px-4">
                          {d.email1 ? (
                            <div className="space-y-1">
                              <span className="font-mono text-slate-800 block text-[11px]">{d.email1}</span>
                              <div className="flex items-center gap-1.5">
                                <span className="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">
                                  {revealedPasswords[`e1_${r.id}`] ? d.passEmail1 || '-' : '••••••••'}
                                </span>
                                <button
                                  type="button"
                                  onClick={() => toggleReveal(`e1_${r.id}`)}
                                  className="text-slate-400 hover:text-slate-700"
                                >
                                  {revealedPasswords[`e1_${r.id}`] ? (
                                    <EyeOff className="w-3 h-3" />
                                  ) : (
                                    <Eye className="w-3 h-3" />
                                  )}
                                </button>
                              </div>
                            </div>
                          ) : (
                            <span className="text-slate-400">-</span>
                          )}
                        </td>

                        {/* IG */}
                        <td className="py-3 px-4">
                          {d.ig1 ? (
                            <div className="space-y-1">
                              <span className="font-medium text-pink-700 block text-[11px]">@{d.ig1}</span>
                              <div className="flex items-center gap-1.5">
                                <span className="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">
                                  {revealedPasswords[`ig1_${r.id}`] ? d.passIg1 || '-' : '••••••••'}
                                </span>
                                <button
                                  type="button"
                                  onClick={() => toggleReveal(`ig1_${r.id}`)}
                                  className="text-slate-400 hover:text-slate-700"
                                >
                                  {revealedPasswords[`ig1_${r.id}`] ? (
                                    <EyeOff className="w-3 h-3" />
                                  ) : (
                                    <Eye className="w-3 h-3" />
                                  )}
                                </button>
                              </div>
                            </div>
                          ) : (
                            <span className="text-slate-400">-</span>
                          )}
                        </td>

                        {/* FB */}
                        <td className="py-3 px-4">
                          {d.fb1 ? (
                            <div className="space-y-1">
                              <span className="font-medium text-blue-700 block text-[11px]">{d.fb1}</span>
                              <div className="flex items-center gap-1.5">
                                <span className="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">
                                  {revealedPasswords[`fb1_${r.id}`] ? d.passFb1 || '-' : '••••••••'}
                                </span>
                                <button
                                  type="button"
                                  onClick={() => toggleReveal(`fb1_${r.id}`)}
                                  className="text-slate-400 hover:text-slate-700"
                                >
                                  {revealedPasswords[`fb1_${r.id}`] ? (
                                    <EyeOff className="w-3 h-3" />
                                  ) : (
                                    <Eye className="w-3 h-3" />
                                  )}
                                </button>
                              </div>
                            </div>
                          ) : (
                            <span className="text-slate-400">-</span>
                          )}
                        </td>

                        {/* WA & Phone */}
                        <td className="py-3 px-4">
                          <span className="font-bold text-slate-800 block text-[11px]">{d.noWa}</span>
                          <span className="text-[10px] text-slate-500">{d.jenisHp || 'HP Kantor'}</span>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
