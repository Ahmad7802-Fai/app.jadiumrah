@extends('layouts.app')

@section('title','Flash Sale')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Flash Sale</h1>

        @can('flashsale.create')
        <a href="{{ route('marketing.flash-sales.create') }}"
           class="btn btn-primary">
            + Buat Flash Sale
        </a>
        @endcan
    </div>

    <div class="card">

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Paket</th>
                        <th>Diskon</th>
                        <th>Periode</th>
                        <th>Seat</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                @foreach($flashSales as $fs)
                    <tr>
                        <td>{{ $fs->paket->name }}</td>

                        <td>
                            @if($fs->discount_type === 'fixed')
                                Rp {{ number_format($fs->value,0,',','.') }}
                            @else
                                {{ $fs->value }}%
                            @endif
                        </td>

                        <td>
                            {{ $fs->start_at->format('d M Y H:i') }}
                            <br>
                            {{ $fs->end_at->format('d M Y H:i') }}
                        </td>

                        <td>
                            {{ $fs->used_seat }}
                            /
                            {{ $fs->seat_limit ?? '∞' }}
                        </td>

                        <td>
                            @if($fs->is_active)
                                <span class="badge-success">Active</span>
                            @else
                                <span class="badge-danger">Inactive</span>
                            @endif
                        </td>

                        <td class="flex gap-2">

                            <a href="{{ route('marketing.flash-sales.edit',$fs) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>

                            <form action="{{ route('marketing.flash-sales.destroy',$fs) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>

                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{ $flashSales->links() }}

    </div>

</div>

@endsection