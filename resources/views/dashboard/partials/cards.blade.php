<div class="stat-grid">
@foreach($cards['cards'] as $card)
    <div class="card card-stat {{ $card['variant'] ?? 'card-stat-muted' }}">
        <div class="stat-icon">
            <i class="fas {{ $card['icon'] }}"></i>
        </div>

        <div class="stat-content">
            <div class="stat-label">{{ $card['label'] }}</div>

            @if(!empty($card['progress']))
                <div class="stat-value">{{ $card['value'] }}%</div>
            @else
                <div class="stat-value">Rp {{ $card['display'] }}</div>
            @endif
        </div>
    </div>
@endforeach
</div>
