@extends('adminlte::page')

@section('content')
<h3>Nueva Comunicación</h3>

<form action="{{ route('admin.comunicaciones.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Empleado</label>
        <select name="empleado_id" class="form-control" required>
            <option value="">Seleccione</option>
            @foreach($empleados as $emp)
                <option value="{{ $emp->id }}">
                    {{ $emp->persona->nombres }} {{ $emp->persona->apellidos }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Asunto</label>
        <input type="text" name="asunto" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Mensaje</label>
        <textarea name="mensaje" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label>Fecha</label>
        <input type="date" name="fecha" class="form-control" required>
    </div>

    {{-- 🔹 ZONA DRAG & DROP --}}
    <div class="mb-3">
        <label>Anexar documentos</label>
        <div id="drop-area" class="border border-primary rounded p-4 text-center" style="cursor:pointer;">
            <p class="mb-2">📂 Arrastra archivos aquí o haz clic</p>
            <small class="text-muted">Puedes subir varios archivos</small>
            <input type="file" name="archivos[]" id="fileElem" multiple hidden>
        </div>

        {{-- Vista previa --}}
        <div id="preview" class="mt-3"></div>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
</form>
@endsection

@section('js')
<script>
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('fileElem');
const preview = document.getElementById('preview');

// abrir selector
dropArea.addEventListener('click', () => fileInput.click());

// mostrar archivos seleccionados
fileInput.addEventListener('change', () => {
    mostrarArchivos(fileInput.files);
});

// drag
dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('bg-light');
});

dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('bg-light');
});

// drop
dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('bg-light');

    fileInput.files = e.dataTransfer.files; // ⚠️ reemplaza los archivos
    mostrarArchivos(fileInput.files);
});

// mostrar nombres
function mostrarArchivos(files) {
    preview.innerHTML = '';

    Array.from(files).forEach(file => {
        let div = document.createElement('div');
        div.classList.add('border', 'p-2', 'mb-2', 'rounded');
        div.innerHTML = `📎 ${file.name}`;
        preview.appendChild(div);
    });
}
</script>
@endsection