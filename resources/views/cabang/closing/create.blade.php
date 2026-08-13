@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">
    Ajukan Closing Lead (Cabang)
</h1>

<div class="mb-4 text-sm text-gray-600">
    <b>Lead:</b> {{ $lead->nama }} <br>
    <b>No HP:</b> {{ $lead->no_hp }}
</div>

<form method="POST"
    action="{{ route('cabang.leads.closing.store', $lead) }}">

    <x-lead_closings.form />

</form>
@endsection
