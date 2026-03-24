<?php 

namespace App\Policies;

use App\Models\User;
use App\Models\FlashSale;

class FlashSalePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('flashsale.view');
    }

    public function view(User $user, FlashSale $flashSale)
    {
        return $user->can('flashsale.view');
    }

    public function create(User $user)
    {
        return $user->can('flashsale.create');
    }

    public function update(User $user, FlashSale $flashSale)
    {
        return $user->can('flashsale.update');
    }

    public function delete(User $user, FlashSale $flashSale)
    {
        return $user->can('flashsale.delete');
    }
}