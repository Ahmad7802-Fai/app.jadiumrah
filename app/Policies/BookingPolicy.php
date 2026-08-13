<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('booking.view');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->owns($user, $booking);
    }

    public function create(User $user): bool
    {
        return $user->can('booking.create');
    }

    public function update(User $user, Booking $booking): bool
    {
        if (!$user->can('booking.update')) {
            return false;
        }

        return $this->owns($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        if (!$user->can('booking.cancel')) {
            return false;
        }

        return $this->owns($user, $booking);
    }

    public function approve(User $user, Booking $booking): bool
    {
        if (!$user->can('booking.approve')) {
            return false;
        }

        return $this->owns($user, $booking);
    }

    protected function owns(User $user, Booking $booking): bool
    {
        if ($user->hasRole(['ADMIN_PUSAT'])) {
            return true;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $booking->branch_id === $user->branch_id;
        }

        if ($user->hasRole('AGENT')) {
            return $booking->agent_id === $user->id
                || $booking->created_by === $user->id;
        }

        if ($user->hasRole('JAMAAH')) {
            return $booking->user_id === $user->id;
        }

        return false;
    }

}