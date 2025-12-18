<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔐 Permisos
        $permisos = [
            'ver dashboard',
            'gestionar usuarios',
            'gestionar productos',
            'gestionar ventas',
            'ver reportes',
            'gestionar bodega',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 🎭 Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $asistente = Role::firstOrCreate(['name' => 'asistente']);
        $bodega = Role::firstOrCreate(['name' => 'bodega']);

        // 🔑 Asignación de permisos
        $admin->givePermissionTo(Permission::all());

        $asistente->givePermissionTo([
            'ver dashboard',
            'gestionar productos',
            'gestionar ventas',
        ]);

        $bodega->givePermissionTo([
            'ver dashboard',
            'gestionar bodega',
        ]);
    }
}
