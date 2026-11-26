<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoCatalogSeeder;
use Database\Seeders\RoleAndAdminSeeder;
use Database\Seeders\StoreSetupSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedStore(): void
    {
        $this->seed([RoleAndAdminSeeder::class, StoreSetupSeeder::class, DemoCatalogSeeder::class]);
    }

    protected function admin(string $role = 'super-admin'): User
    {
        return User::factory()->create(['role_id' => Role::where('slug', $role)->value('id'), 'is_active' => true]);
    }

    protected function customer(): User
    {
        return User::factory()->create(['role_id' => null, 'is_active' => true]);
    }
}
