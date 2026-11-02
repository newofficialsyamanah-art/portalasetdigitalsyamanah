import * as XLSX from 'xlsx';
import { SubmissionRecord, FormPC, FormAkun, MikrotikAccount } from '../types';

/**
 * Clean & normalize keys (lowercase, trim, remove symbols)
 */
function normalizeKey(key: string): string {
  return String(key || '')
    .toLowerCase()
    .replace(/[^a-z0-9]/g, '');
}

/**
 * Get value from an object regardless of case / spaces in header name
 */
function findVal(row: Record<string, any>, possibleKeys: string[]): any {
  if (!row) return '';

  // 1. Direct check
  for (const k of possibleKeys) {
    if (row[k] !== undefined && row[k] !== null && String(row[k]).trim() !== '') {
      return row[k];
    }
  }

  // 2. Normalized check
  const rowKeys = Object.keys(row);
  const normalizedMap: Record<string, string> = {};
  for (const rk of rowKeys) {
    normalizedMap[normalizeKey(rk)] = rk;
  }

  for (const k of possibleKeys) {
    const norm = normalizeKey(k);
    if (normalizedMap[norm]) {
      const actualKey = normalizedMap[norm];
      const val = row[actualKey];
      if (val !== undefined && val !== null && String(val).trim() !== '') {
        return val;
      }
    }
  }

  return '';
}

/**
 * Export all 3 datasets into a multi-sheet Microsoft Excel (.xlsx) workbook
 */
