@extends('layouts.admin')

@section('title','Edit Transaksi Layanan')

@section('content')
<div class="page-container page-container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Transaksi Layanan</h1>
            <p class="text-muted text-sm">
                Update client & item layanan
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
    <form action="{{ route('keuangan.transaksi-layanan.update', $trx->id) }}"
          method="POST">
        @csrf
        @method('PUT')

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
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}"
                                    {{ $trx->id_client == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Catatan</label>
                        <input type="text"
                               name="notes"
                               class="form-control"
                               value="{{ $trx->notes }}"
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
                        @foreach($trx->items as $i => $row)
                            <tr>
                                <td>
                                    <select name="items[{{ $i }}][id_layanan_item]"
                                            class="form-control item-select">
                                        @foreach($items as $itm)
                                            <option value="{{ $itm->id }}"
                                                data-harga="{{ $itm->harga }}"
                                                data-tipe="{{ $itm->tipe }}"
                                                data-days="{{ $itm->durasi_hari_default ?? 1 }}"
                                                {{ $row->id_layanan_item == $itm->id ? 'selected' : '' }}>
                                                {{ $itm->nama_item }}
                                                (Rp {{ number_format($itm->harga) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[{{ $i }}][qty]"
                                           class="form-control qty-input"
                                           value="{{ $row->qty }}"
                                           min="1">
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[{{ $i }}][days]"
                                           class="form-control days-input"
                                           value="{{ $row->days }}"
                                           min="1"
                                           style="{{ $row->item->tipe === 'hotel' ? '' : 'display:none;' }}">
                                </td>

                                <td>
                                    <input type="text"
                                           class="form-control harga-input"
                                           value="Rp {{ number_format($row->harga) }}"
                                           readonly>
                                </td>

                                <td>
                                    <input type="text"
                                           class="form-control subtotal-input"
                                           value="Rp {{ number_format($row->subtotal) }}"
                                           readonly>
                                </td>

                                <td class="col-actions">
                                    <button type="button"
                                            class="btn btn-danger btn-sm removeRow">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        @endforeach
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
                        <span id="totalDisplay">
                            Rp {{ number_format($trx->subtotal) }}
                        </span>
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
                <i class="fas fa-save me-2"></i> Update Transaksi
            </button>
        </div>

    </form>
</div>

{{-- =====================================================
SCRIPT (LOGIC TETAP)
===================================================== --}}
<script>
let rowIndex = {{ count($trx->items) }};

document.getElementById('addRow').onclick = () => {
    const row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][id_layanan_item]"
                    class="form-control item-select">
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
        <td><input type="number" name="items[${rowIndex}][qty]" class="form-control qty-input" value="1" min="1"></td>
        <td><input type="number" name="items[${rowIndex}][days]" class="form-control days-input" value="1" min="1" style="display:none;"></td>
        <td><input type="text" class="form-control harga-input" readonly></td>
        <td><input type="text" class="form-control subtotal-input" readonly></td>
        <td class="col-actions">
            <button type="button" class="btn btn-danger btn-sm removeRow">✕</button>
        </td>
    </tr>`;
    document.querySelector("#itemsTable tbody")
        .insertAdjacentHTML('beforeend', row);
    rowIndex++; attachEvents();
};

function attachEvents(){
    document.querySelectorAll(".item-select").forEach(el => el.onchange = updateRow);
    document.querySelectorAll(".qty-input,.days-input").forEach(el => el.oninput = updateRow);
    document.querySelectorAll(".removeRow").forEach(btn =>
        btn.onclick = () => { btn.closest("tr").remove(); updateTotal(); }
    );
}
attachEvents();

function updateRow(){
    let tr = this.closest("tr");
    let s = tr.querySelector(".item-select");
    let harga = s.selectedOptions[0]?.dataset.harga ?? 0;
    let tipe  = s.selectedOptions[0]?.dataset.tipe ?? 'default';
    let days  = tr.querySelector(".days-input");
    let qty   = tr.querySelector(".qty-input");

    if(tipe === "hotel"){
        days.style.display = "block";
    } else {
        days.style.display = "none";
        days.value = 1;
    }

    tr.querySelector(".harga-input").value = format(harga);
    tr.querySelector(".subtotal-input").value =
        format(harga * qty.value * days.value);

    updateTotal();
}

function updateTotal(){
    let total = 0;
    document.querySelectorAll(".subtotal-input").forEach(i => {
        total += Number(i.value.replace(/\D/g,''));
    });
    document.getElementById("totalDisplay").innerText = format(total);
}

function format(num){
    return "Rp " + Number(num).toLocaleString("id-ID");
}
</script>
@endsection
