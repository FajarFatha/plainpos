<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            SuperAdminSeeder::class,
            MenuSeeder::class,
            GroupMenuSeeder::class,
            ModeSeeder::class,
            SettingSeeder::class,
            SettingMenuSeeder::class,
        ]);
    }
}