import React from 'react';
import {
  Laptop,
  Smartphone,
  Table,
  Wifi,
  ShieldCheck,
  PlusCircle,
  HelpCircle,
  ExternalLink,
  Lock,
  Database,
  Building,
  MapPin,
  Sparkles,
  ArrowUp,
  LogIn,
  CheckCircle2,
  FolderOpen
} from 'lucide-react';
import { TabType } from './Navbar';
import { UserAccount } from '../types';

interface FooterProps {
  activeTab: TabType;
  onTabChange: (tab: TabType) => void;
  onOpenDxDiagGuide: () => void;
  onOpenSettings: () => void;
  onOpenDriveGuide: () => void;
  onOpenLogin: () => void;
  currentUser: UserAccount | null;
  deviceCount: number;
  phoneCount: number;
  totalSubmissions: number;
}

export const Footer: React.FC<FooterProps> = ({
  activeTab,
  onTabChange,
  onOpenDxDiagGuide,
  onOpenSettings,
  onOpenDriveGuide,
  onOpenLogin,
  currentUser,
  deviceCount,
  phoneCount,
  totalSubmissions
}) => {
  const isGA = currentUser?.role === 'general_affair';
  const isSuper = currentUser?.role === 'superadmin' || !currentUser;

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <footer className="bg-[#0F1D2E] text-slate-300 border-t border-slate-800 mt-20 pt-14 pb-8 text-xs relative">
      {/* Decorative Top Accent Line */}
      <div className="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#3A7D8A] via-[#2E5190] to-amber-500" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        {/* Main 4-Column Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 pb-12 border-b border-slate-800/80">
          
          {/* Column 1: Brand & Perusahaan */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="flex items-center bg-white px-2.5 py-1.5 rounded-xl shadow-xs border border-white/20">
                <img
                  src="/logo-syamanah.png"
                  alt="Logo Syamanah"
                  className="h-7 w-auto object-contain"
                  onError={(e) => {
                    e.currentTarget.style.display = 'none';
                  }}
                />
                <span className="font-extrabold text-[#0F1D2E] text-sm tracking-tight ml-1 font-sans">
                  SYAMANAH
                </span>
              </div>
            </div>

            <div className="space-y-1.5">
              <div className="flex items-center gap-2">
                <span className="font-bold text-white text-sm tracking-wide">
                  PORTAL ASET &amp; KREDENSIAL
                </span>
              </div>
              <span className="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white uppercase tracking-wider">
                PT Generasi Sugih Sejahtera
              </span>
              <p className="text-slate-400 text-xs leading-relaxed pt-1">
                Sistem inventarisasi terpusat untuk PC Workstation, Smartphone Operasional Sales, Akun Digital, dan Hotspot Jaringan MikroTik.
              </p>
            </div>

            <div className="pt-2 text-slate-400 text-[11px] space-y-1.5 border-t border-slate-800/60">
              <div className="flex items-start gap-2">
                <MapPin className="w-3.5 h-3.5 text-[#3A7D8A] shrink-0 mt-0.5" />
                <span>Jl. Maleber Utara No. 528 / 187B, Kota Bandung, Jawa Barat</span>
              </div>
              <div>
                <a
                  href="https://syamanah.com"
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-semibold hover:underline"
                >
                  <span>Kunjungi syamanah.com</span>
                  <ExternalLink className="w-3 h-3" />
                </a>
              </div>
            </div>
          </div>

          {/* Column 2: Navigasi Modul Inventaris */}
          <div className="space-y-3.5">
            <h4 className="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-[#3A7D8A]" />
              Katalog &amp; Modul Aset
            </h4>
            <ul className="space-y-2 text-xs">
              <li>
                <button
                  onClick={() => onTabChange('inventory')}
                  className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                    activeTab === 'inventory'
                      ? 'bg-white/10 text-white font-bold'
                      : 'text-slate-400 hover:text-white hover:bg-white/5'
                  }`}
                >
                  <span className="flex items-center gap-2">
                    <Laptop className="w-3.5 h-3.5 text-blue-400" />
                    <span>Laptop &amp; PC Desktop</span>
                  </span>
                  <span className="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">
                    {deviceCount} Unit
                  </span>
                </button>
              </li>

              <li>
                <button
                  onClick={() => onTabChange('phones')}
                  className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                    activeTab === 'phones'
                      ? 'bg-white/10 text-white font-bold'
                      : 'text-slate-400 hover:text-white hover:bg-white/5'
                  }`}
                >
                  <span className="flex items-center gap-2">
                    <Smartphone className="w-3.5 h-3.5 text-amber-400" />
                    <span>HP Android Sales</span>
                  </span>
                  <span className="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">
                    {phoneCount} Unit
                  </span>
                </button>
              </li>

              {!isGA && (
                <>
                  <li>
                    <button
                      onClick={() => onTabChange('spreadsheet')}
                      className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                        activeTab === 'spreadsheet'
                          ? 'bg-white/10 text-white font-bold'
                          : 'text-slate-400 hover:text-white hover:bg-white/5'
                      }`}
                    >
                      <span className="flex items-center gap-2">
                        <Table className="w-3.5 h-3.5 text-emerald-400" />
                        <span>Master Spreadsheet 1:1</span>
                      </span>
                      <span className="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">
                        {totalSubmissions}
                      </span>
                    </button>
                  </li>

                  <li>
                    <button
                      onClick={() => onTabChange('mikrotik')}
                      className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                        activeTab === 'mikrotik'
                          ? 'bg-white/10 text-white font-bold'
                          : 'text-slate-400 hover:text-white hover:bg-white/5'
                      }`}
                    >
                      <span className="flex items-center gap-2">
                        <Wifi className="w-3.5 h-3.5 text-teal-400" />
                        <span>Hotspot MikroTik</span>
                      </span>
                    </button>
                  </li>

                  <li>
                    <button
                      onClick={() => onTabChange('audit')}
                      className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                        activeTab === 'audit'
                          ? 'bg-white/10 text-white font-bold'
                          : 'text-slate-400 hover:text-white hover:bg-white/5'
                      }`}
                    >
                      <span className="flex items-center gap-2">
                        <ShieldCheck className="w-3.5 h-3.5 text-indigo-400" />
                        <span>Audit Kredensial</span>
                      </span>
                    </button>
                  </li>

                  {isSuper && (
                    <li>
                      <button
                        onClick={() => onTabChange('superadmin')}
                        className={`w-full text-left flex items-center justify-between py-1 px-2 rounded-lg transition ${
                          activeTab === 'superadmin'
                            ? 'bg-amber-500/20 text-amber-300 font-bold'
                            : 'text-amber-400/80 hover:text-amber-300 hover:bg-white/5'
                        }`}
                      >
                        <span className="flex items-center gap-2">
                          <Sparkles className="w-3.5 h-3.5 text-amber-400" />
                          <span>Pusat Kontrol Superadmin</span>
                        </span>
                        <span className="text-[9px] font-bold bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded uppercase">
                          CRUD
                        </span>
                      </button>
                    </li>
                  )}
                </>
              )}
            </ul>
          </div>

          {/* Column 3: Pusat Input & Panduan */}
          <div className="space-y-3.5">
            <h4 className="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-amber-500" />
              Pusat Input &amp; Panduan
            </h4>
            <ul className="space-y-2 text-xs">
              <li>
                <button
                  onClick={() => onTabChange('pc')}
                  className="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition"
                >
                  <PlusCircle className="w-3.5 h-3.5 text-[#3A7D8A]" />
                  <span>Input Laptop / PC Baru</span>
                </button>
              </li>

              {!isGA && (
                <li>
                  <button
                    onClick={() => onTabChange('akun')}
                    className="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition"
                  >
                    <PlusCircle className="w-3.5 h-3.5 text-amber-500" />
                    <span>Input HP &amp; Akun Sales</span>
                  </button>
                </li>
              )}

              <li>
                <button
                  onClick={onOpenDxDiagGuide}
                  className="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition"
                >
                  <HelpCircle className="w-3.5 h-3.5 text-blue-400" />
                  <span>Panduan Cek DxDiag Windows</span>
                </button>
              </li>

              {!isGA && (
                <>
                  <li>
                    <button
                      onClick={onOpenDriveGuide}
                      className="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition"
                    >
                      <FolderOpen className="w-3.5 h-3.5 text-emerald-400" />
                      <span>Panduan Google Drive Backup</span>
                    </button>
                  </li>

                  <li>
                    <button
                      onClick={onOpenSettings}
                      className="w-full text-left py-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 flex items-center gap-2 transition"
                    >
                      <Database className="w-3.5 h-3.5 text-teal-400" />
                      <span>Setup Web App GAS &amp; Sheet</span>
                    </button>
                  </li>
                </>
              )}
            </ul>
          </div>

          {/* Column 4: Keamanan, Status & Akun */}
          <div className="space-y-3.5">
            <h4 className="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-400" />
              Status Sistem &amp; Akun
            </h4>

            {/* Status Card */}
            <div className="p-3 bg-slate-900/80 rounded-xl border border-slate-800 space-y-2.5">
              <div className="flex items-center justify-between text-[11px]">
                <span className="text-slate-400">Database Engine:</span>
                <span className="inline-flex items-center gap-1.5 text-emerald-400 font-semibold">
                  <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                  Online 1:1
                </span>
              </div>

              <div className="flex items-center justify-between text-[11px]">
                <span className="text-slate-400">Privasi Sandi:</span>
                <span className="inline-flex items-center gap-1 text-slate-300 font-semibold">
                  <Lock className="w-3 h-3 text-amber-400" />
                  RBAC Terisolasi
                </span>
              </div>

              <div className="pt-2 border-t border-slate-800 text-[11px]">
                <span className="text-slate-400 block mb-1">Masuk Sebagai:</span>
                <div className="flex items-center justify-between">
                  <span className="font-bold text-white truncate max-w-[120px]">
                    {currentUser?.name || 'Tamu'}
                  </span>
                  <button
                    onClick={onOpenLogin}
                    className="text-[10px] font-bold text-amber-400 hover:text-amber-300 hover:underline flex items-center gap-1"
                  >
                    <LogIn className="w-3 h-3" />
                    <span>Ganti Akun</span>
                  </button>
                </div>
              </div>
            </div>

            <div className="pt-1">
              <button
                onClick={scrollToTop}
                className="w-full py-2 px-3 rounded-xl bg-slate-800/80 hover:bg-slate-750 text-slate-300 hover:text-white font-medium text-[11px] flex items-center justify-center gap-2 transition border border-slate-700/50"
              >
                <ArrowUp className="w-3.5 h-3.5 text-amber-400" />
                <span>Kembali ke Atas</span>
              </button>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400 text-[11px]">
          <div className="flex flex-wrap items-center gap-2 text-center sm:text-left">
            <span>
              &copy; {new Date().getFullYear()} <strong>PT Generasi Sugih Sejahtera</strong> &middot; CV Syamanah Berkah Abadi.
            </span>
            <span className="hidden sm:inline">&middot;</span>
            <span>Hak Cipta Dilindungi Undang-Undang.</span>
          </div>

          <div className="flex items-center gap-3">
            <span className="text-[10px] px-2.5 py-1 rounded-full bg-slate-800 text-slate-400 border border-slate-700/60 font-mono">
              Portal Syamanah v2.4
            </span>
            <a
              href="https://syamanah.com"
              target="_blank"
              rel="noreferrer"
              className="text-[#3A7D8A] hover:text-teal-300 font-semibold hover:underline"
            >
              syamanah.com &rarr;
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
};
