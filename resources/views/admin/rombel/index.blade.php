@extends('layouts.app')

@section('title','Rombel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Rombel/Kelas</h2>
        <p class="text-muted mb-0">Kelola data rombel siswa.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', ['master' => 'rombel'])
        <a href="{{ route('admin.rombel.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Rombel
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Tingkat</th><th width="140">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $rombel)
                <tr>
                    <td>{{ $rombel->nama }}</td>
                    <td>{{ $rombel->tingkat ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.rombel.edit', $rombel) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('admin.rombel.destroy', $rombel) }}" method="POST" class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada rombel.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
