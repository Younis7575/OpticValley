<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Uploaded media lives in public/uploads (symlinked to a persistent shared
 * directory in production so deploys never wipe customer/product files).
 */
class Media
{
    public const ALLOWED_IMAGES = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    public static function url(?string $path): string
    {
        if (! $path) {
            return asset('images/placeholder.svg');
        }
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    public static function store(UploadedFile $file, string $folder): string
    {
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $dir = 'uploads/'.trim($folder, '/').'/'.now()->format('Y/m');
        File::ensureDirectoryExists(public_path($dir));
        $name = Str::random(32).'.'.$ext;
        $file->move(public_path($dir), $name);

        return $dir.'/'.$name;
    }

    public static function delete(?string $path): void
    {
        if ($path && Str::startsWith($path, 'uploads/') && ! Str::contains($path, '..')) {
            File::delete(public_path($path));
        }
    }
}
