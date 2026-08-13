@extends('layouts.app')

@section('title','Voucher')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Voucher</h1>

        <a href="{{ route('marketing.vouchers.create') }}"
           class="btn btn-primary">
            + Buat Voucher
        </a>
    </div>

    <div class="card">

        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Tipe</th>
                    <th>Value</th>
                    <th>Quota</th>
                    <th>Used</th>
                    <th>Expired</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td class="font-semibold">{{ $voucher->code }}</td>
                    <td>{{ strtoupper($voucher->type) }}</td>
                    <td>{{ $voucher->value }}</td>
                    <td>{{ $voucher->quota ?? '-' }}</td>
                    <td>{{ $voucher->used }}</td>
                    <td>{{ optional($voucher->expired_at)->format('d M Y') ?? '-' }}</td>
                    <td>
                        @if($voucher->is_active)
                            <span class="badge-success">Active</span>
                        @else
                            <span class="badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('marketing.vouchers.edit',$voucher) }}"
                           class="btn btn-secondary btn-xs">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('marketing.vouchers.destroy',$voucher) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-xs">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-gray-400">
                        Tidak ada voucher.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>

    </div>

</div>

@endsection