@extends('layouts.app')

@section('title','Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">Laporan Infak</h2>
        <p class="text-muted mb-0">Ringkasan untuk administrasi sekolah dan bahan laporan kepada kepala sekolah.</p>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer"></i>
        Cetak
    </button>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label">Filter Periode</label>
            <input type="month" name="periode" value="{{ $periode }}" class="form-control">
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-funnel"></i>
            Terapkan
        </button>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-light border">Reset</a>
    </div>
</form>

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
                                <td><span class="badge bg-secondary">{{ ucfirst($status->status) }}</span></td>
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
                        <td>{{ $tagihan->periode }}</td>
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
