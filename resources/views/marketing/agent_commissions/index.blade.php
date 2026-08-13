@extends('layouts.app')

@section('title','Komisi Agent (Marketing View)')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Performa Agent</h1>

        <form method="GET" class="flex gap-2">
            <input type="date" name="from" value="{{ $from }}" class="form-input">
            <input type="date" name="to" value="{{ $to }}" class="form-input">
            <button class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Total Booking</th>
                    <th>Total Seat</th>
                    <th>Total Revenue</th>
                    <th>Estimasi Komisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>{{ $row->agent->name ?? '-' }}</td>
                        <td>{{ $row->total_booking }}</td>
                        <td>{{ $row->total_seat ?? 0 }}</td>
                        <td>
                            Rp {{ number_format($row->total_revenue,0,',','.') }}
                        </td>
                        <td class="font-semibold text-green-600">
                            Rp {{ number_format($row->total_revenue * 0.05,0,',','.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection