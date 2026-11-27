<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Adds the Baby Grip (by Jaddah) kids TR frames. Existing products are never
 * touched; re-running only creates missing items and refreshes their photos.
 */
class ImportBabyGrip extends Command
{
    protected $signature = 'catalog:import-baby-grip';

    protected $description = 'Add Baby Grip kids eyeglasses frames';

    /** code => [title, shape, colour, size (lens-bridge-temple)] */
    private const FRAMES = [
        'TR25001' => ['Black & Red Round', 'round', 'black', '45-16-130'],
        'TR25002' => ['Smoke Grey Square', 'square', 'grey', '44-16-130'],
        'TR25004' => ['Cherry Red Square', 'square', 'red', '45-16-130'],
        'TR25005' => ['Black & Blue Wayfarer', 'wayfarer', 'black', '44-18-130'],
        'TR25007' => ['Black & Blue Round', 'round', 'black', '43-17-130'],
        'TR25008' => ['Sky Blue Rectangle', 'rectangle', 'blue', '48-15-130'],
        'TR25011' => ['Crystal Clear Round', 'round', 'clear', '38-17-130'],
        'TR25012' => ['Blush Pink Square', 'square', 'pink', '48-18-130'],
    ];

    public function handle(): int
    {
        $src = database_path('seeders/assets/baby-grip');
        $dest = 'uploads/products/baby-grip';
        File::ensureDirectoryExists(public_path($dest));
        $created = 0;

        DB::transaction(function () use ($src, $dest, &$created) {
            $brand = Brand::updateOrCreate(['slug' => 'baby-grip'], [
                'name' => 'Baby Grip', 'is_active' => true, 'is_featured' => true,
                'description' => 'Baby Grip by Jaddah — soft, flexible TR frames made for active kids.',
            ]);
            $kids = Category::where('slug', 'kids-eyeglasses')->value('id');
            $rx = Category::where('slug', 'prescription-glasses')->value('id');

            foreach (self::FRAMES as $code => [$title, $shape, $colour, $size]) {
                File::copy("$src/$code.webp", public_path("$dest/$code.webp"));
                $name = "Baby Grip $code — $title";
                $product = Product::firstOrCreate(['sku' => "BG-$code"], [
                    'name' => $name,
                    'slug' => 'baby-grip-'.strtolower($code),
                    'brand_id' => $brand->id,
                    'category_id' => $kids,
                    'short_description' => "Flexible TR $shape kids frame in $colour (size $size). Soft, light and tough enough for everyday play — ready for prescription lenses.",
                    'description' => "<p>The <strong>Baby Grip $code</strong> by Jaddah is a lightweight, flexible TR frame designed for children. It bends instead of breaking, sits comfortably all day and keeps lenses secure during play.</p>"
                        .'<p>Order it as a frame only, or add single vision, blue-light, anti-glare or photochromic lenses made to your child\'s prescription.</p>'
                        ."<ul><li>Flexible, near-unbreakable TR material</li><li>Size $size (lens – bridge – temple, mm)</li><li>Soft comfortable fit for kids</li><li>Comes with a protective case and cleaning cloth</li><li>Optician-checked before dispatch</li></ul>",
                    'price' => 1500,
                    'stock' => 10,
                    'low_stock_threshold' => 3,
                    'gender' => 'kids',
                    'frame_shape' => $shape,
                    'frame_type' => 'full-rim',
                    'frame_material' => 'tr90',
                    'frame_color' => $colour,
                    'frame_size' => $size,
                    'lens_info' => 'Demo lenses included — replaced with your prescription lenses',
                    'specifications' => ['Model' => $code, 'Size' => "$size mm", 'Material' => 'Flexible TR', 'Rim' => 'Full rim', 'Warranty' => '6 months frame warranty'],
                    'prescription_enabled' => true,
                    'is_active' => true,
                    'is_featured' => true,
                    'is_new' => true,
                ]);
                if ($product->wasRecentlyCreated) {
                    $created++;
                    $product->categories()->sync(array_filter([$rx]));
                }
                if (File::exists(database_path("seeders/assets/baby-grip-tryon/$code.png"))) {
                    File::ensureDirectoryExists(public_path('uploads/tryon'));
                    File::copy(database_path("seeders/assets/baby-grip-tryon/$code.png"), public_path("uploads/tryon/$code.png"));
                    if (! $product->tryon_overlay) {
                        $product->update(['tryon_enabled' => true, 'tryon_overlay' => "uploads/tryon/$code.png", 'tryon_scale' => 1.0]);
                    }
                }
                $img = $product->images()->orderBy('sort_order')->first();
                $img ? $img->update(['path' => "$dest/$code.webp"])
                    : $product->images()->create(['path' => "$dest/$code.webp", 'alt' => "$name kids eyeglasses frame", 'sort_order' => 0]);
            }
        });

        $this->info("Baby Grip: $created new products added, photos refreshed for ".count(self::FRAMES).'.');

        return self::SUCCESS;
    }
}
