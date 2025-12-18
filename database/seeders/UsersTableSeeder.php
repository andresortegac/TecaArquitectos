<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =====================================================
         * ROLES
         * =====================================================
         */
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $asistenteRole = Role::firstOrCreate([
            'name' => 'asistente',
            'guard_name' => 'web',
        ]);

        $bodegaRole = Role::firstOrCreate([
            'name' => 'bodega',
            'guard_name' => 'web',
        ]);

        /**
         * =====================================================
         * PERMISOS
         * =====================================================
         */
        $permisosAdmin = [
            'ver panel',
            'gestionar usuarios',
            'gestionar productos',
            'gestionar ventas',
            'gestionar bodega',
            'configurar sistema',
        ];

        $permisosAsistente = [
            'ver panel',
            'gestionar productos',
            'gestionar ventas',
        ];

        $permisosBodega = [
            'ver panel',
            'gestionar bodega',
        ];

        foreach (array_merge($permisosAdmin, $permisosAsistente, $permisosBodega) as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        // Asignar permisos a roles
        $adminRole->syncPermissions($permisosAdmin);
        $asistenteRole->syncPermissions($permisosAsistente);
        $bodegaRole->syncPermissions($permisosBodega);

        /**
         * =====================================================
         * USUARIOS
         * =====================================================
         */

        // 👨‍💻 Developer
        $developer = User::firstOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Developer',
                'password' => Hash::make('password123'),
            ]
        );
        $developer->assignRole($adminRole);

        // 👨‍💼 Administrador
        $administrador = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
            ]
        );
        $administrador->assignRole($adminRole);

        // 🧑‍💼 Asistente
        $asistente = User::firstOrCreate(
            ['email' => 'asistente@example.com'],
            [
                'name' => 'Asistente',
                'password' => Hash::make('password123'),
            ]
        );
        $asistente->assignRole($asistenteRole);

        // 📦 Bodega
        $bodega = User::firstOrCreate(
            ['email' => 'bodega@example.com'],
            [
                'name' => 'Bodega',
                'password' => Hash::make('password123'),
            ]
        );
        $bodega->assignRole($bodegaRole);
    }
}
