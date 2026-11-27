import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(collapse);

// Reveal-on-scroll for elements with .reveal
const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
        if (e.isIntersecting) {
            e.target.classList.add('is-in');
            io.unobserve(e.target);
        }
    });
}, { rootMargin: '0px 0px -8% 0px' });
document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('.reveal').forEach((el) => io.observe(el)));

Alpine.store('cart', { count: Number(document.querySelector('meta[name=cart-count]')?.content || 0) });

Alpine.data('toasts', () => ({
    items: [],
    init() {
        window.addEventListener('toast', (e) => this.push(e.detail));
        (window.__flash || []).forEach((t) => this.push(t));
    },
    push({ message, type = 'success' }) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => (this.items = this.items.filter((i) => i.id !== id)), 4200);
    },
}));

window.toast = (message, type = 'success') => window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));

const csrf = () => document.querySelector('meta[name=csrf-token]').content;

window.ovFetch = async (url, { method = 'POST', body = null } = {}) => {
    const opts = { method, headers: { 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' } };
    if (body instanceof FormData) opts.body = body;
    else if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const msg = data.message || Object.values(data.errors || {})[0]?.[0] || 'Something went wrong.';
        throw new Error(msg);
    }
    return data;
};

// Quick add from product cards
Alpine.data('quickAdd', (url) => ({
    busy: false,
    async add() {
        this.busy = true;
        try {
            const d = await ovFetch(url, { body: { qty: 1 } });
            Alpine.store('cart').count = d.count;
            toast(d.message);
        } catch (e) { toast(e.message, 'error'); }
        this.busy = false;
    },
}));

Alpine.data('wishlistToggle', (url, initial, loggedIn) => ({
    on: initial,
    async toggle() {
        if (!loggedIn) { window.location = '/login?intended=' + encodeURIComponent(location.pathname); return; }
        try {
            const d = await ovFetch(url);
            this.on = d.in_wishlist;
            toast(d.message);
        } catch (e) { toast(e.message, 'error'); }
    },
}));

Alpine.data('slider', (count, interval = 6500) => ({
    i: 0,
    count,
    timer: null,
    init() { this.play(); },
    play() { if (this.count > 1) this.timer = setInterval(() => this.next(), interval); },
    stop() { clearInterval(this.timer); },
    next() { this.i = (this.i + 1) % this.count; },
    prev() { this.i = (this.i - 1 + this.count) % this.count; },
    go(n) { this.stop(); this.i = n; this.play(); },
}));


// ---- Virtual Try-On (engine loaded only when the customer opens it) ----
Alpine.data('tryOnModal', (frames) => ({
    frames, frame: frames[0], show: false, state: 'intro', message: '', engine: null, facing: 'user', lastFocus: null,
    async ensure() {
        if (this.engine) return this.engine;
        const { createTryOn } = await import('./tryon/tryon.js');
        this.engine = createTryOn({ canvas: this.$refs.canvas, video: this.$refs.video, frames: this.frames, onStatus: (s, m) => { this.state = s; this.message = m; } });
        await this.engine.setFrame(this.frame);
        return this.engine;
    },
    open(id) {
        this.lastFocus = document.activeElement;
        if (id) this.frame = this.frames.find((f) => f.id === id) || this.frame;
        this.show = true; this.state = 'intro'; document.body.style.overflow = 'hidden';
        window.ovTrack?.('tryon_open', { item_id: this.frame?.id });
    },
    async startCamera() { const e = await this.ensure(); this.facing = 'user'; e.start(this.facing); },
    async flip() { const e = await this.ensure(); this.facing = this.facing === 'user' ? 'environment' : 'user'; e.start(this.facing); },
    async usePhoto(file) { const e = await this.ensure(); e.usePhoto(file); },
    async pick(f) { this.frame = f; if (this.engine) await this.engine.setFrame(f); },
    save() { if (!this.engine) return; const a = document.createElement('a'); a.href = this.engine.snapshot(); a.download = 'opticvalley-try-on.jpg'; a.click(); },
    reset() { this.engine?.stop(); this.state = 'intro'; },
    close() { this.engine?.stop(); this.show = false; this.state = 'intro'; document.body.style.overflow = ''; this.lastFocus?.focus?.(); },
}));

// ---- Analytics event layer (GA4 only if configured; never sends prescription or camera data) ----
window.dataLayer = window.dataLayer || [];
window.ovTrack = (event, params = {}) => {
    window.dataLayer.push({ event, ...params });
    if (typeof window.gtag === 'function') window.gtag('event', event, params);
};

// ---- Quick view ----
Alpine.data('quickView', () => ({
    show: false, html: '', loading: false,
    async open(url) {
        this.show = true; this.loading = true; this.html = ''; document.body.style.overflow = 'hidden';
        try { const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }); this.html = await r.text(); }
        catch { this.html = '<p class="p-10 text-center text-sm">Could not load this product. Please try again.</p>'; }
        this.loading = false;
    },
    close() { this.show = false; document.body.style.overflow = ''; },
}));

// ---- Search suggestions ----
Alpine.data('searchSuggest', () => ({
    q: '', items: [], timer: null,
    fetch() {
        clearTimeout(this.timer);
        if (this.q.trim().length < 2) { this.items = []; return; }
        this.timer = setTimeout(async () => {
            try { const r = await fetch('/search/suggest?q=' + encodeURIComponent(this.q.trim())); this.items = (await r.json()).data || []; } catch { this.items = []; }
        }, 180);
    },
}));

// ---- Recently viewed (stored only in this browser) ----
window.ovRecent = {
    key: 'ov.recent',
    read() { try { return JSON.parse(localStorage.getItem(this.key) || '[]'); } catch { return []; } },
    push(item) { try { const list = this.read().filter((i) => i.url !== item.url); list.unshift(item); localStorage.setItem(this.key, JSON.stringify(list.slice(0, 8))); } catch {} },
};

// ---- Signature 3D / lens experiences: lazy, capability-checked ----
function webglOK() { try { const c = document.createElement('canvas'); return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl'))); } catch { return false; } }
const lowPower = () => (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2) || navigator.connection?.saveData;
function lazyMount(selector, loader) {
    document.querySelectorAll(selector).forEach((el) => {
        if (!webglOK() || lowPower()) return; // poster fallback stays visible
        const io = new IntersectionObserver(async ([e]) => {
            if (!e.isIntersecting) return; io.disconnect();
            try { const mod = await loader(); mod(el); } catch (err) { console.warn('3D experience unavailable', err); }
        }, { rootMargin: '200px' });
        io.observe(el);
    });
}
lazyMount('[data-hero-3d]', async () => (await import('./three/hero.js')).mountHero);
lazyMount('[data-lens-3d]', async () => (await import('./three/lens.js')).mountLens);

// ---- GSAP scroll choreography (respects reduced motion) ----
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && document.querySelector('[data-gsap]')) {
    import('./motion.js').then((m) => m.initMotion()).catch(() => {});
}

Alpine.start();
