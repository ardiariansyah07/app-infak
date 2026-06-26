<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KomitmenInfak;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Support\AkademikStatus;
use App\Support\SimpleXlsx;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    private array $templates = [
        'tahun-ajaran' => ['nama', 'tanggal_mulai', 'tanggal_selesai', 'aktif'],
        'guru' => ['nip', 'nama', 'jenis_kelamin', 'no_hp', 'email', 'alamat', 'aktif'],
        'rayon' => ['nama', 'nip_guru'],
        'rombel' => ['nama', 'tingkat'],
        'siswa' => ['nis', 'nama', 'jenis_kelamin', 'status', 'tahun_ajaran', 'tingkat', 'rombel', 'rayon', 'email_login'],
        'komitmen-infak' => ['nis', 'tahun_ajaran', 'nominal_bulanan', 'mulai_bulan'],
        'tagihan-awal' => ['nis', 'tahun_ajaran', 'periode', 'nominal_tagihan', 'nominal_terbayar', 'tanggal_bayar'],
        'saldo-awal' => ['nis', 'tahun_ajaran', 'mulai_bulan', 'nominal_bulanan', 'bulan_lunas', 'bulan_sebagian', 'nominal_sebagian', 'bulan_belum', 'tanggal_import'],
    ];

    private array $examples = [
        'tahun-ajaran' => [
            ['nama' => '2024/2025', 'tanggal_mulai' => '2024-07-15', 'tanggal_selesai' => '2025-06-30', 'aktif' => ''],
            ['nama' => '2025/2026', 'tanggal_mulai' => '2025-07-14', 'tanggal_selesai' => '2026-06-30', 'aktif' => ''],
            ['nama' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'aktif' => ''],
        ],
        'guru' => [
            ['nip' => 'G001', 'nama' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'no_hp' => '081234567890', 'email' => 'budi@sekolah.sch.id', 'alamat' => 'Bogor', 'aktif' => 'aktif'],
        ],
        'rayon' => [
            ['nama' => 'Cicurug 1', 'nip_guru' => 'G001'],
        ],
        'rombel' => [
            ['nama' => 'X PPLG 1', 'tingkat' => 'X'],
            ['nama' => 'XI PPLG 1', 'tingkat' => 'XI'],
            ['nama' => 'XII PPLG 1', 'tingkat' => 'XII'],
        ],
        'siswa' => [
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2024/2025', 'tingkat' => 'X', 'rombel' => 'X PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2025/2026', 'tingkat' => 'XI', 'rombel' => 'XI PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2026/2027', 'tingkat' => 'XII', 'rombel' => 'XII PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
        ],
        'komitmen-infak' => [
            ['nis' => '24250001', 'tahun_ajaran' => '2024/2025', 'nominal_bulanan' => '50000', 'mulai_bulan' => '2024-07-01'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'nominal_bulanan' => '50000', 'mulai_bulan' => '2025-07-01'],
        ],
        'tagihan-awal' => [
            ['nis' => '24250001', 'tahun_ajaran' => '2024/2025', 'periode' => '2024-07', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '50000', 'tanggal_bayar' => '2024-07-10'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'periode' => '2025-07', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '20000', 'tanggal_bayar' => '2025-07-10'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'periode' => '2025-08', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '0', 'tanggal_bayar' => ''],
        ],
        'saldo-awal' => [
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'mulai_bulan' => '2025-07', 'nominal_bulanan' => '50000', 'bulan_lunas' => '3', 'bulan_sebagian' => '0', 'nominal_sebagian' => '0', 'bulan_belum' => '9', 'tanggal_import' => '2026-06-26'],
            ['nis' => '24250002', 'tahun_ajaran' => '2025/2026', 'mulai_bulan' => '2025-07', 'nominal_bulanan' => '50000', 'bulan_lunas' => '2', 'bulan_sebagian' => '1', 'nominal_sebagian' => '20000', 'bulan_belum' => '9', 'tanggal_import' => '2026-06-26'],
        ],
    ];

    public function template(string $master): BinaryFileResponse
    {
        abort_unless(isset($this->templates[$master]), 404);

        $path = SimpleXlsx::template($this->templates[$master], $master, $this->examples[$master] ?? []);

        return response()->download($path, 'template-'.$master.'.xlsx')->deleteFileAfterSend();
    }

    public function import(Request $request, string $master)
    {
        abort_unless(isset($this->templates[$master]), 404);

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:51200'],
        ]);

        try {
            $rows = SimpleXlsx::rows($request->file('file')->getRealPath());
            array_shift($rows);

            $count = match ($master) {
                'tahun-ajaran' => $this->tahunAjaran($rows),
                'guru' => $this->guru($rows),
                'rayon' => $this->rayon($rows),
                'rombel' => $this->rombel($rows),
                'siswa' => $this->siswa($rows),
                'komitmen-infak' => $this->komitmenInfak($rows),
                'tagihan-awal' => $this->tagihanAwal($rows),
                'saldo-awal' => $this->saldoAwal($rows),
            };
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Import gagal: '.$exception->getMessage());
        }

        return back()->with('success', $count.' data berhasil diimport');
    }

    private function tahunAjaran(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }

            TahunAjaran::updateOrCreate(
                ['nama' => $row[0]],
                [
                    'tanggal_mulai' => $this->dateValue($row[1] ?? null),
                    'tanggal_selesai' => $this->dateValue($row[2] ?? null),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function guru(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $user = $this->userForGuru($row);

            Guru::updateOrCreate(
                ['nip' => $row[0]],
                [
                    'user_id' => $user?->id,
                    'nama' => $row[1],
                    'jenis_kelamin' => $row[2] ?: 'L',
                    'no_hp' => $row[3] ?? null,
                    'email' => $row[4] ?? null,
                    'alamat' => $row[5] ?? null,
                    'aktif' => ! in_array(strtolower($row[6] ?? 'aktif'), ['0', 'tidak', 'nonaktif', 'false']),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function rayon(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $guru = Guru::where('nip', $row[1] ?? null)->first();

            if (empty($row[0]) || ! $guru) {
                continue;
            }

            Rayon::updateOrCreate(['nama' => $row[0]], ['guru_id' => $guru->id]);
            $count++;
        }

        return $count;
    }

    private function rombel(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }

            Rombel::updateOrCreate(
                ['nama' => $row[0]],
                ['tingkat' => in_array($row[1] ?? null, ['X', 'XI', 'XII']) ? $row[1] : null]
            );
            $count++;
        }

        return $count;
    }

    private function siswa(array $rows): int
    {
        $count = 0;
        $validRows = [];
        $userCache = [];
        $affectedSiswaIds = [];

        $tahunMap = TahunAjaran::whereIn('nama', collect($rows)->pluck(4)->filter()->unique()->values())
            ->get()
            ->keyBy('nama');
        $rombelMap = Rombel::whereIn('nama', collect($rows)->pluck(6)->filter()->unique()->values())
            ->get()
            ->keyBy('nama');
        $rayonMap = Rayon::whereIn('nama', collect($rows)->pluck(7)->filter()->unique()->values())
            ->get()
            ->keyBy('nama');

        foreach ($rows as $row) {
            $tahun = $tahunMap->get($row[4] ?? null);
            $rombel = $rombelMap->get($row[6] ?? null);
            $rayon = $rayonMap->get($row[7] ?? null);

            if (empty($row[0]) || empty($row[1]) || ! $tahun || ! $rombel || ! $rayon) {
                continue;
            }

            $validRows[] = compact('row', 'tahun', 'rombel', 'rayon');
        }

        usort($validRows, function ($left, $right) {
            return strcmp(
                (string) $left['tahun']->tanggal_mulai?->format('Y-m-d'),
                (string) $right['tahun']->tanggal_mulai?->format('Y-m-d')
            );
        });

        foreach (array_chunk($validRows, 200) as $chunk) {
            DB::transaction(function () use ($chunk, &$userCache, &$affectedSiswaIds) {
                foreach ($chunk as $data) {
                    ['row' => $row, 'tahun' => $tahun, 'rombel' => $rombel, 'rayon' => $rayon] = $data;
                    $email = $row[8] ?? null;
                    $user = null;

                    if ($email) {
                        if (! array_key_exists($email, $userCache)) {
                            $userCache[$email] = $this->userForSiswa($row);
                        }

                        $user = $userCache[$email];
                    }

                    $siswa = Siswa::updateOrCreate(
                        ['nis' => $row[0]],
                        [
                            'user_id' => $user?->id,
                            'nama' => $row[1],
                            'jenis_kelamin' => in_array($row[2] ?? null, ['L', 'P']) ? $row[2] : 'L',
                            'status' => in_array($row[3] ?? null, ['aktif', 'alumni']) ? $row[3] : 'aktif',
                        ]
                    );

                    $siswa->akademik()->updateOrCreate(
                        ['tahun_ajaran_id' => $tahun->id],
                        [
                            'tingkat' => in_array($row[5] ?? null, ['X', 'XI', 'XII']) ? $row[5] : 'X',
                            'rombel_id' => $rombel->id,
                            'rayon_id' => $rayon->id,
                            'status' => 'naik',
                        ]
                    );

                    $affectedSiswaIds[] = $siswa->id;
                }
            });

            $count += count($chunk);
        }

        AkademikStatus::syncMany($affectedSiswaIds);

        return $count;
    }

    private function komitmenInfak(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $siswa = Siswa::where('nis', $row[0] ?? null)->first();
            $tahun = TahunAjaran::where('nama', $row[1] ?? null)->first();

            if (! $siswa || ! $tahun || empty($row[2])) {
                continue;
            }

            $akademik = $siswa->akademik()
                ->where('tahun_ajaran_id', $tahun->id)
                ->first();

            if (! $akademik) {
                continue;
            }

            KomitmenInfak::updateOrCreate(
                ['siswa_akademik_id' => $akademik->id],
                [
                    'nominal_bulanan' => $this->moneyValue($row[2]),
                    'mulai_bulan' => $this->dateValue($row[3] ?? null),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function tagihanAwal(array $rows): int
    {
        $count = 0;
        $this->ensureAkademikForImportRows($rows, 0, 1);
        $akademikMap = $this->akademikImportMap($rows, 0, 1);

        foreach (array_chunk($rows, 300) as $chunk) {
            $plans = [];

            foreach ($chunk as $row) {
                $akademik = $akademikMap->get($this->akademikImportKey($row[0] ?? null, $row[1] ?? null));
                $periode = $this->monthValue($row[2] ?? null);
                $nominalTagihan = $this->moneyValue($row[3] ?? 0);
                $nominalTerbayar = min($nominalTagihan, $this->moneyValue($row[4] ?? 0));

                if (! $akademik || ! preg_match('/^\d{4}-\d{2}$/', (string) $periode) || $nominalTagihan <= 0) {
                    continue;
                }

                $plans[] = [
                    'akademik_id' => (int) $akademik->akademik_id,
                    'siswa_id' => (int) $akademik->siswa_id,
                    'periode' => $periode,
                    'nominal_tagihan' => $nominalTagihan,
                    'nominal_terbayar' => $nominalTerbayar,
                    'tanggal' => $this->dateValue($row[5] ?? null) ?: $periode.'-01',
                ];
            }

            if ($plans === []) {
                continue;
            }

            DB::transaction(function () use ($plans) {
                $tagihanIds = $this->storeTagihanPlans($plans);
                $this->replaceImportPayments($plans, $tagihanIds, 'import-tagihan-awal');
                $this->refreshTagihanStatuses($tagihanIds->values()->all());
            });

            $count += count($plans);
        }

        return $count;
    }

    private function saldoAwal(array $rows): int
    {
        $count = 0;
        $this->ensureAkademikForImportRows($rows, 0, 1);
        $akademikMap = $this->akademikImportMap($rows, 0, 1);

        foreach (array_chunk($rows, 500) as $chunk) {
            $plans = [];
            $komitmen = [];

            foreach ($chunk as $row) {
                $akademik = $akademikMap->get($this->akademikImportKey($row[0] ?? null, $row[1] ?? null));
                $mulaiBulan = $this->monthStart($row[2] ?? null);
                $nominalBulanan = $this->moneyValue($row[3] ?? 0);
                $bulanLunas = max(0, (int) ($row[4] ?? 0));
                $bulanSebagian = max(0, (int) ($row[5] ?? 0));
                $nominalSebagian = min($nominalBulanan, $this->moneyValue($row[6] ?? 0));
                $bulanBelum = max(0, (int) ($row[7] ?? 0));
                $totalBulan = $bulanLunas + $bulanSebagian + $bulanBelum;

                if (! $akademik || ! $mulaiBulan || $nominalBulanan <= 0 || $totalBulan <= 0) {
                    continue;
                }

                for ($index = 0; $index < $totalBulan; $index++) {
                    $nominalTerbayar = match (true) {
                        $index < $bulanLunas => $nominalBulanan,
                        $index < ($bulanLunas + $bulanSebagian) => $nominalSebagian,
                        default => 0,
                    };

                    $plans[] = [
                        'akademik_id' => (int) $akademik->akademik_id,
                        'siswa_id' => (int) $akademik->siswa_id,
                        'periode' => $mulaiBulan->addMonthsNoOverflow($index)->format('Y-m'),
                        'nominal_tagihan' => $nominalBulanan,
                        'nominal_terbayar' => $nominalTerbayar,
                        'tanggal' => $this->dateValue($row[8] ?? null) ?: now()->toDateString(),
                    ];
                }

                $komitmen[(int) $akademik->akademik_id] = [
                    'siswa_akademik_id' => (int) $akademik->akademik_id,
                    'nominal_bulanan' => $nominalBulanan,
                    'mulai_bulan' => $mulaiBulan->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($plans === []) {
                continue;
            }

            DB::transaction(function () use ($plans, $komitmen) {
                $tagihanIds = $this->storeTagihanPlans($plans);
                $this->replaceImportPayments($plans, $tagihanIds, 'import-saldo-awal');
                $this->refreshTagihanStatuses($tagihanIds->values()->all());

                $this->storeKomitmenRows(array_values($komitmen));
            });

            $count += count($komitmen);
        }

        return $count;
    }

    private function ensureAkademikForImportRows(array $rows, int $nisIndex, int $tahunIndex): void
    {
        $pairs = collect($rows)
            ->map(fn ($row) => [
                'nis' => trim((string) ($row[$nisIndex] ?? '')),
                'tahun' => trim((string) ($row[$tahunIndex] ?? '')),
            ])
            ->filter(fn ($row) => $row['nis'] !== '' && $row['tahun'] !== '')
            ->unique(fn ($row) => $this->akademikImportKey($row['nis'], $row['tahun']))
            ->values();

        if ($pairs->isEmpty()) {
            return;
        }

        $siswaMap = Siswa::whereIn('nis', $pairs->pluck('nis')->unique()->values())
            ->get()
            ->keyBy('nis');
        $tahunMap = TahunAjaran::whereIn('nama', $pairs->pluck('tahun')->unique()->values())
            ->get()
            ->keyBy('nama');
        $existingKeys = $this->akademikImportMap($rows, $nisIndex, $tahunIndex)->keys();
        $rombel = Rombel::orderBy('nama')->get();
        $rombelByName = $rombel->keyBy('nama');
        $rombelByTingkat = $rombel->whereNotNull('tingkat')->groupBy('tingkat');
        $affectedSiswaIds = [];

        $referencesBySiswa = DB::table('siswa_akademik')
            ->join('tahun_ajaran', 'tahun_ajaran.id', '=', 'siswa_akademik.tahun_ajaran_id')
            ->leftJoin('rombel', 'rombel.id', '=', 'siswa_akademik.rombel_id')
            ->whereIn('siswa_akademik.siswa_id', $siswaMap->pluck('id')->values())
            ->select(
                'siswa_akademik.*',
                'tahun_ajaran.nama as tahun_ajaran_nama',
                'tahun_ajaran.tanggal_mulai',
                'rombel.nama as rombel_nama'
            )
            ->get()
            ->groupBy('siswa_id');

        $akademikRows = [];

        foreach ($pairs as $pair) {
            $key = $this->akademikImportKey($pair['nis'], $pair['tahun']);

            if ($existingKeys->contains($key)) {
                continue;
            }

            $siswa = $siswaMap->get($pair['nis']);
            $tahun = $tahunMap->get($pair['tahun']);

            if (! $siswa || ! $tahun) {
                continue;
            }

            $references = $referencesBySiswa->get($siswa->id, collect());

            if ($references->isEmpty()) {
                continue;
            }

            $targetYear = $this->tahunAjaranStartYear($tahun);
            $reference = $references
                ->sortBy(fn ($item) => abs($targetYear - $this->tahunAjaranStartYear($item)))
                ->first();
            $tingkat = $this->inferredTingkat($reference, $tahun);
            $rombelId = $this->matchingRombelId($reference, $tingkat, $rombelByName, $rombelByTingkat);

            $akademikRows[] = [
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahun->id,
                'tingkat' => $tingkat,
                'rombel_id' => $rombelId,
                'rayon_id' => $reference->rayon_id,
                'status' => 'naik',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $affectedSiswaIds[] = $siswa->id;
            $existingKeys->push($key);
        }

        foreach (array_chunk($akademikRows, 1000) as $chunk) {
            DB::table('siswa_akademik')->insert($chunk);
        }

        AkademikStatus::syncMany($affectedSiswaIds);
    }

    private function inferredTingkat(object $reference, TahunAjaran $targetTahun): string
    {
        $levels = ['X', 'XI', 'XII'];
        $referenceIndex = array_search($reference->tingkat, $levels, true);

        if ($referenceIndex === false) {
            return 'X';
        }

        $delta = $this->tahunAjaranStartYear($targetTahun) - $this->tahunAjaranStartYear($reference);
        $targetIndex = max(0, min(count($levels) - 1, $referenceIndex + $delta));

        return $levels[$targetIndex];
    }

    private function matchingRombelId(object $reference, string $tingkat, $rombelByName, $rombelByTingkat): int
    {
        $candidateName = preg_replace('/^(XII|XI|X)\b/', $tingkat, (string) $reference->rombel_nama);
        $matched = $candidateName ? $rombelByName->get($candidateName) : null;

        if ($matched) {
            return (int) $matched->id;
        }

        return (int) ($rombelByTingkat->get($tingkat)?->first()?->id ?? $reference->rombel_id);
    }

    private function tahunAjaranStartYear(object $tahun): int
    {
        $date = $tahun->tanggal_mulai ?? null;

        if ($date) {
            return (int) substr((string) $date, 0, 4);
        }

        $nama = $tahun->nama ?? $tahun->tahun_ajaran_nama ?? '';

        return preg_match('/\d{4}/', (string) $nama, $match) ? (int) $match[0] : (int) now()->year;
    }

    private function akademikImportMap(array $rows, int $nisIndex, int $tahunIndex)
    {
        $nises = collect($rows)->pluck($nisIndex)->filter()->map(fn ($value) => (string) $value)->unique()->values();
        $tahun = collect($rows)->pluck($tahunIndex)->filter()->map(fn ($value) => (string) $value)->unique()->values();

        return DB::table('siswa')
            ->join('siswa_akademik', 'siswa_akademik.siswa_id', '=', 'siswa.id')
            ->join('tahun_ajaran', 'tahun_ajaran.id', '=', 'siswa_akademik.tahun_ajaran_id')
            ->whereIn('siswa.nis', $nises)
            ->whereIn('tahun_ajaran.nama', $tahun)
            ->select('siswa.id as siswa_id', 'siswa.nis', 'tahun_ajaran.nama as tahun_ajaran', 'siswa_akademik.id as akademik_id')
            ->get()
            ->keyBy(fn ($row) => $this->akademikImportKey($row->nis, $row->tahun_ajaran));
    }

    private function akademikImportKey(mixed $nis, mixed $tahun): string
    {
        return trim((string) $nis).'|'.trim((string) $tahun);
    }

    private function storeTagihanPlans(array $plans)
    {
        $now = now();
        $akademikIds = collect($plans)->pluck('akademik_id')->unique()->values();
        $periodes = collect($plans)->pluck('periode')->unique()->values();

        $existing = DB::table('tagihan_infak')
            ->whereIn('siswa_akademik_id', $akademikIds)
            ->whereIn('periode', $periodes)
            ->get()
            ->keyBy(fn ($row) => $row->siswa_akademik_id.'|'.$row->periode);

        $tagihanIds = collect();
        $insertRows = [];
        $updateRows = [];

        foreach ($plans as $plan) {
            $key = $plan['akademik_id'].'|'.$plan['periode'];

            if ($existing->has($key)) {
                $tagihanIds->put($key, (int) $existing->get($key)->id);
                $updateRows[] = [
                    'id' => (int) $existing->get($key)->id,
                    'nominal' => $plan['nominal_tagihan'],
                ];

                continue;
            }

            $insertRows[$key] = [
                'siswa_akademik_id' => $plan['akademik_id'],
                'periode' => $plan['periode'],
                'nominal' => $plan['nominal_tagihan'],
                'status' => 'belum',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->bulkUpdateTagihanNominal($updateRows);

        foreach (array_chunk(array_values($insertRows), 1000) as $rows) {
            DB::table('tagihan_infak')->insert($rows);
        }

        DB::table('tagihan_infak')
            ->whereIn('siswa_akademik_id', $akademikIds)
            ->whereIn('periode', $periodes)
            ->get()
            ->each(fn ($row) => $tagihanIds->put($row->siswa_akademik_id.'|'.$row->periode, (int) $row->id));

        return $tagihanIds;
    }

    private function storeKomitmenRows(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $existing = DB::table('komitmen_infak')
            ->whereIn('siswa_akademik_id', collect($rows)->pluck('siswa_akademik_id')->unique()->values())
            ->get()
            ->keyBy('siswa_akademik_id');
        $insertRows = [];
        $updateRows = [];

        foreach ($rows as $row) {
            $current = $existing->get($row['siswa_akademik_id']);

            if ($current) {
                $updateRows[] = [
                    'id' => (int) $current->id,
                    'nominal_bulanan' => $row['nominal_bulanan'],
                    'mulai_bulan' => $row['mulai_bulan'],
                ];

                continue;
            }

            $insertRows[] = $row;
        }

        foreach (array_chunk($insertRows, 1000) as $chunk) {
            DB::table('komitmen_infak')->insert($chunk);
        }

        foreach (array_chunk($updateRows, 500) as $chunk) {
            $ids = collect($chunk)->pluck('id')->all();
            $nominalCase = 'CASE id ';
            $mulaiCase = 'CASE id ';
            $nominalBindings = [];
            $mulaiBindings = [];

            foreach ($chunk as $row) {
                $nominalCase .= 'WHEN ? THEN ? ';
                $nominalBindings[] = $row['id'];
                $nominalBindings[] = $row['nominal_bulanan'];
                $mulaiCase .= 'WHEN ? THEN ? ';
                $mulaiBindings[] = $row['id'];
                $mulaiBindings[] = $row['mulai_bulan'];
            }

            $nominalCase .= 'END';
            $mulaiCase .= 'END';

            DB::update(
                'UPDATE komitmen_infak SET nominal_bulanan = '.$nominalCase.', mulai_bulan = '.$mulaiCase.', updated_at = ? WHERE id IN ('.
                collect($ids)->map(fn () => '?')->join(',').
                ')',
                array_merge($nominalBindings, $mulaiBindings, [now()], $ids)
            );
        }
    }

    private function bulkUpdateTagihanNominal(array $rows): void
    {
        foreach (array_chunk($rows, 500) as $chunk) {
            if ($chunk === []) {
                continue;
            }

            $case = 'CASE id ';
            $bindings = [];

            foreach ($chunk as $row) {
                $case .= 'WHEN ? THEN ? ';
                $bindings[] = $row['id'];
                $bindings[] = $row['nominal'];
            }

            $case .= 'END';

            DB::update(
                'UPDATE tagihan_infak SET nominal = '.$case.', status = ?, updated_at = ? WHERE id IN ('.
                collect($chunk)->pluck('id')->map(fn () => '?')->join(',').
                ')',
                array_merge($bindings, ['belum', now()], collect($chunk)->pluck('id')->all())
            );
        }
    }

    private function replaceImportPayments(array $plans, $tagihanIds, string $source): void
    {
        $ids = $tagihanIds->values()->unique()->values();

        $existingImportPaymentIds = DB::table('alokasi_pembayaran')
            ->join('pembayaran', 'pembayaran.id', '=', 'alokasi_pembayaran.pembayaran_id')
            ->whereIn('alokasi_pembayaran.tagihan_infak_id', $ids)
            ->where('pembayaran.bukti_transfer', $source)
            ->pluck('pembayaran.id')
            ->unique()
            ->values();

        if ($existingImportPaymentIds->isNotEmpty()) {
            DB::table('alokasi_pembayaran')->whereIn('pembayaran_id', $existingImportPaymentIds)->delete();
            Pembayaran::whereIn('id', $existingImportPaymentIds)->delete();
        }

        $paymentGroups = [];

        foreach ($plans as $plan) {
            if ($plan['nominal_terbayar'] <= 0) {
                continue;
            }

            $key = $plan['siswa_id'].'|'.$plan['tanggal'];
            $tagihanId = $tagihanIds->get($plan['akademik_id'].'|'.$plan['periode']);

            if (! $tagihanId) {
                continue;
            }

            $paymentGroups[$key]['siswa_id'] = $plan['siswa_id'];
            $paymentGroups[$key]['tanggal'] = $plan['tanggal'];
            $paymentGroups[$key]['nominal'] = ($paymentGroups[$key]['nominal'] ?? 0) + $plan['nominal_terbayar'];
            $paymentGroups[$key]['alokasi'][] = [
                'tagihan_infak_id' => $tagihanId,
                'nominal' => $plan['nominal_terbayar'],
            ];
        }

        foreach ($paymentGroups as $group) {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $group['siswa_id'],
                'tanggal' => $group['tanggal'],
                'nominal' => $group['nominal'],
                'bukti_transfer' => $source,
                'status_verifikasi' => 'valid',
            ]);

            $now = now();
            $alokasiRows = collect($group['alokasi'])->map(fn ($item) => [
                'pembayaran_id' => $pembayaran->id,
                'tagihan_infak_id' => $item['tagihan_infak_id'],
                'nominal' => $item['nominal'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            DB::table('alokasi_pembayaran')->insert($alokasiRows);
        }
    }

    private function refreshTagihanStatuses(array $tagihanIds): void
    {
        if ($tagihanIds === []) {
            return;
        }

        $paid = DB::table('alokasi_pembayaran')
            ->join('pembayaran', 'pembayaran.id', '=', 'alokasi_pembayaran.pembayaran_id')
            ->whereIn('alokasi_pembayaran.tagihan_infak_id', $tagihanIds)
            ->where('pembayaran.status_verifikasi', 'valid')
            ->groupBy('alokasi_pembayaran.tagihan_infak_id')
            ->selectRaw('alokasi_pembayaran.tagihan_infak_id, SUM(alokasi_pembayaran.nominal) as total')
            ->pluck('total', 'tagihan_infak_id');

        $statusIds = [
            'belum' => [],
            'sebagian' => [],
            'lunas' => [],
        ];

        DB::table('tagihan_infak')
            ->whereIn('id', $tagihanIds)
            ->select('id', 'nominal')
            ->orderBy('id')
            ->chunkById(1000, function ($tagihan) use ($paid, &$statusIds) {
                foreach ($tagihan as $item) {
                    $terbayar = (int) ($paid[$item->id] ?? 0);
                    $status = match (true) {
                        $terbayar <= 0 => 'belum',
                        $terbayar < $item->nominal => 'sebagian',
                        default => 'lunas',
                    };

                    $statusIds[$status][] = $item->id;
                }
            });

        foreach ($statusIds as $status => $ids) {
            foreach (array_chunk($ids, 1000) as $chunk) {
                DB::table('tagihan_infak')
                    ->whereIn('id', $chunk)
                    ->update(['status' => $status, 'updated_at' => now()]);
            }
        }
    }

    private function moneyValue(mixed $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && (int) $value > 20000) {
            return gmdate('Y-m-d', ((int) $value - 25569) * 86400);
        }

        $timestamp = strtotime((string) $value);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function monthValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}$/', (string) $value)) {
            return (string) $value;
        }

        $date = $this->dateValue($value);

        return $date ? substr($date, 0, 7) : null;
    }

    private function monthStart(mixed $value): ?CarbonImmutable
    {
        $month = $this->monthValue($value);

        return $month ? CarbonImmutable::createFromFormat('Y-m-d', $month.'-01') : null;
    }

    private function userForGuru(array $row): ?User
    {
        $email = $row[4] ?? null;

        if (! $email) {
            return null;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $row[1];
        $user->role = User::ROLE_PEMBIMBING;

        if (! $user->exists) {
            $user->password = $this->generatedPasswordHash('Wikrama'.$row[0].'*');
        }

        $user->save();

        return $user;
    }

    private function userForSiswa(array $row): ?User
    {
        $email = $row[8] ?? null;

        if (! $email) {
            return null;
        }

        $nis = (string) $row[0];

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $row[1];
        $user->role = User::ROLE_ORANG_TUA;

        if (! $user->exists) {
            $user->password = $this->generatedPasswordHash('Wikrama'.substr($nis, -4).'*');
        }

        $user->save();

        return $user;
    }

    private function generatedPasswordHash(string $password): string
    {
        return Hash::make($password, ['rounds' => 8]);
    }
}
