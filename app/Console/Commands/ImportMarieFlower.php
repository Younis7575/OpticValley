<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Support\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Replaces the starter catalogue with the Marie Flower frame collection.
 * Order history is kept: order lines store their own name/SKU/price.
 */
class ImportMarieFlower extends Command
{
    protected $signature = 'catalog:import-marie-flower {--keep-old : Do not delete existing products}';

    protected $description = 'Remove existing products and import the Marie Flower eyeglasses collection';

    /** code => [name suffix, shape, colour, gender] */
    private const FRAMES = [
        '4021' => ['Classic Rectangle', 'rectangle', 'brown', 'men'],
        '9508' => ['Soft Oval', 'oval', 'grey', 'men'],
        '6168' => ['Crystal Rectangle', 'rectangle', 'grey', 'men'],
        '6169' => ['Bold Rectangle', 'rectangle', 'grey', 'men'],
        '822' => ['Clear Slim Rectangle', 'rectangle', 'clear', 'men'],
        '1802' => ['Royal Blue Rectangle', 'rectangle', 'blue', 'men'],
        '1809' => ['Graphite Rectangle', 'rectangle', 'black', 'men'],
        '3021' => ['Amber Round', 'round', 'brown', 'men'],
        '9840' => ['Mocha Rectangle', 'rectangle', 'brown', 'men'],
        '8003' => ['Burgundy Rectangle', 'rectangle', 'burgundy', 'men'],
        '6210' => ['Smoke Round', 'round', 'grey', 'men'],
        '9004' => ['Honey Rectangle', 'rectangle', 'honey', 'men'],
        '9005' => ['Carbon Rectangle', 'rectangle', 'black', 'men'],
        '9509' => ['Silver Mist Round', 'round', 'grey', 'men'],
        '9511' => ['Cocoa Rectangle', 'rectangle', 'brown', 'men'],
        '9512' => ['Slate Square', 'square', 'grey', 'men'],
        '9513' => ['Onyx Square', 'square', 'black', 'men'],
        'A013' => ['Kids Espresso Rectangle', 'rectangle', 'brown', 'kids'],
        'A021' => ['Kids Crystal Round', 'round', 'clear', 'kids'],
        'A019' => ['Kids Charcoal Round', 'round', 'black', 'kids'],
    ];

    public function handle(): int
    {
        $src = database_path('seeders/assets/marie-flower');
        $dest = 'uploads/products/marie-flower';
        File::ensureDirectoryExists(public_path($dest));

        DB::transaction(function () use ($src, $dest) {
            if (! $this->option('keep-old')) {
                $old = Product::with('images')->get();
                foreach ($old as $p) {
                    foreach ($p->images as $img) {
                        Media::delete($img->path);
                    }
                    $p->delete(); // reviews, images, variants cascade; order lines keep their copy
                }
                Brand::doesntHave('products')->delete();
                $this->info("Removed {$old->count()} old products.");
            }

            $brand = Brand::updateOrCreate(['slug' => 'marie-flower'], [
                'name' => 'Marie Flower', 'is_active' => true, 'is_featured' => true,
                'description' => 'Lightweight everyday optical frames with a 180° flexible spring hinge.',
            ]);
            $men = Category::where('slug', 'mens-eyeglasses')->value('id');
            $kids = Category::where('slug', 'kids-eyeglasses')->value('id');
            $rx = Category::where('slug', 'prescription-glasses')->value('id');

            $i = 0;
            foreach (self::FRAMES as $code => [$title, $shape, $colour, $gender]) {
                $kidsFrame = $gender === 'kids';
                File::copy("$src/$code.webp", public_path("$dest/$code.webp"));
                $name = "Marie Flower $code — $title";
                $p = Product::updateOrCreate(['sku' => "MF-$code"], [
                    'name' => $name,
                    'slug' => 'marie-flower-'.strtolower($code),
                    'brand_id' => $brand->id,
                    'category_id' => $kidsFrame ? $kids : $men,
                    'short_description' => ucfirst($shape).' '.($kidsFrame ? 'kids' : "men's").' optical frame in '.$colour.', with a 180° flexible spring hinge. Ready for your prescription.',
                    'description' => '<p>The <strong>Marie Flower '.$code.'</strong> is a lightweight '.$shape.' frame designed for all-day comfort. Its 180° single spring hinge ("qabza") flexes outward for a secure, pressure-free fit and resists breakage.</p>'
                        .'<p>Order it as a frame only, or add single vision, blue-light, anti-glare, photochromic or progressive lenses fitted to your prescription.</p>'
                        .'<ul><li>Full-rim '.$shape.' design</li><li>180° flexible spring hinge</li><li>Comfortable adjustable nose area</li><li>Comes with a protective case and cleaning cloth</li><li>Optician-checked before dispatch</li></ul>',
                    'price' => $kidsFrame ? 1500 : 2000,
                    'sale_price' => null,
                    'stock' => 10,
                    'low_stock_threshold' => 3,
                    'gender' => $gender,
                    'frame_shape' => $shape,
                    'frame_type' => 'full-rim',
                    'frame_color' => $colour,
                    'frame_size' => $kidsFrame ? 'Kids' : 'Regular',
                    'lens_info' => 'Demo lenses included — replaced with your prescription lenses',
                    'specifications' => ['Model' => $code, 'Hinge' => '180° single spring hinge', 'Rim' => 'Full rim', 'Warranty' => '6 months frame warranty'],
                    'prescription_enabled' => true,
                    'is_active' => true,
                    'is_featured' => $i < 8,
                    'is_best_seller' => in_array($code, ['4021', '1802', '3021', '9513', '6210', '8003'], true),
                    'is_new' => $i >= 12,
                ]);
                $p->categories()->sync(array_filter([$rx]));
                $p->images()->delete();
                $p->images()->create(['path' => "$dest/$code.webp", 'alt' => "$name eyeglasses frame", 'sort_order' => 0]);
                $i++;
            }

            // Fresh hero / promo artwork from the real collection.
            $heroes = Banner::where('position', 'hero')->orderBy('sort_order')->get();
            foreach ($heroes as $k => $b) {
                $b->update(['image' => "$dest/".(['1802', '3021'][$k % 2]).'.webp']);
            }
            foreach (Banner::where('position', 'promo')->orderBy('sort_order')->get() as $k => $b) {
                $b->update(['image' => "$dest/".(['9513', '8003', 'A021'][$k % 3]).'.webp']);
            }
        });

        $this->info('Imported '.count(self::FRAMES).' Marie Flower frames.');

        return self::SUCCESS;
    }
}
