<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SystemRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear Rol Super Admin
        $roleSuperAdmin = Role::firstOrCreate([
            'name' => 'Super Admin'
        ]);

        // 2. Crear un usuario administrador por defecto
        $user = User::firstOrCreate(
            ['email' => 'admin@milagroz.com'],
            [
                'persona_id' => null,
                'estado' => 1,
                'name' => 'Super Administrador',
                'password' => bcrypt('password'),
            ]
        );

        // 3. Asignar el rol al usuario
        $user->assignRole($roleSuperAdmin);
    }
}
