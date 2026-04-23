<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /**
     * Muestra el documento en el navegador si es posible.
     */
    public function view($id)
    {
        // Limpiamos cualquier buffer previo para evitar que corrompa las cabeceras
        while (ob_get_level() > 0) ob_end_clean();

        $documento = Documento::findOrFail($id);
        $path = storage_path('app/public/' . $documento->ruta);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado físicamente.');
        }

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . str_replace('"', '\\"', basename($documento->nombre_original)) . '"'
        ]);
    }

    /**
     * Fuerza la descarga del documento.
     */
    public function download($id)
    {
        $documento = Documento::findOrFail($id);
        $path = storage_path('app/public/' . $documento->ruta);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path, $documento->nombre_original);
    }

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
