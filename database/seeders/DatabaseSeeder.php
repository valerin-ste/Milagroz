<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemRoleSeeder::class,
            RolePermissionSeeder::class,
            //hay crea el de empleados para que se le ejecute el de calidad
            CalidadDocumentoSeeder::class,
        ]);
    }
}
