<?php
// database/seeders/GroupSeeder.php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['nama_group' => 'superadmin', 'keterangan' => 'Akses penuh ke seluruh sistem'],
            ['nama_group' => 'admin',      'keterangan' => 'Pengelola operasional harian'],
            ['nama_group' => 'kasir',      'keterangan' => 'Transaksi penjualan (POS)'],
            ['nama_group' => 'gudang',     'keterangan' => 'Pengelola stok/inventori'],
        ];

        foreach ($groups as $group) {
            Group::firstOrCreate(['nama_group' => $group['nama_group']], $group);
        }
    }
}