@extends('layouts.app')

@section('title','Siswa Rayon')

@section('content')
<h2 class="fw-bold mb-4">Siswa Rayon Binaan</h2>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <small class="text-muted">Total Tunggakan Keseluruhan</small>
        <h4 class="fw-bold mb-0">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h4>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-server-paginated="true" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}" data-total="{{ $data->total() }}">
            <thead class="table-light"><tr><th>NIS</th><th>Nama</th><th>Rombel</th><th>Rayon</th><th>Komitmen</th><th>Bulan Lunas</th><th>Bulan Belum</th><th>Nominal Tunggakan</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $akademik)
                <tr>
                    <td>{{ $akademik->siswa?->nis }}</td>
                    <td>{{ $akademik->siswa?->nama }}</td>
                    <td>{{ $akademik->rombel?->nama }}</td>
                    <td>{{ $akademik->rayon?->nama }}</td>
                    <td>Rp {{ number_format($akademik->komitmenInfak?->nominal_bulanan ?? 0, 0, ',', '.') }}</td>
                    <td><span class="badge bg-success">{{ $akademik->bulan_lunas_count }}</span></td>
                    <td><span class="badge bg-warning text-dark">{{ $akademik->bulan_belum_count }}</span></td>
                    <td>Rp {{ number_format($akademik->nominal_tunggakan, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('rayon.siswa.show', $akademik->siswa) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada siswa untuk rayon binaan Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        {{ $data->links() }}
    </div>
</div>
@endsection
