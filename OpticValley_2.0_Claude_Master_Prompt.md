# OpticValley 2.0 — Ultra-Premium Eyewear E-commerce Transformation
## Master Implementation Prompt for Claude AI

You are acting as a **principal product designer, senior Next.js engineer, Three.js/WebGL specialist, Laravel integration engineer, e-commerce architect, and QA lead**.

Your task is to audit, redesign, and implement the existing OpticValley project into an **ultra-premium, modern, commercially effective eyewear e-commerce platform**.

Website: https://opticvalley.co/


## Live website review and tailored direction (reviewed 26 November 2026)

Before implementation, compare the current live site with this prompt. The homepage currently presents a functioning eyewear store with eyeglasses, sunglasses, kids frames, blue-light glasses, contact lenses, accessories, sale, product cards, frame-shape browsing, prescription/lens selection, prescription upload, delivery/COD/returns information, WhatsApp support, and a physical store in Gulberg III, Lahore.

### Preserve these existing strengths

- Keep the current product catalog, real product prices, product categories, product selection, prescription flow, store details, delivery/COD messaging, and existing policies.
- Preserve the current customer journey from selecting a frame to choosing lenses and entering/uploading prescription details.
- Retain useful trust and service content such as optician verification, nationwide delivery, returns, WhatsApp support, and the physical store—only when those claims remain accurate.
- Audit the repeated featured product rows/cards visible on the homepage. Confirm whether repeated items are intentional carousel looping or duplicated merchandising data; fix genuine duplication without removing valid products.
- Keep the site’s real product data as the source of truth. Do not replace current product cards with fake luxury products.

### Specific opportunity for OpticValley

The current homepage already has useful commerce and trust content. The transformation should focus on making the first impression and product discovery feel more distinctive, editorial, and premium—not adding visual effects to every section.

Recommended direction:

1. **Signature hero:** Create one memorable, full-width campaign scene with an authentic OpticValley frame, cinematic studio lighting, restrained motion, and a clear “Shop Eyeglasses” / “Shop Sunglasses” path.
2. **Editorial product storytelling:** Use larger product photography, carefully composed whitespace, concise copy, and fewer but more intentional campaign sections.
3. **Frame discovery:** Improve the existing frame-shape/category discovery with elegant visual tiles and useful filters; connect every tile to real products.
4. **Optical lens story:** Add a lens/refraction interaction as a brand storytelling section, while keeping actual lens features and prices grounded in existing backend data.
5. **Virtual Try-On:** Treat this as a real feature distinct from the physical Gulberg store experience. Add it only when product-specific overlays/models and face tracking are functional.
6. **Trust-led conversion:** Keep delivery, COD, returns, prescription verification, and WhatsApp support easy to find. Premium styling must not hide practical buying information.
7. **Mobile-first performance:** Make the 3D hero optional or simplified on mobile and keep category, product, prescription, cart, and checkout flows fast.

### Recommended visual balance

- 1 signature Three.js hero.
- 1 interactive optical lens section.
- 1 restrained particle treatment used in selected sections only.
- GSAP for purposeful transitions and scroll reveals.
- Static/video poster fallbacks for devices where WebGL is slow or unavailable.

Do not use Three.js, particles, or scroll animation as decoration on every section. The goal is an elegant eyewear brand with a few unforgettable interactions—not a heavy technology showcase.


The goal is not to create a visual demo or a disconnected landing page. Build a production-ready, fully responsive, accessible, fast, and commercially functional platform while preserving and improving existing business functionality.

**Work autonomously. Do not stop for approval after the audit or after presenting a plan.** Inspect the project, make sensible decisions, implement the work, test it, and resolve issues. Ask a question only if a genuinely blocking credential, business decision, or missing asset makes implementation impossible. Never fabricate integrations, product data, customer reviews, payment success, or backend capabilities.

---

# 1. Core objectives

Transform OpticValley into a digital luxury eyewear brand with:

1. A cinematic **Three.js 3D floating-eyewear hero**.
2. An immersive **interactive optical lens / refraction experience** using shaders and particles.
3. A practical **Virtual Try-On** experience that lets customers preview real OpticValley frames on their own face.
4. Premium editorial e-commerce design and product merchandising.
5. Advanced product discovery, filtering, comparison, and product details.
6. A polished prescription and lens-selection journey.
7. A seamless mobile shopping experience.
8. Strong SEO, accessibility, performance, security, and conversion optimization.
9. Fully working integration with the existing backend, database, admin panel, and business workflows.

