@extends('layouts.app')

@section('title','Dashboard Admin')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard Admin</h2>
        <p class="text-muted mb-0">Ringkasan kondisi infak, pembayaran, dan data siswa.</p>
    </div>
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-primary">
        <i class="bi bi-credit-card"></i>
        Validasi Pembayaran
    </a>
</div>

<div class="row g-3">

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Siswa</h6>
                        <h3 class="fw-bold">{{ $totalSiswa }}</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-mortarboard"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Rayon</h6>
                        <h3 class="fw-bold">{{ $totalRayon }}</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-diagram-3"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="text-muted">Total Tagihan</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="text-muted">Pembayaran Valid</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

</div>

<div class="card panel-soft mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">Antrian Validasi</h5>
            <p class="text-muted mb-0">Pembayaran dari siswa/keluarga yang menunggu pengecekan petugas.</p>
        </div>
        <span class="badge bg-warning text-dark fs-6">{{ $pendingPembayaran }} pending</span>
    </div>
</div>

@endsection
