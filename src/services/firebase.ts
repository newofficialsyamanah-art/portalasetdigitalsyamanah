import { initializeApp, getApps, getApp } from 'firebase/app';
import { initializeFirestore } from 'firebase/firestore';
import firebaseConfig from '../../firebase-applet-config.json' with { type: 'json' };

// Initialize Firebase App
const app = !getApps().length ? initializeApp(firebaseConfig) : getApp();

// Initialize Firestore targeting the specific provisioned database
export const db = initializeFirestore(app, {}, firebaseConfig.firestoreDatabaseId || '(default)');

export default app;
