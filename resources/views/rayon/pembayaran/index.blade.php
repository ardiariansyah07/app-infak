@extends('layouts.app')

@section('title','Pembayaran Rayon')

@section('content')
@php use App\Support\Periode; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pembayaran Rayon</h2>
        <p class="text-muted mb-0">Bantu laporkan pembayaran siswa rayon binaan.</p>
    </div>
    <a href="{{ route('rayon.pembayaran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Lapor Pembayaran
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-server-paginated="true" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}" data-total="{{ $data->total() }}">
            <thead class="table-light">
                <tr><th>Tanggal</th><th>Siswa</th><th>Nominal</th><th>Bulan Dibayar</th><th>Status</th><th>Bukti</th></tr>
            </thead>
            <tbody>
            @forelse($data as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->tanggal?->format('d/m/Y') }}</td>
                    <td>{{ $pembayaran->siswa ? $pembayaran->siswa->nis.' - '.$pembayaran->siswa->nama : '-' }}</td>
                    <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                    <td>{{ Periode::labels($pembayaran->tagihanInfak) }}</td>
                    <td>
                        <span class="badge bg-{{ $pembayaran->status_verifikasi === 'valid' ? 'success' : ($pembayaran->status_verifikasi === 'ditolak' ? 'danger' : 'warning text-dark') }}">
                            {{ $pembayaran->status_verifikasi }}
                        </span>
                    </td>
                    <td>
                        @if($pembayaran->punyaBuktiUnggahan())
                            <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank" rel="noopener noreferrer">Lihat</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada laporan pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $data->links() }}
    </div>
</div>
@endsection
