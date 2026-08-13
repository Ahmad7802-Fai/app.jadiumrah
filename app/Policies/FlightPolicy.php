<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Flight;

class FlightPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('flight.view');
    }

    public function view(User $user, Flight $flight)
    {
        return $user->can('flight.view');
    }

    public function create(User $user)
    {
        return $user->can('flight.create');
    }

    public function update(User $user, Flight $flight)
    {
        return $user->can('flight.update');
    }

    public function delete(User $user, Flight $flight)
    {
        return $user->can('flight.delete');
    }
}