The experience should feel like a premium international eyewear brand—not a generic template, gaming website, or flashy technology demo.

---

# 2. Mandatory project audit — before changing code

First inspect the entire existing repository and document the current architecture.

Audit:

- Frontend framework, routes, components, styling, and build setup.
- Backend framework, APIs, authentication, and business logic.
- Database schema, migrations, product models, categories, variants, prices, inventory, orders, customers, and reviews.
- Admin panel and content-management workflows.
- Product images, logos, fonts, icons, and other available assets.
- Existing search, filters, wishlist, cart, checkout, prescription upload, payment, delivery, and customer-account flows.
- Existing SEO metadata, structured data, analytics, and performance.
- Current deployment configuration, environment variables, and integrations.

Create a concise implementation checklist in the repository (for example, `IMPLEMENTATION_PLAN.md`) and then **continue directly into implementation**. Do not wait for approval.

## Existing target architecture

Use this architecture unless the repository contains a sound, already-working implementation that should be preserved:

- **Frontend:** Next.js with JavaScript (not TypeScript unless the existing project already uses TypeScript).
- **Backend:** Laravel.
- **Database:** Existing SQL database and schema where practical.
- **3D:** Three.js with React Three Fiber and Drei.
- **Animation:** GSAP and ScrollTrigger.
- **Particles:** tsParticles for lightweight effects; custom Three.js Points/BufferGeometry and shaders for advanced GPU effects.
- **Smooth scrolling:** Lenis only if it does not conflict with accessibility, browser behavior, or other scrolling features.
- **Virtual try-on:** Browser camera APIs and face-landmark tracking, using a suitable maintained face-landmark solution such as MediaPipe Tasks Vision where compatible.

If the existing project is still WordPress-based and the repository/task scope requires WordPress removal, migrate the customer-facing site to the target Next.js + Laravel + SQL architecture. Do not simply hide WordPress pages or create a separate frontend that leaves the old platform as the real source of truth. Preserve data and functionality through a safe migration plan. Never delete the production database or existing source before verified backups and migration checks.

---

# 3. Premium brand and visual direction

## Brand positioning

**OpticValley — Luxury Meets Technology.**

Design a premium eyewear experience that combines:
- Timeless luxury.
- Editorial fashion photography.
- Optical precision.
- Minimalist commerce.
- Subtle futuristic interactions.
- Trustworthy product information.

## Recommended colour system

Use these as a starting point and adapt them to the existing brand assets:

- Warm ivory background: `#F7F6F2`
- Secondary warm surface: `#EDEBE5`
- Luxury black: `#151515`
- Main text: `#222222`
- Muted text: `#77736C`
- Champagne accent: `#B69A70`
- Dark cinematic section: `#101113`
- White: `#FFFFFF`

Keep the overall visual balance approximately:
- 70% warm minimal luxury.
- 20% dark cinematic storytelling.
- 10% restrained champagne accents.

Do not overuse gold, gradients, glassmorphism, borders, shadows, or rounded cards. The interface should feel refined, spacious, confident, and editorial.

## Typography

Choose a premium, highly legible typography system:
- Elegant display type for campaign headlines.
- Modern sans-serif for navigation, product information, and body copy.
- Clear hierarchy, controlled letter spacing, and generous whitespace.

Use locally hosted or properly licensed fonts where possible. Avoid loading many font families or weights.

## Photography and assets

- Reuse genuine OpticValley product images and brand assets wherever available.
- Use consistent, high-quality product photography and editorial imagery.
- Never present a generated or generic 3D frame as an exact purchasable product.
- Never invent product names, prices, specifications, discounts, ratings, stock, testimonials, or guarantees.
- If new photography or 3D models are required, create clearly identified placeholders and document the exact asset requirements.
- Optimize images to AVIF/WebP where appropriate, with responsive sizes and meaningful alt text.

---

# 4. Experience One — Cinematic Three.js 3D Hero

This is the signature first impression of OpticValley.

## Visual concept

Create a full-width, premium hero featuring realistic 3D sunglasses or eyeglasses floating in a cinematic studio environment.

