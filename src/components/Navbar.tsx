import React, { useState, useEffect, useRef } from 'react';
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
  ChevronDown,
  LogIn,
  LogOut,
  Users,
  Sparkles,
  Plus,
  PlusCircle,
  FileSpreadsheet,
  CheckCircle2
} from 'lucide-react';
import { UserAccount } from '../types';

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
  | 'dxdiag_guide'
  | 'users';

interface NavbarProps {
  activeTab: TabType;
  onTabChange: (tab: TabType) => void;
  onOpenSettings: () => void;
  onOpenDriveGuide: () => void;
  onOpenLogin: () => void;
  currentUser: UserAccount | null;
  onLogout: () => void;
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
  onOpenLogin,
  currentUser,
  onLogout,
  deviceCount,
  phoneCount,
  totalSubmissions
}) => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isProfileDropdownOpen, setIsProfileDropdownOpen] = useState(false);
  const [isQuickAddOpen, setIsQuickAddOpen] = useState(false);

  // Close menus when Escape is pressed
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setIsMenuOpen(false);
        setIsProfileDropdownOpen(false);
        setIsQuickAddOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Lock body scroll when mobile drawer is open
  useEffect(() => {
    if (isMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }, [isMenuOpen]);

  const handleNavigate = (tab: TabType) => {
    onTabChange(tab);
    setIsMenuOpen(false);
    setIsQuickAddOpen(false);
    setIsProfileDropdownOpen(false);
  };

  const isGA = currentUser?.role === 'general_affair';
  const isSuper = currentUser?.role === 'superadmin' || !currentUser;

  return (
    <>
      {/* Sleek Modern Header with Glassmorphism & Syamanah Colors */}
      <header className="bg-[#0F1D2E]/95 backdrop-blur-md text-white border-b border-slate-800/90 sticky top-0 z-30 shadow-md">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
          
          {/* Brand Identity - Syamanah.com Inspired */}
          <div
            onClick={() => handleNavigate(isGA ? 'inventory' : 'spreadsheet')}
            className="flex items-center gap-2.5 cursor-pointer select-none group shrink-0"
          >
            <div className="flex items-center bg-white px-2 py-1.5 rounded-xl shadow-xs border border-white/20 group-hover:scale-105 transition-transform duration-200">
              <img
                src="/logo-syamanah.png"
                alt="Logo Syamanah"
                className="h-6 w-auto object-contain"
                onError={(e) => {
                  e.currentTarget.style.display = 'none';
                }}
              />
              <span className="font-black text-[#0F1D2E] text-xs font-sans tracking-tight ml-1">
                SYAMANAH
              </span>
            </div>

            <div className="hidden sm:flex flex-col">
              <div className="flex items-center gap-1.5">
                <span className="font-extrabold text-white tracking-wider text-xs">
                  PORTAL ASET DIGITAL
                </span>
                <span className="text-[9px] font-bold px-1.5 py-0.2 rounded bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white">
                  CV Syamanah
                </span>
              </div>
              <span className="text-[10px] text-slate-400 font-medium">
                PT Generasi Sugih Sejahtera
              </span>
            </div>
          </div>

          {/* Center Navigation - Segmented Pills (Desktop lg:flex) */}
          <nav className="hidden lg:flex items-center bg-slate-900/90 p-1 rounded-full border border-slate-800 shadow-inner">
            {isGA ? (
              /* General Affair Navigation (Hardware Only) */
              <>
                <button
                  onClick={() => onTabChange('inventory')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'inventory'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Laptop className="w-3.5 h-3.5 text-blue-300" />
                  <span>Laptop &amp; PC</span>
                  <span className="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">
                    {deviceCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('phones')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'phones'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Smartphone className="w-3.5 h-3.5 text-amber-300" />
                  <span>HP Android</span>
                  <span className="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">
                    {phoneCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('pc')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'pc'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Monitor className="w-3.5 h-3.5 text-teal-300" />
                  <span>Input PC</span>
                </button>

                <button
                  onClick={() => onTabChange('dxdiag_guide')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'dxdiag_guide'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <HelpCircle className="w-3.5 h-3.5 text-slate-400" />
                  <span>DxDiag</span>
                </button>
              </>
            ) : (
              /* Superadmin & Manager Navigation */
              <>
                <button
                  onClick={() => onTabChange('spreadsheet')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'spreadsheet'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Table className="w-3.5 h-3.5 text-emerald-300" />
                  <span>Spreadsheet</span>
                  <span className="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">
                    {totalSubmissions}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('inventory')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'inventory'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Laptop className="w-3.5 h-3.5 text-blue-300" />
                  <span>Laptop &amp; PC</span>
                  <span className="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">
                    {deviceCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('phones')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'phones'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Smartphone className="w-3.5 h-3.5 text-amber-300" />
                  <span>HP Sales</span>
                  <span className="text-[10px] bg-black/30 px-1.5 py-0.2 rounded-full font-mono text-slate-200">
                    {phoneCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('mikrotik')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'mikrotik'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Wifi className="w-3.5 h-3.5 text-teal-300" />
                  <span>MikroTik</span>
                </button>

                <button
                  onClick={() => onTabChange('audit')}
                  className={`px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'audit'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm ring-1 ring-white/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <ShieldCheck className="w-3.5 h-3.5 text-indigo-300" />
                  <span>Audit</span>
                </button>

                {/* Superadmin Master Control Tab */}
                {isSuper && (
                  <button
                    onClick={() => onTabChange('superadmin')}
                    className={`px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                      activeTab === 'superadmin' || activeTab === 'users'
                        ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-sm ring-1 ring-amber-300/50'
                        : 'text-amber-400 hover:text-amber-300 hover:bg-amber-500/10'
                    }`}
                  >
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>👑 Superadmin</span>
                  </button>
                )}
              </>
            )}
          </nav>

          {/* Right Action: Quick Add + Role Badge & Profile */}
          <div className="flex items-center gap-2 sm:gap-2.5">
            {/* Quick Add Button (Desktop) */}
            {!isGA && (
              <div className="relative hidden md:block">
                <button
                  onClick={() => {
                    setIsQuickAddOpen(!isQuickAddOpen);
                    setIsProfileDropdownOpen(false);
                  }}
                  className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#3A7D8A]/30 hover:bg-[#3A7D8A]/50 border border-[#3A7D8A]/40 text-teal-200 hover:text-white text-xs font-semibold transition"
                  title="Tambah Data Baru"
                >
                  <Plus className="w-3.5 h-3.5 text-teal-300" />
                  <span>+ Tambah</span>
                  <ChevronDown className="w-3 h-3 text-teal-300" />
                </button>

                {isQuickAddOpen && (
                  <>
                    <div
                      onClick={() => setIsQuickAddOpen(false)}
                      className="fixed inset-0 z-40"
                    />
                    <div className="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50 text-slate-800 animate-in fade-in zoom-in-95">
                      <div className="px-3 py-2 border-b border-slate-100">
                        <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                          Pilih Formulir Input
                        </p>
                      </div>
                      <div className="py-1 space-y-1">
                        <button
                          onClick={() => handleNavigate('pc')}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                        >
                          <Laptop className="w-4 h-4 text-blue-600" />
                          <div>
                            <div className="font-bold">Input Laptop &amp; PC</div>
                            <div className="text-[10px] text-slate-400">Workstation &amp; DxDiag</div>
                          </div>
                        </button>

                        <button
                          onClick={() => handleNavigate('akun')}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                        >
                          <Smartphone className="w-4 h-4 text-amber-600" />
                          <div>
                            <div className="font-bold">Input HP &amp; Akun Sales</div>
                            <div className="text-[10px] text-slate-400">IMEI, Email &amp; Sosmed</div>
                          </div>
                        </button>

                        <button
                          onClick={() => handleNavigate('mikrotik')}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                        >
                          <Wifi className="w-4 h-4 text-teal-600" />
                          <div>
                            <div className="font-bold">Generate Hotspot MikroTik</div>
                            <div className="text-[10px] text-slate-400">Voucher &amp; User Profile</div>
                          </div>
                        </button>
                      </div>
                    </div>
                  </>
                )}
              </div>
            )}

            {/* Profile Dropdown */}
            {currentUser ? (
              <div className="relative">
                <button
                  onClick={() => {
                    setIsProfileDropdownOpen(!isProfileDropdownOpen);
                    setIsQuickAddOpen(false);
                  }}
                  className={`flex items-center gap-2 px-2.5 py-1.5 rounded-full border transition-all duration-200 ${
                    currentUser.role === 'superadmin'
                      ? 'bg-amber-500/10 border-amber-500/30 hover:bg-amber-500/20'
                      : currentUser.role === 'manager'
                      ? 'bg-blue-500/10 border-blue-500/30 hover:bg-blue-500/20'
                      : 'bg-teal-500/10 border-teal-500/30 hover:bg-teal-500/20'
                  }`}
                >
                  <div
                    className={`w-6 h-6 rounded-full flex items-center justify-center font-extrabold text-[11px] uppercase shadow-xs shrink-0 ${
                      currentUser.role === 'superadmin'
                        ? 'bg-amber-500 text-slate-950'
                        : currentUser.role === 'manager'
                        ? 'bg-blue-600 text-white'
                        : 'bg-[#3A7D8A] text-white'
                    }`}
                  >
                    {currentUser.name.charAt(0)}
                  </div>

                  <div className="hidden sm:flex flex-col text-left">
                    <span className="text-xs font-bold text-white leading-tight truncate max-w-[110px]">
                      {currentUser.name}
                    </span>
                    <span className="text-[9px] font-semibold leading-tight">
                      {currentUser.role === 'superadmin' && (
                        <span className="text-amber-400 font-bold">👑 Superadmin</span>
                      )}
                      {currentUser.role === 'manager' && (
                        <span className="text-blue-300">👔 Manager</span>
                      )}
                      {currentUser.role === 'general_affair' && (
                        <span className="text-teal-300">🏢 General Affair</span>
                      )}
                    </span>
                  </div>

                  <ChevronDown className="w-3 h-3 text-slate-400" />
                </button>

                {/* Profile Dropdown Menu */}
                {isProfileDropdownOpen && (
                  <>
                    <div
                      onClick={() => setIsProfileDropdownOpen(false)}
                      className="fixed inset-0 z-40"
                    />
                    <div className="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 p-3 z-50 text-slate-800 animate-in fade-in zoom-in-95">
                      <div className="p-2 border-b border-slate-100 pb-3">
                        <p className="text-xs font-bold text-slate-900">{currentUser.name}</p>
                        <p className="text-[11px] text-slate-500 font-mono">@{currentUser.username}</p>
                        <div className="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                          PERAN: {currentUser.role.toUpperCase()}
                        </div>
                      </div>

                      <div className="py-2 space-y-1">
                        {isSuper && (
                          <>
                            <button
                              onClick={() => {
                                onTabChange('superadmin');
                                setIsProfileDropdownOpen(false);
                              }}
                              className="w-full text-left px-3 py-2 rounded-xl text-xs font-bold hover:bg-amber-50 flex items-center gap-2.5 text-amber-900 transition"
                            >
                              <Sparkles className="w-4 h-4 text-amber-600" />
                              <span>👑 Pusat Kontrol Superadmin</span>
                            </button>
                            <button
                              onClick={() => {
                                onTabChange('users');
                                setIsProfileDropdownOpen(false);
                              }}
                              className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                            >
                              <Users className="w-4 h-4 text-[#3A7D8A]" />
                              <span>Kelola Pengguna &amp; Role</span>
                            </button>
                          </>
                        )}

                        {!isGA && (
                          <button
                            onClick={() => {
                              onOpenSettings();
                              setIsProfileDropdownOpen(false);
                            }}
                            className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                          >
                            <Settings className="w-4 h-4 text-slate-500" />
                            <span>Integrasi Google Apps Script</span>
                          </button>
                        )}

                        <button
                          onClick={() => {
                            onOpenLogin();
                            setIsProfileDropdownOpen(false);
                          }}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2.5 text-slate-700 transition"
                        >
                          <LogIn className="w-4 h-4 text-blue-600" />
                          <span>Ganti Akun / Peran</span>
                        </button>
                      </div>

                      <div className="border-t border-slate-100 pt-2">
                        <button
                          onClick={() => {
                            onLogout();
                            setIsProfileDropdownOpen(false);
                            onOpenLogin();
                          }}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2.5 transition"
                        >
                          <LogOut className="w-4 h-4" />
                          <span>Keluar (Logout)</span>
                        </button>
                      </div>
                    </div>
                  </>
                )}
              </div>
            ) : (
              <button
                onClick={onOpenLogin}
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition"
              >
                <LogIn className="w-3.5 h-3.5" />
                <span>Masuk</span>
              </button>
            )}

            {/* Mobile Menu Button (Hamburger) */}
            <button
              onClick={() => setIsMenuOpen(true)}
              className="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 border border-white/10 transition"
              aria-label="Buka menu navigasi"
            >
              <Menu className="w-5 h-5 text-amber-400" />
            </button>
          </div>
        </div>
      </header>

      {/* Hamburger Drawer Overlay for Mobile & Tablet */}
      {isMenuOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <div
            onClick={() => setIsMenuOpen(false)}
            className="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
          />

          <aside className="relative w-full max-w-xs sm:max-w-sm bg-[#0F1D2E] border-l border-slate-800 text-white shadow-2xl flex flex-col h-full z-10 animate-in slide-in-from-right duration-200">
            {/* Drawer Header */}
            <div className="p-4 border-b border-slate-800 flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <div className="bg-white px-2 py-1 rounded-lg">
                  <span className="font-black text-[#0F1D2E] text-xs font-sans">SYAMANAH</span>
                </div>
                <span className="font-bold text-sm text-white">Menu Navigasi</span>
              </div>
              <button
                onClick={() => setIsMenuOpen(false)}
                className="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {/* Role Notice Card */}
            <div className="p-4 bg-slate-900/80 border-b border-slate-800 text-xs">
              <p className="text-slate-400 text-[11px]">Sedang Masuk Sebagai:</p>
              <p className="font-bold text-white text-sm mt-0.5">{currentUser?.name || 'Tamu'}</p>
              <div className="mt-1.5">
                {currentUser?.role === 'superadmin' && (
                  <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">
                    👑 SUPERADMIN
                  </span>
                )}
                {currentUser?.role === 'manager' && (
                  <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">
                    👔 MANAGER
                  </span>
                )}
                {currentUser?.role === 'general_affair' && (
                  <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-400 border border-teal-500/30">
                    🏢 GENERAL AFFAIR (SPEK HARDWARE)
                  </span>
                )}
              </div>
            </div>

            {/* Nav list - Categorized Sections */}
            <div className="flex-1 overflow-y-auto p-4 space-y-4 text-xs">
              {/* Section 1: Inventaris & Modul */}
              <div>
                <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">
                  Inventaris &amp; Modul
                </p>
                <div className="space-y-1">
                  {!isGA && (
                    <button
                      onClick={() => handleNavigate('spreadsheet')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold transition ${
                        activeTab === 'spreadsheet'
                          ? 'bg-[#3A7D8A] text-white shadow-sm'
                          : 'text-slate-300 hover:bg-slate-800'
                      }`}
                    >
                      <div className="flex items-center gap-2.5">
                        <Table className="w-4 h-4 text-emerald-300" />
                        <span>Data Spreadsheet 1:1</span>
                      </div>
                      <span className="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">
                        {totalSubmissions}
                      </span>
                    </button>
                  )}

                  <button
                    onClick={() => handleNavigate('inventory')}
                    className={`w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'inventory'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Laptop className="w-4 h-4 text-blue-300" />
                      <span>Laptop &amp; PC Workstation</span>
                    </div>
                    <span className="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">
                      {deviceCount}
                    </span>
                  </button>

                  <button
                    onClick={() => handleNavigate('phones')}
                    className={`w-full text-left px-3 py-2 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'phones'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Smartphone className="w-4 h-4 text-amber-300" />
                      <span>HP Android Sales</span>
                    </div>
                    <span className="text-[11px] bg-black/30 px-2 py-0.5 rounded-full font-mono">
                      {phoneCount}
                    </span>
                  </button>
                </div>
              </div>

              {/* Section 2: Jaringan & Keamanan */}
              {!isGA && (
                <div>
                  <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">
                    Jaringan &amp; Audit
                  </p>
                  <div className="space-y-1">
                    <button
                      onClick={() => handleNavigate('mikrotik')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                        activeTab === 'mikrotik'
                          ? 'bg-[#3A7D8A] text-white shadow-sm'
                          : 'text-slate-300 hover:bg-slate-800'
                      }`}
                    >
                      <Wifi className="w-4 h-4 text-teal-300" />
                      <span>Hotspot Jaringan MikroTik</span>
                    </button>

                    <button
                      onClick={() => handleNavigate('audit')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                        activeTab === 'audit'
                          ? 'bg-[#3A7D8A] text-white shadow-sm'
                          : 'text-slate-300 hover:bg-slate-800'
                      }`}
                    >
                      <ShieldCheck className="w-4 h-4 text-indigo-300" />
                      <span>Audit Kredensial Perusahaan</span>
                    </button>
                  </div>
                </div>
              )}

              {/* Section 3: Formulir Input */}
              <div>
                <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">
                  Input &amp; Tambah Aset
                </p>
                <div className="space-y-1">
                  <button
                    onClick={() => handleNavigate('pc')}
                    className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                      activeTab === 'pc'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <PlusCircle className="w-4 h-4 text-[#3A7D8A]" />
                    <span>Form Input Laptop &amp; PC</span>
                  </button>

                  {!isGA && (
                    <button
                      onClick={() => handleNavigate('akun')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                        activeTab === 'akun'
                          ? 'bg-[#3A7D8A] text-white shadow-sm'
                          : 'text-slate-300 hover:bg-slate-800'
                      }`}
                    >
                      <PlusCircle className="w-4 h-4 text-amber-500" />
                      <span>Form Input HP &amp; Akun Sales</span>
                    </button>
                  )}
                </div>
              </div>

              {/* Section 4: Superadmin Only */}
              {isSuper && (
                <div>
                  <p className="text-[10px] font-bold uppercase tracking-wider text-amber-400/80 px-2 mb-1.5">
                    Kontrol Superadmin
                  </p>
                  <div className="space-y-1">
                    <button
                      onClick={() => handleNavigate('superadmin')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-bold transition ${
                        activeTab === 'superadmin'
                          ? 'bg-amber-500 text-slate-950 shadow-sm'
                          : 'text-amber-300 hover:bg-slate-800'
                      }`}
                    >
                      <Sparkles className="w-4 h-4 text-amber-400" />
                      <span>👑 Pusat Kontrol Superadmin</span>
                    </button>

                    <button
                      onClick={() => handleNavigate('users')}
                      className={`w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                        activeTab === 'users'
                          ? 'bg-amber-500/20 text-amber-300'
                          : 'text-slate-300 hover:bg-slate-800'
                      }`}
                    >
                      <Users className="w-4 h-4 text-amber-400" />
                      <span>Kelola Pengguna &amp; Role</span>
                    </button>
                  </div>
                </div>
              )}

              {/* Section 5: Bantuan & Integrasi */}
              <div>
                <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 mb-1.5">
                  Bantuan &amp; Panduan
                </p>
                <div className="space-y-1">
                  <button
                    onClick={() => handleNavigate('dxdiag_guide')}
                    className="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition"
                  >
                    <HelpCircle className="w-4 h-4 text-blue-400" />
                    <span>Panduan Cek DxDiag Windows</span>
                  </button>

                  {!isGA && (
                    <>
                      <button
                        onClick={() => {
                          setIsMenuOpen(false);
                          onOpenDriveGuide();
                        }}
                        className="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition"
                      >
                        <FolderOpen className="w-4 h-4 text-emerald-400" />
                        <span>Panduan Google Drive</span>
                      </button>

                      <button
                        onClick={() => {
                          setIsMenuOpen(false);
                          onOpenSettings();
                        }}
                        className="w-full text-left px-3 py-2 rounded-xl flex items-center gap-2.5 font-semibold text-slate-300 hover:bg-slate-800 transition"
                      >
                        <Settings className="w-4 h-4 text-slate-400" />
                        <span>Pengaturan GAS &amp; Sheet</span>
                      </button>
                    </>
                  )}
                </div>
              </div>
            </div>

            {/* Drawer Bottom Action */}
            <div className="p-4 border-t border-slate-800 bg-slate-900/60 flex items-center justify-between">
              <button
                onClick={() => {
                  setIsMenuOpen(false);
                  onOpenLogin();
                }}
                className="flex items-center gap-2 text-xs font-bold text-slate-300 hover:text-white"
              >
                <LogIn className="w-4 h-4 text-[#3A7D8A]" />
                <span>Ganti Akun</span>
              </button>

              <button
                onClick={() => {
                  onLogout();
                  setIsMenuOpen(false);
                  onOpenLogin();
                }}
                className="flex items-center gap-1.5 text-xs font-bold text-red-400 hover:text-red-300"
              >
                <LogOut className="w-4 h-4" />
                <span>Keluar</span>
              </button>
            </div>
          </aside>
        </div>
      )}
    </>
  );
};
