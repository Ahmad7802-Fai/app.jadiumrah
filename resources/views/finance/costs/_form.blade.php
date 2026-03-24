@csrf

@if(isset($cost))
    @method('PUT')
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ================= CATEGORY ================= --}}
    <div>
        <label class="meta-label">Kategori *</label>
        <select name="cost_category_id" class="input" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('cost_category_id', $cost->cost_category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ================= TANGGAL ================= --}}
    <div>
        <label class="meta-label">Tanggal Cost *</label>
        <input type="date"
               name="cost_date"
               class="input"
               value="{{ old('cost_date', isset($cost) ? $cost->cost_date->format('Y-m-d') : '') }}"
               required>
    </div>

    {{-- ================= BOOKING (OPTIONAL) ================= --}}
    @isset($bookings)
    <div>
        <label class="meta-label">Booking (Optional)</label>
        <select name="booking_id" class="input">
            <option value="">-- Tidak terkait booking --</option>
            @foreach($bookings as $booking)
                <option value="{{ $booking->id }}"
                    {{ old('booking_id', $cost->booking_id ?? '') == $booking->id ? 'selected' : '' }}>
                    {{ $booking->booking_code ?? '#'.$booking->id }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

    {{-- ================= DEPARTURE (OPTIONAL) ================= --}}
    @isset($departures)
    <div>
        <label class="meta-label">Departure (Optional)</label>
        <select name="paket_departure_id" class="input">
            <option value="">-- Tidak terkait departure --</option>
            @foreach($departures as $departure)
                <option value="{{ $departure->id }}"
                    {{ old('paket_departure_id', $cost->paket_departure_id ?? '') == $departure->id ? 'selected' : '' }}>
                    {{ optional($departure->paket)->name }}
                    -
                    {{ optional($departure->departure_date)->format('d M Y') }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

</div>


{{-- ================= AMOUNT ================= --}}
<div class="mt-6">
    <label class="meta-label">Jumlah (Rp) *</label>
    <input type="number"
           name="amount"
           class="input"
           value="{{ old('amount', $cost->amount ?? '') }}"
           required>
</div>


{{-- ================= DESCRIPTION ================= --}}
<div class="mt-6">
    <label class="meta-label">Deskripsi</label>
    <textarea name="description"
              rows="3"
              class="input">{{ old('description', $cost->description ?? '') }}</textarea>
</div>


{{-- ================= PROOF FILE ================= --}}
<div class="mt-6">
    <label class="meta-label">Bukti (jpg, png, pdf)</label>

    <input type="file"
           name="proof_file"
           class="input">

    @if(isset($cost) && $cost->proof_file)
        <div class="mt-2">
            <a href="{{ asset('storage/'.$cost->proof_file) }}"
               target="_blank"
               class="btn btn-outline btn-xs">
                Lihat Bukti Lama
            </a>
        </div>
    @endif
</div>


{{-- ================= STATUS INFO (EDIT ONLY) ================= --}}
@if(isset($cost))
<div class="mt-6">
    <label class="meta-label">Status</label>
    <div>
        @if($cost->status === 'approved')
            <span class="badge-success">Approved</span>
        @elseif($cost->status === 'pending')
            <span class="badge-warning">Pending</span>
        @else
            <span class="badge-danger">Rejected</span>
        @endif
    </div>
</div>
@endif