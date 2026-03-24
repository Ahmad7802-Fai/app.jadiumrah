<?php

namespace App\Policies;

use App\Models\User;

class FinanceDashboardPolicy
{
    public function view(User $user): bool
    {
        return $user->can('finance.view');
    }
}