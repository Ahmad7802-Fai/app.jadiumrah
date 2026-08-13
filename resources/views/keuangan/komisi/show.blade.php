@extends('layouts.admin')

@section('title','Detail Komisi')
@section('subtitle','Audit komisi agent')

@section('content')
<div class="page-container container-narrow">

    <div class="card">

        {{-- ===============================
            CARD HEADER
        ================================ --}}
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    Komisi #{{ $komisi->id }}
                </h3>
                <div class="card-subtitle">
                    Detail dan audit komisi agent
                </div>
            </div>
        </div>

        {{-- ===============================
            CARD BODY
        ================================ --}}
        <div class="card-body">

            <div class="card-row">

                {{-- AGENT --}}
                <div class="row-item">
                    <div class="label">Agent</div>
                    <div class="value">
                        <strong>{{ $komisi->agent->kode_agent }}</strong><br>
                        <span class="text-muted small">
                            {{ $komisi->agent->user->nama ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- JAMAAH --}}
                <div class="row-item">
                    <div class="label">Jamaah</div>
                    <div class="value">
                        <strong>{{ $komisi->jamaah->nama_lengkap }}</strong><br>
                        <span class="text-muted small">
                            {{ $komisi->jamaah->no_id }}
                        </span>
                    </div>
                </div>

                {{-- MODE --}}
                <div class="row-item">
                    <div class="label">Mode</div>
                    <div class="value">
                        <span class="badge badge-outline">
                            {{ ucfirst($komisi->mode) }}
                        </span>
                    </div>
                </div>

                {{-- PERSEN --}}
                <div class="row-item">
                    <div class="label">Persentase</div>
                    <div class="value">
                        {{ number_format($komisi->komisi_persen,2) }}%
                    </div>
                </div>

                {{-- NOMINAL --}}
                <div class="row-item">
                    <div class="label">Nominal Komisi</div>
                    <div class="value fw-semibold">
                        Rp {{ number_format($komisi->komisi_nominal,0,',','.') }}
                    </div>
                </div>

                {{-- INVOICE --}}
                <div class="row-item">
                    <div class="label">Invoice</div>
                    <div class="value">
                        {{ $komisi->payment?->invoice?->nomor_invoice ?? '-' }}
                    </div>
                </div>

            </div>

        </div>

        {{-- ===============================
            CARD FOOTER (OPTIONAL)
        ================================ --}}
        <div class="card-footer text-end">
            <a href="{{ route('keuangan.komisi.index') }}"
               class="btn btn-secondary">
                ← Kembali
            </a>
        </div>

    </div>

</div>
@endsection
