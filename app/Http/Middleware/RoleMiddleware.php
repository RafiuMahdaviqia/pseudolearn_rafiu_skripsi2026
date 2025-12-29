<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Cek apakah user adalah admin (is_admin = 1)
        if ($role === 'admin' && $user->is_admin == 1) {
            return $next($request);
        }

        // Cek apakah user adalah mahasiswa (is_admin = 0)
        if ($role === 'mahasiswa' && $user->is_admin == 0) {
            return $next($request);
        }

        return abort(403, 'Unauthorized');
    }

}
