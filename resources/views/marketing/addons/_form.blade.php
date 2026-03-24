@csrf

<div class="grid md:grid-cols-2 gap-6">

    {{-- Nama --}}
    <div>
        <label class="meta-label">Nama Add-On</label>
        <input type="text"
               name="name"
               value="{{ old('name',$addon->name ?? '') }}"
               class="input"
               required>
    </div>

    {{-- Kode --}}
    @if(!isset($addon))
    <div>
        <label class="meta-label">Kode</label>
        <input type="text"
               name="code"
               value="{{ old('code') }}"
               class="input"
               required>
    </div>
    @endif

    {{-- Harga Jual --}}
    <div>
        <label class="meta-label">Harga Jual</label>
        <input type="number"
               name="selling_price"
               value="{{ old('selling_price',$addon->selling_price ?? 0) }}"
               class="input"
               min="0"
               required>
    </div>

    {{-- Cost --}}
    <div>
        <label class="meta-label">Cost Price</label>
        <input type="number"
               name="cost_price"
               value="{{ old('cost_price',$addon->cost_price ?? 0) }}"
               class="input"
               min="0">
    </div>

    {{-- Status --}}
    <div>
        <label class="meta-label">Status</label>
        <select name="is_active" class="input">
            <option value="1"
                {{ old('is_active',$addon->is_active ?? 1) ? 'selected':'' }}>
                Active
            </option>
            <option value="0"
                {{ !old('is_active',$addon->is_active ?? 1) ? 'selected':'' }}>
                Inactive
            </option>
        </select>
    </div>

    {{-- Description --}}
    <div class="md:col-span-2">
        <label class="meta-label">Deskripsi</label>
        <textarea name="description"
                  class="input"
                  rows="3">{{ old('description',$addon->description ?? '') }}</textarea>
    </div>

</div>