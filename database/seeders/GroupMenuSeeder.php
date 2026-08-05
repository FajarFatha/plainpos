<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = Group::where('nama_group', 'superadmin')->first();

        if (! $superadmin) {
            return;
        }

        $allMenuIds = Menu::pluck('id');

        $rows = $allMenuIds->map(fn ($menuId) => [
            'group_id' => $superadmin->id,
            'menu_id' => $menuId,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
        
        foreach ($rows as $row) {
            \DB::table('group_menu')->updateOrInsert(
                ['group_id' => $row['group_id'], 'menu_id' => $row['menu_id']],
                $row
            );
        }
    }
}