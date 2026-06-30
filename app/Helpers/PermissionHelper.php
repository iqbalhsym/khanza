<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class PermissionHelper
{
    /**
     * Check if the logged-in user has the specified permission.
     *
     * @param string $permission
     * @return bool
     */
    public static function has($permission): bool
    {
        $user = Session::get('user');
        if (!$user) {
            return false;
        }

        // Super Admin has access to everything
        if (isset($user->role_name) && $user->role_name === 'Super Admin') {
            return true;
        }

        // Check specific module permission
        if (isset($user->permissions) && is_array($user->permissions)) {
            return in_array($permission, $user->permissions);
        }

        return false;
    }

    /**
     * Check if the logged-in doctor has authority over the patient's record.
     *
     * @param string $no_rawat
     * @param string|null $kd_dokter
     * @return bool
     */
    public static function hasDoctorAccessToPatient(string $no_rawat, ?string $kd_dokter): bool
    {
        if (!$kd_dokter) {
            return true; // Non-doctors (Admins, Nurses, etc.) have global access
        }

        return \Illuminate\Support\Facades\DB::table('reg_periksa')
            ->where('no_rawat', $no_rawat)
            ->where(function($q) use ($kd_dokter) {
                $q->where('kd_dokter', $kd_dokter)
                  ->orWhereIn('no_rawat', function($sub) use ($kd_dokter) {
                      $sub->select('no_rawat')
                          ->from('reg_dpjp_tambahan')
                          ->where('kd_dokter', $kd_dokter);
                  });
            })
            ->exists();
    }
}
