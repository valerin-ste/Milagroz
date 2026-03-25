<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
    'nombre_original',
    'ruta',
    'tipo_documento',
    'documentable_id',      // 🔥 AGREGAR
    'documentable_type'     // 🔥 AGREGAR
];

    public function documentable()
    {
        return $this->morphTo();
    }
}
