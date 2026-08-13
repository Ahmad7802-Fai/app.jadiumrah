@extends('layouts.admin')

@section('title','Edit Invoice')

@section('content')

{{-- =========================
| HEADER
========================= --}}
<div class="mb-5">
    <a href="{{ route('ticketing.invoice.show', $invoice) }}"
       class="text-sm text-gray-500 hover:underline">
        ← Back to Invoice Detail
    </a>

    <h1 class="text-2xl font-semibold mt-2">
        Edit Invoice
        <span class="font-mono">
            {{ $invoice->invoice_number }}
        </span>
    </h1>

    <p class="text-sm text-gray-500 mt-1">
        Hanya catatan invoice yang dapat diubah
    </p>
</div>

{{-- =========================
| FORM
========================= --}}
<form method="POST"
      action="{{ route('ticketing.invoice.update', $invoice) }}"
      class="card-premium p-4 md:p-6 space-y-6">
    @csrf
    @method('PUT')

    {{-- =========================
    | NOTES
    ========================= --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Catatan Invoice
        </label>
        <textarea name="notes"
                  rows="4"
                  class="form-input w-full"
                  placeholder="Catatan tambahan (opsional)">{{ old('notes', $invoice->notes) }}</textarea>

        <p class="text-xs text-gray-500 mt-1">
            Catatan ini akan tampil di PDF invoice
        </p>
    </div>

    {{-- =========================
    | FOOTER
    ========================= --}}
    <div class="form-footer">
        <a href="{{ route('ticketing.invoice.show', $invoice) }}"
           class="btn-ju-secondary btn-sm">
            Cancel
        </a>

        <button type="submit"
                class="btn-ju btn-sm">
            Simpan Perubahan
        </button>
    </div>

</form>

@endsection
