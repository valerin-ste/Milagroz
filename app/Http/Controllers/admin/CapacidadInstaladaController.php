<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapacidadInstalada;
use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class CapacidadInstaladaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-capacidad_instalada', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-capacidad_instalada', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-capacidad_instalada', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-capacidad_instalada', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;
        $fecha = $request->fecha;

        $capacidades = CapacidadInstalada::with(['empleado.persona:id,nombres,apellidos,numero_documento', 'documentos'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%")
                      ->orWhere('numero_documento', 'LIKE', "%$buscar%");
                })->orWhere('proceso', 'LIKE', "%$buscar%");
            })
            ->when($estado !== null, function($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($fecha, function($query) use ($fecha) {
                $query->where('fecha', $fecha);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.capacidad_instalada.index', compact('capacidades', 'buscar', 'estado', 'fecha'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.capacidad_instalada.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'proceso' => 'nullable|string|max:150',
            'capacidad_disponible' => 'nullable|integer|min:0',
            'capacidad_utilizada' => 'nullable|integer|min:0',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $data = $request->only([
            'empleado_id',
            'proceso',
            'capacidad_disponible',
            'capacidad_utilizada',
            'fecha',
            'observaciones'
        ]);

        $data['estado'] = 1;

        $registro = CapacidadInstalada::create($data);

        // 📌 NUEVOS ARCHIVOS
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $ruta = $archivo->store('capacidad_instalada', 'public');

                Documento::create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'capacidad_instalada',
                    'documentable_id' => $registro->id,
                    'documentable_type' => CapacidadInstalada::class,
                ]);
            }
        }

        return redirect()
            ->route('admin.capacidad_instalada.index')
            ->with('success', 'Registro de capacidad instalada creado correctamente.');
    }

    public function edit($id)
    {
        $capacidad = CapacidadInstalada::with('empleado.persona', 'documentos')->findOrFail($id);
        
        if ($capacidad->estado == 0) {
            return redirect()->route('admin.capacidad_instalada.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.capacidad_instalada.edit', compact('capacidad', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $capacidad = CapacidadInstalada::findOrFail($id);

        if ($capacidad->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'proceso' => 'nullable|string|max:150',
            'capacidad_disponible' => 'nullable|integer|min:0',
            'capacidad_utilizada' => 'nullable|integer|min:0',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $data = $request->only([
            'empleado_id',
            'proceso',
            'capacidad_disponible',
            'capacidad_utilizada',
            'fecha',
            'observaciones'
        ]);

        $capacidad->update($data);

        // 📌 NUEVOS ARCHIVOS
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $ruta = $archivo->store('capacidad_instalada', 'public');

                Documento::create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'capacidad_instalada',
                    'documentable_id' => $capacidad->id,
                    'documentable_type' => CapacidadInstalada::class,
                ]);
            }
        }

        // 🔥 ARCHIVOS A ELIMINAR
        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = Documento::find($docId);

                if ($doc && $doc->documentable_id == $capacidad->id && $doc->documentable_type == CapacidadInstalada::class) {
                    if (Storage::disk('public')->exists($doc->ruta)) {
                        Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        return redirect()->route('admin.capacidad_instalada.index')->with('success', 'Registro de capacidad instalada actualizado correctamente.');
    }
    
    public function show($id)
    {
        $capacidad = CapacidadInstalada::with('empleado.persona')->findOrFail($id);
        return view('admin.capacidad_instalada.show', compact('capacidad'));
    }

    public function destroy($id)
    {
        $capacidad = CapacidadInstalada::findOrFail($id);
        $capacidad->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $capacidad = CapacidadInstalada::findOrFail($id);
        $nuevoEstado = $capacidad->estado == 1 ? 0 : 1;
        $capacidad->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivado' : 'desactivado';
        return back()->with('success', "Registro $texto correctamente.");
    }
}
