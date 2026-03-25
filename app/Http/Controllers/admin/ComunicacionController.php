<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comunicacion;
use App\Models\Empleado;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class ComunicacionController extends Controller
{
    public function index()
    {
        $comunicaciones = Comunicacion::with('empleado.persona', 'documentos')
            ->latest()
            ->paginate(10);

        return view('admin.comunicaciones.index', compact('comunicaciones'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->get();
        return view('admin.comunicaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'asunto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $comunicacion = Comunicacion::create([
            'empleado_id' => $request->empleado_id,
            'asunto' => $request->asunto,
            'mensaje' => $request->mensaje,
            'fecha' => $request->fecha,
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $ruta = $archivo->store('comunicaciones', 'public');

                Documento::create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'comunicacion',
                    'documentable_id' => $comunicacion->id,
                    'documentable_type' => Comunicacion::class,
                ]);
            }
        }

        return redirect()->route('admin.comunicaciones.index')
            ->with('success', 'Comunicación creada correctamente');
    }

    public function edit(Comunicacion $comunicacion)
    {
        $empleados = Empleado::with('persona')->get();
        return view('admin.comunicaciones.edit', compact('comunicacion', 'empleados'));
    }

    public function update(Request $request, Comunicacion $comunicacion)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'asunto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $comunicacion->update([
            'empleado_id' => $request->empleado_id,
            'asunto' => $request->asunto,
            'mensaje' => $request->mensaje,
            'fecha' => $request->fecha,
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $ruta = $archivo->store('comunicaciones', 'public');

                Documento::create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'comunicacion',
                    'documentable_id' => $comunicacion->id,
                    'documentable_type' => Comunicacion::class,
                ]);
            }
        }

        return redirect()->route('admin.comunicaciones.index')
            ->with('success', 'Comunicación actualizada correctamente');
    }

    public function destroy(Comunicacion $comunicacion)
    {
        foreach ($comunicacion->documentos as $doc) {
            if (Storage::disk('public')->exists($doc->ruta)) {
                Storage::disk('public')->delete($doc->ruta);
            }
            $doc->delete();
        }

        $comunicacion->delete();

        return back()->with('success', 'Comunicación eliminada correctamente');
    }

    public function deleteArchivo($id)
    {
        $archivo = Documento::findOrFail($id); // si no existe, lanza 404
        Storage::delete($archivo->ruta);
        $archivo->delete();

        return back()->with('success', 'Archivo eliminado correctamente');
    }
}