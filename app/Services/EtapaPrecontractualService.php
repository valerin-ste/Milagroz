<?php

namespace App\Services;

use App\Models\EtapaPrecontractual;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class EtapaPrecontractualService
{
    /**
     * Handle the file upload and store the record.
     */
    public function store($data, $files = null)
    {
        if (!$data) {
            throw new \Exception("Data vacío: validación falló");
        }

        $etapa = EtapaPrecontractual::create([
            'persona_id' => $data['persona_id'],
            'fecha_registro' => $data['fecha_registro'],
            'estado' => $data['estado'],
        ]);

        if ($files) {
            foreach ($files as $archivo) {

                $ruta = $archivo->store('documentos', 'public');

                $etapa->documentos()->create([
                    'archivo' => $ruta, // 🔥 NO "ruta"
                    'nombre_original' => $archivo->getClientOriginalName(),
                ]);
            }
        }

        return $etapa;
    }

    /**
     * Handle the update of the record, replacing the file if a new one is provided.
     */
    public function update(EtapaPrecontractual $etapaPrecontractual, array $data, ?array $files = null): bool
    {
        $updated = $etapaPrecontractual->update($data);

        if (isset($data['eliminar_documentos'])) {
            foreach ($data['eliminar_documentos'] as $docId) {
                $doc = $etapaPrecontractual->documentos()->find($docId);
                if ($doc) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->ruta)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        if ($files) {
            foreach ($files as $file) {
                $etapaPrecontractual->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('documentos', 'public'),
                ]);
            }
        }

        return $updated;
    }
    
    /**
     * Delete the record and its associated file.
     */
    public function delete(EtapaPrecontractual $etapaPrecontractual): bool
    {
        foreach ($etapaPrecontractual->documentos as $doc) {
            if (Storage::disk('public')->exists($doc->ruta)) {
                Storage::disk('public')->delete($doc->ruta);
            }
            $doc->delete();
        }

        return $etapaPrecontractual->delete();
    }
}
