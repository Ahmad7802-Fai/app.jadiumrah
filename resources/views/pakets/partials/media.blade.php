@inject('media', 'App\Services\Media\MediaService')

<div class="card-compact space-y-3">

    <div class="flex justify-between items-center">
        <h3 class="text-sm font-semibold text-gray-700">Media</h3>
    </div>

    {{-- ================= THUMBNAIL ================= --}}
    <div class="space-y-2">

        <label class="text-[11px] text-gray-500">Thumbnail</label>

        <div class="relative">

            <img id="thumbPreview"
                src="{{ isset($paket) && $paket?->thumbnail ? $media->url($paket->thumbnail_small ?? $paket->thumbnail) : '' }}"
                class="w-full h-32 object-cover rounded-lg {{ isset($paket) && $paket?->thumbnail ? '' : 'hidden' }}">

            <div class="border border-dashed rounded-lg p-3 text-center cursor-pointer hover:bg-gray-50"
                 onclick="document.getElementById('thumbInput').click()">
                <p class="text-[11px] text-gray-500">Upload Thumbnail</p>
                <input type="file" id="thumbInput" class="hidden">
            </div>

        </div>

        <input type="hidden" name="thumbnail_base64" id="thumbBase64">

    </div>

    {{-- ================= GALLERY ================= --}}
    <div class="space-y-2">

        <label class="text-[11px] text-gray-500">Gallery</label>

        @if(isset($paket) && $paket->gallery)
        <div class="grid grid-cols-3 gap-1">
            @foreach($paket->gallery as $img)
                <div class="relative group">
                    <img src="{{ $media->url($img) }}"
                         class="h-20 w-full object-cover rounded-md">

                    <label class="absolute top-1 right-1 bg-black/60 text-white text-[10px] px-1 rounded cursor-pointer opacity-0 group-hover:opacity-100 transition">
                        <input type="checkbox" name="remove_gallery[]" value="{{ $img }}">
                        ✕
                    </label>
                </div>
            @endforeach
        </div>
        @endif

        <div id="dropzone"
             class="border border-dashed rounded-lg p-3 text-center cursor-pointer hover:bg-gray-50">
            <p class="text-[11px] text-gray-500">Upload / Drag</p>
            <input type="file" id="galleryInput" multiple class="hidden">
        </div>

        <div id="galleryPreview" class="grid grid-cols-3 gap-1"></div>
        <div id="galleryHidden"></div>

    </div>

</div>

{{-- ================= MODAL ================= --}}
<div id="cropModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[9999]">

    <div class="bg-white rounded-xl w-[92%] max-w-sm p-3 space-y-2">

        <h3 class="text-sm font-semibold text-gray-700">Crop Image</h3>

        <div class="w-full max-h-[300px] overflow-hidden rounded-lg">
            <img id="cropImage" class="w-full object-contain">
        </div>

        <div class="flex justify-end gap-2 pt-1">
            <button type="button" id="cropCancel" class="btn btn-secondary btn-xs">Cancel</button>
            <button type="button" id="cropSave" class="btn btn-primary btn-xs">Crop</button>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    let files = []
    let cropper = null
    let isThumb = false
    let cropperReady = false

    const thumbInput = document.getElementById('thumbInput')
    const thumbPreview = document.getElementById('thumbPreview')
    const thumbBase64 = document.getElementById('thumbBase64')

    const galleryInput = document.getElementById('galleryInput')
    const preview = document.getElementById('galleryPreview')
    const hidden = document.getElementById('galleryHidden')
    const dropzone = document.getElementById('dropzone')

    const modal = document.getElementById('cropModal')
    const cropImg = document.getElementById('cropImage')

    /* ================= OPEN CROP ================= */
    function openCrop(file, thumb = false) {
        isThumb = thumb
        cropperReady = false

        const reader = new FileReader()

        reader.onload = e => {

            cropImg.src = e.target.result

            modal.classList.remove('hidden')
            modal.classList.add('flex')

            cropImg.onload = () => {

                if (cropper) {
                    cropper.destroy()
                }

                cropper = new window.Cropper(cropImg, {
                    aspectRatio: thumb ? 16/9 : 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    ready() {
                        cropperReady = true
                    }
                })
            }
        }

        reader.readAsDataURL(file)
    }

    /* ================= THUMB ================= */
    thumbInput.addEventListener('change', () => {
        if (thumbInput.files[0]) {
            openCrop(thumbInput.files[0], true)
        }
    })

    /* ================= GALLERY ================= */
    dropzone.onclick = () => galleryInput.click()

    galleryInput.addEventListener('change', () => {
        Array.from(galleryInput.files).forEach(file => openCrop(file))
    })

    dropzone.addEventListener('drop', e => {
        e.preventDefault()
        Array.from(e.dataTransfer.files).forEach(file => openCrop(file))
    })

    dropzone.addEventListener('dragover', e => e.preventDefault())

    /* ================= SAVE ================= */
    document.addEventListener('click', async function (e) {

        if (!e.target.closest('#cropSave')) return

        if (!cropper || !cropperReady) return

        const canvas = cropper.getCroppedCanvas({
            width: isThumb ? 800 : 800
        })

        let blob = await new Promise(res => canvas.toBlob(res, 'image/webp', 0.8))

        blob = await imageCompression(blob, {
            maxSizeMB: 0.3,
            maxWidthOrHeight: 1200
        })

        const file = new File([blob], 'img.webp', { type: 'image/webp' })

        if (isThumb) {
            const reader = new FileReader()
            reader.onload = ev => {
                thumbPreview.src = ev.target.result
                thumbPreview.classList.remove('hidden')
                thumbBase64.value = ev.target.result
            }
            reader.readAsDataURL(file)
        } else {
            files.push(file)
            render()
        }

        cropper.destroy()
        cropper = null
        cropperReady = false

        modal.classList.add('hidden')
        modal.classList.remove('flex')
    })

    /* ================= CANCEL ================= */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#cropCancel')) return

        if (cropper) {
            cropper.destroy()
            cropper = null
        }

        modal.classList.add('hidden')
        modal.classList.remove('flex')
    })

    /* ================= RENDER ================= */
    function render() {
        preview.innerHTML = ''
        hidden.innerHTML = ''

        files.forEach((file, i) => {
            const reader = new FileReader()

            reader.onload = e => {
                preview.insertAdjacentHTML('beforeend', `
                    <div class="relative">
                        <img src="${e.target.result}" class="h-20 w-full object-cover rounded">
                        <button type="button" class="remove absolute top-1 right-1 bg-red-500 text-white px-1 text-xs" data-i="${i}">×</button>
                    </div>
                `)

                hidden.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="gallery_base64[]" value="${e.target.result}">
                `)
            }

            reader.readAsDataURL(file)
        })
    }

    /* ================= REMOVE ================= */
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove')) {
            files.splice(e.target.dataset.i, 1)
            render()
        }
    })

})
</script>