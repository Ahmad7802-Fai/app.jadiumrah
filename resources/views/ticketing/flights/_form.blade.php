@php
    $flight ??= null;
@endphp

<div class="space-y-10">

    {{-- ================= BASIC INFO ================= --}}
    <div class="grid md:grid-cols-2 gap-6">

        <x-input
            label="Airline"
            name="airline"
            :value="$flight->airline ?? ''"
            required
        />

        <x-input
            label="Flight Number"
            name="flight_number"
            :value="$flight->flight_number ?? ''"
            required
        />

        <x-input
            label="Aircraft Type"
            name="aircraft_type"
            :value="$flight->aircraft_type ?? ''"
        />

        <x-input
            label="Aircraft Capacity"
            name="aircraft_capacity"
            type="number"
            :value="$flight->aircraft_capacity ?? ''"
            min="1"
        />

        <div class="md:col-span-2">
            <x-checkbox
                label="Charter Flight"
                name="is_charter"
                :checked="$flight->is_charter ?? false"
            />
        </div>

        <x-select label="Status" name="is_active">
            <option value="1" {{ old('is_active', $flight->is_active ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ old('is_active', $flight->is_active ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </x-select>

        <div class="md:col-span-2">
            <x-textarea
                label="Notes"
                name="notes"
                :value="$flight->notes ?? ''"
            />
        </div>

    </div>

    {{-- ================= SEGMENTS ================= --}}
    <div>

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
                Flight Segments
            </h3>

            <button type="button"
                    onclick="addSegment()"
                    class="px-4 py-2 text-sm bg-primary-600 text-white rounded-xl shadow hover:bg-primary-700">
                + Add Segment
            </button>
        </div>

        <div id="segments-wrapper" class="space-y-6"></div>

    </div>

</div>


{{-- ================= TEMPLATE ================= --}}
<template id="segment-template">
    <div class="segment-item grid md:grid-cols-2 gap-6 p-6 bg-gray-50 border rounded-2xl relative">

        <button type="button"
                onclick="removeSegment(this)"
                class="absolute top-4 right-4 text-red-500 text-xs">
            Remove
        </button>

        <div>
            <label class="block text-sm font-medium mb-1">Origin</label>
            <input type="text"
                   class="segment-origin w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                          text-sm shadow-sm focus:ring-2 focus:ring-primary-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Destination</label>
            <input type="text"
                   class="segment-destination w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                          text-sm shadow-sm focus:ring-2 focus:ring-primary-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Departure Time</label>
            <input type="datetime-local"
                   class="segment-departure w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                          text-sm shadow-sm focus:ring-2 focus:ring-primary-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Arrival Time</label>
            <input type="datetime-local"
                   class="segment-arrival w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                          text-sm shadow-sm focus:ring-2 focus:ring-primary-500">
        </div>

    </div>
</template>


{{-- ================= JS ENGINE ================= --}}
<script>
let segmentIndex = 0;

function addSegment(data = null) {
    const template = document.getElementById('segment-template');
    const clone = template.content.cloneNode(true);
    const wrapper = document.getElementById('segments-wrapper');

    const segment = clone.querySelector('.segment-item');

    segment.querySelector('.segment-origin')
        .setAttribute('name', `segments[${segmentIndex}][origin]`);

    segment.querySelector('.segment-destination')
        .setAttribute('name', `segments[${segmentIndex}][destination]`);

    segment.querySelector('.segment-departure')
        .setAttribute('name', `segments[${segmentIndex}][departure_time]`);

    segment.querySelector('.segment-arrival')
        .setAttribute('name', `segments[${segmentIndex}][arrival_time]`);

    if (data) {
        segment.querySelector('.segment-origin').value = data.origin ?? '';
        segment.querySelector('.segment-destination').value = data.destination ?? '';
        segment.querySelector('.segment-departure').value = data.departure_time ?? '';
        segment.querySelector('.segment-arrival').value = data.arrival_time ?? '';
    }

    wrapper.appendChild(clone);
    segmentIndex++;
}

function removeSegment(button) {
    button.closest('.segment-item').remove();
    reindexSegments();
}

function reindexSegments() {
    const segments = document.querySelectorAll('.segment-item');
    segmentIndex = 0;

    segments.forEach((segment) => {
        segment.querySelector('.segment-origin')
            .setAttribute('name', `segments[${segmentIndex}][origin]`);

        segment.querySelector('.segment-destination')
            .setAttribute('name', `segments[${segmentIndex}][destination]`);

        segment.querySelector('.segment-departure')
            .setAttribute('name', `segments[${segmentIndex}][departure_time]`);

        segment.querySelector('.segment-arrival')
            .setAttribute('name', `segments[${segmentIndex}][arrival_time]`);

        segmentIndex++;
    });
}

document.addEventListener('DOMContentLoaded', function() {

    @if(old('segments'))
        @foreach(old('segments') as $segment)
            addSegment({
                origin: "{{ $segment['origin'] ?? '' }}",
                destination: "{{ $segment['destination'] ?? '' }}",
                departure_time: "{{ $segment['departure_time'] ?? '' }}",
                arrival_time: "{{ $segment['arrival_time'] ?? '' }}"
            });
        @endforeach
    @elseif(isset($flight) && $flight->segments->count())
        @foreach($flight->segments as $segment)
            addSegment({
                origin: "{{ $segment->origin }}",
                destination: "{{ $segment->destination }}",
                departure_time: "{{ $segment->departure_time->format('Y-m-d\TH:i') }}",
                arrival_time: "{{ $segment->arrival_time->format('Y-m-d\TH:i') }}"
            });
        @endforeach
    @else
        addSegment();
    @endif

});
</script>