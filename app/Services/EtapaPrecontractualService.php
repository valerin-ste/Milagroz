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
    public function store(array $data, UploadedFile $file): EtapaPrecontractual
    {
        if ($file) {
            $path = $file->store('etapa_precontractual', 'public');
            $data['archivo'] = $path;
        }

        $data['fecha_registro'] = $data['fecha_registro'] ?? now()->toDateString();
        $data['estado'] = $data['estado'] ?? 'en_proceso';

        return EtapaPrecontractual::create($data);
    }

    /**
     * Handle the update of the record, replacing the file if a new one is provided.
     */
    public function update(EtapaPrecontractual $etapaPrecontractual, array $data, ?UploadedFile $file = null): bool
    {
        if ($file) {
            // Delete old file if exists
            if ($etapaPrecontractual->archivo && Storage::disk('public')->exists($etapaPrecontractual->archivo)) {
                Storage::disk('public')->delete($etapaPrecontractual->archivo);
            }

            $path = $file->store('etapa_precontractual', 'public');
            $data['archivo'] = $path;
        }

        return $etapaPrecontractual->update($data);
    }
    
    /**
     * Delete the record and its associated file.
     */
    public function delete(EtapaPrecontractual $etapaPrecontractual): bool
    {
        if ($etapaPrecontractual->archivo && Storage::disk('public')->exists($etapaPrecontractual->archivo)) {
            Storage::disk('public')->delete($etapaPrecontractual->archivo);
        }

        return $etapaPrecontractual->delete();
    }
}
