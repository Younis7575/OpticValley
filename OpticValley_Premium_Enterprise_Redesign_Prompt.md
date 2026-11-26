# OPTIC VALLEY — COMPLETE PREMIUM ENTERPRISE E-COMMERCE REDESIGN

## PROJECT OBJECTIVE

Website: https://opticvalley.co/

Transform Optic Valley into a world-class, premium, visually distinctive eyewear e-commerce brand. The result must feel like a professionally designed international eyewear retailer: luxurious, modern, trustworthy, commercially effective, fast, and easy to use.

This is an implementation task, not a design-advice task. Inspect the actual project, understand its architecture, and implement the redesign in the codebase. Do not stop after an audit, mockup, plan, or recommendation.

The finished experience must be visually memorable while making it effortless for customers to discover eyewear, understand products, choose lenses, submit prescriptions, and complete purchases.

---

# 1. NON-NEGOTIABLE WORKING RULES

1. Inspect the complete existing repository and running website before changing code.
2. Identify the actual framework, dependencies, routes, backend, APIs, database schema, authentication, payment integration, product data, and deployment setup. Do not assume the existing implementation.
3. Preserve working business logic and data. Do not casually replace, delete, rename, or break existing functionality.
4. Do not remove products, categories, brands, images, customer records, orders, pricing, inventory, URLs, or existing integrations.
5. Do not use WordPress. The intended stack is Next.js + JavaScript for the frontend, Laravel for the backend, and SQL for the database. If the existing project already uses this stack, work within it. If it differs, explain the actual situation and propose a safe migration plan before making disruptive architectural changes.
6. Never fabricate products, prices, discounts, stock, ratings, reviews, testimonials, certifications, business addresses, delivery promises, or policy terms.
7. Use real existing website assets and product data wherever possible. Do not replace real assets with unrelated stock imagery or generic placeholders.
8. Do not hardcode secrets, API keys, credentials, or environment-specific URLs.
9. Do not create dead buttons, fake forms, non-functional filters, or decorative controls that look interactive.
10. Do not stop to ask for approval after every phase. Make sensible implementation decisions, document assumptions, and continue. Ask only when a missing credential, irreversible action, or genuinely blocking business decision makes progress impossible.
11. Do not claim a feature works unless you have implemented and tested it.
12. Keep the application runnable throughout the work. Avoid unnecessary rewrites and risky changes.
13. Do not expose private customer data, order information, or credentials in logs or frontend code.
14. Do not commit, push, deploy, or modify production data unless explicitly instructed.

---

# 2. PHASE ONE — COMPLETE WEBSITE AND CODEBASE AUDIT

Perform a thorough audit of the live website and project.

Inspect every accessible page and major state, including:

- Homepage and all homepage sections
- Main navigation, announcement bar, mega menus, mobile navigation
- Eyeglasses collection and category pages
- Sunglasses collection and category pages
- Blue-light and computer glasses
- Contact lenses, accessories, sale and other existing collections
- Brand collection pages
- Product detail pages and product variants
- Search, filters, sorting and pagination
- Wishlist, if available
- Cart and checkout
- Prescription selection and upload workflow
- Customer registration, login, account and order history
- About Us
- Contact Us
- FAQ
- Journal/blog listing and article detail
- Shipping and delivery policies
- Returns and refunds
- Privacy policy, terms and other legal pages
- Error, empty, loading and success states

Inspect the codebase to understand:

- Frontend routing and component structure
- Existing CSS, design system and responsive breakpoints
- Backend routes, controllers, services and middleware
- API contracts and authentication
- SQL tables, relationships and migrations
- Product, variant, inventory and pricing models
- Order, payment and checkout flow
- Prescription and file-upload handling
- Email, WhatsApp or other communication integrations
- Analytics, SEO and tracking
- Build, deployment and environment configuration

Deliver an internal implementation checklist based on the actual findings. Fix the issues as part of the work; do not only produce a report.

---

# 3. TECHNOLOGY AND ARCHITECTURE

Target stack:

## Frontend
- Next.js
- JavaScript (do not convert the project to TypeScript unless the existing project requires it and there is a compelling reason)
- Tailwind CSS
- Reusable, accessible components
- Framer Motion for subtle, purposeful motion where appropriate

## Backend
- Laravel
- RESTful APIs
- Secure authentication and authorization
- Server-side validation
- Consistent API response and error handling

