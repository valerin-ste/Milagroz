<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EtapaPrecontractual;
use App\Models\Persona;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EtapaPrecontractualController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-etapa_precontractual', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-etapa_precontractual', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-etapa_precontractual', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-etapa_precontractual', ['only' => ['destroy', 'toggleStatus']]);
    }
    public function index(Request $request)
    {
        $busqueda = $request->buscar;

        $etapas = EtapaPrecontractual::with(['persona', 'documentos'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%")
                      ->orWhere('numero_documento', 'like', "%$busqueda%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.etapa_precontractual.index', compact('etapas', 'busqueda'));
    }

    public function create()
    {
        $personas = Persona::select('id', 'nombres', 'apellidos', 'numero_documento')->get();
        return view('admin.etapa_precontractual.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'fecha_registro' => 'required|date',
            'estado' => 'required',
            'documentos' => 'nullable|array',
            'documentos.*' => 'nullable|file|max:10240'
        ]);

        DB::beginTransaction();
        try {
            $etapa = EtapaPrecontractual::create([
                'persona_id' => $request->persona_id,
                'fecha_registro' => $request->fecha_registro,
                'estado' => $request->estado,
            ]);

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $archivo) {
                    $ruta = $archivo->store('documentos', 'public');
                    $etapa->documentos()->create([
                        'ruta' => $ruta,
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'tipo_documento' => $archivo->getClientMimeType()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.etapa_precontractual.index')
                ->with('success', 'Registro creado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(EtapaPrecontractual $etapa_precontractual)
    {
        $etapa_precontractual->load('persona', 'documentos');
        return view('admin.etapa_precontractual.edit', compact('etapa_precontractual'));
    }

    public function update(Request $request, $id)
    {
        $etapa = EtapaPrecontractual::findOrFail($id);

        DB::beginTransaction();
        try {
            $etapa->estado = $request->estado;
            $etapa->fecha_registro = $request->fecha_registro;
            $etapa->save();

            // ✅ ELIMINAR DOCUMENTOS SELECCIONADOS
            if ($request->has('eliminar_documentos')) {
                foreach ($request->eliminar_documentos as $docId) {
                    $doc = Documento::find($docId);
                    if ($doc) {
                        if (Storage::disk('public')->exists($doc->ruta)) {
                            Storage::disk('public')->delete($doc->ruta);
                        }
                        $doc->delete();
                    }
                }
            }

            // ✅ SUBIR NUEVOS DOCUMENTOS
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $archivo) {
                    $ruta = $archivo->store('documentos', 'public');
                    $etapa->documentos()->create([
                        'ruta' => $ruta,
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'tipo_documento' => $archivo->getClientMimeType()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.etapa_precontractual.index')
                ->with('success', 'Actualizado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $etapa = EtapaPrecontractual::findOrFail($id);
        $etapa->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $registro = EtapaPrecontractual::findOrFail($id);
        $registro->estado = $registro->estado == 1 ? 0 : 1;
        $registro->save();

        return back()->with('success', 'Estado actualizado correctamente.');
    }
}