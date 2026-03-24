<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncMenusFromRoutes extends Command
{
    protected $signature = 'menus:sync';
    protected $description = 'Auto sync menus from route list';

    public function handle()
    {
        $routes = collect(Route::getRoutes())
            ->map(fn($route) => $route->getName())
            ->filter()
            ->filter(fn($name) => str_ends_with($name, '.index'));

        foreach ($routes as $routeName) {

            $parts = explode('.', $routeName);

            if (count($parts) < 2) {
                continue;
            }

            // contoh:
            // users.index
            // superadmin.users.index

            $resource = $parts[count($parts) - 2];

            $menuRoute = $resource . '.index';

            // 🔥 singular permission
            $permissionName = Str::singular($resource) . '.view';

            // ensure permission exists
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);

            $section = $this->resolveSection($resource);

            $menu = Menu::updateOrCreate(
                ['route' => $menuRoute],
                [
                    'label'      => ucfirst(str_replace('-', ' ', $resource)),
                    'permission' => $permissionName,
                    'section'    => $section,
                    'order'      => 1,
                    'is_active'  => true,
                ]
            );

            // 🔥 Auto attach SUPERADMIN
            $superadmin = Role::where('name', 'SUPERADMIN')->first();
            if ($superadmin) {
                $menu->roles()->syncWithoutDetaching([$superadmin->id]);
            }

            $this->info("Synced: {$menuRoute}");
        }

        $this->info('Menu sync completed.');
    }

    protected function resolveSection(string $resource): string
    {
        return match ($resource) {
            'dashboard' => 'MAIN',
            'users', 'roles', 'branches', 'agents' => 'MANAGEMENT',
            'bookings', 'jamaah', 'commission-schemes' => 'OPERATIONS',
            default => 'GENERAL',
        };
    }
}