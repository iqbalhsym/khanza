<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan modul-modul
        $allPermissions = [
            'dashboard',
            'pendaftaran',
            'rawat_jalan',
            'rawat_inap',
            'laboratorium',
            'radiologi',
            'farmasi',
            'billing',
            'master_data',
            'laporan',
            'pengaturan'
        ];

        // 2. Buat Role Default
        $roles = [
            [
                'name' => 'Super Admin',
                'permissions' => $allPermissions,
            ],
            [
                'name' => 'Dokter',
                'permissions' => ['dashboard', 'rawat_jalan', 'rawat_inap'],
            ],
            [
                'name' => 'Perawat',
                'permissions' => ['dashboard', 'pendaftaran', 'rawat_jalan'],
            ],
            [
                'name' => 'Apoteker',
                'permissions' => ['dashboard', 'farmasi'],
            ],
            [
                'name' => 'Kasir',
                'permissions' => ['dashboard', 'billing'],
            ],
            [
                'name' => 'Laboran',
                'permissions' => ['dashboard', 'laboratorium'],
            ],
            [
                'name' => 'Radiolog',
                'permissions' => ['dashboard', 'radiologi'],
            ],
            [
                'name' => 'Manajemen',
                'permissions' => ['dashboard', 'laporan'],
            ],
            [
                'name' => 'Staf',
                'permissions' => ['dashboard'],
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(
                ['name' => $r['name']],
                ['permissions' => $r['permissions']]
            );
        }

        // Get Super Admin role ID
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        // 3. Buat default Super Admin User 'mohammad.hud'
        User::updateOrCreate(
            ['username' => 'mohammad.hud'],
            [
                'name' => 'Mohammad Hud',
                'email' => 'mohammad.hud@rs.ui.ac.id',
                'role_id' => $superAdminRole->id,
                'password' => Hash::make('admin123'), // Fallback password lokal
                'is_active' => true,
            ]
        );
    }
}
