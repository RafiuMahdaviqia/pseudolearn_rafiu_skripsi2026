<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($role === 'admin' && $user->is_admin) {
            return $next($request);
        }

        if ($role === 'mahasiswa' && !$user->is_admin) {
            return $next($request);
        }

        return abort(403, 'Akses ditolak.');
    }
}
