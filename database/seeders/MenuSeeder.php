<?php
// database/seeders/MenuSeeder.php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {

        $dashboard = Menu::firstOrCreate(
            ['name' => 'dashboard'],
            [
                'realname' => 'Dashboard',
                'description' => 'Ringkasan aktivitas dan statistik toko',
                'route' => 'dashboard',
                'icon' => 'mdi mdi-view-dashboard',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        $produk = Menu::firstOrCreate(
            ['name' => 'produk'],
            [
                'realname' => 'Produk',
                'description' => 'Manajemen produk dan kategori',
                'route' => null,
                'icon' => 'mdi mdi-package-variant',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        $transaksi = Menu::firstOrCreate(
            ['name' => 'transaksi'],
            [
                'realname' => 'Transaksi',
                'description' => 'Transaksi penjualan (POS)',
                'route' => null,
                'icon' => 'mdi mdi-cash-register',
                'nourut' => 3,
                'is_active' => true,
            ]
        );

        $pelanggan = Menu::firstOrCreate(
            ['name' => 'pelanggan'],
            [
                'realname' => 'Pelanggan',
                'description' => 'Manajemen data pelanggan',
                'route' => 'pelanggan.index',
                'icon' => 'mdi mdi-account-group',
                'nourut' => 4,
                'is_active' => true,
            ]
        );

        $inventori = Menu::firstOrCreate(
            ['name' => 'inventori'],
            [
                'realname' => 'Inventori',
                'description' => 'Manajemen stok barang',
                'route' => null,
                'icon' => 'mdi mdi-warehouse',
                'nourut' => 5,
                'is_active' => true,
            ]
        );

        $laporan = Menu::firstOrCreate(
            ['name' => 'laporan'],
            [
                'realname' => 'Laporan',
                'description' => 'Laporan penjualan dan stok',
                'route' => null,
                'icon' => 'mdi mdi-chart-bar',
                'nourut' => 6,
                'is_active' => true,
            ]
        );

        $administrator = Menu::firstOrCreate(
            ['name' => 'administrator'],
            [
                'realname' => 'Administrator',
                'description' => 'Pengelolaan user, group, dan hak akses menu',
                'route' => null,
                'icon' => 'mdi mdi-shield-account',
                'nourut' => 7,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'produk-list'],
            [
                'head' => $produk->id,
                'realname' => 'Daftar Produk',
                'description' => 'Daftar semua produk',
                'route' => 'produk.index',
                'icon' => 'mdi mdi-format-list-bulleted',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'produk-kategori'],
            [
                'head' => $produk->id,
                'realname' => 'Kategori Produk',
                'description' => 'Manajemen kategori produk',
                'route' => 'kategori.index',
                'icon' => 'mdi mdi-shape',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'transaksi-kasir'],
            [
                'head' => $transaksi->id,
                'realname' => 'Kasir (POS)',
                'description' => 'Halaman transaksi kasir',
                'route' => 'transaksi.kasir',
                'icon' => 'mdi mdi-cash-multiple',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'transaksi-riwayat'],
            [
                'head' => $transaksi->id,
                'realname' => 'Riwayat Transaksi',
                'description' => 'Daftar transaksi yang sudah dilakukan',
                'route' => 'transaksi.index',
                'icon' => 'mdi mdi-history',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'transaksi-retur'],
            [
                'head' => $transaksi->id,
                'realname' => 'Retur Penjualan',
                'description' => 'Retur/pengembalian barang',
                'route' => 'retur.index',
                'icon' => 'mdi mdi-keyboard-return',
                'nourut' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'inventori-stok'],
            [
                'head' => $inventori->id,
                'realname' => 'Stok Barang',
                'description' => 'Monitoring stok barang',
                'route' => 'stok.index',
                'icon' => 'mdi mdi-cube-outline',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'inventori-penyesuaian'],
            [
                'head' => $inventori->id,
                'realname' => 'Penyesuaian Stok',
                'description' => 'Stock opname / penyesuaian',
                'route' => 'stok-adjustment.index',
                'icon' => 'mdi mdi-tune',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'inventori-supplier'],
            [
                'head' => $inventori->id,
                'realname' => 'Supplier',
                'description' => 'Manajemen data supplier',
                'route' => 'supplier.index',
                'icon' => 'mdi mdi-truck',
                'nourut' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'laporan-penjualan'],
            [
                'head' => $laporan->id,
                'realname' => 'Laporan Penjualan',
                'description' => 'Laporan rekap penjualan',
                'route' => 'laporan.penjualan',
                'icon' => 'mdi mdi-file-chart',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'laporan-stok'],
            [
                'head' => $laporan->id,
                'realname' => 'Laporan Stok',
                'description' => 'Laporan pergerakan stok',
                'route' => 'laporan.stok',
                'icon' => 'mdi mdi-file-document',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'laporan-laba-rugi'],
            [
                'head' => $laporan->id,
                'realname' => 'Laba Rugi',
                'description' => 'Laporan laba rugi sederhana',
                'route' => 'laporan.laba-rugi',
                'icon' => 'mdi mdi-cash-100',
                'nourut' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'admin-user'],
            [
                'head' => $administrator->id,
                'realname' => 'Mapping User',
                'description' => 'Kelola akun user dan hak akses menu',
                'route' => 'admin.mapping-user',
                'icon' => 'mdi mdi-account-cog',
                'nourut' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'admin-group'],
            [
                'head' => $administrator->id,
                'realname' => 'Mapping Group',
                'description' => 'Kelola hak akses per group',
                'route' => 'admin.mapping-group',
                'icon' => 'mdi mdi-account-multiple-outline',
                'nourut' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'admin-menu'],
            [
                'head' => $administrator->id,
                'realname' => 'Master Menu',
                'description' => 'Kelola daftar menu aplikasi',
                'route' => 'admin.menu.index',
                'icon' => 'mdi mdi-menu',
                'nourut' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'admin-pegawai'],
            [
                'head' => $administrator->id,
                'realname' => 'Data Pegawai',
                'description' => 'Kelola data pegawai',
                'route' => 'admin.pegawai.index',
                'icon' => 'mdi mdi-badge-account',
                'nourut' => 4,
                'is_active' => true,
            ]
        );
        
        [$dashboard, $pelanggan];
    }
}