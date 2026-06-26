@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">

    <div>

        <h2 class="fw-bold mb-1">

            Pembimbing Rayon

        </h2>

        <p class="text-muted">

            Kelola data pembimbing rayon sekolah

        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap justify-content-end">
        @include('components.import-actions', ['master' => 'guru'])

        <a href="{{ route('admin.guru.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>

            Tambah Pembimbing Rayon

        </a>

    </div>

</div>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

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
                                Edit

                            </a>

                            <form
                                action="{{ route('admin.guru.destroy',$guru->id) }}"
                                method="POST"
                                class="d-inline delete-form">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm">

                                    <i class="bi bi-trash"></i>
                                    Hapus

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

                            Belum ada data pembimbing rayon.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
