<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre|max:100',
            'descripcion' => 'nullable|max:255',
        ]);

        Role::create($request->all());
        return redirect()->route('admin.roles.index')->with('success');
    }

    public function edit(Role $role)
    {
        if ($role->estado != 1) {
            return redirect()->route('admin.roles.index')->with('error', 'No se puede editar un rol que está inactivo.');
        }
        return view('admin.roles.edit', compact('role'));
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