The hero should include:
- A high-quality, realistic eyewear 3D model.
- Slow, elegant rotation and subtle floating movement.
- Physically plausible studio lighting.
- Controlled highlights and reflections on lenses and frames.
- A refined dark or warm neutral background.
- Subtle depth, atmosphere, and restrained particles.
- Premium headline and concise supporting copy.
- Clear calls to action.

Suggested copy:

**SEE THE WORLD DIFFERENTLY.**

*Where precision meets timeless design.*

CTAs:
- Explore Collection
- Discover Your Style

## Interaction

- Pointer movement gently influences model orientation.
- The model rotates slowly while idle.
- Scroll can transition the hero into the next section.
- Interactions must be smooth and restrained—not exaggerated.
- Support touch devices with appropriate touch interaction or a simplified presentation.
- Keep all hero text and CTAs as real HTML, not embedded in a 3D canvas.

## Technical implementation

- Use React Three Fiber, Three.js, and Drei.
- Prefer optimized GLB/GLTF models.
- Use physically based materials where assets support them.
- Use environment lighting and controlled reflection techniques.
- Avoid unnecessarily expensive real-time reflections.
- Use Suspense/loading states and a polished poster/fallback image.
- Lazy-load the 3D experience when appropriate.
- Stop or reduce rendering when the scene is off-screen or the page is idle, where feasible.
- Dispose of geometries, materials, textures, and render resources correctly.

If no authentic OpticValley eyewear model exists, create a clearly labelled temporary representative model or use an approved product render. Do not imply that a generic model is the exact product being sold.

---

# 5. Experience Two — Interactive Optical Lens and Refraction

Create a visually memorable section that communicates optical quality and precision.

Suggested heading:

**BEYOND ORDINARY VISION**

*Precision lenses. Exceptional clarity.*

## Visual concept

Display a large, elegant optical lens or glass element with:
- Convincing transparency.
- Controlled refraction and distortion.
- Subtle caustic-inspired light patterns.
- Fine highlights and edge reflections.
- Soft depth and atmospheric lighting.
- A restrained particle field.

## Interaction

- Pointer movement subtly changes the apparent light direction or lens response.
- Scroll can reveal the lens and move the camera or visual focus.
- Particles respond gently to pointer movement.
- The section should remain readable and visually calm.
- On mobile, use a simpler shader or high-quality static fallback if needed.

## Technical implementation

Use:
- Custom GLSL shaders where appropriate.
- Three.js ShaderMaterial or a suitable material pipeline.
- Three.js Points / BufferGeometry for GPU-friendly particles.
- tsParticles for lightweight DOM/canvas particle effects where suitable.

Do not fake optical claims. The visual is a brand storytelling effect, not a scientific demonstration of a specific lens product unless the relevant technical specifications are verified.

---

# 6. Experience Three — Real Virtual Try-On

Virtual Try-On must be a useful customer feature, not just a decorative camera mockup.

## Customer journey

1. Customer opens a product detail page or a dedicated “Virtual Try-On” experience.
2. Customer chooses “Try it on”.
3. Explain camera use and request permission only after the customer initiates the feature.
4. Offer camera preview and, where feasible, an option to upload a photo.
5. Detect facial landmarks.
6. Position and scale the selected frame using face landmarks.
7. Keep the frame aligned as the customer's head moves.
8. Allow the customer to switch between available frame colours/variants.
9. Provide clear controls to close, retry, or switch camera where supported.
10. Provide an appropriate fallback when camera access or tracking is unavailable.

## Product authenticity and alignment

- Use the actual selected product's approved 2D overlay or 3D model.
- Create a per-product asset mapping system for frame overlays/models.
- Store model/overlay references against products or variants in the backend/admin workflow where appropriate.
- Do not use one generic glasses overlay for every product.
- Support frame width, bridge position, temple alignment, lens height, and scale calibration where the assets provide the required data.
- If a product has no compatible try-on asset, clearly show “Virtual Try-On not available for this frame” and offer similar compatible products.

## Technical requirements

- Evaluate a maintained face-landmark tracking solution, such as MediaPipe Tasks Vision, for browser compatibility and performance.
- Use browser camera APIs securely (HTTPS required in production).
- Handle permission denied, camera unavailable, unsupported browser, low light, tracking lost, and model-loading errors.
- Do not upload or store camera images or face data by default.
- Process video locally in the browser wherever possible.
- Do not claim AI recommendations or precise fitting measurements unless genuinely implemented and validated.
- Include a clear privacy notice explaining camera use and whether any data is stored or transmitted.
- Make the feature usable with keyboard-accessible controls where possible.

