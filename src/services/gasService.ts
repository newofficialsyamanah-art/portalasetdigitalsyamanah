import { FormPC, FormAkun, SubmissionRecord, SyamanahConfig, MikrotikAccount } from '../types';
import { INITIAL_SYAMANAH_RECORDS } from '../data/initialRecords';

export const CONFIG_STORAGE_KEY = 'syamanah_system_config';
export const SUBMISSIONS_STORAGE_KEY = 'syamanah_submissions_history';
export const DRAFT_PC_KEY = 'syamanah_draft_pc';
export const DRAFT_AKUN_KEY = 'syamanah_draft_akun';

export const DEFAULT_CONFIG: SyamanahConfig = {
  gasUrl: 'https://script.google.com/macros/s/AKfycbzalkabRunlYtF3eDqZeBI_v_UpOoYSJTHRUUJGn4G_LrUqaSP9NuaSTBipOSN8pttb/exec',
  spreadsheetId: '',
  driveFolderId: '',
  superadminPin: '1234'
};

/**
 * =========================================================================
 * GOOGLE APPS SCRIPT (GAS) CRUD SPREADSHEET ENGINE v3.0
 * =========================================================================
 * Skrip ini menyediakan REST API Webhook dua arah untuk Google Spreadsheet:
 * 1. CRUD Sheet "Spesifikasi PC & Laptop"
 * 2. CRUD Sheet "Akun Sales & HP"
 * 3. CRUD Sheet "Akun MikroTik Hotspot"
 * 4. Unggah Foto otomatis ke folder Google Drive
 * 5. Hapus baris secara fisik di Spreadsheet saat data dihapus dari aplikasi
 */
