@extends('layouts.app')

@section('title','Detail Status Pembayaran')

@section('content')
@php use App\Support\Periode; @endphp

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $siswa->nis }} - {{ $siswa->nama }}</h2>
        <p class="text-muted mb-0">Detail tagihan dan riwayat pembayaran siswa.</p>
    </div>
    <a href="{{ route($prefix.'.status-pembayaran.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Status Tagihan Bulanan</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Periode</th><th>Tahun Ajaran</th><th>Rombel</th><th>Nominal</th><th>Terbayar</th><th>Sisa</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($tagihan as $item)
                    <tr>
                        <td>{{ Periode::label($item->periode) }}</td>
                        <td>{{ $item->siswaAkademik?->tahunAjaran?->nama ?? '-' }}</td>
                        <td>{{ $item->siswaAkademik?->rombel?->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->terbayar, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->sisa, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status === 'lunas' ? 'success' : 'warning text-dark' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tagihan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Riwayat Pembayaran</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal Bayar</th><th>Nominal</th><th>Bulan Dibayar</th><th>Alokasi</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($riwayat as $pembayaran)
                    <tr>
                        <td>{{ $pembayaran->tanggal?->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                        <td>{{ Periode::labels($pembayaran->alokasiPembayaran->pluck('tagihanInfak')) }}</td>
                        <td>
                            @foreach($pembayaran->alokasiPembayaran as $alokasi)
                                <div class="small">
                                    {{ Periode::label($alokasi->tagihanInfak?->periode) }}:
                                    Rp {{ number_format($alokasi->nominal, 0, ',', '.') }}
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-{{ $pembayaran->status_verifikasi === 'valid' ? 'success' : ($pembayaran->status_verifikasi === 'ditolak' ? 'danger' : 'warning text-dark') }}">
                                {{ $pembayaran->status_verifikasi }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pembayaran.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
