import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.plugin(collapse);

Alpine.data('toasts', () => ({
    items: [],
    init() { (window.__flash || []).forEach((t) => this.push(t)); },
    push({ message, type = 'success' }) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => (this.items = this.items.filter((i) => i.id !== id)), 4500);
    },
}));

Alpine.data('slugger', (initial = '', locked = false) => ({
    slug: initial,
    locked,
    fromName(name) {
        if (this.locked) return;
        this.slug = name.toLowerCase().normalize('NFKD').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-').replace(/-+/g, '-');
    },
}));

Alpine.data('repeater', (rows = []) => ({
    rows: rows.length ? rows : [],
    add(row = {}) { this.rows.push({ _k: Date.now() + Math.random(), ...row }); },
    remove(i) { this.rows.splice(i, 1); },
}));

Alpine.data('revenueChart', (labels, revenue, orders) => ({
    init() {
        new Chart(this.$refs.canvas, {
            data: {
                labels,
                datasets: [
                    { type: 'line', label: 'Revenue', data: revenue, borderColor: '#a77c3c', backgroundColor: 'rgba(184,145,79,.12)', fill: true, tension: 0.35, pointRadius: 0, pointHoverRadius: 4, yAxisID: 'y' },
                    { type: 'bar', label: 'Orders', data: orders, backgroundColor: 'rgba(14,13,12,.14)', borderRadius: 6, yAxisID: 'y1' },
                ],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8, color: '#6b7178' } },
                    y: { grid: { color: '#efece6' }, ticks: { color: '#6b7178' }, beginAtZero: true },
                    y1: { position: 'right', grid: { display: false }, ticks: { color: '#6b7178', precision: 0 }, beginAtZero: true },
                },
            },
        });
    },
}));

Alpine.start();
