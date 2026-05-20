<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;

class AuthController extends Controller
{

public function loginForm()
{
    return view('auth.login');
}

    public function login(Request $request)
    {
        // Mengecek apakah ekstensi LDAP sudah diinstal di server
        if (!extension_loaded('ldap')) {
            return back()->with('error', 'Ekstensi php-ldap belum terinstal di server. Silakan hubungi administrator.');
        }

        $ldap_host = env('LDAP_HOST', '10.121.1.162');
        $ldap_port = env('LDAP_PORT', 389);
        $ldap_domain = env('LDAP_DOMAIN', 'domain-anda.local'); // Opsional, mungkin tidak dipakai di skenario 2
        $ldap_base_dn = env('LDAP_BASE_DN', '');
        $ldap_admin = env('LDAP_ADMIN_USER', '');
        $ldap_admin_pass = env('LDAP_ADMIN_PASS', '');
        
        $username = $request->username;
        $password = $request->password;

        if (empty($username) || empty($password)) {
            return back()->with('error', 'Username dan password tidak boleh kosong.');
        }

        $captcha_answer = Session::get('captcha_answer');
        if (!$captcha_answer || $request->captcha != $captcha_answer) {
            return back()->with('error', 'Jawaban hitungan matematika tidak sesuai.');
        }

        // Hapus session captcha setelah divalidasi agar tidak bisa di-reuse
        Session::forget('captcha_answer');

        try {
            // Melatih koneksi LDAP menggunakan format URI (mengatasi deprecated warning di PHP 8.3)
            $ldap_conn = ldap_connect("ldap://{$ldap_host}:{$ldap_port}");
            
            if (!$ldap_conn) {
                return back()->with('error', 'Gagal terhubung ke server LDAP/AD.');
            }

            // Setting opsi LDAP AD standar
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            // TAHAP 1: BIND SEBAGAI ADMINISTRATOR / SERVICE ACCOUNT
            $admin_bind = @ldap_bind($ldap_conn, $ldap_admin, $ldap_admin_pass);

            if (!$admin_bind) {
                return back()->with('error', 'Gagal konfigurasi LDAP: Koneksi Service Account ditolak. Periksa kredensial Admin LDAP di file .env');
            }

            // TAHAP 2: PENCARIAN USERNAME (sAMAccountName)
            $search_filter = "(sAMAccountName={$username})";
            $search = @ldap_search($ldap_conn, $ldap_base_dn, $search_filter);
            
            if (!$search) {
                return back()->with('error', 'Pencarian LDAP gagal: Base DN mungkin salah. Periksa file .env');
            }

            $entries = ldap_get_entries($ldap_conn, $search);

            if ($entries['count'] == 0) {
                 return back()->with('error', 'Akun tidak ditemukan di direktori Active Directory.');
            }

            // Mendapatkan Full Distinguished Name (DN) milik user yang login
            $user_dn = $entries[0]['dn'];

            // TAHAP 3: BIND SEBAGAI USER (Verifikasi Password User)
            $user_bind = @ldap_bind($ldap_conn, $user_dn, $password);

            if ($user_bind) {
                // Login LDAP Berhasil! 
                // Karena kita tidak menggunakan DB lokal (sik.sql), kita langsung membuat object user mock
                // Mock user object disimpan di session agar sesuai dengan kebutuhan aplikasi (Dashboard dll)
                $user = new \stdClass();
                $user->usere = $username;
                
                // Coba ambil nama asli dari AD jika tersedia, jika tidak pakai username
                $user->nama = isset($entries[0]['displayname'][0]) ? $entries[0]['displayname'][0] : $username; 
                
                Session::put('user', $user);
                return redirect('/dashboard');
            } else {
                return back()->with('error', 'Password Anda salah. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menghubungi LDAP: ' . $e->getMessage());
        }
    }

public function logout(){

Session::flush();

return redirect('/login');

}

    public function generateCaptcha()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $answer = $num1 + $num2;
        
        Session::put('captcha_answer', $answer);
        
        $text = "$num1 + $num2 = ?";
        
        $svg = '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="140" height="40" viewBox="0 0 140 40">
  <rect width="100%" height="100%" fill="#e9ecef" />
  <!-- Noise lines -->
  <line x1="10" y1="5" x2="130" y2="35" stroke="#ced4da" stroke-width="2" />
  <line x1="130" y1="5" x2="10" y2="35" stroke="#ced4da" stroke-width="2" />
  <line x1="10" y1="20" x2="130" y2="20" stroke="#ced4da" stroke-width="1.5" />
  <!-- Text -->
  <text x="70" y="27" font-family="Arial, sans-serif" font-size="22" font-weight="bold" fill="#495057" text-anchor="middle" letter-spacing="3">'.$text.'</text>
</svg>';

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
