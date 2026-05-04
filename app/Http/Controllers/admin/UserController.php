<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-usuarios', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-usuarios', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-usuarios', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-usuarios', ['only' => ['destroy', 'toggleStatus']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $personas = Persona::with('empleado.rol.systemRoles')->get();
        return view('admin.users.create', compact('roles', 'personas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'persona_id' => $request->persona_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->estado == 0) {
            return redirect()->route('admin.users.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $roles = Role::all();
        $personas = Persona::all();
        
        return view('admin.users.edit', compact('user', 'roles', 'personas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->estado == 0) {
            return redirect()->route('admin.users.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id}",
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'persona_id' => $request->persona_id,
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        // Actualizar roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage (Inactivate).
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes inactivar tu propia cuenta.');
        }

        $user->update(['estado' => 0]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario inactivado correctamente.');
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes cambiar el estado de tu propia cuenta.');
        }

        // Cambio de estado explícito
        $user->estado = ($user->estado == 1) ? 0 : 1;
        $user->save();

        $mensaje = ($user->estado == 1) ? 'activado' : 'inactivado';
        return redirect()->route('admin.users.index')->with('success', "Usuario {$mensaje} correctamente.");
    }
}