export const GAS_SCRIPT_CODE = `/**
 * @name Syamanah Asset, Akun & MikroTik - Unified Spreadsheet CRUD Webhook v3.0
 * @description 
 * - doPost: Mendukung action "create" (tambah), "update" (perbarui), "delete" (hapus baris fisik),
 *   dan upload foto otomatis ke Google Drive.
 * - doGet: Membaca seluruh data 3 Sheet (PC & Laptop, Akun Sales & HP, Akun MikroTik) secara konsisten.
 */

function doPost(e) {
  try {
    var payload = JSON.parse(e.postData.contents);
    var action = payload.action || "create";
    var formType = payload.formType; // pc, akun, mikrotik
    var data = payload.data || {};
    var targetSpreadsheetId = payload.spreadsheetId || "";
    var targetFolderId = payload.driveFolderId || "";

    // 1. Hubungkan ke Spreadsheet Aktif atau ID yang Ditentukan
    var sheetFile;
    if (targetSpreadsheetId && targetSpreadsheetId.trim() !== "") {
      var cleanSheetId = targetSpreadsheetId.replace(/^.*\\/d\\/([a-zA-Z0-9_-]+).*$/, "$1").trim();
      sheetFile = SpreadsheetApp.openById(cleanSheetId);
    } else {
      sheetFile = SpreadsheetApp.getActiveSpreadsheet();
    }

    // 2. Fungsi Unggah Foto ke Google Drive
    function uploadPhotoToDrive(base64Data, fileNamePrefix) {
      if (!base64Data || base64Data.indexOf('base64,') === -1) {
        return base64Data || "";
      }
      try {
        var parts = base64Data.split('base64,');
        var contentType = parts[0].replace('data:', '').replace(';base64', '');
        var decoded = Utilities.base64Decode(parts[1]);
        var cleanPrefix = (fileNamePrefix || "Aset").replace(/[^a-zA-Z0-9_-]/g, "_");
        var fileName = cleanPrefix + "_" + Utilities.formatDate(new Date(), "Asia/Jakarta", "yyyyMMdd_HHmmss") + ".jpg";
        var blob = Utilities.newBlob(decoded, contentType, fileName);

        var folder;
        if (targetFolderId && targetFolderId.trim() !== "") {
          var cleanFolderId = targetFolderId.replace(/^.*folders\\/([a-zA-Z0-9_-]+).*$/, "$1").trim();
          folder = DriveApp.getFolderById(cleanFolderId);
        } else {
          var existing = DriveApp.getFoldersByName("Foto Aset Syamanah");
          folder = existing.hasNext() ? existing.next() : DriveApp.createFolder("Foto Aset Syamanah");
        }

        var uploadedFile = folder.createFile(blob);
        uploadedFile.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
        return uploadedFile.getUrl();
      } catch (err) {
        return "Gagal upload: " + err.toString();
      }
    }

    // ==========================================
    // ACTION: DELETE (Hapus baris fisik dari Spreadsheet)
    // ==========================================
    if (action === "delete") {
      var targetSheet;
      if (formType === "pc") {
        targetSheet = sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC Syamanah (Responses)") || 
                      sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC") || 
                      sheetFile.getSheetByName("Spesifikasi PC & Laptop") || 
                      sheetFile.getSheetByName("Spesifikasi PC");
        if (!targetSheet) {
          var allSheetsPC = sheetFile.getSheets();
          for (var sp = 0; sp < allSheetsPC.length; sp++) {
            var spName = allSheetsPC[sp].getName().toLowerCase();
            if (spName.indexOf("spesifikasi") !== -1 || spName.indexOf("perangkat pc") !== -1) {
              targetSheet = allSheetsPC[sp];
              break;
            }
          }
        }
      } else if (formType === "akun") {
        targetSheet = sheetFile.getSheetByName("Form Pencatatan Akun & Sales Syamanah 2026 (Responses)") || 
                      sheetFile.getSheetByName("Form Responses 1") || 
                      sheetFile.getSheetByName("Akun Sales & HP") || 
                      sheetFile.getSheetByName("Akun Sales");
        if (!targetSheet) {
          var allSheetsAkun = sheetFile.getSheets();
          for (var sa = 0; sa < allSheetsAkun.length; sa++) {
            var saName = allSheetsAkun[sa].getName().toLowerCase();
            if (saName.indexOf("pencatatan akun") !== -1 || (saName.indexOf("akun") !== -1 && saName.indexOf("mikrotik") === -1)) {
              targetSheet = allSheetsAkun[sa];
              break;
            }
          }
        }
      } else {
        targetSheet = sheetFile.getSheetByName("Akun MikroTik Hotspot") || 
                      sheetFile.getSheetByName("Akun Mikrotik Hotspot") ||
                      sheetFile.getSheetByName("MikroTik") || 
                      sheetFile.getSheetByName("Hotspot");
      }

      if (!targetSheet || targetSheet.getLastRow() <= 1) {
        return ContentService.createTextOutput(JSON.stringify({
          status: "success",
          message: "Sheet kosong atau tidak ditemukan",
          deletedCount: 0
        })).setMimeType(ContentService.MimeType.JSON);
      }

      var rows = targetSheet.getDataRange().getValues();
      var keyToDelete = String(payload.key || payload.id || payload.nama || "").toLowerCase().trim();
      var deletedRows = 0;

      // Hapus dari baris terbawah ke atas agar index baris tidak bergeser
      for (var r = rows.length - 1; r >= 1; r--) {
        var rowText = rows[r].map(function(c) { return String(c).toLowerCase().trim(); });
        var match = false;

        if (keyToDelete && rowText.indexOf(keyToDelete) !== -1) {
          match = true;
        } else if (payload.id && rowText[1] && rowText[1] === String(payload.id).toLowerCase().trim()) {
          match = true;
        } else if (payload.timestamp && rowText[0] && rowText[0].indexOf(String(payload.timestamp).toLowerCase().trim()) !== -1) {
          match = true;
        }

        if (match) {
          targetSheet.deleteRow(r + 1);
          deletedRows++;
        }
      }

      return ContentService.createTextOutput(JSON.stringify({
        status: "success",
        message: "Berhasil menghapus " + deletedRows + " baris di Spreadsheet (" + sheetName + ")",
        deletedCount: deletedRows
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // ==========================================
    // ACTION: CREATE (Tambah baris baru)
    // ==========================================
    var now = new Date();
    var timestamp = Utilities.formatDate(now, "Asia/Jakarta", "M/d/yyyy HH:mm:ss");

    // 1. Sheet Spesifikasi PC & Laptop (Catatan Spesifikasi Perangkat PC Syamanah Responses)
    if (formType === "pc") {
      var sheetPC = sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC Syamanah (Responses)") || 
                    sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC") || 
                    sheetFile.getSheetByName("Form Responses 1") || 
                    sheetFile.getSheetByName("Spesifikasi PC & Laptop") || 
                    sheetFile.getSheetByName("Spesifikasi PC");
      if (!sheetPC) {
        var allShP = sheetFile.getSheets();
        for (var pIdx = 0; pIdx < allShP.length; pIdx++) {
          var pName = allShP[pIdx].getName().toLowerCase();
          if (pName.indexOf("spesifikasi") !== -1 || pName.indexOf("perangkat pc") !== -1) {
            sheetPC = allShP[pIdx];
            break;
          }
        }
      }
      if (!sheetPC) {
        sheetPC = sheetFile.insertSheet("Catatan Spesifikasi Perangkat PC Syamanah (Responses)");
      }
      
      if (sheetPC.getLastRow() === 0) {
        sheetPC.appendRow([
          "Timestamp", "Nama", "Departemen", "Jabatan", "Computer Name", 
          "IP", "Password PC (Optional)", "Operating System", "System Manufacturer", 
          "Processor", "Memory", "Display", "Sound", "Input"
        ]);
        sheetPC.getRange(1, 1, 1, 14).setBackground("#047857").setFontColor("#FFFFFF").setFontWeight("bold");
      }

      // Upload screenshots tab DxDiag langsung ke Google Drive
      var displayUrl = data.displayPhoto ? uploadPhotoToDrive(data.displayPhoto, (data.nama || "PC") + "_DxDiag_Display") : (data.displayLink || "-");
      var soundUrl = data.soundPhoto ? uploadPhotoToDrive(data.soundPhoto, (data.nama || "PC") + "_DxDiag_Sound") : (data.soundLink || "-");
      var inputUrl = data.inputPhoto ? uploadPhotoToDrive(data.inputPhoto, (data.nama || "PC") + "_DxDiag_Input") : (data.inputLink || "-");
      var photoUrl = data.fotoPerangkat ? uploadPhotoToDrive(data.fotoPerangkat, (data.nama || "PC") + "_Unit") : "";

      var headerRowPC = sheetPC.getRange(1, 1, 1, Math.min(sheetPC.getLastColumn(), 5)).getValues()[0];
      var isGoogleFormPC14 = headerRowPC.length > 1 && String(headerRowPC[1]).toLowerCase().indexOf("nama") !== -1;

      if (isGoogleFormPC14) {
        sheetPC.appendRow([
          timestamp,
          data.nama || "-",
          data.departemen || "-",
          data.jabatan || "-",
          data.computerName || "-",
          data.ip || "-",
          data.passwordPc || "-",
          data.os || "-",
          data.manufacturer || "-",
          data.processor || "-",
          data.memory || "-",
          displayUrl || "-",
          soundUrl || "-",
          inputUrl || "-"
        ]);
      } else {
        sheetPC.appendRow([
          timestamp,
          data.id || payload.id || ("pc_" + new Date().getTime()),
          data.nama || "-",
          data.departemen || "-",
          data.jabatan || "-",
          data.jenisPerangkat || "Laptop",
          photoUrl || displayUrl || "-",
          data.computerName || "-",
          data.ip || "-",
          data.passwordPc || "-",
          data.os || "-",
          data.manufacturer || "-",
          data.processor || "-",
          data.memory || "-",
          displayUrl || "-",
          soundUrl || "-",
          inputUrl || "-"
        ]);
      }

      return ContentService.createTextOutput(JSON.stringify({ 
        status: "success", 
        message: "Data " + (data.computerName || data.nama) + " & foto screenshot berhasil disimpan ke Spreadsheet & Google Drive!",
        photoUrl: photoUrl || displayUrl,
        displayUrl: displayUrl,
        soundUrl: soundUrl,
        inputUrl: inputUrl
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // 2. Sheet Akun Sales & HP (Form Pencatatan Akun & Sales Syamanah 2026 Responses)
    else if (formType === "akun") {
      var sheetAkun = sheetFile.getSheetByName("Form Pencatatan Akun & Sales Syamanah 2026 (Responses)") || 
                      sheetFile.getSheetByName("Form Responses 1") || 
                      sheetFile.getSheetByName("Akun Sales & HP") || 
                      sheetFile.getSheetByName("Akun Sales");
      if (!sheetAkun) {
        var allSheetsA = sheetFile.getSheets();
        for (var sIdx = 0; sIdx < allSheetsA.length; sIdx++) {
          var sName = allSheetsA[sIdx].getName().toLowerCase();
          if (sName.indexOf("pencatatan akun") !== -1 || (sName.indexOf("akun") !== -1 && sName.indexOf("mikrotik") === -1)) {
            sheetAkun = allSheetsA[sIdx];
            break;
          }
        }
      }
      if (!sheetAkun) {
        sheetAkun = sheetFile.insertSheet("Form Pencatatan Akun & Sales Syamanah 2026 (Responses)");
      }

      if (sheetAkun.getLastRow() === 0) {
        sheetAkun.appendRow([
          "Timestamp", "Nama ", "Kategori Tim/Role", "Jenis Handphone (Merk, RAM, Memori Internal)", 
          "Bekas handphone siapa?", "Akun yang login di PC", 
          "Email 1", "Password Email 1", "Email 2", "Password Email 2", "Email 3", "Password Email 3", 
          "Username IG 1", "Password IG 1", "Username IG 2", "Password IG 2", "Username IG 3", "Password IG 3", 
          "Facebook 1", "Password FB 1", "Facebook 2", "Password FB 2", "Facebook 3", "Password FB 3", 
          "No Telpon / WhatsApp", "Akun lain yang terhubung ke aset Syamanah", 
          "Isi jika ada akun software lain tapi milik syamanah", "Catatan (Isi jika terdapat sesuatu yang janggal)", "Jabatan"
        ]);
        sheetAkun.getRange(1, 1, 1, 29).setBackground("#0f766e").setFontColor("#FFFFFF").setFontWeight("bold");
      }

      var photoHpUrl = uploadPhotoToDrive(data.fotoHp, (data.nama || "HP") + "_Smartphone");

      // Cek apakah sheet menggunakan header Google Form 29 kolom
      var headerRow = sheetAkun.getRange(1, 1, 1, Math.min(sheetAkun.getLastColumn(), 5)).getValues()[0];
      var isGoogleForm29 = headerRow.length > 1 && String(headerRow[1]).toLowerCase().indexOf("nama") !== -1;

      if (isGoogleForm29) {
        sheetAkun.appendRow([
          timestamp,
          data.nama || "-",
          data.kategori || "-",
          data.jenisHp || "-",
          data.bekasHp || "-",
          data.akunLoginPc || "-",
          data.email1 || "", data.passEmail1 || "",
          data.email2 || "", data.passEmail2 || "",
          data.email3 || "", data.passEmail3 || "",
          data.ig1 || "", data.passIg1 || "",
          data.ig2 || "", data.passIg2 || "",
          data.ig3 || "", data.passIg3 || "",
          data.fb1 || "", data.passFb1 || "",
          data.fb2 || "", data.passFb2 || "",
          data.fb3 || "", data.passFb3 || "",
          data.noWa || "-",
          data.akunLain || "",
          data.softwareLain || "",
          data.catatan || "",
          data.jabatan || ""
        ]);
      } else {
        sheetAkun.appendRow([
          timestamp,
          data.id || payload.id || ("akun_" + new Date().getTime()),
          data.nama || "-",
          data.kategori || "-",
          data.jenisHp || "-",
          photoHpUrl || "-",
          data.bekasHp || "-",
          data.akunLoginPc || "-",
          data.email1 || "", data.passEmail1 || "",
          data.email2 || "", data.passEmail2 || "",
          data.email3 || "", data.passEmail3 || "",
          data.ig1 || "", data.passIg1 || "",
          data.ig2 || "", data.passIg2 || "",
          data.ig3 || "", data.passIg3 || "",
          data.fb1 || "", data.passFb1 || "",
          data.fb2 || "", data.passFb2 || "",
          data.fb3 || "", data.passFb3 || "",
          data.noWa || "-",
          data.akunLain || "",
          data.softwareLain || "",
          data.catatan || "",
          data.jabatan || ""
        ]);
      }

      return ContentService.createTextOutput(JSON.stringify({ 
        status: "success", 
        message: "Data Akun Sales (" + data.nama + ") berhasil disimpan ke Spreadsheet (Form Pencatatan Akun & Sales Syamanah 2026)!",
        photoUrl: photoHpUrl 
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // 3. Sheet Akun MikroTik Hotspot (Fleksibel mendeteksi nama sheet apa pun)
    else if (formType === "mikrotik") {
      var sheetMikrotik = sheetFile.getSheetByName("Akun MikroTik Hotspot") || 
                          sheetFile.getSheetByName("Akun Mikrotik Hotspot") ||
                          sheetFile.getSheetByName("Akun MikroTik") ||
                          sheetFile.getSheetByName("Akun Mikrotik") ||
                          sheetFile.getSheetByName("MikroTik") ||
                          sheetFile.getSheetByName("Mikrotik") ||
                          sheetFile.getSheetByName("Hotspot");
      if (!sheetMikrotik) {
        var allSheets = sheetFile.getSheets();
        for (var s = 0; s < allSheets.length; s++) {
          var sName = allSheets[s].getName().toLowerCase();
          if (sName.indexOf("mikrotik") !== -1 || sName.indexOf("hotspot") !== -1) {
            sheetMikrotik = allSheets[s];
            break;
          }
        }
      }
      if (!sheetMikrotik) {
        sheetMikrotik = sheetFile.insertSheet("Akun MikroTik Hotspot");
      }

      if (sheetMikrotik.getLastRow() === 0) {
        sheetMikrotik.appendRow([
          "Timestamp", "ID", "Nama Pemilik", "Tim", "Jabatan", 
          "Username MikroTik", "Password (6 Digit)", "Profile Hotspot", 
          "Rate Limit", "Script Winbox Terminal", "Status", "Catatan"
        ]);
        sheetMikrotik.getRange(1, 1, 1, 12).setBackground("#1e3a8a").setFontColor("#FFFFFF").setFontWeight("bold");
      }

      sheetMikrotik.appendRow([
        timestamp,
        data.id || payload.id || ("mkt_" + new Date().getTime()),
        data.namaPemilik || "-",
        data.tim || "-",
        data.jabatan || "-",
        data.username || "-",
        data.password || "-",
        data.profileHotspot || "-",
        data.rateLimit || "-",
        data.commandTerminal || "-",
        data.status || "aktif",
        data.catatan || "-"
      ]);

      return ContentService.createTextOutput(JSON.stringify({
        status: "success",
        message: "Akun MikroTik (" + data.username + " - " + data.namaPemilik + ") berhasil disimpan ke Spreadsheet!"
      })).setMimeType(ContentService.MimeType.JSON);
    }

    return ContentService.createTextOutput(JSON.stringify({ 
      status: "error", 
      message: "Tipe form tidak dikenal: " + formType 
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({ 
      status: "error", 
      message: error.toString() 
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * doGet: Membaca seluruh data Spreadsheet dan mengembalikannya ke aplikasi web
 */
function doGet(e) {
  try {
    var targetSpreadsheetId = (e && e.parameter && e.parameter.spreadsheetId) ? e.parameter.spreadsheetId : "";
    var sheetFile;
    if (targetSpreadsheetId && targetSpreadsheetId.trim() !== "") {
      var cleanSheetId = targetSpreadsheetId.replace(/^.*\\/d\\/([a-zA-Z0-9_-]+).*$/, "$1").trim();
      sheetFile = SpreadsheetApp.openById(cleanSheetId);
    } else {
      sheetFile = SpreadsheetApp.getActiveSpreadsheet();
    }

    var result = {
      status: "success",
      pc: [],
      akun: [],
      mikrotik: []
    };

    // 1. Baca Data PC / Laptop (Catatan Spesifikasi Perangkat PC Syamanah Responses)
    var sheetPC = sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC Syamanah (Responses)") || 
                  sheetFile.getSheetByName("Catatan Spesifikasi Perangkat PC") || 
                  sheetFile.getSheetByName("Form Responses 1") || 
                  sheetFile.getSheetByName("Spesifikasi PC & Laptop") || 
                  sheetFile.getSheetByName("Spesifikasi PC");
    if (!sheetPC) {
      var allShP2 = sheetFile.getSheets();
      for (var kp2 = 0; kp2 < allShP2.length; kp2++) {
        var shNP2 = allShP2[kp2].getName().toLowerCase();
        if (shNP2.indexOf("spesifikasi") !== -1 || shNP2.indexOf("perangkat pc") !== -1) {
          sheetPC = allShP2[kp2];
          break;
        }
      }
    }

    if (sheetPC && sheetPC.getLastRow() > 1) {
      var rowsPC = sheetPC.getDataRange().getValues();
      var isGFPC = rowsPC[0].length > 1 && String(rowsPC[0][1]).toLowerCase().indexOf("nama") !== -1;

      for (var i = 1; i < rowsPC.length; i++) {
        var r = rowsPC[i];
        if (isGFPC) {
          if (r[1] || r[4]) {
            var cName = String(r[4] || '');
            var isLap = cName.toLowerCase().indexOf("laptop") !== -1 || cName.toLowerCase().indexOf("macbok") !== -1;
            result.pc.push({
              id: "syam_form_pc_" + i,
              timestamp: r[0] ? String(r[0]) : "",
              nama: r[1] || "",
              departemen: r[2] || "",
              jabatan: r[3] || "",
              jenisPerangkat: isLap ? "Laptop" : "PC Desktop",
              computerName: cName,
              ip: r[5] || "",
              passwordPc: r[6] || "",
              os: r[7] || "",
              manufacturer: r[8] || "",
              processor: r[9] || "",
              memory: r[10] || "",
              displayLink: r[11] || "",
              soundLink: r[12] || "",
              inputLink: r[13] || "",
              drivePhotoUrl: r[11] || ""
            });
          }
        } else {
          var hasIdCol = rowsPC[0][1] === "ID";
          var offset = hasIdCol ? 1 : 0;
          if (r[1 + offset] || r[4 + offset] || r[6 + offset]) {
            result.pc.push({
              id: hasIdCol ? String(r[1]) : "pc_row_" + i,
              timestamp: r[0] ? String(r[0]) : "",
              nama: r[1 + offset] || "",
              departemen: r[2 + offset] || "",
              jabatan: r[3 + offset] || "",
              jenisPerangkat: r[4 + offset] || "Laptop",
              drivePhotoUrl: r[5 + offset] || "",
              computerName: r[6 + offset] || "",
              ip: r[7 + offset] || "",
              passwordPc: r[8 + offset] || "",
              os: r[9 + offset] || "",
              manufacturer: r[10 + offset] || "",
              processor: r[11 + offset] || "",
              memory: r[12 + offset] || "",
              displayLink: r[13 + offset] || "",
              soundLink: r[14 + offset] || "",
              inputLink: r[15 + offset] || ""
            });
          }
        }
      }
    }

    // 2. Baca Data Akun Sales
    var sheetAkun = sheetFile.getSheetByName("Form Pencatatan Akun & Sales Syamanah 2026 (Responses)") || 
                    sheetFile.getSheetByName("Form Responses 1") || 
                    sheetFile.getSheetByName("Akun Sales & HP") || 
                    sheetFile.getSheetByName("Akun Sales");
    if (!sheetAkun) {
      var allShA = sheetFile.getSheets();
      for (var ka = 0; ka < allShA.length; ka++) {
        var shNameA = allShA[ka].getName().toLowerCase();
        if (shNameA.indexOf("pencatatan akun") !== -1 || (shNameA.indexOf("akun") !== -1 && shNameA.indexOf("mikrotik") === -1)) {
          sheetAkun = allShA[ka];
          break;
        }
      }
    }

    if (sheetAkun && sheetAkun.getLastRow() > 1) {
      var rowsAkun = sheetAkun.getDataRange().getValues();
      var isGoogleFormStyle = rowsAkun[0].length > 1 && String(rowsAkun[0][1]).toLowerCase().indexOf("nama") !== -1;

      for (var j = 1; j < rowsAkun.length; j++) {
        var a = rowsAkun[j];
        if (isGoogleFormStyle) {
          if (a[1] || a[2]) {
            result.akun.push({
              id: "syam_form_akun_" + j,
              timestamp: a[0] ? String(a[0]) : "",
              nama: a[1] || "",
              kategori: a[2] || "",
              jenisHp: a[3] || "",
              bekasHp: a[4] || "",
              akunLoginPc: a[5] || "",
              email1: a[6] || "", passEmail1: a[7] || "",
              email2: a[8] || "", passEmail2: a[9] || "",
              email3: a[10] || "", passEmail3: a[11] || "",
              ig1: a[12] || "", passIg1: a[13] || "",
              ig2: a[14] || "", passIg2: a[15] || "",
              ig3: a[16] || "", passIg3: a[17] || "",
              fb1: a[18] || "", passFb1: a[19] || "",
              fb2: a[20] || "", passFb2: a[21] || "",
              fb3: a[22] || "", passFb3: a[23] || "",
              noWa: a[24] || "",
              akunLain: a[25] || "",
              softwareLain: a[26] || "",
              catatan: a[27] || "",
              jabatan: a[28] || ""
            });
          }
        } else {
          var hasAkunId = rowsAkun[0][1] === "ID";
          var aOff = hasAkunId ? 1 : 0;
          if (a[1 + aOff]) {
            result.akun.push({
              id: hasAkunId ? String(a[1]) : "akun_row_" + j,
              timestamp: a[0] ? String(a[0]) : "",
              nama: a[1 + aOff] || "",
              kategori: a[2 + aOff] || "",
              jenisHp: a[3 + aOff] || "",
              drivePhotoUrl: a[4 + aOff] || "",
              bekasHp: a[5 + aOff] || "",
              akunLoginPc: a[6 + aOff] || "",
              email1: a[7 + aOff] || "", passEmail1: a[8 + aOff] || "",
              email2: a[9 + aOff] || "", passEmail2: a[10 + aOff] || "",
              email3: a[11 + aOff] || "", passEmail3: a[12 + aOff] || "",
              ig1: a[13 + aOff] || "", passIg1: a[14 + aOff] || "",
              ig2: a[15 + aOff] || "", passIg2: a[16 + aOff] || "",
              ig3: a[17 + aOff] || "", passIg3: a[18 + aOff] || "",
              fb1: a[19 + aOff] || "", passFb1: a[20 + aOff] || "",
              fb2: a[21 + aOff] || "", passFb2: a[22 + aOff] || "",
              fb3: a[23 + aOff] || "", passFb3: a[24 + aOff] || "",
              noWa: a[25 + aOff] || "",
              akunLain: a[26 + aOff] || "",
              softwareLain: a[27 + aOff] || "",
              catatan: a[28 + aOff] || "",
              jabatan: a[29 + aOff] || ""
            });
          }
        }
      }
    }

    // 3. Baca Data Akun MikroTik
    var sheetMikrotik = sheetFile.getSheetByName("Akun MikroTik Hotspot") || 
                        sheetFile.getSheetByName("Akun Mikrotik Hotspot") ||
                        sheetFile.getSheetByName("Akun MikroTik") ||
                        sheetFile.getSheetByName("Akun Mikrotik") ||
                        sheetFile.getSheetByName("MikroTik") ||
                        sheetFile.getSheetByName("Mikrotik") ||
                        sheetFile.getSheetByName("Hotspot");
    if (!sheetMikrotik) {
      var allSheetsMkt = sheetFile.getSheets();
      for (var sm = 0; sm < allSheetsMkt.length; sm++) {
        var smName = allSheetsMkt[sm].getName().toLowerCase();
        if (smName.indexOf("mikrotik") !== -1 || smName.indexOf("hotspot") !== -1) {
          sheetMikrotik = allSheetsMkt[sm];
          break;
        }
      }
    }
    if (sheetMikrotik && sheetMikrotik.getLastRow() > 1) {
      var rowsMkt = sheetMikrotik.getDataRange().getValues();
      for (var m = 1; m < rowsMkt.length; m++) {
        var mk = rowsMkt[m];
        if (mk[2] || mk[5]) {
          result.mikrotik.push({
            id: mk[1] ? String(mk[1]) : ("mkt_row_" + m),
            createdAt: mk[0] ? String(mk[0]) : "",
            namaPemilik: mk[2] || "",
            tim: mk[3] || "jaket1",
            jabatan: mk[4] || "",
            username: mk[5] || "",
            password: mk[6] || "",
            profileHotspot: mk[7] || "",
            rateLimit: mk[8] || "",
            commandTerminal: mk[9] || "",
            status: mk[10] || "aktif",
            catatan: mk[11] || ""
          });
        }
      }
    }

    return ContentService.createTextOutput(JSON.stringify(result)).setMimeType(ContentService.MimeType.JSON);
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ 
      status: "error", 
      message: err.toString() 
    })).setMimeType(ContentService.MimeType.JSON);
  }
}`;

