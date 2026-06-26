<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Support\SimplePdf;
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
        $pdf = new SimplePdf('Laporan Infak Sekolah');

        $pdf->heading('Laporan Infak Sekolah');
        $pdf->line('Periode: '.($data['periode'] !== '' ? $data['periode'] : 'Semua periode'));
        $pdf->line('Tanggal cetak: '.now()->format('d/m/Y H:i'));
        $pdf->space();

        $pdf->subheading('Ringkasan');
        $pdf->tableRow(['Total Tagihan', $this->rupiah($data['totalTagihan'])], [140, 160]);
        $pdf->tableRow(['Pembayaran Valid', $this->rupiah($data['pembayaranValid'])], [140, 160]);
        $pdf->tableRow(['Estimasi Tunggakan', $this->rupiah($data['tunggakan'])], [140, 160]);
        $pdf->tableRow(['Pending Validasi', $data['pembayaranPending'].' pembayaran'], [140, 160]);
        $pdf->space();

        $pdf->subheading('Status Tagihan');
        $pdf->tableRow(['Status', 'Jumlah', 'Nominal'], [120, 80, 160], true);
        foreach ($data['statusTagihan'] as $status) {
            $pdf->tableRow([ucfirst($status->status), $status->total, $this->rupiah($status->nominal)], [120, 80, 160]);
        }
        $pdf->space();

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
        $pdf->space();

        $pdf->subheading('Tagihan Belum Lunas');
        $pdf->tableRow(['Periode', 'Siswa', 'Rombel', 'Rayon', 'Nominal', 'Status'], [60, 120, 80, 90, 90, 60], true);
        foreach ($data['siswaMenunggak'] as $tagihan) {
            $pdf->tableRow([
                $tagihan->periode,
                $tagihan->siswaAkademik?->siswa?->nama ?? '-',
                $tagihan->siswaAkademik?->rombel?->nama ?? '-',
                $tagihan->siswaAkademik?->rayon?->nama ?? '-',
                $this->rupiah($tagihan->nominal),
                $tagihan->status,
            ], [60, 120, 80, 90, 90, 60]);
        }

        $filename = 'laporan-infak'.($data['periode'] !== '' ? '-'.$data['periode'] : '').'.pdf';

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
