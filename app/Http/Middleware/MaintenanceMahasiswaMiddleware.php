<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class MaintenanceMahasiswaMiddleware
{
    public function handle($request, Closure $next)
    {
        // Jika user belum login, lanjutkan saja (biar bisa login)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $isAdmin = $user->is_admin;

        // Cek status maintenance
        $maintenance = Setting::getValue('maintenance_mahasiswa', '0');

        // Jika maintenance aktif dan user mahasiswa, arahkan ke halaman maintenance
        if ($maintenance === '1' && !$isAdmin) {
            // Hindari infinite loop jika sedang di halaman maintenance
            if (!$request->is('maintenance-mahasiswa')) {
                return redirect()->route('maintenance.mahasiswa');
            }
        }

        return $next($request);
    }
}
