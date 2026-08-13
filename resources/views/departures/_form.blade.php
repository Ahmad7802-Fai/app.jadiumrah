@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Paket --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Paket
        </label>
        <select name="paket_id" class="input w-full" required>
            <option value="">Pilih Paket</option>
            @foreach($pakets as $paket)
                <option value="{{ $paket->id }}"
                    {{ old('paket_id', $departure->paket_id ?? '') == $paket->id ? 'selected' : '' }}>
                    {{ $paket->name }} ({{ $paket->code }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Departure Code --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Departure Code
        </label>
        <input type="text"
               name="departure_code"
               class="input w-full"
               value="{{ old('departure_code', $departure->departure_code ?? '') }}">
    </div>

    {{-- Flight Number --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Flight Number
        </label>
        <input type="text"
               name="flight_number"
               class="input w-full"
               value="{{ old('flight_number', $departure->flight_number ?? '') }}">
    </div>

    {{-- Meeting Point --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Meeting Point
        </label>
        <input type="text"
               name="meeting_point"
               class="input w-full"
               value="{{ old('meeting_point', $departure->meeting_point ?? '') }}">
    </div>

    {{-- Departure Date --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Tanggal Berangkat
        </label>
        <input type="date"
               name="departure_date"
               class="input w-full"
               required
               value="{{ old('departure_date', isset($departure) ? $departure->departure_date?->format('Y-m-d') : '') }}">
    </div>

    {{-- Return Date --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Tanggal Pulang
        </label>
        <input type="date"
               name="return_date"
               class="input w-full"
               value="{{ old('return_date', isset($departure) ? optional($departure->return_date)->format('Y-m-d') : '') }}">
    </div>

    {{-- Quota --}}
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700">
            Quota
        </label>
        <input type="number"
               name="quota"
               min="1"
               class="input w-full"
               required
               value="{{ old('quota', $departure->quota ?? '') }}">
    </div>

    {{-- Active --}}
    <div class="flex items-center gap-2 mt-7">
        <input type="checkbox"
               name="is_active"
               value="1"
               {{ old('is_active', $departure->is_active ?? true) ? 'checked' : '' }}>
        <label class="text-sm text-gray-700">Aktif</label>
    </div>

</div>

{{-- BUTTON --}}
{{-- ACTION BUTTON --}}
<div class="mt-8 flex justify-end gap-3">

    <a href="{{ route('departures.index') }}"
       class="btn btn-secondary">
        Batal
    </a>

    <button type="submit"
            class="btn btn-primary">
        Simpan
    </button>

</div>