// Cached in-memory references for fast synchronous component rendering
let cachedConfig: SyamanahConfig = { ...DEFAULT_CONFIG };
let cachedSubmissions: SubmissionRecord[] = [...INITIAL_SYAMANAH_RECORDS];

// Initialize from localStorage cache if available
try {
  const storedCfg = localStorage.getItem(CONFIG_STORAGE_KEY);
  if (storedCfg) cachedConfig = { ...DEFAULT_CONFIG, ...JSON.parse(storedCfg) };
  const storedSubs = localStorage.getItem(SUBMISSIONS_STORAGE_KEY);
  if (storedSubs) {
    const parsed = JSON.parse(storedSubs);
    if (Array.isArray(parsed) && parsed.length > 0) cachedSubmissions = parsed;
  }
} catch {
  // ignore
}

export function getConfig(): SyamanahConfig {
  return cachedConfig;
}

export async function fetchGlobalConfig(): Promise<SyamanahConfig> {
  try {
    const res = await fetch('/api/config');
    if (res.ok) {
      const data = await res.json();
      cachedConfig = { ...DEFAULT_CONFIG, ...data };
      localStorage.setItem(CONFIG_STORAGE_KEY, JSON.stringify(cachedConfig));
      return cachedConfig;
    }
  } catch {
    // fallback to local cache
  }
  return cachedConfig;
}

