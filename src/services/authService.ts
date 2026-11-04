import { UserAccount, UserRole, AuthSession } from '../types';

export const AUTH_STORAGE_KEY = 'syamanah_auth_user';

export const DEFAULT_USERS: UserAccount[] = [
  {
    id: 'user-superadmin-01',
    username: 'superadmin',
    name: 'Super Admin Syamanah',
    role: 'superadmin',
    createdAt: new Date().toISOString()
  },
  {
    id: 'user-manager-01',
    username: 'manager',
    name: 'Manager Operasional',
    role: 'manager',
    createdAt: new Date().toISOString()
  },
  {
    id: 'user-ga-01',
    username: 'ga',
    name: 'General Affair Staff',
    role: 'general_affair',
    createdAt: new Date().toISOString()
  }
];

export function getCurrentUser(): UserAccount | null {
  try {
    const raw = localStorage.getItem(AUTH_STORAGE_KEY);
    if (raw) {
      return JSON.parse(raw);
    }
  } catch (err) {
    console.error('Error reading current user from storage:', err);
  }
  // Default to Superadmin for initial convenience if not yet set
  return DEFAULT_USERS[0];
}

export function setCurrentUser(user: UserAccount | null): void {
  try {
    if (user) {
      localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(user));
    } else {
      localStorage.removeItem(AUTH_STORAGE_KEY);
    }
  } catch (err) {
    console.error('Error saving current user:', err);
  }
}

export function logout(): void {
  setCurrentUser(null);
}

export async function loginWithApi(
  username: string,
  password: string
): Promise<{ success: boolean; user?: UserAccount; message?: string }> {
  try {
    const res = await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });
    const data = await res.json();
    if (data.success && data.user) {
      setCurrentUser(data.user);
      return { success: true, user: data.user };
    }
    return { success: false, message: data.message || 'Login gagal' };
  } catch {
    // Fallback offline verification against default users if server unavailable
    const match = DEFAULT_USERS.find(
      (u) => u.username.toLowerCase() === username.trim().toLowerCase()
    );
    if (match && (password === 'password123' || password === 'admin123' || password === '1234')) {
      setCurrentUser(match);
      return { success: true, user: match };
    }
    return { success: false, message: 'Username atau password salah' };
  }
}

export async function fetchAllUsers(): Promise<UserAccount[]> {
  try {
    const res = await fetch('/api/users');
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data)) return data;
    }
  } catch (err) {
    console.warn('Failed to fetch users from server:', err);
  }
  return DEFAULT_USERS;
}

export async function createUser(payload: {
  username: string;
  name: string;
  role: UserRole;
  password?: string;
}): Promise<{ success: boolean; user?: UserAccount; message?: string }> {
  try {
    const res = await fetch('/api/users', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return await res.json();
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : String(err);
    return { success: false, message: msg };
  }
}

export async function updateUser(
  id: string,
  payload: { name?: string; role?: UserRole; password?: string }
): Promise<{ success: boolean; user?: UserAccount; message?: string }> {
  try {
    const res = await fetch(`/api/users/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return await res.json();
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : String(err);
    return { success: false, message: msg };
  }
}

export async function deleteUser(
  id: string
): Promise<{ success: boolean; message?: string }> {
  try {
    const res = await fetch(`/api/users/${id}`, {
      method: 'DELETE'
    });
    return await res.json();
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : String(err);
    return { success: false, message: msg };
  }
}

// Permission checking helpers
export function isSuperadmin(user: UserAccount | null): boolean {
  return user?.role === 'superadmin';
}

export function isManager(user: UserAccount | null): boolean {
  return user?.role === 'manager';
}

export function isGeneralAffair(user: UserAccount | null): boolean {
  return user?.role === 'general_affair';
}

/**
 * GA is strictly restricted: CANNOT see company accounts (Email, IG, FB, passwords, software credentials)
 */
export function canViewCompanyAccounts(user: UserAccount | null): boolean {
  if (!user) return false;
  return user.role === 'superadmin' || user.role === 'manager';
}

export function canManageUsers(user: UserAccount | null): boolean {
  return user?.role === 'superadmin';
}

export function canEditConfig(user: UserAccount | null): boolean {
  return user?.role === 'superadmin';
}

