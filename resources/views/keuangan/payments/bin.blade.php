@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                Recycle Bin Pembayaran
                @if($payments->total() > 0)
                    <span class="badge bg-danger">{{ $payments->total() }}</span>
                @endif
            </h4>
            <p class="text-muted mb-0">Pembayaran yang sebelumnya dihapus (soft delete).</p>
        </div>

        <a href="{{ route('keuangan.payments.index') }}"
           class="btn btn-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>


    {{-- CARD WRAPPER --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            {{-- KOSONG --}}
            @if($payments->count() === 0)

                <div class="p-5 text-center text-muted">
                    <i class="fas fa-trash-alt fa-3x mb-3 text-secondary"></i>
                    <h5 class="fw-bold mb-1">Recycle Bin Kosong</h5>
                    <p class="mb-0">Tidak ada pembayaran yang dihapus.</p>
                </div>

            @else

                {{-- TABLE --}}
                <table class="table table-hover mb-0">
                    <thead style="background:#195D51; color:white;">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Jamaah</th>
                            <th>No Invoice</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($payments as $i => $p)
                        <tr>
                            <td>{{ $payments->firstItem() + $i }}</td>

                            <td>
                                <strong>{{ $p->jamaah->nama_lengkap }}</strong><br>
                                <small class="text-muted">{{ $p->jamaah->no_id }}</small>
                            </td>

                            <td>{{ $p->invoice->nomor_invoice }}</td>

                            <td>Rp {{ number_format($p->jumlah) }}</td>

                            <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}</td>

                            <td class="text-end">

                                {{-- RESTORE --}}
                                <form action="{{ route('keuangan.payments.restore', $p->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm btn-success rounded-pill px-3"
                                            onclick="return confirm('Pulihkan pembayaran ini?')">
                                        <i class="fas fa-undo me-1"></i> Restore
                                    </button>
                                </form>

                                {{-- FORCE DELETE --}}
                                <form action="{{ route('keuangan.payments.force', $p->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger rounded-pill px-3"
                                            onclick="return confirm('Hapus permanen? Tidak dapat dikembalikan!')">
                                        <i class="fas fa-times me-1"></i> Hapus Permanen
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif

        </div>

        {{-- PAGINATION --}}
        @if($payments->hasPages())
        <div class="card-footer bg-white">
            {!! $payments->links() !!}
        </div>
        @endif
    </div>

</div>
@endsection
