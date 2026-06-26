@extends('layouts.app')

@section('title','Pembayaran')

@section('content')
@php use App\Support\Periode; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pembayaran</h2>
        <p class="text-muted mb-0">Tambah dan validasi pembayaran infak.</p>
    </div>
    <a href="{{ route($prefix . '.pembayaran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Tambah Pembayaran
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Siswa</th><th>Nominal</th><th>Bulan Dibayar</th><th>Status</th><th>Bukti</th><th width="220">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->tanggal?->format('d/m/Y') }}</td>
                    <td>{{ $pembayaran->siswa ? $pembayaran->siswa->nis.' - '.$pembayaran->siswa->nama : '-' }}</td>
                    <td>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                    <td>{{ Periode::labels($pembayaran->tagihanInfak) }}</td>
                    <td><span class="badge bg-{{ $pembayaran->status_verifikasi === 'valid' ? 'success' : ($pembayaran->status_verifikasi === 'ditolak' ? 'danger' : 'warning') }}">{{ $pembayaran->status_verifikasi }}</span></td>
                    <td>
                        @if($pembayaran->bukti_transfer)
                            <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank">Lihat</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($pembayaran->status_verifikasi === 'pending')
                            <form action="{{ route($prefix . '.pembayaran.verify', $pembayaran) }}" method="POST" class="d-inline confirm-form" data-confirm-title="Validasi Pembayaran?" data-confirm-text="Pembayaran akan ditandai valid dan mengurangi tagihan." data-confirm-button="Ya, Validasi">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status_verifikasi" value="valid">
                                <button class="btn btn-sm btn-success"><i class="bi bi-check-circle"></i> Valid</button>
                            </form>
                            <form action="{{ route($prefix . '.pembayaran.verify', $pembayaran) }}" method="POST" class="d-inline confirm-form" data-confirm-title="Tolak Pembayaran?" data-confirm-text="Pembayaran akan ditandai ditolak." data-confirm-icon="warning" data-confirm-button="Ya, Tolak">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status_verifikasi" value="ditolak">
                                <button class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i> Tolak</button>
                            </form>
                        @else
                            <span class="text-muted">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
