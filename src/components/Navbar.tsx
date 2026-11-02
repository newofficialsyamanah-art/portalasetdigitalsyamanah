import React, { useState, useEffect } from 'react';
import {
  Menu,
  X,
  Table,
  Laptop,
  Smartphone,
  Wifi,
  Monitor,
  FolderOpen,
  Settings,
  HelpCircle,
  ShieldCheck,
  CheckCircle2,
  ChevronRight
} from 'lucide-react';

export type TabType =
  | 'spreadsheet'
  | 'inventory'
  | 'phones'
  | 'mikrotik'
  | 'audit'
  | 'pc'
  | 'akun'
  | 'superadmin'
  | 'history'
  | 'gas_guide'
  | 'dxdiag_guide';

interface NavbarProps {
  activeTab: TabType;
  onTabChange: (tab: TabType) => void;
  onOpenSettings: () => void;
  onOpenDriveGuide: () => void;
  hasGasUrl: boolean;
  hasSpreadsheet: boolean;
  hasDriveFolder: boolean;
  deviceCount: number;
  phoneCount: number;
  totalSubmissions: number;
}

export const Navbar: React.FC<NavbarProps> = ({
  activeTab,
  onTabChange,
  onOpenSettings,
  onOpenDriveGuide,
  deviceCount,
  phoneCount,
  totalSubmissions
}) => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  // Close hamburger drawer when Escape is pressed
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setIsMenuOpen(false);
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Lock body scroll when menu is open
  useEffect(() => {
    if (isMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isMenuOpen]);

  const handleNavigate = (tab: TabType) => {
    onTabChange(tab);
    setIsMenuOpen(false);
  };

  return (
    <>
      {/* Clean, professional enterprise header */}
      <header className="bg-[#0B1B3D] text-white border-b border-slate-800 sticky top-0 z-30 shadow-xs">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-4">
          {/* Logo & Company Title */}
          <div
            onClick={() => handleNavigate('spreadsheet')}
            className="flex items-center gap-2.5 cursor-pointer select-none"
          >
            <div className="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm shadow-xs shrink-0">
              S
            </div>
            <div className="flex items-baseline gap-2">
              <span className="font-bold text-white tracking-tight text-sm sm:text-base">
                Syamanah
              </span>
              <span className="text-xs text-amber-400 font-medium hidden sm:inline">
                Garment &amp; Apparel
              </span>
              <span className="text-slate-500 text-xs hidden md:inline">/</span>
              <span className="text-slate-300 text-xs hidden md:inline">
                Inventaris &amp; Akun
              </span>
            </div>
          </div>

          {/* Quick Tabs on Desktop */}
          <div className="hidden md:flex items-center space-x-1">
            <button
              onClick={() => onTabChange('spreadsheet')}
              className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
                activeTab === 'spreadsheet'
                  ? 'bg-slate-800 text-amber-400 font-semibold'
                  : 'text-slate-300 hover:text-white hover:bg-slate-800/50'
              }`}
            >
              Spreadsheet
            </button>
            <button
              onClick={() => onTabChange('inventory')}
              className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
                activeTab === 'inventory' || activeTab === 'phones'
                  ? 'bg-slate-800 text-amber-400 font-semibold'
                  : 'text-slate-300 hover:text-white hover:bg-slate-800/50'
              }`}
            >
              Katalog Perangkat
            </button>
            <button
              onClick={() => onTabChange('pc')}
              className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
                activeTab === 'pc' || activeTab === 'akun' || activeTab === 'mikrotik'
                  ? 'bg-slate-800 text-amber-400 font-semibold'
                  : 'text-slate-300 hover:text-white hover:bg-slate-800/50'
              }`}
            >
              Input Data
            </button>
          </div>

          {/* Right Action: Drive indicator & Hamburger button */}
          <div className="flex items-center gap-2">
            <button
              onClick={onOpenDriveGuide}
              className="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs text-slate-300 hover:text-white hover:bg-slate-800/80 transition"
              title="Google Drive: newofficialsyamanah@gmail.com"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-400" />
              <span>Drive Syamanah</span>
            </button>

            {/* Hamburger Button */}
            <button
              onClick={() => setIsMenuOpen(true)}
              className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-medium transition"
              aria-label="Buka menu navigasi"
            >
              <Menu className="w-4 h-4 text-amber-400" />
              <span>Menu</span>
            </button>
          </div>
        </div>
      </header>

      {/* Hamburger Drawer Overlay */}
      {isMenuOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          {/* Backdrop */}
          <div
            onClick={() => setIsMenuOpen(false)}
            className="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
          />

          {/* Drawer Sidebar */}
          <aside className="relative w-full max-w-xs sm:max-w-sm bg-[#0B1B3D] border-l border-slate-800 text-white shadow-xl flex flex-col h-full z-10 animate-in slide-in-from-right duration-200">
            {/* Drawer Header */}
            <div className="p-4 border-b border-slate-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-6 h-6 rounded bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center">
                  S
                </div>
                <span className="font-bold text-sm text-white">Menu Navigasi</span>
              </div>
              <button
                onClick={() => setIsMenuOpen(false)}
                className="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition"
                aria-label="Tutup menu"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {/* Navigation Groups */}
            <div className="flex-1 overflow-y-auto p-4 space-y-6 text-sm">
              {/* Group 1: Data & Inventaris */}
              <div>
                <p className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 px-2">
                  Data &amp; Inventaris
                </p>
                <div className="space-y-1">
                  <button
                    onClick={() => handleNavigate('spreadsheet')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'spreadsheet'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Table className="w-4 h-4 text-amber-400" />
                      <span>Data Spreadsheet</span>
                    </div>
                    <span className="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full">
                      {totalSubmissions}
                    </span>
                  </button>

                  <button
                    onClick={() => handleNavigate('inventory')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'inventory'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Laptop className="w-4 h-4 text-blue-400" />
                      <span>Katalog Laptop &amp; PC</span>
                    </div>
                    <span className="text-xs text-slate-400">{deviceCount} unit</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('phones')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'phones'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Smartphone className="w-4 h-4 text-amber-400" />
                      <span>Katalog HP Sales</span>
                    </div>
                    <span className="text-xs text-slate-400">{phoneCount} unit</span>
                  </button>
                </div>
              </div>

              {/* Group 2: Form Input */}
              <div>
                <p className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 px-2">
                  Input Data Baru
                </p>
                <div className="space-y-1">
                  <button
                    onClick={() => handleNavigate('pc')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'pc'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Monitor className="w-4 h-4 text-blue-400" />
                      <span>Form Laptop / PC</span>
                    </div>
                    <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
                  </button>

                  <button
                    onClick={() => handleNavigate('akun')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'akun'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Smartphone className="w-4 h-4 text-amber-400" />
                      <span>Form Akun Sales</span>
                    </div>
                    <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
                  </button>

                  <button
                    onClick={() => handleNavigate('mikrotik')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'mikrotik'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Wifi className="w-4 h-4 text-teal-400" />
                      <span>Form Akun MikroTik</span>
                    </div>
                    <ChevronRight className="w-3.5 h-3.5 text-slate-500" />
                  </button>
                </div>
              </div>

              {/* Group 3: Sistem & Pengaturan */}
              <div>
                <p className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 px-2">
                  Sistem &amp; Pengaturan
                </p>
                <div className="space-y-1">
                  <button
                    onClick={() => {
                      setIsMenuOpen(false);
                      onOpenDriveGuide();
                    }}
                    className="w-full text-left px-3 py-2 rounded-lg flex items-center justify-between text-slate-300 hover:bg-slate-800/80 hover:text-white transition"
                  >
                    <div className="flex items-center gap-2.5">
                      <FolderOpen className="w-4 h-4 text-amber-400" />
                      <span>Google Drive Akun</span>
                    </div>
                    <span className="text-[10px] text-emerald-400 font-medium">Terhubung</span>
                  </button>

                  <button
                    onClick={() => {
                      setIsMenuOpen(false);
                      onOpenSettings();
                    }}
                    className="w-full text-left px-3 py-2 rounded-lg flex items-center justify-between text-slate-300 hover:bg-slate-800/80 hover:text-white transition"
                  >
                    <div className="flex items-center gap-2.5">
                      <Settings className="w-4 h-4 text-slate-400" />
                      <span>Pengaturan Spreadsheet</span>
                    </div>
                  </button>

                  <button
                    onClick={() => handleNavigate('audit')}
                    className={`w-full text-left px-3 py-2 rounded-lg flex items-center justify-between transition ${
                      activeTab === 'audit'
                        ? 'bg-amber-500/15 text-amber-400 font-semibold'
                        : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <ShieldCheck className="w-4 h-4 text-purple-400" />
                      <span>Audit Data &amp; Koneksi</span>
                    </div>
                  </button>

                  <button
                    onClick={() => {
                      setIsMenuOpen(false);
                      onTabChange('dxdiag_guide');
                    }}
                    className="w-full text-left px-3 py-2 rounded-lg flex items-center gap-2.5 text-slate-300 hover:bg-slate-800/80 hover:text-white transition"
                  >
                    <HelpCircle className="w-4 h-4 text-slate-400" />
                    <span>Panduan DxDiag</span>
                  </button>
                </div>
              </div>
            </div>

            {/* Drawer Footer */}
            <div className="p-4 border-t border-slate-800 bg-[#071328] text-xs text-slate-400 flex items-center justify-between">
              <span>Syamanah Bandung &bull; 2026</span>
              <span className="text-[11px] text-amber-400 font-mono">v1.2</span>
            </div>
          </aside>
        </div>
      )}
    </>
  );
};
