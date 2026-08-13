<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" class="form-control" name="nama"
           value="{{ old('nama', $item->nama ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Pesan</label>
    <textarea class="form-control" name="pesan" rows="4" required>{{ old('pesan', $item->pesan ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Rating (1–5)</label>
    <select name="rating" class="form-control" required>
        @for($i=1;$i<=5;$i++)
            <option value="{{ $i }}" {{ (old('rating',$item->rating ?? 5) == $i) ? 'selected' : '' }}>
                {{ $i }}
            </option>
        @endfor
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Foto</label>

    <input type="file" class="form-control" name="photo" accept="image/*"
           onchange="previewImage(event)">

    <div class="mt-3">
        <img id="preview"
             src="{{ $item->photo ? asset('storage/' . $item->photo) : 'https://via.placeholder.com/200' }}"
             class="rounded"
             style="width:150px;height:150px;object-fit:cover;">
    </div>
</div>

<script>
function previewImage(event){
    let reader = new FileReader();
    reader.onload = function(){
        document.getElementById('preview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
