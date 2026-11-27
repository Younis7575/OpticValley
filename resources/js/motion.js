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
        gsap.from(el.querySelectorAll('.gw'), { yPercent: 110, duration: 0.9, ease: 'power3.out', stagger: 0.06, scrollTrigger: { trigger: el, start: 'top 85%' } });
    });
    // Staggered card groups
    document.querySelectorAll('[data-gsap="stagger"]').forEach((group) => {
        gsap.from(group.children, { y: 36, opacity: 0, duration: 0.8, ease: 'power3.out', stagger: 0.08, scrollTrigger: { trigger: group, start: 'top 85%' } });
    });
    // Image clip reveals
    document.querySelectorAll('[data-gsap="clip"]').forEach((el) => {
        gsap.fromTo(el, { clipPath: 'inset(12% 12% 12% 12% round 2rem)' }, { clipPath: 'inset(0% 0% 0% 0% round 2rem)', ease: 'none', scrollTrigger: { trigger: el, start: 'top 90%', end: 'top 30%', scrub: true } });
    });
    // Gentle parallax
    document.querySelectorAll('[data-gsap="parallax"]').forEach((el) => {
        gsap.to(el, { yPercent: -12, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true } });
    });
}
