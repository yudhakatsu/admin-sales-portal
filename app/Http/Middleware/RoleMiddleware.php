<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['message' => 'Silakan login terlebih dahulu.']);
        }

        if (!in_array(Auth::user()->role, $roles)) {
            return redirect('/dashboard')->withErrors(['message' => 'Anda tidak memiliki akses ke halaman ini.']);
        }

        return $next($request);
    }
}


