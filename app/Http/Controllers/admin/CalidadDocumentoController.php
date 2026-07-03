<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalidadDocumento;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CalidadDocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-calidad', ['only' => ['index', 'show', 'viewArchivo', 'downloadArchivo']]);
        $this->middleware('permission:crear-calidad', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-calidad', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-calidad', ['only' => ['destroy', 'toggleStatus']]);
    }

    // ──────────────────────────────────────────
    // CATEGORÍAS PREDEFINIDAS
    // ──────────────────────────────────────────
    public const CATEGORIAS = [
        'Procedimiento',
        'Instructivo',
        'Formato',
        'Política',
        'Manual',
        'Normativa',
        'Protocolo',
        'Certificado',
        'Registro',
        'Otro',
    ];

    // ──────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────
    public function index(Request $request)
    {
        $buscar    = $request->buscar;
        $categoria = $request->categoria;
        $estado    = $request->estado;
        $vencimiento = $request->vencimiento;

        $query = CalidadDocumento::with(['empleado.persona:id,nombres,apellidos'])
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($q2) use ($buscar) {
                    $q2->where('nombre_documento', 'LIKE', "%$buscar%")
                       ->orWhere('codigo', 'LIKE', "%$buscar%")
                       ->orWhereHas('empleado.persona', function ($q3) use ($buscar) {
                           $q3->where('nombres', 'LIKE', "%$buscar%")
                              ->orWhere('apellidos', 'LIKE', "%$buscar%");
                       });
                });
            })
            ->when($categoria, fn($q) => $q->where('categoria', $categoria))
            ->when($estado !== null && $estado !== '', fn($q) => $q->where('estado', $estado))
            ->when($vencimiento === 'vencido',  fn($q) => $q->whereDate('fecha_vencimiento', '<', now()))
            ->when($vencimiento === 'proximo',  fn($q) => $q->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)]))
            ->when($vencimiento === 'vigente',  fn($q) => $q->whereDate('fecha_vencimiento', '>', now()->addDays(30)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categorias = self::CATEGORIAS;

        return view('admin.calidad_documentos.index', compact(
            'query', 'buscar', 'categoria', 'estado', 'vencimiento', 'categorias'
        ))->with('calidad_documentos', $query);
    }

    // ──────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────
    public function create()
    {
        $empleados  = Empleado::with('persona')->where('estado', 1)->get();
        $categorias = self::CATEGORIAS;

        return view('admin.calidad_documentos.create', compact('empleados', 'categorias'));
    }

    // ──────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'      => 'required|exists:empleados,id',
            'categoria'        => 'required|string|max:50',
            'nombre_documento' => 'required|string|max:150',
            'codigo'           => 'nullable|string|max:50',
            'version'          => 'nullable|string|max:20',
            'fecha_emision'    => 'nullable|date',
            'fecha_vencimiento'=> 'nullable|date|after_or_equal:fecha_emision',
            'archivo'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ], 
        [
            'empleado_id.required'       => 'Debes seleccionar un empleado.',
            'nombre_documento.required'  => 'El nombre del documento es obligatorio.',
            'categoria.required'         => 'La categoría es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la de emisión.',
            'archivo.mimes'              => 'Solo se permiten archivos: PDF, Word, Excel e imágenes.',
            'archivo.max'               => 'El archivo no debe superar los 10 MB.',
        ]);

        $data = $request->only([
            'empleado_id', 'categoria', 'nombre_documento',
            'codigo', 'version', 'fecha_emision', 'fecha_vencimiento',
        ]);

        $data['estado'] = 1;

        $documento = CalidadDocumento::create($data);

        if ($request->hasFile('archivo')) {
            $archivo        = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $ruta = $archivo->storeAs('calidad_documentos', $nombreOriginal, 'public');
            $documento->update(['archivo' => $ruta]);
        }

        return redirect()
            ->route('admin.calidad_documentos.index')
            ->with('success', 'Documento de calidad creado correctamente.');
    }

    // ──────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────
    public function edit($id)
    {
        $documento = CalidadDocumento::with('empleado.persona')->findOrFail($id);

        if ($documento->estado == 0) {
            return redirect()
                ->route('admin.calidad_documentos.index')
                ->with('error', 'No se puede editar un documento inactivo.');
        }

        $empleados  = Empleado::with('persona')->where('estado', 1)->get();
        $categorias = self::CATEGORIAS;

        return view('admin.calidad_documentos.edit', compact('documento', 'empleados', 'categorias'));
    }

    // ──────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $documento = CalidadDocumento::findOrFail($id);

        if ($documento->estado == 0) {
            return back()->with('error', 'Edición bloqueada. El documento está inactivo.');
        }

        $request->validate([
            'empleado_id'      => 'required|exists:empleados,id',
            'categoria'        => 'required|string|max:50',
            'nombre_documento' => 'required|string|max:150',
            'codigo'           => 'nullable|string|max:50',
            'version'          => 'nullable|string|max:20',
            'fecha_emision'    => 'nullable|date',
            'fecha_vencimiento'=> 'nullable|date|after_or_equal:fecha_emision',
            'archivo'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ], [
            'empleado_id.required'       => 'Debes seleccionar un empleado.',
            'nombre_documento.required'  => 'El nombre del documento es obligatorio.',
            'categoria.required'         => 'La categoría es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la de emisión.',
            'archivo.mimes'              => 'Solo se permiten archivos: PDF, Word, Excel e imágenes.',
            'archivo.max'               => 'El archivo no debe superar los 10 MB.',
        ]);

        $data = $request->only([
            'empleado_id', 'categoria', 'nombre_documento',
            'codigo', 'version', 'fecha_emision', 'fecha_vencimiento',
        ]);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior
            if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                Storage::disk('public')->delete($documento->archivo);
            }
            $archivo = $request->file('archivo');
            $data['archivo'] = $archivo->storeAs('calidad_documentos', $archivo->getClientOriginalName(), 'public');
        } elseif ($request->eliminar_archivo) {
            if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                Storage::disk('public')->delete($documento->archivo);
            }
            $data['archivo'] = null;
        }

        $documento->update($data);

        return redirect()
            ->route('admin.calidad_documentos.index')
            ->with('success', 'Documento de calidad actualizado correctamente.');
    }

    // ──────────────────────────────────────────
    // DESTROY (inactivar)
    // ──────────────────────────────────────────
    public function destroy($id)
    {
        $documento = CalidadDocumento::findOrFail($id);
        $documento->update(['estado' => 0]);

        return back()->with('success', 'Documento desactivado correctamente.');
    }

    // ──────────────────────────────────────────
    // TOGGLE ESTADO
    // ──────────────────────────────────────────
    public function toggleStatus($id)
    {
        $documento   = CalidadDocumento::findOrFail($id);
        $nuevoEstado = $documento->estado == 1 ? 0 : 1;

        $documento->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivado' : 'desactivado';
        return back()->with('success', "Documento $texto correctamente.");
    }

    // ──────────────────────────────────────────
    // VER ARCHIVO (en navegador)
    // ──────────────────────────────────────────
    public function viewArchivo($id)
    {
        $documento = CalidadDocumento::findOrFail($id);

        if (!$documento->archivo || !Storage::disk('public')->exists($documento->archivo)) {
            abort(404, 'Archivo no encontrado.');
        }

        $path = storage_path('app/public/' . $documento->archivo);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['xls', 'xlsx', 'doc', 'docx'])) {
            $publicUrl = Storage::disk('public')->url($documento->archivo);
            $viewerUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($publicUrl);
            return redirect($viewerUrl);
        }

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $headers = [
            'Content-Disposition' => 'inline; filename="' . str_replace('"', '\\"', basename($documento->archivo)) . '"'
        ];

        if (array_key_exists($ext, $mimeTypes)) {
            $headers['Content-Type'] = $mimeTypes[$ext];
        }

        while (ob_get_level() > 0) ob_end_clean();

        return response()->file($path, $headers);
    }

    // ──────────────────────────────────────────
    // DESCARGAR ARCHIVO
    // ──────────────────────────────────────────
    public function downloadArchivo($id)
    {
        $documento = CalidadDocumento::findOrFail($id);

        if (!$documento->archivo || !Storage::disk('public')->exists($documento->archivo)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('public')->download($documento->archivo);
    }
}
