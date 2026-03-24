<?php

namespace App\Models\Traits;

trait HasRoleHelpers
{
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPERADMIN');
    }

    public function isAdminPusat(): bool
    {
        return $this->hasRole('ADMIN_PUSAT');
    }

    public function isAdminCabang(): bool
    {
        return $this->hasRole('ADMIN_CABANG');
    }

    public function isAgent(): bool
    {
        return $this->hasRole('AGENT');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('CUSTOMER');
    }
}