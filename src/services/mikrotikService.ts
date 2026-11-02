import { MikrotikAccount, MikrotikTeam } from '../types';

export const MIKROTIK_STORAGE_KEY = 'syamanah_mikrotik_accounts';

export const MIKROTIK_TEAMS: {
  id: MikrotikTeam;
  label: string;
  codePrefix: string;
  profile: string;
  rateLimit: string;
}[] = [
  { id: 'jaket1', label: 'Tim Jaket 1', codePrefix: 'SJ01', profile: 'sales_jaket', rateLimit: '10M/10M' },
  { id: 'jaket2', label: 'Tim Jaket 2', codePrefix: 'SJ02', profile: 'sales_jaket', rateLimit: '10M/10M' },
  { id: 'jersey1', label: 'Tim Jersey 1', codePrefix: 'SR01', profile: 'sales_jersey', rateLimit: '10M/10M' },
  { id: 'jersey2', label: 'Tim Jersey 2', codePrefix: 'SR02', profile: 'sales_jersey', rateLimit: '10M/10M' },
  { id: 'eksekutif', label: 'Sales Eksekutif', codePrefix: 'SE01', profile: 'sales_eksekutif', rateLimit: '15M/15M' },
  { id: 'crm', label: 'Tim CRM', codePrefix: 'SC01', profile: 'sales_crm', rateLimit: '15M/15M' }
];

export const INITIAL_MIKROTIK_ACCOUNTS: MikrotikAccount[] = [
  {
    id: 'mkt_01',
    namaPemilik: 'Teh Devia',
    jabatan: 'Sales Officer',
    tim: 'jaket1',
    username: 'SJ01',
    password: '749201',
    profileHotspot: 'sales_jaket',
    rateLimit: '10M/10M',
    commandTerminal: '/ip hotspot user add name="SJ01" password="749201" profile="sales_jaket" limit-rate="10M/10M" comment="Teh Devia - Sales Officer (Tim Jaket 1)"',
    createdAt: '27/10/2026 09:00:00',
    status: 'aktif',
    catatan: 'Prioritas akses internet sales WhatsApp & Marketplace'
  },
  {
    id: 'mkt_02',
    namaPemilik: 'Teh Aas',
    jabatan: 'Sales Officer',
    tim: 'jaket2',
    username: 'SJ02',
    password: '839104',
    profileHotspot: 'sales_jaket',
    rateLimit: '10M/10M',
    commandTerminal: '/ip hotspot user add name="SJ02" password="839104" profile="sales_jaket" limit-rate="10M/10M" comment="Teh Aas - Sales Officer (Tim Jaket 2)"',
    createdAt: '27/10/2026 09:05:00',
    status: 'aktif',
    catatan: 'Prioritas akses internet sales WhatsApp'
  },
  {
    id: 'mkt_03',
    namaPemilik: 'Teh Wahdah',
    jabatan: 'Head of Jersey',
    tim: 'jersey1',
    username: 'SR01',
    password: '619283',
    profileHotspot: 'sales_jersey',
    rateLimit: '10M/10M',
    commandTerminal: '/ip hotspot user add name="SR01" password="619283" profile="sales_jersey" limit-rate="10M/10M" comment="Teh Wahdah - Head of Jersey (Tim Jersey 1)"',
    createdAt: '27/10/2026 09:10:00',
    status: 'aktif',
    catatan: 'Prioritas traffic CRM & Instagram Sales'
  },
  {
    id: 'mkt_04',
    namaPemilik: 'Fitri Handayani',
    jabatan: 'Sales Representative',
    tim: 'jersey2',
    username: 'SR02',
    password: '492018',
    profileHotspot: 'sales_jersey',
    rateLimit: '10M/10M',
    commandTerminal: '/ip hotspot user add name="SR02" password="492018" profile="sales_jersey" limit-rate="10M/10M" comment="Fitri Handayani - Sales Representative (Tim Jersey 2)"',
    createdAt: '27/10/2026 09:15:00',
    status: 'aktif',
    catatan: 'Akses hotspot operasional'
  },
  {
    id: 'mkt_05',
    namaPemilik: 'Hesti Puspitasari',
    jabatan: 'Head of Sales Eksekutif',
    tim: 'eksekutif',
    username: 'SE01',
    password: '918273',
    profileHotspot: 'sales_eksekutif',
    rateLimit: '15M/15M',
    commandTerminal: '/ip hotspot user add name="SE01" password="918273" profile="sales_eksekutif" limit-rate="15M/15M" comment="Hesti Puspitasari - Head of Sales Eksekutif (Sales Eksekutif)"',
    createdAt: '27/10/2026 09:20:00',
    status: 'aktif',
    catatan: 'Bandwidth prioritas eksekutif'
  },
  {
    id: 'mkt_06',
    namaPemilik: 'Wilda Nurul Fauziah',
    jabatan: 'Customer Relationship Officer',
    tim: 'crm',
    username: 'SC01',
    password: '528194',
    profileHotspot: 'sales_crm',
    rateLimit: '15M/15M',
    commandTerminal: '/ip hotspot user add name="SC01" password="528194" profile="sales_crm" limit-rate="15M/15M" comment="Wilda Nurul Fauziah - Customer Relationship Officer (Tim CRM)"',
    createdAt: '27/10/2026 09:25:00',
    status: 'aktif',
    catatan: 'Akses database CRM & WhatsApp Cloud'
  }
];

let cachedMikrotik: MikrotikAccount[] = [...INITIAL_MIKROTIK_ACCOUNTS];

try {
  const raw = localStorage.getItem(MIKROTIK_STORAGE_KEY);
  if (raw) {
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed) && parsed.length > 0) cachedMikrotik = parsed;
  }
} catch {
  // ignore
}

