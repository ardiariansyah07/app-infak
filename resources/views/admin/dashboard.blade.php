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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Tagihan</h6>
                        <h3 class="fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-receipt"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Pembayaran Valid</h6>
                        <h3 class="fw-bold">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-cash-coin"></i></span>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mt-2">
    <div class="col-lg-8">
        <div class="card panel-soft h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Tren Pembayaran Valid</h5>
                        <p class="text-muted mb-0">Akumulasi pembayaran enam bulan terakhir.</p>
                    </div>
                </div>

                <div class="chart-bars">
                    @foreach($monthlyPayments as $payment)
                        <div class="chart-bar">
                            <div class="small fw-semibold">
                                Rp {{ number_format($payment['total'], 0, ',', '.') }}
                            </div>
                            <div class="chart-bar-track">
                                <div
                                    class="chart-bar-fill"
                                    style="height: {{ max(6, ($payment['total'] / $maxMonthlyPayment) * 100) }}%">
                                </div>
                            </div>
                            <div class="chart-label">{{ $payment['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card panel-soft h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-1">Status Tagihan</h5>
                <p class="text-muted">Distribusi status seluruh tagihan.</p>

                @foreach(['belum' => 'Belum', 'sebagian' => 'Sebagian', 'lunas' => 'Lunas'] as $status => $label)
                    @php($total = (int) ($tagihanStatus[$status] ?? 0))
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $label }}</span>
                            <span class="fw-semibold">{{ $total }}</span>
                        </div>
                        <div class="progress" style="height:10px">
                            <div
                                class="progress-bar {{ $status === 'lunas' ? 'bg-success' : ($status === 'sebagian' ? 'bg-warning' : 'bg-secondary') }}"
                                style="width: {{ ($total / $totalTagihanRows) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estimasi tunggakan</span>
                        <strong>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted">Antrian validasi</span>
                        <strong>{{ $pendingPembayaran }} pembayaran</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
