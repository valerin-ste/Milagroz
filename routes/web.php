<?php

use Illuminate\Support\Facades\Route;

// Controllers Admin
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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemRoleController;
use App\Http\Controllers\Admin\FechaEspecialController;
use App\Http\Controllers\Admin\CertificacionController;
use App\Http\Controllers\Admin\DotacionController;
use App\Http\Controllers\Admin\ProductividadController;
use App\Http\Controllers\Admin\CalidadDocumentoController;
use App\Http\Controllers\Admin\CapacidadInstaladaController;
use App\Http\Controllers\Admin\ReporteNovedadNominaController;
use App\Http\Controllers\Admin\PlantaPersonalSenaController;

// 👇 Controller fuera de Admin
use App\Http\Controllers\SolicitudController;


// ----------------------
// 🔹 REDIRECCIÓN RAÍZ
// ----------------------
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// ----------------------
// 🔹 AUTHENTICATION (NO REGISTRO PÚBLICO)
// ----------------------
Auth::routes(['register' => false]);

// ----------------------
// 🔹 GRUPO ADMIN (PROTEGIDO)
// ----------------------
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {

    // ======================
    // 🔥 DASHBOARD
    // ======================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // ======================
    // 🔥 TOGGLES (UNIFICADOS 🔥)
    // ======================
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
        'users' => UserController::class,
        'system_roles' => SystemRoleController::class,
        'fechas_especiales' => FechaEspecialController::class,
        'certificaciones' => CertificacionController::class,
        'dotaciones' => DotacionController::class,
        'productividades'      => ProductividadController::class,
        'calidad_documentos'  => CalidadDocumentoController::class,
        'capacidad_instalada' => CapacidadInstaladaController::class,
        'reportes-novedades-nomina' => ReporteNovedadNominaController::class,
        'planta_personal_sena' => PlantaPersonalSenaController::class,
    ];

    foreach ($toggleRoutes as $uri => $controller) {
        Route::match(['post', 'patch'], "$uri/{id}/toggle", [$controller, 'toggleStatus'])
            ->name("$uri.toggle");
    }


    // ======================
    // 🔥 SOLICITUDES (TOGGLE 🔥)
    // ======================
    Route::match(['post', 'patch'], 'solicitudes/{id}/toggle', [SolicitudController::class, 'toggle'])
        ->name('solicitudes.toggle');

    Route::get('solicitudes/{id}/archivo/view', [SolicitudController::class, 'viewArchivo'])
        ->name('solicitudes.archivo.view');

    Route::get('solicitudes/{id}/archivo/download', [SolicitudController::class, 'downloadArchivo'])
        ->name('solicitudes.archivo.download');


    // ======================
    // 🔥 CAMBIAR ESTADO SOLICITUD
    // ======================
    Route::post('solicitudes/{id}/estado', [SolicitudController::class, 'cambiarEstado'])
        ->name('solicitudes.estado');


    // ======================
    // 🔥 CRUD PRINCIPAL
    // ======================
    Route::resource('sedes', SedeController::class);
    Route::resource('areas', AreaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('personas', PersonaController::class);
    Route::resource('empleados', EmpleadoController::class);
    Route::resource('etapa_precontractual', EtapaPrecontractualController::class);
    Route::resource('etapa_contractual', EtapaContractualController::class);
    Route::resource('seguridad_salud_trabajo', SeguridadSaludTrabajoController::class);
    Route::resource('evaluaciones_desempeno', EvaluacionDesempenoController::class);
    Route::get('formaciones/vencimientos', [FormacionController::class, 'vencimientos'])->name('formaciones.vencimientos');
    Route::resource('formaciones', FormacionController::class);

    Route::resource('comunicaciones', ComunicacionController::class)
        ->parameters([
            'comunicaciones' => 'comunicacion'
        ]);

    Route::resource('solicitudes', SolicitudController::class)
        ->parameters([
            'solicitudes' => 'solicitud'
        ]);

    Route::resource('fechas_especiales', FechaEspecialController::class);
    Route::resource('certificaciones', CertificacionController::class);
    Route::resource('dotaciones', DotacionController::class);
    Route::resource('productividades', ProductividadController::class);
    Route::resource('capacidad_instalada', CapacidadInstaladaController::class);
    Route::resource('reportes-novedades-nomina', ReporteNovedadNominaController::class);
    Route::resource('planta_personal_sena', PlantaPersonalSenaController::class);
    
    Route::get('reportes-novedades-nomina/{id}/archivo/view', [ReporteNovedadNominaController::class, 'viewArchivo'])->name('reportes-novedades-nomina.archivo.view');
    Route::get('reportes-novedades-nomina/{id}/archivo/download', [ReporteNovedadNominaController::class, 'downloadArchivo'])->name('reportes-novedades-nomina.archivo.download');

    Route::get('productividades/{id}/archivo/view', [ProductividadController::class, 'viewArchivo'])->name('productividades.archivo.view');
    Route::get('productividades/{id}/archivo/download', [ProductividadController::class, 'downloadArchivo'])->name('productividades.archivo.download');

    // ======================
    // 🔥 CALIDAD DOCUMENTOS
    // ======================
    Route::resource('calidad_documentos', CalidadDocumentoController::class);

    Route::get('calidad_documentos/{id}/archivo/view', [CalidadDocumentoController::class, 'viewArchivo'])
        ->name('calidad_documentos.archivo.view');
    Route::get('calidad_documentos/{id}/archivo/download', [CalidadDocumentoController::class, 'downloadArchivo'])
        ->name('calidad_documentos.archivo.download');
    

    // ======================
    // 🔥 DOCUMENTOS
    // ======================
    Route::get('documentos/{id}/view', [DocumentoController::class, 'view'])->name('documentos.view');
    Route::get('test-pdf', [DocumentoController::class, 'testPdf']); // Ruta de prueba
    Route::get('documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::resource('documentos', DocumentoController::class)->only(['destroy']);

    // ======================
    // 🔥 EXPORTACIÓN DE REPORTES (EMPLEADOS)
    // ======================
    Route::get('empleados/reporte/pdf', [EmpleadoController::class, 'exportPdf'])->name('empleados.reporte.pdf');
    Route::get('empleados/{id}/file-view/{path}', [EmpleadoController::class, 'viewFile'])->name('empleados.file.view')->where('path', '.*');
    Route::get('empleados/{id}/file-download/{path}', [EmpleadoController::class, 'downloadFile'])->name('empleados.file.download')->where('path', '.*');

    // ======================
    // 🔥 CONFIGURACIÓN / ACCESOS (SEGURIDAD)
    // ======================
    Route::resource('users', UserController::class);
    Route::resource('system_roles', SystemRoleController::class);

});
