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

        $ldap_success = false;
        $ldap_user_data = [];

        // 1. Cobalah menggunakan LDAP jika ekstensi terinstal
        if (extension_loaded('ldap')) {
            $ldap_host = config('ldap.host', '10.121.1.162');
            $ldap_port = config('ldap.port', 389);
            $ldap_base_dn = config('ldap.base_dn', '');
            $ldap_admin = config('ldap.admin_user', '');
            $ldap_admin_pass = config('ldap.admin_pass', '');

            try {
                $ldap_conn = @ldap_connect("ldap://{$ldap_host}:{$ldap_port}");
                if ($ldap_conn) {
                    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

                    // Bind admin/service account
                    $admin_bind = @ldap_bind($ldap_conn, $ldap_admin, $ldap_admin_pass);
                    if ($admin_bind) {
                        // Cari user
                        $search_filter = "(|(sAMAccountName={$username})(userPrincipalName={$username}))";
                        $search = @ldap_search($ldap_conn, $ldap_base_dn, $search_filter);
                        if ($search) {
                            $entries = ldap_get_entries($ldap_conn, $search);
                            if ($entries['count'] > 0) {
                                $user_dn = $entries[0]['dn'];
                                // Bind user password
                                $user_bind = @ldap_bind($ldap_conn, $user_dn, $password);
                                if ($user_bind) {
                                    $ldap_success = true;
                                    $ldap_user_data = [
                                        'username' => $username,
                                        'displayname' => $entries[0]['displayname'][0] ?? $username,
                                        'mail' => $entries[0]['mail'][0] ?? null,
                                        'office' => $entries[0]['physicaldeliveryofficename'][0] ?? null,
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Biarkan lanjut ke fallback database lokal jika koneksi LDAP error/timeout
            }
        }

        // 2. Jika LDAP berhasil, lakukan pencarian/pendaftaran lokal
        if ($ldap_success) {
            $dbUser = \App\Models\User::with('role')->where('username', $username)->first();

            $ad_office = $ldap_user_data['office'] ?? null;
            $doctor = null;

            // Cari dokter dari AD Office (physicalDeliveryOfficeName) terlebih dahulu
            if (!empty($ad_office)) {
                $doctor = DB::connection('dokter')->table('dokter')
                    ->where('kd_dokter', $ad_office)
                    ->first();
            }

            // Fallback cari dokter berdasarkan username (Kode Dokter)
            if (!$doctor) {
                $doctor = DB::connection('dokter')->table('dokter')
                    ->where('kd_dokter', $username)
                    ->first();
            }

            // Fallback cari dokter berdasarkan email
            if (!$doctor && !empty($ldap_user_data['mail'])) {
                $doctor = DB::connection('dokter')->table('dokter')
                    ->where('email', $ldap_user_data['mail'])
                    ->first();
            }

            $kd_dokter = $doctor ? $doctor->kd_dokter : null;
            $roleName = $doctor ? 'Dokter' : 'Staf';

            // Auto-Register jika belum ada di database lokal
            if (!$dbUser) {
                $role = \App\Models\Role::where('name', $roleName)->first();
                if (!$role) {
                    $role = \App\Models\Role::where('name', 'Staf')->first();
                }

                $dbUser = \App\Models\User::create([
                    'name' => $doctor ? $doctor->nm_dokter : $ldap_user_data['displayname'],
                    'username' => $username,
                    'email' => $ldap_user_data['mail'],
                    'role_id' => $role ? $role->id : null,
                    'kd_dokter' => $kd_dokter,
                    'is_active' => true,
                ]);
            } else {
                // Sinkronisasi data dari AD ke lokal jika sudah ada perubahan di AD
                $updated = false;

                if ($doctor && $dbUser->kd_dokter !== $doctor->kd_dokter) {
                    $dbUser->kd_dokter = $doctor->kd_dokter;
                    
                    // Update ke role Dokter jika saat ini bukan Super Admin
                    if (!$dbUser->role || $dbUser->role->name !== 'Super Admin') {
                        $role = \App\Models\Role::where('name', 'Dokter')->first();
                        if ($role) {
                            $dbUser->role_id = $role->id;
                        }
                    }
                    $updated = true;
                }

                if ($dbUser->name !== ($doctor ? $doctor->nm_dokter : $ldap_user_data['displayname'])) {
                    $dbUser->name = $doctor ? $doctor->nm_dokter : $ldap_user_data['displayname'];
                    $updated = true;
                }

                if (!empty($ldap_user_data['mail']) && $dbUser->email !== $ldap_user_data['mail']) {
                    $dbUser->email = $ldap_user_data['mail'];
                    $updated = true;
                }

                if ($updated) {
                    $dbUser->save();
                }
            }
            
            $dbUser->load('role');

            if (!$dbUser->is_active) {
                return back()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
            }

            // Simpan session
            $user = new \stdClass();
            $user->id = $dbUser->id;
            $user->usere = $dbUser->username;
            $user->nama = $dbUser->name;
            $user->kd_dokter = $dbUser->kd_dokter;
            $user->role_id = $dbUser->role_id;
            $user->role_name = $dbUser->role ? $dbUser->role->name : 'Staf';
            $user->permissions = $dbUser->role ? $dbUser->role->permissions : ['dashboard'];

            Session::put('user', $user);
            return redirect('/dashboard');
        }

        // 3. Fallback: Authenticate via Local Database
        $dbUser = \App\Models\User::with('role')->where('username', $username)->first();
        if ($dbUser && \Illuminate\Support\Facades\Hash::check($password, $dbUser->password)) {
            if (!$dbUser->is_active) {
                return back()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
            }

            $user = new \stdClass();
            $user->id = $dbUser->id;
            $user->usere = $dbUser->username;
            $user->nama = $dbUser->name;
            $user->kd_dokter = $dbUser->kd_dokter;
            $user->role_id = $dbUser->role_id;
            $user->role_name = $dbUser->role ? $dbUser->role->name : 'Staf';
            $user->permissions = $dbUser->role ? $dbUser->role->permissions : ['dashboard'];

            Session::put('user', $user);
            return redirect('/dashboard');
        }

        // Jika semua metode gagal
        if (extension_loaded('ldap')) {
            return back()->with('error', 'Login gagal. Silakan periksa username dan password Anda (LDAP/Lokal).');
        } else {
            return back()->with('error', 'Ekstensi php-ldap tidak terpasang & autentikasi lokal gagal. Periksa kembali username/password.');
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
