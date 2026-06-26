@extends('layouts.app')

@section('title','Siswa Rayon')

@section('content')
@php use App\Support\Periode; @endphp

<h2 class="fw-bold mb-4">Siswa Rayon Binaan</h2>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>NIS</th><th>Nama</th><th>Rombel</th><th>Rayon</th><th>Komitmen</th><th>Bulan Lunas</th><th>Bulan Belum</th></tr></thead>
            <tbody>
            @forelse($data as $akademik)
                <tr>
                    <td>{{ $akademik->siswa?->nis }}</td>
                    <td>{{ $akademik->siswa?->nama }}</td>
                    <td>{{ $akademik->rombel?->nama }}</td>
                    <td>{{ $akademik->rayon?->nama }}</td>
                    <td>Rp {{ number_format($akademik->komitmenInfak?->nominal_bulanan ?? 0, 0, ',', '.') }}</td>
                    <td class="small">{{ Periode::labels($akademik->tagihanInfak->where('status', 'lunas')) ?: '-' }}</td>
                    <td class="small">{{ Periode::labels($akademik->tagihanInfak->whereIn('status', ['belum', 'sebagian'])) ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada siswa untuk rayon binaan Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
