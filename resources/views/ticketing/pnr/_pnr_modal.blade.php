<div id="pnrModal" class="fixed inset-0 z-[9999] hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 bg-black/50"
         onclick="closePnrModal()"></div>

    {{-- MODAL --}}
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-xl">

            {{-- HEADER --}}
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h3 class="text-sm font-semibold">
                    PNR Information
                </h3>
                <button onclick="closePnrModal()" class="text-xl text-gray-400">
                    ×
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-5 space-y-4 text-sm">

                <div>
                    <label class="text-xs text-gray-500">PNR Code</label>
                    <input type="text"
                           id="pnr_code_input"
                           class="form-input w-full"
                           placeholder="JKC8MC">
                </div>

                <div>
                    <label class="text-xs text-gray-500">Airline Class</label>
                    <input type="text"
                           id="airline_class_input"
                           class="form-input w-full"
                           placeholder="Y / C / G">
                </div>

                <div>
                    <label class="text-xs text-gray-500">Agent ID (optional)</label>
                    <input type="number"
                           id="agent_id_input"
                           class="form-input w-full"
                           placeholder="—">
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="px-5 py-4 border-t flex justify-end gap-2">
                <button onclick="closePnrModal()"
                        class="btn-ju-outline btn-sm">
                    Cancel
                </button>

                <button onclick="savePnrInfo()"
                        class="btn-ju btn-sm">
                    Save
                </button>
            </div>

        </div>
    </div>
</div>
