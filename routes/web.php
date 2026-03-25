<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PersonaController; 
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\EtapaPrecontractualController;
use App\Http\Controllers\Admin\EtapaContractualController;
use App\Http\Controllers\Admin\SeguridadSaludTrabajoController;
use App\Http\Controllers\Admin\ComunicacionController;
use App\Http\Controllers\Admin\EvaluacionDesempenoController;
use App\Http\Controllers\Admin\FormacionController;
use App\Http\Controllers\Admin\DocumentoController;

// Redireccionar raíz a dashboard
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// Grupo admin
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Toggles de estado
    $toggleRoutes = [
        'sedes' => SedeController::class,
        'areas' => AreaController::class,
        'roles' => RoleController::class,
        'empleados' => EmpleadoController::class,
        'etapa_precontractual' => EtapaPrecontractualController::class,
        'etapa_contractual' => EtapaContractualController::class,
        'seguridad_salud_trabajo' => SeguridadSaludTrabajoController::class,
        'evaluaciones_desempeno' => EvaluacionDesempenoController::class,
        'formaciones' => FormacionController::class,
    ];

    foreach ($toggleRoutes as $uri => $controller) {
        Route::post("$uri/{id}/toggle", [$controller, 'toggleStatus'])->name("$uri.toggle");
    }

    // Recursos CRUD
    Route::resource('sedes', SedeController::class);
    Route::resource('areas', AreaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('personas', PersonaController::class);
    Route::resource('empleados', EmpleadoController::class);
    Route::resource('etapa_precontractual', EtapaPrecontractualController::class);
    Route::resource('etapa_contractual', EtapaContractualController::class);
    Route::resource('seguridad_salud_trabajo', SeguridadSaludTrabajoController::class);
    Route::resource('evaluaciones_desempeno', EvaluacionDesempenoController::class);
    Route::resource('formaciones', FormacionController::class);

    // 🔹 Comunicaciones
    Route::resource('comunicaciones', ComunicacionController::class)
        ->parameters(['comunicaciones' => 'comunicacion']);

    // 🔹 Eliminación de archivos individuales de comunicaciones
    Route::delete('comunicaciones/archivo/{id}', [ComunicacionController::class, 'deleteArchivo'])
        ->name('comunicaciones.deleteArchivo');

});