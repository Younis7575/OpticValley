/**
 * Virtual Try-On — runs entirely in the browser. Camera frames and face
 * landmarks never leave the device; nothing is uploaded or stored.
 */
const MODEL_URL = 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task';
let landmarkerPromise = null;

async function getLandmarker(mode) {
    if (!landmarkerPromise) {
        landmarkerPromise = (async () => {
            const { FaceLandmarker, FilesetResolver } = await import('@mediapipe/tasks-vision');
            const fileset = await FilesetResolver.forVisionTasks('/mediapipe/wasm');
            return FaceLandmarker.createFromOptions(fileset, {
                baseOptions: { modelAssetPath: MODEL_URL, delegate: 'GPU' },
                runningMode: 'VIDEO', numFaces: 1,
            });
        })().catch((e) => { landmarkerPromise = null; throw e; });
    }
    const lm = await landmarkerPromise;
    await lm.setOptions({ runningMode: mode });
    return lm;
}

const imgCache = new Map();
function loadImg(src) {
    if (!imgCache.has(src)) {
        imgCache.set(src, new Promise((res, rej) => { const i = new Image(); i.decoding = 'async'; i.onload = () => res(i); i.onerror = rej; i.src = src; }));
    }
    return imgCache.get(src);
}

/**
 * Draws the frame overlay. Uses the outer eye corners (33/263) and eye centres
 * (iris 468/473 when available, else eye-corner midpoints) for position and roll,
 * and the face edges (234/454) for width. Works for mirrored (selfie) and
 * non-mirrored video because left/right are decided on screen, not by landmark id.
 */
export function placement(lm, W, H, mirror, frame, imgW, imgH) {
    const P = (i) => ({ x: (mirror ? 1 - lm[i].x : lm[i].x) * W, y: lm[i].y * H });
    const mid = (a, b) => ({ x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 });
    let a = P(33), b = P(263);                      // outer eye corners
    let ca = lm[468] ? P(468) : mid(P(33), P(133));  // eye centres
    let cb = lm[473] ? P(473) : mid(P(263), P(362));
    if (a.x > b.x) { [a, b] = [b, a]; [ca, cb] = [cb, ca]; } // a = screen-left eye
    const faceL = P(234), faceR = P(454);
    const faceW = Math.hypot(faceR.x - faceL.x, faceR.y - faceL.y);
    const angle = Math.atan2(b.y - a.y, b.x - a.x);   // always within ±90° → never upside down
    const centre = mid(ca, cb);
    const w = faceW * 0.98 * (frame.scale || 1);
    const h = w * (imgH / imgW);
    // lens centres sit ~45% down the overlay image
    const ox = (frame.dx || 0) * w, oy = h * (0.05 + (frame.dy || 0));
    return { x: centre.x + Math.cos(angle) * ox - Math.sin(angle) * oy, y: centre.y + Math.sin(angle) * ox + Math.cos(angle) * oy, angle, w, h };
}

function drawGlasses(ctx, lm, img, frame, W, H, mirror) {
    const p = placement(lm, W, H, mirror, frame, img.naturalWidth, img.naturalHeight);
    ctx.save();
    ctx.translate(p.x, p.y);
    ctx.rotate(p.angle);
    ctx.drawImage(img, -p.w / 2, -p.h / 2, p.w, p.h);
    ctx.restore();
}

export function createTryOn({ canvas, video, frames, onStatus }) {
    const ctx = canvas.getContext('2d');
    let stream = null, raf = 0, frame = frames[0], img = null, lastSeen = 0, running = false, photo = null;

    const status = (s, msg = '') => onStatus && onStatus(s, msg);

    async function setFrame(f) { frame = f; img = await loadImg(f.overlay); if (photo) renderPhoto(); }

    async function start(facingMode = 'user') {
        stop();
        if (!navigator.mediaDevices?.getUserMedia) { status('error', 'Your browser does not support camera access. Try uploading a photo instead.'); return; }
        status('loading', 'Starting camera…');
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false });
        } catch (e) {
            const msg = e.name === 'NotAllowedError' ? 'Camera access is blocked. Tap the lock icon next to the address bar → Permissions → Camera → Allow, then try again — or upload a photo instead.'
                : e.name === 'NotFoundError' ? 'No camera was found on this device. You can upload a photo instead.'
                : 'The camera could not be started. Close other apps using it, or upload a photo instead.';
            status('error', msg); return;
        }
        video.srcObject = stream; await video.play();
        status('loading', 'Loading face tracking…');
        let lm;
        try { [lm, img] = await Promise.all([getLandmarker('VIDEO'), loadImg(frame.overlay)]); }
        catch { status('error', 'Face tracking could not be loaded. Check your connection and try again.'); stop(); return; }
        photo = null; running = true; status('live');
        const mirror = facingMode === 'user';
        const loop = () => {
            if (!running) return;
            const W = canvas.width = video.videoWidth, H = canvas.height = video.videoHeight;
            ctx.save();
            if (mirror) { ctx.translate(W, 0); ctx.scale(-1, 1); }
            ctx.drawImage(video, 0, 0, W, H);
            ctx.restore();
            const res = lm.detectForVideo(video, performance.now());
            if (res.faceLandmarks?.length) { lastSeen = performance.now(); drawGlasses(ctx, res.faceLandmarks[0], img, frame, W, H, mirror); status('live'); }
            else if (performance.now() - lastSeen > 1500) status('noface', 'Face not detected — look at the camera in good, even light.');
            raf = requestAnimationFrame(loop);
        };
        loop();
    }

    async function renderPhoto() {
        const lm = await getLandmarker('IMAGE');
        img = await loadImg(frame.overlay);
        const W = canvas.width = photo.naturalWidth, H = canvas.height = photo.naturalHeight;
        ctx.drawImage(photo, 0, 0, W, H);
        const res = lm.detect(photo);
        if (res.faceLandmarks?.length) { drawGlasses(ctx, res.faceLandmarks[0], img, frame, W, H, false); status('photo'); }
        else status('noface', 'We could not find a face in this photo. Try a clear, front-facing photo.');
    }

    async function usePhoto(file) {
        stop();
        if (!file || !/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 8 * 1024 * 1024) { status('error', 'Please choose a JPG, PNG or WebP photo under 8 MB.'); return; }
        status('loading', 'Analysing photo on your device…');
        const url = URL.createObjectURL(file);
        try { photo = await loadImg(url); await renderPhoto(); }
        catch { status('error', 'Face tracking could not be loaded. Check your connection and try again.'); }
        finally { imgCache.delete(url); URL.revokeObjectURL(url); }
    }

    function snapshot() { return canvas.toDataURL('image/jpeg', 0.92); }

    function stop() {
        running = false; cancelAnimationFrame(raf);
        if (stream) { stream.getTracks().forEach((t) => t.stop()); stream = null; }
        video.srcObject = null;
    }

    return { start, stop, setFrame, usePhoto, snapshot };
}