## Database
- Existing SQL database (use the database already configured by the project)
- Proper relational integrity
- Indexed queries where useful
- Safe migrations and rollback strategy

## Architecture requirements
- Separate presentation from business logic.
- Reuse components and avoid duplicated code.
- Keep API communication organized.
- Validate all untrusted input on the server.
- Use environment variables for configuration and secrets.
- Preserve existing API contracts unless a versioned, backward-compatible change is necessary.
- Avoid unnecessary dependencies.
- Follow the conventions already established by the project.
- Keep the code maintainable and production-ready.

Do not introduce WordPress.

---

# 4. PREMIUM BRAND DESIGN SYSTEM

Create a cohesive visual identity for Optic Valley.

## Creative direction

Aim for modern luxury optical retail with premium editorial fashion aesthetics.

Use these brands as inspiration for quality, presentation and usability only:
- Gentle Monster
- Oliver Peoples
- Ray-Ban
- Warby Parker

Do not copy their logos, exact layouts, proprietary assets, text or brand identity. Optic Valley must retain its own recognizable identity.

## Suggested color palette

- Warm ivory: `#F6F3ED`
- Soft beige: `#E6DED1`
- Charcoal: `#222421`
- Muted taupe: `#8A8175`
- Champagne accent: `#B59A72`
- White: `#FFFFFF`

Use colors consistently. Check text contrast and accessibility. Do not force the accent color into every component.

## Typography

Suggested:
- Headings: Playfair Display or Cormorant Garamond
- Body and UI: Inter or Manrope

Use a clear type scale, controlled line lengths, elegant headings, readable body text and consistent font weights. Load fonts efficiently and provide fallbacks.

## Visual principles

- Strong editorial photography
- Large, intentional whitespace
- Consistent image aspect ratios
- Beautiful product cutouts and lifestyle photography
- Clean grids and alignment
- Refined borders and restrained shadows
- Clear visual hierarchy
- Consistent buttons, inputs, cards and spacing
- Subtle hover and scroll interactions
- High-quality mobile layouts

Avoid:
- Excessive gradients
- Excessive glassmorphism
- Too many rounded cards
- Random animations
- Overcrowded sections
- Generic AI-looking layouts
- Inconsistent icon styles
- Huge blocks of unnecessary text
- Fake urgency, fake countdowns or misleading sale labels

The result should look custom-designed, not like a copied template.

---

# 5. GLOBAL HEADER AND NAVIGATION

Redesign the complete navigation system.

## Announcement bar
- Display only verified offers and delivery information.
- Keep it elegant and compact.
- Ensure announcements are manageable and accurate.
- Avoid distracting, excessively fast marquee animations.

## Desktop header
- Optic Valley logo on the left.
- Main navigation centered or carefully balanced.
- Search, account, wishlist and cart on the right.
- Sticky header with a subtle transition when scrolling.
- Clear active and hover states.
- Accessible mega menus with category links and relevant imagery.

Suggested navigation (adapt to actual available categories):
- Eyeglasses
- Sunglasses
- Blue Light
- Contact Lenses
- Accessories
- Brands
- Sale

Do not show empty or unavailable categories.

## Mobile navigation
- Compact, easy-to-use header.
- Accessible menu drawer.
- Search easy to find.
- Clear account, wishlist and cart access.
- Touch-friendly controls.
- No horizontal overflow.

---

# 6. HOMEPAGE — COMPLETE REDESIGN

Create a carefully composed homepage with the following sections. Adjust the order if testing and actual merchandising data justify a better experience.

## Section 1: Hero campaign

Create a visually striking, full-width editorial hero.

Suggested headline:
“SEE THE WORLD DIFFERENTLY.”

Suggested supporting copy:
“Discover eyewear that brings together timeless design, exceptional comfort and precision-crafted lenses.”

CTAs:
- Explore Eyeglasses
- Discover Sunglasses

Requirements:
- Use relevant, high-quality existing campaign assets where available.
- Strong typography and intentional text placement.
- Responsive image crops for desktop and mobile.
- Accessible text contrast.
- Clear CTAs.
- Avoid autoplay video unless optimized, muted, accessible and genuinely beneficial.

Do not use unrelated imagery. If suitable assets are missing, create a clearly documented asset requirement rather than pretending a stock image represents Optic Valley.

## Section 2: Trust indicators

Show only verifiable benefits, such as:
- Premium eyewear
- Prescription support
- Nationwide delivery
- Secure checkout
- Customer support

