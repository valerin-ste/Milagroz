<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Models\Formacion;
use Carbon\Carbon;

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
        // 🔍 AUDITORÍA: Observador para Roles de Sistema (Spatie)
        \Spatie\Permission\Models\Role::observe(\App\Observers\SystemRoleObserver::class);

        // Inyectar alertas en la navbar globalmente (campana de notificaciones)
        \Illuminate\Support\Facades\View::composer('adminlte::partials.navbar.navbar', function($view) {
            $now       = \Carbon\Carbon::now();
            $today     = $now->copy()->startOfDay();
            $eightDays = $now->copy()->addDays(8)->endOfDay();
            $thirtyDays= $now->copy()->addDays(30)->endOfDay();

            $alertasCount = [
                'contratos_vencidos'    => \App\Models\EtapaContractual::where('fecha_fin', '<=', $today)->where('estado', 1)->count(),
                'contratos_criticos'    => \App\Models\EtapaContractual::whereBetween('fecha_fin', [$today->copy()->addDay(), $eightDays])->where('estado', 1)->count(),
                'contratos_por_vencer'  => \App\Models\EtapaContractual::whereBetween('fecha_fin', [$eightDays->copy()->addDay(), $thirtyDays])->where('estado', 1)->count(),
                'sst_vencidos'          => \App\Models\SeguridadSaludTrabajo::where('fecha', '<=', $today)->where('estado', 1)->count(),
                'sst_criticos'          => \App\Models\SeguridadSaludTrabajo::whereBetween('fecha', [$today->copy()->addDay(), $eightDays])->where('estado', 1)->count(),
                'solicitudes_pendientes'=> \App\Models\Solicitud::where('estado', 'Pendiente')->count(),
                'evaluaciones_pendientes'=> \App\Models\EvaluacionDesempeno::where('estado', 0)->count(),
                'formaciones_vencidas'  => \App\Models\Formacion::where('vence', 1)->where('estado', 1)->where('fecha_fin', '<=', $today)->count(),
            ];
            $view->with('alertasCount', $alertasCount);
        });

        // 🔒 AUDITORÍA: Eventos de Autenticación
        Event::listen([
            \Illuminate\Auth\Events\Login::class,
            \Illuminate\Auth\Events\Logout::class,
            \Illuminate\Auth\Events\PasswordReset::class,
            \Illuminate\Auth\Events\Failed::class,
        ], \App\Listeners\AuditAuthListener::class);

        // 🛡️ INYECTAR BADGE DINÁMICO EN EL MENÚ (FORMACIÓN)
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $hoy = Carbon::now()->startOfDay();
            $limite = Carbon::now()->addDays(30)->endOfDay();
            
            $count = Formacion::where('vence', 1)
                ->where('estado', 1)
                ->where('fecha_fin', '<=', $limite)
                ->count();

            $event->menu->addAfter('formaciones_general', [
                'key'         => 'formaciones_vencimientos',
                'text'        => 'Cursos con Vencimiento',
                'url'         => 'admin/formaciones/vencimientos',
                'icon'        => 'fas fa-clock',
                'label'       => ($count > 0) ? $count : null,
                'label_color' => 'danger',
            ]);
        });
    }
}
