@extends('adminlte::page')

@section('title', 'Agenda Semanal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark">Agenda Semanal</h1>
    </div>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">




    <div class="row">
        <!-- Columna Izquierda: Tareas Sin Agendar -->
        <div class="col-lg-4 col-md-5">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-tasks mr-1"></i> Sin Agendar (2026)

                    </h3>
                    <button class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalEvento">
                        <i class="fas fa-plus mr-1"></i> Nueva

                    </button>

                    {{-- abrir modal importar excel --}}
                    <button type="button" class="btn btn-success btn-sm ml-auto" data-toggle="modal"
                        data-target="#importExcelModal">
                        <i class="fas fa-file-excel me-1"></i> Importar Excel
                    </button>

                    {{-- //////// --}}
                    <!-- Modal de Importación -->
                    <div class="modal fade" id="importExcelModal" aria-labelledby="importExcelModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="importExcelModalLabel">
                                        <i class="fas fa-file-import me-1"></i> Importar Agendas desde Excel
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="formImportExcel" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="excelFile" class="form-label fw-bold">Selecciona el archivo (.xlsx,
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
                                                <span class="visually-hidden">Procesando...</span>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Procesando registros en lotes, por favor
                                                espera...</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success" id="btnSubmitImport">
                                            <i class="fas fa-upload me-1"></i> Subir e Importar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    {{-- //////// --}}


                </div>
                <div class="card-body p-2">
                    <div id="external-events" class="p-2 border rounded bg-light"
                        style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                        @forelse ($unassignedEvents as $evento)
                            <div class="external-event shadow-sm rounded p-2 mb-2 text-white d-flex justify-content-between align-items-center"
                                style="background-color: {{ $evento->color ?? '#6c757d' }}; cursor: grab;"
                                data-id="{{ $evento->id }}" data-titulo="{{ $evento->titulo }}"
                                data-tipo="{{ $evento->tipo ?? 'Llamada' }}" data-estado="{{ $evento->estado }}">
                                <div>
                                    <span class="badge badge-light mr-1">#{{ $evento->id }}</span>
                                    <strong>{{ $evento->titulo }}</strong>
                                </div>
                                <i class="fas fa-grip-lines text-white-50"></i>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No hay tareas pendientes por agendar</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Calendario -->
        <div class="col-lg-8 col-md-7">
            <div class="card card-outline card-info shadow-sm">
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
                            <input type="text" value="0000-0000" id="evt-telefono" class="form-control"
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
                                {{-- <option value="6CrcqJQWVH88MRcJ">Soporte Megatk</option>
                                <option value="6Qq9F3FC9FCQMrcj">Cumpleaños</option> --}}
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
                            <option value="8503605">Luis Diaz</option>
                            <option value="10128774">Celvyn Lozano</option>
                            <option value="52565829">Harling Maradiaga</option>
                            <option value="49411725">Kevin Godoy</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit-event-completado"
                            name="completado" value="2">
                        <label class="form-check-label fw-bold" for="completado">
                            Marcar como completado
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" id="delete-event-btn" class="btn btn-outline-danger"><i
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
        .external-event {
            transition: all 0.2s ease-in-out;
            user-select: none;
        }

        .external-event:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #calendar .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script>
document.addEventListener('DOMContentLoaded', function() {

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
        initialView: 'dayGridWeek',
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
                if (data.success) {
                    info.draggedEl.remove();
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
                    title: el.innerText.trim(),
                    color: el.style.backgroundColor,
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

            if (!nombre || !telefono) {
                Swal.fire('Atención', 'Nombre y teléfono son obligatorios', 'warning');
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
            .then(res => res.json())
            .then(data => {
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
            .then(res => res.json())
            .then(data => {
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

                    $('#editEventModal').modal('hide');
                    toastr.success("Evento actualizado correctamente");
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
            alert('boton completar presionado');
            const id = document.getElementById('save-event-id').value;
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
            .then(res => res.json())
            .then(data => {
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

                    $('#editEventModal').modal('hide');
                    toastr.success("Evento actualizado correctamente");
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
