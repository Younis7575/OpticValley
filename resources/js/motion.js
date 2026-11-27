import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initMotion() {
    gsap.registerPlugin(ScrollTrigger);
    // Headline word reveals
    document.querySelectorAll('[data-gsap="words"]').forEach((el) => {
        if (el.dataset.split) return;
        el.dataset.split = '1';
        const words = el.innerHTML.split(/(\s+|<[^>]+>)/).map((w) => (w.trim() && !w.startsWith('<') ? `<span class="inline-block overflow-hidden align-bottom" style="padding-bottom:.14em;margin-bottom:-.14em"><span class="gw inline-block">${w}</span></span>` : w)).join('');
        el.innerHTML = words;
        // CSS-driven so the text always ends visible, even if animation frames are throttled.
        el.querySelectorAll('.gw').forEach((w, i) => { w.style.transitionDelay = `${i * 60}ms`; });
        el.classList.add('gwords');
        const io = new IntersectionObserver(([e]) => { if (e.isIntersecting) { el.classList.add('is-in'); io.disconnect(); } }, { rootMargin: '0px 0px -8% 0px' });
        io.observe(el);
    });
    // Staggered card groups
    document.querySelectorAll('[data-gsap="stagger"]').forEach((group) => {
        gsap.from(group.children, { y: 32, duration: 0.8, ease: 'power3.out', stagger: 0.08, scrollTrigger: { trigger: group, start: 'top 92%', once: true } });
    });
    // Image clip reveals
    document.querySelectorAll('[data-gsap="clip"]').forEach((el) => {
        gsap.fromTo(el, { clipPath: 'inset(12% 12% 12% 12% round 2rem)' }, { clipPath: 'inset(0% 0% 0% 0% round 2rem)', ease: 'none', scrollTrigger: { trigger: el, start: 'top 90%', end: 'top 30%', scrub: true } });
    });
    // Safety net: never leave content hidden if a trigger was skipped (fast scroll, throttled tab, late layout).
    const reveal = () => ScrollTrigger.getAll().forEach((st) => {
        if (st.animation && st.vars.scrub === undefined && st.start <= window.scrollY + window.innerHeight) st.animation.progress(1);
    });
    window.addEventListener('load', () => { ScrollTrigger.refresh(); setTimeout(reveal, 1200); });
    let t; window.addEventListener('scroll', () => { clearTimeout(t); t = setTimeout(reveal, 700); }, { passive: true });

    // Gentle parallax
    document.querySelectorAll('[data-gsap="parallax"]').forEach((el) => {
        gsap.to(el, { yPercent: -12, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true } });
    });
}
