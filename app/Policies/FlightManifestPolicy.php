<?php

namespace App\Policies;

use App\Models\FlightManifest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlightManifestPolicy
{
    public function view(User $user)
    {
        return $user->can('manifest.view');
    }

    public function generate(User $user)
    {
        return $user->can('manifest.generate');
    }
}