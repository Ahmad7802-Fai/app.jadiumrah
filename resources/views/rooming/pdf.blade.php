<h2>Rooming List</h2>
<p>{{ $departure->paket->name }}</p>
<p>{{ $departure->departure_date->format('d M Y') }}</p>

@foreach($departure->rooms as $room)
    <h4>Room {{ $room->room_number }} ({{ $room->city }})</h4>
    <ul>
        @foreach($room->jamaahs as $jamaah)
            <li>{{ $jamaah->nama_lengkap }}</li>
        @endforeach
    </ul>
@endforeach