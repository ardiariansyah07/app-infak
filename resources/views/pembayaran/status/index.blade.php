@extends('layouts.app')

@section('title','Status Pembayaran Siswa')

@section('content')
@php use App\Support\Periode; @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Status Pembayaran Siswa</h2>
        <p class="text-muted mb-0">Pantau bulan yang belum dibayar dan buka detail riwayat pembayaran siswa.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-server-paginated="true" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}" data-total="{{ $data->total() }}">
            <thead class="table-light">
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Rombel Aktif</th>
                    <th>Rayon</th>
                    <th>Bulan Belum Dibayar</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($data as $siswa)
                @php
                    $tagihanBelum = $siswa->akademik
                        ->flatMap->tagihanInfak
                        ->whereIn('status', ['belum', 'sebagian'])
                        ->sortBy('periode');
                @endphp
                <tr>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->akademikAktif?->rombel?->nama ?? '-' }}</td>
                    <td>{{ $siswa->akademikAktif?->rayon?->nama ?? '-' }}</td>
                    <td class="small">{{ Periode::labels($tagihanBelum) ?: 'Tidak ada tunggakan' }}</td>
                    <td>
                        <a href="{{ route($prefix.'.status-pembayaran.show', $siswa) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i>
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada siswa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $data->links() }}
    </div>
</div>
@endsection
