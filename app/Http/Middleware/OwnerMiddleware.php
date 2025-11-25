<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }


        $user = Auth::user();

        if ($user->username === 'owner') {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya owner yang dapat mengakses.');
    }
}