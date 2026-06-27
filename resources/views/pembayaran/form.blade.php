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
                <select name="siswa_id" class="form-select" data-searchable-select data-placeholder="Pilih data..." required>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->nis }}) - {{ $item->akademikAktif?->rayon?->nama }} - {{ $item->akademikAktif?->rombel?->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input name="tanggal" type="date" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nominal</label>
                <input name="nominal" type="number" min="1000" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
                    <option value="">Pilih...</option>
                    <option value="cash" @selected(old('metode_pembayaran') === 'cash')>Cash</option>
                    <option value="transfer" @selected(old('metode_pembayaran') === 'transfer')>Transfer</option>
                </select>
            </div>
            <div class="col-md-12">
                <div class="alert alert-info mb-0">
                    Pilih Cash untuk pembayaran tunai, atau Transfer jika siswa/orang tua menunjukkan bukti transfer kepada admin/petugas. Alokasi tagihan dilakukan otomatis dari periode tertua.
                </div>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Simpan</button>
        <a href="{{ route($prefix . '.pembayaran.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>
@endsection
