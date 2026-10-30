@extends('adminlte::page')
@section('title', 'Agenda Semanal')
@section('content_header')
    <div class="agenda-heading">
        <div><span class="agenda-eyebrow">PLANIFICACIÓN DEL EQUIPO</span>
            <h1 class="m-0">Agenda semanal</h1>
            <p class="text-muted mb-0 mt-1">Organiza las actividades y da seguimiento al trabajo de tu equipo.</p>
        </div>
        <div class="agenda-actions"><button type="button" class="btn btn-outline-success" data-toggle="modal"
                data-target="#importExcelModal"><i class="fas fa-file-excel mr-2" aria-hidden="true"></i>Importar
                Excel</button>

            <button type="button" id="btnVerificarTodoist" class="btn btn-danger">
                Verificar Todoist
            </button>
            <div id="resultadoTodoist" role="status" aria-live="polite" class="mt-2"></div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEvento"><i
                    class="fas fa-plus mr-2" aria-hidden="true"></i>Nueva actividad</button>
        </div>
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
                        <i class="fas fa-tasks mr-1"></i> Sin agendar <span id="pending-count"
                            class="badge badge-primary ml-2">{{ count($unassignedEvents) }}</span>
                    </h3>
                    {{-- abrir modal importar excel --}}
                    {{-- //////// --}}
                    <!-- Modal de Importación -->
                    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog"
                        aria-labelledby="importExcelModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="importExcelModalLabel">
                                        <i class="fas fa-file-import mr-1"></i> Importar Agendas desde Excel
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"
                                        aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form id="formImportExcel" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="excelFile" class="form-label font-weight-bold">Selecciona el archivo
                                                (.xlsx,
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
                    <div class="agenda-search mb-2"><i class="fas fa-search text-muted" aria-hidden="true"></i><input
                            id="task-search" type="search" class="form-control" placeholder="Buscar por título o ticket…">
                    </div>
                    <label for="task-type" class="sr-only">Filtrar tareas por tipo</label>
                    <select id="task-type" class="custom-select mb-2">
                        <option value="">Todos los tipos</option>
                        <option>Interno</option>
                        <option>Taller</option>
                        <option>Visita</option>
                        <option>Llamada</option>
                    </select>
                    <p class="small text-muted mb-3">Haz doble clic sobre una tarea para editarla. Después arrástrala al
                        calendario.</p>
                    <div id="external-events" class="agenda-task-list">
                        @forelse ($unassignedEvents as $evento)
                            <div class="external-event d-flex justify-content-between align-items-start"
                                style="border-left-color: {{ $evento->color ?? '#6c757d' }};" data-id="{{ $evento->id }}"
                                data-titulo="{{ $evento->titulo }}" data-tipo="{{ $evento->tipo ?? 'Llamada' }}"
                                data-estado="{{ $evento->estado }}"
                                data-assigned-user-id="{{ $evento->assigned_user_id }}"
                                data-contacto="{{ $evento->contacto }}" data-telefono="{{ $evento->telefono }}"
                                data-servicio="{{ $evento->servicio }}" data-ticket="{{ $evento->secuencia_ticket }}">
                                <div class="agenda-drag-handle flex-grow-1"
                                    title="Doble clic para editar. Arrastra para agendar.">
                                    <span class="badge badge-light mr-1">#{{ $evento->id }}</span>
                                    <small class="text-muted agenda-task-ticket">Ticket
                                        {{ $evento->secuencia_ticket ?? '—' }}</small>
                                    <strong class="agenda-task-title">{{ $evento->titulo }}</strong>
                                    <small
                                        class="agenda-task-service d-block text-muted mb-1">{{ $evento->servicio }}</small>
                                    <span class="agenda-task-type">{{ $evento->tipo ?? 'Llamada' }}</span>
                                    @php
                                        $responsables = [
                                            '8503605' => 'Luis Diaz',
                                            '49411725' => 'Kevyn Godoy',
                                            '10128774' => 'Celvyn Lozano',
                                            '52565829' => 'Harling Maradiaga',
                                        ];
                                    @endphp

                                    <span class="agenda-user-type">
                                        {{ $responsables[(string) $evento->assigned_user_id] ?? ($evento->assigned_user_id === null ? 'Todos' : 'Sin identificar') }}
                                    </span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary edit-pending-btn ml-2"
                                    title="Editar tarea antes de agendar" aria-label="Editar tarea {{ $evento->id }}">
                                    <i class="fas fa-pencil-alt" aria-hidden="true"></i><span
                                        class="sr-only">Editar</span>
                                </button>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No hay tareas pendientes por agendar</p>
                            </div>
                        @endforelse
                    </div>
                    <p id="task-no-results" class="text-muted text-center py-3 d-none" role="status">No hay tareas que
                        coincidan con tu búsqueda.</p>
                    <small id="task-results" class="text-muted" aria-live="polite"></small>
                </div>
            </div>
        </div>
        <!-- Columna Derecha: Calendario -->
        <div class="col-xl-9 col-lg-8">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header agenda-calendar-header">
                    <h3 class="card-title font-weight-bold">Calendario de actividades</h3>
                    <div class="agenda-legend"><span><i style="background:#3788d8"></i>Interno</span><span><i
                                style="background:#28a745"></i>Taller</span><span><i
                                style="background:#ffc107"></i>Visita</span><span><i
                                style="background:#6c757d"></i>Llamada</span></div>
                </div>
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
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-plus mr-2"></i>Agregar Actividad
                    </h5>
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
                            <input type="text" value="0000-0000" id="evt-telefono" class="form-control"
                                inputmode="tel" placeholder="Teléfono">
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
    <!-- Editor compartido: tareas pendientes y actividades agendadas -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-title"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold" id="edit-modal-title">Editar actividad</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit-event-form">
                    <div class="modal-body">
                        <input type="hidden" id="edit-event-id">
                        <div id="edit-pending-hint" class="alert alert-info d-none">
                            Los cambios se guardarán en «Sin agendar». Después arrastra la tarea al calendario para enviarla
                            a Todoist.
                        </div>
                        <div id="edit-event-error" class="alert alert-danger d-none" role="alert"></div>
                        <div class="form-group">
                            <label for="edit-event-title">Título <span class="text-danger">*</span></label>
                            <input type="text" id="edit-event-title" class="form-control" required maxlength="255">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="edit-event-contacto">Contacto</label>
                                <input type="text" id="edit-event-contacto" class="form-control" maxlength="255">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="edit-event-telefono">Teléfono</label>
                                <input type="text" id="edit-event-telefono" class="form-control" inputmode="tel"
                                    maxlength="255">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-event-servicio">Servicio solicitado</label>
                            <textarea id="edit-event-servicio" class="form-control" rows="3" maxlength="255"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="edit-event-type">Tipo de actividad</label>
                                <select id="edit-event-type" class="form-control custom-select" required>
                                    <option value="Interno">Interno</option>
                                    <option value="Taller">Taller</option>
                                    <option value="Visita">Visita</option>
                                    <option value="Llamada">Llamada</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="edit-event-usuario">Asignado a <span class="text-danger">*</span></label>
                                <select id="edit-event-usuario" class="form-control custom-select" required>
                                    <option value="">-- Seleccionar Responsable --</option>
                                    <option value="todos">Todos (Equipo Completo)</option>
                                    <option value="8503605">Luis Diaz</option>
                                    <option value="10128774">Celvyn Lozano</option>
                                    <option value="52565829">Harling Maradiaga</option>
                                    <option value="49411725">Kevin Godoy</option>
                                </select>
                            </div>
                        </div>
                        <div id="edit-date-group" class="form-group">
                            <label for="edit-event-fecha_inicio">Fecha</label>
                            <input type="date" id="edit-event-fecha_inicio" class="form-control">
                        </div>
                        <div id="edit-completed-group" class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit-event-completado" value="2">
                            <label class="form-check-label" for="edit-event-completado">Marcar como completado</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="save-event-btn" class="btn btn-outline-success">Completar</button>
                        <button type="submit" id="edit-event-btn" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        .content-wrapper {
            background: #f3f6fa;
        }

        .agenda-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 12px 0 18px;
        }

        .agenda-heading h1 {
            font-size: 28px;
            font-weight: 700;
            color: #172b4d;
        }

        .agenda-eyebrow {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #61738c;
            margin-bottom: 6px;
        }

        .agenda-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .agenda-actions .btn {
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
        }

        .agenda-layout .card {
            border: 1px solid #e4eaf2;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(27, 46, 75, .04) !important;
            overflow: hidden;
        }

        .agenda-layout .card-header {
            background: #fff;
            border-bottom: 1px solid #edf1f6;
            padding: 18px;
        }

        .agenda-layout .card-title {
            font-size: 16px;
            color: #243852;
        }

        .agenda-search {
            position: relative;
        }

        .agenda-search i {
            position: absolute;
            left: 12px;
            top: 13px;
        }

        .agenda-search input {
            padding-left: 34px;
        }

        .agenda-task-list {
            min-height: 250px;
            max-height: 620px;
            overflow-y: auto;
            padding: 2px;
        }

        .external-event {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-left: 4px solid #6c757d;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            color: #263b55;
            cursor: grab;
            user-select: none;
            transition: box-shadow .15s, transform .15s;
        }

        .external-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(27, 46, 75, .10);
        }

        .external-event:active {
            cursor: grabbing;
        }

        .agenda-task-title {
            display: block;
            font-size: 13px;
            line-height: 1.5;
            overflow-wrap: anywhere;
            margin: 6px 0;
        }

        .agenda-task-type {
            display: inline-block;
            color: #526780;
            background: #f1f5f9;
            padding: 3px 7px;
            border-radius: 5px;
            font-size: 11px;
        }

        .agenda-calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .agenda-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 11px;
            color: #556981;
        }

        .agenda-legend i {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }

        #calendar {
            --fc-border-color: #e8edf4;
            --fc-today-bg-color: #edf5ff;
            --fc-button-bg-color: #fff;
            --fc-button-text-color: #344966;
            --fc-button-border-color: #dae2ed;
            --fc-button-hover-bg-color: #edf3fa;
            --fc-button-hover-border-color: #c9d5e4;
            --fc-button-active-bg-color: #2563eb;
            --fc-button-active-border-color: #2563eb;
        }

        #calendar .fc-toolbar-title {
            font-size: 20px;
            font-weight: 700;
            color: #243852;
        }

        #calendar .fc-button {
            font-size: 12px;
            box-shadow: none;
            padding: 7px 10px;
        }

        #calendar .fc-button-active {
            color: #fff;
        }

        #calendar .fc-col-header-cell {
            background: #f8fafc;
            padding: 8px 0;
            font-size: 12px;
        }

        #calendar .fc-col-header-cell-cushion,
        #calendar .fc-daygrid-day-number {
            color: #526780;
        }

        #calendar .fc-event {
            cursor: pointer;
            border-radius: 5px;
            padding: 3px 5px;
            font-size: 12px;
        }

        #calendar .fc-event-title {
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .modal-content {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-body label {
            font-size: 13px;
            color: #425773;
        }

        .form-control,
        .custom-select {
            border-radius: 7px;
            border-color: #dce3ed;
        }

        .form-control:focus,
        .custom-select:focus {
            border-color: #80aaff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        @media(max-width:991px) {
            .agenda-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .agenda-task-list {
                min-height: 120px;
                max-height: 300px;
            }
        }

        @media(max-width:575px) {
            .agenda-heading h1 {
                font-size: 24px;
            }

            .agenda-actions {
                width: 100%;
            }

            .agenda-actions .btn {
                flex: 1;
                padding: 10px;
                font-size: 13px;
            }

            #calendar .fc-toolbar {
                flex-direction: column;
                gap: 12px;
            }

            #calendar .fc-toolbar-title {
                font-size: 18px;
            }

            .modal-body {
                padding: 16px;
            }

            .modal-footer {
                gap: 8px;
            }

            .modal-footer>div {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }
        }

        @media(prefers-reduced-motion:reduce) {
            .external-event {
                transition: none;
            }

            .external-event:hover {
                transform: none;
            }
        }

        .external-event {
            cursor: default;
        }

        .agenda-drag-handle {
            cursor: grab;
            min-width: 0;
        }

        .agenda-drag-handle:active {
            cursor: grabbing;
        }

        .external-event.is-saving {
            opacity: .65;
            pointer-events: none;
        }

        .edit-pending-btn {
            flex-shrink: 0;
            min-width: 34px;
            min-height: 34px;
        }

        .agenda-task-service {
            overflow-wrap: anywhere;
        }
    </style>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = id => document.getElementById(id);
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const urlStore = "{{ route('produccion.admin.agendas.store') }}";
            const updateDateUrl = "{{ route('produccion.admin.agendas.updateDate', ['event' => ':id']) }}";
            const updateDateItem = "{{ route('produccion.admin.agendas.updateDateItem', ['event' => ':id']) }}";
            const urlGetEvent = "{{ route('produccion.admin.agendas.getEvents') }}";
            const importUrl = "{{ route('produccion.admin.agenda.import') }}";
            const colors = {
                Interno: '#3788d8',
                Taller: '#28a745',
                Visita: '#ffc107',
                Llamada: '#6c757d'
            };
            const busyTasks = new Set();
            let editor = null;
            let savingEditor = false;
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-bottom-right',
                timeOut: 5000
            };

            function routeFor(template, id) {
                return template.replace(/:id|%3Aid/gi, encodeURIComponent(id));
            }

            function responsable(value) {
                if (value === null || value === undefined || value === '' || value === 'todos') return 'todos';
                return String(value) === '8503605' ? '8503605' : String(value);
            }

            function showError(message) {
                el('edit-event-error').textContent = message;
                el('edit-event-error').classList.remove('d-none');
            }

            function notify(data, successMessage) {
                if (data.warning || data.todoist?.success === false) {
                    const details = (data.todoist?.errors || []).join(' ');
                    toastr.warning([data.warning || 'Sincronización pendiente.', details].filter(Boolean).join(' '),
                        'Actividad guardada', {
                            timeOut: 12000
                        });
                } else {
                    toastr.success(successMessage);
                }
            }
            async function post(url, payload, formData = false) {
                const headers = {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                };
                if (!formData) headers['Content-Type'] = 'application/json';
                const response = await window.fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers,
                    body: formData ? payload : JSON.stringify(payload)
                });
                let data;
                try {
                    data = await response.json();
                } catch (_) {
                    throw new Error('El servidor no devolvió JSON. Comprueba tu sesión e intenta nuevamente.');
                }
                if (!response.ok || data.success === false) {
                    const errors = Object.values(data.errors || {}).flat().join(' ');
                    throw new Error(errors || data.message || 'No se pudo guardar la actividad.');
                }
                return data;
            }

            function filterTasks() {
                const query = el('task-search').value.trim().toLocaleLowerCase('es');
                const type = el('task-type').value;
                const tasks = Array.from(document.querySelectorAll('#external-events .external-event'));
                let visible = 0;
                tasks.forEach(task => {
                    const search = [task.dataset.id, task.dataset.ticket, task.dataset.titulo, task.dataset
                        .servicio
                    ].join(' ').toLocaleLowerCase('es');
                    const match = (!query || search.includes(query)) && (!type || task.dataset.tipo ===
                        type);
                    task.classList.toggle('d-none', !match);
                    if (match) visible++;
                });
                el('pending-count').textContent = tasks.length;
                el('task-results').textContent = tasks.length ? `${visible} de ${tasks.length} tareas` :
                    'No hay tareas pendientes por agendar';
                el('task-no-results').classList.toggle('d-none', visible > 0 || tasks.length === 0);
            }

            function updateCard(card, event) {
                const fields = {
                    titulo: 'titulo',
                    tipo: 'tipo',
                    assignedUserId: 'assigned_user_id',
                    contacto: 'contacto',
                    telefono: 'telefono',
                    servicio: 'servicio',
                    estado: 'estado'
                };
                Object.entries(fields).forEach(([key, field]) => {
                    card.dataset[key] = event[field] ?? '';
                });
                card.querySelector('.agenda-task-title').textContent = event.titulo || '';
                card.querySelector('.agenda-task-type').textContent = event.tipo || 'Llamada';
                card.querySelector('.agenda-task-service').textContent = event.servicio || '';
                card.style.borderLeftColor = event.color || colors[event.tipo] || '#6c757d';
            }

            function openEditor(record, card = null) {
                if (savingEditor || busyTasks.has(String(record.id))) return;
                editor = {
                    id: String(record.id),
                    card,
                    pending: Boolean(card),
                    date: (record.fecha_inicio || '').slice(0, 10)
                };
                el('edit-event-id').value = record.id;
                el('edit-event-title').value = record.titulo || '';
                el('edit-event-contacto').value = record.contacto || '';
                el('edit-event-telefono').value = record.telefono || '';
                el('edit-event-servicio').value = record.servicio || '';
                el('edit-event-type').value = record.tipo || 'Llamada';
                el('edit-event-usuario').value = responsable(record.assigned_user_id);
                el('edit-event-fecha_inicio').value = editor.date;
                el('edit-event-fecha_inicio').required = !editor.pending;
                el('edit-event-fecha_inicio').disabled = editor.pending;
                el('edit-event-completado').checked = Number(record.estado) === 2;
                el('edit-event-completado').disabled = editor.pending;
                el('edit-event-error').classList.add('d-none');
                el('edit-modal-title').textContent = editor.pending ? 'Editar tarea sin agendar' :
                    'Editar actividad agendada';
                el('edit-pending-hint').classList.toggle('d-none', !editor.pending);
                el('edit-date-group').classList.toggle('d-none', editor.pending);
                el('edit-completed-group').classList.toggle('d-none', editor.pending);
                el('save-event-btn').classList.toggle('d-none', editor.pending);
                el('edit-event-btn').textContent = editor.pending ? 'Guardar cambios' : 'Actualizar';
                $('#editEventModal').modal('show');
            }
            $('#editEventModal').on('hide.bs.modal', function(event) {
                if (savingEditor) event.preventDefault();
            });
            el('external-events').addEventListener('dblclick', function(event) {
                if (event.target.closest('button, a, input, select, textarea')) return;
                const card = event.target.closest('.external-event');
                if (!card) return;
                event.preventDefault();
                openEditor({
                    id: card.dataset.id,
                    titulo: card.dataset.titulo,
                    tipo: card.dataset.tipo,
                    assigned_user_id: card.dataset.assignedUserId,
                    contacto: card.dataset.contacto,
                    telefono: card.dataset.telefono,
                    servicio: card.dataset.servicio,
                    estado: card.dataset.estado
                }, card);
            });
            el('external-events').addEventListener('click', function(event) {
                const button = event.target.closest('.edit-pending-btn');
                if (!button) return;
                const card = button.closest('.external-event');
                openEditor({
                    id: card.dataset.id,
                    titulo: card.dataset.titulo,
                    tipo: card.dataset.tipo,
                    assigned_user_id: card.dataset.assignedUserId,
                    contacto: card.dataset.contacto,
                    telefono: card.dataset.telefono,
                    servicio: card.dataset.servicio,
                    estado: card.dataset.estado
                }, card);
            });
            el('task-search').addEventListener('input', filterTasks);
            el('task-type').addEventListener('change', filterTasks);
            filterTasks();

            const calendar = new FullCalendar.Calendar(el('calendar'), {
                initialView: window.innerWidth < 576 ? 'listWeek' : 'dayGridWeek',
                firstDay: 1,
                height: 'auto',
                locale: 'es',
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    list: 'Lista'
                },
                noEventsContent: 'No hay actividades en este período',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listWeek'
                },
                editable: true,
                eventDurationEditable: false,
                droppable: true,
                events: {
                    url: urlGetEvent,
                    method: 'GET'
                },
                eventDisplay: 'block',
                dayMaxEvents: true,
                loading(active) {
                    el('calendar-status').textContent = active ? 'Cargando actividades…' : '';
                },
                eventSourceFailure() {
                    el('calendar-status').textContent =
                        'No se pudieron cargar las actividades. Actualiza la página para reintentar.';
                },
                eventAllow(_drop, event) {
                    return !busyTasks.has(String(event.id));
                },
                dropAccept(dragged) {
                    return !busyTasks.has(String(dragged.closest('.external-event')?.dataset.id));
                },
                async eventDrop(info) {
                    const id = String(info.event.id);
                    if (busyTasks.has(id)) {
                        info.revert();
                        return;
                    }
                    busyTasks.add(id);
                    try {
                        const data = await post(routeFor(updateDateUrl, id), {
                            start: info.event.startStr,
                            estado: 1
                        });
                        notify(data, 'Fecha actualizada correctamente.');
                    } catch (error) {
                        info.revert();
                        toastr.error(error.message);
                    } finally {
                        busyTasks.delete(id);
                    }
                },
                async drop(info) {
                    const card = info.draggedEl.closest('.external-event');
                    if (!card || busyTasks.has(card.dataset.id)) return;
                    const id = card.dataset.id;
                    busyTasks.add(id);
                    card.classList.add('is-saving');
                    try {
                        const data = await post(routeFor(updateDateUrl, id), {
                            start: info.dateStr,
                            estado: 1
                        });
                        card.remove();
                        filterTasks();
                        calendar.refetchEvents();
                        notify(data, 'Actividad agendada correctamente.');
                    } catch (error) {
                        toastr.error(error.message);
                    } finally {
                        busyTasks.delete(id);
                        card.classList.remove('is-saving');
                    }
                },
                eventClick(info) {
                    info.jsEvent.preventDefault();
                    const event = info.event;
                    openEditor({
                        ...event.extendedProps,
                        id: event.id,
                        titulo: event.title,
                        fecha_inicio: event.startStr
                    });
                }
            });
            calendar.render();
            new FullCalendar.Draggable(el('external-events'), {
                itemSelector: '.external-event:not(.is-saving) .agenda-drag-handle',
                minDistance: 5,
                eventData(handle) {
                    const card = handle.closest('.external-event');
                    return {
                        id: card.dataset.id,
                        title: card.dataset.titulo,
                        color: card.style.borderLeftColor,
                        create: false,
                        extendedProps: {
                            tipo: card.dataset.tipo,
                            assigned_user_id: card.dataset.assignedUserId
                        }
                    };
                }
            });

            async function saveEditor(complete = false) {
                if (!editor || savingEditor || busyTasks.has(editor.id)) return;
                if (!el('edit-event-form').reportValidity()) return;
                const current = editor;
                const title = el('edit-event-title').value.trim();
                if (!title) {
                    showError('El título es obligatorio.');
                    return;
                }
                const payload = {
                    title,
                    tipo: el('edit-event-type').value,
                    assigned_user_id: el('edit-event-usuario').value,
                    contacto: el('edit-event-contacto').value.trim(),
                    telefono: el('edit-event-telefono').value.trim(),
                    servicio: el('edit-event-servicio').value.trim()
                };
                // Pendiente: omitir fecha y estado, para conservar su condición sin agendar.
                if (!current.pending) {
                    const date = el('edit-event-fecha_inicio').value;
                    // Evitar borrar la hora de una actividad si solo se editaron sus detalles.
                    if (date !== current.date) payload.start = date;
                    payload.estado = complete || el('edit-event-completado').checked ? 2 : 1;
                }
                savingEditor = true;
                busyTasks.add(current.id);
                current.card?.classList.add('is-saving');
                el('edit-event-error').classList.add('d-none');
                const buttons = el('editEventModal').querySelectorAll('button');
                buttons.forEach(button => {
                    button.disabled = true;
                });
                el('edit-event-btn').textContent = 'Guardando…';
                let saved = false;
                try {
                    const data = await post(routeFor(updateDateItem, current.id), payload);
                    if (!data.event) throw new Error(
                        'Falta el evento actualizado en la respuesta. Actualiza la página para comprobar el guardado.'
                    );
                    if (current.card) {
                        if (data.event.fecha_inicio || Number(data.event.estado) === 2) current.card.remove();
                        else updateCard(current.card, data.event);
                        filterTasks();
                    }
                    if (!current.pending || data.event.fecha_inicio) calendar.refetchEvents();
                    notify(data, current.pending ? 'Cambios guardados. La tarea sigue sin agendar.' :
                        'Actividad actualizada correctamente.');
                    saved = true;
                } catch (error) {
                    showError(error.message);
                } finally {
                    savingEditor = false;
                    busyTasks.delete(current.id);
                    current.card?.classList.remove('is-saving');
                    buttons.forEach(button => {
                        button.disabled = false;
                    });
                    el('edit-event-btn').textContent = current.pending ? 'Guardar cambios' : 'Actualizar';
                }
                if (saved) $('#editEventModal').modal('hide');
            }
            el('edit-event-form').addEventListener('submit', event => {
                event.preventDefault();
                saveEditor();
            });
            el('save-event-btn').addEventListener('click', () => saveEditor(true));

            el('add-event-btn').addEventListener('click', async function() {
                const nombre = el('evt-nombre').value.trim();
                const contacto = el('evt-contacto').value.trim();
                const telefono = el('evt-telefono').value.trim();
                const servicio = el('evt-servicio').value.trim();
                const tipo = el('new-event-type').value;
                const assignedUser = el('evt-usuarios').value;
                if (!nombre || !telefono || !assignedUser) {
                    Swal.fire('Atención', 'Nombre, teléfono y responsable son obligatorios.',
                        'warning');
                    return;
                }
                this.disabled = true;
                try {
                    const data = await post(urlStore, {
                        title: `${nombre} - ${contacto} - ${telefono} - ${tipo} - ${servicio}`,
                        contacto,
                        telefono,
                        servicio,
                        tipo,
                        assigned_user_id: assignedUser,
                        proyecto: el('evt-proyecto').value
                    });
                    $('#modalEvento').modal('hide');
                    notify(data, 'Tarea creada en Sin agendar.');
                    window.location.reload();
                } catch (error) {
                    toastr.error(error.message);
                } finally {
                    this.disabled = false;
                }
            });
            el('formImportExcel').addEventListener('submit', async function(event) {
                event.preventDefault();
                if (el('btnSubmitImport').disabled) return;
                if (!el('excelFile').files.length) {
                    Swal.fire('Atención', 'Selecciona un archivo Excel.', 'warning');
                    return;
                }
                el('btnSubmitImport').disabled = true;
                el('importLoading').classList.remove('d-none');
                try {
                    const data = await post(importUrl, new FormData(this), true);
                    $('#importExcelModal').modal('hide');
                    await Swal.fire('Importación completada', data.message, 'success');
                    window.location.reload();
                } catch (error) {
                    Swal.fire('Error de importación', error.message, 'error');
                } finally {
                    el('btnSubmitImport').disabled = false;
                    el('importLoading').classList.add('d-none');
                }
            });
        });


        
    </script>

    <script>
document.getElementById('btnVerificarTodoist')
    .addEventListener('click', async function () {
        const boton = this;
        const resultado = document.getElementById('resultadoTodoist');

        boton.disabled = true;
        boton.textContent = 'Verificando Todoist…';
        resultado.textContent = '';

        try {
            const respuesta = await fetch(
                @json(route('produccion.admin.agenda.verificar-todoist')),
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({})
                }
            );

            const data = await respuesta.json();

            if (!respuesta.ok || !data.success) {
                throw new Error(
                    data.message || 'No se pudo verificar Todoist.'
                );
            }

            resultado.textContent = data.message;

            if (data.actualizadas > 0) {
                alert(data.message);
                window.location.reload();
            }
        } catch (error) {
            resultado.textContent = error.message;
        } finally {
            boton.disabled = false;
            boton.textContent = 'Verificar completadas en Todoist';
        }
    });
</script>
@endsection
