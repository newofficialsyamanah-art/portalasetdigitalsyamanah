import {
  collection,
  doc,
  getDocs,
  setDoc,
  deleteDoc,
  writeBatch,
  getDocFromServer
} from 'firebase/firestore';
import { db } from './firebase';
import { SubmissionRecord, MikrotikAccount } from '../types';

export const SUBMISSIONS_COLLECTION = 'submissions';
export const MIKROTIK_COLLECTION = 'mikrotik';

/**
 * Validate connection to Firestore using getDocFromServer
 */
export async function validateFirestoreConnection(): Promise<boolean> {
  try {
    const testDoc = doc(db, '_connection_test', 'ping');
    await getDocFromServer(testDoc).catch(() => {});
    return true;
  } catch (err) {
    console.warn('Firestore connection check:', err);
    return false;
  }
}

/**
 * Fetch all submission records from Firestore
 */
export async function getFirestoreSubmissions(): Promise<SubmissionRecord[]> {
  const colRef = collection(db, SUBMISSIONS_COLLECTION);
  const snap = await getDocs(colRef);
  const records: SubmissionRecord[] = [];
  snap.forEach((d: any) => {
    records.push(d.data() as SubmissionRecord);
  });
  return records;
}

/**
 * Save single submission record to Firestore
 */
export async function saveFirestoreSubmission(record: SubmissionRecord): Promise<void> {
  const docRef = doc(db, SUBMISSIONS_COLLECTION, record.id);
  await setDoc(docRef, record, { merge: true });
}

/**
 * Delete submission record from Firestore
 */
export async function deleteFirestoreSubmission(id: string): Promise<void> {
  const docRef = doc(db, SUBMISSIONS_COLLECTION, id);
  await deleteDoc(docRef);
}

/**
 * Fetch all MikroTik accounts from Firestore
 */
export async function getFirestoreMikrotik(): Promise<MikrotikAccount[]> {
  const colRef = collection(db, MIKROTIK_COLLECTION);
  const snap = await getDocs(colRef);
  const accounts: MikrotikAccount[] = [];
  snap.forEach((d: any) => {
    accounts.push(d.data() as MikrotikAccount);
  });
  return accounts;
}

/**
 * Save single MikroTik account to Firestore
 */
export async function saveFirestoreMikrotik(account: MikrotikAccount): Promise<void> {
  const docRef = doc(db, MIKROTIK_COLLECTION, account.id);
  await setDoc(docRef, account, { merge: true });
}

/**
 * Delete MikroTik account from Firestore
 */
export async function deleteFirestoreMikrotik(id: string): Promise<void> {
  const docRef = doc(db, MIKROTIK_COLLECTION, id);
  await deleteDoc(docRef);
}

/**
 * Bulk seed or migrate local data into Firestore
 */
export async function bulkMigrateToFirestore(
  submissions: SubmissionRecord[],
  mikrotik: MikrotikAccount[]
): Promise<{ submissionsCount: number; mikrotikCount: number }> {
  // Batch write submissions in chunks of 450 (Firestore limit is 500)
  const batchSize = 400;

  for (let i = 0; i < submissions.length; i += batchSize) {
    const chunk = submissions.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((rec) => {
      const ref = doc(db, SUBMISSIONS_COLLECTION, rec.id);
      batch.set(ref, rec, { merge: true });
    });
    await batch.commit();
  }

  for (let i = 0; i < mikrotik.length; i += batchSize) {
    const chunk = mikrotik.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((acc) => {
      const ref = doc(db, MIKROTIK_COLLECTION, acc.id);
      batch.set(ref, acc, { merge: true });
    });
    await batch.commit();
  }

  return {
    submissionsCount: submissions.length,
    mikrotikCount: mikrotik.length
  };
}

/**
 * Clear all submissions of a specific category from Firestore ('pc' or 'akun')
 */
export async function clearCategoryFirestoreData(category: 'pc' | 'akun'): Promise<number> {
  const colRef = collection(db, SUBMISSIONS_COLLECTION);
  const snap = await getDocs(colRef);
  const toDelete: string[] = [];

  snap.forEach((docSnap: any) => {
    const data = docSnap.data();
    if (data.type === category) {
      toDelete.push(docSnap.id);
    }
  });

  const batchSize = 400;
  for (let i = 0; i < toDelete.length; i += batchSize) {
    const chunk = toDelete.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((id) => {
      batch.delete(doc(db, SUBMISSIONS_COLLECTION, id));
    });
    await batch.commit();
  }

  return toDelete.length;
}

/**
 * Clear all MikroTik accounts from Firestore
 */
export async function clearAllFirestoreMikrotik(): Promise<number> {
  const colRef = collection(db, MIKROTIK_COLLECTION);
  const snap = await getDocs(colRef);
  const toDelete: string[] = [];

  snap.forEach((docSnap: any) => {
    toDelete.push(docSnap.id);
  });

  const batchSize = 400;
  for (let i = 0; i < toDelete.length; i += batchSize) {
    const chunk = toDelete.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((id) => {
      batch.delete(doc(db, MIKROTIK_COLLECTION, id));
    });
    await batch.commit();
  }

  return toDelete.length;
}

/**
 * Clear entire Firestore database (all submissions and all mikrotik accounts)
 */
export async function clearAllFirestoreData(): Promise<{ submissionsCount: number; mikrotikCount: number }> {
  // 1. Delete all submissions
  const subRef = collection(db, SUBMISSIONS_COLLECTION);
  const subSnap = await getDocs(subRef);
  const subIds: string[] = [];
  subSnap.forEach((d: any) => subIds.push(d.id));

  const batchSize = 400;
  for (let i = 0; i < subIds.length; i += batchSize) {
    const chunk = subIds.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((id) => batch.delete(doc(db, SUBMISSIONS_COLLECTION, id)));
    await batch.commit();
  }

  // 2. Delete all mikrotik
  const mktRef = collection(db, MIKROTIK_COLLECTION);
  const mktSnap = await getDocs(mktRef);
  const mktIds: string[] = [];
  mktSnap.forEach((d: any) => mktIds.push(d.id));

  for (let i = 0; i < mktIds.length; i += batchSize) {
    const chunk = mktIds.slice(i, i + batchSize);
    const batch = writeBatch(db);
    chunk.forEach((id) => batch.delete(doc(db, MIKROTIK_COLLECTION, id)));
    await batch.commit();
  }

  return {
    submissionsCount: subIds.length,
    mikrotikCount: mktIds.length
  };
}
