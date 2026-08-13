@extends('layouts.admin')

@section('title','Buat Transaksi Layanan')

@section('content')
<div class="page-container page-container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Buat Transaksi Layanan</h1>
            <p class="text-muted text-sm">
                Input client & layanan yang digunakan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.transaksi-layanan.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- =====================================================
    FORM
    ====================================================== --}}
    <form action="{{ route('keuangan.transaksi-layanan.store') }}" method="POST">
        @csrf

        {{-- =====================================================
        CLIENT CARD
        ====================================================== --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Client</h3>
            </div>

            <div class="card-body card-body-lg">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Client *</label>
                        <select name="id_client" class="form-control" required>
                            <option value="">Pilih client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Catatan</label>
                        <input type="text"
                               name="notes"
                               class="form-control"
                               placeholder="Catatan opsional">
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================
        ITEM LAYANAN
        ====================================================== --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Item Layanan</h3>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="itemsTable" class="table table-compact">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th width="90">Qty</th>
                                <th width="90">Hari</th>
                                <th width="160">Harga</th>
                                <th width="160">Subtotal</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][id_layanan_item]"
                                            class="form-control item-select"
                                            required>
                                        <option value="">Pilih item...</option>
                                        @foreach($items as $itm)
                                            <option value="{{ $itm->id }}"
                                                data-harga="{{ $itm->harga }}"
                                                data-tipe="{{ $itm->tipe }}"
                                                data-days="{{ $itm->durasi_hari_default ?? 1 }}">
                                                {{ $itm->nama_item }}
                                                (Rp {{ number_format($itm->harga) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[0][qty]"
                                           class="form-control qty-input"
                                           min="1" value="1">
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[0][days]"
                                           class="form-control days-input"
                                           min="1" value="1"
                                           style="display:none;">
                                </td>

                                <td>
                                    <input type="text"
                                           name="items[0][harga]"
                                           class="form-control harga-input"
                                           readonly>
                                </td>

                                <td>
                                    <input type="text"
                                           name="items[0][subtotal]"
                                           class="form-control subtotal-input"
                                           readonly>
                                </td>

                                <td class="col-actions">
                                    <button type="button"
                                            class="btn btn-danger btn-sm removeRow">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm"
                            id="addRow">
                        + Tambah Item
                    </button>

                    <div class="fw-bold">
                        Total:
                        <span id="totalDisplay">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        {{-- =====================================================
        SUBMIT
        ====================================================== --}}
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-lg px-3">
                <i class="fas fa-save me-2"></i> Simpan Transaksi
            </button>
        </div>

    </form>
</div>

{{-- =====================================================
SCRIPT (LOGIC TETAP)
===================================================== --}}
<script>
let rowIndex = 1;

document.getElementById('addRow').onclick = () => {
    const row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][id_layanan_item]"
                    class="form-control item-select" required>
                <option value="">Pilih item...</option>
                @foreach($items as $itm)
                    <option value="{{ $itm->id }}"
                        data-harga="{{ $itm->harga }}"
                        data-tipe="{{ $itm->tipe }}"
                        data-days="{{ $itm->durasi_hari_default ?? 1 }}">
                        {{ $itm->nama_item }}
                        (Rp {{ number_format($itm->harga) }})
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][qty]" class="form-control qty-input" min="1" value="1"></td>
        <td><input type="number" name="items[${rowIndex}][days]" class="form-control days-input" min="1" value="1" style="display:none;"></td>
        <td><input type="text" name="items[${rowIndex}][harga]" class="form-control harga-input" readonly></td>
        <td><input type="text" name="items[${rowIndex}][subtotal]" class="form-control subtotal-input" readonly></td>
        <td class="col-actions">
            <button type="button" class="btn btn-danger btn-sm removeRow">✕</button>
        </td>
    </tr>`;
    document.querySelector("#itemsTable tbody").insertAdjacentHTML('beforeend', row);
    rowIndex++; attachEvents();
};

function attachEvents(){
    document.querySelectorAll(".item-select").forEach(e => e.onchange = updateRow);
    document.querySelectorAll(".qty-input,.days-input").forEach(e => e.oninput = updateRow);
    document.querySelectorAll(".removeRow").forEach(btn =>
        btn.onclick = () => { btn.closest("tr").remove(); updateTotal(); }
    );
}
attachEvents();

function updateRow(){
    let tr = this.closest("tr"),
        select = tr.querySelector(".item-select"),
        qty = tr.querySelector(".qty-input"),
        days = tr.querySelector(".days-input"),
        harga = select.selectedOptions[0]?.dataset.harga || 0,
        tipe = select.selectedOptions[0]?.dataset.tipe,
        defaultDays = select.selectedOptions[0]?.dataset.days || 1;

    if(tipe === "hotel"){
        days.style.display = "block";
        if(!days.value) days.value = defaultDays;
    } else {
        days.style.display = "none";
        days.value = 1;
    }

    tr.querySelector(".harga-input").value =
        "Rp " + Number(harga).toLocaleString("id-ID");

    tr.querySelector(".subtotal-input").value =
        "Rp " + (Number(harga) * qty.value * days.value).toLocaleString("id-ID");

    updateTotal();
}

function updateTotal(){
    let total = 0;
    document.querySelectorAll(".subtotal-input").forEach(e => {
        total += Number(e.value.replace(/\D/g,''));
    });
    document.getElementById("totalDisplay").innerText =
        "Rp " + total.toLocaleString("id-ID");
}
</script>
@endsection
