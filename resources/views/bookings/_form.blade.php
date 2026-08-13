@csrf

@php
    $isEdit = isset($booking);
@endphp

<div class="bg-white rounded-2xl shadow-sm p-5 md:p-6 space-y-6">

    {{-- ================= BASIC INFO ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- PAKET --}}
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Paket
            </label>
            <select name="paket_id"
                    id="paketSelect"
                    class="w-full h-10 px-3 text-sm border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    required>
                <option value="">-- Pilih Paket --</option>
                @foreach($pakets as $paket)
                    <option value="{{ $paket->id }}"
                        {{ old('paket_id', $booking->paket_id ?? '') == $paket->id ? 'selected' : '' }}>
                        {{ $paket->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- DEPARTURE --}}
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Departure
            </label>
            <select name="paket_departure_id"
                    id="departureSelect"
                    class="w-full h-10 px-3 text-sm border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    required>
                <option value="">-- Pilih Departure --</option>
            </select>
        </div>

        {{-- ROOM TYPE --}}
        <div class="space-y-1 md:col-span-2">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Room Type
            </label>
            <select name="room_type"
                    id="roomTypeSelect"
                    class="w-full h-10 px-3 text-sm border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    required>
                <option value="double">Double</option>
                <option value="triple">Triple</option>
                <option value="quad">Quad</option>
            </select>
        </div>

    </div>

    {{-- ================= PRICE BOX ================= --}}
    <div id="priceBox" class="hidden bg-gray-50 border rounded-xl p-4 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500">Harga per seat</span>
            <span id="pricePerSeat" class="font-semibold text-primary-600">
                -
            </span>
        </div>

        <div class="flex justify-between mt-2 text-base">
            <span class="text-gray-600 font-medium">Total</span>
            <span id="totalPrice" class="font-bold text-green-600">
                -
            </span>
        </div>
    </div>

    {{-- ================= JAMAAH ================= --}}
    <div>
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">
            Pilih Jamaah
        </label>

        @php
            $selectedJamaahs = old(
                'jamaah_ids',
                isset($booking)
                    ? $booking->jamaahs->pluck('id')->toArray()
                    : []
            );
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-52 overflow-y-auto pr-2">

            @foreach($jamaahs as $jamaah)
                <label class="flex items-center gap-3 p-2 border rounded-lg hover:bg-gray-50 transition cursor-pointer text-sm">
                    <input type="checkbox"
                           name="jamaah_ids[]"
                           value="{{ $jamaah->id }}"
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500 jamaahCheckbox"
                           {{ in_array($jamaah->id, $selectedJamaahs) ? 'checked' : '' }}>
                    <span class="truncate">
                        {{ $jamaah->nama_lengkap }}
                    </span>
                </label>
            @endforeach

        </div>
    </div>

    {{-- ================= SUBMIT ================= --}}
    <div class="flex justify-end">
        <button type="submit"
                class="px-6 h-10 text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition shadow-sm">
            {{ $isEdit ? 'Update Booking' : 'Simpan Booking' }}
        </button>
    </div>

</div> 

<script>
document.addEventListener('DOMContentLoaded', function() {

    const paketSelect = document.getElementById('paketSelect');
    const departureSelect = document.getElementById('departureSelect');
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    const jamaahCheckboxes = document.querySelectorAll('.jamaahCheckbox');

    const priceBox = document.getElementById('priceBox');
    const pricePerSeatEl = document.getElementById('pricePerSeat');
    const totalPriceEl = document.getElementById('totalPrice');

    let departureData = [];

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function updatePrice() {

        const departureId = departureSelect.value;
        const roomType = roomTypeSelect.value;

        if (!departureId || !roomType) return;

        const departure = departureData.find(d => d.id == departureId);
        if (!departure || !departure.prices) return;

        const priceObj = departure.prices.find(p => p.room_type === roomType);
        if (!priceObj) return;

        const price = parseFloat(priceObj.price);

        const selectedCount = document.querySelectorAll('.jamaahCheckbox:checked').length;
        const total = price * selectedCount;

        pricePerSeatEl.textContent = formatRupiah(price);
        totalPriceEl.textContent = selectedCount ? formatRupiah(total) : '-';

        priceBox.classList.remove('hidden');
    }

    function loadDepartures(paketId) {

        if (!paketId) {
            departureSelect.innerHTML = '<option value="">-- Pilih Departure --</option>';
            return;
        }

        departureSelect.innerHTML = '<option>Loading...</option>';

        fetch(`/pakets/${paketId}/departures`)
            .then(res => res.json())
            .then(data => {

                departureData = data;

                departureSelect.innerHTML = '<option value="">-- Pilih Departure --</option>';

                if (!data.length) {
                    departureSelect.innerHTML = '<option value="">Tidak ada departure</option>';
                    return;
                }

                data.forEach(dep => {

                    const option = document.createElement('option');
                    option.value = dep.id;

                    const sisa = dep.quota - dep.booked;

                    option.textContent =
                        new Date(dep.departure_date).toLocaleDateString('id-ID') +
                        ' | Sisa: ' + sisa;

                    departureSelect.appendChild(option);
                });

            })
            .catch(() => {
                departureSelect.innerHTML = '<option value="">Gagal load departure</option>';
            });
    }

    paketSelect.addEventListener('change', function() {
        loadDepartures(this.value);
    });

    departureSelect.addEventListener('change', updatePrice);
    roomTypeSelect.addEventListener('change', updatePrice);

    jamaahCheckboxes.forEach(cb => {
        cb.addEventListener('change', updatePrice);
    });

});
</script>