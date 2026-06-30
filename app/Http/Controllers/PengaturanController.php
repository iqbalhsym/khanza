<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    /**
     * Display the account and role management panel.
     */
    public function index()
    {
        $users = User::with('role')->orderBy('name', 'asc')->get();
        $roles = Role::all();
        
        // Fetch doctors list for selection from the doctor database connection
        $dokters = [];
        try {
            $dokters = DB::connection('dokter')->table('dokter')
                ->select('kd_dokter', 'nm_dokter')
                ->where('status', '1') // Active doctors
                ->orderBy('nm_dokter', 'asc')
                ->get();
        } catch (\Exception $e) {
            // Fallback if doctor DB is not available
        }

        $allPermissions = [
            'dashboard' => 'Dashboard Utama',
            'pendaftaran' => 'Pendaftaran & Antrian',
            'rawat_jalan' => 'Rawat Jalan & Tindakan',
            'rawat_inap' => 'Rawat Inap & Kamar',
            'laboratorium' => 'Layanan Laboratorium',
            'radiologi' => 'Layanan Radiologi',
            'farmasi' => 'Farmasi & Apotek',
            'billing' => 'Billing / Kasir',
            'master_data' => 'Master Data',
            'laporan' => 'Laporan Sistem',
            'pengaturan' => 'Pengaturan & RBAC'
        ];

        return view('pengaturan.index', compact('users', 'roles', 'dokters', 'allPermissions'));
    }

    /**
     * Store a newly created local or LDAP user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255',
            'role_id' => 'required|exists:roles,id',
            'kd_dokter' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'kd_dokter' => $request->kd_dokter ?: null,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        User::create($userData);

        return redirect('/pengaturan')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified user in the local database.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'nullable|email|max:255',
            'role_id' => 'required|exists:roles,id',
            'kd_dokter' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->kd_dokter = $request->kd_dokter ?: null;
        $user->is_active = $request->has('is_active') ? true : false;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect('/pengaturan')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Delete the specified user.
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting current user from session
        if (session('user') && session('user')->id == $user->id) {
            return redirect('/pengaturan')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect('/pengaturan')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Update permissions for a specific role.
     */
    public function updateRolePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        // Super Admin permissions should remain immutable or protected
        if ($role->name === 'Super Admin') {
            return redirect('/pengaturan')->with('error', 'Hak akses Super Admin tidak dapat dimodifikasi.');
        }

        $role->permissions = $request->input('permissions', []);
        $role->save();

        return redirect('/pengaturan')->with('success', 'Hak akses role ' . $role->name . ' berhasil diperbarui.');
    }
}
