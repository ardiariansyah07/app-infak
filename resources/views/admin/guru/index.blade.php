@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">

    <div>

        <nav>

            <ol class="breadcrumb mb-2">

                <li class="breadcrumb-item">

                    <a href="{{ route('admin.dashboard') }}">

                        Dashboard

                    </a>

                </li>

                <li class="breadcrumb-item">

                    Master Data

                </li>

                <li class="breadcrumb-item active">

                    Guru

                </li>

            </ol>

        </nav>

        <h2 class="fw-bold mb-1">

            Guru

        </h2>

        <p class="text-muted">

            Kelola data guru sekolah

        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', ['master' => 'guru'])

        <a href="{{ route('admin.guru.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>

            Tambah Guru

        </a>

    </div>

</div>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-2">

                <select class="form-select">

                    <option>10</option>

                    <option>25</option>

                    <option>50</option>

                </select>

            </div>

            <div class="col-md-4 ms-auto">

                <input
                    type="text"
                    class="form-control"
                    placeholder="Cari Guru...">

            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="70">No</th>

                        <th>NIP</th>

                        <th>Nama</th>

                        <th>JK</th>

                        <th>No HP</th>

                        <th>Status</th>

                        <th width="170">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($data as $guru)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ $guru->nip }}

                        </td>

                        <td>

                            {{ $guru->nama }}

                        </td>

                        <td>

                            {{ $guru->jenis_kelamin }}

                        </td>

                        <td>

                            {{ $guru->no_hp }}

                        </td>

                        <td>

                            @if($guru->aktif)

                                <span class="badge bg-success">

                                    Aktif

                                </span>

                            @else

                                <span class="badge bg-secondary">

                                    Tidak Aktif

                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('admin.guru.edit',$guru->id) }}"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-square"></i>

                            </a>

                            <form
                                action="{{ route('admin.guru.destroy',$guru->id) }}"
                                method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus data ini?')">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7"
                            class="text-center py-5">

                            <i class="bi bi-inbox display-5 text-secondary"></i>

                            <br><br>

                            Belum ada data Guru.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
