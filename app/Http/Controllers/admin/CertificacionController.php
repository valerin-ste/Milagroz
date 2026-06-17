<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificacion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-certificaciones', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-certificaciones', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-certificaciones', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-certificaciones', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;

        $certificaciones = Certificacion::with(['empleado.persona:id,nombres,apellidos', 'documentos:id,documentable_id,documentable_type,nombre_original'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%");
                })->orWhere('nombre_certificacion', 'LIKE', "%$buscar%")
                  ->orWhere('institucion', 'LIKE', "%$buscar%");
            })
            ->when($estado !== null && $estado !== '', function($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.certificaciones.index', compact('certificaciones', 'buscar', 'estado'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.certificaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'nombre_certificacion' => 'required|string|max:200',
            'tipo_certificacion' => 'nullable|string|max:100',
            'institucion' => 'required|string|max:200',
            'codigo_certificado' => 'nullable|string|max:100',
            'fecha_expedicion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_expedicion',
            'archivo' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'observaciones' => 'nullable|string'
        ]);

        $data = $request->only([
            'empleado_id', 'nombre_certificacion', 'tipo_certificacion', 
            'institucion', 'codigo_certificado', 'fecha_expedicion', 
            'fecha_vencimiento', 'observaciones'
        ]);
        $data['estado'] = 1;

        $certificacion = Certificacion::create($data);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            if ($file->isValid()) {
                $ruta = $file->store('certificaciones', 'public');
                $certificacion->update(['archivo' => $ruta]);
                
                $certificacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'Certificación Oficial',
                ]);
            }
        }

        return redirect()->route('admin.certificaciones.index')->with('success', 'Certificación registrada correctamente.');
    }

    public function edit($id)
    {
        $certificacion = Certificacion::with('empleado.persona', 'documentos')->findOrFail($id);
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.certificaciones.edit', compact('certificacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $certificacion = Certificacion::findOrFail($id);

        $request->validate([
            'nombre_certificacion' => 'required|string|max:200',
            'tipo_certificacion' => 'nullable|string|max:100',
            'institucion' => 'required|string|max:200',
            'codigo_certificado' => 'nullable|string|max:100',
            'fecha_expedicion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_expedicion',
            'archivo' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'observaciones' => 'nullable|string'
        ]);

        $data = $request->only([
            'nombre_certificacion', 'tipo_certificacion', 'institucion', 
            'codigo_certificado', 'fecha_expedicion', 'fecha_vencimiento', 
            'observaciones'
        ]);

        $certificacion->update($data);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($certificacion->archivo && Storage::disk('public')->exists($certificacion->archivo)) {
                Storage::disk('public')->delete($certificacion->archivo);
            }

            $file = $request->file('archivo');
            if ($file->isValid()) {
                $ruta = $file->store('certificaciones', 'public');
                $certificacion->update(['archivo' => $ruta]);
                
                $certificacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'Certificación Oficial',
                ]);
            }
        }

        // Manejo de eliminación de documentos (si el sistema ya lo tiene)
        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = \App\Models\Documento::find($docId);
                if ($doc && $doc->documentable_id == $certificacion->id) {
                    if (Storage::disk('public')->exists($doc->ruta)) {
                        Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        return redirect()->route('admin.certificaciones.index')->with('success', 'Certificación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $certificacion = Certificacion::findOrFail($id);
        $certificacion->delete(); // Soft delete
        return back()->with('success', 'Certificación eliminada correctamente.');
    }

    public function toggleStatus($id)
    {
        $certificacion = Certificacion::findOrFail($id);
        $nuevoEstado = $certificacion->estado == 1 ? 0 : 1;
        $certificacion->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'activada' : 'desactivada';
        return back()->with('success', "Certificación $texto correctamente.");
    }
}
