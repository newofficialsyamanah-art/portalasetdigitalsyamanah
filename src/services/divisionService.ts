// Division & Team Management Service
export const DEFAULT_DIVISIONS: string[] = [
  'Sales Jaket',
  'Sales Jersey',
  'Sales Eksekutif',
  'Sales Seragam & Kaos Polo',
  'CRM (Customer Relationship Management)',
  'Marketing Communication & Ads',
  'Desain Grafis & Sublimasi',
  'Produksi & Quality Control',
  'Finance & Accounting',
  'General Affair & Fasilitas',
  'IT Infrastructure & Network',
  'HRD & Manajemen',
  'Procurement & Gudang'
];

export const DIVISION_STORAGE_KEY = 'syamanah_divisions';

let cachedDivisions: string[] = [...DEFAULT_DIVISIONS];

// Initialize from localStorage
try {
  const stored = localStorage.getItem(DIVISION_STORAGE_KEY);
  if (stored) {
    const parsed = JSON.parse(stored);
    if (Array.isArray(parsed) && parsed.length > 0) {
      cachedDivisions = parsed;
    }
  }
} catch {
  // ignore
}

export function getDivisions(): string[] {
  return cachedDivisions;
}

export function setCachedDivisions(list: string[]): void {
  cachedDivisions = list;
  try {
    localStorage.setItem(DIVISION_STORAGE_KEY, JSON.stringify(list));
  } catch {
    // ignore
  }
}

export async function fetchDivisions(): Promise<string[]> {
  try {
    const res = await fetch('/api/divisions');
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data) && data.length > 0) {
        setCachedDivisions(data);
        return data;
      }
    }
  } catch (err) {
    console.warn('Failed to fetch divisions from server, using cached:', err);
  }
  return cachedDivisions;
}

export async function addDivision(name: string): Promise<{
  success: boolean;
  divisions: string[];
  message: string;
}> {
  const clean = name.trim();
  if (!clean) {
    return { success: false, divisions: cachedDivisions, message: 'Nama divisi tidak boleh kosong' };
  }

  if (cachedDivisions.some((d) => d.toLowerCase() === clean.toLowerCase())) {
    return { success: false, divisions: cachedDivisions, message: `Divisi "${clean}" sudah terdaftar` };
  }

  const updated = [...cachedDivisions, clean];
  setCachedDivisions(updated);

  try {
    const res = await fetch('/api/divisions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: clean })
    });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data.divisions)) {
        setCachedDivisions(data.divisions);
        return { success: true, divisions: data.divisions, message: `Divisi "${clean}" berhasil ditambahkan` };
      }
    }
  } catch (err) {
    console.warn('Server offline, saved division locally:', err);
  }

  return { success: true, divisions: updated, message: `Divisi "${clean}" berhasil ditambahkan` };
}

export async function updateDivision(
  oldName: string,
  newName: string
): Promise<{ success: boolean; divisions: string[]; message: string }> {
  const cleanNew = newName.trim();
  if (!cleanNew) {
    return { success: false, divisions: cachedDivisions, message: 'Nama divisi baru tidak boleh kosong' };
  }

  const updated = cachedDivisions.map((d) => (d.toLowerCase() === oldName.toLowerCase() ? cleanNew : d));
  setCachedDivisions(updated);

  try {
    const res = await fetch('/api/divisions', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ oldName, newName: cleanNew })
    });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data.divisions)) {
        setCachedDivisions(data.divisions);
        return { success: true, divisions: data.divisions, message: `Divisi berhasil diubah menjadi "${cleanNew}"` };
      }
    }
  } catch (err) {
    console.warn('Server offline, updated division locally:', err);
  }

  return { success: true, divisions: updated, message: `Divisi berhasil diubah menjadi "${cleanNew}"` };
}

export async function deleteDivision(
  name: string
): Promise<{ success: boolean; divisions: string[]; message: string }> {
  const updated = cachedDivisions.filter((d) => d.toLowerCase() !== name.toLowerCase());
  setCachedDivisions(updated);

  try {
    const res = await fetch(`/api/divisions/${encodeURIComponent(name)}`, {
      method: 'DELETE'
    });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data.divisions)) {
        setCachedDivisions(data.divisions);
        return { success: true, divisions: data.divisions, message: `Divisi "${name}" berhasil dihapus` };
      }
    }
  } catch (err) {
    console.warn('Server offline, deleted division locally:', err);
  }

  return { success: true, divisions: updated, message: `Divisi "${name}" berhasil dihapus` };
}

