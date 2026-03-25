<?php

namespace App\Services;

use App\Models\SeguridadSaludTrabajo;
use Illuminate\Support\Facades\Storage;

class SeguridadSaludTrabajoService
{
    public function store(array $data, ?array $files = null)
    {
        $registro = SeguridadSaludTrabajo::create($data);

        if ($files) {
            foreach ($files as $file) {
                $registro->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('seguridad_salud_trabajo', 'public'),
                    'tipo_documento' => $registro->tipo_documento,
                ]);
            }
        }

        return $registro;
    }

    public function update(SeguridadSaludTrabajo $registro, array $data, ?array $files = null)
    {
        $registro->update($data);

        // Opcional: Eliminar documentos específicos si el usuario lo pide en el futuro
        if (isset($data['eliminar_documentos'])) {
            foreach ($data['eliminar_documentos'] as $docId) {
                $doc = $registro->documentos()->find($docId);
                if ($doc) {
                    if (Storage::disk('public')->exists($doc->ruta)) {
                        Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        if ($files) {
            foreach ($files as $file) {
                $registro->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $file->store('seguridad_salud_trabajo', 'public'),
                    'tipo_documento' => $registro->tipo_documento,
                ]);
            }
        }

        return $registro;
    }

    public function delete(SeguridadSaludTrabajo $registro)
    {
        foreach ($registro->documentos as $doc) {
            if (Storage::disk('public')->exists($doc->ruta)) {
                Storage::disk('public')->delete($doc->ruta);
            }
            $doc->delete();
        }

        return $registro->delete();
    }
}
