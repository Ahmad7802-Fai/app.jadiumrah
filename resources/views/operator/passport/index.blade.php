@extends('layouts.admin')

@section('title','Data Passport Jamaah')

@section('content')
<div class="page-passport">

    {{-- =====================================================
       PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Passport Jamaah</h1>
            <p class="text-muted text-sm">
                Kelola data passport & cetak SRP
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions">
            <a href="{{ route('operator.passport.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Passport
            </a>
        </div>
    </div>

    {{-- =====================================================
       FILTER
    ====================================================== --}}
    <form method="GET" class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                {{-- SEARCH --}}
                <div class="col-md-5">
                    <label class="form-label text-sm">Cari Passport</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Nama jamaah / nomor passport">
                </div>

                {{-- REKOMENDASI --}}
                <div class="col-md-3">
                    <label class="form-label text-sm">Rekomendasi</label>
                    <select name="filter_rekomendasi" class="form-select">
                        <option value="">Semua</option>
                        <option value="Masih Berlaku" @selected(request('filter_rekomendasi') === 'Masih Berlaku')>
                            Masih Berlaku
                        </option>
                        <option value="Segera Perpanjang" @selected(request('filter_rekomendasi') === 'Segera Perpanjang')>
                            Segera Perpanjang
                        </option>
                        <option value="Perlu Perpanjang" @selected(request('filter_rekomendasi') === 'Perlu Perpanjang')>
                            Perlu Perpanjang
                        </option>
                    </select>
                </div>

                {{-- ACTION --}}
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    <a href="{{ route('operator.passport.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    {{-- =====================================================
       TABLE (DESKTOP + MOBILE AUTO)
    ====================================================== --}}
    <div class="card card-hover">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Jamaah</th>
                            <th>No Passport</th>
                            <th>Tgl Terbit</th>
                            <th>Tgl Habis</th>
                            <th class="table-center">Rekomendasi</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($passports as $passport)
                        <tr>

                            {{-- NO --}}
                            <td data-label="#">
                                {{ $loop->iteration + ($passports->currentPage() - 1) * $passports->perPage() }}
                            </td>

                            {{-- JAMAAH --}}
                            <td data-label="Jamaah">
                                <div class="fw-semibold">
                                    {{ $passport->jamaah->nama_lengkap ?? '-' }}
                                </div>
                                <div class="text-muted text-sm">
                                    {{ $passport->jamaah->no_id ?? '-' }}
                                </div>
                            </td>

                            {{-- PASSPORT --}}
                            <td data-label="No Passport">
                                {{ $passport->nomor_paspor }}
                            </td>

                            <td data-label="Tgl Terbit">
                                {{ $passport->tanggal_terbit_paspor }}
                            </td>

                            <td data-label="Tgl Habis">
                                {{ $passport->tanggal_habis_paspor }}
                            </td>

                            {{-- REKOMENDASI --}}
                            <td data-label="Rekomendasi" class="table-center">
                                @php
                                    $passportBadgeClass = match ($passport->rekomendasi_paspor) {
                                        'Masih Berlaku'      => 'badge-soft-success',
                                        'Segera Perpanjang'  => 'badge-soft-warning',
                                        default              => 'badge-soft-danger',
                                    };
                                @endphp

                                <span class="badge {{ $passportBadgeClass }}">
                                    {{ $passport->rekomendasi_paspor }}
                                </span>
                            </td>

                            {{-- ACTION --}}
                            <td class="table-right col-actions">
                                <div class="table-actions">
                                    <a href="{{ route('operator.passport.edit', $passport->id) }}"
                                       class="btn btn-outline-primary btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('operator.passport.print', $passport->id) }}"
                                       target="_blank"
                                       class="btn btn-outline-danger btn-xs">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <a href="{{ route('operator.passport.srp', $passport->id) }}"
                                       target="_blank"
                                       class="btn btn-outline-success btn-xs">
                                        <i class="fas fa-stamp"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">
                                Belum ada data passport
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- =====================================================
       PAGINATION
    ====================================================== --}}
    @if ($passports->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $passports->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- =====================================================
   FAB — MOBILE ONLY
====================================================== --}}
<div class="d-md-none">
    <x-fab.add route="{{ route('operator.passport.create') }}" />
</div>
@endsection
