@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-semibold mb-4">
    Approval Closing (Pusat)
</h1>

<div class="overflow-x-auto bg-white rounded-lg border">
<table class="table-auto w-full">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-3 py-2">Lead</th>
            <th class="px-3 py-2">Agent</th>
            <th class="px-3 py-2">Cabang</th>
            <th class="px-3 py-2">Tanggal</th>
            <th class="px-3 py-2">Aksi</th>
        </tr>
    </thead>

    <tbody>
    @forelse($closings as $c)
        <tr class="border-t">

            <td class="px-3 py-2 font-medium">
                {{ $c->lead->nama }}
                <div class="text-xs text-gray-500">
                    {{ $c->lead->no_hp }}
                </div>
            </td>

            <td class="px-3 py-2">
                {{ optional($c->agent)->nama ?? '-' }}
            </td>

            <td class="px-3 py-2">
                {{ optional($c->branch)->nama_cabang ?? '-' }}
            </td>

            <td class="px-3 py-2 text-sm">
                {{ $c->created_at->format('d M Y H:i') }}
            </td>

            <td class="px-3 py-2">
                <div class="flex gap-2">

                    {{-- APPROVE --}}
                    <form
                        action="{{ route('crm.lead-closings.approve', $c) }}"
                        method="POST"
                        onsubmit="return confirm('Setujui closing ini?')"
                    >
                        @csrf
                        <button class="btn-sm btn-success">
                            Approve
                        </button>
                    </form>

                    {{-- REJECT --}}
                    <form
                        action="{{ route('crm.lead-closings.reject', $c) }}"
                        method="POST"
                        onsubmit="return confirm('Tolak closing ini?')"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="reason"
                            value="Ditolak oleh pusat"
                        >
                        <button class="btn-sm btn-danger">
                            Reject
                        </button>
                    </form>

                </div>
            </td>

        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center py-6 text-gray-500">
                Tidak ada closing menunggu approval.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>

<div class="mt-4">
    {{ $closings->links() }}
</div>

@endsection
