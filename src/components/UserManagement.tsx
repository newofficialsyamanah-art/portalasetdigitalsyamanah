import React, { useState, useEffect } from 'react';
import {
  ShieldCheck,
  UserPlus,
  Edit2,
  Trash2,
  Search,
  Key,
  Building,
  Briefcase,
  CheckCircle2,
  AlertCircle,
  X,
  Lock,
  User,
  RefreshCw,
  HelpCircle
} from 'lucide-react';
import { UserAccount, UserRole } from '../types';
import {
  fetchAllUsers,
  createUser,
  updateUser,
  deleteUser
} from '../services/authService';

interface UserManagementProps {
  currentUser: UserAccount | null;
  onRefreshSession?: () => void;
}

export const UserManagement: React.FC<UserManagementProps> = ({
  currentUser,
  onRefreshSession
}) => {
  const [users, setUsers] = useState<UserAccount[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const [roleFilter, setRoleFilter] = useState<'all' | UserRole>('all');

  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<UserAccount | null>(null);
  const [formName, setFormName] = useState('');
  const [formUsername, setFormUsername] = useState('');
  const [formPassword, setFormPassword] = useState('');
  const [formRole, setFormRole] = useState<UserRole>('general_affair');
  const [modalError, setModalError] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Delete Confirm State
  const [deleteTarget, setDeleteTarget] = useState<UserAccount | null>(null);

  const loadUsers = async () => {
    setIsLoading(true);
    const list = await fetchAllUsers();
    setUsers(list);
    setIsLoading(false);
  };

  useEffect(() => {
    loadUsers();
  }, []);

  const handleOpenAdd = () => {
    setEditingUser(null);
    setFormName('');
    setFormUsername('');
    setFormPassword('');
    setFormRole('general_affair');
    setModalError(null);
    setIsModalOpen(true);
  };

  const handleOpenEdit = (user: UserAccount) => {
    setEditingUser(user);
    setFormName(user.name);
    setFormUsername(user.username);
    setFormPassword('');
    setFormRole(user.role);
    setModalError(null);
    setIsModalOpen(true);
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formName.trim() || (!editingUser && !formUsername.trim())) {
      setModalError('Nama dan Username wajib diisi');
      return;
    }

    setIsSubmitting(true);
    setModalError(null);

    if (editingUser) {
      // Update
      const res = await updateUser(editingUser.id, {
        name: formName.trim(),
        role: formRole,
        password: formPassword.trim() ? formPassword.trim() : undefined
      });
      setIsSubmitting(false);
      if (res.success) {
        setIsModalOpen(false);
        loadUsers();
        if (onRefreshSession && editingUser.id === currentUser?.id) {
          onRefreshSession();
        }
      } else {
        setModalError(res.message || 'Gagal memperbarui pengguna');
      }
    } else {
      // Create
      const res = await createUser({
        name: formName.trim(),
        username: formUsername.trim().toLowerCase(),
        role: formRole,
        password: formPassword.trim() || 'password123'
      });
      setIsSubmitting(false);
      if (res.success) {
        setIsModalOpen(false);
        loadUsers();
      } else {
        setModalError(res.message || 'Gagal menambahkan pengguna');
      }
    }
  };

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setIsSubmitting(true);
    const res = await deleteUser(deleteTarget.id);
    setIsSubmitting(false);
    setDeleteTarget(null);
    if (res.success) {
      loadUsers();
    } else {
      alert(res.message || 'Gagal menghapus pengguna');
    }
  };

  const filteredUsers = users.filter((u) => {
    const matchSearch =
      u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      u.username.toLowerCase().includes(searchTerm.toLowerCase());
    const matchRole = roleFilter === 'all' || u.role === roleFilter;
    return matchSearch && matchRole;
  });

  const countSuperadmin = users.filter((u) => u.role === 'superadmin').length;
  const countManager = users.filter((u) => u.role === 'manager').length;
  const countGA = users.filter((u) => u.role === 'general_affair').length;

  return (
    <div className="space-y-6">
      {/* Header Banner - Syamanah Theme */}
      <div className="bg-gradient-to-r from-[#0F1D2E] via-[#1b3a5b] to-[#3A7D8A] rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-medium text-amber-300 mb-2 border border-white/10">
            <ShieldCheck className="w-3.5 h-3.5" />
            <span>Otoritas Superadmin</span>
          </div>
          <h1 className="text-2xl font-bold tracking-tight">Manajemen Pengguna &amp; Role</h1>
          <p className="text-xs sm:text-sm text-slate-200 mt-1 max-w-2xl leading-relaxed">
            Atur akun pengguna dan batasan peran. Akun <strong>General Affair (GA)</strong> otomatis
            dibatasi hanya melihat nama pemilik dan spesifikasi hardware (PC/Laptop/Android) tanpa akses ke data kredensial perusahaan.
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="self-start md:self-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-sm shadow-md transition shrink-0"
        >
          <UserPlus className="w-4 h-4" />
          <span>Tambah Pengguna</span>
        </button>
      </div>

      {/* Role Summary Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
          <p className="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Pengguna</p>
          <p className="text-2xl font-black text-slate-900 mt-1">{users.length}</p>
          <p className="text-[11px] text-slate-400 mt-0.5">Terdaftar di sistem</p>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-amber-200/80 bg-amber-50/20 shadow-2xs">
          <div className="flex items-center justify-between">
            <p className="text-xs font-semibold text-amber-800 uppercase tracking-wider">Superadmin</p>
            <ShieldCheck className="w-4 h-4 text-amber-600" />
          </div>
          <p className="text-2xl font-black text-amber-900 mt-1">{countSuperadmin}</p>
          <p className="text-[11px] text-amber-700/80 mt-0.5">Akses penuh &amp; kelola user</p>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-blue-200/80 bg-blue-50/20 shadow-2xs">
          <div className="flex items-center justify-between">
            <p className="text-xs font-semibold text-blue-800 uppercase tracking-wider">Manager</p>
            <Briefcase className="w-4 h-4 text-blue-600" />
          </div>
          <p className="text-2xl font-black text-blue-900 mt-1">{countManager}</p>
          <p className="text-[11px] text-blue-700/80 mt-0.5">Akses perangkat &amp; akun sales</p>
        </div>

        <div className="bg-white rounded-2xl p-4 border border-teal-200/80 bg-teal-50/20 shadow-2xs">
          <div className="flex items-center justify-between">
            <p className="text-xs font-semibold text-teal-800 uppercase tracking-wider">General Affair</p>
            <Building className="w-4 h-4 text-teal-600" />
          </div>
          <p className="text-2xl font-black text-teal-900 mt-1">{countGA}</p>
          <p className="text-[11px] text-teal-700/80 mt-0.5">Hanya hardware &amp; nama pemakai</p>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <div className="relative w-full sm:w-80">
          <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Cari nama atau username..."
            className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A]"
          />
        </div>

        <div className="flex items-center gap-2 w-full sm:w-auto">
          <button
            onClick={() => setRoleFilter('all')}
            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
              roleFilter === 'all'
                ? 'bg-[#0F1D2E] text-white'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            Semua Role
          </button>
          <button
            onClick={() => setRoleFilter('superadmin')}
            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
              roleFilter === 'superadmin'
                ? 'bg-amber-600 text-white'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            Superadmin
          </button>
          <button
            onClick={() => setRoleFilter('manager')}
            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
              roleFilter === 'manager'
                ? 'bg-blue-600 text-white'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            Manager
          </button>
          <button
            onClick={() => setRoleFilter('general_affair')}
            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
              roleFilter === 'general_affair'
                ? 'bg-teal-600 text-white'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            General Affair
          </button>

          <button
            onClick={loadUsers}
            className="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
            title="Muat ulang data"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${isLoading ? 'animate-spin' : ''}`} />
          </button>
        </div>
      </div>

      {/* Users Table */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider font-semibold">
              <tr>
                <th className="py-3 px-4">Nama Lengkap</th>
                <th className="py-3 px-4">Username</th>
                <th className="py-3 px-4">Role / Peran</th>
                <th className="py-3 px-4">Batasan Hak Akses</th>
                <th className="py-3 px-4">Terdaftar</th>
                <th className="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredUsers.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-slate-400 text-sm">
                    Tidak ada pengguna yang cocok dengan pencarian
                  </td>
                </tr>
              ) : (
                filteredUsers.map((u) => {
                  const isCurrent = currentUser?.id === u.id;
                  return (
                    <tr key={u.id} className="hover:bg-slate-50/70 transition">
                      <td className="py-3.5 px-4 font-semibold text-slate-900 flex items-center gap-2.5">
                        <div
                          className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs uppercase text-white shadow-2xs ${
                            u.role === 'superadmin'
                              ? 'bg-amber-500'
                              : u.role === 'manager'
                              ? 'bg-blue-600'
                              : 'bg-teal-600'
                          }`}
                        >
                          {u.name.charAt(0)}
                        </div>
                        <div>
                          <span>{u.name}</span>
                          {isCurrent && (
                            <span className="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">
                              Akun Anda
                            </span>
                          )}
                        </div>
                      </td>

                      <td className="py-3.5 px-4 font-mono text-xs text-slate-600">
                        @{u.username}
                      </td>

                      <td className="py-3.5 px-4">
                        {u.role === 'superadmin' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                            <ShieldCheck className="w-3.5 h-3.5 text-amber-600" />
                            Superadmin
                          </span>
                        )}
                        {u.role === 'manager' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-900 border border-blue-200">
                            <Briefcase className="w-3.5 h-3.5 text-blue-600" />
                            Manager
                          </span>
                        )}
                        {u.role === 'general_affair' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-900 border border-teal-200">
                            <Building className="w-3.5 h-3.5 text-teal-600" />
                            General Affair
                          </span>
                        )}
                      </td>

                      <td className="py-3.5 px-4 text-xs text-slate-600 max-w-xs">
                        {u.role === 'superadmin' && (
                          <span className="text-amber-800 font-medium">Akses mutlak (User, database, inventaris &amp; akun)</span>
                        )}
                        {u.role === 'manager' && (
                          <span className="text-blue-800 font-medium">Akses perangkat, akun sales &amp; spreadsheet</span>
                        )}
                        {u.role === 'general_affair' && (
                          <span className="text-teal-800 font-medium bg-teal-50 px-2 py-1 rounded border border-teal-100 inline-block">
                            Hanya nama pemakai &amp; spek hardware (Tanpa akun/password)
                          </span>
                        )}
                      </td>

                      <td className="py-3.5 px-4 text-xs text-slate-400">
                        {new Date(u.createdAt).toLocaleDateString('id-ID')}
                      </td>

                      <td className="py-3.5 px-4 text-right space-x-1">
                        <button
                          onClick={() => handleOpenEdit(u)}
                          className="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1 text-xs"
                          title="Edit Pengguna & Role"
                        >
                          <Edit2 className="w-4 h-4 text-blue-600" />
                        </button>

                        <button
                          onClick={() => setDeleteTarget(u)}
                          disabled={u.role === 'superadmin' && countSuperadmin <= 1}
                          className="p-1.5 rounded-lg text-slate-600 hover:bg-red-50 hover:text-red-600 transition inline-flex items-center gap-1 text-xs disabled:opacity-30 disabled:hover:bg-transparent"
                          title={
                            u.role === 'superadmin' && countSuperadmin <= 1
                              ? 'Tidak bisa menghapus satu-satunya superadmin'
                              : 'Hapus Pengguna'
                          }
                        >
                          <Trash2 className="w-4 h-4 text-red-500" />
                        </button>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Add / Edit Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div
            onClick={() => setIsModalOpen(false)}
            className="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"
          />

          <div className="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-200">
            <div className="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded-xl bg-[#3A7D8A] text-white flex items-center justify-center font-bold text-sm">
                  {editingUser ? <Edit2 className="w-4 h-4" /> : <UserPlus className="w-4 h-4" />}
                </div>
                <div>
                  <h3 className="font-bold text-slate-900 text-base">
                    {editingUser ? 'Edit Pengguna & Role' : 'Tambah Pengguna Baru'}
                  </h3>
                  <p className="text-xs text-slate-500">
                    {editingUser
                      ? `Memperbarui akun @${editingUser.username}`
                      : 'Buat akun baru dan tentukan hak akses perannya'}
                  </p>
                </div>
              </div>
              <button
                onClick={() => setIsModalOpen(false)}
                className="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 flex items-center justify-center"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleSave} className="p-6 space-y-4">
              {modalError && (
                <div className="p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                  <AlertCircle className="w-4 h-4 shrink-0 text-red-500" />
                  <span>{modalError}</span>
                </div>
              )}

              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">
                  Nama Lengkap *
                </label>
                <input
                  type="text"
                  value={formName}
                  onChange={(e) => setFormName(e.target.value)}
                  placeholder="Contoh: Rian Anggara"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A]"
                  required
                />
              </div>

              {!editingUser && (
                <div>
                  <label className="block text-xs font-semibold text-slate-700 mb-1">
                    Username *
                  </label>
                  <input
                    type="text"
                    value={formUsername}
                    onChange={(e) => setFormUsername(e.target.value.toLowerCase())}
                    placeholder="Contoh: rian.ga"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A]"
                    required
                  />
                  <p className="text-[11px] text-slate-400 mt-1">Gunakan huruf kecil tanpa spasi.</p>
                </div>
              )}

              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">
                  {editingUser ? 'Password Baru (Opsional)' : 'Password Masuk *'}
                </label>
                <input
                  type="password"
                  value={formPassword}
                  onChange={(e) => setFormPassword(e.target.value)}
                  placeholder={
                    editingUser
                      ? 'Kosongkan jika tidak ingin mengubah password'
                      : 'Masukkan password (default: password123)'
                  }
                  className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3A7D8A]"
                  {...(!editingUser ? { required: true } : {})}
                />
              </div>

              {/* Role Radio Cards */}
              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-2">
                  Pilih Peran / Role *
                </label>
                <div className="space-y-2">
                  {/* General Affair Card */}
                  <label
                    className={`block p-3 rounded-2xl border cursor-pointer transition ${
                      formRole === 'general_affair'
                        ? 'border-teal-500 bg-teal-50/70 ring-1 ring-teal-500'
                        : 'border-slate-200 hover:border-slate-300'
                    }`}
                  >
                    <div className="flex items-start gap-3">
                      <input
                        type="radio"
                        name="role"
                        value="general_affair"
                        checked={formRole === 'general_affair'}
                        onChange={() => setFormRole('general_affair')}
                        className="mt-1 text-teal-600 focus:ring-teal-500"
                      />
                      <div>
                        <div className="flex items-center gap-1.5 font-bold text-sm text-teal-900">
                          <Building className="w-4 h-4 text-teal-600" />
                          <span>General Affair (GA)</span>
                        </div>
                        <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                          Hanya dapat melihat <strong>nama pemakai</strong> dan <strong>spesifikasi hardware</strong> (Laptop, PC, Android, RAM, Processor, OS). Data akun perusahaan, password &amp; email dibatasi.
                        </p>
                      </div>
                    </div>
                  </label>

                  {/* Manager Card */}
                  <label
                    className={`block p-3 rounded-2xl border cursor-pointer transition ${
                      formRole === 'manager'
                        ? 'border-blue-500 bg-blue-50/70 ring-1 ring-blue-500'
                        : 'border-slate-200 hover:border-slate-300'
                    }`}
                  >
                    <div className="flex items-start gap-3">
                      <input
                        type="radio"
                        name="role"
                        value="manager"
                        checked={formRole === 'manager'}
                        onChange={() => setFormRole('manager')}
                        className="mt-1 text-blue-600 focus:ring-blue-500"
                      />
                      <div>
                        <div className="flex items-center gap-1.5 font-bold text-sm text-blue-900">
                          <Briefcase className="w-4 h-4 text-blue-600" />
                          <span>Manager</span>
                        </div>
                        <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                          Dapat melihat seluruh katalog perangkat hardware, spreadsheet, data akun sales/perusahaan, dan laporan ringkasan operasional.
                        </p>
                      </div>
                    </div>
                  </label>

                  {/* Superadmin Card */}
                  <label
                    className={`block p-3 rounded-2xl border cursor-pointer transition ${
                      formRole === 'superadmin'
                        ? 'border-amber-500 bg-amber-50/70 ring-1 ring-amber-500'
                        : 'border-slate-200 hover:border-slate-300'
                    }`}
                  >
                    <div className="flex items-start gap-3">
                      <input
                        type="radio"
                        name="role"
                        value="superadmin"
                        checked={formRole === 'superadmin'}
                        onChange={() => setFormRole('superadmin')}
                        className="mt-1 text-amber-600 focus:ring-amber-500"
                      />
                      <div>
                        <div className="flex items-center gap-1.5 font-bold text-sm text-amber-900">
                          <ShieldCheck className="w-4 h-4 text-amber-600" />
                          <span>Superadmin</span>
                        </div>
                        <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                          Akses penuh ke seluruh sistem tanpa batas, termasuk mengelola pengguna &amp; role, konfigurasi webhook spreadsheet, dan reset database.
                        </p>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="px-5 py-2 rounded-xl bg-[#3A7D8A] hover:bg-[#2d636e] text-white text-xs font-bold shadow-sm transition disabled:opacity-50"
                >
                  {isSubmitting ? 'Menyimpan...' : editingUser ? 'Simpan Perubahan' : 'Tambah Pengguna'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Delete Confirmation Modal */}
      {deleteTarget && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div
            onClick={() => setDeleteTarget(null)}
            className="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"
          />
          <div className="relative w-full max-w-sm bg-white rounded-3xl p-6 shadow-xl z-10 text-center animate-in fade-in zoom-in-95">
            <div className="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
              <Trash2 className="w-6 h-6" />
            </div>
            <h3 className="font-bold text-slate-900 text-base">Hapus Pengguna?</h3>
            <p className="text-xs text-slate-500 mt-1">
              Apakah Anda yakin ingin menghapus akun <strong>{deleteTarget.name}</strong> (@{deleteTarget.username})?
              Pengguna ini tidak akan bisa login lagi ke portal.
            </p>

            <div className="flex items-center justify-center gap-2 mt-5">
              <button
                onClick={() => setDeleteTarget(null)}
                className="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50"
              >
                Batal
              </button>
              <button
                onClick={handleDelete}
                disabled={isSubmitting}
                className="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm"
              >
                {isSubmitting ? 'Menghapus...' : 'Ya, Hapus'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