export function exportDatabaseToExcel(
  pcRecords: SubmissionRecord[],
  akunRecords: SubmissionRecord[],
  mikrotikAccounts: MikrotikAccount[]
) {
  const wb = XLSX.utils.book_new();

  // 1. Sheet PC & Laptop
  const pcRows = pcRecords.map((r, idx) => {
    const d = r.data as FormPC;
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

  // 2. Sheet Akun Sales & HP
  const akunRows = akunRecords.map((r, idx) => {
    const d = r.data as FormAkun;
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

  // 3. Sheet MikroTik
  const mikrotikRows = mikrotikAccounts.map((m, idx) => ({
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

  const dateStr = new Date().toISOString().slice(0, 10);
  const filename = `Database_Syamanah_${dateStr}.xlsx`;

  // Write workbook to binary array buffer
  const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
  const blob = new Blob([wbout], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

  // Safe browser download via Blob ObjectURL without navigating the page
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.style.display = 'none';
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  setTimeout(() => {
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }, 500);
}

/**
 * Helper to get prefix, tim and profile for MikroTik
 */
function getMikrotikMeta(kat: string) {
  const k = (kat || '').toLowerCase();
  if (k.includes('jaket 1') || k.includes('alya')) return { tim: 'jaket1' as const, prefix: 'SJ1', profile: 'sales_jaket' };
  if (k.includes('jaket 2') || k.includes('devia')) return { tim: 'jaket2' as const, prefix: 'SJ2', profile: 'sales_jaket' };
  if (k.includes('jersey 1') || k.includes('wahdah')) return { tim: 'jersey1' as const, prefix: 'SR1', profile: 'sales_jersey' };
  if (k.includes('jersey 2') || k.includes('iir') || k.includes('seragam')) return { tim: 'jersey2' as const, prefix: 'SR2', profile: 'sales_jersey' };
  if (k.includes('crm')) return { tim: 'crm' as const, prefix: 'CRM', profile: 'crm' };
  return { tim: 'eksekutif' as const, prefix: 'SE', profile: 'sales_eksekutif' };
}

/**
 * Universal & ultra-resilient parser for any uploaded Excel (.xlsx, .xls) or CSV file
 */
export async function parseExcelOrCsvFile(file: File): Promise<{
  pcRecords: SubmissionRecord[];
  akunRecords: SubmissionRecord[];
  mikrotikAccounts: MikrotikAccount[];
  totalCount: number;
}> {
  let workbook: XLSX.WorkBook;

  try {
    const data = await file.arrayBuffer();
    workbook = XLSX.read(new Uint8Array(data), { type: 'array' });
  } catch {
    const text = await file.text();
    workbook = XLSX.read(text, { type: 'string' });
  }

  const pcList: SubmissionRecord[] = [];
  const akunList: SubmissionRecord[] = [];
  const mikrotikList: MikrotikAccount[] = [];

  const mktCounters: Record<string, number> = {};

  for (const sheetName of workbook.SheetNames) {
    const ws = workbook.Sheets[sheetName];
    if (!ws) continue;

    // Use sheet_to_json with defval empty string
    const rawRows: Record<string, any>[] = XLSX.utils.sheet_to_json(ws, { defval: '' });
    if (!rawRows || rawRows.length === 0) continue;

    const nameLower = sheetName.toLowerCase();

    // Collect all column keys from the first few rows
    const allKeysSet = new Set<string>();
    rawRows.slice(0, 3).forEach((r) => Object.keys(r).forEach((k) => allKeysSet.add(k)));
    const sampleKeys = Array.from(allKeysSet).map(normalizeKey);

    // 1. Detect by sheet name
    const isNamedPC = nameLower.includes('pc') || nameLower.includes('laptop') || nameLower.includes('perangkat') || nameLower.includes('komputer') || nameLower.includes('hardware');
    const isNamedAkun = nameLower.includes('akun') || nameLower.includes('sales') || nameLower.includes('hp') || nameLower.includes('handphone');
    const isNamedMikrotik = nameLower.includes('mikrotik') || nameLower.includes('hotspot') || nameLower.includes('router');

    // 2. Detect by columns
    const hasMikrotikHeaders = sampleKeys.some((k) => k.includes('mikrotik') || k.includes('winbox') || (k.includes('username') && (k.includes('profile') || k.includes('rate') || k.includes('script') || k.includes('password') || k.includes('tim'))));
    const hasDxDiagHeaders = sampleKeys.some((k) => k.includes('computername') || k.includes('processor') || k.includes('display') || k.includes('dxdiag') || k.includes('ram'));
    const hasAkunHeaders = sampleKeys.some((k) => k.includes('handphone') || k.includes('jenishp') || k.includes('email1') || k.includes('email') || k.includes('ig1') || k.includes('fb1') || k.includes('bekashp') || k.includes('nowa') || k.includes('kategori') || k.includes('pemegang'));

    let targetType: 'pc' | 'akun' | 'mikrotik' = 'pc';

    if (isNamedMikrotik || hasMikrotikHeaders) {
      targetType = 'mikrotik';
    } else if (isNamedPC || hasDxDiagHeaders) {
      targetType = 'pc';
    } else if (isNamedAkun || hasAkunHeaders || sampleKeys.length >= 10) {
      targetType = 'akun';
    }

    rawRows.forEach((row, i) => {
      // Find row values with extreme flexibility
      const rawValues = Object.values(row).map((v) => String(v || '').trim()).filter(Boolean);
      if (rawValues.length === 0) return; // Ignore empty rows

      if (targetType === 'pc') {
        const nama = findVal(row, ['Nama', 'nama', 'Nama Lengkap', 'User', 'Pengguna', 'Nama Staf', 'Staf', 'Nama Pemilik']);
        const computerName = findVal(row, ['Computer Name', 'computerName', 'Nama Komputer', 'Device Name', 'Hostname']);
        const ip = findVal(row, ['IP', 'ip', 'IP Address', 'Alamat IP']);

        // Resilient: if not found, use first text column
        const finalNama = String(nama || rawValues[0] || `Staf-${i + 1}`);
        const cName = String(computerName || (finalNama ? `PC-${finalNama}` : `PC-${i + 1}`));
        const isLap = cName.toUpperCase().includes('LAPTOP') || cName.toUpperCase().includes('MACBOOK') || cName.toUpperCase().includes('NOTEBOOK');

        pcList.push({
          id: `pc_import_${Date.now()}_${i}`,
          type: 'pc',
          timestamp: String(findVal(row, ['Timestamp', 'timestamp', 'Waktu', 'Tanggal']) || new Date().toLocaleString('id-ID')),
          data: {
            nama: finalNama,
            departemen: String(findVal(row, ['Departemen', 'departemen', 'Divisi', 'Bagian', 'Department']) || '-'),
            jabatan: String(findVal(row, ['Jabatan', 'jabatan', 'Posisi', 'Role']) || '-'),
            jenisPerangkat: isLap ? 'Laptop' : 'PC Desktop',
            computerName: cName,
            ip: String(ip || '-'),
            passwordPc: String(findVal(row, ['Password PC', 'passwordPc', 'Password', 'Password PC (Optional)', 'PW PC']) || '-'),
            os: String(findVal(row, ['Operating System', 'os', 'OS', 'Sistem Operasi']) || 'Windows 10 / 11'),
            manufacturer: String(findVal(row, ['System Manufacturer', 'manufacturer', 'Brand', 'Merk', 'Merk Laptop']) || '-'),
            processor: String(findVal(row, ['Processor', 'processor', 'CPU', 'Prosesor']) || '-'),
            memory: String(findVal(row, ['Memory', 'memory', 'RAM', 'Kapasitas RAM']) || '-'),
            displayLink: String(findVal(row, ['Display Link', 'Display', 'displayLink', 'Foto Display']) || '-'),
            soundLink: String(findVal(row, ['Sound Link', 'Sound', 'soundLink', 'Foto Sound']) || '-'),
            inputLink: String(findVal(row, ['Input Link', 'Input', 'inputLink', 'Foto Input']) || '-')
          },
          syncedToGoogle: true
        });
      } else if (targetType === 'akun') {
        const nama = findVal(row, ['Nama Pemegang', 'Nama', 'nama', 'Nama Lengkap', 'Sales', 'Staf Sales', 'Nama Staf', 'User']);
        const email1 = findVal(row, ['Email 1', 'email1', 'Email', 'Akun Email', 'Email Utama']);
        const noWa = findVal(row, ['No Telpon / WhatsApp', 'No Telpon / WA', 'noWa', 'No WA', 'WhatsApp', 'Nomor WA', 'No Telepon', 'No Handphone']);

        const finalNama = String(nama || rawValues[1] || rawValues[0] || `Sales-${i + 1}`);
        const kategori = String(findVal(row, ['Kategori Tim/Role', 'Kategori / Tim', 'Kategori', 'kategori', 'Tim', 'Team', 'Divisi']) || 'Sales & CRM');
        const jabatan = String(findVal(row, ['Jabatan', 'jabatan', 'Posisi']) || 'Staff Sales');

        akunList.push({
          id: String(findVal(row, ['ID', 'id']) || `akun_import_${Date.now()}_${i}`),
          type: 'akun',
          timestamp: String(findVal(row, ['Timestamp', 'timestamp', 'Waktu', 'Tanggal']) || new Date().toLocaleString('id-ID')),
          data: {
            nama: finalNama,
            kategori,
            jabatan,
            jenisHp: String(findVal(row, ['Jenis Handphone', 'Jenis HP', 'jenisHp', 'Tipe HP', 'Merk HP', 'Model HP']) || '-'),
            bekasHp: String(findVal(row, ['Bekas handphone siapa?', 'Bekas HP Siapa', 'bekasHp', 'Pengguna Sebelumnya']) || '-'),
            akunLoginPc: String(findVal(row, ['Akun login di PC', 'Akun Login PC', 'akunLoginPc', 'Login PC']) || '-'),
            email1: String(email1 || '-'),
            passEmail1: String(findVal(row, ['Password Email 1', 'Pass Email 1', 'passEmail1', 'PW Email 1']) || '-'),
            email2: String(findVal(row, ['Email 2', 'email2']) || '-'),
            passEmail2: String(findVal(row, ['Password Email 2', 'Pass Email 2', 'passEmail2']) || '-'),
            email3: String(findVal(row, ['Email 3', 'email3']) || '-'),
            passEmail3: String(findVal(row, ['Password Email 3', 'Pass Email 3', 'passEmail3']) || '-'),
            ig1: String(findVal(row, ['Username IG 1', 'IG 1', 'ig1', 'Instagram 1', 'Akun IG 1']) || '-'),
            passIg1: String(findVal(row, ['Password IG 1', 'Pass IG 1', 'passIg1']) || '-'),
            ig2: String(findVal(row, ['Username IG 2', 'IG 2', 'ig2']) || '-'),
            passIg2: String(findVal(row, ['Password IG 2', 'Pass IG 2', 'passIg2']) || '-'),
            ig3: String(findVal(row, ['Username IG 3', 'IG 3', 'ig3']) || '-'),
            passIg3: String(findVal(row, ['Password IG 3', 'Pass IG 3', 'passIg3']) || '-'),
            fb1: String(findVal(row, ['Facebook 1', 'FB 1', 'fb1', 'Akun FB 1']) || '-'),
            passFb1: String(findVal(row, ['Password FB 1', 'Pass FB 1', 'passFb1']) || '-'),
            fb2: String(findVal(row, ['Facebook 2', 'FB 2', 'fb2']) || '-'),
            passFb2: String(findVal(row, ['Password FB 2', 'Pass FB 2', 'passFb2']) || '-'),
            fb3: String(findVal(row, ['Facebook 3', 'FB 3', 'fb3']) || '-'),
            passFb3: String(findVal(row, ['Password FB 3', 'Pass FB 3', 'passFb3']) || '-'),
            noWa: String(noWa || '-'),
            akunLain: String(findVal(row, ['Akun lain terhubung aset Syamanah', 'Akun Lain Terhubung', 'akunLain']) || '-'),
            softwareLain: String(findVal(row, ['Akun software lain', 'Software Lain', 'softwareLain']) || '-'),
            catatan: String(findVal(row, ['Catatan Khusus / Audit', 'Catatan', 'catatan', 'Keterangan', 'Notes']) || '-')
          },
          syncedToGoogle: true
        });

        // Also generate corresponding MikroTik account automatically!
        const meta = getMikrotikMeta(kategori);
        mktCounters[meta.prefix] = (mktCounters[meta.prefix] || 0) + 1;
        const numStr = String(mktCounters[meta.prefix]).padStart(2, '0');
        const username = `${meta.prefix}${numStr}`;
        const pass = String(100000 + ((i * 8461 + 31415) % 900000));

        mikrotikList.push({
          id: `mkt_auto_${Date.now()}_${i}`,
          namaPemilik: finalNama,
          tim: meta.tim,
          jabatan,
          username,
          password: pass,
          profileHotspot: meta.profile,
          rateLimit: '10M/10M',
          status: 'aktif',
          commandTerminal: `/ip hotspot user add name="${username}" password="${pass}" profile="${meta.profile}" limit-rate="10M/10M" comment="${finalNama} - ${kategori}"`,
          createdAt: String(findVal(row, ['Timestamp', 'timestamp', 'Waktu']) || new Date().toLocaleString('id-ID'))
        });
      } else if (targetType === 'mikrotik') {
        const username = findVal(row, ['Username MikroTik', 'Username', 'username', 'User Hotspot', 'User', 'Kode Sales']);
        if (!username) return;

        const nama = findVal(row, ['Nama Pemilik', 'Nama Sales', 'Nama', 'namaPemilik', 'Staf', 'Sales']);
        const pass = findVal(row, ['Password (6 Digit)', 'Password', 'password', 'PIN', 'Pass', 'Passcode']);
        const terminalScript = findVal(row, ['Script Winbox Terminal', 'Terminal', 'commandTerminal', 'Command', 'Script Winbox']);

        mikrotikList.push({
          id: String(findVal(row, ['ID', 'id']) || `mkt_import_${Date.now()}_${i}`),
          namaPemilik: String(nama || 'Sales'),
          tim: (findVal(row, ['Tim', 'tim', 'Team', 'Divisi']) || 'jaket1') as any,
          jabatan: String(findVal(row, ['Jabatan', 'jabatan', 'Posisi', 'Role']) || 'Staff'),
          username: String(username),
          password: String(pass || '123456'),
          profileHotspot: String(findVal(row, ['Profile Hotspot', 'profileHotspot', 'Profile', 'Paket']) || 'sales_jaket'),
          rateLimit: String(findVal(row, ['Rate Limit', 'rateLimit', 'Speed', 'Kecepatan']) || '10M/10M'),
          status: (findVal(row, ['Status', 'status']) || 'aktif') as any,
          commandTerminal: String(terminalScript || `/ip hotspot user add name="${username}" password="${pass || '123456'}" profile="sales_jaket" limit-rate="10M/10M" comment="${nama} - ${username}"`),
          createdAt: String(findVal(row, ['Tanggal Dibuat', 'createdAt', 'Timestamp', 'timestamp']) || new Date().toLocaleString('id-ID')),
          catatan: String(findVal(row, ['Catatan', 'catatan', 'Keterangan', 'Notes']) || '-')
        });
      }
    });
  }

  return {
    pcRecords: pcList,
    akunRecords: akunList,
    mikrotikAccounts: mikrotikList,
    totalCount: pcList.length + akunList.length + mikrotikList.length
  };
}
