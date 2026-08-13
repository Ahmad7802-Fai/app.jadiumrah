@extends('layouts.admin')

@section('title', 'Approve Closing')

@section('content')
@php
    $isApproved = $closing->status === 'APPROVED';
@endphp

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Approve Closing</h1>
        <div class="page-subtitle">
            Review dan persetujuan closing lead
        </div>
    </div>

    <div class="page-header__actions">
        <a href="{{ url()->previous() }}"
           class="btn btn-light btn-sm">
            ← Kembali
        </a>
    </div>
</div>

{{-- ======================================================
| STATUS ALERT
====================================================== --}}
@if($isApproved)
    <div class="alert alert-success mb-4">
        <strong>Approved.</strong>
        Closing ini sudah disetujui dan tidak dapat diubah kembali.
    </div>
@endif

{{-- ======================================================
| FORM CARD
====================================================== --}}
<div class="card card-hover">
    <div class="card-body">

        <form method="POST"
              action="{{ route('crm.closing.approve', $closing) }}"
              class="form">
            @csrf

            {{-- ================= NOMINAL DP ================= --}}
            <div class="form-group">
                <label>Nominal DP</label>

                @if($isApproved)
                    <input
                        type="text"
                        class="form-control"
                        value="Rp {{ number_format($closing->nominal_dp,0,',','.') }}"
                        readonly
                    >
                @else
                    <input
                        type="number"
                        name="nominal_dp"
                        class="form-control"
                        value="{{ old('nominal_dp', $closing->nominal_dp) }}"
                        required
                    >
                @endif
            </div>

            {{-- ================= TOTAL PAKET ================= --}}
            <div class="form-group">
                <label>Total Paket</label>

                @if($isApproved)
                    <input
                        type="text"
                        class="form-control"
                        value="Rp {{ number_format($closing->total_paket,0,',','.') }}"
                        readonly
                    >
                @else
                    <input
                        type="number"
                        name="total_paket"
                        class="form-control"
                        value="{{ old('total_paket', $closing->total_paket) }}"
                        required
                    >
                @endif
            </div>

            {{-- ================= AGENT ================= --}}
            <div class="form-group">
                <label>Agent</label>

                @if($isApproved)
                    <input
                        type="text"
                        class="form-control"
                        value="{{ optional($closing->agent)->nama ?? '—' }}"
                        readonly
                    >
                    <input type="hidden" name="agent_id" value="{{ $closing->agent_id }}">
                @else
                    <select name="agent_id" class="form-select">
                        <option value="">—</option>
                        @foreach($agents ?? [] as $agent)
                            <option
                                value="{{ $agent->id }}"
                                @selected(old('agent_id', $closing->agent_id) == $agent->id)>
                                {{ $agent->nama }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- ================= CATATAN ================= --}}
            <div class="form-group">
                <label>Catatan</label>

                @if($isApproved)
                    <div class="form-static">
                        {{ $closing->catatan ?: '— Tidak ada catatan —' }}
                    </div>
                @else
                    <textarea
                        name="catatan"
                        class="form-textarea"
                        rows="3">{{ old('catatan', $closing->catatan) }}</textarea>
                @endif
            </div>

            {{-- ================= CABANG ================= --}}
            @if(auth()->user()->role === 'ADMIN')
                <div class="form-group">
                    <label>Cabang</label>

                    @if($isApproved)
                        <input
                            type="text"
                            class="form-control"
                            value="{{ optional($closing->branch)->nama_cabang ?? '-' }}"
                            readonly
                        >
                        <input type="hidden" name="branch_id" value="{{ $closing->branch_id }}">
                    @else
                        <select name="branch_id" class="form-select" required>
                            @foreach($branches as $branch)
                                <option
                                    value="{{ $branch->id }}"
                                    @selected(old('branch_id', $closing->branch_id) == $branch->id)>
                                    {{ $branch->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @else
                <input
                    type="hidden"
                    name="branch_id"
                    value="{{ $closing->branch_id
                        ?? auth()->user()->branch_id
                        ?? config('crm.default_branch_id') }}"
                >
            @endif

            {{-- ================= ACTION ================= --}}
            <div class="form-actions">
                @if(!$isApproved)
                    <button type="submit" class="btn btn-primary">
                        Approve Closing
                    </button>
                @else
                    <span class="badge badge-soft-success">
                        ✔ Closing Approved
                    </span>
                @endif

                <a href="{{ url()->previous() }}"
                   class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
