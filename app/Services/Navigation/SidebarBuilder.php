<?php

namespace App\Services\Navigation;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

class SidebarBuilder
{
    public function build(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return Cache::remember(
            'sidebar_user_' . $user->id,
            60,
            function () use ($user) {

                $rootMenus = Menu::with('childrenRecursive')
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('section')
                    ->orderBy('order')
                    ->get();

                $sections = [];

                foreach ($rootMenus as $menu) {

                    $item = $this->transform($menu, $user);

                    if (!$item) {
                        continue;
                    }

                    $section = $menu->section ?? 'GENERAL';
                    $sections[$section][] = $item;
                }

                return collect($sections)
                    ->map(fn($items, $section) => [
                        'section' => $section,
                        'items'   => $items,
                    ])
                    ->values()
                    ->toArray();
            }
        );
    }

    private function transform($menu, $user)
    {
        /*
        |--------------------------------------------------------------------------
        | 🔐 PERMISSION CHECK
        |--------------------------------------------------------------------------
        */

        if (!$user->hasRole('SUPERADMIN')) {

            if ($menu->permission && !$user->can($menu->permission)) {
                return null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 🚦 ROUTE VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($menu->route && !Route::has($menu->route)) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | 🔁 TRANSFORM CHILDREN
        |--------------------------------------------------------------------------
        */

        $children = [];
        $isActive = false;

        foreach ($menu->childrenRecursive as $child) {

            $childItem = $this->transform($child, $user);

            if ($childItem) {

                if ($childItem['is_active']) {
                    $isActive = true;
                }

                $children[] = $childItem;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 🔥 SELF ACTIVE CHECK
        |--------------------------------------------------------------------------
        */

        if ($menu->route && request()->routeIs($menu->route . '*')) {
            $isActive = true;
        }

        /*
        |--------------------------------------------------------------------------
        | 🧠 PARENT RULES
        |--------------------------------------------------------------------------
        | 1. Jika punya route → tampil
        | 2. Jika tidak punya route tapi punya children → tampil
        | 3. Jika tidak punya route & tidak punya children → hide
        */

        if (!$menu->route && empty($children)) {
            return null;
        }

        return [
            'label'     => $menu->label,
            'route'     => $menu->route,
            'icon'      => $menu->icon,
            'is_active' => $isActive,
            'children'  => $children,
        ];
    }
}