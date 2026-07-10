@extends('adminlte::page')

@section('title', 'Acceso Denegado')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 shadow-lg bg-white rounded-4" style="max-width: 600px; border-top: 5px solid #ef4444;">
        <div class="mb-4">
            <i class="fas fa-shield-alt fa-5x text-danger animate__animated animate__pulse animate__infinite"></i>
        </div>
        <h1 class="fw-bold text-dark mb-2" style="letter-spacing: -1px;">Acceso Restringido</h1>
        <p class="text-muted mb-4" style="font-size: 1.1rem;">
            Lo sentimos, pero <strong>no tienes los permisos suficientes</strong> para acceder a este recurso o realizar esta acción.
        </p>
        <div class="alert alert-light border-0 py-3 mb-4" style="background-color: #fef2f2; color: #b91c1c; border-radius: 12px;">
            <i class="fas fa-info-circle me-2"></i> Si crees que esto es un error, contacta al administrador del sistema.
        </div>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Volver atrás
            </a>

            @if(auth()->user()->hasRole('Empleado'))
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-danger px-4 rounded-pill shadow-sm">
                    <i class="fas fa-file-alt me-2"></i> Ir a Mis Solicitudes
                </a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger px-4 rounded-pill shadow-sm">
                    <i class="fas fa-home me-2"></i> Ir al Dashboard
                </a>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .rounded-4 { border-radius: 20px !important; }
    .bg-white { background-color: #ffffff !important; }
</style>
@stop
