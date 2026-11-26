<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use App\Support\FrameArt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Starter catalogue so the storefront is complete on day one. Every item is
 * editable/deletable from Admin → Products; replace the renders with real
 * product photography as it becomes available.
 */
class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::exists()) {
            $this->command?->info('Products already exist — skipping demo catalogue.');

            return;
        }

        $dir = public_path('uploads/demo/products');
        File::ensureDirectoryExists($dir);

        // Banner art
        File::put(public_path('uploads/demo/hero-1.svg'), FrameArt::svg('wayfarer', 'tortoise', null, false, 0, '#eadfce'));
        File::put(public_path('uploads/demo/hero-2.svg'), FrameArt::svg('aviator', 'gold', 'dark', true, 0, '#dfe6ea'));
        File::put(public_path('uploads/demo/promo-1.svg'), FrameArt::svg('square', 'blue', null, false, 1, '#dde7f0'));
        File::put(public_path('uploads/demo/promo-2.svg'), FrameArt::svg('cat-eye', 'burgundy', 'rose', false, 1, '#f1dfe0'));
        File::put(public_path('uploads/demo/promo-3.svg'), FrameArt::svg('round', 'green', null, false, 1, '#e3eddf'));

        $brands = collect([
            ['Valley Atelier', 'Our signature house label — hand-polished acetate and titanium.', true],
            ['Lumen & Co.', 'Minimal metal frames with a featherweight feel.', true],
            ['Northline', 'Bold, modern shapes for everyday confidence.', true],
            ['Solenne', 'Sun-first eyewear with polarised lenses.', true],
            ['Little Lens', 'Flexible, near-unbreakable frames for kids.', false],
            ['ClearDay', 'Lens care and accessories.', false],
        ])->mapWithKeys(fn ($b) => [$b[0] => Brand::create(['name' => $b[0], 'slug' => Str::slug($b[0]), 'description' => $b[1], 'is_featured' => $b[2], 'is_active' => true])]);

        $cat = fn (string $slug) => Category::where('slug', $slug)->value('id');

        // name, brand, primary cat, extra cats, price, sale, shape, material, color, tint|null, gender, rx, flags[featured,best,new], stock
        $items = [
            ['Aurelio Wayfarer', 'Valley Atelier', 'mens-eyeglasses', ['prescription-glasses'], 6900, 5490, 'wayfarer', 'acetate', 'tortoise', null, 'men', true, [1, 1, 0], 34],
            ['Noor Cat-Eye', 'Valley Atelier', 'womens-eyeglasses', ['prescription-glasses'], 7400, null, 'cat-eye', 'acetate', 'burgundy', null, 'women', true, [1, 0, 1], 22],
            ['Hayden Round Titanium', 'Lumen & Co.', 'mens-eyeglasses', ['prescription-glasses'], 9800, 8490, 'round', 'titanium', 'silver', null, 'unisex', true, [1, 1, 0], 18],
            ['Mira Oval', 'Lumen & Co.', 'womens-eyeglasses', ['prescription-glasses'], 5900, null, 'oval', 'metal', 'rose gold', null, 'women', true, [0, 1, 1], 27],
            ['Kestrel Rectangle', 'Northline', 'mens-eyeglasses', ['prescription-glasses', 'computer-glasses'], 4900, 3990, 'rectangle', 'tr90', 'black', null, 'men', true, [0, 1, 0], 51],
            ['Ivy Square', 'Northline', 'womens-eyeglasses', ['prescription-glasses'], 5200, null, 'square', 'acetate', 'ivory', null, 'women', true, [1, 0, 1], 15],
            ['Atlas Geometric', 'Valley Atelier', 'mens-eyeglasses', ['prescription-glasses'], 8200, null, 'geometric', 'titanium', 'gunmetal', null, 'men', true, [0, 0, 1], 12],
            ['Elara Crystal', 'Northline', 'womens-eyeglasses', ['prescription-glasses'], 4600, 3690, 'square', 'acetate', 'crystal', null, 'women', true, [0, 1, 0], 40],
            ['Pixel Blue Shield', 'Northline', 'blue-light-blocking-glasses', ['computer-glasses'], 3900, 2990, 'rectangle', 'tr90', 'navy', null, 'unisex', true, [1, 1, 0], 80],
            ['Focus Pro Computer', 'Lumen & Co.', 'computer-glasses', ['blue-light-blocking-glasses'], 4400, null, 'round', 'metal', 'gold', null, 'unisex', true, [0, 0, 1], 45],
            ['Lumi Screen Square', 'Northline', 'blue-light-blocking-glasses', ['computer-glasses'], 3500, null, 'square', 'tr90', 'black', null, 'unisex', true, [0, 1, 1], 60],
            ['Reader Classic +1.5', 'Lumen & Co.', 'reading-glasses', [], 2500, 1990, 'rectangle', 'metal', 'gunmetal', null, 'unisex', false, [0, 0, 0], 70],
            ['Reader Soft Oval', 'Lumen & Co.', 'reading-glasses', [], 2200, null, 'oval', 'acetate', 'tortoise', null, 'unisex', false, [0, 0, 1], 55],
            ['Maverick Aviator', 'Solenne', 'mens-sunglasses', [], 7900, 6490, 'aviator', 'metal', 'gold', 'dark', 'men', false, [1, 1, 0], 30],
            ['Riviera Cat-Eye Sun', 'Solenne', 'womens-sunglasses', [], 8400, null, 'cat-eye', 'acetate', 'black', 'brown', 'women', false, [1, 1, 1], 25],
            ['Coastline Wayfarer Sun', 'Solenne', 'mens-sunglasses', [], 6500, 4990, 'wayfarer', 'acetate', 'black', 'green', 'unisex', false, [1, 1, 0], 44],
            ['Saffron Round Sun', 'Solenne', 'womens-sunglasses', [], 6900, null, 'round', 'metal', 'rose gold', 'rose', 'women', false, [0, 0, 1], 20],
            ['Horizon Polarised Square', 'Northline', 'mens-sunglasses', [], 5900, null, 'square', 'tr90', 'navy', 'blue', 'men', false, [0, 1, 1], 33],
            ['Dune Geometric Sun', 'Valley Atelier', 'womens-sunglasses', [], 9200, 7990, 'geometric', 'titanium', 'gold', 'brown', 'women', false, [1, 0, 0], 14],
            ['Shadow Oval Sun', 'Solenne', 'womens-sunglasses', [], 5400, null, 'oval', 'acetate', 'tortoise', 'brown', 'women', false, [0, 1, 0], 29],
            ['Pilot Gunmetal Aviator', 'Solenne', 'mens-sunglasses', [], 7200, null, 'aviator', 'metal', 'gunmetal', 'green', 'men', false, [0, 0, 1], 3],
            ['Bubbles Kids Round', 'Little Lens', 'kids-eyeglasses', ['prescription-glasses'], 2900, 2490, 'round', 'tr90', 'blue', null, 'kids', true, [0, 1, 0], 38],
            ['Sprout Kids Rectangle', 'Little Lens', 'kids-eyeglasses', ['prescription-glasses'], 2700, null, 'rectangle', 'tr90', 'green', null, 'kids', true, [0, 0, 1], 41],
            ['Sunny Kids Wayfarer', 'Little Lens', 'kids-sunglasses', [], 2400, 1990, 'wayfarer', 'tr90', 'blue', 'blue', 'kids', false, [0, 1, 0], 36],
            ['Comet Kids Aviator', 'Little Lens', 'kids-sunglasses', [], 2600, null, 'aviator', 'tr90', 'silver', 'dark', 'kids', false, [0, 0, 1], 0],
        ];

        foreach ($items as $n => [$name, $brand, $primary, $extra, $price, $sale, $shape, $material, $color, $tint, $gender, $rx, $flags, $stock]) {
            $isSun = $tint !== null;
            $metal = in_array($material, ['metal', 'titanium'], true);
            $slug = Str::slug($name);
            $product = Product::create([
                'category_id' => $cat($primary),
                'brand_id' => $brands[$brand]->id,
                'name' => $name,
                'slug' => $slug,
                'sku' => 'OV-'.strtoupper(substr(Str::slug($brand, ''), 0, 3)).'-'.str_pad((string) ($n + 101), 4, '0', STR_PAD_LEFT),
                'short_description' => $isSun
                    ? 'UV400 '.($tint === 'dark' ? 'polarised ' : '')."{$shape} sunglasses in {$color} {$material}."
                    : ucfirst($shape)." {$material} frame in {$color}".($rx ? ', ready for your prescription.' : '.'),
                'description' => $this->description($name, $shape, $material, $color, $isSun, $rx),
                'price' => $price,
                'sale_price' => $sale,
                'stock' => $stock,
                'low_stock_threshold' => 5,
                'gender' => $gender,
                'frame_shape' => $shape,
                'frame_type' => $shape === 'geometric' ? 'rimless' : ($metal && $shape === 'round' ? 'half-rim' : 'full-rim'),
                'frame_material' => $material,
                'frame_color' => $color,
                'frame_size' => $gender === 'kids' ? 'Small (46-16-125)' : ['Medium (52-18-140)', 'Large (55-18-145)', 'Medium (50-20-140)'][$n % 3],
                'lens_info' => $isSun ? 'UV400 '.($tint === 'dark' ? 'polarised ' : '').'tinted lenses' : ($rx ? 'Demo lenses included — replaced with your prescription lenses' : 'Magnification lenses'),
                'specifications' => [
                    'Lens width' => $gender === 'kids' ? '46 mm' : '52 mm',
                    'Bridge' => '18 mm',
                    'Temple length' => $gender === 'kids' ? '125 mm' : '140 mm',
                    'Weight' => $metal ? '18 g' : '24 g',
                    'Hinge' => $metal ? 'Spring hinge' : '5-barrel metal hinge',
                    'Warranty' => '6 months frame warranty',
                ],
                'prescription_enabled' => $rx,
                'is_featured' => (bool) $flags[0],
                'is_best_seller' => (bool) $flags[1],
                'is_new' => (bool) $flags[2],
                'is_active' => true,
                'sold_count' => $flags[1] ? 40 + $n * 3 : $n,
            ]);
            $product->categories()->sync(array_filter(array_map($cat, $extra)));

            foreach ([0, 1, 2] as $v) {
                $file = "uploads/demo/products/{$slug}-{$v}.svg";
                File::put(public_path($file), FrameArt::svg($shape, $color, $tint, $metal, $v));
                $product->images()->create(['path' => $file, 'alt' => "{$name} — ".['front view', 'angled view', 'detail'][$v], 'sort_order' => $v]);
            }

            $alt = ['black' => ['Tortoise', '#7a4a1f'], 'tortoise' => ['Matte Black', '#1d1f23'], 'gold' => ['Silver', '#c4c9cf'], 'silver' => ['Gold', '#d1ad5e']][$color] ?? null;
            $product->variants()->create(['name' => Str::title($color), 'color_hex' => FrameArt::COLORS[$color][1] ?? '#333', 'sku' => $product->sku.'-A', 'stock' => $stock, 'price_adjustment' => 0]);
            if ($alt && $stock > 0) {
                $product->variants()->create(['name' => $alt[0], 'color_hex' => $alt[1], 'sku' => $product->sku.'-B', 'stock' => max(1, intdiv($stock, 2)), 'price_adjustment' => 0]);
            }
        }

        // Accessories
        foreach ([
            ['Hard Shell Eyewear Case', 'eyewear-cases', 1200, null, 'Crush-resistant zip case with a soft microfibre lining.'],
            ['Vegan Leather Folding Case', 'eyewear-cases', 1500, 1250, 'Slim magnetic folding case that slips into any pocket.'],
            ['Lens Care Kit — Spray + Cloth', 'lens-cleaning-kits', 650, null, 'Alcohol-free anti-fog spray with two premium microfibre cloths.'],
            ['Microfibre Cloth (Pack of 3)', 'lens-cleaning-kits', 450, null, 'Ultra-soft cloths that lift fingerprints without scratching.'],
        ] as $k => [$name, $c, $price, $sale, $desc]) {
            $slug = Str::slug($name);
            $file = "uploads/demo/products/{$slug}-0.svg";
            File::put(public_path($file), $this->accessoryArt($k));
            $p = Product::create([
                'category_id' => $cat($c), 'brand_id' => $brands['ClearDay']->id, 'name' => $name, 'slug' => $slug,
                'sku' => 'OV-CLD-'.str_pad((string) (900 + $k), 4, '0', STR_PAD_LEFT), 'short_description' => $desc,
                'description' => "<p>{$desc}</p><p>Designed to keep your OpticValley eyewear in perfect condition, every day.</p>",
                'price' => $price, 'sale_price' => $sale, 'stock' => 100, 'gender' => 'unisex', 'is_active' => true, 'is_new' => $k === 1,
            ]);
            $p->images()->create(['path' => $file, 'alt' => $name, 'sort_order' => 0]);
        }

        // Reviews
        $names = ['Ayesha K.', 'Hamza R.', 'Sana M.', 'Bilal A.', 'Fatima Z.', 'Usman T.', 'Mahnoor S.', 'Ali H.'];
        $texts = [
            [5, 'Absolutely love them', 'The frame quality is excellent and the lenses were spot-on with my prescription. Delivery to Karachi took just 3 days.'],
            [5, 'Premium feel', 'Feels much more expensive than the price. Packaging was beautiful too.'],
            [4, 'Great fit', 'Comfortable for long hours at the laptop. Would have liked one more colour option.'],
            [5, 'Super helpful team', 'They confirmed my order on WhatsApp and helped me choose the right lens. Highly recommended.'],
            [4, 'Very good', 'Stylish and lightweight. Cash on delivery made it easy.'],
        ];
        Product::whereNotNull('frame_shape')->get()->each(function (Product $p, $i) use ($names, $texts) {
            foreach (range(0, ($i % 3) + 1) as $j) {
                [$rating, $title, $body] = $texts[($i + $j) % count($texts)];
                Review::create(['product_id' => $p->id, 'name' => $names[($i + $j) % count($names)], 'rating' => $rating, 'title' => $title, 'body' => $body, 'is_approved' => true]);
            }
        });

        Coupon::firstOrCreate(['code' => 'WELCOME10'], ['type' => 'percent', 'value' => 10, 'min_order' => 3000, 'max_discount' => 1500, 'is_active' => true]);

        foreach ([
            ['How to choose the right frame for your face shape', 'Round, square, oval or heart — here is the simple guide our opticians use in-store.', '<p>Finding the right frame starts with your face shape. <strong>Round faces</strong> are balanced by angular frames such as rectangles and wayfarers. <strong>Square faces</strong> soften beautifully with round and oval frames. <strong>Oval faces</strong> suit almost everything — try a bold cat-eye or geometric shape. <strong>Heart-shaped faces</strong> look great in bottom-heavy or light rimless styles.</p><h3>Mind the width</h3><p>Your frame should be roughly as wide as your face, with your eyes centred in each lens.</p><h3>Still unsure?</h3><p>Send us a selfie on WhatsApp and our team will recommend three frames for you.</p>'],
            ['Blue light glasses: do you really need them?', 'If you spend more than six hours a day on screens, here is what blue-light lenses can and cannot do.', '<p>Digital screens emit high-energy visible (HEV) blue light. Blue-light filtering lenses reduce glare and may ease digital eye strain during long working hours.</p><h3>Who benefits most</h3><ul><li>Office workers and students</li><li>Gamers and creators</li><li>Anyone using screens late at night</li></ul><p>Pair them with the 20-20-20 rule: every 20 minutes, look 20 feet away for 20 seconds.</p>'],
            ['Polarised vs. UV400: what is the difference?', 'Both protect your eyes, but in different ways. Here is how to choose your next pair of sunglasses.', '<p><strong>UV400</strong> means the lenses block 100% of UVA and UVB rays — essential for eye health. <strong>Polarised</strong> lenses additionally cut horizontal glare from roads, water and glass, making them ideal for driving.</p><p>All OpticValley sunglasses are UV400; look for the "polarised" note in the product details for glare reduction.</p>'],
        ] as $k => [$title, $excerpt, $body]) {
            Post::create(['title' => $title, 'slug' => Str::slug($title), 'excerpt' => $excerpt, 'body' => $body, 'image' => 'uploads/demo/promo-'.($k + 1).'.svg', 'published_at' => now()->subDays(3 + $k * 6)]);
        }
    }

    private function description(string $name, string $shape, string $material, string $color, bool $sun, bool $rx): string
    {
        $intro = $sun
            ? "<p>The <strong>{$name}</strong> pairs a timeless {$shape} silhouette with full UV400 protection. Crafted in {$color} {$material}, it is built for bright Pakistani summers and everyday style.</p>"
            : "<p>The <strong>{$name}</strong> is a refined {$shape} frame in {$color} {$material}, balanced for all-day comfort and designed to flatter a wide range of face shapes.</p>";
        $rxText = $rx ? '<p>Available with prescription lenses — choose single vision, blue-light, anti-glare, photochromic or progressive at checkout, then enter or upload your prescription.</p>' : '';

        return $intro.$rxText.'<ul><li>Comes with a protective case and microfibre cloth</li><li>Optician-checked before dispatch</li><li>6-month frame warranty</li></ul>';
    }

    private function accessoryArt(int $k): string
    {
        $fill = ['#1d2a33', '#7a4a1f', '#2f6f8f', '#c9a24a'][$k];
        $shape = $k < 2
            ? '<rect x="220" y="230" width="360" height="150" rx="75" fill="'.$fill.'"/><rect x="236" y="246" width="328" height="118" rx="59" fill="none" stroke="#fff" stroke-opacity=".25" stroke-width="3" stroke-dasharray="8 8"/>'
            : '<rect x="300" y="170" width="90" height="230" rx="18" fill="'.$fill.'"/><rect x="320" y="140" width="50" height="40" rx="8" fill="#e8e2d8"/><rect x="420" y="250" width="170" height="150" rx="10" fill="#9fb7c8"/><path d="M420 290h170M420 330h170M420 370h170" stroke="#fff" stroke-opacity=".35"/>';

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600"><defs><radialGradient id="b" cx="50%" cy="42%" r="75%"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#f3efe9"/></radialGradient></defs><rect width="800" height="600" fill="url(#b)"/><ellipse cx="400" cy="420" rx="230" ry="14" fill="#000" opacity=".08"/>'.$shape.'</svg>';
    }
}
