<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /**
     * Eliminar un archivo individual del storage y de la base de datos.
     */
    public function destroy($id)
    {
        try {
            $documento = Documento::findOrFail($id);

            // Eliminar archivo del storage físico
            if (Storage::disk('public')->exists($documento->ruta)) {
                Storage::disk('public')->delete($documento->ruta);
            }

            // Eliminar registro de la DB
            $documento->delete();

            return back()->with('success', 'Archivo eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }
}
