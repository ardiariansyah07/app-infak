@extends('layouts.guest')

@section('content')

<div class="container-fluid min-vh-100">

    <div class="row min-vh-100">

        <!-- LEFT -->

        <div class="col-lg-7 d-none d-lg-flex align-items-center justify-content-center login-left">

            <div class="text-white p-5">

                <div class="brand-mark mb-4">

                    <i class="bi bi-wallet2 fs-1"></i>

                </div>

                <h1 class="fw-bold mb-3">

                    Sistem Informasi
                    <br>
                    Infak Sekolah

                </h1>

                <p class="fs-5 text-light opacity-75">

                    Kelola pembayaran infak siswa
                    secara cepat, aman dan transparan.

                </p>

                <div class="mt-5">

                    <div class="overview-item">

                        <span class="overview-icon"><i class="bi bi-shield-check"></i></span>

                        <div>
                            <div class="fw-semibold">Akses sesuai peran</div>
                            <div class="text-white-50">Admin, petugas infak, siswa/keluarga, dan pembimbing rayon.</div>
                        </div>

                    </div>

                    <div class="overview-item">

                        <span class="overview-icon"><i class="bi bi-receipt"></i></span>

                        <div>
                            <div class="fw-semibold">Tagihan dan pembayaran</div>
                            <div class="text-white-50">Pantau status belum, sebagian, lunas, hingga bukti pembayaran.</div>
                        </div>

                    </div>

                    <div class="overview-item">

                        <span class="overview-icon"><i class="bi bi-clock-history"></i></span>

                        <div>
                            <div class="fw-semibold">Riwayat akademik</div>
                            <div class="text-white-50">Data siswa tetap terbaca dari kelas X hingga XII meski rombel berubah.</div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="col-lg-5 d-flex align-items-center justify-content-center">

            <div class="login-card shadow">

                <div class="text-center mb-4">

                    <div class="brand-mark text-white mb-3">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>

                    <h3 class="fw-bold mt-3">

                        Selamat Datang

                    </h3>

                    <p class="text-muted">

                        Login ke Sistem Infak

                    </p>

                </div>

                <form method="POST"
                      action="{{ route('login') }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">

                            Email

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-envelope"></i>

                            </span>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control"
                                required
                                autofocus>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Password

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-lock"></i>

                            </span>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="form-check mb-4">

                        <input
                            type="checkbox"
                            name="remember"
                            class="form-check-input"
                            id="remember">

                        <label
                            class="form-check-label"
                            for="remember">

                            Ingat Saya

                        </label>

                    </div>

                    <button
                        class="btn btn-primary w-100 py-2">

                        <i class="bi bi-box-arrow-in-right"></i>

                        Masuk ke Sistem

                    </button>

                </form>

                <hr>

                <div class="text-center text-muted">

                    © {{ date('Y') }}

                    Sistem Informasi Infak Sekolah

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
