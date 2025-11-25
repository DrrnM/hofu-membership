<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthenticatesRequests::class, function ($app) {
            return new class ($app['auth']) extends Authenticate {
                protected function redirectTo($request)
                {
                    return '/';
                }
            };
        });

        $this->app->bind('kasir', function ($app) {
            return new \App\Http\Middleware\KasirMiddleware;
        });

        $this->app->bind('owner', function ($app) {
            return new \App\Http\Middleware\OwnerMiddleware;
        });
    }

    public function boot(): void
    {

        Authenticate::redirectUsing(function ($request) {
            return '/';
        });

        $this->app->singleton('auth.middleware', function ($app) {
            return new class ($app['auth']) extends Authenticate {
                protected function redirectTo($request)
                {
                    return '/';
                }
            };
        });
    }
}