Use accurate claims and link to relevant details where appropriate.

## Section 3: Shop by category

Create premium image-led category cards using the actual available collections.

Possible categories:
- Men's Eyewear
- Women's Eyewear
- Kids' Eyewear
- Sunglasses
- Prescription Glasses
- Computer Glasses

Each card should have:
- Relevant image
- Category name
- Short useful description
- Explore link
- Refined hover interaction

Only show categories that actually exist.

## Section 4: Best sellers / featured products

Create a premium product merchandising section.

Requirements:
- Pull real products from the existing catalogue/API.
- Avoid repeating the same product unnecessarily.
- Show accurate product title, price, sale price and availability.
- Show available color/variant options where appropriate.
- Wishlist action if supported.
- Quick view only if fully functional.
- Clear product links.
- Responsive product grid.
- Proper loading, empty and error states.

Possible tabs, only where data supports them:
- Best Sellers
- New Arrivals
- Trending
- Premium Collection

Do not invent popularity or best-seller rankings. Use actual sales or merchandising data.

## Section 5: Campaign banners

Create distinct editorial campaigns for existing collections, for example:
- The Sunglasses Edit
- Everyday Eyewear
- Blue Light Collection
- Kids' Vision Collection
- New Arrivals

Use genuine products and relevant assets. Each banner must lead to the correct collection.

## Section 6: Eyeglasses editorial feature

Create a strong image-led section introducing the eyeglasses collection.

Include:
- Editorial lifestyle image
- Clear headline
- Short brand-appropriate description
- Relevant collection links
- Shop Eyeglasses CTA

## Section 7: Prescription lens experience

Make prescription ordering a prominent Optic Valley differentiator.

Explain the actual available process in a clear three-step layout:
1. Choose your frame.
2. Select your lens.
3. Add your prescription.

Include:
- Prescription entry or upload options supported by the existing system
- Clear instructions
- Lens type explanations based on actual offerings
- Optician assistance only if genuinely available
- Privacy and file handling information
- Link to the real prescription workflow

Do not invent lens types, professional verification or medical claims.

## Section 8: Shop by shape / style

Where the catalogue supports it, introduce visual discovery by:
- Aviator
- Round
- Rectangle
- Square
- Cat-eye
- Wayfarer

Each item must filter or navigate to real matching products.

## Section 9: Featured brands

Show actual brands available in the catalogue.

Include:
- Brand logos or approved brand assets
- Relevant products
- Explore brand links

Do not create fictional brand partnerships.

## Section 10: Customer reviews

Use genuine reviews only.

Include:
- Verified purchase status where supported
- Actual ratings and review content
- Product association where available
- Customer images only with permission

If there are too few genuine reviews, design a restrained testimonial section without fabricated content.

## Section 11: Why Optic Valley

Create a concise brand story and explain real differentiators:
- Product quality
- Frame selection
- Prescription support
- Customer service
- Delivery and returns

Use only facts supported by the business.

## Section 12: Journal / eye-care content

Create an editorial magazine-style section featuring real published articles.

Include:
- One featured article
- Supporting article cards
- Relevant images
- Reading time if available
- Author or optician attribution only when verified

## Section 13: Newsletter

Suggested headline:
“A CLEARER VIEW STARTS HERE.”

Include:
- Short supporting copy
- Email field
- Subscribe button
- Validation
- Success and error states
- Privacy reassurance

Connect to the existing newsletter service or implement a secure backend endpoint if needed. Do not create a fake subscription form.

## Section 14: Footer

Build a structured premium footer with:
- Brand logo and short description
- Shop categories
- Customer support
- About and company links
- Contact information
- Store address and hours, if verified
- Social links
- Newsletter
- Accepted payment methods, only if supported
- Shipping, returns and legal links
- Copyright

Keep all information accurate and consistent.

---

# 7. COLLECTION AND CATEGORY LISTING PAGES

Redesign all collection pages consistently.

Requirements:
- Attractive collection hero/banner where appropriate
- Collection title and useful description
- Accurate product count
- Responsive product grid
- Sorting
- Filters
- Active filter chips
- Clear-all filters
- Pagination or load-more behavior
- Loading skeletons
- Empty results state
- Error and retry state

Useful filters, only when supported by real product attributes:
- Brand
- Gender
- Frame shape
- Frame material
- Frame color
- Price range
- Frame size
- Lens type
- Prescription availability

