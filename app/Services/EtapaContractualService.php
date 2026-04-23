<?php

namespace App\Services;

use App\Models\EtapaContractual;
use Illuminate\Support\Facades\Storage;

class EtapaContractualService
{
    public function store(array $data, ?array $files = null)
    {
        if (isset($data['tipo_contrato']) && $data['tipo_contrato'] === 'Término Indefinido') {
            $data['fecha_fin'] = null;
        }

        $etapa = EtapaContractual::create($data);

        if ($files) {
            foreach ($files as $file) {
                $etapa->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('documentos', 'public'),
                ]);
            }
        }

        return $etapa;
    }

    public function update(EtapaContractual $etapa, array $data, ?array $files = null)
    {
        if (isset($data['tipo_contrato']) && $data['tipo_contrato'] === 'Término Indefinido') {
            $data['fecha_fin'] = null;
        }

        $etapa->update($data);

        if (isset($data['eliminar_documentos'])) {
            foreach ($data['eliminar_documentos'] as $docId) {
                $doc = $etapa->documentos()->find($docId);
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
                $etapa->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('documentos', 'public'),
                ]);
            }
        }

        return $etapa;
    }

    public function delete(EtapaContractual $etapa)
    {
        foreach ($etapa->documentos as $doc) {
            if (Storage::disk('public')->exists($doc->ruta)) {
                Storage::disk('public')->delete($doc->ruta);
            }
            $doc->delete();
        }

        return $etapa->delete();
    }
}
