@extends('layouts.app')

@section('title','Tambah Pembayaran')

@section('content')
<h2 class="fw-bold mb-4">Tambah Pembayaran</h2>

<form action="{{ route($prefix . '.pembayaran.store') }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Siswa</label>
                <input type="search" class="form-control mb-2" placeholder="Cari NIS atau nama siswa..." data-select-filter="siswa_id">
                <select name="siswa_id" id="siswa_id" class="form-select" required>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}">{{ $item->nis }} - {{ $item->nama }}</option>
                    @endforeach
                </select>
                <div class="form-text">Ketik NIS atau nama, lalu pilih siswa dari dropdown.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input name="tanggal" type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nominal</label>
                <input name="nominal" type="number" min="1000" class="form-control" required>
            </div>
            <div class="col-md-12">
                <div class="alert alert-info mb-0">
                    Alokasi tagihan otomatis dari periode tertua siswa yang dipilih. Pembayaran sebagian akan tercatat sebagai sisa tagihan.
                </div>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Simpan</button>
        <a href="{{ route($prefix . '.pembayaran.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>
@endsection
