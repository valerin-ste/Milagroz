<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PersonaController; 
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\EtapaPrecontractualController;
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

//Rutas AdmiLTE
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

//Rutas Areas
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('areas', AreaController::class);
});

//Rutas Sedes

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('sedes', SedeController::class);
});

//Rutas Roles   
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleController::class);
});

//Rutas Personas
Route::resource('personas', PersonaController::class)->names('admin.personas');

//Rutas Empleados
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('empleados', EmpleadoController::class);
    Route::resource('etapa_precontractual', EtapaPrecontractualController::class);
});