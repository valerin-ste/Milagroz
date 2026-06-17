<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReporteNovedadNomina extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reportes_novedades_nomina';

    protected $fillable = [
        'empleado_id',
        'tipo_novedad',
        'cantidad',
        'observaciones',
        'archivo',
        'fecha',
        'estado',
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
