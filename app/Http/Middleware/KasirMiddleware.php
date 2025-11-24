<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }


        if (Auth::user()->username !== 'kasir') {
            abort(403, 'Unauthorized access. Kasir only.');
        }

        return $next($request);
    }
}