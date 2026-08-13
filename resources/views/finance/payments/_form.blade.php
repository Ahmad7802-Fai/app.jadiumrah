@csrf

@php
    $bookingId = $booking->id ?? ($payment->booking_id ?? null);
@endphp

@if($bookingId)
    <input type="hidden" name="booking_id" value="{{ $bookingId }}">
@endif


<div class="space-y-6">

    {{-- ================= TANGGAL BAYAR ================= --}}
    <div>
        <label class="label">Tanggal Bayar</label>

        <input type="date"
               name="paid_at"
               value="{{ old('paid_at', isset($payment) ? optional($payment->paid_at)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="input @error('paid_at') border-red-500 @enderror">

        @error('paid_at')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>


    {{-- ================= NOMINAL ================= --}}
    <div>
        <label class="label">Jumlah Bayar</label>

        <input type="number"
               name="amount"
               value="{{ old('amount', $payment->amount ?? '') }}"
               class="input @error('amount') border-red-500 @enderror"
               placeholder="Masukkan nominal pembayaran">

        @error('amount')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>


    {{-- ================= TIPE PEMBAYARAN ================= --}}
    <div>
        <label class="label">Tipe Pembayaran</label>

        <select name="type"
                class="input @error('type') border-red-500 @enderror">

            <option value="">-- Pilih Tipe --</option>

            @foreach(\App\Models\Payment::TYPES as $type)
                <option value="{{ $type }}"
                    {{ old('type', $payment->type ?? '') === $type ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_',' ', $type)) }}
                </option>
            @endforeach

        </select>

        @error('type')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>


    {{-- ================= METODE ================= --}}
    <div>
        <label class="label">Metode Pembayaran</label>

        <select name="method"
                class="input @error('method') border-red-500 @enderror">

            <option value="">-- Pilih Metode --</option>

            <option value="cash"
                {{ old('method', $payment->method ?? '') === 'cash' ? 'selected' : '' }}>
                Cash
            </option>

            <option value="transfer"
                {{ old('method', $payment->method ?? '') === 'transfer' ? 'selected' : '' }}>
                Transfer
            </option>

            <option value="gateway"
                {{ old('method', $payment->method ?? '') === 'gateway' ? 'selected' : '' }}>
                Payment Gateway
            </option>

            <option value="edc"
                {{ old('method', $payment->method ?? '') === 'edc' ? 'selected' : '' }}>
                EDC
            </option>

        </select>

        @error('method')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>


    {{-- ================= UPLOAD BUKTI ================= --}}
    <div>
        <label class="label">Upload Bukti Pembayaran</label>

        <input type="file"
               name="proof_file"
               accept="image/*,.pdf"
               class="input @error('proof_file') border-red-500 @enderror">

        <p class="text-xs text-gray-400 mt-1">
            Format: JPG, PNG, PDF (max 2MB)
        </p>

        @error('proof_file')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror


        {{-- Preview Bukti Lama --}}
        @if(isset($payment) && $payment->proof_file)
            <div class="mt-3">
                <a href="{{ asset('storage/'.$payment->proof_file) }}"
                   target="_blank"
                   class="text-primary-600 underline text-sm">
                    Lihat Bukti Lama
                </a>
            </div>
        @endif
    </div>


    {{-- ================= KETERANGAN ================= --}}
    <div>
        <label class="label">Keterangan</label>

        <textarea name="note"
                  rows="3"
                  class="input @error('note') border-red-500 @enderror"
                  placeholder="Tambahkan catatan jika perlu">{{ old('note', $payment->note ?? '') }}</textarea>

        @error('note')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>

</div>