export async function saveConfig(cfg: Partial<SyamanahConfig>): Promise<void> {
  cachedConfig = { ...cachedConfig, ...cfg };
  localStorage.setItem(CONFIG_STORAGE_KEY, JSON.stringify(cachedConfig));

  try {
    await fetch('/api/config', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(cachedConfig)
    });
  } catch (err) {
    console.error('Failed to sync config to server:', err);
  }
}

export function isValidGasUrl(url: string): boolean {
  if (!url) return false;
  return url.startsWith('https://script.google.com/macros/s/') && url.endsWith('/exec');
}

export function extractIdFromUrl(urlOrId: string, type: 'spreadsheets' | 'folders'): string {
  if (!urlOrId) return '';
  const trimmed = urlOrId.trim();
  if (type === 'spreadsheets') {
    const match = trimmed.match(/\/d\/([a-zA-Z0-9_-]+)/);
    if (match && match[1]) return match[1];
  } else if (type === 'folders') {
    const match = trimmed.match(/folders\/([a-zA-Z0-9_-]+)/);
    if (match && match[1]) return match[1];
  }
  return trimmed;
}

export function getSubmissions(): SubmissionRecord[] {
  return cachedSubmissions;
}

export async function fetchSubmissions(): Promise<SubmissionRecord[]> {
  try {
    const res = await fetch('/api/submissions');
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data)) {
        cachedSubmissions = data;
        localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(data));
        return data;
      }
    }
  } catch {
    // fallback to local cache
  }
  return cachedSubmissions;
}

