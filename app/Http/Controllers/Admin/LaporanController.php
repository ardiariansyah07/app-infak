<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Support\Periode;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->reportData($request);

        return view('admin.laporan.index', $data);
    }

    public function pdf(Request $request): Response
    {
        $data = $this->reportData($request);
        $jenis = $request->string('jenis', 'ringkasan')->toString();
        abort_unless(in_array($jenis, ['ringkasan', 'rayon', 'detail-rayon', 'tunggakan', 'tunggakan-rayon'], true), 404);

        $titles = [
            'ringkasan' => 'Laporan Ringkasan Infak Sekolah',
            'rayon' => 'Laporan Rekap Infak Per Rayon',
            'detail-rayon' => 'Laporan Infak Siswa Per Rayon',
            'tunggakan' => 'Laporan Tagihan Belum Lunas',
            'tunggakan-rayon' => 'Laporan Tunggakan Siswa Per Rayon',
        ];

        $pdf = new SimplePdf($titles[$jenis]);

        $pdf->heading($titles[$jenis]);
        $pdf->line('Periode: '.($data['periode'] !== '' ? Periode::label($data['periode']) : 'Semua periode'));
        if ($jenis === 'detail-rayon') {
            abort_unless($data['selectedRayon'], 422, 'Pilih rayon yang akan dicetak.');
            $pdf->line('Rayon: '.$data['selectedRayon']->nama);
            $pdf->line('Pembimbing: '.($data['selectedRayon']->guru?->nama ?? '-'));
        }
        $pdf->line('Tanggal cetak: '.now()->format('d/m/Y H:i'));
        $pdf->space();

        if ($jenis === 'ringkasan') {
            $pdf->subheading('Ringkasan');
            $pdf->tableRow(['Total Tagihan', $this->rupiah($data['totalTagihan'])], [180, 180]);
            $pdf->tableRow(['Pembayaran Valid', $this->rupiah($data['pembayaranValid'])], [180, 180]);
            $pdf->tableRow(['Estimasi Tunggakan', $this->rupiah($data['tunggakan'])], [180, 180]);
            $pdf->tableRow(['Pending Validasi', $data['pembayaranPending'].' pembayaran'], [180, 180]);
            $pdf->space();

            $pdf->subheading('Status Tagihan');
            $pdf->tableRow(['Status', 'Jumlah', 'Nominal'], [120, 80, 160], true);
            foreach ($data['statusTagihan'] as $status) {
                $pdf->tableRow([ucfirst($status->status), $status->total, $this->rupiah($status->nominal)], [120, 80, 160]);
            }
        }

        if ($jenis === 'rayon') {
            $pdf->subheading('Rekap Per Rayon');
            $pdf->tableRow(['Rayon', 'Pembimbing', 'Siswa', 'Tagihan', 'Valid', 'Tunggakan'], [80, 110, 45, 85, 85, 85], true);
            foreach ($data['rekapRayon'] as $rayon) {
                $pdf->tableRow([
                    $rayon['nama'],
                    $rayon['pembimbing'],
                    $rayon['siswa'],
                    $this->rupiah($rayon['tagihan']),
                    $this->rupiah($rayon['pembayaran']),
                    $this->rupiah($rayon['tunggakan']),
                ], [80, 110, 45, 85, 85, 85]);
            }
        }

        if ($jenis === 'detail-rayon') {
            $pdf->subheading('Data Siswa '.$data['selectedRayon']->nama);
            $pdf->tableRow(['NIS', 'Siswa', 'Rombel', 'Tagihan', 'Terbayar', 'Tunggakan'], [65, 130, 80, 90, 90, 90], true);
            foreach ($data['siswaRayon'] as $siswa) {
                $pdf->tableRow([
                    $siswa['nis'],
                    $siswa['nama'],
                    $siswa['rombel'],
                    $this->rupiah($siswa['tagihan']),
                    $this->rupiah($siswa['pembayaran']),
                    $this->rupiah($siswa['tunggakan']),
                ], [65, 130, 80, 90, 90, 90]);
            }
        }

        if ($jenis === 'tunggakan') {
            $pdf->subheading('Tagihan Belum Lunas');
            $pdf->tableRow(['Periode', 'Siswa', 'Rombel', 'Rayon', 'Nominal', 'Status'], [80, 120, 75, 85, 85, 65], true);
            foreach ($data['siswaMenunggak'] as $tagihan) {
                $pdf->tableRow([
                    Periode::label($tagihan->periode),
                    $tagihan->siswaAkademik?->siswa?->nama ?? '-',
                    $tagihan->siswaAkademik?->rombel?->nama ?? '-',
                    $tagihan->siswaAkademik?->rayon?->nama ?? '-',
                    $this->rupiah($tagihan->nominal),
                    $tagihan->status,
                ], [80, 120, 75, 85, 85, 65]);
            }
        }

        if ($jenis === 'tunggakan-rayon') {
            $pdf->subheading('Tunggakan Siswa Per Rayon');
            $pdf->tableRow(['Rayon', 'NIS', 'Siswa', 'Rombel', 'Sudah', 'Belum', 'Nominal'], [70, 65, 130, 65, 45, 45, 85], true);
            foreach ($data['tunggakanPerRayon'] as $siswa) {
                $pdf->tableRow([
                    $siswa->rayon_nama,
                    $siswa->nis,
                    $siswa->nama,
                    $siswa->rombel_nama,
                    $siswa->bulan_lunas,
                    $siswa->bulan_belum,
                    $this->rupiah($siswa->nominal_tunggakan),
                ], [70, 65, 130, 65, 45, 45, 85]);
            }
        }

        $rayonSuffix = $data['selectedRayon'] ? '-rayon-'.$data['selectedRayon']->id : '';
        $filename = 'laporan-infak-'.$jenis.$rayonSuffix.($data['periode'] !== '' ? '-'.$data['periode'] : '').'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function reportData(Request $request): array
    {
        $periode = $request->string('periode')->toString();
        $rayonId = $request->integer('rayon_id') ?: null;
        $rayons = Rayon::orderBy('nama')->get();
        $selectedRayon = $rayonId ? Rayon::with('guru')->findOrFail($rayonId) : null;

        $tagihanQuery = TagihanInfak::query();
        $pembayaranQuery = Pembayaran::query();

        if ($periode !== '') {
            $tagihanQuery->where('periode', $periode);
            $pembayaranQuery->whereYear('tanggal', substr($periode, 0, 4))
                ->whereMonth('tanggal', substr($periode, 5, 2));
        }

        $totalTagihan = (clone $tagihanQuery)->sum('nominal');
        $pembayaranValid = (clone $pembayaranQuery)->where('status_verifikasi', 'valid')->sum('nominal');
        $pembayaranPending = (clone $pembayaranQuery)->where('status_verifikasi', 'pending')->count();
        $tunggakan = max(0, $totalTagihan - $pembayaranValid);

        $statusTagihan = (clone $tagihanQuery)
            ->selectRaw('status, COUNT(*) as total, SUM(nominal) as nominal')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $rekapRayon = Rayon::with('guru')
            ->withCount(['siswaAkademik as siswa_aktif_count' => fn ($query) => $query->where('status', 'aktif')])
            ->get()
            ->map(function (Rayon $rayon) use ($periode) {
                $akademikIds = SiswaAkademik::where('rayon_id', $rayon->id)->pluck('id');
                $siswaIds = SiswaAkademik::where('rayon_id', $rayon->id)->pluck('siswa_id')->unique();

                $tagihan = TagihanInfak::whereIn('siswa_akademik_id', $akademikIds)
                    ->when($periode !== '', fn ($query) => $query->where('periode', $periode))
                    ->sum('nominal');

                $pembayaran = Pembayaran::whereIn('siswa_id', $siswaIds)
                    ->where('status_verifikasi', 'valid')
                    ->when($periode !== '', function ($query) use ($periode) {
                        $query->whereYear('tanggal', substr($periode, 0, 4))
                            ->whereMonth('tanggal', substr($periode, 5, 2));
                    })
                    ->sum('nominal');

                return [
                    'nama' => $rayon->nama,
                    'pembimbing' => $rayon->guru?->nama ?? '-',
                    'siswa' => $rayon->siswa_aktif_count,
                    'tagihan' => $tagihan,
                    'pembayaran' => $pembayaran,
                    'tunggakan' => max(0, $tagihan - $pembayaran),
                ];
            })
            ->sortByDesc('tunggakan')
            ->values();

        $siswaMenunggak = TagihanInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon')
            ->when($periode !== '', fn ($query) => $query->where('periode', $periode))
            ->whereIn('status', ['belum', 'sebagian'])
            ->latest()
            ->limit(25)
            ->get();

        $tunggakanPerRayon = DB::table('siswa_akademik as aktif')
            ->join('siswa', 'siswa.id', '=', 'aktif.siswa_id')
            ->leftJoin('rombel', 'rombel.id', '=', 'aktif.rombel_id')
            ->leftJoin('rayon', 'rayon.id', '=', 'aktif.rayon_id')
            ->leftJoin('siswa_akademik as semua_akademik', 'semua_akademik.siswa_id', '=', 'siswa.id')
            ->leftJoin('tagihan_infak', function ($join) use ($periode) {
                $join->on('tagihan_infak.siswa_akademik_id', '=', 'semua_akademik.id');

                if ($periode !== '') {
                    $join->where('tagihan_infak.periode', $periode);
                }
            })
            ->where('aktif.status', 'aktif')
            ->where('siswa.status', 'aktif')
            ->groupBy('rayon.id', 'rayon.nama', 'rombel.nama', 'siswa.id', 'siswa.nis', 'siswa.nama')
            ->havingRaw("SUM(CASE WHEN tagihan_infak.status IN ('belum', 'sebagian') THEN 1 ELSE 0 END) > 0")
            ->orderBy('rayon.nama')
            ->orderBy('siswa.nis')
            ->selectRaw("
                COALESCE(rayon.nama, '-') as rayon_nama,
                siswa.nis,
                siswa.nama,
                COALESCE(rombel.nama, '-') as rombel_nama,
                SUM(CASE WHEN tagihan_infak.status = 'lunas' THEN 1 ELSE 0 END) as bulan_lunas,
                SUM(CASE WHEN tagihan_infak.status IN ('belum', 'sebagian') THEN 1 ELSE 0 END) as bulan_belum,
                SUM(CASE WHEN tagihan_infak.status IN ('belum', 'sebagian') THEN tagihan_infak.nominal ELSE 0 END) as nominal_tunggakan
            ")
            ->get();
        $totalTunggakanPerRayon = $tunggakanPerRayon->sum('nominal_tunggakan');

        $siswaRayon = collect();
        if ($selectedRayon) {
            $anggota = SiswaAkademik::with('siswa', 'rombel')
                ->where('rayon_id', $selectedRayon->id)
                ->where('status', 'aktif')
                ->whereHas('siswa', fn ($query) => $query->where('status', 'aktif'))
                ->orderBy('siswa_id')
                ->get();
            $siswaIds = $anggota->pluck('siswa_id')->unique()->values();

            $tagihanPerSiswa = TagihanInfak::query()
                ->join('siswa_akademik', 'siswa_akademik.id', '=', 'tagihan_infak.siswa_akademik_id')
                ->whereIn('siswa_akademik.siswa_id', $siswaIds)
                ->when($periode !== '', fn ($query) => $query->where('tagihan_infak.periode', $periode))
                ->groupBy('siswa_akademik.siswa_id')
                ->selectRaw('siswa_akademik.siswa_id, SUM(tagihan_infak.nominal) as total')
                ->pluck('total', 'siswa_akademik.siswa_id');

            $pembayaranPerSiswa = Pembayaran::query()
                ->whereIn('siswa_id', $siswaIds)
                ->where('status_verifikasi', 'valid')
                ->when($periode !== '', function ($query) use ($periode) {
                    $query->whereYear('tanggal', substr($periode, 0, 4))
                        ->whereMonth('tanggal', substr($periode, 5, 2));
                })
                ->groupBy('siswa_id')
                ->selectRaw('siswa_id, SUM(nominal) as total')
                ->pluck('total', 'siswa_id');

            $siswaRayon = $anggota->sortBy(fn ($akademik) => $akademik->siswa?->nis)->map(function ($akademik) use ($tagihanPerSiswa, $pembayaranPerSiswa) {
                $tagihan = (int) ($tagihanPerSiswa[$akademik->siswa_id] ?? 0);
                $pembayaran = (int) ($pembayaranPerSiswa[$akademik->siswa_id] ?? 0);

                return [
                    'nis' => $akademik->siswa?->nis ?? '-',
                    'nama' => $akademik->siswa?->nama ?? '-',
                    'rombel' => $akademik->rombel?->nama ?? '-',
                    'tagihan' => $tagihan,
                    'pembayaran' => $pembayaran,
                    'tunggakan' => max(0, $tagihan - $pembayaran),
                ];
            })->values();
        }

        return compact(
            'periode',
            'rayonId',
            'rayons',
            'selectedRayon',
            'siswaRayon',
            'totalTagihan',
            'pembayaranValid',
            'pembayaranPending',
            'tunggakan',
            'statusTagihan',
            'rekapRayon',
            'siswaMenunggak',
            'tunggakanPerRayon',
            'totalTunggakanPerRayon',
        );
    }

    private function rupiah(int|float $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
