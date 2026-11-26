<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role_id', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function wishlist(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role_id !== null && $this->is_active;
    }

    public function isCustomer(): bool
    {
        return $this->role_id === null;
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }
        $perms = $this->role->permissions ?? [];

        return in_array('*', $perms, true) || in_array($permission, $perms, true);
    }
}
