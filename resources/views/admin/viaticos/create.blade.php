@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
   <div class="row">
    <div class="col-md-12">
        <div class="row">
    <div class="col-md-3">
        <select id="origen" class="form-control">
            <option value="Tegucigalpa">Tegucigalpa</option>
            <option value="San Pedro Sula">San Pedro Sula</option>
        </select>
    </div>

    <div class="col-md-3">
        <select id="ciudad" class="form-control">
            @foreach($ciudades as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="row">
    <div class="col-md-4">
        <label>Cliente</label>
        <input type="text" id="cliente" class="form-control" required>
    </div>

    <div class="col-md-8">
        <label>Detalle de la visita</label>
        <textarea id="detalle_visita" class="form-control" rows="2"></textarea>
    </div>
</div>

<hr>


    <div class="col-md-3">
        <select id="vehiculo" class="form-control">
            @foreach($vehiculos as $v)
                <option value="{{ $v->id }}">{{ $v->tipo }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label>
            <input type="checkbox" id="ida_vuelta"> Ida y vuelta
        </label>
    </div>

    <div class="col-md-1">
        <button id="calcular" class="btn btn-primary">Calcular</button>
    </div>
</div>

<hr>
<h4 id="resultado"></h4>

    </div>
   </div>

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
$('#calcular').click(function () {
    $.post("{{ route('viaticos.store') }}", {
        _token: "{{ csrf_token() }}",
        origen: $('#origen').val(),
        ciudad_id: $('#ciudad').val(),
        vehiculo_id: $('#vehiculo').val(),
        cliente: $('#cliente').val(),
        detalle_visita: $('#detalle_visita').val(),
        ida_vuelta: $('#ida_vuelta').is(':checked')
    }, function (res) {
        Swal.fire('Guardado', 'Viático registrado correctamente', 'success');
    });
});

</script>
@stop