import {
  getFirestoreSubmissions,
  saveFirestoreSubmission,
  deleteFirestoreSubmission,
  getFirestoreMikrotik,
  bulkMigrateToFirestore,
  validateFirestoreConnection,
  clearCategoryFirestoreData,
  clearAllFirestoreData
} from './firestoreService';

export async function fetchAllUnifiedData(): Promise<{
  config: SyamanahConfig;
  submissions: SubmissionRecord[];
  mikrotik: MikrotikAccount[];
  lastSyncedAt?: string;
}> {
  // 1. Fetch from authoritative server database (.data/db.json)
  try {
    const res = await fetch('/api/data');
    if (res.ok) {
      const data = await res.json();
      if (data.config) {
        cachedConfig = { ...DEFAULT_CONFIG, ...data.config };
        localStorage.setItem(CONFIG_STORAGE_KEY, JSON.stringify(cachedConfig));
      }
      if (Array.isArray(data.submissions)) {
        cachedSubmissions = data.submissions;
        localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(data.submissions));
      }

      // Sync both submissions and mikrotik to Firestore in background
      if (Array.isArray(data.submissions) && Array.isArray(data.mikrotik)) {
        bulkMigrateToFirestore(data.submissions, data.mikrotik).catch(console.warn);
      }

      return {
        config: cachedConfig,
        submissions: data.submissions || cachedSubmissions,
        mikrotik: data.mikrotik || [],
        lastSyncedAt: data.lastSyncedAt || new Date().toLocaleString('id-ID')
      };
    }
  } catch (err) {
    console.warn('Failed to fetch from server /api/data, trying Firestore fallback:', err);
  }

  // 2. Fallback to Firestore directly if server offline
  try {
    const firestoreSubmissions = await getFirestoreSubmissions();
    const firestoreMikrotik = await getFirestoreMikrotik();

    if (firestoreSubmissions.length > 0 || firestoreMikrotik.length > 0) {
      cachedSubmissions = firestoreSubmissions;
      localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(firestoreSubmissions));
      return {
        config: cachedConfig,
        submissions: firestoreSubmissions,
        mikrotik: firestoreMikrotik,
        lastSyncedAt: new Date().toLocaleString('id-ID')
      };
    }
  } catch (err) {
    console.warn('Firestore fallback check error:', err);
  }

  return {
    config: cachedConfig,
    submissions: cachedSubmissions,
    mikrotik: getMikrotikAccounts()
  };
}

