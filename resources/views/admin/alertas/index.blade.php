@extends('adminlte::page')

@section('content')
<h3>Alertas de Red en Tiempo Real</h3>

<table class="table table-bordered" id="tablaAlertas">
    <thead>
        <tr>
            <th>Equipo</th>
            <th>Tipo</th>
            <th>Mensaje</th>
            <th>Fecha</th>
        </tr>
    </thead>
</table>
@endsection

@section('js')
<script>

    
let tablaAlertas = $('#tablaAlertas').DataTable({
    ajax: "{{ route('admin.alertas.data') }}",
    columns:[
        {data:'equipo.nombre', defaultContent:'-'},
        {data:'tipo'},
        {data:'mensaje'},
        {data:'created_at'}
    ],
    order:[[3,'desc']]
});

// 🔄 REFRESCO AUTOMÁTICO CADA 5 SEGUNDOS
setInterval(function(){
    tablaAlertas.ajax.reload(null,false);
}, 5000);
</script>
@endsection