<div class="grid grid-cols-4 gap-6">

    <x-dashboard.card 
        title="My Booking"
        :value="$stats['my_bookings']"
    />

    <x-dashboard.card 
        title="Confirmed"
        :value="$stats['confirmed_bookings']"
    />

    <x-dashboard.card 
        title="My Revenue"
        :value="'Rp ' . number_format($stats['my_total_revenue'])"
    />

    <x-dashboard.card 
        title="My Commission"
        :value="'Rp ' . number_format($stats['my_commission'])"
    />

</div>