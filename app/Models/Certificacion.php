<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasStatusAlerts;

class Certificacion extends Model
{
    use SoftDeletes, HasStatusAlerts;

    protected $table = 'certificaciones';

        protected $fillable = [
        'nombre_certificacion',
        'tipo_certificacion',
        'institucion',
        'codigo_certificado',
        'fecha_expedicion',
        'fecha_vencimiento',
        'observaciones',
        'estado',   
        'archivo'
    ];

    protected $casts = [
        'fecha_expedicion' => 'date',
        'fecha_vencimiento' => 'date',
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
