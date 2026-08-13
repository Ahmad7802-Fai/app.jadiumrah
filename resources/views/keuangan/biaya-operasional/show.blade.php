@extends('layouts.admin')

@section('title','Detail Biaya Operasional')

@section('content')
<div class="page-container" style="max-width:auto">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div class="page-header-left">

            {{-- MOBILE BACK --}}
            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-icon d-md-none">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="page-title">Detail Biaya Operasional</h1>
                <p class="page-subtitle">
                    Informasi lengkap pengeluaran operasional
                </p>
            </div>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>


    {{-- =====================================================
    | DETAIL CARD
    ===================================================== --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                {{ $item->kategori }}
            </h3>
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- LEFT: META --}}
                <div class="col-md-6">

                    <table class="table table-borderless mb-0">
                        <tbody>

                            <tr>
                                <td class="text-muted" width="160">Kategori</td>
                                <td class="fw-semibold">
                                    {{ $item->kategori }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted">Deskripsi</td>
                                <td>
                                    {{ $item->deskripsi ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted">Jumlah</td>
                                <td class="fw-bold text-danger">
                                    Rp {{ number_format($item->jumlah,0,',','.') }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted">Tanggal</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted">Dibuat Oleh</td>
                                <td>
                                    {{ $item->user->name ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-muted">Dibuat Pada</td>
                                <td>
                                    {{ optional($item->created_at)->format('d M Y H:i') }}
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </div>


                {{-- RIGHT: BUKTI --}}
                <div class="col-md-6">

                    <div class="fw-semibold mb-2">
                        Bukti Pengeluaran
                    </div>

                    @if($item->bukti)

                        @php
                            $ext = strtolower(pathinfo($item->bukti, PATHINFO_EXTENSION));
                            $src = asset('storage/'.$item->bukti);
                        @endphp

                        {{-- IMAGE --}}
                        @if(in_array($ext, ['jpg','jpeg','png']))
                            <img src="{{ $src }}"
                                 alt="Bukti"
                                 class="img-thumbnail mb-2"
                                 style="max-width:280px">

                            <div>
                                <a href="{{ $src }}"
                                   target="_blank"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-image"></i>
                                    Lihat Ukuran Penuh
                                </a>
                            </div>

                        {{-- PDF --}}
                        @else
                            <a href="{{ $src }}"
                               target="_blank"
                               class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf"></i>
                                Lihat Dokumen PDF
                            </a>
                        @endif

                    @else
                        <p class="text-muted fst-italic">
                            Tidak ada bukti terlampir.
                        </p>
                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
        | ACTIONS
        ===================================================== --}}
        <div class="card-footer d-flex gap-2 justify-content-end">

            <a href="{{ route('keuangan.operasional.edit', $item->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i>
                Edit
            </a>

            <form action="{{ route('keuangan.operasional.destroy', $item->id) }}"
                  method="POST"
                  onsubmit="return confirm('Hapus biaya operasional ini?')">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                    Hapus
                </button>
            </form>

        </div>

    </div>

</div>
@endsection
