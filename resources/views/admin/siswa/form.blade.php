@extends('layouts.app')

@section('title','Form Siswa')

@section('content')
<h2 class="fw-bold mb-4">Form Siswa</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">NIS</label>
                <input name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Nama</label>
                <input name="nama" class="form-control" value="{{ old('nama', $siswa->nama) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'L')>L</option>
                    <option value="P" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'P')>P</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="aktif" @selected(old('status', $siswa->status ?: 'aktif') === 'aktif')>Aktif</option>
                    <option value="alumni" @selected(old('status', $siswa->status) === 'alumni')>Alumni</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="form-select" required>
                    @foreach($tahunAjaran as $item)
                        <option value="{{ $item->id }}" @selected(old('tahun_ajaran_id', $akademik?->tahun_ajaran_id) == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tingkat</label>
                <select name="tingkat" class="form-select" required>
                    @foreach(['X','XI','XII'] as $tingkat)
                        <option value="{{ $tingkat }}" @selected(old('tingkat', $akademik?->tingkat) === $tingkat)>{{ $tingkat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rombel</label>
                <select name="rombel_id" class="form-select" required>
                    @foreach($rombel as $item)
                        <option value="{{ $item->id }}" @selected(old('rombel_id', $akademik?->rombel_id) == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rayon</label>
                <select name="rayon_id" class="form-select" required>
                    @foreach($rayon as $item)
                        <option value="{{ $item->id }}" @selected(old('rayon_id', $akademik?->rayon_id) == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Akun Login Siswa/Keluarga</label>
                <select name="user_id" class="form-select">
                    <option value="">Belum ditautkan</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $siswa->user_id) == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                    @endforeach
                </select>
                <div class="form-text">Akun ini dipakai siswa/orang tua untuk login dan melaporkan pembayaran.</div>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Simpan</button>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>

@if($siswa->exists)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">Riwayat Akademik</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tahun Ajaran</th><th>Tingkat</th><th>Rombel</th><th>Rayon</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($riwayatAkademik as $item)
                    <tr>
                        <td>{{ $item->tahunAjaran?->nama }}</td>
                        <td>{{ $item->tingkat }}</td>
                        <td>{{ $item->rombel?->nama }}</td>
                        <td>{{ $item->rayon?->nama }}</td>
                        <td><span class="badge bg-secondary">{{ $item->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat akademik.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
