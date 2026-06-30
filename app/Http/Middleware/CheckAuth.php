<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengecek apakah ada object user di session (hasil login dari AuthController)
        if (!Session::has('user')) {
            // Jika tidak ada session, redirect paksa ke halaman login dengan pesan error
            return redirect('/login')->with('error', 'Sesi Anda telah berakhir. Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        // AUTO-REFRESH: Mengambil status terbaru dari database secara real-time
        $sessionUser = Session::get('user');
        if ($sessionUser && isset($sessionUser->id)) {
            $dbUser = \App\Models\User::with('role')->find($sessionUser->id);
            if ($dbUser) {
                // Jika dinonaktifkan oleh admin, paksa keluar seketika
                if (!$dbUser->is_active) {
                    Session::forget('user');
                    return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
                }

                // Perbarui hak akses, nama, dan role secara instan tanpa perlu relogin
                $sessionUser->nama = $dbUser->name;
                $sessionUser->kd_dokter = $dbUser->kd_dokter;
                $sessionUser->role_id = $dbUser->role_id;
                $sessionUser->role_name = $dbUser->role ? $dbUser->role->name : 'Staf';
                $sessionUser->permissions = $dbUser->role ? $dbUser->role->permissions : ['dashboard'];

                Session::put('user', $sessionUser);
            }
        }

        // Jika ada session, izinkan untuk mengakses route dan cegah caching browser
        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
