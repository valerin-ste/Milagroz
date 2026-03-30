<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasStatusAlerts
{
    /**
     * Calcula el estado de vencimiento basado en una columna de fecha.
     * 
     * @param string|Carbon|null $dateValue
     * @return array
     */
    public function getStatusBadge($dateValue)
    {
        if (!$dateValue) {
            return [
                'label' => 'Indefinido',
                'class' => 'bg-soft-slate',
                'icon'  => 'fas fa-question-circle',
                'text'  => 'text-soft-slate'
            ];
        }

        try {
            $date = Carbon::parse($dateValue);
            $now  = Carbon::now();

            // 1. Vencido (Pasado)
            if ($date->isPast() && !$date->isToday()) {
                return [
                    'label' => 'Vencido',
                    'class' => 'bg-soft-red',
                    'icon'  => 'fas fa-times-circle',
                    'text'  => 'text-soft-red'
                ];
            }

            // 2. Por Vencer (Próximos 30 días)
            if ($date->diffInDays($now) <= 30) {
                return [
                    'label' => 'Por Vencer',
                    'class' => 'bg-soft-yellow',
                    'icon'  => 'fas fa-exclamation-triangle',
                    'text'  => 'text-soft-yellow'
                ];
            }

            // 3. Vigente (Más de 30 días)
            return [
                'label' => 'Vigente',
                'class' => 'bg-soft-green',
                'icon'  => 'fas fa-check-circle',
                'text'  => 'text-soft-green'
            ];

        } catch (\Exception $e) {
            return [
                'label' => 'Error de fecha',
                'class' => 'bg-soft-slate',
                'icon'  => 'fas fa-exclamation-circle',
                'text'  => 'text-soft-slate'
            ];
        }
    }
}
