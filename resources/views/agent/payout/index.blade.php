@extends('layouts.admin')

@section('title','Payout Agent')
@section('subtitle','Persetujuan & Pembayaran Komisi Agent')

@section('content')

<div class="card-premium">

    {{-- ===============================
    | HEADER
    =============================== --}}
    <div class="card-header-premium">
        <h3 class="card-title-premium">Daftar Pencairan Komisi Agent</h3>
        <p class="card-subtitle-premium">
            Kelola approval, pembayaran, dan penolakan payout agent
        </p>
    </div>

    {{-- ===============================
    | TABLE
    =============================== --}}
    <div class="table-responsive">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Agent</th>
                    <th>Total Komisi</th>
                    <th>Total Item</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($payouts as $payout)
                <tr>
                    <td>{{ $payout->id }}</td>

                    <td>
                        <div class="font-semibold">
                            {{ $payout->agent->user->nama ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $payout->agent->kode_agent }}
                        </div>
                    </td>

                    <td class="font-semibold">
                        Rp {{ number_format($payout->total_komisi,0,',','.') }}
                    </td>

                    <td>{{ $payout->total_item }} item</td>

                    <td>
                        @switch($payout->status)
                            @case('requested')
                                <span class="badge badge-followup">Requested</span>
                                @break
                            @case('approved')
                                <span class="badge badge-new">Approved</span>
                                @break
                            @case('paid')
                                <span class="badge badge-closed">Paid</span>
                                @break
                            @case('rejected')
                                <span class="badge badge-danger">Rejected</span>
                                @break
                        @endswitch
                    </td>

                    <td class="text-sm text-gray-500">
                        {{ optional($payout->requested_at)->format('d M Y H:i') }}
                    </td>

                    {{-- ===============================
                    | ACTIONS
                    =============================== --}}
                    <td class="text-right space-x-2">

                        <a href="{{ route('keuangan.payout.show',$payout->id) }}"
                           class="btn-secondary btn-sm">
                            👁 Detail
                        </a>

                        {{-- ===== STATUS: REQUESTED ===== --}}
                        @if($payout->status === 'requested')

                            {{-- APPROVE --}}
                            <form method="POST"
                                  action="{{ route('keuangan.payout.approve',$payout->id) }}"
                                  class="inline"
                                  onsubmit="return confirm('Approve payout ini?')">
                                @csrf
                                <button class="btn-primary btn-sm">
                                    ✔ Approve
                                </button>
                            </form>

                            {{-- REJECT (MODAL) --}}
                            <button
                                type="button"
                                class="btn-danger btn-sm"
                                onclick="openRejectModal({{ $payout->id }})">
                                ✖ Reject
                            </button>

                        @endif

                        {{-- ===== STATUS: APPROVED ===== --}}
                        @if($payout->status === 'approved')
                            <form method="POST"
                                  action="{{ route('keuangan.payout.pay',$payout->id) }}"
                                  class="inline"
                                  onsubmit="return confirm('Tandai payout ini sebagai PAID?')">
                                @csrf
                                <button class="btn-success btn-sm">
                                    💸 Pay
                                </button>
                            </form>
                        @endif

                        {{-- ===== STATUS: PAID ===== --}}
                        @if($payout->status === 'paid')
                            <span class="text-green-600 font-semibold text-sm">
                                ✔ Paid
                            </span>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400">
                        Belum ada data payout.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payouts->links() }}
    </div>
</div>

{{-- =====================================================
| MODAL REJECT PAYOUT
===================================================== --}}
<div id="rejectModal" class="modal-overlay hidden">
    <div class="modal-card">
        <h3 class="modal-title">Tolak Payout</h3>
        <p class="modal-subtitle">
            Masukkan alasan penolakan. Agent akan dapat mengajukan ulang.
        </p>

        <form method="POST" id="rejectForm">
            @csrf

            <textarea
                name="reason"
                class="form-control mt-3"
                rows="4"
                required
                placeholder="Contoh: Data rekening belum valid / perlu klarifikasi..."
            ></textarea>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        class="btn-secondary"
                        onclick="closeRejectModal()">
                    Batal
                </button>

                <button type="submit"
                        class="btn-danger">
                    Tolak Payout
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =====================================================
| SCRIPT
===================================================== --}}
<script>
    function openRejectModal(payoutId) {
        const modal = document.getElementById('rejectModal');
        const form  = document.getElementById('rejectForm');

        form.action = `/keuangan/payout/${payoutId}/reject`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal')
            .classList.add('hidden');
    }
</script>

@endsection
