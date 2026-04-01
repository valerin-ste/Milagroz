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
                'text'  => 'text-soft-slate',
                'color' => '#64748b'
            ];
        }

        try {
            $date = Carbon::parse($dateValue)->startOfDay();
            $now  = Carbon::now()->startOfDay();

            // 1. Vencido (Pasado)
            if ($date->isPast() && !$date->isToday()) {
                return [
                    'label' => 'Vencido',
                    'class' => 'bg-soft-red',
                    'icon'  => 'fas fa-times-circle',
                    'text'  => 'text-soft-red',
                    'color' => '#ef4444'
                ];
            }

            // 2. Vence Hoy (Urgente)
            if ($date->isToday()) {
                return [
                    'label' => 'Vence Hoy',
                    'class' => 'bg-soft-orange',
                    'icon'  => 'fas fa-clock',
                    'text'  => 'text-soft-orange',
                    'color' => '#f97316'
                ];
            }

            // 3. Crítico (Próximos 8 días)
            $daysRemaining = $now->diffInDays($date, false);
            if ($daysRemaining <= 8) {
                return [
                    'label' => 'Crítico (8d)',
                    'class' => 'bg-soft-orange-light',
                    'icon'  => 'fas fa-exclamation-circle',
                    'text'  => 'text-soft-orange-light',
                    'color' => '#fb923c'
                ];
            }

            // 4. Por Vencer (Próximos 30 días)
            if ($daysRemaining <= 30) {
                return [
                    'label' => 'Por Vencer',
                    'class' => 'bg-soft-yellow',
                    'icon'  => 'fas fa-exclamation-triangle',
                    'text'  => 'text-soft-yellow',
                    'color' => '#eab308'
                ];
            }

            // 5. Vigente (Más de 30 días)
            return [
                'label' => 'Vigente',
                'class' => 'bg-soft-green',
                'icon'  => 'fas fa-check-circle',
                'text'  => 'text-soft-green',
                'color' => '#10b981'
            ];

        } catch (\Exception $e) {
            return [
                'label' => 'Error de fecha',
                'class' => 'bg-soft-slate',
                'icon'  => 'fas fa-exclamation-circle',
                'text'  => 'text-soft-slate',
                'color' => '#64748b'
            ];
        }
    }
}
