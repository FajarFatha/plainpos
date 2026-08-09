<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class FixMappingUserRouteSeeder extends Seeder
{
    public function run(): void
    {
        Menu::where('route', 'admin.mapping-user')
            ->update(['route' => 'admin.mapping-user.index']);
    }
}