<?php

namespace App\Services\Contracts;

use App\Models\User;

interface UserServiceInterface
{
    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;
}