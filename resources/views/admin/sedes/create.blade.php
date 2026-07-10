@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Crear Nueva Sede
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Defina los parámetros de la sede.</p>
    </div>
    <a href="{{ route('admin.sedes.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: var(--radius-md); border: none; background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center border-bottom pb-2 mb-2" style="border-color: #fecaca !important;">
                <i class="fas fa-exclamation-circle fa-lg me-2"></i>
                <strong>Revise los siguientes errores:</strong>
            </div>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sedes.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title d-flex align-items-center" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-hospital"></i>
                            </div>
                            Información de la Sede
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                       value="{{ old('nombre') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control"
                                       value="{{ old('ciudad') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                       value="{{ old('direccion') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="{{ old('telefono') }}">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    Áreas asignadas <span class="text-danger">*</span>
                                </label>

                                <div class="row g-3 border rounded-3 p-3 bg-light">

                                    @foreach(\App\Models\Area::orderBy('nombre')->get() as $area)

                                        <div class="col-md-4">
                                            <div class="form-check border rounded p-2 h-100 bg-white shadow-sm">

                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="areas[]"
                                                    value="{{ $area->id }}"
                                                    id="area{{ $area->id }}"
                                                    {{ in_array($area->id, old('areas', [])) ? 'checked' : '' }}
                                                >

                                                <label class="form-check-label ms-2" for="area{{ $area->id }}">
                                                    {{ $area->nombre }}
                                                </label>

                                            </div>
                                        </div>

                                    @endforeach

                                </div>

                                <small class="text-muted">
                                    Seleccione una o varias áreas que pertenecen a esta sede.
                                </small>

                            </div>

                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.sedes.index') }}"
                                class="btn btn-light border px-4 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                    <i class="fas fa-times me-2"></i> CANCELAR
                            </a>

                            <button type="submit"
                                    class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                    style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                <i class="fas fa-save me-2"></i> GUARDAR SEDE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </form>
</div>
@stop

@push('css')
<style>
/* ── Botones ── */
.btn-orange { background-color: #ff6a00; border: none; color: #fff; border-radius: 8px; transition: all 0.2s; font-weight: 600; }
.btn-orange:hover { background-color: #e65c00; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,106,0,0.25); }
.btn-light-custom { background-color: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; border-radius: 8px; transition: all 0.2s; font-weight: 500; }
.btn-light-custom:hover { background-color: #e2e8f0; color: #334155; }
/* ── Card ── */
.card { border-radius: 1rem !important; }
.card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 1rem 1rem 0 0 !important; }
/* ── Formulario ── */
.form-label { font-weight: 600; color: #475569; font-size: 0.875rem; margin-bottom: 0.4rem; }
.form-control, .form-select { border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; transition: border-color 0.2s, box-shadow 0.2s; }
.form-control:focus, .form-select:focus { border-color: #13b6ec; box-shadow: 0 0 0 3px rgba(19,182,236,0.12); }
</style>
@endpush