export type DeviceType = 'Laptop' | 'PC Desktop' | 'All-in-One' | 'Mini PC' | 'Smartphone';

export interface FormPC {
  nama: string;
  departemen: string;
  jabatan: string;
  jenisPerangkat?: 'Laptop' | 'PC Desktop' | 'All-in-One' | 'Mini PC';
  computerName: string;
  ip: string;
  passwordPc: string;
  os: string;
  manufacturer: string;
  processor: string;
  memory: string;
  // Foto langsung & link drive
  fotoPerangkat?: string; // base64 / dataUrl
  fotoPerangkatName?: string;
  displayLink: string;
  soundLink: string;
  inputLink: string;
  // Foto screenshot langsung (otomatis masuk Google Drive)
  displayPhoto?: string;
  displayPhotoName?: string;
  soundPhoto?: string;
  soundPhotoName?: string;
  inputPhoto?: string;
  inputPhotoName?: string;
}

export interface FormAkun {
  nama: string;
  kategori: string;
  jenisHp: string;
  bekasHp: string;
  fotoHp?: string; // base64 / dataUrl
  fotoHpName?: string;
  akunLoginPc: string;
  email1: string;
  passEmail1: string;
  email2: string;
  passEmail2: string;
  email3: string;
  passEmail3: string;
  ig1: string;
  passIg1: string;
  ig2: string;
  passIg2: string;
  ig3: string;
  passIg3: string;
  fb1: string;
  passFb1: string;
  fb2: string;
  passFb2: string;
  fb3: string;
  passFb3: string;
  noWa: string;
  akunLain: string;
  softwareLain: string;
  catatan: string;
  jabatan?: string;
}

export interface SubmissionRecord {
  id: string;
  type: 'pc' | 'akun';
  timestamp: string;
  syncedToGoogle: boolean;
  syncMessage?: string;
  drivePhotoUrl?: string; // URL of the uploaded photo in user's Google Drive
  data: FormPC | FormAkun;
}

export interface SyamanahConfig {
  gasUrl: string;
  spreadsheetId: string;
  driveFolderId: string;
  superadminPin: string;
}

export type MikrotikTeam = 'jaket1' | 'jaket2' | 'jersey1' | 'jersey2' | 'eksekutif' | 'crm';

export interface MikrotikAccount {
  id: string;
  namaPemilik: string;
  jabatan: string;
  tim: MikrotikTeam;
  username: string;
  password: string;
  profileHotspot: string;
  rateLimit: string;
  commandTerminal: string;
  createdAt: string;
  status: 'aktif' | 'nonaktif';
  catatan?: string;
}

export interface AlertState {
  show: boolean;
  type: 'success' | 'error' | 'info' | 'warning';
  title?: string;
  message: string;
}

// User & Role Management
export type UserRole = 'superadmin' | 'manager' | 'general_affair';

export interface UserAccount {
  id: string;
  username: string;
  password?: string;
  name: string;
  role: UserRole;
  createdAt: string;
  lastLogin?: string;
}

export interface AuthSession {
  user: UserAccount;
  token?: string;
}
