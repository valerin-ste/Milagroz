<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::latest()->paginate(10);

        return view('admin.personas.index', compact('personas'));
    }

    public function create()
    {
        return view('admin.personas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|numeric|digits_between:5,20|unique:personas',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|numeric|digits_between:7,15',
        ]);

        Persona::create($request->all());

        return redirect()->route('admin.personas.index')
            ->with('success', 'Empleado creado correctamente');
    }

    public function edit(Persona $persona)
    {
        return view('admin.personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|numeric|digits_between:5,20|unique:personas,numero_documento,' . $persona->id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|numeric|digits_between:7,15',
        ]);

        $persona->update($request->all());

        return redirect()->route('admin.personas.index')
            ->with('success', 'Empleado actualizado correctamente');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('admin.personas.index')
            ->with('success', 'Empleado eliminado');
    }
}