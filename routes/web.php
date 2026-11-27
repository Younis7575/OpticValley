<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('/brand/{brand:slug}', [ShopController::class, 'brand'])->name('brand.show');
Route::get('/product/{product:slug}', [ShopController::class, 'product'])->name('product.show');
Route::get('/product/{product:slug}/quick', [ShopController::class, 'quick'])->name('product.quick');
Route::get('/search/suggest', [ShopController::class, 'suggest'])->middleware('throttle:60,1')->name('search.suggest');
Route::get('/virtual-try-on', [PageController::class, 'tryOn'])->name('tryon');
Route::post('/product/{product:slug}/reviews', [ShopController::class, 'review'])->middleware('throttle:forms')->name('product.review');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->middleware('throttle:forms')->name('cart.coupon');
Route::delete('/cart-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
Route::get('/order/{order:order_number}/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thanks');

Route::get('/track-order', [PageController::class, 'track'])->name('track');
Route::post('/track-order', [PageController::class, 'trackLookup'])->middleware('throttle:forms')->name('track.lookup');

Route::post('/wishlist/{product:slug}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

Route::get('/pages/{slug}', [PageController::class, 'page'])->name('page.show');
Route::get('/journal', [PageController::class, 'blog'])->name('blog.index');
Route::get('/journal/{post:slug}', [PageController::class, 'post'])->name('blog.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSend'])->middleware('throttle:forms')->name('contact.send');
Route::post('/newsletter', [PageController::class, 'newsletter'])->middleware('throttle:forms')->name('newsletter');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots']);

/*
|--------------------------------------------------------------------------
| Customer auth & account
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendReset'])->middleware('throttle:auth')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:auth')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order:order_number}', [AccountController::class, 'order'])->name('order');
    Route::post('/orders/{order:order_number}/cancel', [AccountController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:auth');
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->middleware('can.admin:dashboard')->name('dashboard');

        Route::middleware('can.admin:products')->group(function () {
            Route::resource('products', Admin\ProductController::class)->except('show');
            Route::delete('product-images/{image}', [Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
            Route::resource('categories', Admin\CategoryController::class)->except('show');
            Route::resource('brands', Admin\BrandController::class)->except('show');
            Route::resource('lens-options', Admin\LensOptionController::class)->except('show')->parameters(['lens-options' => 'lensOption']);
        });

        Route::middleware('can.admin:orders')->group(function () {
            Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
            Route::put('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
            Route::post('orders/{order}/refund', [Admin\OrderController::class, 'refund'])->name('orders.refund');
            Route::get('orders/{order}/invoice', [Admin\OrderController::class, 'invoice'])->name('orders.invoice');
            Route::get('orders/{order}/items/{item}/prescription', [Admin\OrderController::class, 'prescription'])->name('orders.prescription');
            Route::get('orders-export', [Admin\OrderController::class, 'export'])->name('orders.export');
        });

        Route::middleware('can.admin:customers')->group(function () {
            Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');
            Route::put('customers/{customer}', [Admin\CustomerController::class, 'update'])->name('customers.update');
        });

        Route::middleware('can.admin:marketing')->group(function () {
            Route::resource('coupons', Admin\CouponController::class)->except('show');
            Route::resource('banners', Admin\BannerController::class)->except('show');
            Route::get('subscribers', [Admin\SubscriberController::class, 'index'])->name('subscribers.index');
            Route::delete('subscribers/{subscriber}', [Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
            Route::get('subscribers-export', [Admin\SubscriberController::class, 'export'])->name('subscribers.export');
        });

        Route::middleware('can.admin:content')->group(function () {
            Route::resource('pages', Admin\PageController::class)->except('show');
            Route::resource('posts', Admin\PostController::class)->except('show');
            Route::resource('menus', Admin\MenuItemController::class)->except('show')->parameters(['menus' => 'menuItem']);
            Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
            Route::put('reviews/{review}', [Admin\ReviewController::class, 'update'])->name('reviews.update');
            Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
            Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
        });

        Route::middleware('can.admin:settings')->group(function () {
            Route::get('settings/{group?}', [Admin\SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings/{group}', [Admin\SettingController::class, 'update'])->name('settings.update');
        });

        Route::middleware('can.admin:users')->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');
            Route::resource('roles', Admin\RoleController::class)->except('show');
        });

        Route::get('activity', [Admin\ActivityController::class, 'index'])->middleware('can.admin:logs')->name('activity.index');
        Route::get('profile', [Admin\AuthController::class, 'profile'])->name('profile');
        Route::put('profile', [Admin\AuthController::class, 'updateProfile'])->name('profile.update');
    });
});
