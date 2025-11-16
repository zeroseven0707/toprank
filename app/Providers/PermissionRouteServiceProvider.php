<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PermissionRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::macro('autoPermission', function () {
            return Route::middleware('auto.permission');
        });
    }
}
