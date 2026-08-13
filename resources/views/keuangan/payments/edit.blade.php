@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- =====================================================
         HEADER — MOBILE FIRST + DESKTOP COMPACT
    ====================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="d-flex align-items-center">
            {{-- Mobile Back --}}
            <a href="{{ route('keuangan.payments.show', $payment->id) }}"
               class="btn btn-light shadow-sm rounded-circle d-md-none me-3"
               style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h4 class="fw-bold mb-1">Edit Pembayaran</h4>
                <p class="text-muted small mb-0">Perbarui detail transaksi pembayaran.</p>
            </div>
        </div>

        {{-- Desktop Back --}}
        <a href="{{ route('keuangan.payments.show', $payment->id) }}" 
           class="btn btn-light rounded-pill px-4 shadow-sm d-none d-md-inline-flex">
            ← Kembali
        </a>
    </div>


    {{-- =====================================================
         ALERT COMPONENT
    ====================================================== --}}
    @include('components.alert')


    {{-- =====================================================
         FORM WRAPPER — PREMIUM CARD
    ====================================================== --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <form action="{{ route('keuangan.payments.update', $payment->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')


                {{-- ================================
                    JAMAAH (DISPLAY ONLY)
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Jamaah</label>
                    <input type="text"
                           class="form-control bg-light rounded-pill"
                           value="{{ $payment->jamaah->nama_lengkap }} ({{ $payment->jamaah->no_id }})"
                           disabled>
                </div>

                {{-- ================================
                    INVOICE NUMBER
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Nomor Invoice</label>
                    <input type="text"
                           class="form-control bg-light rounded-pill"
                           value="{{ $payment->invoice->nomor_invoice }}"
                           disabled>
                </div>


                {{-- ================================
                    METODE PEMBAYARAN
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Metode Pembayaran *</label>
                    <select name="metode"
                            class="form-control rounded-pill"
                            required>
                        <option value="transfer" {{ $payment->metode=='transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="cash"     {{ $payment->metode=='cash'     ? 'selected' : '' }}>Cash</option>
                        <option value="kantor"   {{ $payment->metode=='kantor'   ? 'selected' : '' }}>Kantor</option>
                        <option value="gateway"  {{ $payment->metode=='gateway'  ? 'selected' : '' }}>Gateway</option>
                    </select>
                </div>


                {{-- ================================
                    JUMLAH PEMBAYARAN
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Jumlah Pembayaran *</label>
                    <input type="number"
                           name="jumlah"
                           class="form-control rounded-pill"
                           value="{{ $payment->jumlah }}"
                           required>
                </div>


                {{-- ================================
                    TANGGAL BAYAR
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Tanggal Bayar *</label>
                    <input type="date"
                           name="tanggal_bayar"
                           class="form-control rounded-pill"
                           value="{{ $payment->tanggal_bayar }}"
                           required>
                </div>


                {{-- ================================
                    BUKTI TRANSFER
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Bukti Transfer (Opsional)</label>
                    <input type="file"
                           name="bukti_transfer"
                           class="form-control rounded-pill"
                           accept=".jpg,.jpeg,.png,.pdf">

                    <small class="text-muted">
                        Biarkan kosong jika tidak ingin mengubah bukti.
                    </small>

                    @if($payment->bukti_transfer)
                        <div class="mt-2">
                            <a href="{{ asset($payment->bukti_transfer) }}"
                               target="_blank"
                               class="btn btn-outline-dark btn-sm px-3 rounded-pill">
                                <i class="fas fa-file-image me-1"></i>
                                Lihat Bukti
                            </a>
                        </div>
                    @endif
                </div>


                {{-- ================================
                    KETERANGAN
                ================================= --}}
                <div class="mb-3">
                    <label class="fw-semibold">Keterangan</label>
                    <textarea name="keterangan"
                              rows="2"
                              class="form-control rounded-4">{{ $payment->keterangan }}</textarea>
                </div>


                {{-- ================================
                    SUBMIT BUTTON — PREMIUM
                ================================= --}}
                <div class="mt-4">
                    <button class="btn-ju w-100 py-2 rounded-pill">
                        <i class="fas fa-save me-2"></i>
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
