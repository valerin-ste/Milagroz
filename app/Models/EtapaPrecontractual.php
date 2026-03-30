<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtapaPrecontractual extends Model
{
    use HasFactory;

    protected $table = 'etapa_precontractual';
    public $timestamps = false; // El servidor no tiene created_at/updated_at en esta tabla

    protected $fillable = [
        'persona_id',
        'estado',
        'fecha_registro',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentable');
    }
}
