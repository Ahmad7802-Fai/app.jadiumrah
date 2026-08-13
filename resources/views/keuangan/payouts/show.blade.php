@extends('layouts.admin')

@section('title','Detail Payout')
@section('subtitle','Audit & rincian pencairan komisi agent')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | ACTION BAR
    ===================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        {{-- BACK --}}
        <a href="{{ route('keuangan.payout.index') }}"
        class="btn btn-outline btn-sm">
            ← Kembali
        </a>

        {{-- EXPORT --}}
        <a href="{{ route('keuangan.payout.export-pdf', $payout->id) }}"
        target="_blank"
        class="btn btn-primary btn-sm">
            📄 Export PDF
        </a>

    </div>


    {{-- =====================================================
    | PAYOUT SUMMARY
    ===================================================== --}}
    <div class="card mb-4">

        <div class="card-header">
            <h3 class="card-title">
                Detail Pencairan Komisi #{{ $payout->id }}
            </h3>
        </div>

        <div class="card-body">

            {{-- AGENT --}}
            <div class="mb-4">
                <div class="text-muted small">Agent</div>
                <div class="fw-semibold">
                    {{ $payout->agent->user->nama ?? '-' }}
                    ({{ $payout->agent->kode_agent }})
                    · {{ $payout->agent->phone ?? '-' }}
                </div>
            </div>

            {{-- META --}}
            <div class="row g-3">

                <div class="col-md-3 col-6">
                    <div class="text-muted small">Total Komisi</div>
                    <div class="fw-bold fs-5">
                        Rp {{ number_format($payout->total_komisi,0,',','.') }}
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="text-muted small">Jumlah Item</div>
                    <div class="fw-semibold">
                        {{ $payout->total_item }} komisi
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="text-muted small">Status</div>
                    @switch($payout->status)
                        @case('requested')
                            <span class="badge badge-info">Requested</span>
                            @break
                        @case('approved')
                            <span class="badge badge-warning">Approved</span>
                            @break
                        @case('paid')
                            <span class="badge badge-success">PAID</span>
                            @break
                        @case('rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @break
                    @endswitch
                </div>

                <div class="col-md-3 col-6">
                    <div class="text-muted small">Requested At</div>
                    <div class="fw-semibold">
                        {{ optional($payout->requested_at)->format('d M Y H:i') }}
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="text-muted small">Branch</div>
                    <div class="fw-semibold">
                        {{ $payout->branch->nama_cabang ?? '-' }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- =====================================================
    | TRANSFER SNAPSHOT (AUDIT)
    ===================================================== --}}
    @if($payout->transfer)
    <div class="card card-soft mb-4">

        <div class="card-header">
            <h3 class="card-title">Informasi Transfer (Snapshot)</h3>
        </div>

        <div class="card-body">

            <p class="text-muted small mb-3">
                Data rekening saat pembayaran dilakukan
                <strong>(audit-proof & tidak terpengaruh perubahan agent)</strong>
            </p>

            <div class="row g-3">

                <div class="col-md-3 col-6">
                    <div class="text-muted small">Bank</div>
                    <div class="fw-semibold">
                        {{ $payout->transfer->bank_name }}
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="text-muted small">No Rekening</div>
                    <div class="fw-semibold">
                        {{ $payout->transfer->bank_account_number }}
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="text-muted small">Atas Nama</div>
                    <div class="fw-semibold">
                        {{ $payout->transfer->bank_account_name }}
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="text-muted small">Dibayar Pada</div>
                    <div class="fw-semibold">
                        {{ optional($payout->transfer->paid_at)->format('d M Y H:i') }}
                    </div>
                </div>

            </div>

        </div>
    </div>
    @endif


    {{-- =====================================================
    | KOMISI DETAIL
    ===================================================== --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Detail Komisi dalam Payout</h3>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jamaah</th>
                            <th>Invoice / Payment</th>
                            <th class="text-end">%</th>
                            <th class="text-end">Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($komisi as $k)
                        <tr>
                            <td data-label="#">
                                {{ $k->id }}
                            </td>

                            <td data-label="Jamaah">
                                <strong>{{ $k->jamaah->nama_lengkap ?? '-' }}</strong><br>
                                <small class="text-muted">
                                    {{ $k->jamaah->no_id ?? '-' }}
                                </small>
                            </td>

                            <td data-label="Invoice / Payment">
                                @if($k->payment && $k->payment->invoice)
                                    <a href="{{ route('keuangan.invoice-jamaah.show', $k->payment->invoice->id) }}"
                                       class="btn btn-outline btn-xs">
                                        📄 {{ $k->payment->invoice->nomor_invoice }}
                                    </a>
                                @elseif($k->payment)
                                    <span class="small">
                                        💳 Payment #{{ $k->payment->id }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td data-label="%" class="text-end">
                                {{ number_format($k->komisi_persen,2) }}%
                            </td>

                            <td data-label="Nominal" class="text-end fw-semibold">
                                Rp {{ number_format($k->komisi_nominal,0,',','.') }}
                            </td>

                            <td data-label="Status">
                                @if($k->status === 'paid')
                                    <span class="badge badge-success">PAID</span>
                                @else
                                    <span class="badge badge-muted">
                                        {{ ucfirst($k->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Tidak ada data komisi.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">
                                Total Komisi
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($komisi->sum('komisi_nominal'),0,',','.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