## Admin and asset management

Where the existing admin architecture permits, add fields/workflows for:
- Try-on enabled/disabled.
- 2D overlay asset.
- Optional 3D model asset.
- Frame width / scale calibration.
- Bridge position calibration.
- Vertical offset.
- Variant-specific asset mapping.

Validate file type, size, and access permissions. Do not expose private storage paths.

---

# 7. Particle system and animation direction

Use particles as a premium visual accent, never as visual clutter.

Recommended placement:
- Hero: sparse floating champagne dust.
- 3D eyewear: subtle light particles around the model.
- Lens section: fine optical particles and light movement.
- Scroll transitions: restrained movement tied to scroll progress.
- Footer: extremely subtle ambient particles, only if performance permits.

Interactions:
- Gentle pointer response.
- Subtle attraction/repulsion.
- Slow ambient movement.
- Reduced or disabled motion on low-powered devices and when reduced motion is requested.

Use tsParticles for lightweight effects and custom Three.js particle systems for effects that need to live inside the 3D scene. Avoid running multiple unnecessary particle engines simultaneously.

---

# 8. GSAP animation system

Use GSAP and ScrollTrigger for polished motion:
- Headline split/reveal animations.
- Staggered product card reveals.
- Image clip-path reveals.
- Smooth section transitions.
- Controlled parallax.
- Lens section scroll choreography.
- Subtle navigation transitions.
- Editorial storytelling sequences.

Rules:
- Animation must support `prefers-reduced-motion`.
- Do not hijack normal scrolling.
- Avoid excessive pinned sections.
- Do not delay access to content or purchase controls.
- Clean up GSAP timelines and ScrollTriggers when components unmount.
- Avoid conflicting animation ownership between GSAP, CSS, and other libraries.

Lenis may be added for smooth scrolling only after testing keyboard navigation, anchors, browser history, touch behavior, and ScrollTrigger synchronization.

---

# 9. Premium homepage architecture

Build a complete homepage with the following order. Adjust only when the existing business structure or content makes a better sequence necessary.

## Section 1 — Announcement bar and navigation
- Refined announcement bar for verified promotions or delivery messages.
- Premium responsive header.
- Logo and clear category navigation.
- Search, account, wishlist, and cart.
- Accessible desktop mega menu.
- Mobile drawer with clear hierarchy.
- Sticky header with subtle transition.

## Section 2 — Cinematic 3D hero
- Signature floating eyewear model.
- Premium campaign headline.
- Explore Collection and Discover Your Style CTAs.
- Responsive static fallback.

## Section 3 — Shop by collection
- Men.
- Women.
- Kids.
- Eyeglasses.
- Sunglasses.
- Blue Light.
- Contact Lenses.

Use real category links and relevant images. Do not create dead-end navigation.

## Section 4 — Trending / Best Sellers
- Products loaded from the real backend.
- High-quality product cards.
- Actual product name, price, variants, and availability.
- Wishlist and quick view.
- Add to cart where valid.
- Clear sale pricing only when supported by backend data.

Avoid repetitive products unless they are genuinely the most relevant products returned by the merchandising rules.

## Section 5 — Optical lens experience
- Interactive lens/refraction visual.
- Short, accurate lens information.
- CTA to explore lens options.

## Section 6 — Find your perfect frame
- Face-shape education and frame recommendations.
- Explain that recommendations are style guidance, not medical advice.
- Link to compatible product collections.
- Do not infer sensitive attributes.

## Section 7 — Virtual Try-On
- Clear explanation and CTA.
- Show compatible products.
- Launch the real camera/overlay experience.
- Provide a privacy explanation.

## Section 8 — Prescription made simple
Present the existing prescription shopping journey as a polished, easy-to-understand process:
1. Choose your frame.
2. Select your lenses.
3. Add prescription details or upload a prescription.

Preserve existing validation, pricing, upload, and checkout behavior. Do not remove a working prescription flow.

## Section 9 — Brand story / editorial campaign
- OpticValley brand philosophy.
- Craftsmanship and product quality only where verified.
- High-quality fashion/editorial photography.
- Strong but restrained CTA.

