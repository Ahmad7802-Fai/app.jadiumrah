<?php

namespace App\Support;

use App\Models\User;

class UserRoleResolver
{

    /*
    |--------------------------------------------------------------------------
    | FRONTEND ROLE
    |--------------------------------------------------------------------------
    |
    | guest
    | jamaah
    | agent
    | staff
    |
    */

    public const ROLE_GUEST = 'guest';
    public const ROLE_JAMAAH = 'jamaah';
    public const ROLE_AGENT = 'agent';
    public const ROLE_STAFF = 'staff';



    /*
    |--------------------------------------------------------------------------
    | RESOLVE ROLE
    |--------------------------------------------------------------------------
    */

    public static function resolve(?User $user): string
    {

        if(!$user){
            return self::ROLE_GUEST;
        }

        $roles = $user->roles->pluck('name')->toArray();

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */

        if(in_array('AGENT',$roles)){
            return self::ROLE_AGENT;
        }

        /*
        |--------------------------------------------------------------------------
        | JAMAAH
        |--------------------------------------------------------------------------
        */

        if(in_array('JAMAAH',$roles)){
            return self::ROLE_JAMAAH;
        }

        /*
        |--------------------------------------------------------------------------
        | STAFF (ALL INTERNAL ROLES)
        |--------------------------------------------------------------------------
        */

        $staffRoles = [

            'SUPERADMIN',

            'ADMIN_PUSAT',
            'ADMIN_CABANG',

            'KEUANGAN_PUSAT',
            'KEUANGAN_CABANG',

            'OPERATOR_CABANG',
            'CRM_CABANG',

            'FINANCE'

        ];

        foreach($roles as $role){

            if(in_array($role,$staffRoles)){
                return self::ROLE_STAFF;
            }

        }

        return self::ROLE_GUEST;

    }



    /*
    |--------------------------------------------------------------------------
    | IS AGENT
    |--------------------------------------------------------------------------
    */

    public static function isAgent(?User $user): bool
    {

        return self::resolve($user) === self::ROLE_AGENT;

    }



    /*
    |--------------------------------------------------------------------------
    | IS JAMAAH
    |--------------------------------------------------------------------------
    */

    public static function isJamaah(?User $user): bool
    {

        return self::resolve($user) === self::ROLE_JAMAAH;

    }



    /*
    |--------------------------------------------------------------------------
    | IS STAFF
    |--------------------------------------------------------------------------
    */

    public static function isStaff(?User $user): bool
    {

        return self::resolve($user) === self::ROLE_STAFF;

    }

}