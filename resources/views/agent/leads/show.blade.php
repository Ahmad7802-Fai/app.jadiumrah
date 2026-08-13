@extends('layouts.agent')

@section('page-title','Detail Lead')
@section('page-subtitle','Riwayat & follow up calon jamaah')

@section('content')

{{-- ===============================
   PAGE HEADER
=============================== --}}
<div class="mb-4">
    <a href="{{ route('agent.leads.index') }}"
       class="btn btn-ghost btn-xs mb-2">
        ← Kembali
    </a>

    <h1 class="text-lg font-semibold leading-tight">
        {{ $lead->nama }}
    </h1>

    <p class="text-sm text-muted">
        Detail lead & histori follow up
    </p>
</div>

{{-- ===============================
   FLASH MESSAGE
=============================== --}}
@if(session('success'))
<div class="card mb-3">
    <div class="text-sm text-success font-semibold">
        ✔ {{ session('success') }}
    </div>
</div>
@endif

<div class="card-stack">

{{-- =====================================================
   INFORMASI LEAD
===================================================== --}}
<div class="card">
    <div class="fw-semibold mb-3">
        Informasi Lead
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">

        <div>
            <div class="text-muted text-xs">Nama</div>
            <div class="fw-semibold">{{ $lead->nama }}</div>
        </div>

        <div>
            <div class="text-muted text-xs">No HP</div>
            <div>{{ $lead->no_hp }}</div>
        </div>

        <div>
            <div class="text-muted text-xs">Email</div>
            <div>{{ $lead->email ?? '-' }}</div>
        </div>

        <div>
            <div class="text-muted text-xs">Sumber</div>
            <div>{{ optional($lead->sumber)->nama_sumber ?? '-' }}</div>
        </div>

        <div>
            <div class="text-muted text-xs">Channel</div>
            <span class="badge badge--neutral">
                {{ strtoupper($lead->channel) }}
            </span>
        </div>

        <div>
            <div class="text-muted text-xs">Status</div>

            @php
                $statusClass = match(strtoupper($lead->status)) {
                    'NEW'       => 'badge-new',
                    'PROSPECT'  => 'badge-prospect',
                    'FOLLOWUP'  => 'badge-followup',
                    'MEETING'   => 'badge-meeting',
                    'KOMIT'     => 'badge-komit',
                    'CLOSING'   => 'badge-closing',
                    'CLOSED'    => 'badge-closed',
                    'LOST'      => 'badge-lost',
                    default     => 'badge-gray',
                };
            @endphp

            <span class="badge {{ $statusClass }}">
                {{ strtoupper($lead->status) }}
            </span>

            @if($lead->isOverdue())
                <span class="text-xs text-danger fw-semibold ml-1">
                    ⚠ Overdue
                </span>
            @endif
        </div>

    </div>
</div>

{{-- =====================================================
   RIWAYAT FOLLOW UP
===================================================== --}}
<div class="card">
    <div class="fw-semibold mb-3">
        Riwayat Follow Up
    </div>

    @if($lead->activities->isEmpty())
        <div class="text-sm text-muted text-center py-3">
            Belum ada aktivitas follow up
        </div>
    @else
        <div class="card-stack">
            @foreach($lead->activities as $act)
                <div class="card">
                    <div class="flex justify-between items-center mb-1">
                        <strong class="text-xs uppercase">
                            {{ $act->aktivitas }}
                        </strong>
                        <span class="text-xs text-muted">
                            {{ $act->created_at->format('d M Y H:i') }}
                        </span>
                    </div>

                    <div class="text-sm">
                        {{ $act->hasil }}
                    </div>

                    @if($act->next_action)
                        <div class="text-xs text-muted mt-1">
                            Next: {{ $act->next_action }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- =====================================================
   TAMBAH FOLLOW UP
===================================================== --}}
@if(!in_array($lead->status,['CLOSED','LOST']))
<div class="card">
    <div class="fw-semibold mb-3">
        Tambah Follow Up
    </div>

    <form method="POST"
          action="{{ route('agent.leads.followup.store', $lead) }}"
          class="form">
        @csrf

        <div class="form-group">
            <label>Aktivitas</label>
            <select name="aktivitas" class="form-select" required>
                <option value="">— Pilih —</option>
                <option value="wa">WhatsApp</option>
                <option value="telpon">Telepon</option>
                <option value="dm">DM</option>
                <option value="meeting">Meeting</option>
                <option value="kunjungan">Kunjungan</option>
                <option value="lost">❌ Lead Gagal</option>
            </select>
        </div>

        <div class="form-group">
            <label>Hasil</label>
            <textarea name="hasil"
                      class="form-textarea"
                      required></textarea>
        </div>

        <div class="form-group">
            <label>Next Action</label>
            <input type="text"
                   name="next_action"
                   class="form-input">
        </div>

        <div class="form-group">
            <label>Tanggal Follow Up</label>
            <input type="datetime-local"
                   name="followup_date"
                   class="form-input">
        </div>

        <div class="form-actions">
            <button class="btn btn-primary w-full">
                Simpan Follow Up
            </button>
        </div>
    </form>
</div>
@endif

{{-- =====================================================
   AJUKAN CLOSING
===================================================== --}}
@if($lead->canSubmitClosing())
<div class="card">
    <div class="fw-semibold mb-3">
        🚀 Ajukan Closing
    </div>

    <form method="POST"
          action="{{ route('agent.leads.closing.submit', $lead) }}"
          class="form">
        @csrf

        <div class="form-group">
            <label>Total Harga Paket</label>
            <input type="number"
                   name="total_paket"
                   class="form-input"
                   required>
        </div>

        <div class="form-group">
            <label>DP (Opsional)</label>
            <input type="number"
                   name="nominal_dp"
                   class="form-input">
        </div>

        <div class="form-group">
            <label>Catatan Closing</label>
            <textarea name="catatan"
                      class="form-textarea"></textarea>
        </div>

        <div class="form-actions">
            <button class="btn btn-warning w-full">
                🚀 Ajukan Closing
            </button>
        </div>
    </form>
</div>
@endif

</div>
@endsection
