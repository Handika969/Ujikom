<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\Member;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Administrator Sistem',
            'username' => 'admin',
            'password' => 'password',
            'role' => 'admin',
            'status_aktif' => 1,
        ]);
        User::create([
            'nama_lengkap' => 'Petugas Gate',
            'username' => 'petugas',
            'password' => 'password',
            'role' => 'petugas',
            'status_aktif' => 1,
        ]);
        User::create([
            'nama_lengkap' => 'Owner Parkir',
            'username' => 'owner',
            'password' => 'password',
            'role' => 'owner',
            'status_aktif' => 1,
        ]);

        Tarif::create(['jenis_kendaraan' => 'motor', 'tarif_per_jam' => 2000]);
        Tarif::create(['jenis_kendaraan' => 'mobil', 'tarif_per_jam' => 5000]);
        AreaParkir::create(['nama_area' => 'Gerbang Utama', 'kapasitas' => 50, 'terisi' => 0]);

        $member = Member::create([
            'nama_member' => 'Andi Member',
            'username_member' => 'member',
            'password_member' => Hash::make('password'),
            'no_hp' => '081234567890',
            'alamat' => 'Bandung',
            'saldo' => 100000,
            'status_aktif' => 1,
        ]);
        Kendaraan::create([
            'plat_nomor' => 'D1234ABC',
            'jenis_kendaraan' => 'motor',
            'pemilik' => 'Andi Member',
            'id_member' => $member->id_member,
        ]);
    }
}
