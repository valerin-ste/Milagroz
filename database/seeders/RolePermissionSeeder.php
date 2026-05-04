<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Módulos del sistema y sus sufijos de permisos
        $modulos = [
            'sedes' => ['ver', 'crear', 'editar', 'eliminar'],
            'areas' => ['ver', 'crear', 'editar', 'eliminar'],
            'perfiles_cargo' => ['ver', 'crear', 'editar', 'eliminar'], // equivale a 'roles' business logic
            'empleados' => ['ver', 'crear', 'editar', 'eliminar', 'exportar'],
            'etapa_precontractual' => ['ver', 'crear', 'editar', 'eliminar'],
            'etapa_contractual' => ['ver', 'crear', 'editar', 'eliminar'],
            'seguridad_salud_trabajo' => ['ver', 'crear', 'editar', 'eliminar'],
            'comunicaciones' => ['ver', 'crear', 'editar', 'eliminar'],
            'solicitudes' => ['ver', 'crear', 'editar', 'eliminar', 'gestionar_estado'],
            'evaluaciones_desempeno' => ['ver', 'crear', 'editar', 'eliminar'],
            'formaciones' => ['ver', 'crear', 'editar', 'eliminar'],
            'usuarios' => ['ver', 'crear', 'editar', 'eliminar'], // Módulo users en web.php
            'roles' => ['ver', 'crear', 'editar', 'eliminar'],    // Módulo system_roles en web.php
            'documentos' => ['ver', 'descargar', 'eliminar'],
            'auditoria' => ['ver'],
        ];

        // Crear permisos
        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$accion}-{$modulo}"]);
            }
        }

        // Crear Roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleRecursosHumanos = Role::firstOrCreate(['name' => 'Recursos Humanos']);
        $roleEmpleado = Role::firstOrCreate(['name' => 'Empleado']);

        // Asignar TODOS los permisos al Super Admin
        $roleSuperAdmin->syncPermissions(Permission::all());

        // Asignar permisos básicos y específicos a Recursos Humanos
        $rhPermissions = Permission::whereIn('name', [
            // Empleados y etapas
            'ver-empleados', 'crear-empleados', 'editar-empleados', 'exportar-empleados',
            'ver-etapa_precontractual', 'crear-etapa_precontractual', 'editar-etapa_precontractual',
            'ver-etapa_contractual', 'crear-etapa_contractual', 'editar-etapa_contractual',
            
            // Gestión de vida del empleado
            'ver-seguridad_salud_trabajo', 'crear-seguridad_salud_trabajo', 'editar-seguridad_salud_trabajo',
            'ver-evaluaciones_desempeno', 'crear-evaluaciones_desempeno', 'editar-evaluaciones_desempeno',
            'ver-formaciones', 'crear-formaciones', 'editar-formaciones',
            
            // Comunicaciones y Solicitudes
            'ver-comunicaciones', 'crear-comunicaciones', 'editar-comunicaciones',
            'ver-solicitudes', 'gestionar_estado-solicitudes',
            
            // Documentos
            'ver-documentos', 'descargar-documentos',
            
            // Estructura (solo ver)
            'ver-sedes', 'ver-areas', 'ver-perfiles_cargo'
        ])->get();
        
        $roleRecursosHumanos->syncPermissions($rhPermissions);

        // Asignar permisos al Empleado (muy limitados, principalmente para auto-gestión si se habilita)
        $empleadoPermissions = Permission::whereIn('name', [
            'ver-comunicaciones',
            'ver-solicitudes', 'crear-solicitudes',
            'ver-documentos', 'descargar-documentos'
        ])->get();
        
        $roleEmpleado->syncPermissions($empleadoPermissions);
    }
}
