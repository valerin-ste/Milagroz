<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-documentos', ['only' => ['view']]);
        $this->middleware('permission:descargar-documentos', ['only' => ['download']]);
        $this->middleware('permission:eliminar-documentos', ['only' => ['destroy']]);
    }
    /**
     * Muestra el documento en el navegador si es posible.
     */
    public function view($id)
    {
        $documento = Documento::findOrFail($id);
        $path = storage_path('app/public/' . $documento->ruta);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado físicamente.');
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['xls', 'xlsx', 'doc', 'docx'])) {
            $publicUrl = Storage::disk('public')->url($documento->ruta);
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
            'Content-Disposition' => 'inline; filename="' . str_replace('"', '\\"', basename($documento->nombre_original)) . '"'
        ];

        if (array_key_exists($ext, $mimeTypes)) {
            $headers['Content-Type'] = $mimeTypes[$ext];
        }

        while (ob_get_level() > 0) ob_end_clean();

        return response()->file($path, $headers);
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
