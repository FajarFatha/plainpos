<?php
// database/seeders/SuperAdminSeeder.php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = Pegawai::firstOrCreate(
            ['nip' => '0000000001'],
            [
                'nama_pegawai' => 'Super Administrator',
                'jenis_kelamin' => 'L',
                'branch' => 1,
                'is_active' => true,
            ]
        );

        $groupSuperadmin = Group::where('nama_group', 'superadmin')->first();

        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'pegawaifk' => $pegawai->id,
                'groupfk' => $groupSuperadmin->id,
                'password' => Hash::make('password'),
                'is_superadmin' => true,
                'is_active' => true,
            ]
        );
    }
}