<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(
            ['nama_branch' => 'Cabang Utama'],
            [
                'lokasi' => 'Lokasi Utama',
                'is_active' => true,
            ]
        );
    }
}