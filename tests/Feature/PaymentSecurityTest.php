<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_cannot_allocate_another_students_bill(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $tahun = TahunAjaran::create([
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'aktif' => true,
        ]);
        $guru = Guru::create([
            'nip' => 'G001',
            'nama' => 'Guru Tes',
            'jenis_kelamin' => 'L',
            'aktif' => true,
        ]);
        $rayon = Rayon::create(['nama' => 'Rayon Tes', 'guru_id' => $guru->id]);
        $rombel = Rombel::create(['nama' => 'TJKT X-1', 'tingkat' => 'X']);
        $siswaA = Siswa::create(['nis' => '260001', 'nama' => 'Siswa A', 'jenis_kelamin' => 'L', 'status' => 'aktif']);
        $siswaB = Siswa::create(['nis' => '260002', 'nama' => 'Siswa B', 'jenis_kelamin' => 'P', 'status' => 'aktif']);

        $akademikB = SiswaAkademik::create([
            'siswa_id' => $siswaB->id,
            'tahun_ajaran_id' => $tahun->id,
            'tingkat' => 'X',
            'rombel_id' => $rombel->id,
            'rayon_id' => $rayon->id,
            'status' => 'aktif',
        ]);

        $tagihanB = TagihanInfak::create([
            'siswa_akademik_id' => $akademikB->id,
            'periode' => '2026-07',
            'nominal' => 50000,
            'status' => 'belum',
        ]);

        $this->actingAs($admin)
            ->post('/admin/pembayaran', [
                'siswa_id' => $siswaA->id,
                'tanggal' => '2026-07-10',
                'nominal' => 50000,
                'metode_pembayaran' => Pembayaran::METODE_CASH,
                'tagihan_infak_ids' => [$tagihanB->id],
            ])
            ->assertForbidden();
    }
}
