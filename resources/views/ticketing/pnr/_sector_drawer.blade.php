<div id="sectorDrawer"
     class="fixed inset-y-0 right-0 w-full md:w-[420px]
            bg-white z-[9999] transform translate-x-full transition">

    <div class="p-5 border-b flex justify-between items-center">
        <h3 class="text-sm font-semibold">Flight Sector</h3>
        <button onclick="closeSectorDrawer()">✕</button>
    </div>

    <div class="p-5 space-y-3 overflow-y-auto" id="sectorList"></div>

    <div class="p-5 border-t">
        <button class="btn-ju w-full" onclick="saveSector()">Save Sector</button>
    </div>
</div>
