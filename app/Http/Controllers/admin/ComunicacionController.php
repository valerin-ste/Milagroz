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
    public function __construct()
    {
        $this->middleware('permission:ver-comunicaciones', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-comunicaciones', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-comunicaciones', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-comunicaciones', ['only' => ['destroy', 'toggleStatus']]);
    }
    public function index(Request $request)
    {
        $busqueda  = $request->buscar;
        $documento = $request->documento;

        $comunicaciones = Comunicacion::with('empleado.persona', 'documentos')
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('empleado.persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%");
                });
            })
            ->when($documento, function ($query, $documento) {
                $query->whereHas('empleado.persona', function ($q) use ($documento) {
                    $q->where('numero_documento', 'like', "%$documento%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.comunicaciones.index', compact('comunicaciones', 'busqueda', 'documento'));
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

        $comunicacion = Comunicacion::create($request->only([
            'empleado_id',
            'asunto',
            'mensaje',
            'fecha'
        ]));

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
        $comunicacion->load('documentos');

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

    // 📌 NUEVOS ARCHIVOS
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

    // 🔥 ARCHIVOS A ELIMINAR (ESTO ES LO IMPORTANTE)
    if ($request->eliminar_documentos) {
        foreach ($request->eliminar_documentos as $id) {
            $doc = Documento::find($id);

            if ($doc) {
                if (Storage::disk('public')->exists($doc->ruta)) {
                    Storage::disk('public')->delete($doc->ruta);
                }
                $doc->delete();
            }
        }
    }

    return redirect()->route('admin.comunicaciones.index')
        ->with('success', 'Comunicación actualizada correctamente');
}

    public function destroy(Comunicacion $comunicacion)
    {
        $comunicacion->estado = $comunicacion->estado == 1 ? 0 : 1;
        $comunicacion->save();

        return redirect()
            ->route('admin.comunicaciones.index')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function deleteArchivo($documento)
    {
        $documento = Documento::findOrFail($documento);

        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }

        $documento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado correctamente',
            'id' => $documento->id
        ]);
    }
}