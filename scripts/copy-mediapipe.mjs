// Copies the MediaPipe Tasks Vision WASM runtime into public/ so it is served
// from our own domain (no third-party CDN needed for the runtime).
import { cpSync, mkdirSync } from 'node:fs';
mkdirSync('public/mediapipe', { recursive: true });
cpSync('node_modules/@mediapipe/tasks-vision/wasm', 'public/mediapipe/wasm', { recursive: true });
console.log('MediaPipe WASM copied to public/mediapipe/wasm');
