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
                <select name="siswa_id" class="form-select" required>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} - {{ $item->nis }}</option>
                    @endforeach
                </select>
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
                <label class="form-label">Alokasi Tagihan</label>
                <select name="tagihan_infak_ids[]" class="form-select" multiple>
                    @foreach($tagihan as $item)
                        <option value="{{ $item->id }}">{{ $item->siswaAkademik?->siswa?->nama }} - {{ $item->periode }} - sisa Rp {{ number_format($item->sisa, 0, ',', '.') }}</option>
                    @endforeach
                </select>
                <div class="form-text">Pilih tagihan yang dibayar. Sistem akan mengalokasikan nominal dari periode terlama.</div>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Simpan</button>
        <a href="{{ route($prefix . '.pembayaran.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>
@endsection
