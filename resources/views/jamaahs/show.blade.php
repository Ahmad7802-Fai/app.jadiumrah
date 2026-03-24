@extends('layouts.app')

@section('title','Detail Jamaah')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold">
                {{ $jamaah->nama_lengkap }}
            </h1>

            <p class="text-sm text-gray-500">
                Kode: {{ $jamaah->jamaah_code }}
            </p>
        </div>

        <div class="flex gap-3">

            <a href="{{ route('jamaah.edit',$jamaah) }}"
               class="btn btn-outline">
                Edit
            </a>

            <a href="{{ route('jamaah.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </div>

    </div>


    {{-- ================= INFORMASI UTAMA ================= --}}
    <div class="card">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">

            <div>
                <div class="text-gray-500">Phone</div>
                <div class="font-medium">
                    {{ $jamaah->phone ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Email</div>
                <div class="font-medium">
                    {{ $jamaah->email ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Gender</div>
                <div class="font-medium">

                    {{ match($jamaah->gender) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => '-'
                    } }}

                </div>
            </div>

            <div>
                <div class="text-gray-500">Tempat / Tanggal Lahir</div>
                <div class="font-medium">
                    {{ $jamaah->tempat_lahir ?? '-' }}
                    /
                    {{ $jamaah->tanggal_lahir?->format('d M Y') ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Cabang</div>
                <div class="font-medium">
                    {{ $jamaah->branch?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Agent</div>
                <div class="font-medium">
                    {{ $jamaah->agent?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Source</div>
                <div class="font-medium">
                    {{ ucfirst($jamaah->source) }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Status Approval</div>

                <div>

                    @if($jamaah->approval_status === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($jamaah->approval_status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif

                </div>

            </div>

            <div>
                <div class="text-gray-500">Status Aktif</div>

                <div>
                    @if($jamaah->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </div>

            </div>

        </div>


        {{-- ================= ALAMAT ================= --}}
        @if($jamaah->address)

        <div class="mt-6">

            <div class="text-gray-500 text-sm">
                Alamat
            </div>

            <div class="text-sm">
                {{ $jamaah->address }}
            </div>

        </div>

        @endif


        {{-- ================= APPROVAL BUTTON ================= --}}
        @if($jamaah->approval_status === 'pending')

        <div class="mt-6 flex gap-3">

            @can('jamaah.approval.view')

            <form method="POST"
                  action="{{ route('jamaah.approve',$jamaah) }}">
                @csrf

                <button class="btn btn-primary">
                    Approve
                </button>
            </form>

            <form method="POST"
                  action="{{ route('jamaah.reject',$jamaah) }}">
                @csrf

                <button class="btn btn-danger">
                    Reject
                </button>
            </form>

            @endcan

        </div>

        @endif

    </div>


    {{-- ================= DOKUMEN ================= --}}
    <div class="card">

        <div class="card-header">
            Dokumen
        </div>


        {{-- ================= UPLOAD FORM ================= --}}
        @can('jamaah.document.view')

        <form method="POST"
              action="{{ route('jamaah.documents.store',$jamaah) }}"
              enctype="multipart/form-data"
              class="mb-6 space-y-4">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="label">Jenis</label>

                    <select name="document_type"
                            class="input"
                            required>

                        <option value="">-- Pilih --</option>

                        <option value="passport">Passport</option>
                        <option value="visa">Visa</option>
                        <option value="ktp">KTP</option>
                        <option value="kk">KK</option>
                        <option value="vaccine">Vaccine</option>

                    </select>
                </div>


                <div>
                    <label class="label">Expired</label>

                    <input type="date"
                           name="expired_at"
                           class="input">
                </div>


                <div>
                    <label class="label">File</label>

                    <input type="file"
                           name="file"
                           class="input"
                           required>
                </div>


                <div class="flex items-end">

                    <button type="submit"
                            class="btn btn-primary w-full">
                        Upload
                    </button>

                </div>

            </div>

        </form>

        @endcan


        {{-- ================= LIST DOKUMEN ================= --}}
        @if($jamaah->documents->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Expired</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($jamaah->documents as $doc)

                <tr>

                    <td class="font-medium">
                        {{ strtoupper($doc->document_type) }}
                    </td>

                    <td>
                        {{ $doc->expired_at?->format('d M Y') ?? '-' }}
                    </td>

                    <td>

                        <span class="badge badge-{{ $doc->expiry_badge }}">
                            {{ strtoupper($doc->status) }}
                        </span>

                    </td>

                    <td class="space-x-3">

                        <a href="{{ $doc->file_url }}"
                           target="_blank"
                           class="text-primary hover:underline">

                            Lihat

                        </a>


                        @can('jamaah.document.view')

                        <form method="POST"
                              action="{{ route('jamaah.documents.destroy',$doc) }}"
                              class="inline">

                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:underline">
                                Hapus
                            </button>

                        </form>

                        @endcan

                    </td>

                </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        @else

        <div class="text-sm text-gray-400">
            Belum ada dokumen
        </div>

        @endif

    </div>


    {{-- ================= RIWAYAT BOOKING ================= --}}
    <div class="card">

        <div class="card-header">
            Riwayat Booking
        </div>

        @if($jamaah->bookings->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Paket</th>
                        <th>Keberangkatan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($jamaah->bookings as $booking)

                <tr>

                    <td>
                        {{ $booking->booking_code }}
                    </td>

                    <td>
                        {{ $booking->paket?->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $booking->departure?->tanggal_berangkat ?? '-' }}
                    </td>

                    <td>
                        {{ ucfirst($booking->status) }}
                    </td>

                </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        @else

        <div class="text-sm text-gray-400">
            Belum ada booking
        </div>

        @endif

    </div>

</div>

@endsection