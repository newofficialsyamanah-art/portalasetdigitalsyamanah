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
  ChevronRight,
  User,
  LogIn,
  LogOut,
  Users,
  Briefcase,
  Building,
  History,
  Lock,
  ChevronDown,
  Sparkles
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

  // Close hamburger drawer when Escape is pressed
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setIsMenuOpen(false);
        setIsProfileDropdownOpen(false);
      }
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
  }, [isMenuOpen]);

  const handleNavigate = (tab: TabType) => {
    onTabChange(tab);
    setIsMenuOpen(false);
  };

  const isGA = currentUser?.role === 'general_affair';
  const isMgr = currentUser?.role === 'manager';
  const isSuper = currentUser?.role === 'superadmin' || !currentUser;

  return (
    <>
      {/* Sleek Modern Header with Glassmorphism & Syamanah Colors */}
      <header className="bg-[#0F1D2E]/95 backdrop-blur-md text-white border-b border-slate-800/80 sticky top-0 z-30 shadow-lg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
          
          {/* Brand Identity - Syamanah.com Inspired */}
          <div
            onClick={() => handleNavigate(isGA ? 'inventory' : 'spreadsheet')}
            className="flex items-center gap-3 cursor-pointer select-none group shrink-0"
          >
            <div className="flex items-center bg-white px-2.5 py-1.5 rounded-xl shadow-sm border border-white/20 group-hover:scale-105 transition-transform duration-200">
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

            <div className="hidden sm:flex flex-col">
              <div className="flex items-center gap-1.5">
                <span className="font-extrabold text-white tracking-wider text-xs sm:text-sm">
                  PORTAL ASET &amp; AKUN
                </span>
                <span className="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-2xs">
                  PRO
                </span>
              </div>
              <span className="text-[10px] text-slate-300 font-medium">
                PT Generasi Sugih Sejahtera
              </span>
            </div>
          </div>

          {/* Center Segmented Pill Navigation */}
          <nav className="hidden md:flex items-center bg-slate-900/80 p-1.5 rounded-full border border-slate-750/70 shadow-inner">
            {isGA ? (
              /* General Affair Navigation (Hardware Only) */
              <>
                <button
                  onClick={() => onTabChange('inventory')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 transition-all duration-200 ${
                    activeTab === 'inventory'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Laptop className="w-3.5 h-3.5" />
                  <span>Laptop &amp; PC</span>
                  <span className="text-[10px] bg-black/25 px-1.5 py-0.5 rounded-full font-mono">
                    {deviceCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('phones')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 transition-all duration-200 ${
                    activeTab === 'phones'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Smartphone className="w-3.5 h-3.5" />
                  <span>HP Android</span>
                  <span className="text-[10px] bg-black/25 px-1.5 py-0.5 rounded-full font-mono">
                    {phoneCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('pc')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 transition-all duration-200 ${
                    activeTab === 'pc'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Monitor className="w-3.5 h-3.5" />
                  <span>Input PC</span>
                </button>

                <button
                  onClick={() => onTabChange('dxdiag_guide')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 transition-all duration-200 ${
                    activeTab === 'dxdiag_guide'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <HelpCircle className="w-3.5 h-3.5" />
                  <span>DxDiag</span>
                </button>
              </>
            ) : (
              /* Superadmin & Manager Navigation */
              <>
                <button
                  onClick={() => onTabChange('spreadsheet')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'spreadsheet'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Table className="w-3.5 h-3.5" />
                  <span>Spreadsheet</span>
                  <span className="text-[10px] bg-black/25 px-1.5 py-0.5 rounded-full font-mono">
                    {totalSubmissions}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('inventory')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'inventory'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Laptop className="w-3.5 h-3.5" />
                  <span>Laptop &amp; PC</span>
                  <span className="text-[10px] bg-black/25 px-1.5 py-0.5 rounded-full font-mono">
                    {deviceCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('phones')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'phones'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Smartphone className="w-3.5 h-3.5" />
                  <span>HP Sales</span>
                  <span className="text-[10px] bg-black/25 px-1.5 py-0.5 rounded-full font-mono">
                    {phoneCount}
                  </span>
                </button>

                <button
                  onClick={() => onTabChange('mikrotik')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'mikrotik'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <Wifi className="w-3.5 h-3.5" />
                  <span>MikroTik</span>
                </button>

                <button
                  onClick={() => onTabChange('audit')}
                  className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                    activeTab === 'audit'
                      ? 'bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] text-white shadow-sm'
                      : 'text-slate-300 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <ShieldCheck className="w-3.5 h-3.5" />
                  <span>Audit Kredensial</span>
                </button>

                {/* Superadmin Tab */}
                {isSuper && (
                  <button
                    onClick={() => onTabChange('users')}
                    className={`px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 transition-all duration-200 ${
                      activeTab === 'users'
                        ? 'bg-amber-500 text-slate-950 shadow-sm'
                        : 'text-amber-400 hover:text-amber-300 hover:bg-white/10'
                    }`}
                  >
                    <Users className="w-3.5 h-3.5" />
                    <span>Kelola Role</span>
                  </button>
                )}
              </>
            )}
          </nav>

          {/* Right Action: Role Badge & Profile */}
          <div className="flex items-center gap-3">
            {currentUser ? (
              <div className="relative">
                <button
                  onClick={() => setIsProfileDropdownOpen(!isProfileDropdownOpen)}
                  className={`flex items-center gap-2.5 px-3 py-1.5 rounded-full border transition-all duration-200 ${
                    currentUser.role === 'superadmin'
                      ? 'bg-amber-500/10 border-amber-500/30 hover:bg-amber-500/20'
                      : currentUser.role === 'manager'
                      ? 'bg-blue-500/10 border-blue-500/30 hover:bg-blue-500/20'
                      : 'bg-teal-500/10 border-teal-500/30 hover:bg-teal-500/20'
                  }`}
                >
                  <div
                    className={`w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs uppercase text-white shadow-xs shrink-0 ${
                      currentUser.role === 'superadmin'
                        ? 'bg-amber-500 text-slate-950 font-black'
                        : currentUser.role === 'manager'
                        ? 'bg-blue-600'
                        : 'bg-teal-600'
                    }`}
                  >
                    {currentUser.name.charAt(0)}
                  </div>

                  <div className="hidden lg:flex flex-col text-left">
                    <span className="text-xs font-bold text-white leading-tight">
                      {currentUser.name}
                    </span>
                    <span className="text-[10px] font-semibold leading-tight">
                      {currentUser.role === 'superadmin' && (
                        <span className="text-amber-400">👑 Superadmin</span>
                      )}
                      {currentUser.role === 'manager' && (
                        <span className="text-blue-300">👔 Manager</span>
                      )}
                      {currentUser.role === 'general_affair' && (
                        <span className="text-teal-300">🏢 General Affair</span>
                      )}
                    </span>
                  </div>

                  <ChevronDown className="w-3.5 h-3.5 text-slate-400" />
                </button>

                {/* Modern Dropdown */}
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
                        <div className="mt-2 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                          PERAN: {currentUser.role.toUpperCase()}
                        </div>
                      </div>

                      <div className="py-2 space-y-1">
                        {isSuper && (
                          <button
                            onClick={() => {
                              onTabChange('users');
                              setIsProfileDropdownOpen(false);
                            }}
                            className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2 text-slate-700 transition"
                          >
                            <Users className="w-4 h-4 text-[#3A7D8A]" />
                            <span>Kelola Pengguna &amp; Role</span>
                          </button>
                        )}

                        <button
                          onClick={() => {
                            onOpenLogin();
                            setIsProfileDropdownOpen(false);
                          }}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 flex items-center gap-2 text-slate-700 transition"
                        >
                          <LogIn className="w-4 h-4 text-blue-600" />
                          <span>Ganti Akun / Role</span>
                        </button>
                      </div>

                      <div className="border-t border-slate-100 pt-2">
                        <button
                          onClick={() => {
                            onLogout();
                            setIsProfileDropdownOpen(false);
                            onOpenLogin();
                          }}
                          className="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2 transition"
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
                className="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gradient-to-r from-[#3A7D8A] to-[#2E5190] hover:brightness-110 text-white font-bold text-xs shadow-md transition"
              >
                <LogIn className="w-3.5 h-3.5" />
                <span>Masuk</span>
              </button>
            )}

            {/* Mobile Menu Button */}
            <button
              onClick={() => setIsMenuOpen(true)}
              className="md:hidden p-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 border border-white/10 transition"
              aria-label="Buka menu navigasi"
            >
              <Menu className="w-5 h-5 text-amber-400" />
            </button>
          </div>
        </div>
      </header>

      {/* Hamburger Drawer Overlay for Mobile */}
      {isMenuOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <div
            onClick={() => setIsMenuOpen(false)}
            className="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
          />

          <aside className="relative w-full max-w-xs sm:max-w-sm bg-[#0F1D2E] border-l border-slate-800 text-white shadow-2xl flex flex-col h-full z-10 animate-in slide-in-from-right duration-200">
            {/* Drawer Header */}
            <div className="p-4 border-b border-slate-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="bg-white px-2 py-1 rounded-lg">
                  <span className="font-extrabold text-[#0F1D2E] text-xs font-sans">SYAMANAH</span>
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

            {/* Role Notice */}
            <div className="p-4 bg-slate-900/60 border-b border-slate-800 text-xs">
              <p className="text-slate-400 text-[11px]">Sedang Masuk Sebagai:</p>
              <p className="font-bold text-white text-sm mt-0.5">{currentUser?.name || 'Tamu'}</p>
              <div className="mt-1">
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

            {/* Nav list */}
            <div className="flex-1 overflow-y-auto p-4 space-y-2 text-sm">
              {isGA ? (
                <>
                  <button
                    onClick={() => handleNavigate('inventory')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'inventory'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Laptop className="w-4 h-4 text-teal-300" />
                      <span>Katalog Laptop &amp; PC</span>
                    </div>
                    <span className="text-xs bg-black/30 px-2 py-0.5 rounded-full">{deviceCount}</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('phones')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'phones'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Smartphone className="w-4 h-4 text-teal-300" />
                      <span>Katalog HP Android</span>
                    </div>
                    <span className="text-xs bg-black/30 px-2 py-0.5 rounded-full">{phoneCount}</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('pc')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                      activeTab === 'pc'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <Monitor className="w-4 h-4 text-blue-300" />
                    <span>Input Perangkat PC</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('dxdiag_guide')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                      activeTab === 'dxdiag_guide'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <HelpCircle className="w-4 h-4 text-amber-300" />
                    <span>Panduan DxDiag</span>
                  </button>
                </>
              ) : (
                <>
                  <button
                    onClick={() => handleNavigate('spreadsheet')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'spreadsheet'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Table className="w-4 h-4 text-amber-400" />
                      <span>Data Spreadsheet</span>
                    </div>
                    <span className="text-xs bg-black/30 px-2 py-0.5 rounded-full">{totalSubmissions}</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('inventory')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'inventory'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Laptop className="w-4 h-4 text-blue-300" />
                      <span>Laptop &amp; PC</span>
                    </div>
                    <span className="text-xs bg-black/30 px-2 py-0.5 rounded-full">{deviceCount}</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('phones')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center justify-between font-semibold transition ${
                      activeTab === 'phones'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Smartphone className="w-4 h-4 text-amber-300" />
                      <span>HP Sales</span>
                    </div>
                    <span className="text-xs bg-black/30 px-2 py-0.5 rounded-full">{phoneCount}</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('mikrotik')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                      activeTab === 'mikrotik'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <Wifi className="w-4 h-4 text-emerald-300" />
                    <span>Akun MikroTik</span>
                  </button>

                  <button
                    onClick={() => handleNavigate('audit')}
                    className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 font-semibold transition ${
                      activeTab === 'audit'
                        ? 'bg-[#3A7D8A] text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <ShieldCheck className="w-4 h-4 text-teal-300" />
                    <span>Audit Kredensial</span>
                  </button>

                  {isSuper && (
                    <button
                      onClick={() => handleNavigate('users')}
                      className={`w-full text-left px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 font-bold transition ${
                        activeTab === 'users'
                          ? 'bg-amber-500 text-slate-950 shadow-sm'
                          : 'text-amber-300 hover:bg-slate-800'
                      }`}
                    >
                      <Users className="w-4 h-4" />
                      <span>Kelola Pengguna &amp; Role</span>
                    </button>
                  )}
                </>
              )}
            </div>

            {/* Drawer Bottom */}
            <div className="p-4 border-t border-slate-800 bg-slate-900/40 flex items-center justify-between">
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