UX requirements:
- Desktop filter sidebar or well-designed filter panel
- Mobile filter drawer
- Filters must update actual results
- Sorting must work
- Query parameters should preserve filter state where appropriate
- Back/forward browser navigation should behave sensibly
- Do not show filters that have no usable data

Product cards should include:
- Consistent product image
- Product name
- Brand
- Accurate price and discount
- Availability
- Color options where supported
- Wishlist action if supported
- Quick view only if functional
- Clear product detail link

---

# 8. PRODUCT DETAIL PAGE — HIGH PRIORITY

This is one of the most important conversion pages.

Create a premium, conversion-focused product experience.

## Product gallery
- Large high-resolution primary image
- Multiple product angles
- Thumbnail navigation
- Zoom
- Responsive gallery
- Lifestyle/model images when available
- Video only when a real product video exists

## Product information
- Product title
- Brand
- SKU or product identifier where useful
- Accurate price and discount
- Stock/availability
- Color and variant selection
- Frame specifications
- Material
- Size and dimensions
- Warranty information
- Delivery details based on actual policy

## Purchase actions
- Quantity selector
- Add to Cart
- Buy Now if supported
- Wishlist if supported
- Clear loading, success and error feedback
- Prevent accidental duplicate submissions

## Prescription customization

Preserve and improve the existing prescription workflow.

Create a step-by-step experience, for example:

Step 1: Choose purchase option
- Frame only
- Frame with prescription lenses

Step 2: Choose available lens option
- Show only lens products and options actually offered

Step 3: Add prescription
- Enter prescription details or upload a file, based on supported options
- Provide clear upload instructions
- Validate file type and size on both client and server
- Explain how customers can get assistance

Step 4: Review selection
- Display selected frame, lens, prescription and price
- Allow customers to edit choices
- Add the complete configuration to cart

Important:
- Do not lose prescription details when a customer updates the cart.
- Validate and securely store uploaded files.
- Do not expose private prescription files through public URLs.
- Do not make medical recommendations.
- Keep the existing order and checkout integration intact.

## Additional product page sections
- Description
- Specifications
- Size and fit guide
- Shipping and returns
- Warranty
- Genuine customer reviews
- Related products
- Recently viewed products, if supported
- Frequently bought together only if backed by real catalogue data

Add a mobile sticky purchase bar without covering important content.

---

# 9. CART AND CHECKOUT

Review and improve the complete purchasing journey.

Cart:
- Accurate product and variant details
- Quantity updates
- Remove item
- Prescription configuration summary
- Accurate subtotal
- Shipping charges
- Discounts, if valid
- Clear checkout CTA
- Empty cart state
- Error handling

Checkout:
- Guest checkout if currently supported or safely implementable
- Customer information
- Shipping address
- Delivery method
- Payment options supported by the business
- Order summary
- Secure payment flow
- Validation
- Clear error and success messages
- Order confirmation

Important:
- Never expose payment secrets.
- Never trust frontend-calculated totals.
- Recalculate price, discounts, inventory and shipping on the backend.
- Prevent duplicate orders and payment processing.
- Preserve existing payment gateway integration.
- Test successful, failed and cancelled payment scenarios where a test environment exists.

---

# 10. CUSTOMER ACCOUNT

Improve the existing customer account experience.

Include the features already supported by the backend:
- Registration
- Login
- Password reset
- Profile
- Addresses
- Order history
- Order details
- Order tracking
- Wishlist
- Saved preferences, if available

Protect all private routes and customer data.

Do not create non-functional account menu items.

---

# 11. OTHER WEBSITE PAGES

Apply the same design system across all inner pages.

## About Us
- Editorial hero
- Brand story
- Mission and values
- Real store or team photography
- Verified quality and service information
- Relevant CTA

## Contact Us
- Clear contact details
- Functional contact form
- Form validation
- Success and error states
- WhatsApp link if the business uses it
- Store address and opening hours if verified
- Map only for a verified location

## FAQ
Organize real questions into categories:
- Ordering & Payments
- Prescription & Lenses
- Shipping & Delivery
- Returns & Refunds
- Warranty
- Product Care
- Frame Size & Fit
- Order Tracking

Use accessible accordions and searchable FAQs if useful.

## Shipping and Returns
- Clear delivery charges
- Delivery estimates
- Prescription order timelines
- Return eligibility
- Refund process
- Warranty details

Make policy information consistent across product pages, cart, checkout, FAQ and footer. Do not invent policy terms.

