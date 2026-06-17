<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CalidadDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'calidad_documentos';

    protected $fillable = [
        'empleado_id',
        'categoria',
        'nombre_documento',
        'codigo',
        'version',
        'fecha_emision',
        'fecha_vencimiento',
        'archivo',
        'estado',
    ];

    protected $casts = [
        'fecha_emision'    => 'date',
        'fecha_vencimiento'=> 'date',
    ];

    // ──────────────────────────────────────────
    // RELACIONES
    // ──────────────────────────────────────────

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // ──────────────────────────────────────────
    // ACCESSORS — lógica de vencimiento
    // ──────────────────────────────────────────

    /**
     * Devuelve: 'vigente' | 'proximo' | 'vencido' | 'sin_fecha'
     */
    public function getEstadoVencimientoAttribute(): string
    {
        if (!$this->fecha_vencimiento) {
            return 'sin_fecha';
        }

        $hoy  = Carbon::today();
        $vence = Carbon::parse($this->fecha_vencimiento);

        if ($vence->isPast()) {
            return 'vencido';
        }

        if ($vence->diffInDays($hoy) <= 30) {
            return 'proximo';
        }

        return 'vigente';
    }

    /**
     * Color Bootstrap del badge de vencimiento
     */
    public function getColorVencimientoAttribute(): string
    {
        return match ($this->estado_vencimiento) {
            'vencido'   => 'danger',
            'proximo'   => 'warning',
            'vigente'   => 'success',
            default     => 'secondary',
        };
    }

    /**
     * Extensión del archivo adjunto
     */
    public function getExtensionArchivoAttribute(): string
    {
        if (!$this->archivo) return '';
        return strtolower(pathinfo($this->archivo, PATHINFO_EXTENSION));
    }

    /**
     * Nombre original del archivo (último segmento de la ruta)
     */
    public function getNombreArchivoAttribute(): string
    {
        if (!$this->archivo) return '';
        return basename($this->archivo);
    }
}
