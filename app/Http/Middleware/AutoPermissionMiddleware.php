<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutoPermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $name = $route?->getName();
        $method = $request->method();

        if (!$name) return $next($request);

        [$action, $module] = $this->parseRouteName($name, $method);

        if ($action && $module) {
            $permission = "{$action} {$module}";
            if (auth()->check() && !auth()->user()->can($permission)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }

    protected function parseRouteName(string $name, string $method): array
    {
        $map = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
            'reorder' => 'reorder',
            'run' => 'run',
        ];

        $segments = explode('.', $name);
        if (count($segments) < 2) return [null, null];

        $module = $segments[0];
        $action = $map[$segments[1]] ?? null;

        return [$action, $module];
    }
}
