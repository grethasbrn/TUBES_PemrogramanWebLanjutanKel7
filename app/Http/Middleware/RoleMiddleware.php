<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Kalau belum login, redirect ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kalau sudah login tapi role-nya salah
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}