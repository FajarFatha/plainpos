<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
            Setting::firstOrCreate(
            ['parameter' => 'mode'],
            [
                'value' => '1',
                'keterangan' => 'Mode transaksi aktif saat ini. Merujuk ke id pada tabel mode_m (1 = Simple Mode, 2 = Complex Mode).',
            ]
        );
    }
}