## Section 10 — Trust and service information
Show only verified information:
- Delivery details.
- Returns/exchange policy.
- Payment methods.
- Customer support.
- Warranty or product guarantees, if actually offered.
- Genuine customer reviews.

## Section 11 — Newsletter and premium footer
- Clean newsletter signup connected to the real backend/provider.
- Useful navigation links.
- Contact information.
- Social links.
- Policies.
- Payment/delivery information where verified.

---

# 10. Product listing and product detail experience

## Product listing pages

Implement or improve:
- Category-specific banners.
- Search and autocomplete.
- Filters for category, brand, frame shape, frame material, colour, gender/collection, price, availability, and other real product attributes.
- Sorting by relevance, newest, price, and popularity where supported.
- Pagination or accessible load-more behavior.
- Filter chips and clear-all.
- Product count based on real results.
- Responsive grid.
- Empty, loading, and error states.

Do not show filters that have no corresponding data.

## Product cards

Each card should include:
- Real product image.
- Product name.
- Accurate price and sale price.
- Available colours/variants where supported.
- Wishlist action.
- Quick view.
- Product availability.
- Accessible focus and hover states.

## Product detail page

Create a premium, conversion-focused layout:
- Large image gallery.
- Zoom and fullscreen image viewing.
- 3D viewer only for products with real 3D assets.
- Variant selection.
- Frame dimensions and material details where known.
- Lens compatibility.
- Price and stock status from backend.
- Delivery/returns information from verified policies.
- Prescription/lens configuration.
- Add to cart and buy flow.
- Related products.
- Recently viewed products if privacy-safe and supported.

Never invent frame measurements or optical specifications.

---

# 11. Prescription and lens configurator

Preserve and improve the existing prescription journey.

Support the existing business rules for:
- Lens type.
- Prescription entry.
- Prescription image/PDF upload, if supported.
- Right eye and left eye values where applicable.
- Lens coatings and options.
- Lens price calculation.
- Validation and order summary.

Requirements:
- Make each step understandable.
- Explain technical terms in simple language.
- Show a clear progress indicator.
- Preserve uploaded file validation and secure storage.
- Validate values on both frontend and backend.
- Recalculate final price on the server.
- Do not trust client-submitted prices.
- Do not provide medical diagnosis or claim that a customer has a particular vision condition.
- Do not silently change existing backend pricing rules.

---

# 12. E-commerce functionality — preserve and verify

Audit and maintain all existing working capabilities, including where present:
- Customer registration and login.
- Account profile.
- Product search.
- Categories and subcategories.
- Product variants.
- Wishlist.
- Cart.
- Coupons and discounts.
- Checkout.
- Payment methods and payment verification.
- Cash on Delivery, if currently supported.
- Shipping and delivery calculations.
- Order creation and order history.
- Prescription selection and upload.
- Reviews and ratings.
- Newsletter.
- Contact/support forms.
- Admin product management.
- Inventory management.
- Order management.
- Customer management.
- Content and banner management.

Do not replace a real integration with a fake success message or hardcoded mock data. If an existing service requires credentials, preserve the integration interface and document the missing environment variables without exposing secrets.

---

# 13. Backend, database, and admin panel

The frontend must use the real Laravel APIs and SQL data.

Requirements:
- Review existing API contracts before making changes.
- Reuse existing endpoints when they are sound.
- Add versioned or backward-compatible endpoints when necessary.
- Use server-side validation and authorization.
- Validate uploaded files and enforce size/type limits.
- Use secure storage for private prescription files.
- Keep secrets in environment variables.
- Add database migrations for genuinely required new fields.
- Avoid destructive schema changes.
- Add indexes where justified by query patterns.
- Use pagination and efficient queries.
- Prevent N+1 queries where relevant.
- Add API error handling and consistent response structures.
- Keep admin routes protected by role/permission checks.

If admin changes are required, add practical management for:
- Homepage campaigns and banners.
- Featured collections.
- Featured/trending products.
- Product 3D assets.
- Virtual Try-On overlays and calibration.
- Product variants and attributes.
- SEO metadata.
- Newsletter content/settings where relevant.

Do not create a second disconnected admin panel if an existing admin system can be extended.

---

# 14. Responsive design and accessibility

