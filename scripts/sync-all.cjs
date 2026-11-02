const fs = require('fs');
const url = 'https://script.google.com/macros/s/AKfycbzalkabRunlYtF3eDqZeBI_v_UpOoYSJTHRUUJGn4G_LrUqaSP9NuaSTBipOSN8pttb/exec';

async function pushItem(item, type) {
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'text/plain;charset=utf-8' },
      body: JSON.stringify({
        action: 'create',
        formType: type,
        id: item.id,
        timestamp: item.timestamp,
        data: item.data
      })
    });
    const json = await res.json();
    return json.status === 'success' || !json.error;
  } catch (err) {
    console.error(`Failed to push ${type} ${item.id}:`, err.message);
    return false;
  }
}

async function run() {
  const db = JSON.parse(fs.readFileSync('.data/db.json', 'utf8'));
  console.log('Fetching current sheet data...');
  const res = await fetch(url);
  const data = await res.json();

  const sheetPcIds = new Set((data.pc || []).map(p => p.id));
  const sheetAkunIds = new Set((data.akun || []).map(a => a.id));

  const missingPc = db.submissions.filter(s => s.type === 'pc' && !sheetPcIds.has(s.id));
  const missingAkun = db.submissions.filter(s => s.type === 'akun' && !sheetAkunIds.has(s.id));

  console.log(`Pushing ${missingPc.length} missing PC records...`);
  for (let i = 0; i < missingPc.length; i += 4) {
    const batch = missingPc.slice(i, i + 4);
    await Promise.all(batch.map(item => pushItem(item, 'pc')));
    console.log(`PC Progress: ${Math.min(i + 4, missingPc.length)}/${missingPc.length}`);
    await new Promise(r => setTimeout(r, 400));
  }

  console.log(`Pushing ${missingAkun.length} missing Akun records...`);
  for (let i = 0; i < missingAkun.length; i += 4) {
    const batch = missingAkun.slice(i, i + 4);
    await Promise.all(batch.map(item => pushItem(item, 'akun')));
    console.log(`Akun Progress: ${Math.min(i + 4, missingAkun.length)}/${missingAkun.length}`);
    await new Promise(r => setTimeout(r, 400));
  }

  // Update local mikrotik from sheet
  if (Array.isArray(data.mikrotik) && data.mikrotik.length > 0) {
    console.log(`Syncing ${data.mikrotik.length} Mikrotik accounts from sheet to local DB...`);
    const sheetMap = new Map();
    data.mikrotik.forEach(m => {
      if (m.username) sheetMap.set(m.username.toUpperCase(), m);
    });
    db.mikrotik.forEach(m => {
      if (m.username && !sheetMap.has(m.username.toUpperCase())) {
        sheetMap.set(m.username.toUpperCase(), m);
      }
    });
    db.mikrotik = Array.from(sheetMap.values());
    fs.writeFileSync('.data/db.json', JSON.stringify(db, null, 2));
  }

  console.log('Verifying final counts in Google Sheet...');
  const verifyRes = await fetch(url);
  const verifyData = await verifyRes.json();
  console.log('Final Sheet Counts:');
  console.log('- PC:', verifyData.pc?.length);
  console.log('- Akun:', verifyData.akun?.length);
  console.log('- Mikrotik:', verifyData.mikrotik?.length);
}

run().catch(console.error);
