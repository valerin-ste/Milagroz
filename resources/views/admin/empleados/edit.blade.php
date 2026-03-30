@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Actualizar Empleado</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Modifique la información de {{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}.</p>
    </div>
    <a href="{{ route('admin.empleados.index') }}" class="btn btn-light-custom">
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

    <form action="{{ route('admin.empleados.update', $empleado) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- ================== DATOS PERSONALES ================== --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            Información Personal
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $empleado->persona->nombres) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $empleado->persona->apellidos) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="CC" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="CE" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="PP" {{ old('tipo_documento', $empleado->persona->tipo_documento) == 'PP' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Número Documento <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento', $empleado->persona->numero_documento) }}" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $empleado->persona->fecha_nacimiento) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $empleado->persona->telefono) }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="{{ old('correo', $empleado->persona->correo) }}">
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">Dirección de Residencia</label>
                                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $empleado->persona->direccion) }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================== DATOS LABORALES ================== --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            Información Laboral
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Área <span class="text-danger">*</span></label>
                                <select name="area_id" class="form-select" required>
                                    @foreach($areas as $a)
                                        <option value="{{ $a->id }}" {{ old('area_id', $empleado->area_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sede <span class="text-danger">*</span></label>
                                <select name="sede_id" class="form-select" required>
                                    @foreach($sedes as $s)
                                        <option value="{{ $s->id }}" {{ old('sede_id', $empleado->sede_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                                <select name="rol_id" class="form-select" required>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ old('rol_id', $empleado->rol_id) == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cargo Específico <span class="text-danger">*</span></label>
                                <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $empleado->cargo) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Ingreso <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}" required>
                            </div>

                            <div class="col-12 mt-4">
                                <label class="form-label d-block mb-3">Estado del Empleado <span class="text-danger">*</span></label>
                                <div class="custom-radio-group">
                                    <input type="radio" name="estado" id="estadoActivo" class="custom-radio-input" value="1" {{ old('estado', $empleado->estado) == '1' ? 'checked' : '' }}>
                                    <label for="estadoActivo" class="custom-radio-label active-success">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </label>

                                    <input type="radio" name="estado" id="estadoInactivo" class="custom-radio-input" value="0" {{ old('estado', $empleado->estado) == '0' ? 'checked' : '' }}>
                                    <label for="estadoInactivo" class="custom-radio-label active-danger">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </label>
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
            <button type="submit" class="btn btn-orange px-5">
                <i class="fas fa-sync-alt me-2 pb-1"></i> Actualizar Cambios
            </button>
        </div>

    </form>

</div>
@endsection