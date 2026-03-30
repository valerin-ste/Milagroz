<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'nombre_original',
        'ruta',
        'tipo_documento',
        'documentable_id',
        'documentable_type'
    ];

    public function documentable()
    {
        return $this->morphTo();
    }
}