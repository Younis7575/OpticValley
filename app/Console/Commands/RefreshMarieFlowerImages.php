<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Product;
use App\Support\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/** Swaps Marie Flower product photos for the background-free versions without touching names, prices or stock. */
class RefreshMarieFlowerImages extends Command
{
    protected $signature = 'catalog:refresh-marie-flower-images';

    protected $description = 'Replace Marie Flower product images with transparent cut-outs';

    public function handle(): int
    {
        $src = database_path('seeders/assets/marie-flower');
        $dest = 'uploads/products/marie-flower';
        File::ensureDirectoryExists(public_path($dest));
        $n = 0;
        foreach (File::glob("$src/*.webp") as $file) {
            $code = pathinfo($file, PATHINFO_FILENAME);
            $product = Product::where('sku', "MF-$code")->first();
            if (! $product) {
                continue;
            }
            File::copy($file, public_path("$dest/$code.webp"));
            $img = $product->images()->orderBy('sort_order')->first();
            if ($img && $img->path !== "$dest/$code.webp") {
                if (str_starts_with($img->path, "$dest/")) {
                    Media::delete($img->path);
                }
                $img->update(['path' => "$dest/$code.webp"]);
            } elseif (! $img) {
                $product->images()->create(['path' => "$dest/$code.webp", 'alt' => $product->name, 'sort_order' => 0]);
            }
            $n++;
        }
        Banner::where('image', 'like', "$dest/%.jpg")->get()->each(fn ($b) => $b->update(['image' => preg_replace('/\.jpg$/', '.webp', $b->image)]));
        $this->info("Updated $n product images.");

        return self::SUCCESS;
    }
}
