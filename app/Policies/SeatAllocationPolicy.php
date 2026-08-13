<?php

namespace App\Policies;

use App\Models\SeatAllocation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SeatAllocationPolicy
{
    public function view(User $user)
    {
        return $user->can('seat.view');
    }

    public function update(User $user)
    {
        return $user->can('seat.update');
    }
}