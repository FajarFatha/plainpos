<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class SettingMenuSeeder extends Seeder
{
    public function run(): void
    {
        $setting = Menu::updateOrCreate(
            ['name' => 'setting'],
            [
                'realname' => 'Setting',
                'description' => 'Pengaturan umum aplikasi',
                'route' => null,
                'icon' => 'mdi mdi-cog-outline',
                'nourut' => 8,
                'is_active' => true,
            ]
        );

        Menu::updateOrCreate(
            ['name' => 'setting-mode'],
            [
                'head' => $setting->id,
                'realname' => 'Mode',
                'description' => 'Pengaturan mode transaksi (Simple/Complex)',
                'route' => 'admin.setting.mode',
                'icon' => 'mdi mdi-swap-horizontal',
                'nourut' => 1,
                'is_active' => true,
            ]
        );
    }
}