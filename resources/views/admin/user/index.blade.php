@extends('layouts.app')

@section('title','User & Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">User & Role</h2>
        <p class="text-muted mb-0">Kelola akun admin, petugas infak, pembimbing rayon, dan siswa/keluarga.</p>
    </div>
    <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Tambah User
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($data as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $roles[$user->role] ?? $user->role }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.user.edit', $user) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('admin.user.destroy', $user) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Belum ada user.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
