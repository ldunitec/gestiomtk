@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de tareas  </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tareas registrados</h3>

                    <div class="card-tools">
                        <a href="{{url('/admin/task/create')}}" class="btn btn-primary"> Crear nuevo</a>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  asdasasdasdasdasd
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>


<style>
  #contenedor {
    display: flex;
    gap: 20px;
    font-family: Arial, sans-serif;
  }
  #pendientes, #agenda {
    flex: 1;
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 400px;
    border-radius: 6px;
  }
  #pendientes h3, #agenda h3 {
    text-align: center;
  }
  .tarea {
    padding: 6px 10px;
    margin: 5px 0;
    background-color: #eee;
    border-radius: 4px;
    cursor: grab;
  }
  .tarea.dragging {
    opacity: 0.5;
  }
  .dia {
    border: 1px solid #bbb;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 4px;
  }
  .dia h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    text-align: center;
  }
  button {
    margin-left: 5px;
  }
</style>

<div id="contenedor">
  <div id="pendientes">
    <h3>Pendientes</h3>
    <input id="inputPendiente" placeholder="Nueva tarea..." />
    <button id="btnAddPendiente">Agregar</button>
    <div id="listaPendientes"></div>
  </div>

  <div id="agenda">
    <h3>Agenda Semanal</h3>
    <div id="semanasDias"></div>
  </div>
</div>

<script>
const apiBase = "/api/tasks";
const colores = { Kevyn: "lightblue", Luis: "lightgreen", Celvyn: "lightcoral", Harling: "lightgoldenrodyellow", Interno: "lightgray" };

let tasks = [];
let inicioSemana;

function obtenerInicioSemana(fecha) {
  let d = new Date(fecha);
  let day = d.getDay();
  let diff = d.getDate() - day + (day === 0 ? -6 : 1);
  return new Date(d.setDate(diff));
}

async function cargarTareas() {
  const res = await fetch(apiBase);
  tasks = await res.json();
  inicioSemana = obtenerInicioSemana(new Date());
  mostrarPendientes();
  mostrarAgenda();
}

function mostrarPendientes() {
  const cont = document.getElementById("listaPendientes");
  cont.innerHTML = "";
  tasks.filter(t => !t.due_date).forEach(t => {
    const div = crearDivTarea(t, true);
    cont.appendChild(div);
  });
}

function mostrarAgenda() {
  const cont = document.getElementById("semanasDias");
  cont.innerHTML = "";

  for(let i=0; i<7; i++) {
    let d = new Date(inicioSemana);
    d.setDate(d.getDate()+i);
    let fechaStr = d.toISOString().split("T")[0];
    
    let diaDiv = document.createElement("div");
    diaDiv.className = "dia";
    diaDiv.dataset.fecha = fechaStr;
    
    let h4 = document.createElement("h4");
    h4.textContent = d.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'short' });
    diaDiv.appendChild(h4);
    
    // Contenedor para tareas
    let tareasDia = document.createElement("div");
    tareasDia.className = "tareasDia";
    tareasDia.ondragover = e => e.preventDefault();
    tareasDia.ondrop = e => {
      e.preventDefault();
      const id = e.dataTransfer.getData("text/plain");
      moverTarea(id, fechaStr);
    };
    
    tasks.filter(t => t.due_date && t.due_date.startsWith(fechaStr)).forEach(t => {
      const div = crearDivTarea(t, false);
      tareasDia.appendChild(div);
    });

    diaDiv.appendChild(tareasDia);
    cont.appendChild(diaDiv);
  }
}

