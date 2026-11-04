import React, { useState } from 'react';
import {
  ShieldCheck,
  User,
  Lock,
  Eye,
  EyeOff,
  LogIn,
  CheckCircle2,
  AlertCircle,
  Building,
  Laptop,
  Briefcase
} from 'lucide-react';
import { UserAccount, UserRole } from '../types';
import { loginWithApi } from '../services/authService';

interface LoginModalProps {
  isOpen: boolean;
  onClose: () => void;
  currentUser: UserAccount | null;
  onLoginSuccess: (user: UserAccount) => void;
}

export const LoginModal: React.FC<LoginModalProps> = ({
  isOpen,
  onClose,
  currentUser,
  onLoginSuccess
}) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  if (!isOpen) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!username.trim() || !password) {
      setErrorMessage('Silakan isi username dan password');
      return;
    }

    setIsLoading(true);
    setErrorMessage(null);

    const result = await loginWithApi(username.trim(), password);
    setIsLoading(false);

    if (result.success && result.user) {
      onLoginSuccess(result.user);
      onClose();
    } else {
      setErrorMessage(result.message || 'Username atau password tidak sesuai');
    }
  };

  const handleQuickLogin = async (roleUsername: string) => {
    setIsLoading(true);
    setErrorMessage(null);
    const result = await loginWithApi(roleUsername, 'password123');
    setIsLoading(false);

    if (result.success && result.user) {
      onLoginSuccess(result.user);
      onClose();
    } else {
      // If custom password was set, fill the form
      setUsername(roleUsername);
      setPassword('');
      setErrorMessage('Silakan masukkan password untuk akun ' + roleUsername);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      {/* Backdrop */}
      <div
        onClick={onClose}
        className="fixed inset-0 bg-[#0F1D2E]/80 backdrop-blur-sm transition-opacity"
      />

      {/* Modal Dialog */}
      <div className="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-200">
        {/* Header with Syamanah Brand Palette */}
        <div className="bg-gradient-to-r from-[#0F1D2E] via-[#1a3452] to-[#3A7D8A] p-6 text-white text-center relative">
          <div className="flex justify-center mb-3">
            <img
              src="/logo-syamanah.png"
              alt="Logo Syamanah"
              className="h-10 object-contain drop-shadow"
              onError={(e) => {
                // Fallback to text badge if logo failed to load
                e.currentTarget.style.display = 'none';
              }}
            />
          </div>
          <h2 className="text-xl font-bold tracking-tight">Portal Aset &amp; Akun</h2>
          <p className="text-xs text-slate-200/90 mt-1">
            Masuk untuk mengakses sistem sesuai peran (Role) Anda
          </p>

          {currentUser && (
            <div className="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-xs backdrop-blur-xs text-slate-100 border border-white/20">
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span>Saat ini: <strong>{currentUser.name}</strong> ({currentUser.role})</span>
            </div>
          )}
        </div>

        {/* Content Body */}
        <div className="p-6 space-y-6">
          {/* Quick Switch / Demo Buttons */}
          <div>
            <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
              Pilihan Cepat Masuk Berdasarkan Role:
            </label>
            <div className="grid grid-cols-3 gap-2">
              <button
                type="button"
                onClick={() => handleQuickLogin('superadmin')}
                className="p-2.5 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100/80 text-amber-900 flex flex-col items-center justify-center text-center transition group shadow-2xs"
              >
                <ShieldCheck className="w-5 h-5 text-amber-600 mb-1 group-hover:scale-110 transition" />
                <span className="text-xs font-bold leading-tight">Superadmin</span>
                <span className="text-[10px] text-amber-700/80">Semua Akses</span>
              </button>

              <button
                type="button"
                onClick={() => handleQuickLogin('manager')}
                className="p-2.5 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100/80 text-blue-900 flex flex-col items-center justify-center text-center transition group shadow-2xs"
              >
                <Briefcase className="w-5 h-5 text-blue-600 mb-1 group-hover:scale-110 transition" />
                <span className="text-xs font-bold leading-tight">Manager</span>
                <span className="text-[10px] text-blue-700/80">Aset &amp; Akun</span>
              </button>

              <button
                type="button"
                onClick={() => handleQuickLogin('ga')}
                className="p-2.5 rounded-xl border border-teal-200 bg-teal-50 hover:bg-teal-100/80 text-teal-900 flex flex-col items-center justify-center text-center transition group shadow-2xs"
              >
                <Building className="w-5 h-5 text-teal-600 mb-1 group-hover:scale-110 transition" />
                <span className="text-xs font-bold leading-tight">General Affair</span>
                <span className="text-[10px] text-teal-700/80">Spek Hardware</span>
              </button>
            </div>
            <p className="text-[11px] text-slate-400 mt-1.5 text-center">
              *Klik salah satu role di atas untuk login instan atau isi form di bawah
            </p>
          </div>

          <div className="relative flex py-1 items-center">
            <div className="flex-grow border-t border-slate-200"></div>
            <span className="flex-shrink mx-3 text-xs font-medium text-slate-400">Atau Manual</span>
            <div className="flex-grow border-t border-slate-200"></div>
          </div>

          {/* Error Notice */}
          {errorMessage && (
            <div className="p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
              <AlertCircle className="w-4 h-4 shrink-0 text-red-500" />
              <span>{errorMessage}</span>
            </div>
          )}

          {/* Form */}
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1">
                Username
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <User className="w-4 h-4" />
                </div>
                <input
                  type="text"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  placeholder="superadmin / manager / ga"
                  className="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A] focus:border-transparent transition"
                  required
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1">
                Password
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <Lock className="w-4 h-4" />
                </div>
                <input
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="Masukkan password..."
                  className="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A] focus:border-transparent transition"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className="w-full py-2.5 px-4 rounded-xl bg-[#3A7D8A] hover:bg-[#2e646f] active:bg-[#25525c] text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-sm transition disabled:opacity-50"
            >
              {isLoading ? (
                <span>Memverifikasi...</span>
              ) : (
                <>
                  <LogIn className="w-4 h-4" />
                  <span>Masuk ke Portal</span>
                </>
              )}
            </button>
          </form>

          {/* Role Policy Explanation */}
          <div className="bg-slate-50 rounded-xl p-3 border border-slate-100 text-[11px] text-slate-600 space-y-1">
            <p className="font-semibold text-slate-700 flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-[#3A7D8A]" />
              Ketentuan Hak Akses Role:
            </p>
            <ul className="list-disc pl-4 space-y-0.5 text-slate-500">
              <li><strong>General Affair (GA)</strong>: Hanya dapat melihat nama pemakai dan spesifikasi hardware (Laptop, PC, Android). Data akun perusahaan, password &amp; email dibatasi.</li>
              <li><strong>Manager</strong>: Akses penuh inventaris perangkat &amp; akun operasional.</li>
              <li><strong>Superadmin</strong>: Akses penuh seluruh sistem serta pengelolaan pengguna &amp; role.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

