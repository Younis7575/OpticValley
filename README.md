# OpticValley — Eyewear E-commerce Platform

Premium optical e-commerce store for **opticvalley.co**, built with **Laravel 13**, **Blade + Tailwind CSS 4 + Alpine.js** and **MySQL**. Includes a full storefront, a separate role-based admin panel, a read-only REST catalogue API, and a GitHub Actions pipeline that deploys every push to `main`.

## Features

**Storefront**
- Homepage: hero slider, shop-by-category, featured / best sellers / new arrivals tabs, promo tiles, eyeglasses & sunglasses showcases, prescription-lens story, special offers, featured brands, testimonials (from real approved reviews), journal, newsletter
- Catalogue with nested categories, brand pages, search, filters (gender, brand, shape, material, colour, price, sale, in stock, prescription-ready), sorting and pagination
- Product page: gallery with zoom, colour variants with per-variant stock, lens selection with pricing, prescription entry (SPH/CYL/AXIS/ADD/PD) or private upload, wishlist, reviews, related products, Product JSON-LD
- Session cart (prices always recalculated server-side), coupons, free-shipping threshold, tax
- Guest & customer checkout, Cash on Delivery / bank transfer / online-payment-link methods, order confirmation e-mails
- Customer account: orders, order detail & cancellation, tracking timeline, wishlist, addresses, profile, password reset
- Pages (About, Privacy, Terms, Shipping, Returns, FAQs), blog, contact form, order tracking, WhatsApp button
- SEO: meta & Open Graph tags, canonical URLs, Organization/Product/Article/Breadcrumb structured data, `sitemap.xml`, `robots.txt`

**Admin panel** (`/admin`)
- Dashboard with real revenue, orders, customers, status counts, revenue/orders chart, top sellers, low stock, recent orders
- Products (images, variants, specs, flags, SEO), categories (tree), brands, lens options
- Orders: filtering, status workflow with automatic restock on cancel/refund, payment status, courier & tracking, timeline notes, refunds, printable invoice, CSV export, prescription viewer
- Customers (history, spend, block/unblock), coupons, banners, newsletter subscribers (CSV export)
- Pages, blog, navigation menus, review moderation, contact messages
- Settings (store, currency & tax, shipping, payments, SEO & social, e-mail), admin users, roles & permissions, activity log

**Security**: hashed passwords, CSRF, rate-limited auth/checkout/forms, role-based permissions, server-side price/stock validation in DB transactions with row locks, HTML sanitising for admin rich text, private storage for prescriptions, security headers, no secrets in git.

## Local development

Requirements: PHP 8.3+, Composer, Node 20+.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite          # or configure MySQL in .env
php artisan migrate --seed               # prints the generated admin password
npm run build                            # or: npm run dev
php artisan serve
```

- Store: http://localhost:8000
- Admin: http://localhost:8000/admin (login `admin@opticvalley.co`, password printed by the seeder or set `ADMIN_PASSWORD` in `.env`)

Run tests: `php artisan test`

## Project structure

```
app/
  Http/Controllers/Store   storefront, cart, checkout, account, pages
  Http/Controllers/Admin   admin panel
  Http/Controllers/Api     public catalogue API
  Models/                  Eloquent models
  Services/Cart.php        session cart & totals
  Services/OrderService.php  transactional checkout, status changes, refunds
  Support/                 media uploads, HTML sanitiser, frame artwork
database/migrations, seeders
resources/views/store      storefront Blade templates
resources/views/admin      admin Blade templates
deployment/                server scripts (deploy, rollback, setup, env template)
.github/workflows/deploy.yml  CI + CD pipeline
docs/DEPLOYMENT.md         production setup guide
```

## API

Public, read-only, rate-limited (60/min):

| Endpoint | Description |
|---|---|
| `GET /api/v1/products?category=&brand=&q=&gender=&frame_shape=&per_page=` | Paginated products |
| `GET /api/v1/products/{slug}` | Product detail with images & variants |
| `GET /api/v1/categories` | Category tree |
| `GET /api/v1/brands` | Brands |
| `GET /api/v1/settings` | Public store settings |

## Deployment

See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md).
