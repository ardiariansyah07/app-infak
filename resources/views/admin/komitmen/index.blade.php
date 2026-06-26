@extends('layouts.app')

@section('title','Komitmen Infak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Komitmen Infak</h2>
        <p class="text-muted mb-0">Nominal infak bulanan per siswa.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', ['master' => 'komitmen-infak'])
        <a href="{{ route('admin.komitmen-infak.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Komitmen
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Siswa</th><th>Rombel</th><th>Rayon</th><th>Nominal</th><th width="140">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $komitmen)
                <tr>
                    <td>{{ $komitmen->siswaAkademik?->siswa?->nama }}</td>
                    <td>{{ $komitmen->siswaAkademik?->rombel?->nama }}</td>
                    <td>{{ $komitmen->siswaAkademik?->rayon?->nama }}</td>
                    <td>Rp {{ number_format($komitmen->nominal_bulanan, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('admin.komitmen-infak.edit', $komitmen) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.komitmen-infak.destroy', $komitmen) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus komitmen?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada komitmen.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
