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
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre,' . $role->id . '|max:100',
            'descripcion' => 'nullable|max:255',
        ]);

        $role->update($request->all());
        return redirect()->route('admin.roles.index')->with('success');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success');
    }
}