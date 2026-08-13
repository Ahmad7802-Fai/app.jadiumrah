@extends('layouts.admin')

@section('title','Clients')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Daftar Client</h1>
            <p class="text-muted small mb-0">
                Data client yang terdaftar di sistem
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.clients.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Tambah Client
            </a>
        </div>
    </div>


    {{-- =====================================================
    | TABLE CARD
    ===================================================== --}}
    <div class="card card-hover">

        <div class="card-header">
            <h3 class="card-title">List Client</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th class="col-actions text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($clients as $client)
                        <tr>

                            <td data-label="Nama">
                                <strong>{{ $client->nama }}</strong>
                            </td>

                            <td data-label="Tipe">
                                {{ strtoupper($client->tipe) }}
                            </td>

                            <td data-label="Email">
                                {{ $client->email }}
                            </td>

                            <td data-label="Telepon">
                                {{ $client->telepon }}
                            </td>

                            <td data-label="Aksi" class="col-actions">
                                <div class="table-actions">

                                    {{-- EDIT --}}
                                    <a href="{{ route('keuangan.clients.edit',$client->id) }}"
                                       class="btn-round btn-ju-outline"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('keuangan.clients.destroy',$client->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus client ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn-round btn-danger-ghost"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty">
                                Belum ada client terdaftar.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div>
@endsection