export function getMikrotikAccounts(): MikrotikAccount[] {
  return cachedMikrotik;
}

export function setCachedMikrotikAccounts(accounts: MikrotikAccount[]): void {
  cachedMikrotik = accounts;
  localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(accounts));
}

export async function fetchMikrotikAccounts(): Promise<MikrotikAccount[]> {
  try {
    const res = await fetch('/api/mikrotik');
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data)) {
        cachedMikrotik = data;
        localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(data));
        return data;
      }
    }
  } catch {
    // fallback
  }
  return cachedMikrotik;
}

export function saveMikrotikAccounts(accounts: MikrotikAccount[]): void {
  setCachedMikrotikAccounts(accounts);
}

export async function addMikrotikAccount(account: MikrotikAccount): Promise<{
  success: boolean;
  gasSynced: boolean;
  message: string;
  sheetName?: string;
}> {
  cachedMikrotik = [account, ...cachedMikrotik.filter((a) => a.id !== account.id)];
  localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(cachedMikrotik));

  try {
    const res = await fetch('/api/mikrotik', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(account)
    });
    if (res.ok) {
      const data = await res.json();
      return {
        success: true,
        gasSynced: Boolean(data.gasSynced),
        message: data.gasMessage || 'Berhasil disimpan ke Google Spreadsheet!',
        sheetName: data.sheetName || 'Akun MikroTik Hotspot'
      };
    }
  } catch (err) {
    console.error('Failed to save mikrotik account to server:', err);
  }

  return {
    success: true,
    gasSynced: false,
    message: 'Tersimpan di database lokal'
  };
}

export async function updateMikrotikAccount(account: MikrotikAccount): Promise<{
  success: boolean;
  message: string;
}> {
  cachedMikrotik = cachedMikrotik.map((a) => (a.id === account.id ? account : a));
  localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(cachedMikrotik));

  try {
    const res = await fetch(`/api/mikrotik/${encodeURIComponent(account.id)}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(account)
    });
    if (res.ok) {
      const data = await res.json();
      return { success: true, message: data.message || 'Akun MikroTik berhasil diperbarui di server & Spreadsheet!' };
    }
  } catch (err) {
    console.error('Failed to update mikrotik account on server:', err);
  }
  return { success: true, message: 'Akun diperbarui di cache lokal' };
}

export async function syncAllMikrotikToSpreadsheet(): Promise<{
  success: boolean;
  message: string;
  syncedCount?: number;
}> {
  try {
    const res = await fetch('/api/mikrotik/sync-all-to-sheet', {
      method: 'POST'
    });
    const data = await res.json();
    return data;
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Gagal menghubungi server';
    return { success: false, message: msg };
  }
}

export function deleteMikrotikAccount(id: string): void {
  cachedMikrotik = cachedMikrotik.filter((a) => a.id !== id);
  localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(cachedMikrotik));

  // Delete from server & spreadsheet
  fetch(`/api/mikrotik/${encodeURIComponent(id)}`, {
    method: 'DELETE'
  }).catch((err) => console.error('Failed to delete mikrotik account on server:', err));
}

export function resetMikrotikSeed(): void {
  cachedMikrotik = INITIAL_MIKROTIK_ACCOUNTS;
  localStorage.setItem(MIKROTIK_STORAGE_KEY, JSON.stringify(INITIAL_MIKROTIK_ACCOUNTS));

  fetch('/api/mikrotik/reset', {
    method: 'POST'
  }).catch((err) => console.error('Failed to reset mikrotik seed on server:', err));
}

/**
 * Generate kode username pendek otomatis (contoh: SJ01, SJ02, SR01, SE01, SC01)
 * Tidak panjang, menggunakan kode tim dan nomor urut 2 digit
 */
export function generateShortCodeUsername(tim: MikrotikTeam, existingAccounts: MikrotikAccount[] = []): string {
  const prefixMap: Record<MikrotikTeam, string> = {
    jaket1: 'SJ',
    jaket2: 'SJ',
    jersey1: 'SR',
    jersey2: 'SR',
    eksekutif: 'SE',
    crm: 'SC'
  };

  const prefix = prefixMap[tim] || 'SJ';

  // Hitung jumlah akun yang sudah ada dengan prefix yang sama untuk nomor urut
  const countInTeam = existingAccounts.filter((a) => a.tim === tim).length;
  let nextNum = countInTeam + 1;

  // Format 2 digit (01, 02, 03 ...)
  let candidate = `${prefix}${String(nextNum).padStart(2, '0')}`;

  // Pastikan unik jika sudah ada
  const existingUsernames = new Set(existingAccounts.map((a) => a.username.toUpperCase()));
  while (existingUsernames.has(candidate.toUpperCase())) {
    nextNum++;
    candidate = `${prefix}${String(nextNum).padStart(2, '0')}`;
  }

  return candidate;
}

/**
 * Generate password maksimal 6 digit (contoh: 749201, 6 digit angka aman & mudah diketik)
 */
export function generate6DigitPassword(): string {
  // 6 digit angka acak (100000 s/d 999999)
  const min = 100000;
  const max = 999999;
  const num = Math.floor(Math.random() * (max - min + 1)) + min;
  return num.toString().slice(0, 6);
}

/**
 * Generate RouterOS command siap pakai
 */
export function generateMikrotikCommand(
  username: string,
  pass: string,
  profile: string,
  rateLimit: string,
  namaPemilik: string,
  jabatan: string,
  timLabel: string
): string {
  const comment = `${namaPemilik} - ${jabatan} (${timLabel})`;
  return `/ip hotspot user add name="${username}" password="${pass}" profile="${profile}" limit-rate="${rateLimit}" comment="${comment}"`;
}
