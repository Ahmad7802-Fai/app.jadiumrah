@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label class="meta-label">Judul</label>
        <input type="text" name="title"
               value="{{ old('title',$banner->title ?? '') }}"
               class="input">
    </div>

    <div>
        <label class="meta-label">Sub Judul</label>
        <input type="text" name="subtitle"
               value="{{ old('subtitle',$banner->subtitle ?? '') }}"
               class="input">
    </div>

    <div>
        <label class="meta-label">Gambar</label>
        <input type="file" name="image" class="input">
    </div>

    <div>
        <label class="meta-label">Halaman</label>
        <select name="page" class="input">
            <option value="landing">Landing</option>
            <option value="dashboard">Dashboard</option>
            <option value="booking">Booking</option>
        </select>
    </div>

    <div>
        <label class="meta-label">Link</label>
        <input type="text" name="link"
               value="{{ old('link',$banner->link ?? '') }}"
               class="input">
    </div>

    <div>
        <label class="meta-label">Urutan</label>
        <input type="number" name="sort_order"
               value="{{ old('sort_order',$banner->sort_order ?? 0) }}"
               class="input">
    </div>

    <div>
        <label class="meta-label">Mulai</label>
        <input type="date" name="start_date"
               value="{{ old('start_date',$banner->start_date ?? '') }}"
               class="input">
    </div>

    <div>
        <label class="meta-label">Berakhir</label>
        <input type="date" name="end_date"
               value="{{ old('end_date',$banner->end_date ?? '') }}"
               class="input">
    </div>

</div>

<div class="mt-6 flex justify-end">
    <button class="btn btn-primary">
        Simpan
    </button>
</div>