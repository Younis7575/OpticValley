<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Services\Cart;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Cart::class);
    }

    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        RateLimiter::for('auth', fn (Request $r) => Limit::perMinute(8)->by($r->ip()));
        RateLimiter::for('forms', fn (Request $r) => Limit::perMinute(10)->by($r->ip()));
        RateLimiter::for('checkout', fn (Request $r) => Limit::perMinute(6)->by($r->ip()));

        View::composer('layouts.store', function ($view) {
            $view->with('navCategories', Category::active()
                ->whereNull('parent_id')->where('show_in_menu', true)->with(['children' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')->get());
            $view->with('menus', MenuItem::where('is_active', true)->orderBy('sort_order')->get()->groupBy('location'));
            $view->with('cartCount', app(Cart::class)->count());
        });
    }
}
