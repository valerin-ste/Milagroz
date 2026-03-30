<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    // 📄 LISTADO
    public function index(Request $request)
    {
        $busqueda = $request->buscar;
        $estado   = $request->estado;
        $tipo     = $request->tipo;

        $solicitudes = Solicitud::with('empleado.persona')
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('empleado.persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%");
                })->orWhere('tipo', 'like', "%$busqueda%");
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->when($tipo, function ($query, $tipo) {
                $query->where('tipo', $tipo);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.solicitudes.index', compact('solicitudes', 'busqueda', 'estado', 'tipo'));
    }

    // ➕ FORM CREAR
    public function create()
    {
        $empleados = Empleado::with('persona')->get();
        return view('admin.solicitudes.create', compact('empleados'));
    }

    // 💾 GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|max:10240',
        ]);

        $data = $request->only([
            'empleado_id',
            'tipo',
            'descripcion',
            'estado',
            'fecha'
        ]);

        // 🔥 por defecto activo
        $data['activo'] = 1;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            $data['archivo'] = $file->store('solicitudes', 'public');
            $data['nombre_archivo'] = $file->getClientOriginalName();
        }

        Solicitud::create($data);

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Solicitud creada correctamente');
    }

    // 👁️ MOSTRAR
    public function show(Solicitud $solicitud)
    {
        $solicitud->load('empleado.persona');
        return view('admin.solicitudes.show', compact('solicitud'));
    }

    // ✏️ EDITAR
    public function edit($id)
    {
        $solicitud = Solicitud::with('documentos')->findOrFail($id);
        $empleados = Empleado::with('persona')->get();

        return view('admin.solicitudes.edit', compact('solicitud', 'empleados'));
    }

    // 🔄 ACTUALIZAR
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,aprobado,rechazado',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|max:10240',
        ]);

        $solicitud->update([
            'empleado_id' => $request->empleado_id,
            'tipo' => $request->tipo,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'fecha' => $request->fecha
        ]);

        // 🔥 actualizar archivo
        if ($request->hasFile('archivo')) {

            if ($solicitud->archivo && Storage::disk('public')->exists($solicitud->archivo)) {
                Storage::disk('public')->delete($solicitud->archivo);
            }

            $file = $request->file('archivo');

            $solicitud->update([
                'archivo' => $file->store('solicitudes', 'public'),
                'nombre_archivo' => $file->getClientOriginalName()
            ]);
        }

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Actualizado correctamente');
    }

    // ❌ ELIMINAR (AHORA INACTIVA 🔥)
    public function destroy(Solicitud $solicitud)
    {
        $solicitud->activo = 0;
        $solicitud->save();

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Solicitud inactivada correctamente');
    }

    // 🔄 CAMBIAR ESTADO (pendiente/aprobado/rechazado)
    public function cambiarEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobado,rechazado'
        ]);

        $solicitud->update([
            'estado' => $request->estado
        ]);

        return back()->with('success', 'Estado actualizado');
    }

    // 📎 ELIMINAR DOCUMENTO
    public function deleteDocumento($id)
    {
        $doc = Documento::findOrFail($id);

        if ($doc->ruta && Storage::disk('public')->exists($doc->ruta)) {
            Storage::disk('public')->delete($doc->ruta);
        }

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento eliminado correctamente'
        ]);
    }

    // 🔁 ACTIVAR / INACTIVAR
    public function toggle($id)
    {
        $s = Solicitud::findOrFail($id);

        $s->activo = $s->activo == 1 ? 0 : 1;
        $s->save();

        return back()->with('success', 'Estado actualizado');
    }
}