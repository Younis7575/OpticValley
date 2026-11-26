<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndAdminSeeder::class,
            StoreSetupSeeder::class,
        ]);

        if (env('SEED_DEMO_CATALOG', true)) {
            $this->call(DemoCatalogSeeder::class);
        }
    }
}
