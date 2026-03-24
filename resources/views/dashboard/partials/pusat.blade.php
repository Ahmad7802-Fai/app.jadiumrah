<div class="grid grid-cols-4 gap-6">

    <x-dashboard.card 
        title="Total Branches"
        :value="$stats['total_branches']"
    />

    <x-dashboard.card 
        title="Total Users"
        :value="$stats['total_users']"
    />

    <x-dashboard.card 
        title="Total Booking"
        :value="$stats['total_bookings']"
    />

    <x-dashboard.card 
        title="Total Revenue"
        :value="'Rp ' . number_format($stats['total_revenue'])"
    />

</div>