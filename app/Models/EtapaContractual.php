<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaContractual extends Model
{
    protected $table = 'etapa_contractual';
    public $timestamps = false;

    protected $fillable = [
        'empleado_id',
        'tipo_contrato',
        'salario',
        'fecha_inicio',
        'fecha_fin',
        'archivo',
        'estado'
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
