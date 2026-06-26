@extends('layouts.app')

@section('title','Laporkan Pembayaran')

@section('content')
<h2 class="fw-bold mb-4">Laporkan Pembayaran Infak</h2>

<form action="{{ route('ortu.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Anak</label>
                <select name="siswa_id" class="form-select" required>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} - {{ $item->nis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Bayar</label>
                <input name="tanggal" type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nominal</label>
                <input name="nominal" type="number" min="1000" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">Bulan yang Dibayar</label>
                <select name="tagihan_infak_ids[]" class="form-select" multiple required>
                    @foreach($tagihan as $item)
                        <option value="{{ $item->id }}">{{ $item->siswaAkademik?->siswa?->nama }} - {{ $item->periode }} - sisa Rp {{ number_format($item->sisa, 0, ',', '.') }}</option>
                    @endforeach
                </select>
                <div class="form-text">Tekan Ctrl/Cmd untuk memilih lebih dari satu bulan.</div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Bukti Bayar</label>
                <input name="bukti_transfer" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="form-text">Format JPG, PNG, atau PDF. Maksimal 2 MB.</div>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Kirim Laporan</button>
        <a href="{{ route('ortu.pembayaran.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>
@endsection
