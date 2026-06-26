<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Infak#2026');

        $admin = User::updateOrCreate(
            ['email' => 'admin@infak.test'],
            ['name' => 'Admin Utama', 'password' => $password, 'role' => User::ROLE_ADMIN]
        );

        $petugas = User::updateOrCreate(
            ['email' => 'petugas@infak.test'],
            ['name' => 'Petugas Infak', 'password' => $password, 'role' => User::ROLE_PETUGAS]
        );

        $pembimbingUser = User::updateOrCreate(
            ['email' => 'pembimbing@infak.test'],
            ['name' => 'Pak Ardi Ariansyah', 'password' => $password, 'role' => User::ROLE_PEMBIMBING]
        );

        $siswaUser = User::updateOrCreate(
            ['email' => 'siswa@infak.test'],
            ['name' => 'Siswa Demo', 'password' => $password, 'role' => User::ROLE_ORANG_TUA]
        );

        $guru = Guru::updateOrCreate(
            ['nip' => 'DUMMY001'],
            [
                'user_id' => $pembimbingUser->id,
                'nama' => 'Pak Ardi Ariansyah',
                'jenis_kelamin' => 'L',
                'email' => 'pembimbing@infak.test',
                'aktif' => true,
            ]
        );

        $rayon = Rayon::updateOrCreate(
            ['nama' => 'Cicurug 2'],
            ['guru_id' => $guru->id]
        );

        $rombel = Rombel::updateOrCreate(
            ['nama' => 'TJKT X-1'],
            ['tingkat' => 'X']
        );

        $tahun = TahunAjaran::updateOrCreate(
            ['nama' => '2026/2027'],
            [
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'aktif' => true,
            ]
        );

        $siswa = Siswa::updateOrCreate(
            ['nis' => '2600001'],
            [
                'user_id' => $siswaUser->id,
                'nama' => 'Siswa Demo',
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]
        );

        $siswa->akademik()->updateOrCreate(
            ['tahun_ajaran_id' => $tahun->id],
            [
                'tingkat' => 'X',
                'rombel_id' => $rombel->id,
                'rayon_id' => $rayon->id,
                'status' => 'aktif',
            ]
        );

        $this->command?->info('Akun dummy dibuat. Password semua akun: Infak#2026');
        $this->command?->line('admin@infak.test | petugas@infak.test | pembimbing@infak.test | siswa@infak.test');
    }
}
