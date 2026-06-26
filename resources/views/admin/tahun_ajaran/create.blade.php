@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')

<div class="mb-4">

    <h2 class="page-title mb-1">
        Tambah Tahun Ajaran
    </h2>

    <p class="text-muted mb-0">
        Tambahkan data tahun ajaran baru
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
              action="{{ route('admin.tahun-ajaran.store') }}">

            @csrf

            <div class="mb-3">

                <label class="form-label fw-semibold">

                    Nama Tahun Ajaran

                    <span class="text-danger">*</span>

                </label>

                <input
                    type="text"
                    name="nama"
                    class="form-control"
                    placeholder="Contoh: 2025/2026"
                    required>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Tanggal Mulai

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            class="form-control"
                            required>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Tanggal Selesai

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            class="form-control"
                            required>

                    </div>

                </div>

            </div>

            <div class="alert alert-info mb-4">
                Status aktif ditentukan otomatis berdasarkan tanggal mulai dan tanggal selesai.
            </div>

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="bi bi-check-circle"></i>

                    Simpan Data

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
