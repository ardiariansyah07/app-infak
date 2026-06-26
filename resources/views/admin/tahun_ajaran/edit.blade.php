@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')

<nav aria-label="breadcrumb">

    <ol class="breadcrumb">

        <li class="breadcrumb-item">

            <a href="{{ url('/admin/dashboard') }}">
                Dashboard
            </a>

        </li>

        <li class="breadcrumb-item">

            <a href="{{ route('admin.tahun-ajaran.index') }}">
                Tahun Ajaran
            </a>

        </li>

        <li class="breadcrumb-item active">
            Edit
        </li>

    </ol>

</nav>

<div class="mb-4">

    <h2 class="page-title mb-1">
        Edit Tahun Ajaran
    </h2>

    <p class="text-muted mb-0">
        Perbarui data tahun ajaran sekolah
    </p>

</div>

@if ($errors->any())

<div class="alert alert-danger">

    <ul class="mb-0">

        @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<div class="card card-modern">

    <div class="card-body p-4">

        <form method="POST"
              action="{{ route('admin.tahun-ajaran.update', $tahun_ajaran->id) }}">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label fw-semibold">

                    Nama Tahun Ajaran

                </label>

                <input type="text"
                       name="nama"
                       value="{{ old('nama', $tahun_ajaran->nama) }}"
                       class="form-control"
                       placeholder="Contoh: 2025/2026"
                       required>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Tanggal Mulai

                        </label>

                        <input type="date"
                               name="tanggal_mulai"
                               value="{{ old('tanggal_mulai', $tahun_ajaran->tanggal_mulai) }}"
                               class="form-control">

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Tanggal Selesai

                        </label>

                        <input type="date"
                               name="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $tahun_ajaran->tanggal_selesai) }}"
                               class="form-control">

                    </div>

                </div>

            </div>

            <div class="form-check form-switch mb-4">

                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       id="aktif"
                       name="aktif"
                       value="1"
                       {{ $tahun_ajaran->aktif ? 'checked' : '' }}>

                <label class="form-check-label"
                       for="aktif">

                    Jadikan Tahun Ajaran Aktif

                </label>

            </div>

            <div class="d-flex gap-2">

                <button type="submit"
                        class="btn btn-success">

                    <i class="bi bi-check2-square"></i>

                    Update Data

                </button>

                <a href="{{ route('admin.tahun-ajaran.index') }}"
                   class="btn btn-light border">

                    <i class="bi bi-x-circle"></i>

                    Batal

                </a>

            </div>

        </form>

    </div>

</div>

@endsection