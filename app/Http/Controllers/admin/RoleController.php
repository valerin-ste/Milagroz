<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Spatie\Permission\Models\Role as SystemRole;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-perfiles_cargo', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-perfiles_cargo', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-perfiles_cargo', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-perfiles_cargo', ['only' => ['destroy', 'toggleStatus']]);
    }
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $system_roles = SystemRole::all();
        return view('admin.roles.create', compact('system_roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre|max:100',
            'descripcion' => 'nullable|max:255',
        ]);

        $role = Role::create($request->all());

        if ($request->has('system_roles')) {
            $role->systemRoles()->sync($request->system_roles);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Perfil de cargo creado correctamente.');
    }

    public function edit(Role $role)
    {
        if ($role->estado != 1) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede editar un rol que está inactivo.');
        }

        $system_roles = SystemRole::all();
        return view('admin.roles.edit', compact('role', 'system_roles'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->estado != 1) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede actualizar un rol que está inactivo.');
        }

        $request->validate([
            'nombre' => 'required|unique:roles,nombre,' . $role->id . '|max:100',
            'descripcion' => 'nullable|max:255',
        ]);

        $role->update($request->all());

        if ($request->has('system_roles')) {
            $role->systemRoles()->sync($request->system_roles);
        } else {
            $role->systemRoles()->sync([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        $role->update(['estado' => 0]);
        return redirect()->route('admin.roles.index')->with('success', 'El rol ha sido desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $role = Role::findOrFail($id);
        $nuevoEstado = $role->estado == 1 ? 0 : 1;
        $role->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activado' : 'desactivado';
        return redirect()->route('admin.roles.index')->with('success', "El rol ha sido $mensaje correctamente.");
    }
}