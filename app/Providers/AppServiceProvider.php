<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inyectar alertas en la navbar globalmente (campana de notificaciones)
        \Illuminate\Support\Facades\View::composer('adminlte::partials.navbar.navbar', function($view) {
            $now       = \Carbon\Carbon::now();
            $today     = $now->copy()->startOfDay();
            $eightDays = $now->copy()->addDays(8)->endOfDay();
            $thirtyDays= $now->copy()->addDays(30)->endOfDay();

            $alertas = [
                'contratos_vencidos'    => \App\Models\EtapaContractual::where('fecha_fin', '<=', $today)->where('estado', 1)->get(),
                'contratos_criticos'    => \App\Models\EtapaContractual::whereBetween('fecha_fin', [$today->copy()->addDay(), $eightDays])->where('estado', 1)->get(),
                'contratos_por_vencer'  => \App\Models\EtapaContractual::whereBetween('fecha_fin', [$eightDays->copy()->addDay(), $thirtyDays])->where('estado', 1)->get(),
                'sst_vencidos'          => \App\Models\SeguridadSaludTrabajo::where('fecha', '<=', $today)->where('estado', 1)->get(),
                'sst_criticos'          => \App\Models\SeguridadSaludTrabajo::whereBetween('fecha', [$today->copy()->addDay(), $eightDays])->where('estado', 1)->get(),
                'solicitudes_pendientes'=> \App\Models\Solicitud::where('estado', 'Pendiente')->count(),
                'evaluaciones_pendientes'=> \App\Models\EvaluacionDesempeno::where('estado', 0)->count(),
            ];
            $view->with('alertas', $alertas);
        });
    }
}
