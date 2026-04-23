<?php

namespace App\Models;

use App\Traits\HasStatusAlerts;
use Illuminate\Database\Eloquent\Model;

class Formacion extends Model
{
    use HasStatusAlerts;
    protected $table = 'formaciones';

    protected $fillable = [
        'empleado_id',
        'nombre_curso',
        'institucion',
        'fecha_inicio',
        'fecha_fin',
        'archivo',
        'estado',
        'vence'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
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
