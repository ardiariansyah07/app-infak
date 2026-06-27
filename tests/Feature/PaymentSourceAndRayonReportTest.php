<?php

namespace Tests\Feature;

use App\Http\Middleware\AutoGenerateMonthlyTagihan;
use App\Models\Guru;
use App\Models\KomitmenInfak;
use App\Models\OrangTua;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentSourceAndRayonReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembimbing_can_upload_payment_proof_for_a_student_in_their_rayon(): void
    {
        Storage::fake('public');
        ['pembimbing' => $pembimbing, 'siswa' => $siswa] = $this->schoolData();

        $this->actingAs($pembimbing)
            ->get('/rayon/pembayaran/create')
            ->assertOk()
            ->assertSee($siswa->nama);

        $this->actingAs($pembimbing)
            ->post('/rayon/pembayaran', [
                'siswa_id' => $siswa->id,
                'tanggal' => '2026-06-27',
                'nominal' => 50000,
                'bukti_transfer' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect('/rayon/pembayaran');

        $pembayaran = Pembayaran::firstOrFail();
        $this->assertSame(Pembayaran::SUMBER_PEMBIMBING, $pembayaran->sumber);
        $this->assertSame('pending', $pembayaran->status_verifikasi);
        $this->assertTrue($pembayaran->punyaBuktiUnggahan());
        Storage::disk('public')->assertExists($pembayaran->bukti_transfer);
    }

    public function test_admin_payment_and_import_payment_do_not_show_proof_links(): void
    {
        ['admin' => $admin, 'siswa' => $siswa] = $this->schoolData();
        $petugas = User::factory()->create(['role' => User::ROLE_PETUGAS]);

        Pembayaran::create([
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-06-27',
            'nominal' => 50000,
            'sumber' => Pembayaran::SUMBER_IMPORT_SALDO_AWAL,
            'status_verifikasi' => 'valid',
        ]);

        $this->actingAs($admin)
            ->post('/admin/pembayaran', [
                'siswa_id' => $siswa->id,
                'tanggal' => '2026-06-27',
                'nominal' => 25000,
                'metode_pembayaran' => Pembayaran::METODE_CASH,
            ])
            ->assertRedirect('/admin/pembayaran');

        $this->assertDatabaseHas('pembayaran', [
            'nominal' => 25000,
            'sumber' => Pembayaran::SUMBER_ADMIN,
            'metode_pembayaran' => Pembayaran::METODE_CASH,
            'bukti_transfer' => null,
        ]);

        $this->actingAs($petugas)
            ->post('/petugas/pembayaran', [
                'siswa_id' => $siswa->id,
                'tanggal' => '2026-06-27',
                'nominal' => 15000,
                'metode_pembayaran' => Pembayaran::METODE_TRANSFER,
            ])
            ->assertRedirect('/petugas/pembayaran');

        $this->assertDatabaseHas('pembayaran', [
            'nominal' => 15000,
            'sumber' => Pembayaran::SUMBER_PETUGAS,
            'metode_pembayaran' => Pembayaran::METODE_TRANSFER,
            'bukti_transfer' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pembayaran')
            ->assertOk()
            ->assertSee('Import saldo awal')
            ->assertSee('Input admin')
            ->assertSee('Input petugas infak')
            ->assertSee('Cash')
            ->assertSee('Transfer')
            ->assertDontSee('storage/import-saldo-awal');
    }

    public function test_admin_can_select_a_rayon_and_generate_its_student_report(): void
    {
        ['admin' => $admin, 'rayon' => $rayon, 'siswa' => $siswa] = $this->schoolData();

        $this->actingAs($admin)
            ->get('/admin/laporan?rayon_id='.$rayon->id)
            ->assertOk()
            ->assertSee('Data Siswa '.$rayon->nama)
            ->assertSee($siswa->nama);

        $response = $this->actingAs($admin)
            ->get('/admin/laporan/pdf?jenis=detail-rayon&rayon_id='.$rayon->id)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_global_table_search_uses_all_paginated_data_sources(): void
    {
        $this->withoutMiddleware(AutoGenerateMonthlyTagihan::class);

        ['admin' => $admin, 'pembimbing' => $pembimbing, 'siswa' => $siswa, 'akademik' => $akademik] = $this->schoolData();
        $orangTua = OrangTua::create([
            'nama' => 'Orang Tua Pencarian',
            'email' => 'pencarian@example.test',
            'password' => '-',
        ]);
        $siswa->orangTua()->attach($orangTua);
        KomitmenInfak::create([
            'siswa_akademik_id' => $akademik->id,
            'nominal_bulanan' => 50000,
            'mulai_bulan' => '2026-06-01',
        ]);
        $targetPayment = Pembayaran::create([
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-06-27',
            'nominal' => 50000,
            'sumber' => Pembayaran::SUMBER_ADMIN,
            'metode_pembayaran' => Pembayaran::METODE_CASH,
            'status_verifikasi' => 'valid',
        ]);
        $targetPayment->forceFill(['created_at' => '2020-01-01 00:00:00'])->save();

        for ($index = 0; $index < 101; $index++) {
            $decoy = Siswa::create([
                'nis' => '10'.str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                'nama' => 'Siswa Pengisi '.$index,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $decoyAkademik = SiswaAkademik::create([
                'siswa_id' => $decoy->id,
                'tahun_ajaran_id' => $akademik->tahun_ajaran_id,
                'tingkat' => 'X',
                'rombel_id' => $akademik->rombel_id,
                'rayon_id' => $akademik->rayon_id,
                'status' => 'aktif',
            ]);
            KomitmenInfak::create([
                'siswa_akademik_id' => $decoyAkademik->id,
                'nominal_bulanan' => 50000,
                'mulai_bulan' => '2026-06-01',
            ]);
            TagihanInfak::create([
                'siswa_akademik_id' => $decoyAkademik->id,
                'periode' => '2026-07',
                'nominal' => 50000,
                'status' => 'belum',
            ]);
            Pembayaran::create([
                'siswa_id' => $decoy->id,
                'tanggal' => '2026-07-01',
                'nominal' => 50000,
                'sumber' => Pembayaran::SUMBER_ADMIN,
                'metode_pembayaran' => Pembayaran::METODE_CASH,
                'status_verifikasi' => 'valid',
            ]);
        }

        foreach (['/admin/komitmen-infak', '/admin/tagihan', '/admin/pembayaran', '/admin/status-pembayaran'] as $url) {
            $this->actingAs($admin)
                ->get($url)
                ->assertOk()
                ->assertDontSee($siswa->nama);

            $this->actingAs($admin)
                ->get($url.'?q='.$siswa->nis)
                ->assertOk()
                ->assertSee($siswa->nama);
        }

        foreach (['/admin/orang-tua'] as $url) {
            $this->actingAs($admin)
                ->get($url.'?q='.$siswa->nis)
                ->assertOk()
                ->assertSee($siswa->nama);

            $this->actingAs($admin)
                ->get($url.'?q=data-yang-tidak-ada')
                ->assertOk()
                ->assertDontSee($siswa->nama);
        }

        $this->actingAs($pembimbing)
            ->get('/rayon/pembayaran?q='.$siswa->nis)
            ->assertOk()
            ->assertSee($siswa->nama);

        $this->actingAs($admin)
            ->get('/admin/siswa')
            ->assertOk()
            ->assertDontSee('NIS, nama, rayon, atau rombel')
            ->assertSee('data-server-paginated="true"', false);
    }

    private function schoolData(): array
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $pembimbing = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);
        $guru = Guru::create([
            'user_id' => $pembimbing->id,
            'nip' => 'G001',
            'nama' => 'Guru Rayon',
            'jenis_kelamin' => 'L',
            'aktif' => true,
        ]);
        $rayon = Rayon::create(['nama' => 'Rayon Cicurug 1', 'guru_id' => $guru->id]);
        $rombel = Rombel::create(['nama' => 'X PPLG 1', 'tingkat' => 'X']);
        $tahun = TahunAjaran::create([
            'nama' => '2025/2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'aktif' => true,
        ]);
        $siswa = Siswa::create([
            'nis' => '1260001',
            'nama' => 'Siswa Rayon',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);
        $akademik = SiswaAkademik::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahun->id,
            'tingkat' => 'X',
            'rombel_id' => $rombel->id,
            'rayon_id' => $rayon->id,
            'status' => 'aktif',
        ]);
        TagihanInfak::create([
            'siswa_akademik_id' => $akademik->id,
            'periode' => '2026-06',
            'nominal' => 50000,
            'status' => 'belum',
        ]);

        return compact('admin', 'pembimbing', 'guru', 'rayon', 'rombel', 'tahun', 'siswa', 'akademik');
    }
}
