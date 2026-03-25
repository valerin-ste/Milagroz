<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EtapaPrecontractual;
use App\Models\Persona;
use App\Models\Documento;
use App\Services\EtapaPrecontractualService;
use App\Http\Requests\Admin\StoreEtapaPrecontractualRequest;
use Illuminate\Support\Facades\Storage;

class EtapaPrecontractualController extends Controller
{
    protected $service;

    public function __construct(EtapaPrecontractualService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $etapas = EtapaPrecontractual::with(['persona', 'documentos'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.etapa_precontractual.index', compact('etapas'));
    }

    public function create()
    {
        $personas = Persona::select('id', 'nombres', 'apellidos', 'numero_documento')->get();
        return view('admin.etapa_precontractual.create', compact('personas'));
    }

    public function store(StoreEtapaPrecontractualRequest $request)
    {
        $this->service->store($request->validated(), $request->file('documentos'));

        return redirect()->route('admin.etapa_precontractual.index')
            ->with('success', 'Registro creado correctamente.');
    }

    public function edit(EtapaPrecontractual $etapa_precontractual)
    {
        $etapa_precontractual->load('persona', 'documentos'); // 🔥 importante
        return view('admin.etapa_precontractual.edit', compact('etapa_precontractual'));
    }

    public function update(Request $request, $id)
    {
        $etapa = EtapaPrecontractual::findOrFail($id);

        // ✅ ACTUALIZAR ESTADO
        $etapa->estado = $request->estado;
        $etapa->save();

        // 🔥 ELIMINAR DOCUMENTOS
        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = Documento::find($docId);
                if ($doc) {
                    Storage::disk('public')->delete($doc->ruta);
                    $doc->delete();
                }
            }
        }

        // 🔥 SUBIR MÚLTIPLES ARCHIVOS (CORREGIDO)
        if ($request->hasFile('documentos')) {

            foreach ($request->file('documentos') as $archivo) {

                $ruta = $archivo->store('documentos', 'public');

                // ✅ USANDO RELACIÓN POLIMÓRFICA (LA CLAVE)
                $etapa->documentos()->create([
                    'ruta' => $ruta,
                    'nombre_original' => $archivo->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('admin.etapa_precontractual.index')
            ->with('success', 'Actualizado correctamente');
    }

    public function destroy(EtapaPrecontractual $etapa_precontractual)
    {
        $etapa_precontractual->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $registro = EtapaPrecontractual::findOrFail($id);
        $nuevoEstado = $registro->estado == 1 ? 0 : 1;
        $registro->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activado' : 'desactivado';
        return back()->with('success', "Registro $mensaje correctamente.");
    }
}