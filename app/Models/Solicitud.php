<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    
    protected $fillable = [
        'empleado_id',
        'tipo',
        'descripcion',
        'estado',
        'fecha',
        'archivo',
        'activo',
        'nombre_archivo'
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