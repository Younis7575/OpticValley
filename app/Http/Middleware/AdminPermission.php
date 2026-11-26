<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->hasPermission($permission)) {
            if ($permission === 'dashboard' && $request->user()?->isAdmin()) {
                // Staff without dashboard access land on the first section they can use.
                foreach (['orders' => 'admin.orders.index', 'products' => 'admin.products.index', 'content' => 'admin.pages.index', 'marketing' => 'admin.coupons.index'] as $perm => $route) {
                    if ($request->user()->hasPermission($perm)) {
                        return redirect()->route($route);
                    }
                }
            }
            abort(403, 'Your role does not allow access to this section.');
        }

        return $next($request);
    }
}
