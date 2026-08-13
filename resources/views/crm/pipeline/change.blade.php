@extends('layouts.admin')

@section('content')

<div class="mb-4">
    <a href="{{ route('crm.leads.show', $lead) }}" class="text-sm text-blue-600">
        ← Kembali ke Lead
    </a>
    <h1 class="text-xl font-semibold mt-1">Ubah Pipeline Lead</h1>
</div>

<div class="bg-white border rounded-lg p-4 max-w-xl">

    <form
        method="POST"
        action="{{ route('crm.pipeline.update-for-lead', $lead) }}"
    >
        @csrf

        <div class="mb-4">
            <label class="form-label">Tahap Pipeline</label>
            <select name="pipeline_id" class="form-input" required>
                @foreach($pipelines as $p)
                    <option
                        value="{{ $p->id }}"
                        @selected($lead->pipeline_id == $p->id)
                    >
                        {{ ucfirst($p->tahap) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3">
            <button class="btn-primary">
                Simpan
            </button>
            <a href="{{ route('crm.leads.show', $lead) }}" class="btn-secondary">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection
