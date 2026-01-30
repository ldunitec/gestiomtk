@extends('adminlte::page')

@section('content_header')


    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <h4>📅 Agenda Semanal</h4>
                    </h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearsw_mikModal">
                            Nuevo
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <body>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="pendientes">
                                    <h3>Pendientes sin agendar</h3>
                                    <input type="text" id="nuevaPendiente" placeholder="Nueva tarea...">
                                    <button onclick="agregarPendiente()">Agregar</button>
                                    <div id="listaPendientes"></div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="botones">
                                    <button onclick="semanaAnterior()">⬅ Semana anterior</button>
                                    <button onclick="semanaSiguiente()">Semana siguiente ➡</button>
                                    <button onclick="sincronizarTodoist()">🔄 Sincronizar Todoist</button>
                                    <button onclick="exportarExcel()">📊 Exportar Excel</button>
                                </div>

                                <div id="agenda" class="agenda"></div>


                            </div>
                        </div>



                    </body>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>



    </div>
    </div>


@stop

@section('css')
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        .agenda {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .dia {
            border: 1px solid #ccc;
            padding: 5px;
            min-height: 150px;
        }

        .tarea {
            padding: 5px;
            margin: 3px 0;
            background: #f2f2f2;
            cursor: grab;
        }

        .pendientes {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }

        .botones {
            margin: 10px 0;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    {{-- <script>
        function agregarPendiente() {
            let texto = document.getElementById("nuevaPendiente").value.trim();
            if (!texto) return;
 const urlCrear = "{{ url('admin/tasks/pendientes') }}";
            fetch(urlCrear, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        texto: texto,
                        asignado: "Interno"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    pendientes.push(data);
                    document.getElementById("nuevaPendiente").value = "";
                    guardar(); // si quieres seguir guardando en localStorage
                    mostrarPendientes();
                })
               
        }
    </script> --}}
@stop
