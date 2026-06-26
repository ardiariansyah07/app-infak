@extends('layouts.app')

@section('title','Orang Tua')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Orang Tua</h2>
        <p class="text-muted mb-0">Kelola akun dan data orang tua siswa.</p>
    </div>
    <a href="{{ route('admin.orang-tua.create') }}" class="btn btn-primary">Tambah Orang Tua</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>User Login</th><th>Anak</th><th width="140">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $orangTua)
                <tr>
                    <td>{{ $orangTua->nama }}</td>
                    <td>{{ $orangTua->email ?? '-' }}</td>
                    <td>{{ $orangTua->user?->email ?? '-' }}</td>
                    <td>{{ $orangTua->siswa->pluck('nama')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('admin.orang-tua.edit', $orangTua) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.orang-tua.destroy', $orangTua) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus orang tua?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada orang tua.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
