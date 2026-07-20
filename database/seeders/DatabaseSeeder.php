<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MenuPermissionSeeder::class,
            DokumenAdministrasiSeeder::class,
            SystemContentSeeder::class,
            ThemeSettingSeeder::class,
            DummyUserSeeder::class,
            DummyBukuSeeder::class,
        ]);
    }
}