function crearDivTarea(tarea, esPendiente) {
  const div = document.createElement("div");
  div.className = "tarea";
  div.draggable = true;
  div.textContent = tarea.content + (tarea.assigned_to ? ` (${tarea.assigned_to})` : "");
  div.style.backgroundColor = colores[tarea.assigned_to] || "#ddd";
  div.dataset.id = tarea.id;
  
  div.ondragstart = e => {
    e.dataTransfer.setData("text/plain", tarea.id);
    div.classList.add("dragging");
  };
  div.ondragend = e => {
    div.classList.remove("dragging");
  };
  
  div.oncontextmenu = e => {
    e.preventDefault();
    const nuevo = prompt("Asignar a (Kevyn, Luis, Celvyn, Harling, Interno):", tarea.assigned_to || "");
    if (nuevo && colores[nuevo]) {
      actualizarTarea(tarea.id, {assigned_to: nuevo});
    }
  };

  if (esPendiente) {
    const btnFecha = document.createElement("button");
    btnFecha.textContent = "Asignar Fecha";
    btnFecha.onclick = () => asignarFecha(tarea.id);
    div.appendChild(btnFecha);
  } else {
    const btnQuitar = document.createElement("button");
    btnQuitar.textContent = "Quitar Fecha";
    btnQuitar.onclick = () => actualizarTarea(tarea.id, {due_date: null});
    div.appendChild(btnQuitar);
  }

  return div;
}

function asignarFecha(id) {
  const fecha = prompt("Fecha (YYYY-MM-DD):");
  if(!fecha) return;
  const hora = prompt("Hora (HH:MM):") || "08:00";
  actualizarTarea(id, {due_date: fecha + "T" + hora + ":00"});
}

async function actualizarTarea(id, datos) {
  await fetch(apiBase + "/" + id, {
    method: "PUT",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify(datos)
  });
  await cargarTareas();
}

async function moverTarea(id, fecha) {
  await actualizarTarea(id, {due_date: fecha + "T08:00:00"});
}

async function agregarPendiente() {
  const input = document.getElementById("inputPendiente");
  const texto = input.value.trim();
  if(!texto) return alert("Ingrese una tarea");
  await fetch(apiBase, {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({content: texto, assigned_to:"Interno"})
  });
  input.value = "";
  cargarTareas();
}

async function sincronizarTodoist() {
  const res = await fetch(apiBase + "/sync", {method: "POST"});
  const data = await res.json();
  alert(data.message);
  cargarTareas();
}

document.getElementById("btnAddPendiente").onclick = agregarPendiente;

document.getElementById("btnSync")?.addEventListener("click", sincronizarTodoist);

cargarTareas();
</script>


{{-- @section('css')
    <style>
        /* Fondo transparente y sin borde en el contenedor */
        #example1_wrapper .dt-buttons {
            background-color: transparent;
            box-shadow: none;
            border: none;
            display: flex;
            justify-content: center; /* Centrar los botones */
            gap: 10px; /* Espaciado entre botones */
            margin-bottom: 15px; /* Separar botones de la tabla */
        }

        /* Estilo personalizado para los botones */
        #example1_wrapper .btn {
            color: #fff; /* Color del texto en blanco */
            border-radius: 4px; /* Bordes redondeados */
            padding: 5px 15px; /* Espaciado interno */
            font-size: 14px; /* Tamaño de fuente */
        }

        /* Colores por tipo de botón */
        .btn-danger { background-color: #dc3545; border: none; }
        .btn-success { background-color: #28a745; border: none; }
        .btn-info { background-color: #17a2b8; border: none; }
        .btn-warning { background-color: #ffc107; color: #212529; border: none; }
        .btn-default { background-color: #6e7176; color: #212529; border: none; }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            $("#example1").DataTable({
                "pageLength": 5,
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ administrativos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 administrativos",
                    "infoFiltered": "(Filtrado de _MAX_ total administrativos)",
                    "lengthMenu": "Mostrar _MENU_ administrativos",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscador:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                buttons: [
                    { text: '<i class="fas fa-copy"></i> COPIAR', extend: 'copy', className: 'btn btn-default' },
                    { text: '<i class="fas fa-file-pdf"></i> PDF', extend: 'pdf', className: 'btn btn-danger' },
                    { text: '<i class="fas fa-file-csv"></i> CSV', extend: 'csv', className: 'btn btn-info' },
                    { text: '<i class="fas fa-file-excel"></i> EXCEL', extend: 'excel', className: 'btn btn-success' },
                    { text: '<i class="fas fa-print"></i> IMPRIMIR', extend: 'print', className: 'btn btn-warning' }
                ]
            }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
        });
    </script>
@stop --}}
