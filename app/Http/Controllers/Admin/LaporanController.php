<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Support\SimplePdf;
use App\Support\Periode;
use Illuminate\Http\Request;
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
        abort_unless(in_array($jenis, ['ringkasan', 'rayon', 'tunggakan'], true), 404);

        $titles = [
            'ringkasan' => 'Laporan Ringkasan Infak Sekolah',
            'rayon' => 'Laporan Rekap Infak Per Rayon',
            'tunggakan' => 'Laporan Tagihan Belum Lunas',
        ];

        $pdf = new SimplePdf($titles[$jenis]);

        $pdf->heading($titles[$jenis]);
        $pdf->line('Periode: '.($data['periode'] !== '' ? Periode::label($data['periode']) : 'Semua periode'));
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

        $filename = 'laporan-infak-'.$jenis.($data['periode'] !== '' ? '-'.$data['periode'] : '').'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function reportData(Request $request): array
    {
        $periode = $request->string('periode')->toString();

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

        return compact(
            'periode',
            'totalTagihan',
            'pembayaranValid',
            'pembayaranPending',
            'tunggakan',
            'statusTagihan',
            'rekapRayon',
            'siswaMenunggak',
        );
    }

    private function rupiah(int|float $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
