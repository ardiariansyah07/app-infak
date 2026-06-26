@extends('layouts.app')

@section('title','Dashboard Petugas Infak')

@section('content')

<h2 class="mb-4">Dashboard Petugas Infak</h2>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted">Menunggu Validasi</div>
                <h3>{{ $pendingPembayaran }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted">Pembayaran Valid</div>
                <h3>{{ $validPembayaran }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted">Tagihan Aktif</div>
                <h3>{{ $tagihanBelum }}</h3>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('petugas.pembayaran.index') }}" class="btn btn-primary mt-4">
    Kelola Pembayaran
</a>

@endsection
