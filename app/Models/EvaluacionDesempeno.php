<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDesempeno extends Model
{
    protected $table = 'evaluaciones_desempeno';

    protected $fillable = [
        'empleado_id',
        'calificacion',
        'observaciones',
        'archivo',
        'fecha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'calificacion' => 'integer',
        'estado' => 'integer',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentable');
    }
}