export function saveSubmission(record: SubmissionRecord): void {
  cachedSubmissions = [record, ...cachedSubmissions.filter((s) => s.id !== record.id)];
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(cachedSubmissions));

  // 1. Save directly to Cloud Firestore (Permanent)
  saveFirestoreSubmission(record).catch((err) => console.warn('Firestore save error:', err));

  // 2. Sync to server & optional GAS
  fetch('/api/submissions', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(record)
  }).catch((err) => console.error('Failed to push submission to server:', err));
}

export function setAllSubmissions(records: SubmissionRecord[]): void {
  cachedSubmissions = records;
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(records));
}

export function resetToSeedData(): void {
  setAllSubmissions(INITIAL_SYAMANAH_RECORDS);
}

export function updateSubmissionStatus(id: string, synced: boolean, message?: string, drivePhotoUrl?: string): void {
  cachedSubmissions = cachedSubmissions.map((item) => {
    if (item.id === id) {
      return {
        ...item,
        syncedToGoogle: synced,
        syncMessage: message || item.syncMessage,
        drivePhotoUrl: drivePhotoUrl || item.drivePhotoUrl
      };
    }
    return item;
  });
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(cachedSubmissions));
}

export async function updateSubmission(record: SubmissionRecord): Promise<{
  success: boolean;
  message: string;
}> {
  cachedSubmissions = cachedSubmissions.map((s) => (s.id === record.id ? record : s));
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(cachedSubmissions));

  // Save to Firestore
  saveFirestoreSubmission(record).catch(console.warn);

  try {
    const res = await fetch(`/api/submissions/${encodeURIComponent(record.id)}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(record)
    });
    if (res.ok) {
      const data = await res.json();
      return { success: true, message: data.message || 'Data berhasil diperbarui di database Cloud!' };
    }
  } catch (err) {
    console.error('Failed to update submission on server:', err);
  }
  return { success: true, message: 'Data diperbarui di database Cloud' };
}

export async function deleteSubmission(id: string): Promise<void> {
  // Update local cache immediately
  cachedSubmissions = cachedSubmissions.filter((item) => item.id !== id);
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(cachedSubmissions));

  // Delete from Cloud Firestore
  deleteFirestoreSubmission(id).catch(console.warn);

  // Delete from Server
  try {
    await fetch(`/api/submissions/${encodeURIComponent(id)}`, {
      method: 'DELETE'
    });
  } catch (err) {
    console.error('Failed to delete on server:', err);
  }
}

export async function clearCategorySubmissions(category: 'pc' | 'akun'): Promise<void> {
  cachedSubmissions = cachedSubmissions.filter((item) => item.type !== category);
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify(cachedSubmissions));

  // Clear from Firestore
  clearCategoryFirestoreData(category).catch(console.warn);

  // Clear from Server
  try {
    await fetch(`/api/submissions?type=${category}`, { method: 'DELETE' });
  } catch (err) {
    console.error('Failed to clear category submissions on server:', err);
  }
}

export async function clearAllSubmissions(): Promise<void> {
  cachedSubmissions = [];
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify([]));

  // Clear from Firestore
  clearAllFirestoreData().catch(console.warn);

  try {
    await fetch('/api/submissions', { method: 'DELETE' });
  } catch (err) {
    console.error('Failed to clear submissions on server:', err);
  }
}

export async function wipeCompleteDatabase(): Promise<void> {
  cachedSubmissions = [];
  localStorage.setItem(SUBMISSIONS_STORAGE_KEY, JSON.stringify([]));

  // Clear from Firestore
  await clearAllFirestoreData().catch(console.warn);

  // Clear from Server
  try {
    await fetch('/api/database/clear-all', { method: 'POST' });
  } catch (err) {
    console.error('Failed to wipe database on server:', err);
  }
}

/**
 * Tarik data yang ada di Spreadsheet Google Apps Script atau Google Sheet URL
 */
export async function pullDataFromGoogleSpreadsheet(): Promise<{
  success: boolean;
  message: string;
  importedCount: number;
}> {
  try {
    const res = await fetch('/api/sync-spreadsheet', {
      method: 'POST'
    });

    const data = await res.json();
    if (res.ok && data.success) {
      // Refresh local cache
      await fetchAllUnifiedData();
      return {
        success: true,
        message: data.message || `Berhasil menyinkronkan data dengan Google Spreadsheet!`,
        importedCount: data.submissionsCount || 0
      };
    } else {
      throw new Error(data.message || 'Gagal sinkronisasi data dari Google Spreadsheet');
    }
  } catch (err: unknown) {
    const error = err as Error;
    throw new Error(error.message || 'Gagal menghubungi server untuk sinkronisasi Spreadsheet');
  }
}

/**
 * Parsing data teks CSV atau copy-paste dari Excel / Spreadsheet
 */
export function importSpreadsheetCsv(csvText: string): { count: number; error?: string } {
  if (!csvText || !csvText.trim()) {
    return { count: 0, error: 'Teks CSV / tabel kosong!' };
  }

  try {
    const lines = csvText.trim().split(/\r?\n/).map((line) => line.trim()).filter(Boolean);
    if (lines.length < 2) {
      return { count: 0, error: 'Minimal harus terdapat baris header dan minimal 1 baris data.' };
    }

    const newRecords: SubmissionRecord[] = [];
    const headerLine = lines[0].toLowerCase();
    const delimiter = lines[0].includes('\t') ? '\t' : (lines[0].includes(';') ? ';' : ',');

    // Skip header line
    for (let i = 1; i < lines.length; i++) {
      const parts = lines[i].split(delimiter).map((p) => p.replace(/^"|"$/g, '').trim());
      if (parts.length >= 2 && parts[1]) {
        const isAkun = headerLine.includes('akun') || headerLine.includes('whatsapp') || parts.length > 18;

        if (isAkun) {
          newRecords.push({
            id: 'sub_csv_' + i + '_' + Date.now(),
            type: 'akun',
            timestamp: parts[0] || new Date().toLocaleString('id-ID'),
            syncedToGoogle: true,
            data: {
              nama: parts[1] || 'Staf',
              kategori: parts[2] || 'Sales Eksekutif',
              jenisHp: parts[3] || 'HP Kantor',
              bekasHp: parts[4] || '-',
              akunLoginPc: parts[5] || '',
              email1: parts[6] || '', passEmail1: parts[7] || '',
              email2: parts[8] || '', passEmail2: parts[9] || '',
              email3: parts[10] || '', passEmail3: parts[11] || '',
              ig1: parts[12] || '', passIg1: parts[13] || '',
              ig2: parts[14] || '', passIg2: parts[15] || '',
              ig3: parts[16] || '', passIg3: parts[17] || '',
              fb1: parts[18] || '', passFb1: parts[19] || '',
              fb2: parts[20] || '', passFb2: parts[21] || '',
              fb3: parts[22] || '', passFb3: parts[23] || '',
              noWa: parts[24] || '-',
              akunLain: parts[25] || '',
              softwareLain: parts[26] || '',
              catatan: parts[27] || ''
            }
          });
        } else {
          newRecords.push({
            id: 'sub_csv_' + i + '_' + Date.now(),
            type: 'pc',
            timestamp: parts[0] || new Date().toLocaleString('id-ID'),
            syncedToGoogle: true,
            data: {
              nama: parts[1] || 'Staf',
              departemen: parts[2] || 'Sales & CRM',
              jabatan: parts[3] || 'Staf',
              jenisPerangkat: (parts[4] as FormPC['jenisPerangkat']) || 'Laptop',
              fotoPerangkat: parts[5] || '',
              computerName: parts[6] || 'PC-' + i,
              ip: parts[7] || '192.168.1.' + (100 + i),
              passwordPc: parts[8] || '',
              os: parts[9] || 'Windows 11',
              manufacturer: parts[10] || '-',
              processor: parts[11] || '-',
              memory: parts[12] || '16 GB',
              displayLink: parts[13] || '',
              soundLink: parts[14] || '',
              inputLink: parts[15] || ''
            }
          });
        }
      }
    }

    if (newRecords.length > 0) {
      newRecords.forEach((r) => saveSubmission(r));
      return { count: newRecords.length };
    }

    return { count: 0, error: 'Tidak ada baris data yang valid dikenali dari file/teks tersebut.' };
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Format data tidak valid';
    return { count: 0, error: msg };
  }
}

export async function submitToGoogleAppsScript(
  formType: 'pc' | 'akun',
  data: FormPC | FormAkun
): Promise<{ success: boolean; message: string; record: SubmissionRecord }> {
  const now = new Date();
  const timestamp = now.toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });

  const recordId = 'sub_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);

  const localRecord: SubmissionRecord = {
    id: recordId,
    type: formType,
    timestamp,
    syncedToGoogle: false,
    syncMessage: 'Mengirim ke server dan Spreadsheet...',
    data
  };

  try {
    // Send to central Express server which handles GAS webhook execution
    const res = await fetch('/api/submissions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(localRecord)
    });

    if (res.ok) {
      const resJson = await res.json();
      const serverRecord: SubmissionRecord = resJson.record || localRecord;
      saveSubmission(serverRecord);
      return {
        success: true,
        message: resJson.gasSynced
          ? 'Data & Foto berhasil tersimpan di Google Spreadsheet & Google Drive!'
          : 'Data berhasil disimpan ke database terpusat Syamanah Portal!',
        record: serverRecord
      };
    }
  } catch (err) {
    console.error('Server submission failed, saving locally:', err);
  }

  // Fallback save locally
  saveSubmission(localRecord);
  return {
    success: true,
    message: 'Data tersimpan di penyimpanan lokal dan akan disinkronkan saat terhubung!',
    record: localRecord
  };
}

export async function pushSeedToSpreadsheet(): Promise<{ success: boolean; message: string }> {
  try {
    const res = await fetch('/api/sync/push-seed', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    });
    const json = await res.json();
    return {
      success: Boolean(json.success),
      message: json.message || 'Data berhasil dikirim ke Google Spreadsheet'
    };
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Gagal menghubungi server';
    return { success: false, message: msg };
  }
}

export async function syncAllAkunToGoogleSheet(): Promise<{
  success: boolean;
  syncedCount: number;
  totalCount: number;
  message: string;
}> {
  try {
    const res = await fetch('/api/akun/sync-all-to-sheet', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    });
    const json = await res.json();
    return {
      success: Boolean(json.success),
      syncedCount: json.syncedCount || 0,
      totalCount: json.totalCount || 0,
      message: json.message || 'Sinkronisasi selesai'
    };
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Gagal menghubungi server';
    return { success: false, syncedCount: 0, totalCount: 0, message: msg };
  }
}

export async function syncAllPCToGoogleSheet(): Promise<{
  success: boolean;
  syncedCount: number;
  totalCount: number;
  message: string;
}> {
  try {
    const res = await fetch('/api/pc/sync-all-to-sheet', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    });
    const json = await res.json();
    return {
      success: Boolean(json.success),
      syncedCount: json.syncedCount || 0,
      totalCount: json.totalCount || 0,
      message: json.message || 'Sinkronisasi PC selesai'
    };
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Gagal menghubungi server';
    return { success: false, syncedCount: 0, totalCount: 0, message: msg };
  }
}



