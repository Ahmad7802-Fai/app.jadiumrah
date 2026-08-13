<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder; // 🔥 WAJIB
use Illuminate\Support\Facades\Request;

class AccessScope
{
    public static function apply(Builder $query): Builder
    {
        // 🔥 BYPASS UNTUK ROUTE DETAIL (show / edit)
        $routeName = request()->route()?->getName();

        if ($routeName && preg_match('/\.(show|edit)$/', $routeName)) {
            return $query;
        }

        if (! app()->bound('access.context')) {
            return $query->whereRaw('1 = 0');
        }

        $ctx = app('access.context');

        $role     = strtoupper($ctx['role'] ?? '');
        $branchId = $ctx['branch_id'] ?? null;
        $agentId  = $ctx['agent_id'] ?? null;

        return match ($role) {

            'SUPERADMIN',
            'OPERATOR'
                => $query,

            'ADMIN'
                => $branchId
                    ? $query->where('branch_id', $branchId)
                    : $query->whereRaw('1 = 0'),

            'SALES'
                => $agentId
                    ? $query->where('agent_id', $agentId)
                    : $query->whereRaw('1 = 0'),

            default
                => $query->whereRaw('1 = 0'),
        };
    }
}
