<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class BranchMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Menu::where('name', 'administrator')->first();

        if (! $administrator) {
            return;
        }

        Menu::updateOrCreate(
            ['name' => 'admin-branch'],
            [
                'head' => $administrator->id,
                'realname' => 'Master Cabang',
                'description' => 'Kelola data cabang/branch',
                'route' => 'admin.branch.index',
                'icon' => 'mdi mdi-storefront-outline',
                'nourut' => 5,
                'is_active' => true,
            ]
        );
    }
}