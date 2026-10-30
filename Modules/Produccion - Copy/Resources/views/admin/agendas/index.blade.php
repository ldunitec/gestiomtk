@extends('adminlte::page')
@section('title', 'Agenda Semanal')
@section('content_header')
    <div class="agenda-heading">
        <div><span class="agenda-eyebrow">PLANIFICACIÓN DEL EQUIPO</span><h1 class="m-0">Agenda semanal</h1><p class="text-muted mb-0 mt-1">Organiza las actividades y da seguimiento al trabajo de tu equipo.</p></div>
        <div class="agenda-actions"><button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#importExcelModal"><i class="fas fa-file-excel mr-2" aria-hidden="true"></i>Importar Excel</button><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEvento"><i class="fas fa-plus mr-2" aria-hidden="true"></i>Nueva actividad</button></div>
    </div>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row agenda-layout">
        <!-- Columna Izquierda: Tareas Sin Agendar -->
        <div class="col-xl-3 col-lg-4">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-tasks mr-1"></i> Sin agendar <span id="pending-count" class="badge badge-primary ml-2">{{ count($unassignedEvents) }}</span>
                    </h3>
                    {{-- abrir modal importar excel --}}
                    {{-- //////// --}}
                    <!-- Modal de Importación -->
                    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="importExcelModalLabel">
                                        <i class="fas fa-file-import mr-1"></i> Importar Agendas desde Excel
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form id="formImportExcel" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="excelFile" class="form-label font-weight-bold">Selecciona el archivo (.xlsx,
                                                .xls, .csv)</label>
                                            <input type="file" class="form-control" id="excelFile" name="file"
                                                accept=".xlsx, .xls, .csv" required>
                                            <div class="form-text mt-2">
                                                <small class="text-muted">
                                                    * Se validará la <strong>Secuencia ID del ticket</strong> para omitir
                                                    registros duplicados.<br>
                                                    * Se mapearán automáticamente los usuarios asignados (Kevyn, Celvyn,
                                                    Luis, Harling).
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Spinner de Carga -->
                                        <div id="importLoading" class="text-center d-none my-3">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="sr-only">Procesando...</span>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Procesando registros en lotes, por favor
                                                espera...</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success" id="btnSubmitImport">
                                            <i class="fas fa-upload mr-1"></i> Subir e Importar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- //////// --}}
                </div>
                <div class="card-body p-3">
                    <label for="task-search" class="sr-only">Buscar tareas sin agendar</label>
                    <div class="agenda-search mb-2"><i class="fas fa-search text-muted" aria-hidden="true"></i><input id="task-search" type="search" class="form-control" placeholder="Buscar por título o ticket…"></div>
                    <label for="task-type" class="sr-only">Filtrar tareas por tipo</label>
                    <select id="task-type" class="custom-select mb-2"><option value="">Todos los tipos</option><option>Interno</option><option>Taller</option><option>Visita</option><option>Llamada</option></select>
                    <p class="small text-muted mb-3">Arrastra una tarea al día que deseas asignar.</p>
                    <div id="external-events" class="agenda-task-list">
                        @forelse ($unassignedEvents as $evento)
                            <div class="external-event d-flex justify-content-between align-items-start"
                                style="border-left-color: {{ $evento->color ?? '#6c757d' }};"
                                data-id="{{ $evento->id }}" data-titulo="{{ $evento->titulo }}"
                                data-tipo="{{ $evento->tipo ?? 'Llamada' }}" data-estado="{{ $evento->estado }}">
                                <div>
                                    <span class="badge badge-light mr-1">#{{ $evento->id }}</span>
                                    <strong class="agenda-task-title">{{ $evento->titulo }}</strong><span class="agenda-task-type">{{ $evento->tipo ?? 'Llamada' }}</span>
                                </div>
                                <i class="fas fa-grip-lines text-muted ml-2 mt-1"></i>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No hay tareas pendientes por agendar</p>
                            </div>
                        @endforelse
                    </div>
                    <p id="task-no-results" class="text-muted text-center py-3 d-none" role="status">No hay tareas que coincidan con tu búsqueda.</p>
                    <small id="task-results" class="text-muted" aria-live="polite"></small>
                </div>
            </div>
        </div>
        <!-- Columna Derecha: Calendario -->
        <div class="col-xl-9 col-lg-8">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header agenda-calendar-header"><h3 class="card-title font-weight-bold">Calendario de actividades</h3><div class="agenda-legend"><span><i style="background:#3788d8"></i>Interno</span><span><i style="background:#28a745"></i>Taller</span><span><i style="background:#ffc107"></i>Visita</span><span><i style="background:#6c757d"></i>Llamada</span></div></div>
                <div id="calendar-status" class="px-3 pt-2 small text-muted" role="status" aria-live="polite"></div>
                <div class="card-body p-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: Nueva Tarea -->
    <div class="modal fade" id="modalEvento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-plus mr-2"></i>Agregar Actividad</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="evt-nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="evt-nombre" class="form-control"
                                placeholder="Nombre de la actividad">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="evt-contacto">Contacto</label>
                            <input type="text" value="Cliente" id="evt-contacto" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="evt-telefono">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" value="0000-0000" id="evt-telefono" class="form-control" inputmode="tel"
                                placeholder="Teléfono">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="evt-servicio">Servicio Solicitado</label>
                            <input type="text" id="evt-servicio" class="form-control" placeholder="Servicio">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="new-event-type">Tipo de Actividad</label>
                            <select id="new-event-type" class="form-control custom-select">
                                <option value="Interno">Interno</option>
                                <option value="Taller">Taller</option>
                                <option value="Visita">Visita</option>
                                <option value="Llamada" selected>Llamada</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="evt-proyecto">Proyecto</label>
                            <select id="evt-proyecto" class="form-control custom-select">
                                <option value="6CrcqJQWf66RGq3R">Soporte</option>
                                {{-- <option value="6CrcqJQWVH88MRcJ">Soporte Megatk</option>*
*                                <option value="6Qq9F3FC9FCQMrcj">Cumpleaños</option> --}}
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="evt-usuarios">Asignado a <span class="text-danger">*</span></label>
                            <select id="evt-usuarios" class="form-control custom-select">
                                <option value="">-- Seleccionar Responsable --</option>
                                <option value="todos">👥 Todos (Equipo Completo)</option>
                                <option value="8503605">Luis Diaz</option>
                                <option value="10128774">Celvyn Lozano</option>
                                <option value="52565829">Harling Maradiaga</option>
                                <option value="49411725">Kevin Godoy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="add-event-btn" class="btn btn-primary"><i class="fas fa-save mr-1"></i>
                        Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: Editar Evento Agendado -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Editar Evento </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-event-id">
                    <div class="form-group mb-3">
                        <label for="edit-event-title">Título</label>
                        <input type="text" id="edit-event-title" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit-event-fecha_inicio">Fecha</label>
                        <input type="date" id="edit-event-fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit-event-type">Categoría</label>
                        <select id="edit-event-type" class="form-control custom-select">
                            <option value="Interno">Interno</option>
                            <option value="Taller">Taller</option>
                            <option value="Visita">Visita</option>
                            <option value="Llamada">Llamada</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit-event-usuario">Asignado a</label>
                        <select id="edit-event-usuario" class="form-control custom-select">
                            <option value="">-- Seleccionar Responsable --</option>
                            <option value="todos">Todos (Equipo Completo)</option>
                            <option value="8503605">Luis Diaz</option>
                            <option value="10128774">Celvyn Lozano</option>
                            <option value="52565829">Harling Maradiaga</option>
                            <option value="49411725">Kevin Godoy</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit-event-completado"
                            name="completado" value="2">
                        <label class="form-check-label font-weight-bold" for="edit-event-completado">
                            Marcar como completado
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" id="delete-event-btn" class="btn btn-outline-danger" disabled title="El archivo original no incluye la acción de eliminación"><i
                            class="fas fa-trash mr-1"></i> Eliminar</button>
                    <div>
                        <button type="button" id="save-event-btn" class="btn btn-secondary mr-1">Completar</button>
                        <button type="button" id="edit-event-btn" class="btn btn-success"><i
                                class="fas fa-sync-alt mr-1"></i> Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        .content-wrapper { background: #f3f6fa; }
        .agenda-heading { display:flex; justify-content:space-between; align-items:center; gap:20px; padding:12px 0 18px; }
        .agenda-heading h1 { font-size:28px; font-weight:700; color:#172b4d; }
        .agenda-eyebrow { display:block; font-size:11px; font-weight:700; letter-spacing:1.5px; color:#61738c; margin-bottom:6px; }
        .agenda-actions { display:flex; gap:10px; flex-wrap:wrap; }
        .agenda-actions .btn { border-radius:8px; padding:10px 16px; font-weight:600; }
        .agenda-layout .card { border:1px solid #e4eaf2; border-radius:12px; box-shadow:0 4px 18px rgba(27,46,75,.04)!important; overflow:hidden; }
        .agenda-layout .card-header { background:#fff; border-bottom:1px solid #edf1f6; padding:18px; }
        .agenda-layout .card-title { font-size:16px; color:#243852; }
        .agenda-search { position:relative; }
        .agenda-search i { position:absolute; left:12px; top:13px; }
        .agenda-search input { padding-left:34px; }
        .agenda-task-list { min-height:250px; max-height:620px; overflow-y:auto; padding:2px; }
        .external-event { background:#fff; border:1px solid #e5eaf0; border-left:4px solid #6c757d; border-radius:8px; padding:12px; margin-bottom:10px; color:#263b55; cursor:grab; user-select:none; transition:box-shadow .15s,transform .15s; }
        .external-event:hover { transform:translateY(-2px); box-shadow:0 6px 15px rgba(27,46,75,.10); }
        .external-event:active { cursor:grabbing; }
        .agenda-task-title { display:block; font-size:13px; line-height:1.5; overflow-wrap:anywhere; margin:6px 0; }
        .agenda-task-type { display:inline-block; color:#526780; background:#f1f5f9; padding:3px 7px; border-radius:5px; font-size:11px; }
        .agenda-calendar-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .agenda-legend { display:flex; flex-wrap:wrap; gap:12px; font-size:11px; color:#556981; }
        .agenda-legend i { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:5px; }
        #calendar { --fc-border-color:#e8edf4; --fc-today-bg-color:#edf5ff; --fc-button-bg-color:#fff; --fc-button-text-color:#344966; --fc-button-border-color:#dae2ed; --fc-button-hover-bg-color:#edf3fa; --fc-button-hover-border-color:#c9d5e4; --fc-button-active-bg-color:#2563eb; --fc-button-active-border-color:#2563eb; }
        #calendar .fc-toolbar-title { font-size:20px; font-weight:700; color:#243852; }
        #calendar .fc-button { font-size:12px; box-shadow:none; padding:7px 10px; }
        #calendar .fc-button-active { color:#fff; }
        #calendar .fc-col-header-cell { background:#f8fafc; padding:8px 0; font-size:12px; }
        #calendar .fc-col-header-cell-cushion, #calendar .fc-daygrid-day-number { color:#526780; }
        #calendar .fc-event { cursor:pointer; border-radius:5px; padding:3px 5px; font-size:12px; }
        #calendar .fc-event-title { white-space:normal; overflow-wrap:anywhere; }
        .modal-content { border:0; border-radius:12px; overflow:hidden; }
        .modal-body { padding:24px; }
        .modal-body label { font-size:13px; color:#425773; }
        .form-control,.custom-select { border-radius:7px; border-color:#dce3ed; }
        .form-control:focus,.custom-select:focus { border-color:#80aaff; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        @media(max-width:991px) { .agenda-heading { align-items:flex-start; flex-direction:column; } .agenda-task-list { min-height:120px; max-height:300px; } }
        @media(max-width:575px) { .agenda-heading h1 { font-size:24px; } .agenda-actions { width:100%; } .agenda-actions .btn { flex:1; padding:10px; font-size:13px; } #calendar .fc-toolbar { flex-direction:column; gap:12px; } #calendar .fc-toolbar-title { font-size:18px; } .modal-body { padding:16px; } .modal-footer { gap:8px; } .modal-footer > div { display:flex; flex-wrap:wrap; gap:6px; } }
        @media(prefers-reduced-motion:reduce) { .external-event { transition:none; } .external-event:hover { transform:none; } }
    </style>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
document.addEventListener('DOMContentLoaded', function() {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-bottom-right', timeOut: 4000 };
    const originalFetch = window.fetch.bind(window);
    const pendingWrites = new Set();
    function fetch(url, options) {
        const writing = options && options.method && options.method.toUpperCase() !== 'GET';
        if (!writing) return originalFetch(url, options);
        if (pendingWrites.has(url)) return Promise.reject(new Error('La solicitud ya está en proceso'));
        pendingWrites.add(url);
        const buttons = [document.getElementById('add-event-btn'), document.getElementById('edit-event-btn'), document.getElementById('save-event-btn')];
        buttons.forEach(button => { if (button) button.disabled = true; });
        return originalFetch(url, options).finally(() => {
            pendingWrites.delete(url);
            if (!pendingWrites.size) buttons.forEach(button => { if (button) button.disabled = false; });
        });
    }
    function filterTasks() {
        const query = document.getElementById('task-search').value.trim().toLocaleLowerCase('es');
        const type = document.getElementById('task-type').value;
        const tasks = Array.from(document.querySelectorAll('#external-events .external-event'));
        let visible = 0;
        tasks.forEach(task => {
            const match = (!query || (task.dataset.id + ' ' + task.dataset.titulo).toLocaleLowerCase('es').includes(query)) && (!type || task.dataset.tipo === type);
            task.classList.toggle('d-none', !match);
            if (match) visible++;
        });
        document.getElementById('pending-count').textContent = tasks.length;
        document.getElementById('task-results').textContent = tasks.length ? visible + ' de ' + tasks.length + ' tareas' : '';
        document.getElementById('task-no-results').classList.toggle('d-none', visible > 0 || tasks.length === 0);
    }
    document.getElementById('task-search').addEventListener('input', filterTasks);
    document.getElementById('task-type').addEventListener('change', filterTasks);
    filterTasks();
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const urlStore = "{{ route('produccion.admin.agendas.store') }}";
    const updateDateUrl = "{{ route('produccion.admin.agendas.updateDate', ['event' => ':id']) }}";
    const updateDateItem = "{{ route('produccion.admin.agendas.updateDateItem', ['event' => ':id']) }}";
    const urlGetEvent = "{{ route('produccion.admin.agendas.getEvents') }}";
    const formImport = document.getElementById('formImportExcel');
    const loadingSpinner = document.getElementById('importLoading');
    const btnSubmit = document.getElementById('btnSubmitImport');
    // --- IMPORTACIÓN EXCEL ---
    if (formImport) {
        formImport.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('excelFile');
            if (!fileInput.files.length) {
                Swal.fire('Atención', 'Por favor selecciona un archivo Excel.', 'warning');
                return;
            }
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            loadingSpinner.classList.remove('d-none');
            btnSubmit.disabled = true;
            fetch("{{ route('produccion.admin.agenda.import') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;
                if (!response.ok) {
                    const errorText = !isJson ? await response.text() : null;
                    console.error('Respuesta del servidor (no JSON):', errorText);
                    throw new Error((data && data.message) ? data.message : `Error ${response.status}: Respuesta no válida del servidor.`);
                }
                return data;
            })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                // 1. Limpiar el input
                document.getElementById('excelFile').value = '';
                // 2. Cerrar el modal (Soporte jQuery y Bootstrap 5 nativo)
                const modalElement = document.getElementById('importExcelModal');
                if (typeof $ !== 'undefined' && $('#importExcelModal').length) {
                    $('#importExcelModal').modal('hide');
                } else if (modalElement) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modalInstance.hide();
                }
                // 3. Mostrar la alerta de éxito
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    timer: 1000,
                    timerProgressBar: true
                }).then(() => {
                    // 4. Recargar el calendario o la página completa para actualizar el index
                    if (typeof calendar !== 'undefined' && calendar !== null) {
                        calendar.refetchEvents();
                    }
                    // Si tienes elementos fuera del calendario en el index (como la lista de tareas pendientes a la izquierda), recargamos la página:
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire('Error de Importación', error.message, 'error');
            })
            .finally(() => {
                loadingSpinner.classList.add('d-none');
                btnSubmit.disabled = false;
            });
        });
    }
    // --- INICIALIZACIÓN DE FULLCALENDAR ---
    const tipoColors = {
        'Interno': '#3788d8',
        'Taller': '#28a745',
        'Visita': '#ffc107',
        'Llamada': '#6c757d'
    };
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 576 ? 'listWeek' : 'dayGridWeek',
        firstDay: 1,
        height: 'auto',
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' },
        noEventsContent: 'No hay actividades en este período',
        loading: function(active) { document.getElementById('calendar-status').textContent = active ? 'Cargando actividades…' : ''; },
        eventSourceFailure: function() { document.getElementById('calendar-status').textContent = 'No se pudieron cargar las actividades. Actualiza la página para reintentar.'; },
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listWeek'
        },
        editable: true,
        droppable: true,
        events: {
            url: urlGetEvent,
            method: 'GET'
        },
        eventDisplay: 'block',
        dayMaxEvents: true,
        // Drag interno en el calendario
        eventDrop: function(info) {
            let url = updateDateUrl.replace(':id', info.event.id);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start: info.event.startStr,
                    estado: 1
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en servidor');
                return res.json();
            })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                if (data.success) {
                    toastr.success("Fecha actualizada correctamente");
                } else {
                    info.revert();
                    toastr.error(data.message || "No se pudo actualizar");
                }
            })
            .catch(err => {
                info.revert();
                console.error("Error:", err);
                toastr.error("Error de conexión");
            });
        },
        // Drag desde la lista externa
        drop: function(info) {
            let eventId = info.draggedEl.dataset.id;
            if (!eventId) {
                toastr.error("ID de evento no válido");
                return;
            }
            let url = updateDateUrl.replace(':id', eventId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start: info.dateStr,
                    estado: 1
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en servidor');
                return res.json();
            })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                if (data.success) {
                    info.draggedEl.remove();
                    filterTasks();
                    toastr.success("Evento asignado exitosamente");
                    setTimeout(() => {
                        info.view.calendar.refetchEvents();
                    }, 500);
                } else {
                    toastr.error(data.message || "Error al asignar");
                }
            })
            .catch(err => {
                console.error("Error en drop:", err);
                toastr.error("Error de conexión");
            });
        },
///10.79.1.10
        // Click en evento (Abrir Modal Edición)
        eventClick: function(info) {
            if (!info.event.start) return;
            const fecha = info.event.startStr.slice(0, 10);
            document.getElementById('edit-event-id').value = info.event.id;
            document.getElementById('edit-event-title').value = info.event.title;
            document.getElementById('edit-event-fecha_inicio').value = fecha;
            document.getElementById('edit-event-type').value = info.event.extendedProps.tipo || 'Llamada';
            const selectUser = document.getElementById('edit-event-usuario');
            if (selectUser) {
                selectUser.value = info.event.extendedProps.assigned_user_id || '';
            }
            const checkCompletado = document.getElementById('edit-event-completado');
            if (checkCompletado) {
                checkCompletado.checked = info.event.extendedProps.estado == 2;
            }
            $('#editEventModal').modal('show');
        }
    });
    calendar.render();
    // Inicializar Arrastrables Externos
    const containerEl = document.getElementById('external-events');
    if (containerEl) {
        new FullCalendar.Draggable(containerEl, {
            itemSelector: '.external-event',
            eventData: function(el) {
                return {
                    id: el.dataset.id,
                    title: el.dataset.titulo || el.innerText.trim(),
                    color: el.style.borderLeftColor,
                    create: false,
                    extendedProps: {
                        tipo: el.dataset.tipo
                    }
                };
            }
        });
    }
    // Crear Nueva Tarea Sin Agendar
    const btnAddEvent = document.getElementById('add-event-btn');
    if (btnAddEvent) {
        btnAddEvent.addEventListener('click', function() {
            const nombre = document.getElementById('evt-nombre').value.trim();
            const contacto = document.getElementById('evt-contacto').value.trim();
            const telefono = document.getElementById('evt-telefono').value.trim();
            const tipo = document.getElementById('new-event-type').value;
            const assignedUser = document.getElementById('evt-usuarios').value;
            const servicio = document.getElementById('evt-servicio').value;
            if (!nombre || !telefono || !assignedUser) {
                Swal.fire('Atención', 'Nombre, teléfono y responsable son obligatorios', 'warning');
                return;
            }
            const payload = {
                title: `${nombre} - ${contacto} - ${telefono} - ${tipo} - ${servicio}`,
                tipo: tipo,
                assigned_user_id: assignedUser,
                contacto: contacto,
                telefono: telefono,
                servicio: servicio,
                proyecto: document.getElementById('evt-proyecto').value
            };
            fetch(urlStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async res => { const data = await res.json(); if (!res.ok) throw new Error(data.message || 'No se pudo guardar la actividad'); return data; })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                if (data.success) {
                    $('#modalEvento').modal('hide');
                    Swal.fire('¡Listo!', 'Tarea creada en lista de espera', 'success')
                        .then(() => {
                            location.reload();
                        });
                } else {
                    toastr.error(data.message || 'Error al guardar la tarea');
                }
            })
            .catch(err => {
                console.error(err);
                toastr.error('Error al procesar la solicitud');
            });
        });
    }
    // Guardar Cambios (Actualizar Evento Existente)
    const btnEditEvent = document.getElementById('edit-event-btn');
    if (btnEditEvent) {
        btnEditEvent.addEventListener('click', function() {
            const id = document.getElementById('edit-event-id').value;
            const url = updateDateItem.replace(':id', id);
            const title = document.getElementById('edit-event-title').value;
            const tipo = document.getElementById('edit-event-type').value;
            const selectUser = document.getElementById('edit-event-usuario');
            const assignedUser = selectUser ? selectUser.value : null;
            const fechaInicio = document.getElementById('edit-event-fecha_inicio').value;
            const checkCompletado = document.getElementById('edit-event-completado');
            const estaCompletado = checkCompletado ? checkCompletado.checked : false;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    tipo: tipo,
                    assigned_user_id: assignedUser,
                    start: fechaInicio,
                    estado: estaCompletado ? 2 : 1
                })
            })
            .then(async res => { const data = await res.json(); if (!res.ok) throw new Error(data.message || 'No se pudo guardar la actividad'); return data; })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                if (data.success || data.event) {
                    const ev = calendar.getEventById(id);
                    if (ev) {
                        if (estaCompletado) {
                            ev.remove();
                        } else {
                            const finalTitle = data.event ? (data.event.titulo || data.event.title) : title;
                            const finalTipo = data.event ? data.event.tipo : tipo;
                            const finalUser = data.event ? data.event.assigned_user_id : assignedUser;
                            ev.setProp('title', finalTitle);
                            ev.setStart(fechaInicio);
                            ev.setExtendedProp('tipo', finalTipo);
                            ev.setExtendedProp('assigned_user_id', finalUser);
                            if (tipoColors[finalTipo]) {
                                ev.setProp('backgroundColor', tipoColors[finalTipo]);
                            }
                        }
                    }
                    if (!data.warning) { $('#editEventModal').modal('hide'); }
                    if (!data.warning) { toastr.success("Evento actualizado correctamente"); }
                }
                 else {
                    toastr.error(data.message || "No se pudo actualizar el evento");
                }
            })
            .catch(err => {
                console.error("Error en edit-event:", err);
                toastr.error("Error de conexión al actualizar");
            });
        });
    }
   const btnSaveEvent = document.getElementById('save-event-btn');
    if (btnSaveEvent) {
        btnSaveEvent.addEventListener('click', function() {
            const id = document.getElementById('edit-event-id').value;
            const url = updateDateItem.replace(':id', id);
            const title = document.getElementById('edit-event-title').value;
            const tipo = document.getElementById('edit-event-type').value;
            const selectUser = document.getElementById('edit-event-usuario');
            const assignedUser = selectUser ? selectUser.value : null;
            const fechaInicio = document.getElementById('edit-event-fecha_inicio').value;
            const estaCompletado = 2;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    tipo: tipo,
                    assigned_user_id: assignedUser,
                    start: fechaInicio,
                    estado: estaCompletado ? 2 : 1
                })
            })
            .then(async res => { const data = await res.json(); if (!res.ok) throw new Error(data.message || 'No se pudo guardar la actividad'); return data; })
            .then(data => {
                if (data.warning) {
                    toastr.warning(data.warning, 'Sincronización pendiente', { timeOut: 10000 });
                }
                if (data.success || data.event) {
                    const ev = calendar.getEventById(id);
                    if (ev) {
                        if (estaCompletado) {
                            ev.remove();
                        } else {
                            const finalTitle = data.event ? (data.event.titulo || data.event.title) : title;
                            const finalTipo = data.event ? data.event.tipo : tipo;
                            const finalUser = data.event ? data.event.assigned_user_id : assignedUser;
                            ev.setProp('title', finalTitle);
                            ev.setStart(fechaInicio);
                            ev.setExtendedProp('tipo', finalTipo);
                            ev.setExtendedProp('assigned_user_id', finalUser);
                            if (tipoColors[finalTipo]) {
                                ev.setProp('backgroundColor', tipoColors[finalTipo]);
                            }
                        }
                    }
                    if (!data.warning) { $('#editEventModal').modal('hide'); }
                    if (!data.warning) { toastr.success("Evento actualizado correctamente"); }
                } else {
                    toastr.error(data.message || "No se pudo actualizar el evento");
                }
            })
            .catch(err => {
                console.error("Error en edit-event:", err);
                toastr.error("Error de conexión al actualizar");
            });
        });
    }
});
</script>
@endsection
