@extends('layouts.admin')

@section('title', 'Edit Marketing Expense')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Edit Marketing Expense</h4>
        <small class="text-muted">
            Perbarui data biaya campaign
        </small>
    </div>

    <a href="{{ route('marketing.expenses.index') }}"
       class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<form method="POST"
      action="{{ route('marketing.expenses.update', $expense) }}"
      class="card">

    @csrf
    @method('PUT')

    <div class="card-body row g-3">

        {{-- SUMBER LEAD --}}
        <div class="col-md-6">
            <label class="form-label">Sumber Lead <span class="text-danger">*</span></label>
            <select name="sumber_id"
                    class="form-select @error('sumber_id') is-invalid @enderror"
                    required>
                @foreach(\App\Models\LeadSource::orderBy('nama_sumber')->get() as $sumber)
                    <option value="{{ $sumber->id }}"
                        @selected(old('sumber_id', $expense->sumber_id) == $sumber->id)>
                        {{ $sumber->nama_sumber }}
                    </option>
                @endforeach
            </select>
            @error('sumber_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- PLATFORM --}}
{{-- PLATFORM --}}
<div class="col-md-6">
    <label class="form-label">Platform</label>
    <select name="platform"
            class="form-select @error('platform') is-invalid @enderror">

        <option value="">-- Pilih Platform --</option>

        <option value="meta_ads"
            @selected(old('platform') === 'meta_ads')>
            Meta Ads (Facebook & Instagram)
        </option>

        <option value="tiktok_ads"
            @selected(old('platform') === 'tiktok_ads')>
            TikTok Ads
        </option>

        <option value="google_ads"
            @selected(old('platform') === 'google_ads')>
            Google Ads
        </option>

        <option value="offline"
            @selected(old('platform') === 'offline')>
            Offline
        </option>

    </select>

    @error('platform')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


        {{-- NAMA CAMPAIGN --}}
        <div class="col-md-6">
            <label class="form-label">Nama Campaign</label>
            <input type="text"
                   name="nama_campaign"
                   class="form-control"
                   value="{{ old('nama_campaign', $expense->nama_campaign) }}">
        </div>

        {{-- TANGGAL --}}
        <div class="col-md-3">
            <label class="form-label">Tanggal</label>
            <input type="date"
                   name="tanggal"
                   class="form-control"
                   value="{{ old('tanggal', $expense->tanggal->toDateString()) }}">
        </div>

        {{-- BIAYA --}}
        <div class="col-md-3">
            <label class="form-label">Biaya (Rp)</label>
            <input type="number"
                   name="biaya"
                   class="form-control"
                   value="{{ old('biaya', $expense->biaya) }}">
        </div>

        {{-- CATATAN --}}
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea name="catatan"
                      rows="3"
                      class="form-control">{{ old('catatan', $expense->catatan) }}</textarea>
        </div>

    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('marketing.expenses.index') }}"
           class="btn btn-outline-secondary">
            Batal
        </a>
        <button class="btn btn-warning">
            <i class="fa fa-save me-1"></i> Update
        </button>
    </div>

</form>

@endsection
