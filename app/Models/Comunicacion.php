<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunicacion extends Model
{
        protected $table = 'comunicaciones'; // 🔥 SOLUCIÓN

        protected $fillable = [
        'empleado_id',
        'asunto',
        'mensaje',
        'archivo',
        'nombre_original', // 🔥 AGREGAR ESTO
        'fecha'
    ];

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class);
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentable');
    }
}