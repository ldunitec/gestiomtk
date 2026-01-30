@extends('adminlte::page')

@section('title', 'Agenda Semanal')

@section('content_header')
    <h1>Agenda Semanal</h1>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-md-3">
            <h4>Tareas sin agendar 2026</h4>
            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#modalEvento">
                Nueva tarea
            </button>

            <div id="external-events" class="p-3 border rounded" style="min-height:250px; background:#f9f9f9;">
                @foreach ($unassignedEvents as $evento)
                    <div class="external-event rounded p-2 mb-2 text-white"
                        style="background: {{ $evento->color }}; cursor:grab;" data-id="{{ $evento->id }}"
                        data-titulo="{{ $evento->titulo }}" data-tipo="{{ $evento->tipo ?? 'Llamada' }}">
                        {{ $evento->id }} // {{ $evento->titulo }}
                    </div>
                @endforeach
            </div>
            <!-- Botón para abrir modal -->

            <!-- Modal -->
            <div class="modal modal-xlg fade" id="modalEvento" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header bg-primary">
                            <h5 class="modal-title">Agregar tarea</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal-body">

                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Nueva Actividad</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nombre">Nombre</label>
                                                        <input type="text" id="evt-nombre" class="form-control mb-2"
                                                            placeholder="Nombre">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contacto">Contacto</label>
                                                        <input type="text" id="evt-contacto" class="form-control mb-2"
                                                            placeholder="Contacto">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="telefono">Telefono</label>
                                                        <input type="text" id="evt-telefono" class="form-control mb-2"
                                                            placeholder="Teléfono">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="servicio">Servico solicitado</label>
                                                        <input type="text" id="evt-servicio" class="form-control mb-2"
                                                            placeholder="Servicio">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="asignado">Asignado</label>
                                                        <select id="evt-asignado" class="form-control mt-2">
                                                            <option value="Ninguno"selected>Ninguno</option>
                                                            <option value="Luis Diaz">Luis Diaz</option>
                                                            <option value="Celvyn Lozano">Celvyn Lozano</option>
                                                            <option value="Harling Maradiaga">Harling Maradiaga</option>
                                                            <option value="Kevin Godoy">Kevin Godoy</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tipo">Tipo de actividad</label>
                                                        <select id="new-event-type" class="form-control mt-2">
                                                            <option value="Interno">Interno</option>
                                                            <option value="Taller">Taller</option>
                                                            <option value="Visita">Visita</option>
                                                            <option value="Llamada" selected>Llamada</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>






                                            <hr>

                                        </div>
                                        <!-- /.card-body -->
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="add-event-btn" class="btn btn-primary">
                                Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                        </div>



                    </div>
                </div>
            </div>




            {{-- card nuevo 
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Nueva Actividad</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="evt-nombre" class="form-control mb-2" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="contacto">Contacto</label>
                        <input type="text" id="evt-contacto" class="form-control mb-2" placeholder="Contacto">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Telefono</label>
                        <input type="text" id="evt-telefono" class="form-control mb-2" placeholder="Teléfono">
                    </div>
                    <div class="form-group">
                        <label for="servicio">Servico solicitado</label>
                        <input type="text" id="evt-servicio" class="form-control mb-2" placeholder="Servicio">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de actividad</label>
                        <select id="new-event-type" class="form-control mt-2">
                            <option value="Interno">Interno</option>
                            <option value="Taller">Taller</option>
                            <option value="Visita">Visita</option>
                            <option value="Llamada" selected>Llamada</option>
                        </select>
                    </div>
                    <hr>
                    <button id="add-event-btn" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
                <!-- /.card-body -->
            </div> --}}

        </div>

        <div class="col-md-9">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal para editar/eliminar evento -->
    {{-- <div class="modal fade" id="eventoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Editar Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formEvento">
          @csrf
          <input type="hidden" id="event_id" name="id">
          
          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title">
          </div>
          
          <div class="mb-3">
            <label for="start" class="form-label">Inicio</label>
            <input type="datetime-local" class="form-control" id="start" name="start">
          </div>
          
          <div class="mb-3">
            <label for="end" class="form-label">Fin</label>
            <input type="datetime-local" class="form-control" id="end" name="end">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" id="btnEliminar" class="btn btn-danger">Eliminar</button>
        <button type="button" id="btnGuardar" class="btn btn-success">Guardar</button>
      </div>

    </div>
  </div>
</div> --}}
    <!-- Modal para editar evento sin agendar -->
    <div class="modal fade" id="modalEditarEvento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditarEvento">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_evento_id" name="id">
                        <div class="mb-3">
                            <label for="edit_titulo">Título</label>
                            <input type="text" id="edit_titulo" name="titulo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo">Tipo</label>
                            <input type="text" id="edit_tipo" name="tipo" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar evento -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-event-id">
                    <div class="form-group mb-2">
                        <label>Título</label>
                        <input type="text" id="edit-event-title" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label>Fecha</label>
                        <input type="date" id="edit-event-fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label>Categoría</label>
                        <select id="edit-event-type" class="form-control">
                            <option value="Interno">Interno</option>
                            <option value="Taller">Taller</option>
                            <option value="Visita">Visita</option>
                            <option value="Llamada">Llamada</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete-event-btn" class="btn btn-danger">Eliminar</button>
                    <button type="button" id="save-event-btn" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet"> --}}
    <style>
        .external-event:hover {
            opacity: 0.85;
            transform: scale(1.03);
            transition: 0.2s;
        }

        #calendar .fc-event {
            cursor: grab;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const urlStore = "{{ route('admin.agenda.store') }}";
            const updateDateUrl = "{{ route('admin.agenda.updateDate', ['event' => ':id']) }}";
            const updateDateItem = "{{ route('admin.agenda.updateDateItem', ['event' => ':id']) }}";
            const updateEstaUrl = "{{ route('admin.agenda.updateEstado', ['event' => ':id']) }}";
            // const updateEventUrl = "{{ route('admin.agenda.updateEstado', ['event' => ':id']) }}";

            const tipoColors = {
                'Interno': '#3788d8',
                'Taller': '#28a745',
                'Visita': '#ffc107',
                'Llamada': '#6c757d'
            };

            // Inicializar calendario
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                locale: 'es',
                initialView: 'dayGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listWeek'
                },
                editable: true,
                droppable: true,
                events: @json($events),
                eventDisplay: 'block',
                dayMaxEvents: true,

                eventDrop: function(info) {
                    let url = updateDateUrl.replace(':id', info.event.id);
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                start: info.event.startStr
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            info.event.setProp('color', tipoColors[data.event.tipo] || '#6c757d');
                        })
                        .catch(err => console.error(err));
                },

                drop: function(info) {
                    let eventId = info.draggedEl.dataset.id;
                    let url = updateDateUrl.replace(':id', eventId);

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                start: info.dateStr,
                                end: null
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            calendar.refetchEvents();
                            info.draggedEl.parentNode.removeChild(info.draggedEl);
                        })
                        .catch(err => console.error("Error en asignación:", err));
                },

                // 🔹 Abrir modal al hacer click en evento
                eventClick: function(info) {
                    const fecha = info.event.start.toISOString().slice(0, 10);
                    document.getElementById('edit-event-id').value = info.event.id;
                    document.getElementById('edit-event-title').value = info.event.title;
                    document.getElementById('edit-event-fecha_inicio').value = fecha;
                    document.getElementById('edit-event-type').value = info.event.extendedProps.tipo ||
                        'Llamada';
                    $('#editEventModal').modal('show');
                }
            });


            calendar.render();

            // Arrastrables
            new FullCalendar.Draggable(document.getElementById('external-events'), {
                itemSelector: '.external-event',
                eventData: function(el) {
                    return {
                        id: el.dataset.id,
                        title: el.innerText,
                        color: tipoColors[el.dataset.tipo] || '#6c757d',
                        tipo: el.dataset.tipo || 'Llamada'
                    };
                }
            });

            // Crear nuevo evento
            document.getElementById('add-event-btn').addEventListener('click', function() {
                const nombre = document.getElementById('evt-nombre').value.trim();
                const contacto = document.getElementById('evt-contacto').value.trim();
                const telefono = document.getElementById('evt-telefono').value.trim();
                const servicio = document.getElementById('evt-servicio').value.trim();
                const asignado = document.getElementById('evt-asignado').value.trim();
                const tipo = document.getElementById('new-event-type').value;

                if (!nombre || !telefono) {
                    alert('Nombre y teléfono son obligatorios');
                    return;
                }

                // 👉 Unir todo en un solo string separado por /
                const title = `${nombre} _ ${contacto} _ ${telefono} _ ${asignado} _ ${servicio}`;
                // if (!title) return;
                alert(title)

                fetch(urlStore, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            title: title,
                            tipo: tipo
                        })


                    })

                    .then(res => res.json())
                    .then(data => {
                        const div = document.createElement('div');
                        div.classList.add('external-event', 'rounded', 'p-2', 'mb-2', 'text-white');
                        div.style.background = tipoColors[data.event.tipo] || '#6c757d';
                        div.dataset.id = data.event.id;
                        div.dataset.tipo = data.event.tipo;
                        div.title = data.event.title;
                        div.innerText = data.event.title;
                        div.style.cursor = 'grab';
                        document.getElementById('external-events').appendChild(div);
                        document.getElementById('new-event').value = '';
                    })

                    .catch(err => console.error(err));
                $('#modalEvento').modal('hide');
            });


            // Guardar cambios desde modal
            document.getElementById('save-event-btn').addEventListener('click', function() {
                const id = document.getElementById('edit-event-id').value;
                const title = document.getElementById('edit-event-title').value.trim();
                const start = document.getElementById('edit-event-fecha_inicio').value.trim();
                const tipo = document.getElementById('edit-event-type').value;


                if (!title) return;

                const url = updateDateItem.replace(':id', id);
                console.log(url);
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            title,
                            tipo,
                            start
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        const ev = calendar.getEventById(data.event.id);
                        ev.setProp('title', data.event.title);
                        ev.setProp('color', tipoColors[data.event.tipo] || '#6c757d');
                        $('#editEventModal').modal('hide');
                    })
                    .catch(err => console.error(err));
            });

            // MODIFICAR ESTADO  desde modal
            document.getElementById('delete-event-btn').addEventListener('click', function() {
                const id = document.getElementById('edit-event-id').value;
                const title = document.getElementById('edit-event-title').value.trim();
                const tipo = document.getElementById('edit-event-type').value;


                if (!title) return;

                const url = updateEventUrl.replace(':id', id);
                console.log(url);
                fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            title,
                            tipo
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        const ev = calendar.getEventById(data.event.id);
                        ev.setProp('title', data.event.title);
                        ev.setProp('color', tipoColors[data.event.tipo] || '#6c757d');
                        $('#editEventModal').modal('hide');
                    })
                    .catch(err => console.error(err));
            });

            // :::::::::::::: Abrir modal desde no agendado:::::::::::::::::::::::::::::::::::::::::
            $(document).on('click', '.external-event', function() {
                let id = $(this).data('id');
                let titulo = $(this).data('titulo');
                let tipo = $(this).data('tipo');
                console.log(id + "-" + titulo + "-" + tipo)
                $('#edit_evento_id').val(id);
                $('#edit_titulo').val(titulo);
                $('#edit_tipo').val(tipo);

                $('#modalEditarEvento').modal('show');
            });


            // ::::::::::::::::::::::::::::::  

            // Eliminar evento desde modal
            document.getElementById('delete-event-btn').addEventListener('click', function() {
                const id = document.getElementById('edit-event-id').value;
                const url = deleteEventUrl.replace(':id', id);
                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        const ev = calendar.getEventById(id);
                        if (ev) ev.remove();
                        $('#editEventModal').modal('hide');
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
@stop
