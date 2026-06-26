@extends('layouts.app')

@section('title','Siswa Rayon')

@section('content')
<h2 class="fw-bold mb-4">Siswa Rayon Binaan</h2>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>NIS</th><th>Nama</th><th>Rombel</th><th>Rayon</th><th>Komitmen</th><th>Tagihan Aktif</th></tr></thead>
            <tbody>
            @forelse($data as $akademik)
                <tr>
                    <td>{{ $akademik->siswa?->nis }}</td>
                    <td>{{ $akademik->siswa?->nama }}</td>
                    <td>{{ $akademik->rombel?->nama }}</td>
                    <td>{{ $akademik->rayon?->nama }}</td>
                    <td>Rp {{ number_format($akademik->komitmenInfak?->nominal_bulanan ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $akademik->tagihanInfak->whereIn('status', ['belum', 'sebagian'])->count() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada siswa untuk rayon binaan Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
