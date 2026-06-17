<?php

namespace App\Models;

use App\Traits\HasStatusAlerts;
use Illuminate\Database\Eloquent\Model;

class EvaluacionDesempeno extends Model
{
    use HasStatusAlerts;
    protected $table = 'evaluaciones_desempeno';

    protected $fillable = [
        'empleado_id',
        'observaciones',
        'archivo',
        'fecha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
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
