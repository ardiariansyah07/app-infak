@extends('layouts.app')

@section('title','Tagihan Infak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Tagihan Infak</h2>
        <p class="text-muted mb-0">Tagihan bulanan dan status pelunasannya.</p>
    </div>
    <a href="{{ route('admin.tagihan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Tambah Tagihan
    </a>
</div>

<form action="{{ route('admin.tagihan.generate') }}" method="POST" class="card border-0 shadow-sm mb-4">
    @csrf
    <div class="card-body d-flex gap-2 align-items-end">
        <div>
            <label class="form-label">Generate Periode</label>
            <input name="periode" type="month" class="form-control" required>
        </div>
        <button class="btn btn-outline-primary">Generate dari Komitmen</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Periode</th><th>Siswa</th><th>Rombel</th><th>Rayon</th><th>Nominal</th><th>Status</th><th width="140">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $tagihan)
                <tr>
                    <td>{{ $tagihan->periode }}</td>
                    <td>{{ $tagihan->siswaAkademik?->siswa?->nama }}</td>
                    <td>{{ $tagihan->siswaAkademik?->rombel?->nama }}</td>
                    <td>{{ $tagihan->siswaAkademik?->rayon?->nama }}</td>
                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $tagihan->status === 'lunas' ? 'success' : ($tagihan->status === 'sebagian' ? 'warning' : 'secondary') }}">{{ $tagihan->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.tagihan.edit', $tagihan) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.tagihan.destroy', $tagihan) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus tagihan?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada tagihan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
