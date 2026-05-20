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

        // Jika ada session, izinkan untuk mengakses route dan cegah caching browser
        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
