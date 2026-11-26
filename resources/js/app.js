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

Alpine.start();
