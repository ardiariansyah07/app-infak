@extends('layouts.app')

@section('title','Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">Laporan Infak</h2>
        <p class="text-muted mb-0">Ringkasan untuk administrasi sekolah dan bahan laporan kepada kepala sekolah.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        <a href="{{ route('admin.laporan.pdf', ['periode' => $periode, 'jenis' => 'ringkasan']) }}" class="btn btn-primary">
            <i class="bi bi-filetype-pdf"></i>
            PDF Ringkasan
        </a>
        <a href="{{ route('admin.laporan.pdf', ['periode' => $periode, 'jenis' => 'rayon']) }}" class="btn btn-outline-primary">
            <i class="bi bi-filetype-pdf"></i>
            PDF Rekap Rayon
        </a>
        <a href="{{ route('admin.laporan.pdf', ['periode' => $periode, 'jenis' => 'tunggakan']) }}" class="btn btn-outline-primary">
            <i class="bi bi-filetype-pdf"></i>
            PDF Tunggakan
        </a>
        <a href="{{ route('admin.laporan.pdf', ['periode' => $periode, 'jenis' => 'tunggakan-rayon']) }}" class="btn btn-outline-primary">
            <i class="bi bi-filetype-pdf"></i>
            PDF Tunggakan Rayon
        </a>
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i>
            Cetak
        </button>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label">Filter Periode</label>
            <input type="month" name="periode" value="{{ $periode }}" class="form-control">
        </div>
        <div>
            <label class="form-label">Rayon untuk Laporan Siswa</label>
            <select name="rayon_id" class="form-select" data-searchable-select data-placeholder="Pilih rayon...">
                <option value="">Pilih rayon...</option>
                @foreach($rayons as $rayon)
                    <option value="{{ $rayon->id }}" @selected($rayonId === $rayon->id)>{{ $rayon->nama }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-funnel"></i>
            Terapkan
        </button>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-light border">Reset</a>
        @if($selectedRayon)
            <a href="{{ route('admin.laporan.pdf', ['periode' => $periode, 'rayon_id' => $selectedRayon->id, 'jenis' => 'detail-rayon']) }}" class="btn btn-success">
                <i class="bi bi-filetype-pdf"></i>
                PDF Siswa {{ $selectedRayon->nama }}
            </a>
        @endif
    </div>
</form>

@if($selectedRayon)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
            <div>
                <h5 class="fw-bold mb-1">Data Siswa {{ $selectedRayon->nama }}</h5>
                <p class="text-muted mb-0">Pembimbing: {{ $selectedRayon->guru?->nama ?? '-' }}</p>
            </div>
            <span class="badge bg-primary">{{ $siswaRayon->count() }} siswa aktif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>NIS</th><th>Siswa</th><th>Rombel</th><th>Tagihan</th><th>Terbayar</th><th>Tunggakan</th></tr></thead>
                <tbody>
                @forelse($siswaRayon as $siswa)
                    <tr>
                        <td>{{ $siswa['nis'] }}</td>
                        <td>{{ $siswa['nama'] }}</td>
                        <td>{{ $siswa['rombel'] }}</td>
                        <td>Rp {{ number_format($siswa['tagihan'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($siswa['pembayaran'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($siswa['tunggakan'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada siswa aktif di rayon ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card report-summary">
            <div class="card-body">
                <small class="text-muted">Total Tagihan</small>
                <h4 class="fw-bold mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card report-summary">
            <div class="card-body">
                <small class="text-muted">Pembayaran Valid</small>
                <h4 class="fw-bold mb-0">Rp {{ number_format($pembayaranValid, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card report-summary">
            <div class="card-body">
                <small class="text-muted">Estimasi Tunggakan</small>
                <h4 class="fw-bold mb-0">Rp {{ number_format($tunggakan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card report-summary">
            <div class="card-body">
                <small class="text-muted">Pending Validasi</small>
                <h4 class="fw-bold mb-0">{{ $pembayaranPending }} pembayaran</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Status Tagihan</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($statusTagihan as $status)
                            <tr>
                                <td><span class="badge bg-{{ $status->status === 'lunas' ? 'success' : 'warning text-dark' }}">{{ ucfirst($status->status) }}</span></td>
                                <td>{{ $status->total }}</td>
                                <td>Rp {{ number_format($status->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada tagihan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Rekap Per Rayon</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Rayon</th>
                                <th>Pembimbing</th>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th>Valid</th>
                                <th>Tunggakan</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rekapRayon as $rayon)
                            <tr>
                                <td>{{ $rayon['nama'] }}</td>
                                <td>{{ $rayon['pembimbing'] }}</td>
                                <td>{{ $rayon['siswa'] }}</td>
                                <td>Rp {{ number_format($rayon['tagihan'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($rayon['pembayaran'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($rayon['tunggakan'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data rayon.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
            <div>
                <h5 class="fw-bold mb-1">Tunggakan Siswa Per Rayon</h5>
                <p class="text-muted mb-0">Satu siswa tampil satu kali dengan total bulan sudah bayar dan belum bayar.</p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Total Tunggakan Keseluruhan</small>
                <strong class="fs-5">Rp {{ number_format($totalTunggakanPerRayon, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rayon</th>
                        <th>NIS</th>
                        <th>Siswa</th>
                        <th>Rombel</th>
                        <th>Sudah Bayar</th>
                        <th>Belum Bayar</th>
                        <th>Nominal Tunggakan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tunggakanPerRayon as $siswa)
                    <tr>
                        <td>{{ $siswa->rayon_nama }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $siswa->rombel_nama }}</td>
                        <td><span class="badge bg-success">{{ $siswa->bulan_lunas }} bulan</span></td>
                        <td><span class="badge bg-warning text-dark">{{ $siswa->bulan_belum }} bulan</span></td>
                        <td>Rp {{ number_format($siswa->nominal_tunggakan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada siswa yang memiliki tunggakan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h5 class="fw-bold mb-1">Daftar Tagihan Belum Lunas</h5>
        <p class="text-muted">Maksimal 25 data terbaru untuk tindak lanjut cepat.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Siswa</th>
                        <th>Rombel</th>
                        <th>Rayon</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($siswaMenunggak as $tagihan)
                    <tr>
                        <td>{{ \App\Support\Periode::label($tagihan->periode) }}</td>
                        <td>{{ $tagihan->siswaAkademik?->siswa?->nama }}</td>
                        <td>{{ $tagihan->siswaAkademik?->rombel?->nama }}</td>
                        <td>{{ $tagihan->siswaAkademik?->rayon?->nama }}</td>
                        <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $tagihan->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada tagihan belum lunas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
