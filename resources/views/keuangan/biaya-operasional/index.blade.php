@extends('layouts.admin')

@section('title','Biaya Operasional')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Biaya Operasional</h1>
            <p class="page-subtitle">
                Pengeluaran kantor & keberangkatan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.operasional.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah
            </a>
        </div>
    </div>


    {{-- =====================================================
    | SUMMARY
    ===================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card card-stat">
                <div class="stat-label">
                    Pengeluaran Bulan {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
                </div>
                <div class="stat-value text-danger">
                    Rp {{ number_format($totalBulan) }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat">
                <div class="stat-label">
                    Pengeluaran Tahun {{ $tahun }}
                </div>
                <div class="stat-value text-primary">
                    Rp {{ number_format($totalTahun) }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        Top Kategori — {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
                    </h6>
                </div>
                <div class="card-body card-body-sm">
                    @forelse($kategoriBulan as $kb)
                        <div class="d-flex justify-content-between py-1">
                            <span>{{ $kb->kategori }}</span>
                            <strong>Rp {{ number_format($kb->total) }}</strong>
                        </div>
                    @empty
                        <div class="text-muted text-sm">
                            Belum ada data
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>


    {{-- =====================================================
    | FILTER (SCSS COMPONENT)
    ===================================================== --}}
    <form method="GET" class="filter mb-4">

        <div class="filter-header">
            <div class="filter-title">
                Filter Data
            </div>

            <div class="filter-toggle"
                 onclick="this.closest('.filter').classList.toggle('is-open')">
                Tampilkan
            </div>
        </div>

        <div class="filter-body">

            <div class="filter-item">
                <label>Pencarian</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Kategori / deskripsi">
            </div>

            <div class="filter-item">
                <label>Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" @selected($bulan==$b)>
                            {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label>Tahun</label>
                <select name="tahun" class="form-select">
                    @foreach(range(2023, date('Y')) as $t)
                        <option value="{{ $t }}" @selected($tahun==$t)>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="filter-actions">
            <button class="btn btn-primary btn-sm">
                Filter
            </button>

            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-sm">
                Reset
            </a>
        </div>

    </form>


    {{-- =====================================================
    | EXPORT
    ===================================================== --}}
    <div class="mb-4 d-flex gap-2">
        <a href="{{ route('keuangan.operasional.excel', compact('bulan','tahun')) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-excel"></i>
            Excel
        </a>

        <a href="{{ route('keuangan.operasional.pdf', compact('bulan','tahun')) }}"
           class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i>
            PDF
        </a>
    </div>


    {{-- =====================================================
    | TABLE (AUTO MOBILE)
    ===================================================== --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-compact">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th class="table-right">Jumlah</th>
                        <th>Tanggal</th>
                        <th>Oleh</th>
                        <th class="col-actions table-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($data as $i => $row)
                    <tr>
                        <td data-label="#">
                            {{ $data->firstItem() + $i }}
                        </td>

                        <td data-label="Kategori">
                            <strong>{{ $row->kategori }}</strong>
                        </td>

                        <td data-label="Deskripsi">
                            {{ Str::limit($row->deskripsi, 60) }}
                        </td>

                        <td data-label="Jumlah" class="table-right text-danger fw-semibold">
                            Rp {{ number_format($row->jumlah) }}
                        </td>

                        <td data-label="Tanggal">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                        </td>

                        <td data-label="Oleh">
                            {{ $row->user->name ?? '-' }}
                        </td>

                        <td class="col-actions table-actions">
                            <a href="{{ route('keuangan.operasional.show',$row->id) }}"
                               class="btn btn-outline-secondary btn-xs">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('keuangan.operasional.edit',$row->id) }}"
                               class="btn btn-outline-secondary btn-xs">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form method="POST"
                                  action="{{ route('keuangan.operasional.destroy',$row->id) }}"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-empty">
                            Belum ada data
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
