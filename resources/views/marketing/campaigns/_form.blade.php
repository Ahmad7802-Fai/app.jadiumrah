@csrf

<div class="grid md:grid-cols-2 gap-6">

    {{-- NAME --}}
    <div>
        <label class="meta-label">Nama Campaign</label>
        <input type="text"
               name="name"
               value="{{ old('name', $campaign->name ?? '') }}"
               class="input w-full"
               required>
    </div>

    {{-- TARGET --}}
    <div>
        <label class="meta-label">Target Revenue</label>
        <input type="number"
               name="target_revenue"
               value="{{ old('target_revenue', $campaign->target_revenue ?? 0) }}"
               class="input w-full">
    </div>

    {{-- START DATE --}}
    <div>
        <label class="meta-label">Start Date</label>
        <input type="date"
               name="start_date"
               value="{{ old('start_date', optional($campaign->start_date ?? null)->format('Y-m-d')) }}"
               class="input w-full"
               required>
    </div>

    {{-- END DATE --}}
    <div>
        <label class="meta-label">End Date</label>
        <input type="date"
               name="end_date"
               value="{{ old('end_date', optional($campaign->end_date ?? null)->format('Y-m-d')) }}"
               class="input w-full"
               required>
    </div>

    {{-- BUDGET --}}
    <div class="md:col-span-2">
        <label class="meta-label">Budget Marketing</label>
        <input type="number"
               name="budget_marketing"
               value="{{ old('budget_marketing', $campaign->budget_marketing ?? 0) }}"
               class="input w-full">
    </div>

</div>

{{-- PAKET --}}
<div class="mt-6">
    <label class="meta-label">Paket yang termasuk campaign</label>

    <div class="grid md:grid-cols-3 gap-3 mt-2">

        @foreach($pakets as $paket)
            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="paket_ids[]"
                       value="{{ $paket->id }}"
                       {{ isset($campaign) && $campaign->pakets->contains($paket->id) ? 'checked' : '' }}>
                {{ $paket->name }}
            </label>
        @endforeach

    </div>
</div>