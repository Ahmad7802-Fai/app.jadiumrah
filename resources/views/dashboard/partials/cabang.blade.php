<div class="grid grid-cols-4 gap-6">

    <x-dashboard.card 
        title="Total Booking"
        :value="$stats['total_bookings']"
    />

    <x-dashboard.card 
        title="Confirmed"
        :value="$stats['confirmed_bookings']"
    />

    <x-dashboard.card 
        title="Revenue"
        :value="'Rp ' . number_format($stats['total_revenue'])"
    />

    <x-dashboard.card 
        title="Branch Commission"
        :value="'Rp ' . number_format($stats['branch_commission_received'])"
    />

</div>