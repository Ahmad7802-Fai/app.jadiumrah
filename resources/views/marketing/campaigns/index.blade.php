@extends('layouts.app')

@section('title','Marketing • Campaign')

@section('content')

<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Campaign Marketing
            </h1>
            <p class="text-sm text-gray-500">
                Kelola campaign promosi & performanya
            </p>
        </div>

        @can('campaign.create')
        <a href="{{ route('marketing.campaigns.create') }}"
           class="btn btn-primary">
            + Buat Campaign
        </a>
        @endcan
    </div>

    {{-- TABLE --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Periode</th>
                    <th>Target</th>
                    <th>Revenue</th>
                    <th>ROI</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($campaigns as $campaign)

                <tr>
                    <td class="font-medium">
                        {{ $campaign->name }}
                    </td>

                    <td>
                        {{ $campaign->start_date->format('d M Y') }}
                        –
                        {{ $campaign->end_date->format('d M Y') }}
                    </td>

                    <td>
                        Rp {{ number_format($campaign->target_revenue,0,',','.') }}
                    </td>

                    <td>
                        Rp {{ number_format($campaign->revenue,0,',','.') }}
                    </td>

                    <td>
                        {{ number_format($campaign->roi,1) }}%
                    </td>

                    <td>
                        @if($campaign->status === 'active')
                            <span class="badge-success">Active</span>
                        @elseif($campaign->status === 'draft')
                            <span class="badge-warning">Draft</span>
                        @elseif($campaign->status === 'finished')
                            <span class="badge-secondary">Finished</span>
                        @else
                            <span class="badge-danger">Cancelled</span>
                        @endif
                    </td>

                    <td class="text-right">
                        <div class="flex gap-2 justify-end">

                            @if($campaign->status === 'draft')
                                <form method="POST"
                                      action="{{ route('marketing.campaigns.activate',$campaign) }}">
                                    @csrf
                                    <button class="btn btn-success btn-xs">
                                        Activate
                                    </button>
                                </form>
                            @endif

                            @if($campaign->status === 'active')
                                <form method="POST"
                                      action="{{ route('marketing.campaigns.finish',$campaign) }}">
                                    @csrf
                                    <button class="btn btn-warning btn-xs">
                                        Finish
                                    </button>
                                </form>
                            @endif

                            @can('campaign.update')
                            <a href="{{ route('marketing.campaigns.edit',$campaign) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>
                            @endcan

                            @can('campaign.delete')
                            <form method="POST"
                                  action="{{ route('marketing.campaigns.destroy',$campaign) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>
                            @endcan

                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7"
                        class="text-center py-12 text-gray-400">
                        Belum ada campaign
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $campaigns->links() }}

</div>

@endsection