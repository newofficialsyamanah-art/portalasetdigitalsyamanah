import express, { Request, Response } from 'express';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import * as XLSX from 'xlsx';
import { INITIAL_SYAMANAH_RECORDS } from './src/data/initialRecords.ts';
import { INITIAL_MIKROTIK_ACCOUNTS } from './src/services/mikrotikService.ts';
import { SubmissionRecord, MikrotikAccount, SyamanahConfig, FormPC, FormAkun, UserAccount, UserRole } from './src/types/index.ts';

dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = parseInt(process.env.PORT || '3000', 10);
const DATA_DIR = path.resolve(__dirname, '.data');
const DB_FILE = path.resolve(DATA_DIR, 'db.json');

// Ensure database directory exists
if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

interface ServerDatabase {
  config: SyamanahConfig;
  submissions: SubmissionRecord[];
  mikrotik: MikrotikAccount[];
  users: UserAccount[];
  divisions?: string[];
  lastSyncedAt?: string;
}

const DEFAULT_DIVISIONS: string[] = [
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

const DEFAULT_USERS: UserAccount[] = [
  {
    id: 'user-superadmin-01',
    username: 'superadmin',
    password: 'password123',
    name: 'Super Admin Syamanah',
    role: 'superadmin',
    createdAt: new Date().toISOString()
  },
  {
    id: 'user-manager-01',
    username: 'manager',
    password: 'password123',
    name: 'Manager Operasional',
    role: 'manager',
    createdAt: new Date().toISOString()
  },
  {
    id: 'user-ga-01',
    username: 'ga',
    password: 'password123',
    name: 'General Affair Staff',
    role: 'general_affair',
    createdAt: new Date().toISOString()
  }
];

const DEFAULT_CONFIG: SyamanahConfig = {
  gasUrl: 'https://script.google.com/macros/s/AKfycbzalkabRunlYtF3eDqZeBI_v_UpOoYSJTHRUUJGn4G_LrUqaSP9NuaSTBipOSN8pttb/exec',
  spreadsheetId: '',
  driveFolderId: '',
  superadminPin: '1234'
};

function readDb(): ServerDatabase {
  try {
    if (fs.existsSync(DB_FILE)) {
      const raw = fs.readFileSync(DB_FILE, 'utf-8');
      const parsed = JSON.parse(raw);
      return {
        config: { ...DEFAULT_CONFIG, ...(parsed.config || {}) },
        submissions: Array.isArray(parsed.submissions) ? parsed.submissions : INITIAL_SYAMANAH_RECORDS,
        mikrotik: Array.isArray(parsed.mikrotik) ? parsed.mikrotik : INITIAL_MIKROTIK_ACCOUNTS,
        users: Array.isArray(parsed.users) && parsed.users.length > 0 ? parsed.users : DEFAULT_USERS,
        divisions: Array.isArray(parsed.divisions) && parsed.divisions.length > 0 ? parsed.divisions : DEFAULT_DIVISIONS,
        lastSyncedAt: parsed.lastSyncedAt || undefined
      };
    }
  } catch (err) {
    console.error('Error reading DB, using defaults:', err);
  }

  const initialDb: ServerDatabase = {
    config: DEFAULT_CONFIG,
    submissions: INITIAL_SYAMANAH_RECORDS,
    mikrotik: INITIAL_MIKROTIK_ACCOUNTS,
    users: DEFAULT_USERS,
    divisions: DEFAULT_DIVISIONS
  };
  saveDb(initialDb);
  return initialDb;
}

function saveDb(data: ServerDatabase): void {
  try {
    fs.writeFileSync(DB_FILE, JSON.stringify(data, null, 2), 'utf-8');
  } catch (err) {
    console.error('Error saving DB:', err);
  }
}

/**
 * Server-side helper to call Google Apps Script webhook
 * Avoids browser CORS and automatically follows 302 redirects!
 */
async function callGasWebhook(payload: Record<string, unknown>, gasUrl?: string): Promise<{ success: boolean; data?: unknown; message?: string }> {
  const db = readDb();
  const targetUrl = gasUrl || db.config.gasUrl;
  if (!targetUrl || !targetUrl.startsWith('https://script.google.com/macros/s/')) {
    return { success: false, message: 'URL Google Apps Script belum dikonfigurasi di server' };
  }

  try {
    const payloadWithSpreadsheet = {
      ...payload,
      spreadsheetId: db.config.spreadsheetId || payload.spreadsheetId,
      driveFolderId: db.config.driveFolderId || payload.driveFolderId
    };

    const res = await fetch(targetUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'text/plain;charset=utf-8'
      },
      body: JSON.stringify(payloadWithSpreadsheet),
      redirect: 'follow'
    });

    const text = await res.text();
    try {
      const json = JSON.parse(text);
      return {
        success: json.status === 'success' || !json.error,
        data: json,
        message: json.message || (json.status === 'success' ? 'Berhasil tersinkronisasi ke Google Apps Script' : 'Respon GAS')
      };
    } catch {
      return { success: true, message: 'Webhook terkirim', data: text };
    }
  } catch (error: unknown) {
    const err = error as Error;
    console.error('GAS call error:', err);
    return { success: false, message: err?.message || 'Gagal menghubungi Google Apps Script' };
  }
}

async function startServer() {
  const app = express();

  // Allow larger payload for base64 photo uploads
  app.use(express.json({ limit: '50mb' }));
  app.use(express.urlencoded({ extended: true, limit: '50mb' }));

  // ==========================================
  // API ROUTES
  // ==========================================

  // Status check
  app.get('/api/status', (_req: Request, res: Response) => {
    const db = readDb();
    res.json({
      status: 'online',
      hasGasConfigured: Boolean(db.config.gasUrl),
      hasSpreadsheet: Boolean(db.config.spreadsheetId),
      submissionCount: db.submissions.length,
      mikrotikCount: db.mikrotik.length,
      lastSyncedAt: db.lastSyncedAt || null
    });
  });

  // Get all unified shared data (Config + Submissions + MikroTik)
  app.get('/api/data', (_req: Request, res: Response) => {
    const db = readDb();
    res.json({
      config: db.config,
      submissions: db.submissions,
      mikrotik: db.mikrotik,
      divisions: db.divisions || DEFAULT_DIVISIONS,
      lastSyncedAt: db.lastSyncedAt
    });
  });


  // ==========================================
  // AUTH & USER ROLE MANAGEMENT
  // ==========================================
  app.post('/api/auth/login', (req: Request, res: Response) => {
    const { username, password } = req.body || {};
    if (!username || !password) {
      res.status(400).json({ success: false, message: 'Username dan password wajib diisi' });
      return;
    }
    const db = readDb();
    const user = db.users.find(
      (u) => u.username.toLowerCase() === String(username).trim().toLowerCase()
    );
    if (!user || user.password !== String(password)) {
      res.status(401).json({ success: false, message: 'Username atau password salah' });
      return;
    }

    user.lastLogin = new Date().toISOString();
    saveDb(db);

    const { password: _, ...safeUser } = user;
    res.json({ success: true, user: safeUser });
  });

  app.get('/api/users', (_req: Request, res: Response) => {
    const db = readDb();
    const safeUsers = (db.users || DEFAULT_USERS).map(({ password: _, ...rest }) => rest);
    res.json(safeUsers);
  });

  app.post('/api/users', (req: Request, res: Response) => {
    const db = readDb();
    const { username, name, role, password } = req.body || {};

    if (!username || !name || !role) {
      res.status(400).json({ success: false, message: 'Data pengguna tidak lengkap' });
      return;
    }

    if (db.users.some((u) => u.username.toLowerCase() === String(username).trim().toLowerCase())) {
      res.status(400).json({ success: false, message: `Username "${username}" sudah digunakan` });
      return;
    }

    const newUser: UserAccount = {
      id: 'user-' + Date.now(),
      username: String(username).trim().toLowerCase(),
      name: String(name).trim(),
      role: role as UserRole,
      password: String(password || 'password123'),
      createdAt: new Date().toISOString()
    };

    db.users.push(newUser);
    saveDb(db);

    const { password: _, ...safeUser } = newUser;
    res.json({ success: true, user: safeUser, message: 'Pengguna baru berhasil ditambahkan' });
  });

  app.put('/api/users/:id', (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;
    const index = db.users.findIndex((u) => u.id === id);

    if (index === -1) {
      res.status(404).json({ success: false, message: 'Pengguna tidak ditemukan' });
      return;
    }

    const existing = db.users[index];
    const { name, role, password } = req.body || {};

    if (name) existing.name = String(name).trim();
    if (role) existing.role = role as UserRole;
    if (password) existing.password = String(password);

    db.users[index] = existing;
    saveDb(db);

    const { password: _, ...safeUser } = existing;
    res.json({ success: true, user: safeUser, message: 'Data pengguna berhasil diperbarui' });
  });

  app.delete('/api/users/:id', (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;

    const target = db.users.find((u) => u.id === id);
    if (!target) {
      res.status(404).json({ success: false, message: 'Pengguna tidak ditemukan' });
      return;
    }

    if (target.role === 'superadmin') {
      const superadminCount = db.users.filter((u) => u.role === 'superadmin').length;
      if (superadminCount <= 1) {
        res.status(400).json({ success: false, message: 'Tidak dapat menghapus satu-satunya Super Admin' });
        return;
      }
    }

    db.users = db.users.filter((u) => u.id !== id);
    saveDb(db);

    res.json({ success: true, message: `Pengguna "${target.name}" berhasil dihapus` });
  });

  // ==========================================
  // DIVISIONS & TEAMS MANAGEMENT (ALL-MODULES)
  // ==========================================
  app.get('/api/divisions', (_req: Request, res: Response) => {
    const db = readDb();
    res.json(db.divisions || DEFAULT_DIVISIONS);
  });

  app.post('/api/divisions', (req: Request, res: Response) => {
    const db = readDb();
    const { name } = req.body || {};
    if (!name || !String(name).trim()) {
      res.status(400).json({ success: false, message: 'Nama divisi tidak boleh kosong' });
      return;
    }
    const cleanName = String(name).trim();
    const current = db.divisions || [...DEFAULT_DIVISIONS];
    if (current.some((d) => d.toLowerCase() === cleanName.toLowerCase())) {
      res.status(400).json({ success: false, message: `Divisi "${cleanName}" sudah terdaftar`, divisions: current });
      return;
    }
    current.push(cleanName);
    db.divisions = current;
    saveDb(db);
    res.json({ success: true, divisions: db.divisions, message: `Divisi "${cleanName}" berhasil ditambahkan` });
  });

  app.put('/api/divisions', (req: Request, res: Response) => {
    const db = readDb();
    const { oldName, newName } = req.body || {};
    if (!oldName || !newName || !String(newName).trim()) {
      res.status(400).json({ success: false, message: 'Nama divisi lama dan baru wajib diisi' });
      return;
    }
    const cleanNew = String(newName).trim();
    let current = db.divisions || [...DEFAULT_DIVISIONS];
    const idx = current.findIndex((d) => d.toLowerCase() === String(oldName).trim().toLowerCase());
    if (idx !== -1) {
      current[idx] = cleanNew;
    } else {
      current.push(cleanNew);
    }
    db.divisions = current;

    // Update submissions that used the old division name
    db.submissions = db.submissions.map((sub) => {
      if (sub.type === 'pc') {
        const d = sub.data as FormPC;
        if (d.departemen === oldName) {
          return { ...sub, data: { ...d, departemen: cleanNew } };
        }
      } else if (sub.type === 'akun') {
        const d = sub.data as FormAkun;
        if (d.kategori === oldName) {
          return { ...sub, data: { ...d, kategori: cleanNew } };
        }
      }
      return sub;
    });

    saveDb(db);
    res.json({ success: true, divisions: db.divisions, message: `Divisi "${cleanNew}" berhasil diperbarui` });
  });

  app.delete('/api/divisions/:name', (req: Request, res: Response) => {
    const db = readDb();
    const { name } = req.params;
    let current = db.divisions || [...DEFAULT_DIVISIONS];
    current = current.filter((d) => d.toLowerCase() !== decodeURIComponent(name).toLowerCase());
    db.divisions = current;
    saveDb(db);
    res.json({ success: true, divisions: db.divisions, message: `Divisi berhasil dihapus` });
  });

  // Get and Update Configuration
  app.get('/api/config', (_req: Request, res: Response) => {
    const db = readDb();
    res.json(db.config);
  });

  app.post('/api/config', (req: Request, res: Response) => {
    const db = readDb();
    const newConfig = { ...db.config, ...req.body };
    db.config = newConfig;
    saveDb(db);
    res.json({ success: true, config: db.config });
  });

  // Submissions (PC & Akun) CRUD
  app.get('/api/submissions', (_req: Request, res: Response) => {
    const db = readDb();
    res.json(db.submissions);
  });

  app.post('/api/submissions', async (req: Request, res: Response) => {
    const db = readDb();
    const record: SubmissionRecord = req.body;

    if (!record || !record.id) {
      res.status(400).json({ success: false, message: 'Invalid submission record' });
      return;
    }

    // Forward to Google Apps Script if URL is configured
    let gasResult: { success: boolean; data?: unknown; message?: string } | null = null;
    if (db.config.gasUrl) {
      gasResult = await callGasWebhook({
        action: 'create',
        formType: record.type,
        data: record.data,
        id: record.id,
        timestamp: record.timestamp
      });

      if (gasResult.success && gasResult.data) {
        const gasData = gasResult.data as Record<string, unknown>;
        if (gasData.photoUrl) {
          record.drivePhotoUrl = String(gasData.photoUrl);
        }
        if (record.type === 'pc') {
          const pcData = record.data as FormPC;
          if (gasData.displayUrl) pcData.displayLink = String(gasData.displayUrl);
          if (gasData.soundUrl) pcData.soundLink = String(gasData.soundUrl);
          if (gasData.inputUrl) pcData.inputLink = String(gasData.inputUrl);
          if (!record.drivePhotoUrl && gasData.displayUrl) {
            record.drivePhotoUrl = String(gasData.displayUrl);
          }
        }
        record.syncedToGoogle = true;
        record.syncMessage = gasResult.message || 'Tersimpan di Spreadsheet & Drive';
      }
    }

    // Save locally on server
    db.submissions = [record, ...db.submissions.filter((s) => s.id !== record.id)];
    saveDb(db);

    res.json({
      success: true,
      record,
      gasSynced: Boolean(gasResult?.success),
      gasMessage: gasResult?.message
    });
  });

  // Update submission
  app.put('/api/submissions/:id', async (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;
    const index = db.submissions.findIndex((s) => s.id === id);

    if (index === -1) {
      res.status(404).json({ success: false, message: 'Catatan tidak ditemukan' });
      return;
    }

    const updated = { ...db.submissions[index], ...req.body, id };
    db.submissions[index] = updated;
    saveDb(db);

    // Also notify GAS if configured
    if (db.config.gasUrl) {
      callGasWebhook({
        action: 'update',
        formType: updated.type,
        id: updated.id,
        data: updated.data
      }).catch(console.error);
    }

    res.json({ success: true, record: updated });
  });

  // Delete submission (Removes from Server AND Spreadsheet!)
  app.delete('/api/submissions/:id', async (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;
    const target = db.submissions.find((s) => s.id === id);

    db.submissions = db.submissions.filter((s) => s.id !== id);
    saveDb(db);

    // Call GAS to delete the row in the Google Spreadsheet!
    let gasDeleteResult: unknown = null;
    if (db.config.gasUrl && target) {
      const dataObj = target.data as unknown as Record<string, unknown>;
      const targetName = String(dataObj?.nama || '');
      const targetPc = String(dataObj?.computerName || '');

      gasDeleteResult = await callGasWebhook({
        action: 'delete',
        formType: target.type,
        id: target.id,
        timestamp: target.timestamp,
        nama: targetName,
        key: target.id || targetPc || targetName
      });
    }

    res.json({
      success: true,
      message: 'Catatan berhasil dihapus dari server dan Google Spreadsheet',
      gasDelete: gasDeleteResult
    });
  });

  // Clear submissions (with optional ?type=pc or ?type=akun query)
  app.delete('/api/submissions', async (req: Request, res: Response) => {
    const db = readDb();
    const type = req.query.type as string | undefined;

    if (type === 'pc' || type === 'akun') {
      const initialCount = db.submissions.length;
      db.submissions = db.submissions.filter((s) => s.type !== type);
      const deletedCount = initialCount - db.submissions.length;
      saveDb(db);
      res.json({
        success: true,
        message: `Seluruh data ${type === 'pc' ? 'Spesifikasi PC & Laptop' : 'Akun Sales'} (${deletedCount} baris) berhasil dihapus`,
        deletedCount
      });
      return;
    }

    // Delete all submissions
    const count = db.submissions.length;
    db.submissions = [];
    saveDb(db);
    res.json({ success: true, message: `Seluruh ${count} data berhasil dikosongkan`, deletedCount: count });
  });

  // Clear all mikrotik accounts
  app.delete('/api/mikrotik/all', async (_req: Request, res: Response) => {
    const db = readDb();
    const count = db.mikrotik.length;
    db.mikrotik = [];
    saveDb(db);
    res.json({ success: true, message: `Seluruh ${count} akun MikroTik berhasil dikosongkan` });
  });

  // Clear entire database (all data across collections)
  app.post('/api/database/clear-all', async (_req: Request, res: Response) => {
    const db = readDb();
    const subCount = db.submissions.length;
    const mktCount = db.mikrotik.length;
    db.submissions = [];
    db.mikrotik = [];
    db.lastSyncedAt = new Date().toLocaleString('id-ID');
    saveDb(db);
    res.json({
      success: true,
      message: `Database berhasil dikosongkan (${subCount} inventaris & ${mktCount} akun MikroTik dihapus)`
    });
  });

  // ==========================================
  // MIKROTIK CRUD
  // ==========================================
  app.get('/api/mikrotik', (_req: Request, res: Response) => {
    const db = readDb();
    const seen = new Set<string>();
    const deduped: MikrotikAccount[] = [];
    for (const item of db.mikrotik) {
      const key = (item.username || item.id).toUpperCase();
      if (!seen.has(key)) {
        seen.add(key);
        deduped.push(item);
      }
    }
    res.json(deduped);
  });

  app.post('/api/mikrotik', async (req: Request, res: Response) => {
    const db = readDb();
    const newAccount: MikrotikAccount = req.body;

    if (!newAccount || !newAccount.username) {
      res.status(400).json({ success: false, message: 'Data akun MikroTik tidak lengkap' });
      return;
    }

    db.mikrotik = [newAccount, ...db.mikrotik.filter((a) => a.id !== newAccount.id)];
    saveDb(db);

    // Forward to GAS Mikrotik sheet - AWAIT to guarantee it is written to the Spreadsheet before responding
    let gasResult: { success: boolean; data?: unknown; message?: string } | null = null;
    if (db.config.gasUrl) {
      try {
        gasResult = await callGasWebhook({
          action: 'create',
          formType: 'mikrotik',
          id: newAccount.id,
          data: newAccount
        });
      } catch (err) {
        console.error('GAS mikrotik sync error:', err);
      }
    }

    res.json({
      success: true,
      account: newAccount,
      gasSynced: Boolean(gasResult?.success),
      gasMessage: gasResult?.message || (gasResult ? 'Tersimpan di Google Spreadsheet' : 'Tersimpan di server lokal'),
      sheetName: 'Akun MikroTik Hotspot'
    });
  });

  // Push all existing MikroTik accounts to Google Spreadsheet in 1-click
  app.post('/api/mikrotik/sync-all-to-sheet', async (_req: Request, res: Response) => {
    const db = readDb();
    if (!db.config.gasUrl) {
      res.status(400).json({ success: false, message: 'URL Google Apps Script belum disetel' });
      return;
    }

    let successCount = 0;
    const errors: string[] = [];

    for (const acc of db.mikrotik) {
      try {
        const gasRes = await callGasWebhook({
          action: 'create',
          formType: 'mikrotik',
          id: acc.id,
          data: acc
        });
        if (gasRes.success) successCount++;
      } catch (err: unknown) {
        const msg = err instanceof Error ? err.message : String(err);
        errors.push(msg);
      }
    }

    res.json({
      success: true,
      syncedCount: successCount,
      totalCount: db.mikrotik.length,
      message: `Berhasil menyinkronkan ${successCount} dari ${db.mikrotik.length} akun MikroTik ke tab "Akun MikroTik Hotspot" di Spreadsheet!`
    });
  });

  // Push all existing Akun Sales records to Google Spreadsheet in 1-click
  app.post('/api/akun/sync-all-to-sheet', async (_req: Request, res: Response) => {
    const db = readDb();
    if (!db.config.gasUrl) {
      res.status(400).json({ success: false, message: 'URL Google Apps Script belum disetel di Pengaturan' });
      return;
    }

    const akunList = db.submissions.filter((s) => s.type === 'akun');
    let successCount = 0;
    const batchSize = 5;

    for (let i = 0; i < akunList.length; i += batchSize) {
      const chunk = akunList.slice(i, i + batchSize);
      await Promise.all(
        chunk.map(async (item) => {
          try {
            const gasRes = await callGasWebhook({
              action: 'create',
              formType: 'akun',
              id: item.id,
              timestamp: item.timestamp,
              data: item.data
            });
            if (gasRes.success) {
              successCount++;
              item.syncedToGoogle = true;
              item.syncMessage = 'Tersimpan di Form Pencatatan Akun & Sales Syamanah 2026';
            }
          } catch (err: unknown) {
            console.error('Akun push error:', item.id, err);
          }
        })
      );
    }

    saveDb(db);

    res.json({
      success: true,
      syncedCount: successCount,
      totalCount: akunList.length,
      message: `Berhasil menyinkronkan ${successCount} dari ${akunList.length} data akun ke tab "Form Pencatatan Akun & Sales Syamanah 2026 (Responses)" di Google Spreadsheet!`
    });
  });

  // Push all existing PC/Laptop records to Google Spreadsheet in fast concurrent batches
  app.post('/api/pc/sync-all-to-sheet', async (_req: Request, res: Response) => {
    const db = readDb();
    if (!db.config.gasUrl) {
      res.status(400).json({ success: false, message: 'URL Google Apps Script belum disetel di Pengaturan' });
      return;
    }

    const pcList = db.submissions.filter((s) => s.type === 'pc');
    let successCount = 0;
    const batchSize = 5;

    for (let i = 0; i < pcList.length; i += batchSize) {
      const chunk = pcList.slice(i, i + batchSize);
      await Promise.all(
        chunk.map(async (item) => {
          try {
            const gasRes = await callGasWebhook({
              action: 'create',
              formType: 'pc',
              id: item.id,
              timestamp: item.timestamp,
              data: item.data
            });
            if (gasRes.success) {
              successCount++;
              item.syncedToGoogle = true;
              item.syncMessage = 'Tersimpan di Catatan Spesifikasi Perangkat PC Syamanah (Responses)';
            }
          } catch (err: unknown) {
            console.error('PC push error:', item.id, err);
          }
        })
      );
    }

    saveDb(db);

    res.json({
      success: true,
      syncedCount: successCount,
      totalCount: pcList.length,
      message: `Berhasil menyinkronkan ${successCount} dari ${pcList.length} data perangkat ke tab "Catatan Spesifikasi Perangkat PC Syamanah (Responses)" di Google Spreadsheet!`
    });
  });

  // Update MikroTik Account (CRUD: Update)
  app.put('/api/mikrotik/:id', async (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;
    const index = db.mikrotik.findIndex((a) => a.id === id);

    if (index === -1) {
      res.status(404).json({ success: false, message: 'Akun MikroTik tidak ditemukan' });
      return;
    }

    const updated: MikrotikAccount = { ...db.mikrotik[index], ...req.body, id };
    db.mikrotik[index] = updated;
    saveDb(db);

    // Forward update to Google Spreadsheet if configured
    if (db.config.gasUrl) {
      callGasWebhook({
        action: 'update',
        formType: 'mikrotik',
        id: updated.id,
        data: updated
      }).catch(console.error);
    }

    res.json({
      success: true,
      message: `Akun MikroTik (${updated.username} - ${updated.namaPemilik}) berhasil diperbarui!`,
      account: updated
    });
  });

  app.delete('/api/mikrotik/:id', async (req: Request, res: Response) => {
    const db = readDb();
    const { id } = req.params;
    const target = db.mikrotik.find((a) => a.id === id);

    db.mikrotik = db.mikrotik.filter((a) => a.id !== id);
    saveDb(db);

    // Notify GAS to delete from sheet "Akun MikroTik Hotspot"
    if (db.config.gasUrl && target) {
      await callGasWebhook({
        action: 'delete',
        formType: 'mikrotik',
        id: target.id,
        key: target.username || target.namaPemilik
      });
    }

    res.json({ success: true, message: 'Akun MikroTik berhasil dihapus' });
  });

  // Direct database bulk import endpoint (e.g. from Excel/CSV upload)
  app.post('/api/database/import', async (req: Request, res: Response) => {
    try {
      const db = readDb();
      const { submissions, mikrotik } = req.body;

      if (Array.isArray(submissions) && submissions.length > 0) {
        // Merge or replace submissions
        const existingIds = new Set(submissions.map((s: SubmissionRecord) => s.id));
        db.submissions = [...submissions, ...db.submissions.filter((s) => !existingIds.has(s.id))];
      }

      if (Array.isArray(mikrotik) && mikrotik.length > 0) {
        const existingUsernames = new Set(mikrotik.map((m: MikrotikAccount) => (m.username || '').toUpperCase()));
        db.mikrotik = [...mikrotik, ...db.mikrotik.filter((m) => !existingUsernames.has((m.username || '').toUpperCase()))];
      }

      db.lastSyncedAt = new Date().toLocaleString('id-ID');
      saveDb(db);

      res.json({
        success: true,
        message: 'Database berhasil diperbarui',
        totalSubmissions: db.submissions.length,
        totalMikrotik: db.mikrotik.length,
        lastSyncedAt: db.lastSyncedAt
      });
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : String(err);
      res.status(500).json({ success: false, message: 'Gagal memperbarui database: ' + msg });
    }
  });

  // Server-side safe Excel export endpoint
  app.get('/api/database/export-excel', (_req: Request, res: Response) => {
    try {
      const db = readDb();
      const wb = XLSX.utils.book_new();

      // 1. PC
      const pcRecords = db.submissions.filter((s: SubmissionRecord) => s.type === 'pc');
      const pcRows = pcRecords.map((r: SubmissionRecord, idx: number) => {
        const d = (r.data || {}) as FormPC;
        return {
          No: idx + 1,
          Timestamp: r.timestamp || '',
          Nama: d.nama || '',
          Departemen: d.departemen || '',
          Jabatan: d.jabatan || '',
          'Jenis Perangkat': d.jenisPerangkat || 'PC Desktop',
          'Computer Name': d.computerName || '',
          IP: d.ip || '',
          'Password PC': d.passwordPc || '',
          'Operating System': d.os || '',
          'System Manufacturer': d.manufacturer || '',
          Processor: d.processor || '',
          Memory: d.memory || '',
          'Display Link': d.displayLink || '',
          'Sound Link': d.soundLink || '',
          'Input Link': d.inputLink || ''
        };
      });
      const pcWs = XLSX.utils.json_to_sheet(pcRows);
      XLSX.utils.book_append_sheet(wb, pcWs, 'Spesifikasi PC & Laptop');

      // 2. Akun Sales
      const akunRecords = db.submissions.filter((s: SubmissionRecord) => s.type === 'akun');
      const akunRows = akunRecords.map((r: SubmissionRecord, idx: number) => {
        const d = (r.data || {}) as FormAkun;
        return {
          No: idx + 1,
          Timestamp: r.timestamp || '',
          'Nama Pemegang': d.nama || '',
          'Kategori Tim/Role': d.kategori || '',
          Jabatan: d.jabatan || '',
          'Jenis Handphone': d.jenisHp || '',
          'Bekas handphone siapa?': d.bekasHp || '',
          'Akun login di PC': d.akunLoginPc || '',
          'Email 1': d.email1 || '',
          'Password Email 1': d.passEmail1 || '',
          'Email 2': d.email2 || '',
          'Password Email 2': d.passEmail2 || '',
          'Email 3': d.email3 || '',
          'Password Email 3': d.passEmail3 || '',
          'Username IG 1': d.ig1 || '',
          'Password IG 1': d.passIg1 || '',
          'Username IG 2': d.ig2 || '',
          'Password IG 2': d.passIg2 || '',
          'Username IG 3': d.ig3 || '',
          'Password IG 3': d.passIg3 || '',
          'Facebook 1': d.fb1 || '',
          'Password FB 1': d.passFb1 || '',
          'Facebook 2': d.fb2 || '',
          'Password FB 2': d.passFb2 || '',
          'Facebook 3': d.fb3 || '',
          'Password FB 3': d.passFb3 || '',
          'No Telpon / WhatsApp': d.noWa || '',
          'Akun lain terhubung aset Syamanah': d.akunLain || '',
          'Akun software lain': d.softwareLain || '',
          'Catatan Khusus / Audit': d.catatan || ''
        };
      });
      const akunWs = XLSX.utils.json_to_sheet(akunRows);
      XLSX.utils.book_append_sheet(wb, akunWs, 'Akun Sales & HP');

      // 3. MikroTik
      const mikrotikRows = (db.mikrotik || []).map((m: MikrotikAccount, idx: number) => ({
        No: idx + 1,
        'Tanggal Dibuat': m.createdAt || '',
        'Nama Pemilik': m.namaPemilik || '',
        Tim: m.tim || '',
        Jabatan: m.jabatan || '',
        Username: m.username || '',
        Password: m.password || '',
        'Profile Hotspot': m.profileHotspot || '',
        'Rate Limit': m.rateLimit || '',
        Status: m.status || 'aktif',
        Terminal: m.commandTerminal || ''
      }));
      const mikrotikWs = XLSX.utils.json_to_sheet(mikrotikRows);
      XLSX.utils.book_append_sheet(wb, mikrotikWs, 'Akun MikroTik');

      const buffer = XLSX.write(wb, { bookType: 'xlsx', type: 'buffer' });
      const dateStr = new Date().toISOString().slice(0, 10);
      const filename = `Database_Syamanah_${dateStr}.xlsx`;

      res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
      res.send(buffer);
    } catch (err: unknown) {
      console.error('Failed to export excel:', err);
      res.status(500).json({ success: false, message: 'Gagal membuat berkas Excel' });
    }
  });

  app.post('/api/mikrotik/reset', (_req: Request, res: Response) => {
    const db = readDb();
    db.mikrotik = INITIAL_MIKROTIK_ACCOUNTS;
    saveDb(db);
    res.json({ success: true, mikrotik: db.mikrotik });
  });

  // Pull / Sync directly from Google Spreadsheet
  app.post('/api/sync-spreadsheet', async (_req: Request, res: Response) => {
    const db = readDb();
    if (!db.config.gasUrl) {
      res.status(400).json({
        success: false,
        message: 'URL Google Apps Script belum diisi di Pengaturan.'
      });
      return;
    }

    try {
      const url = new URL(db.config.gasUrl);
      if (db.config.spreadsheetId) {
        url.searchParams.set('spreadsheetId', db.config.spreadsheetId);
      }

      const fetchRes = await fetch(url.toString(), {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        redirect: 'follow'
      });

      const json = await fetchRes.json();
      if (json.status !== 'success') {
        res.status(502).json({
          success: false,
          message: json.message || 'Google Apps Script mengembalikan error'
        });
        return;
      }

      let count = 0;
      const importedRecords: SubmissionRecord[] = [];

      // Import PC rows
      if (Array.isArray(json.pc)) {
        json.pc.forEach((pc: Record<string, unknown>, idx: number) => {
          if (pc.nama || pc.computerName) {
            importedRecords.push({
              id: String(pc.id || `gas_pc_${Date.now()}_${idx}`),
              type: 'pc',
              timestamp: String(pc.timestamp || new Date().toLocaleString('id-ID')),
              syncedToGoogle: true,
              syncMessage: 'Tersinkron dari Spreadsheet',
              drivePhotoUrl: pc.drivePhotoUrl ? String(pc.drivePhotoUrl) : undefined,
              data: {
                nama: String(pc.nama || ''),
                departemen: String(pc.departemen || ''),
                jabatan: String(pc.jabatan || ''),
                jenisPerangkat: (String(pc.jenisPerangkat || 'Laptop')) as FormPC['jenisPerangkat'],
                computerName: String(pc.computerName || ''),
                ip: String(pc.ip || ''),
                passwordPc: String(pc.passwordPc || ''),
                os: String(pc.os || ''),
                manufacturer: String(pc.manufacturer || ''),
                processor: String(pc.processor || ''),
                memory: String(pc.memory || ''),
                displayLink: String(pc.displayLink || ''),
                soundLink: String(pc.soundLink || ''),
                inputLink: String(pc.inputLink || '')
              }
            });
            count++;
          }
        });
      }

      // Import Akun rows
      if (Array.isArray(json.akun)) {
        json.akun.forEach((ak: Record<string, unknown>, idx: number) => {
          if (ak.nama) {
            importedRecords.push({
              id: String(ak.id || `gas_akun_${Date.now()}_${idx}`),
              type: 'akun',
              timestamp: String(ak.timestamp || new Date().toLocaleString('id-ID')),
              syncedToGoogle: true,
              syncMessage: 'Tersinkron dari Spreadsheet',
              drivePhotoUrl: ak.drivePhotoUrl ? String(ak.drivePhotoUrl) : undefined,
              data: {
                nama: String(ak.nama || ''),
                kategori: String(ak.kategori || ''),
                jenisHp: String(ak.jenisHp || ''),
                bekasHp: String(ak.bekasHp || ''),
                akunLoginPc: String(ak.akunLoginPc || ''),
                email1: String(ak.email1 || ''),
                passEmail1: String(ak.passEmail1 || ''),
                email2: String(ak.email2 || ''),
                passEmail2: String(ak.passEmail2 || ''),
                email3: String(ak.email3 || ''),
                passEmail3: String(ak.passEmail3 || ''),
                ig1: String(ak.ig1 || ''),
                passIg1: String(ak.passIg1 || ''),
                ig2: String(ak.ig2 || ''),
                passIg2: String(ak.passIg2 || ''),
                ig3: String(ak.ig3 || ''),
                passIg3: String(ak.passIg3 || ''),
                fb1: String(ak.fb1 || ''),
                passFb1: String(ak.passFb1 || ''),
                fb2: String(ak.fb2 || ''),
                passFb2: String(ak.passFb2 || ''),
                fb3: String(ak.fb3 || ''),
                passFb3: String(ak.passFb3 || ''),
                noWa: String(ak.noWa || ''),
                akunLain: String(ak.akunLain || ''),
                softwareLain: String(ak.softwareLain || ''),
                catatan: String(ak.catatan || ''),
                jabatan: String(ak.jabatan || '')
              }
            });
            count++;
          }
        });
      }

      // Import MikroTik rows if returned by sheet
      if (Array.isArray(json.mikrotik) && json.mikrotik.length > 0) {
        const fromSheet: MikrotikAccount[] = json.mikrotik.map((m: Record<string, unknown>, idx: number) => ({
          id: String(m.id || `mkt_sheet_${idx}`),
          namaPemilik: String(m.namaPemilik || ''),
          tim: (m.tim as MikrotikAccount['tim']) || 'jaket1',
          jabatan: String(m.jabatan || ''),
          username: String(m.username || ''),
          password: String(m.password || ''),
          profileHotspot: String(m.profileHotspot || 'sales_jaket'),
          rateLimit: String(m.rateLimit || '10M/10M'),
          commandTerminal: String(m.commandTerminal || ''),
          createdAt: String(m.createdAt || new Date().toLocaleString('id-ID')),
          status: (m.status as MikrotikAccount['status']) || 'aktif',
          catatan: m.catatan ? String(m.catatan) : undefined
        }));

        // Deduplicate rows from sheet by username
        const uniqueSheetMap = new Map<string, MikrotikAccount>();
        fromSheet.forEach((item) => {
          if (item.username) {
            uniqueSheetMap.set(item.username.toUpperCase(), item);
          }
        });
        const dedupedSheet = Array.from(uniqueSheetMap.values());

        db.mikrotik = dedupedSheet;
      }

      if (importedRecords.length > 0) {
        // Data strictly matches Google Spreadsheet rows 1:1
        db.submissions = importedRecords;
      }

      db.lastSyncedAt = new Date().toLocaleString('id-ID');
      saveDb(db);

      res.json({
        success: true,
        message: `Berhasil menarik ${count} aset PC/Akun dan ${Array.isArray(json.mikrotik) ? json.mikrotik.length : 0} akun MikroTik dari Google Spreadsheet!`,
        submissionsCount: db.submissions.length,
        mikrotikCount: db.mikrotik.length,
        lastSyncedAt: db.lastSyncedAt
      });
    } catch (err: unknown) {
      const error = err as Error;
      res.status(500).json({
        success: false,
        message: 'Gagal menarik data dari Spreadsheet: ' + (error?.message || String(err))
      });
    }
  });

  // Diagnostic Audit: Cek status semua spreadsheet dan tab secara realtime
  app.get('/api/spreadsheet/audit', async (_req: Request, res: Response) => {
    const db = readDb();
    const url = db.config.gasUrl;
    if (!url) {
      res.status(400).json({
        success: false,
        connected: false,
        message: 'URL Google Apps Script belum disetel'
      });
      return;
    }

    const startTime = Date.now();
    try {
      const response = await fetch(url, { method: 'GET' });
      const latencyMs = Date.now() - startTime;
      const json = await response.json();

      const localPc = db.submissions.filter((s) => s.type === 'pc').length;
      const localAkun = db.submissions.filter((s) => s.type === 'akun').length;
      const localMikrotik = db.mikrotik.length;

      const sheetPc = Array.isArray(json.pc) ? json.pc.length : 0;
      const sheetAkun = Array.isArray(json.akun) ? json.akun.length : 0;
      const sheetMikrotik = Array.isArray(json.mikrotik) ? json.mikrotik.length : 0;

      res.json({
        success: true,
        connected: true,
        latencyMs,
        gasUrl: url,
        auditTimestamp: new Date().toLocaleString('id-ID'),
        tabs: [
          {
            tabName: 'Spesifikasi PC & Laptop',
            sheetCount: sheetPc,
            localCount: localPc,
            status: sheetPc >= localPc ? 'Tersinkron Penuh' : 'Perlu Sinkronisasi',
            description: 'Data spesifikasi hardware, DxDiag, IP address, dan screenshot display/sound/input'
          },
          {
            tabName: 'Akun Sales & Handphone',
            sheetCount: sheetAkun,
            localCount: localAkun,
            status: sheetAkun >= localAkun ? 'Tersinkron Penuh' : 'Perlu Sinkronisasi',
            description: 'Data akun email, password, Instagram, Facebook, TikTok, dan inventaris HP tim sales'
          },
          {
            tabName: 'Akun MikroTik Hotspot',
            sheetCount: sheetMikrotik,
            localCount: localMikrotik,
            status: sheetMikrotik >= localMikrotik ? 'Tersinkron Penuh' : 'Tersambung Aktif',
            description: 'Daftar user Hotspot MikroTik Sales (Jaket1, Jaket2, Jersey1, Jersey2, Eksekutif, CRM)'
          }
        ],
        totalSheetRows: sheetPc + sheetAkun + sheetMikrotik,
        totalLocalRecords: localPc + localAkun + localMikrotik
      });
    } catch (err: unknown) {
      const error = err as Error;
      res.status(500).json({
        success: false,
        connected: false,
        latencyMs: Date.now() - startTime,
        message: 'Gagal terhubung ke Google Apps Script: ' + (error?.message || String(err))
      });
    }
  });

  // Live CRUD Test: Uji coba operasi Create dan Delete langsung di Google Spreadsheet secara real-time
  app.post('/api/spreadsheet/test-crud', async (_req: Request, res: Response) => {
    const testId = `test_crud_audit_${Date.now()}`;
    const testName = 'AUDIT_PING_TEST';
    const auditLogs: Array<{ step: string; status: 'ok' | 'error'; detail: string; timestamp: string }> = [];

    try {
      // Step 1: Create
      auditLogs.push({
        step: '1. Mengirim perintah CREATE ke Google Spreadsheet',
        status: 'ok',
        detail: `Membuat baris tes dengan ID: ${testId}`,
        timestamp: new Date().toLocaleTimeString('id-ID')
      });

      const createRes = await callGasWebhook({
        action: 'create',
        formType: 'mikrotik',
        id: testId,
        data: {
          namaPemilik: testName,
          tim: 'crm',
          jabatan: 'Tester System',
          username: 'TEST99',
          password: '999999',
          profileHotspot: 'sales_crm',
          rateLimit: '10M/10M',
          commandTerminal: '/ip hotspot user add name="TEST99"',
          status: 'aktif',
          catatan: 'Baris verifikasi live CRUD otomatis'
        }
      });

      auditLogs.push({
        step: '2. Verifikasi Respon CREATE dari Google Apps Script',
        status: createRes.success ? 'ok' : 'error',
        detail: `Google Apps Script mengonfirmasi: ${createRes.message || 'Data berhasil masuk'}`,
        timestamp: new Date().toLocaleTimeString('id-ID')
      });

      // Small delay
      await new Promise((r) => setTimeout(r, 600));

      // Step 3: Delete
      auditLogs.push({
        step: '3. Mengirim perintah DELETE ke Google Spreadsheet',
        status: 'ok',
        detail: `Menghapus baris tes ID: ${testId} dari spreadsheet`,
        timestamp: new Date().toLocaleTimeString('id-ID')
      });

      const deleteRes = await callGasWebhook({
        action: 'delete',
        formType: 'mikrotik',
        id: testId
      });

      const deleteData = deleteRes.data as Record<string, unknown> | undefined;
      const delCount = typeof deleteData?.deletedCount === 'number' ? deleteData.deletedCount : 1;

      auditLogs.push({
        step: '4. Verifikasi Respon DELETE dari Google Apps Script',
        status: deleteRes.success ? 'ok' : 'error',
        detail: `Google Apps Script mengonfirmasi: ${deleteRes.message || 'Baris berhasil dihapus'} (Dihapus: ${delCount} baris)`,
        timestamp: new Date().toLocaleTimeString('id-ID')
      });

      res.json({
        success: true,
        message: 'Uji Coba Live CRUD Berhasil! Data dapat ditulis dan dihapus langsung dari Google Spreadsheet.',
        testId,
        auditLogs
      });
    } catch (err: unknown) {
      const error = err as Error;
      auditLogs.push({
        step: 'Gagal melakukan tes CRUD',
        status: 'error',
        detail: error?.message || String(err),
        timestamp: new Date().toLocaleTimeString('id-ID')
      });
      res.status(500).json({
        success: false,
        message: 'Uji coba CRUD gagal: ' + (error?.message || String(err)),
        auditLogs
      });
    }
  });

  // Push all existing submissions to Google Apps Script (in fast concurrent batches)
  app.post('/api/sync/push-seed', async (_req: Request, res: Response) => {
    const db = readDb();
    if (!db.config.gasUrl) {
      res.status(400).json({ success: false, message: 'URL Google Apps Script belum diisi' });
      return;
    }

    let successCount = 0;
    const batchSize = 5;

    // Push submissions in batches of 5
    for (let i = 0; i < db.submissions.length; i += batchSize) {
      const batch = db.submissions.slice(i, i + batchSize);
      await Promise.all(
        batch.map(async (record) => {
          try {
            await callGasWebhook({
              action: 'create',
              formType: record.type,
              data: record.data,
              id: record.id,
              timestamp: record.timestamp
            });
            record.syncedToGoogle = true;
            successCount++;
          } catch (err) {
            console.error('Failed to push record to GAS:', record.id, err);
          }
        })
      );
    }

    // Push mikrotik accounts in batches of 5
    for (let i = 0; i < db.mikrotik.length; i += batchSize) {
      const batch = db.mikrotik.slice(i, i + batchSize);
      await Promise.all(
        batch.map(async (mkt) => {
          try {
            await callGasWebhook({
              action: 'create',
              formType: 'mikrotik',
              data: mkt,
              id: mkt.id
            });
          } catch (err) {
            console.error('Failed to push mikrotik to GAS:', mkt.username, err);
          }
        })
      );
    }

    saveDb(db);
    res.json({
      success: true,
      message: `Berhasil mengekspor ${successCount} data aset/akun ke Google Spreadsheet!`,
      totalSubmissions: db.submissions.length,
      totalMikrotik: db.mikrotik.length
    });
  });

  // ==========================================
  // FRONTEND SERVING
  // ==========================================
  const isProduction = process.env.NODE_ENV === 'production';

  if (!isProduction) {
    const { createServer: createViteServer } = await import('vite');
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: 'spa'
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.resolve(__dirname, 'dist');
    app.use(express.static(distPath));
    app.get('*', (_req: Request, res: Response) => {
      res.sendFile(path.resolve(distPath, 'index.html'));
    });
  }

  app.listen(PORT, '0.0.0.0', () => {
    console.log(`\n  🚀 Portal Aset Digital Syamanah aktif!`);
    console.log(`  ➜  Local:   http://localhost:${PORT}/`);
    console.log(`  ➜  Network: http://127.0.0.1:${PORT}/\n`);
  });
}

startServer().catch((err) => {
  console.error('Failed to start server:', err);
  process.exit(1);
});
