@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Crear Nuevo Usuario
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Registre una nueva cuenta de acceso al sistema.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light-custom px-4">
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

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title d-flex align-items-center" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            Datos del Usuario
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">

                            <div class="col-12">
                                <label class="form-label">Vincular con Persona/Empleado <span class="text-muted">(Opcional)</span></label>
                                <select name="persona_id" id="persona_id" class="form-select select2 @error('persona_id') is-invalid @enderror">
                                    <option value="">-- Usuario sin vinculación laboral --</option>
                                    @foreach($personas as $persona)
                                        @php
                                            $suggestedRoles = $persona->empleado?->rol?->systemRoles->pluck('name')->toArray() ?? [];
                                        @endphp
                                        <option value="{{ $persona->id }}" 
                                                data-nombre="{{ $persona->full_name }}" 
                                                data-email="{{ $persona->correo }}"
                                                data-roles="{{ json_encode($suggestedRoles) }}"
                                                {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->full_name }} ({{ $persona->numero_documento }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('persona_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                <p class="text-muted small mt-1 mb-0">Si selecciona una persona, el nombre y correo se completarán automáticamente.</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name" value="{{ old('name') }}" required>
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email" value="{{ old('email') }}" required>
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password" required>
                                @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" id="password_confirmation" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Roles de Sistema</label>
                                <p class="text-muted small mb-3">Seleccione los roles que tendrá este usuario.</p>
                                <div class="row g-2">
                                    @foreach($roles as $role)
                                        <div class="col-md-4">
                                            <div class="role-check-card {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}">
                                                <input class="form-check-input" type="checkbox"
                                                       id="role_{{ $role->id }}" name="roles[]"
                                                       value="{{ $role->name }}"
                                                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                                                       onchange="this.closest('.role-check-card').classList.toggle('selected', this.checked)">
                                                <label for="role_{{ $role->id }}" class="form-check-label ms-2 fw-semibold">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 px-4 pb-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}"
                            class="btn btn-light border px-4 fw-bold shadow-sm"
                            style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                <i class="fas fa-times me-2"></i> CANCELAR
                            </a>

                            <button type="submit"
                                    class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                    style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                <i class="fas fa-save me-2"></i> GUARDAR USUARIO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@stop

@push('js')
<script>
$(document).ready(function() {
    $('#persona_id').on('change', function() {
        var selected = $(this).find(':selected');
        var nombre = selected.data('nombre');
        var email = selected.data('email');
        var roles = selected.data('roles'); // Es un array
        
        if (nombre) {
            $('#name').val(nombre);
        }
        if (email) {
            $('#email').val(email);
        }

        // Limpiar roles actuales si se selecciona una persona nueva
        if (roles) {
            $('input[name="roles[]"]').prop('checked', false).closest('.role-check-card').removeClass('selected');
            
            roles.forEach(function(roleName) {
                var checkbox = $('input[name="roles[]"][value="' + roleName + '"]');
                checkbox.prop('checked', true).closest('.role-check-card').addClass('selected');
            });
        }
    });
});
</script>
@endpush

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
/* ── Role Cards ── */
.role-check-card { display: flex; align-items: center; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; cursor: pointer; transition: all 0.2s; }
.role-check-card:hover { border-color: #13b6ec; background: #f0f9ff; }
.role-check-card.selected { border-color: #13b6ec; background: #e0f2fe; }
</style>
@endpush
