<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formacion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-formaciones', ['only' => ['index', 'show', 'vencimientos']]);
        $this->middleware('permission:crear-formaciones', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-formaciones', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-formaciones', ['only' => ['destroy', 'toggleStatus']]);
    }
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;

        $formaciones = Formacion::with([
                'empleado.persona:id,nombres,apellidos',
                'documentos:id,documentable_id,documentable_type,nombre_original'
            ])
            ->select(
                'id',
                'empleado_id',
                'nombre_curso',
                'estado_curso',
                'fecha_inicio',
                'fecha_fin',
                'vence',
                'estado'
            )

            // Si es empleado, solo ve sus formaciones
            ->when(auth()->user()->hasRole('Empleado'), function ($query) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('persona_id', auth()->user()->persona_id);
                });
            })

            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->whereHas('empleado.persona', function ($sq) use ($buscar) {
                        $sq->where('nombres', 'LIKE', "%{$buscar}%")
                        ->orWhere('apellidos', 'LIKE', "%{$buscar}%");
                    })
                    ->orWhere('nombre_curso', 'LIKE', "%{$buscar}%");
                });
            })

            ->when($request->vence !== null && $request->vence !== '', function ($query) use ($request) {
                $query->where('vence', $request->vence);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        $vence = $request->vence;

        return view('admin.formaciones.index', compact(
            'formaciones',
            'buscar',
            'estado',
            'vence'
        ));
    }

    public function vencimientos(Request $request)
    {
        $buscar = $request->buscar;
        $hoy = \Carbon\Carbon::now()->startOfDay();
        $en30d = \Carbon\Carbon::now()->addDays(30)->endOfDay();

        $formaciones = Formacion::with(['empleado.persona:id,nombres,apellidos,numero_documento', 'documentos:id,documentable_id,documentable_type,nombre_original'])
            ->select('id', 'empleado_id', 'nombre_curso', 'estado_curso', 'fecha_inicio', 'fecha_fin', 'vence', 'estado')
            ->where('vence', 1)
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%")
                      ->orWhere('numero_documento', 'LIKE', "%$buscar%");
                });
            })
            ->when($request->nombre_curso, function($query) use ($request) {
                $query->where('nombre_curso', 'LIKE', "%{$request->nombre_curso}%");
            })
            ->orderBy('fecha_fin', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.formaciones.vencimientos', compact('formaciones', 'buscar', 'hoy', 'en30d'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.formaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'nombre_curso' => 'required|string|max:150',
            'estado_curso' => 'required|string|in:en curso,finalizado,pendiente',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required_if:vence,1|nullable|date',
            'vence' => 'required|integer|in:0,1',
            'documento' => 'nullable|file|mimes:pdf|max:2048',
            'observaciones' => 'nullable|string'
        ], [
            'fecha_fin.required_if' => 'La fecha de vencimiento es obligatoria si el curso vence.',
            'documento.mimes' => 'El soporte debe ser un archivo de tipo PDF.',
        ]);

        $data = [
            'empleado_id' => $request->empleado_id,
            'nombre_curso' => $request->nombre_curso,
            'estado_curso' => $request->estado_curso,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => ($request->vence == 0) ? null : $request->fecha_fin,
            'vence' => $request->vence,
            'observaciones' => $request->observaciones,
            'estado' => 1
        ];
        

        $formacion = Formacion::create($data);

        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            if ($file->isValid()) {
                $formacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('formaciones', 'public'),
                    'tipo_documento' => 'Certificado formación',
                ]);
            }
        }

        return redirect()->route('admin.formaciones.index')->with('success', 'Formación registrada correctamente.');
    }

    public function edit($id)
    {
        $formacion = Formacion::with('empleado.persona', 'documentos')->findOrFail($id);
        
        if ($formacion->estado == 0) {
            return redirect()->route('admin.formaciones.index')->with('error', 'No se puede editar una formación inactiva.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.formaciones.edit', compact('formacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $formacion = Formacion::findOrFail($id);

        if ($formacion->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'nombre_curso' => 'required|string|max:150',
            'estado_curso' => 'required|string|in:en curso,finalizado,pendiente',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required_if:vence,1|nullable|date',
            'vence' => 'required|integer|in:0,1',
            'documento' => 'nullable|file|mimes:pdf|max:2048',
            'observaciones' => 'nullable|string'
        ], [
            'fecha_fin.required_if' => 'La fecha de vencimiento es obligatoria si el curso vence.',
            'documento.mimes' => 'El soporte debe ser un archivo de tipo PDF.',
        ]);

        $data = $request->only(['nombre_curso', 'estado_curso', 'fecha_inicio', 'fecha_fin', 'vence', 'observaciones']);
        if ($data['vence'] == 0) $data['fecha_fin'] = null;

        $formacion->update($data);

        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            if ($file->isValid()) {
                $formacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('formaciones', 'public'),
                    'tipo_documento' => 'Certificado formación',
                ]);
            }
        }

        //  ELIMINAR DOCUMENTOS MARCADOS (SISTEMA ESTANDARIZADO)
        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = \App\Models\Documento::find($docId);
                if ($doc && $doc->documentable_id == $formacion->id) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->ruta)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        // Removida validación post-update de documento obligatorio
        // ya que el archivo o soporte es opcional según los requerimientos.

        return redirect()->route('admin.formaciones.index')->with('success', 'Formación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $formacion = Formacion::findOrFail($id);
        $formacion->update(['estado' => 0]);
        return back()->with('success', 'Formación desactivada correctamente.');
    }

    public function toggleStatus($id)
    {
        $formacion = Formacion::findOrFail($id);
        $nuevoEstado = $formacion->estado == 1 ? 0 : 1;
        $formacion->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivada' : 'desactivada';
        return back()->with('success', "Formación $texto correctamente.");
    }
}
