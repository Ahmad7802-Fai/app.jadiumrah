@extends('layouts.admin')

@section('title','Ticketing Report')

@section('content')

<div class="page page--narrow">

    {{-- ======================================================
    | PAGE HEADER
    ====================================================== --}}
    <div class="page-header mb-md">
        <div>
            <div class="page-title">Ticketing Report</div>
            <div class="text-sm text-muted">
                Generate laporan payment & refund dalam format PDF
            </div>
        </div>
    </div>

    {{-- ======================================================
    | REPORT CARDS
    ====================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">

        {{-- ======================================================
        | PAYMENT REPORT
        ====================================================== --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Payment Report</div>
            </div>

            <div class="card-body">
                <form method="GET"
                      action="{{ route('ticketing.report.payment.pdf') }}"
                      class="form-grid">

                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date"
                               name="from"
                               class="form-input"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date"
                               name="to"
                               class="form-input"
                               required>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary btn-sm">
                            Generate PDF
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- ======================================================
        | REFUND REPORT
        ====================================================== --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Refund Report</div>
            </div>

            <div class="card-body">
                <form method="GET"
                      action="{{ route('ticketing.report.refund.pdf') }}"
                      class="form-grid">

                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date"
                               name="from"
                               class="form-input"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date"
                               name="to"
                               class="form-input"
                               required>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-danger btn-sm">
                            Generate PDF
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>

@endsection
