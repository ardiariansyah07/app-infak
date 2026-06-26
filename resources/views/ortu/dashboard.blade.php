@extends('layouts.app')

@section('title','Dashboard Orang Tua')

@section('content')

<h2 class="mb-4">Dashboard Orang Tua</h2>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted">Tagihan Aktif</div>
                <h3>{{ $tagihanAktif }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted">Laporan Pending</div>
                <h3>{{ $pembayaranPending }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <a href="{{ route('ortu.pembayaran.create') }}" class="btn btn-primary">Laporkan Pembayaran</a>
    <a href="{{ route('ortu.pembayaran.index') }}" class="btn btn-outline-primary">Riwayat Pembayaran</a>
    <a href="{{ route('ortu.komitmen.index') }}" class="btn btn-outline-secondary">Nominal Infak</a>
</div>

<div class="mt-4">
    @include('components.payment-card', ['tagihan' => $tagihanBulanan, 'title' => 'Kartu Bayaran'])
</div>

@endsection