Test at minimum:
- Small mobile: 320–375px.
- Standard mobile: 390–430px.
- Tablet: 768px.
- Laptop: 1366px.
- Desktop: 1440px and above.

Requirements:
- No horizontal overflow.
- No clipped headings or controls.
- Touch-friendly controls.
- Mobile-friendly navigation.
- Responsive product images.
- Simplified 3D scenes on mobile.
- Keyboard navigation.
- Visible focus states.
- Semantic HTML.
- Accessible labels and error messages.
- Sufficient contrast.
- Screen-reader-friendly dialogs and drawers.
- Reduced-motion support.
- Camera permissions explained before requesting access.

The mobile shopping experience is a first-class priority, not a compressed desktop layout.

---

# 15. Performance and Web Vitals

Premium design must not make the store slow.

Targets:
- Aim for strong Core Web Vitals and a Lighthouse score of 90+ where realistically achievable.
- Prioritize good LCP, INP, and CLS.
- Avoid loading large 3D bundles on every route.
- Lazy-load 3D, camera, and non-critical features.
- Use dynamic imports for heavy experiences.
- Use optimized GLB/GLTF models and textures.
- Compress images and serve responsive sizes.
- Preload only genuinely critical assets.
- Avoid layout shifts.
- Use caching appropriately.
- Reduce unnecessary JavaScript and third-party scripts.
- Pause or reduce off-screen animation/rendering.
- Respect low-power and reduced-motion settings.
- Keep product listing, cart, checkout, and account pages fast even if 3D is unavailable.

Create a graceful fallback for WebGL-disabled devices, older phones, and unsupported browsers.

---

# 16. SEO, analytics, and conversion optimization

Implement:
- Unique page titles and meta descriptions.
- Canonical URLs.
- Open Graph and social metadata.
- Product structured data using accurate backend values.
- Breadcrumb structured data.
- Organization/store structured data where appropriate.
- XML sitemap.
- Robots configuration.
- Clean URLs.
- Correct image alt text.
- Internal links between categories and products.
- Indexable server-rendered product/category content.
- Proper 404 and redirect behavior.

Preserve existing valuable URLs where possible. If URLs change, implement verified 301 redirects.

Analytics:
- Track product views, searches, filter use, wishlist, add-to-cart, checkout steps, and completed purchases.
- Respect applicable consent/privacy requirements.
- Do not send prescription details, camera frames, face landmarks, or other sensitive user data to analytics.

Conversion improvements:
- Clear shipping/returns information.
- Visible product availability.
- Trustworthy checkout.
- Helpful size/frame guidance.
- Easy contact/support access.
- Clear error and empty states.
- No fake urgency, fake stock counters, or fabricated reviews.

---

# 17. Security and privacy

- Never commit `.env` files, API keys, credentials, or private certificates.
- Add or update `.env.example` with placeholder variable names only.
- Enforce authentication and authorization server-side.
- Validate and sanitize user input.
- Protect uploads from malicious files.
- Use CSRF protection where applicable.
- Apply rate limiting to sensitive endpoints.
- Use secure cookies and HTTPS in production.
- Avoid exposing internal storage paths.
- Do not store camera frames or face data by default.
- Explain camera permissions and processing clearly.
- Ensure prescription files are private and accessible only to authorized users.

---

# 18. Code quality and architecture

- Use reusable, well-organized components.
- Separate 3D scenes from normal commerce UI.
- Keep product and pricing logic out of decorative components.
- Use clear naming and consistent formatting.
- Avoid giant monolithic components.
- Avoid duplicate dependencies.
- Avoid unnecessary libraries.
- Add loading, empty, error, and retry states.
- Keep API calls centralized and predictable.
- Use environment-based configuration.
- Add comments only where they clarify non-obvious logic.
- Do not leave dead buttons, fake links, placeholder checkout flows, or unfinished interactions.
- Do not break existing routes or integrations without a verified replacement.

Suggested component organization (adapt to the actual repository):

```text
src/
  app/
  components/
    layout/
    navigation/
    home/
    products/
    cart/
    checkout/
    prescription/
    virtual-try-on/
    three/
      HeroScene/
      LensExperience/
      ProductViewer/
    motion/
    ui/
  lib/
  services/
  hooks/
  styles/
  utils/
```

Do not force this exact structure if the existing project has a better established architecture.

---

# 19. Testing and quality assurance

