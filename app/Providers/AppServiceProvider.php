<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        if (!config('app.timezone') || config('app.timezone') === 'UTC') {
            config(['app.timezone' => 'Asia/Jakarta']);
        }

        date_default_timezone_set(config('app.timezone'));

        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        \Log::info('Application booted', [
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'current_time' => now()->format('Y-m-d H:i:s')
        ]);

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