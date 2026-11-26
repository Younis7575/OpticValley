<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'permissions'];

    protected $casts = ['permissions' => 'array'];

    /** Every permission the admin panel checks, grouped for the role editor. */
    public const PERMISSIONS = [
        'dashboard' => 'View dashboard & analytics',
        'products' => 'Manage products, brands, categories & lenses',
        'orders' => 'Manage orders, refunds & invoices',
        'customers' => 'Manage customers',
        'marketing' => 'Manage coupons, banners & subscribers',
        'content' => 'Manage pages, blog, menus & reviews',
        'settings' => 'Manage store settings',
        'users' => 'Manage admin users & roles',
        'logs' => 'View activity logs',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
