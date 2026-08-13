<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PaketDeparture;

class DeparturePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('departure.view');
    }

    public function view(User $user, PaketDeparture $departure): bool
    {
        return $user->can('departure.view');
    }

    public function create(User $user): bool
    {
        return $user->can('departure.create');
    }

    public function update(User $user, PaketDeparture $departure): bool
    {
        return $user->can('departure.update');
    }

    public function delete(User $user, PaketDeparture $departure): bool
    {
        return $user->can('departure.delete');
    }
}