<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JamaahDocument;

class JamaahDocumentPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW LIST
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('jamaah.document.view');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW SINGLE
    |--------------------------------------------------------------------------
    */

    public function view(User $user, JamaahDocument $document): bool
    {
        return $user->can('jamaah.document.view');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / UPLOAD
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('jamaah.document.upload');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, JamaahDocument $document): bool
    {
        return $user->can('jamaah.document.delete');
    }

}