After implementation, run the checks supported by the repository.

At minimum:
- Install dependencies using the existing package manager and lockfile.
- Run linting.
- Run production build.
- Run available unit/integration tests.
- Test key routes.
- Test responsive layouts.
- Test navigation and product filters.
- Test product detail and variants.
- Test wishlist and cart.
- Test checkout flow without creating fake production transactions.
- Test prescription validation and upload.
- Test Virtual Try-On permission/error/fallback states.
- Test 3D fallback with WebGL unavailable.
- Test keyboard navigation and reduced motion.
- Check browser console and network errors.
- Check for broken images and dead links.
- Verify API errors are handled properly.

If browser automation is available, use it for critical user journeys. Do not claim a test passed unless you actually ran it.

---

# 20. Required implementation workflow

Follow this workflow autonomously:

### Phase 1 — Inspect
- Inspect the repository and current website implementation.
- Understand frontend, backend, database, APIs, and admin.
- Identify existing functionality and technical risks.
- Create `IMPLEMENTATION_PLAN.md`.

### Phase 2 — Foundation and design system
- Establish premium design tokens.
- Improve typography, layout, spacing, buttons, cards, navigation, and responsive behavior.
- Build reusable UI components.

### Phase 3 — E-commerce redesign
- Implement the homepage structure.
- Improve category and product listing pages.
- Improve product detail pages.
- Preserve cart, checkout, account, prescription, and backend functionality.

### Phase 4 — Signature 3D experiences
- Implement cinematic Three.js hero.
- Implement interactive lens/refraction section.
- Add restrained particle effects.
- Add GSAP motion and scroll choreography.
- Add optimized fallbacks.

### Phase 5 — Virtual Try-On
- Implement camera permission flow.
- Integrate face landmarks.
- Implement product-specific overlays/models.
- Add calibration and admin asset mapping where practical.
- Add privacy, error, and fallback states.

### Phase 6 — Backend and admin integration
- Connect all features to real Laravel APIs and SQL data.
- Add compatible migrations/endpoints only when required.
- Extend the existing admin panel where necessary.

### Phase 7 — QA and optimization
- Run builds, lint, tests, and browser checks.
- Fix issues rather than merely listing them.
- Optimize performance, SEO, accessibility, and security.

### Phase 8 — Final report
Provide a concise but detailed report containing:
1. What was implemented.
2. Main pages and components changed.
3. Three.js and shader experiences delivered.
4. Virtual Try-On implementation and supported products.
5. Backend/API/database/admin changes.
6. Dependencies added or removed.
7. Tests actually run and their results.
8. Any remaining blockers or required real assets/credentials.
9. Environment variables that must be configured (names only, never secret values).
10. How to run the project locally and deploy it safely.

---

# 21. Non-negotiable rules

1. **Do not stop after the audit or plan. Implement the work.**
2. Do not ask for approval at every stage.
3. Do not replace the existing project with a static mockup.
4. Do not break existing customer, admin, order, payment, or prescription functionality.
5. Do not invent product information, reviews, pricing, or business policies.
6. Do not use generic 3D glasses as if they are exact products for sale.
7. Do not make Virtual Try-On appear functional if it is only a static image.
8. Do not request camera access automatically; require a customer action first.
9. Do not upload or store face/camera data by default.
10. Do not add animations that make shopping difficult or slow.
11. Do not leave non-functional buttons or fake integrations.
12. Do not delete databases, production data, or existing code without a verified backup and safe migration.
13. Preserve the current working architecture where it is sound; migrate only when required by the target architecture.
14. Test the final implementation and report honestly what is complete and what still needs real assets or credentials.

# Final direction

Build **OpticValley 2.0** as an ultra-premium, editorial, technology-enabled eyewear commerce experience.

The three signature experiences must be central to the creative direction:

- **3D Hero:** Realistic floating eyewear with cinematic lighting and refined interaction.
- **Interactive Lens:** Advanced glass refraction, optical-inspired shaders, and subtle particles.
- **Virtual Try-On:** Real customer-facing face tracking with product-specific frame assets.

But the complete website must remain a real, fast, trustworthy online store. Every visual experience must support the brand, product discovery, customer confidence, and sales.

**Start by inspecting the existing repository and then execute the full implementation from audit through testing. Do not return only recommendations, wireframes, or a plan.**
