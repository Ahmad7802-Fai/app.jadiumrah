<div id="clientModal"
     class="fixed inset-0 z-[9999] hidden">

    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black/60"
         onclick="closeClientModal()"></div>

    <!-- MODAL BOX -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl">

            <!-- HEADER -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-sm font-semibold tracking-wide">
                    Select Client
                </h3>

                <button type="button"
                        onclick="closeClientModal()"
                        class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                    ✕
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 space-y-4">

                <!-- SEARCH + ADD -->
                <div class="flex gap-3">
                    <input type="text"
                           id="clientSearch"
                           placeholder="Search client..."
                           class="form-input flex-1"
                           autofocus
                           onkeyup="filterClientTable()">

                    <a href="{{ route('keuangan.clients.create') }}"
                       target="_blank"
                       class="btn-ju-outline btn-xs whitespace-nowrap">
                        + Add Client
                    </a>
                </div>

                <!-- TABLE -->
                <div class="border rounded-xl overflow-hidden max-h-[420px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr class="text-xs text-gray-500">
                                <th class="text-left px-4 py-3">Client</th>
                                <th class="text-center px-3 py-3">Type</th>
                                <th class="text-center px-3 py-3">Contact</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="clientTable">
                            @foreach($clients as $c)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">
                                    {{ $c->nama }}
                                </td>
                                <td class="text-center text-xs uppercase">
                                    {{ $c->tipe }}
                                </td>
                                <td class="text-center text-xs">
                                    {{ $c->telepon }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                            class="btn-ju btn-xs"
                                            onclick="selectClient('{{ $c->id }}','{{ $c->nama }}')">
                                        Select
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
