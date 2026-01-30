@extends('adminlte::page')

@section('title', 'Diagrama de Gantt')

@section('content_header')
    <h1>Diagrama de Gantt</h1>
@stop

@section('content')
    <div id="gantt" style="height:500px">
      
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/frappe-gantt/dist/frappe-gantt.css">

<style>
.bar-pending { fill: orange; }
.bar-complete { fill: green; }
</style>
@stop

@section('js')

<script src="https://unpkg.com/frappe-gantt/dist/frappe-gantt.js"></script>
<script>
    // Pasamos los datos desde Laravel
    var tasks = @json($tasks);
console.json tasks
    // Creamos el Gantt
    var gantt = new Gantt("#gantt", tasks, {
        view_mode: 'Day', // También puede ser Week, Month, Year
        date_format: 'Y-m-d',
        custom_popup_html: function(task) {
            return `<div class="gantt-popup">
                        <strong>${task.name}</strong><br>
                        Desde: ${task.start}<br>
                        Hasta: ${task.end}<br>
                        Progreso: ${task.progress}%
                    </div>`;
        },
        on_click: function(task) {
            alert("Tarea seleccionada: " + task.name);
        }
    });

</script>
@stop