## Journal / Blog
- Premium editorial listing
- Featured article
- Category navigation
- Article cards
- Reading time if available
- Well-structured article pages
- Related articles
- Relevant product links

## Legal pages
Make privacy policy, terms, refunds and other legal pages readable and consistent. Do not rewrite legal commitments without verifying the actual business policy.

---

# 12. MOBILE-FIRST RESPONSIVE EXPERIENCE

The mobile website must feel intentionally designed, not like a compressed desktop layout.

Test at:
- Small mobile
- Standard mobile
- Large mobile
- Tablet
- Laptop
- Desktop
- Large desktop

Requirements:
- No horizontal overflow
- Readable typography
- Proper image cropping
- Touch-friendly controls
- Compact navigation
- Easy search
- Responsive product grid
- Mobile filter drawer
- Easy prescription upload
- Sticky Add to Cart where appropriate
- Proper modal and drawer behavior
- Accessible forms
- Consistent spacing

Test real interactions at different viewport sizes.

---

# 13. ANIMATIONS AND INTERACTIONS

Use Framer Motion or the existing animation library where suitable.

Recommended:
- Subtle page transitions
- Smooth dropdowns
- Refined mega menu
- Product card hover effects
- Image reveal
- Gentle scroll animations
- Button feedback
- Drawer and modal transitions
- Small loading animations

Rules:
- Animation should support the experience, not distract.
- Respect reduced-motion preferences.
- Avoid unnecessary parallax and heavy effects.
- Do not delay access to content.
- Maintain good performance on mobile.

---

# 14. SEARCH, DISCOVERY AND CONVERSION UX

Improve product discovery.

Where supported, implement:
- Search suggestions
- Product thumbnails in suggestions
- Search by product name or brand
- Relevant search results
- Search empty state
- Recent searches only if privacy-appropriate
- Sorting and filtering
- Related products
- Recently viewed products
- Clear category navigation

Conversion improvements:
- Clear product pricing
- Transparent delivery information
- Visible returns and warranty information
- Trust indicators based on real facts
- Clear purchase buttons
- Helpful prescription guidance
- Reduced checkout friction
- Helpful validation and error messages

Do not use deceptive patterns, fake scarcity or misleading discount displays.

---

# 15. PERFORMANCE AND TECHNICAL QUALITY

Optimize the complete website.

Requirements:
- Use Next.js image optimization appropriately.
- Use WebP/AVIF where suitable.
- Lazy-load below-the-fold imagery.
- Avoid layout shifts by reserving image dimensions.
- Reduce unnecessary JavaScript.
- Avoid duplicate API requests.
- Use caching carefully without exposing private customer data.
- Optimize Laravel queries.
- Add indexes where appropriate.
- Paginate large collections.
- Prevent N+1 query problems.
- Minimize unnecessary dependencies.
- Keep animations lightweight.
- Avoid blocking rendering with non-critical scripts.

Aim for strong Core Web Vitals and good Lighthouse performance. Measure the actual result rather than claiming a score without testing.

---

# 16. SEO AND ACCESSIBILITY

SEO:
- Unique page titles and meta descriptions
- Canonical URLs
- Open Graph metadata
- XML sitemap
- Robots configuration
- Product structured data based on real product information
- Breadcrumb structured data
- Organization/local business structured data only with verified details
- Clean URLs
- Proper heading hierarchy
- Descriptive image alt text
- Internal linking
- Correct redirects when URLs genuinely change

Accessibility:
- Semantic HTML
- Keyboard navigation
- Visible focus states
- Accessible labels
- Alt text
- Sufficient contrast
- Accessible dialogs and drawers
- Form error announcements
- Reduced-motion support
- Proper button and link semantics

Do not add fabricated structured data or fake ratings.

---

# 17. SECURITY AND DATA INTEGRITY

Review and strengthen:
- Authentication and authorization
- CSRF protection where applicable
- Input validation
- File upload validation
- Rate limiting
- Secure session/cookie settings
- API access control
- SQL injection prevention
- XSS prevention
- Sensitive file storage
- Payment verification
- Environment secrets
- Error logging without exposing private information

Use Laravel's established security mechanisms and follow framework best practices.

Never expose admin routes or customer information publicly.

---

# 18. ADMIN PANEL AND BACKEND COMPATIBILITY

Inspect the existing admin panel and backend before making changes.

Preserve existing administration capabilities for:
- Products
- Categories
- Brands
- Variants
- Inventory
- Pricing
- Discounts
- Orders
- Customers
- Prescription files
- Reviews
- Content and banners
- Newsletter subscriptions
- Site settings

