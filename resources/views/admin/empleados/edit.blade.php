@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Actualizar Empleado</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Modifique la información de {{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}.</p>
    </div>
    <a href="{{ route('admin.empleados.index') }}" class="btn btn-light-custom px-4">
        Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: 12px; border: none; background-color: #fef2f2; color: #991b1b;">
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

    <form action="{{ route('admin.empleados.update', $empleado) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- ================== DATOS PERSONALES ================== --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header pt-4 px-4 pb-3 bg-white border-0">
                        <h5 class="card-title fw-bold" style="color: var(--primary-blue); font-size: 1.1rem;">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px; color: #13b6ec;">
                                </div>
                                Información Personal
                            </div>
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" class="form-control rounded-3" value="{{ old('nombres', $empleado->persona->nombres) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" class="form-control rounded-3" value="{{ old('apellidos', $empleado->persona->apellidos) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-select rounded-3" required>
                                    <option value="CC" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="CE" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="PP" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'PP' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Número Documento <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento" class="form-control rounded-3" value="{{ old('numero_documento', $empleado->persona->numero_documento) }}" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control rounded-3" value="{{ old('fecha_nacimiento', $empleado->persona->fecha_nacimiento) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control rounded-3" value="{{ old('telefono', $empleado->persona->telefono) }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control rounded-3" value="{{ old('correo', $empleado->persona->correo) }}">
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label text-muted small fw-bold">Dirección de Residencia</label>
                                <input type="text" name="direccion" class="form-control rounded-3" value="{{ old('direccion', $empleado->persona->direccion) }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================== DATOS LABORALES ================== --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header pt-4 px-4 pb-3 bg-white border-0">
                        <h5 class="card-title fw-bold" style="color: var(--primary-blue); font-size: 1.1rem;">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px; color: #13b6ec;">
                                </div>
                                Información Laboral
                            </div>
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Área <span class="text-danger">*</span></label>
                                <select name="area_id" class="form-select rounded-3" required>
                                    @foreach($areas as $a)
                                        <option value="{{ $a->id }}" {{ old('area_id', $empleado->area_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Sede <span class="text-danger">*</span></label>
                                <select name="sede_id" class="form-select rounded-3" required>
                                    @foreach($sedes as $s)
                                        <option value="{{ $s->id }}" {{ old('sede_id', $empleado->sede_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Rol del Sistema <span class="text-danger">*</span></label>
                                <select name="rol_id" class="form-select rounded-3" required>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ old('rol_id', $empleado->rol_id) == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Cargo Específico <span class="text-danger">*</span></label>
                                <input type="text" name="cargo" class="form-control rounded-3" value="{{ old('cargo', $empleado->cargo) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Fecha Ingreso <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_ingreso" class="form-control rounded-3" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}" required>
                            </div>

                            <div class="col-12 mt-4">
                                <label class="form-label text-muted small fw-bold d-block mb-3">Estado del Empleado <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="estado" id="estadoActivo" value="1" {{ old('estado', $empleado->estado) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label text-success fw-bold" for="estadoActivo">Activo</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="estado" id="estadoInactivo" value="0" {{ old('estado', $empleado->estado) == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger fw-bold" for="estadoInactivo">Inactivo</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES ACCION --}}
        <div class="d-flex justify-content-end gap-3 mt-4 mb-5 pb-4 px-2">
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-light-custom px-4">
                Cancelar
            </a>
            <button type="submit" class="btn btn-orange px-5 py-2 fw-bold shadow-sm">
                Actualizar Cambios
            </button>
        </div>

    </form>

</div>
@endsection

@push('css')
<style>
    .btn-orange { background-color: #ff6a00; border: none; color: #fff; border-radius: 10px; transition: all 0.2s; }
    .btn-orange:hover { background-color: #e65c00; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,106,0,0.2); }
    
    .btn-light-custom { background-color: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; border-radius: 10px; transition: all 0.2s; font-weight: 500; }
    .btn-light-custom:hover { background-color: #e2e8f0; color: #334155; }

    .form-label { letter-spacing: 0.02em; }
    .form-control:focus, .form-select:focus { border-color: #13b6ec; box-shadow: 0 0 0 0.25rem rgba(19, 182, 236, 0.1); }
    
    .card { border-radius: 15px; }
</style>
@endpush