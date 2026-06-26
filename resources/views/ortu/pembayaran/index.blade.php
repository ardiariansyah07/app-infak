@extends('layouts.app')

@section('title','Riwayat Pembayaran')

@section('content')
@php use App\Support\Periode; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Riwayat Pembayaran</h2>
        <p class="text-muted mb-0">Pembayaran infak anak yang sudah dilaporkan.</p>
    </div>
    <a href="{{ route('ortu.pembayaran.create') }}" class="btn btn-primary">Laporkan Pembayaran</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Anak</th><th>Nominal</th><th>Bulan Dibayar</th><th>Status</th><th>Bukti</th></tr></thead>
            <tbody>
            @forelse($data as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->tanggal?->format('d/m/Y') }}</td>
                    <td>{{ $pembayaran->siswa?->nama }}</td>
                    <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                    <td>{{ Periode::labels($pembayaran->tagihanInfak) }}</td>
                    <td><span class="badge bg-{{ $pembayaran->status_verifikasi === 'valid' ? 'success' : ($pembayaran->status_verifikasi === 'ditolak' ? 'danger' : 'warning') }}">{{ $pembayaran->status_verifikasi }}</span></td>
                    <td><a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank" rel="noopener noreferrer">Lihat</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    @include('components.payment-card', ['tagihan' => $tagihanBulanan, 'title' => 'Kartu Bayaran'])
</div>
@endsection
