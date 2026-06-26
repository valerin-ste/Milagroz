<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Productividad;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Documento;

class ProductividadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-productividad', ['only' => ['index', 'viewArchivo', 'downloadArchivo']]);
        $this->middleware('permission:crear-productividad', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-productividad', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-productividad', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;
        $fecha = $request->fecha;

        $productividades = Productividad::with(['empleado.persona:id,nombres,apellidos'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%");
                })->orWhere('titulo', 'LIKE', "%$buscar%");
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

        return view('admin.productividades.index', compact('productividades', 'buscar', 'estado', 'fecha'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.productividades.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'nullable|string|max:100',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|max:5120',
        ]);

        $data = $request->only([
            'empleado_id',
            'tipo',
            'titulo',
            'descripcion',
            'fecha'
        ]);

        $fechaParsed = Carbon::parse($request->fecha);

        $data['mes'] = $fechaParsed->month;
        $data['anio'] = $fechaParsed->year;
        $data['estado'] = 1;

        $productividad = Productividad::create($data);

        // ✅ GUARDAR DOCUMENTO
        if ($request->hasFile('archivo')) {

        $archivo = $request->file('archivo');

        // Nombre original
        $nombreOriginal = $archivo->getClientOriginalName();

        // Guardar archivo con nombre original
        $ruta = $archivo->storeAs(
            'productividades',
            $nombreOriginal,
            'public'
        );

        // Guardar ruta en productividad
        $productividad->update([
            'archivo' => $ruta
        ]);

        // Guardar documento relacionado
        $productividad->documentos()->create([
            'ruta' => $ruta,
            'nombre_original' => $nombreOriginal,
            'tipo_documento' => $archivo->getClientMimeType()
        ]);
    }

        return redirect()
            ->route('admin.productividades.index')
            ->with('success', 'Registro de productividad creado correctamente.');
    }

    public function edit($id)
    {
        $productividad = Productividad::with('empleado.persona')->findOrFail($id);
        
        if ($productividad->estado == 0) {
            return redirect()->route('admin.productividades.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.productividades.edit', compact('productividad', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $productividad = Productividad::findOrFail($id);

        if ($productividad->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'nullable|string|max:100',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|max:5120',
        ]);

        $data = $request->only(['empleado_id', 'tipo', 'titulo', 'descripcion', 'fecha']);
        
        $fechaParsed = Carbon::parse($request->fecha);
        $data['mes'] = $fechaParsed->month;
        $data['anio'] = $fechaParsed->year;

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($productividad->archivo && Storage::disk('public')->exists($productividad->archivo)) {
                Storage::disk('public')->delete($productividad->archivo);
            }
            $data['archivo'] = $request->file('archivo')->store('productividades', 'public');
        } elseif ($request->eliminar_archivo) {
            if ($productividad->archivo && Storage::disk('public')->exists($productividad->archivo)) {
                Storage::disk('public')->delete($productividad->archivo);
            }
            $data['archivo'] = null;
        }

        $productividad->update($data);

        return redirect()->route('admin.productividades.index')->with('success', 'Registro de productividad actualizado correctamente.');
    }

    public function destroy($id)
    {
        $productividad = Productividad::findOrFail($id);
        $productividad->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $productividad = Productividad::findOrFail($id);
        $nuevoEstado = $productividad->estado == 1 ? 0 : 1;
        $productividad->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivado' : 'desactivado';
        return back()->with('success', "Registro $texto correctamente.");
    }

    public function viewArchivo($id)
    {
        $productividad = Productividad::findOrFail($id);
        if (!$productividad->archivo || !Storage::disk('public')->exists($productividad->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        $path = storage_path('app/public/' . $productividad->archivo);
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['xls', 'xlsx', 'doc', 'docx'])) {
            $publicUrl = Storage::disk('public')->url($productividad->archivo);
            return redirect('https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($publicUrl));
        }

        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];

        $headers = ['Content-Disposition' => 'inline; filename="' . basename($productividad->archivo) . '"'];
        if (isset($mimeMap[$ext])) {
            $headers['Content-Type'] = $mimeMap[$ext];
        }

        while (ob_get_level() > 0) ob_end_clean();
        return response()->file($path, $headers);
    }

    public function downloadArchivo($id)
    {
        $productividad = Productividad::findOrFail($id);
        if (!$productividad->archivo || !Storage::disk('public')->exists($productividad->archivo)) {
            abort(404, 'Archivo no encontrado');
        }
        return Storage::disk('public')->download($productividad->archivo);
    }
}
