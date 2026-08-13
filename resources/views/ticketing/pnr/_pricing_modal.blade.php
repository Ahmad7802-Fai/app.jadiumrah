<div id="pricingModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/50"
         onclick="closePricingModal()"></div>

    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-xl">

            <div class="px-5 py-4 border-b">
                <h3 class="text-sm font-semibold">Pricing (Per Pax)</h3>
            </div>

            <div class="p-5 space-y-4 text-sm">
                <div class="grid grid-cols-3 gap-3">
                    <input id="pax" type="number" placeholder="Pax" class="form-input">
                    <input id="fare" type="number" placeholder="Fare / Pax" class="form-input">
                    <input id="deposit" type="number" placeholder="Deposit / Pax" class="form-input">
                </div>

                <div class="bg-gray-50 rounded-lg p-3 text-xs">
                    <div>Total Fare: <b id="pf-total">Rp 0</b></div>
                    <div>Balance: <b id="pf-balance">Rp 0</b></div>
                </div>
            </div>

            <div class="px-5 py-4 border-t flex justify-end gap-2">
                <button class="btn-ju-outline btn-xs" onclick="closePricingModal()">Cancel</button>
                <button class="btn-ju btn-xs" onclick="savePricing()">Save</button>
            </div>

        </div>
    </div>
</div>
