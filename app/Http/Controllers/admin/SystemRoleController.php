<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-roles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-roles', ['only' => ['destroy', 'toggleStatus']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.system_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.system_roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:system_roles,name'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.system_roles.index')->with('success', 'Rol de sistema creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->estado == 0) {
            return redirect()->route('admin.system_roles.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $permissions = Permission::all();
        
        return view('admin.system_roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->estado == 0) {
            return redirect()->route('admin.system_roles.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $request->validate([
            'name' => "required|string|unique:system_roles,name,{$id}"
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.system_roles.index')->with('success', 'Rol de sistema actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage (Inactivate).
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->name === 'Admin') {
            return redirect()->route('admin.system_roles.index')->with('error', 'No se puede inactivar el rol Administrador principal.');
        }

        $role->update(['estado' => 0]);

        return redirect()->route('admin.system_roles.index')->with('success', 'Rol de sistema inactivado correctamente.');
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->name === 'Admin') {
            return redirect()->route('admin.system_roles.index')->with('error', 'No se puede cambiar el estado del rol Administrador principal.');
        }

        // Cambio de estado explícito
        $role->estado = ($role->estado == 1) ? 0 : 1;
        $role->save();

        $mensaje = ($role->estado == 1) ? 'activado' : 'inactivado';
        return redirect()->route('admin.system_roles.index')->with('success', "Rol {$mensaje} correctamente.");
    }
}
