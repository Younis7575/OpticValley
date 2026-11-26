<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $super = Role::updateOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'permissions' => ['*']]);
        Role::updateOrCreate(['slug' => 'manager'], ['name' => 'Store Manager', 'permissions' => ['dashboard', 'products', 'orders', 'customers', 'marketing', 'content']]);
        Role::updateOrCreate(['slug' => 'order-staff'], ['name' => 'Order Staff', 'permissions' => ['dashboard', 'orders', 'customers']]);
        Role::updateOrCreate(['slug' => 'content-editor'], ['name' => 'Content Editor', 'permissions' => ['content', 'marketing']]);

        $email = env('ADMIN_EMAIL', 'admin@opticvalley.co');
        if (User::where('email', $email)->exists()) {
            return;
        }
        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);
        User::create([
            'name' => 'OpticValley Admin',
            'email' => $email,
            'password' => $password,
            'role_id' => $super->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        if (! env('ADMIN_PASSWORD')) {
            if (app()->isProduction()) {
                // Never print credentials into CI/deploy logs.
                $file = storage_path('app/private/admin-credentials.txt');
                @mkdir(dirname($file), 0700, true);
                file_put_contents($file, "Admin login: {$email}\nPassword: {$password}\nDelete this file after changing the password.\n");
                @chmod($file, 0600);
                $this->command?->warn("Admin created: {$email}. Initial password saved to storage/app/private/admin-credentials.txt on the server.");
            } else {
                $this->command?->warn("Admin created: {$email} / {$password}  — change this password after first login.");
            }
        }
    }
}
