<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,

        
        'owner' => \App\Http\Middleware\OwnerMiddleware::class,
        'kasir' => \App\Http\Middleware\KasirMiddleware::class,
    ];
}