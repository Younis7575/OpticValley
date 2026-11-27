# OpticValley 2.0 — Implementation Plan

Source brief: `OpticValley_2.0_Claude_Master_Prompt.md` (26 Nov 2026).

## 1. Audit summary (current state)

| Area | Finding |
|---|---|
| Architecture | Laravel 13 monolith, Blade + Tailwind CSS 4 + Alpine.js, built with Vite. MySQL in production (Hostinger Premium shared hosting), SQLite locally/tests. |
| Why not Next.js | Hostinger Premium cannot run a Node.js server; the owner chose "Full Laravel". The brief allows preserving a sound working architecture, so 3D/try-on are built as lazily-loaded Vite modules (vanilla Three.js instead of React Three Fiber). |
| Catalogue | 28 real products: 20 Marie Flower (men/kids, top-down photos) and 8 Baby Grip kids frames (front-facing photos). Categories nested; brands; colour variants; lens options with prices. |
| Commerce | Session cart with server-side pricing, coupons, free-shipping threshold, tax, COD / bank transfer, transactional checkout with row locks, order history/tracking, wishlist, reviews (moderated), newsletter, contact form. |
| Prescription | Lens selection, SPH/CYL/AXIS/ADD/PD entry or private upload (`storage/app/private`), admin-only download. |
| Admin | Dashboard analytics, products/variants/images, categories, brands, lenses, orders (status, refunds, invoice, CSV), customers, coupons, banners, pages, blog, menus, reviews, messages, settings, roles, activity log. |
| SEO | Meta/OG, canonical, Organization/Product/Breadcrumb/Article JSON-LD, sitemap.xml, robots.txt. |
| Deploy | GitHub Actions → tests → build → SSH release → migrate → cache → symlink switch → health check / rollback. |
| Gaps vs brief | No 3D hero, no lens shader section, no virtual try-on, no GSAP, no quick view, no search autocomplete, no filter chips, no recently viewed, no analytics events, no face-shape guide. |
| Duplication audit | Homepage "Featured / Best sellers / New" tabs draw from different flags; overlap is genuine merchandising, not duplicated data. Kids carousel repeats kids items shown elsewhere — kept as a deliberate collection row. |

## 2. Checklist

- [x] Audit and plan (this file)
- [x] Design tokens aligned to brief palette (ivory surfaces, champagne accent, restrained gold); page background stays pure white per owner request
- [x] Three.js hero: representative procedural frame (clearly labelled, not a product), studio lighting, pointer tilt, idle rotation, sparse champagne particles, pause off-screen, reduced-motion + WebGL fallback poster
- [x] Lens experience: ShaderMaterial refraction/caustics over brand imagery, GPU Points particles reacting to pointer, static fallback on low-power/mobile
- [x] Virtual Try-On: MediaPipe Tasks Vision Face Landmarker, local processing only, explicit consent before camera, per-product overlay + calibration (scale, vertical offset, horizontal offset), photo upload fallback, error states, admin fields, "not available" + compatible suggestions
- [x] Homepage order per brief §9; GSAP ScrollTrigger reveals with reduced-motion support
- [x] Face-shape guide (style guidance, not medical advice) linking to real filtered collections
- [x] Quick view, search autocomplete, filter chips + clear all, recently viewed (localStorage), availability on cards
- [x] Analytics event layer (GA4 optional via admin setting); never sends prescription/camera data
- [x] Tests, browser QA (desktop/mobile), deploy, live verification

## 3. Required real assets (owner)

- Front-facing, background-free photos per frame (Marie Flower currently top-down → try-on not available for them).
- Optional authentic 3D models (GLB) per frame for a true product 3D viewer.
- Store photography showing the real OpticValley signage.
- GA4 Measurement ID (optional), payment gateway credentials (optional).

## 4. Delivery notes

- 3D hero and lens experience are separate lazy chunks (`resources/js/three/*`), mounted only when visible, on WebGL-capable, non-low-power devices; poster fallback otherwise. Rendering pauses off-screen/hidden; resources disposed on cleanup.
- Hero model is procedural and labelled "Representative 3D render" — not a purchasable product.
- Virtual Try-On: `resources/js/tryon/tryon.js` + `store/partials/tryon-modal.blade.php`. MediaPipe WASM is self-hosted (`npm run build` copies it to `public/mediapipe/wasm`); the face model is fetched from Google's MediaPipe CDN. Camera starts only after the customer presses Start. No frames/landmarks leave the browser.
- Enabled for the 8 Baby Grip frames (front-view overlays). Marie Flower frames show "not available" with compatible alternatives until front-view overlays are uploaded in Admin → Products → Virtual Try-On.
- Admin: try-on toggle, overlay upload (PNG/WebP ≤ 3 MB), scale/offset calibration; GA4 Measurement ID under Settings → SEO & social.
- Analytics events (only when GA4 configured): view_item, add_to_cart, search, tryon_open. Never prescription or camera data.
