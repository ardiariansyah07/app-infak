@extends('layouts.app')

@section('title','Komitmen Infak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Komitmen Infak</h2>
        <p class="text-muted mb-0">Nominal infak bulanan per siswa.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @if(($prefix ?? 'admin') === 'admin')
            @include('components.import-actions', ['master' => 'komitmen-infak'])
        @endif
        <a href="{{ route((isset($prefix) ? $prefix : str(request()->route()?->getName() ?? 'admin.')->before('.')->toString()) . '.komitmen-infak.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Komitmen
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}">
            <thead class="table-light"><tr><th>Siswa</th><th>Tahun Ajaran</th><th>Rombel</th><th>Rayon</th><th>Nominal</th><th>Mulai Bulan</th><th width="160">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $komitmen)
                <tr>
                    <td>{{ $komitmen->siswaAkademik?->siswa?->nama }}</td>
                    <td>{{ $komitmen->siswaAkademik?->tahunAjaran?->nama }}</td>
                    <td>{{ $komitmen->siswaAkademik?->rombel?->nama }}</td>
                    <td>{{ $komitmen->siswaAkademik?->rayon?->nama }}</td>
                    <td>Rp {{ number_format($komitmen->nominal_bulanan, 0, ',', '.') }}</td>
                    <td>{{ optional($komitmen->mulai_bulan)->format('Y-m') ?: '-' }}</td>
                    <td>
                        <a href="{{ route((isset($prefix) ? $prefix : str(request()->route()?->getName() ?? 'admin.')->before('.')->toString()) . '.komitmen-infak.edit', $komitmen) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route((isset($prefix) ? $prefix : str(request()->route()?->getName() ?? 'admin.')->before('.')->toString()) . '.komitmen-infak.destroy', $komitmen) }}" method="POST" class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada komitmen.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $data->links() }}
    </div>
</div>
@endsection
