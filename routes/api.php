<?php

use App\Http\Controllers\Api\CatalogController;
use Illuminate\Support\Facades\Route;

/*
| Public, read-only catalogue API (v1) — for mobile apps, marketplaces and
| integrations. All write operations go through the authenticated admin panel.
*/
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('categories', [CatalogController::class, 'categories']);
    Route::get('brands', [CatalogController::class, 'brands']);
    Route::get('products', [CatalogController::class, 'products']);
    Route::get('products/{product:slug}', [CatalogController::class, 'product']);
    Route::get('settings', [CatalogController::class, 'settings']);
});
