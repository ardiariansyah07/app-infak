@extends('layouts.app')

@section('title','Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Siswa</h2>
        <p class="text-muted mb-0">Data NIS, nama, rombel, rayon, dan orang tua.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', ['master' => 'siswa'])
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Siswa
        </a>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Cari Siswa</label>
            <input name="q" class="form-control" value="{{ $search }}" placeholder="NIS, nama, rayon, atau rombel">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-light">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}">
            <thead class="table-light">
                <tr><th>NIS</th><th>Nama</th><th>Rombel Aktif</th><th>Rayon</th><th>Akun Login</th><th>Status</th><th width="160">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($data as $siswa)
                <tr>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->akademikAktif?->rombel?->nama ?? '-' }}</td>
                    <td>{{ $siswa->akademikAktif?->rayon?->nama ?? '-' }}</td>
                    <td>{{ $siswa->user?->email ?? '-' }}</td>
                    <td><span class="badge bg-{{ $siswa->status === 'aktif' ? 'success' : 'secondary' }}">{{ $siswa->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('admin.siswa.destroy', $siswa) }}" method="POST" class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada siswa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $data->links() }}
    </div>
</div>
@endsection