If a required admin capability is missing, identify it and implement it only when it fits the existing architecture and scope.

The redesigned frontend must use real backend data. Avoid duplicating business logic in the frontend.

Do not break existing API consumers.

---

# 19. TESTING AND QUALITY ASSURANCE

After implementation, test the actual website and application.

## Functional tests
- Navigation links
- Mega menus
- Search
- Category navigation
- Filters
- Sorting
- Product variants
- Wishlist
- Add to cart
- Cart quantity updates
- Prescription selection
- Prescription upload
- Checkout
- Payment outcomes in test mode
- Registration and login
- Password reset
- Customer account
- Contact form
- Newsletter
- FAQ accordions
- Blog navigation

## Responsive tests
- Mobile navigation
- Product grid
- Product gallery
- Filters and drawers
- Prescription wizard
- Cart
- Checkout
- Footer

## Technical tests
- Frontend build
- Laravel tests where available
- API response validation
- Database migrations
- Browser console
- Network errors
- Broken links
- Image loading
- Accessibility checks
- Lighthouse and Core Web Vitals where possible

Fix errors discovered during testing. Do not simply list them.

---

# 20. IMPLEMENTATION WORKFLOW

Work in the following order:

### Phase 1 — Discovery
- Inspect repository and website.
- Understand the current stack and business logic.
- Identify all pages and components.
- Document existing integrations and risks.

### Phase 2 — Foundation
- Establish design tokens.
- Typography and color system.
- Global spacing and responsive rules.
- Buttons, inputs, cards and shared components.
- Header and footer.

### Phase 3 — Homepage
- Build all homepage sections.
- Use real products and assets.
- Implement responsive layouts and interactions.

### Phase 4 — Shopping pages
- Collection pages.
- Product cards.
- Filters and sorting.
- Product detail pages.
- Prescription workflow.

### Phase 5 — Commerce
- Wishlist.
- Cart.
- Checkout.
- Customer account.
- Order experience.

### Phase 6 — Content pages
- About.
- Contact.
- FAQ.
- Journal.
- Shipping.
- Returns.
- Legal pages.

### Phase 7 — Optimization
- SEO.
- Accessibility.
- Performance.
- Security.
- Responsive QA.

### Phase 8 — Final verification
- Run builds and tests.
- Fix errors.
- Verify all important user journeys.
- Document completed work and remaining blockers.

Do not leave the project in a half-finished state.

---

# 21. FINAL ACCEPTANCE CRITERIA

The work is complete only when:

1. The website has a cohesive premium Optic Valley identity.
2. The homepage is fully redesigned.
3. All important collection and product pages are redesigned.
4. All existing business functionality is preserved.
5. Prescription ordering works correctly.
6. Cart and checkout work correctly.
7. Mobile and desktop experiences are polished.
8. Navigation and interactive controls work.
9. Real products and accurate business information are displayed.
10. No fake reviews, products or business claims are introduced.
11. No broken images or dead buttons remain in the implemented scope.
12. No critical console or build errors remain.
13. SEO and accessibility fundamentals are implemented.
14. Performance has been checked.
15. The code is maintainable and production-ready.

---

# 22. FINAL REPORT

When finished, provide a concise but complete report containing:

- What was inspected
- What was redesigned
- Pages completed
- Components created or updated
- Backend/API changes
- Database changes and migrations
- Features implemented
- Existing features preserved
- Tests performed and their results
- Build status
- Performance findings
- Known issues or blocked tasks
- Any environment variables or credentials I must configure
- Exact commands to run the frontend and backend locally

Be honest about anything that could not be completed or verified.

---

# FINAL INSTRUCTION

Start by inspecting the actual Optic Valley project and website. Then proceed with implementation.

I do not want a basic template, a generic ecommerce layout, a design-only prototype, or a list of suggestions.

I want a complete, visually exceptional, premium, modern, enterprise-level eyewear e-commerce website with a refined shopping experience, consistent branding, reliable functionality, responsive layouts and production-quality code.

Make every page feel like it belongs to the same premium brand.

**Think like a senior product designer, luxury e-commerce creative director, senior Next.js engineer, Laravel architect and QA engineer working together on one serious commercial product.**

Prioritize exceptional design, real functionality, customer trust, maintainability and business results.

Do not stop at planning. Implement, test, fix and report.
