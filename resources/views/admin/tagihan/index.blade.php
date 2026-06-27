@extends('layouts.app')

@section('title','Tagihan Infak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Tagihan Infak</h2>
        <p class="text-muted mb-0">Tagihan bulanan dan status pelunasannya.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', [
            'master' => 'saldo-awal',
            'templateLabel' => 'Template Saldo Awal',
            'importLabel' => 'Import Saldo Awal',
        ])
        @include('components.import-actions', [
            'master' => 'tagihan-awal',
            'templateLabel' => 'Template Per Bulan',
            'importLabel' => 'Import Per Bulan',
        ])
        <a href="{{ route('admin.tagihan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Tagihan
        </a>
    </div>
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

<div class="alert alert-info">
    Untuk data awal tanpa riwayat detail, gunakan <strong>Template Saldo Awal</strong>.
    Isi satu baris per siswa: bulan mulai, nominal bulanan, jumlah bulan lunas, sebagian, dan belum.
    Jika punya rincian per bulan, gunakan <strong>Template Per Bulan</strong>.
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body row g-2 align-items-end">
        <input type="hidden" name="q" value="{{ $search }}">
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="belum" @selected($status === 'belum')>Belum</option>
                <option value="sebagian" @selected($status === 'sebagian')>Sebagian</option>
                <option value="lunas" @selected($status === 'lunas')>Lunas</option>
            </select>
        </div>
        <div class="col-md-8 d-flex gap-2">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.tagihan.index') }}" class="btn btn-light">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" data-server-paginated="true" data-row-offset="{{ ($data->firstItem() ?? 1) - 1 }}" data-total="{{ $data->total() }}">
            <thead class="table-light"><tr><th>Periode</th><th>Tahun Ajaran</th><th>Siswa</th><th>Rombel</th><th>Rayon</th><th>Nominal</th><th>Status</th><th width="140">Aksi</th></tr></thead>
            <tbody>
            @forelse($data as $tagihan)
                <tr>
                    <td>{{ \App\Support\Periode::label($tagihan->periode) }}</td>
                    <td>{{ $tagihan->siswaAkademik?->tahunAjaran?->nama }}</td>
                    <td>{{ $tagihan->siswaAkademik?->siswa?->nama }}</td>
                    <td>{{ $tagihan->siswaAkademik?->rombel?->nama }}</td>
                    <td>{{ $tagihan->siswaAkademik?->rayon?->nama }}</td>
                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $tagihan->status === 'lunas' ? 'success' : ($tagihan->status === 'sebagian' ? 'warning text-dark' : 'warning text-dark') }}">{{ $tagihan->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.tagihan.edit', $tagihan) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('admin.tagihan.destroy', $tagihan) }}" method="POST" class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada tagihan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $data->links() }}
    </div>
</div>
@endsection
