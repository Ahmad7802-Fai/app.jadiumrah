@extends('layouts.agent')

@section('page-title','Kelola Lead')
@section('page-subtitle','Follow up, pipeline, dan closing')

@section('content')

{{-- =====================
FLASH MESSAGE
===================== --}}
@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4">
        {{ $errors->first() }}
    </div>
@endif

{{-- =====================
HEADER LEAD
===================== --}}
<div class="mb-4">
    <h2 class="text-base font-semibold">
        {{ $lead->nama }}
    </h2>
    <p class="text-sm text-gray-500">
        {{ $lead->no_hp }}
    </p>

    {{-- STATUS PIPELINE --}}
    <div class="mt-2 flex items-center gap-2 text-sm">
        <span class="text-gray-500">Tahap saat ini:</span>

        <span class="px-3 py-1 rounded-full text-xs font-semibold
            {{ $lead->pipeline?->tahap === 'komit' ? 'bg-green-100 text-green-700' :
               ($lead->pipeline?->tahap === 'closing' ? 'bg-blue-100 text-blue-700' :
               'bg-gray-100 text-gray-700') }}">
            {{ strtoupper($lead->pipeline?->tahap ?? 'NEW') }}
        </span>
    </div>
</div>

{{-- =====================
1️⃣ EDIT CATATAN LEAD
===================== --}}
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title">Catatan Lead</h3>
    </div>

    <div class="card-body">
        <form method="POST"
              action="{{ route('agent.leads.update', $lead) }}">
            @csrf
            @method('PUT')

            <x-leads.form
                :lead="$lead"
                :sources="$sources"
            />

            <div class="mt-4 flex gap-2">
                <button class="btn-primary btn-sm">
                    Simpan Perubahan
                </button>

                <a href="{{ route('agent.leads.index') }}"
                   class="btn-secondary btn-sm">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

{{-- =====================
2️⃣ FOLLOW UP
===================== --}}
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title">Follow Up</h3>
    </div>

    <div class="card-body">
        @include('agent.leads.partials.followup')

        <div class="mt-2 text-xs text-gray-400">
            💡 Pipeline akan otomatis naik berdasarkan hasil follow up.
        </div>
    </div>
</div>

{{-- =====================
3️⃣ PIPELINE (MANUAL MOVE)
===================== --}}
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title">Pipeline</h3>
    </div>

    <div class="card-body">

        @php
            $locked = in_array($lead->status, ['CLOSING','CLOSED']);
        @endphp

        <div class="flex flex-wrap gap-2">
        @foreach($pipelines as $pipeline)
            @php
                $isCurrent  = $lead->pipeline_id === $pipeline->id;
                $isBackward = $lead->pipeline && $pipeline->urutan <= $lead->pipeline->urutan;
            @endphp

            <form method="POST"
                  action="{{ route('agent.leads.pipeline.move', [$lead, $pipeline]) }}">
                @csrf

                <button
                    class="btn-xs
                        {{ $isCurrent ? 'btn-primary' : 'btn-secondary' }}"
                    {{ ($locked || $isBackward) ? 'disabled' : '' }}
                    title="
                        {{ $locked ? 'Lead sedang atau sudah closing' :
                           ($isBackward ? 'Tidak bisa mundur pipeline' : '') }}
                    "
                >
                    {{ ucfirst($pipeline->tahap) }}
                </button>
            </form>
        @endforeach
        </div>

        @if($locked)
            <div class="mt-3 text-xs text-gray-500">
                Pipeline terkunci karena lead sudah masuk tahap closing.
            </div>
        @endif
    </div>
</div>

{{-- =====================
4️⃣ AJUKAN CLOSING
===================== --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Closing</h3>
    </div>

    <div class="card-body">

        @if(
            $lead->pipeline?->tahap === 'komit'
            && !in_array($lead->status,['CLOSING','CLOSED'])
        )
            <form method="POST"
                  action="{{ route('agent.leads.closing.submit', $lead) }}">
                @csrf

                <button class="btn-warning btn-sm">
                    Ajukan Closing
                </button>
            </form>

            <div class="mt-2 text-xs text-gray-500">
                Lead siap diajukan ke pusat untuk approval.
            </div>

        @elseif(in_array($lead->status,['CLOSING','CLOSED']))
            <div class="text-sm text-green-600 font-medium">
                Closing sudah diajukan / selesai.
            </div>

        @else
            <div class="text-sm text-gray-500">
                Closing hanya bisa diajukan setelah lead berada di tahap
                <strong>KOMIT</strong>.
            </div>
        @endif

    </div>
</div>

@endsection
