<?php

namespace Database\Seeders;

use App\Models\Mode;
use Illuminate\Database\Seeder;

class ModeSeeder extends Seeder
{
    public function run(): void
    {
        $modes = [
            [
                'mode' => 'Simple Mode',
                'penjelasan' => 'Transaksi tidak memotong stok. Hanya mencatat data transaksi dan laporan penjualan, tanpa ketergantungan pada data stok/inventori.',
                'is_active' => true,
            ],
            [
                'mode' => 'Complex Mode',
                'penjelasan' => 'Setiap transaksi mengurangi stok secara otomatis dan tercatat di kartu stok. Cocok untuk usaha yang memerlukan pelacakan inventori penuh.',
                'is_active' => true,
            ],
        ];

        foreach ($modes as $mode) {
            Mode::firstOrCreate(['mode' => $mode['mode']], $mode);
        }
    }
}