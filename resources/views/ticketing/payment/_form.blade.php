<form method="POST"
      action="{{ route('ticketing.payment.store', $invoice) }}"
      class="card-premium p-4 mt-6 space-y-4">
    @csrf

    <h3 class="font-semibold">
        Pembayaran Invoice
    </h3>

    {{-- INFO TAGIHAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

        <div>
            <div class="text-gray-500">Total Invoice</div>
            <div class="font-semibold">
                @money($invoice->total_amount)
            </div>
        </div>

        <div>
            <div class="text-gray-500">Sudah Dibayar</div>
            <div class="font-semibold text-green-600">
                @money($invoice->paid_amount)
            </div>
        </div>

        <div>
            <div class="text-gray-500">Sisa Tagihan</div>
            <div class="font-semibold text-red-600">
                @money($invoice->outstanding_amount)
            </div>
        </div>

    </div>

    {{-- INPUT --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- AMOUNT --}}
        <div>
            <label class="block text-sm mb-1">
                Jumlah Pembayaran
            </label>
            <input type="number"
                   name="amount"
                   min="1000"
                   max="{{ $invoice->outstanding_amount }}"
                   required
                   class="form-input w-full"
                   placeholder="Masukkan nominal">
        </div>

        {{-- TANGGAL --}}
        <div>
            <label class="block text-sm mb-1">
                Tanggal Pembayaran
            </label>
            <input type="date"
                   name="payment_date"
                   value="{{ now()->toDateString() }}"
                   required
                   class="form-input w-full">
        </div>

        {{-- METODE --}}
        <div>
            <label class="block text-sm mb-1">
                Metode Pembayaran
            </label>
            <select name="method"
                    required
                    class="form-input w-full">
                <option value="">Pilih metode</option>
                <option value="TRANSFER">Transfer</option>
                <option value="CASH">Cash</option>
                <option value="VIRTUAL_ACCOUNT">Virtual Account</option>
            </select>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="form-footer">
        <span class="text-xs text-gray-500">
            Pembayaran akan tercatat di audit log
        </span>

        <button type="submit"
                class="btn-ju btn-sm">
            Bayar Invoice
        </button>
    </div>

</form>
