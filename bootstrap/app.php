<?php

use App\Http\Middleware\AdminPermission;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(SecurityHeaders::class);
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'can.admin' => AdminPermission::class,
        ]);
        $middleware->redirectGuestsTo(fn (Request $r) => $r->is('admin', 'admin/*') ? route('admin.login') : route('login'));
        $middleware->redirectUsersTo(fn (Request $r) => $r->user()?->isAdmin() && $r->is('admin/*') ? route('admin.dashboard') : route('account.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
