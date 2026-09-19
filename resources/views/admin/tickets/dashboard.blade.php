@extends('adminlte::page')

@section('content')

<div class="row">

    <div class="col-md-3">
         <a href="{{ url('admin/tickets') }}">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total }}</h3>
                <p>Total Tickets</p>
            </div>
        </div>
        </a>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendientes }}</h3>
                <p>Pendientes</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $proceso }}</h3>
                <p>En Proceso</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $finalizados }}</h3>
                <p>Finalizados</p>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Tickets por Mes</div>
            <div class="card-body">
                <canvas id="graficoMes"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Tickets por Técnico</div>
            <div class="card-body">
                <canvas id="graficoTecnico"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="alert alert-danger mt-3">
    SLA Vencidos: {{ $sla_vencidos }}
</div>

@endsection

@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// grafico mensual
new Chart(document.getElementById('graficoMes'), {
    type: 'bar',
    data: {
        labels: @json($meses),
        datasets: [{
            label: 'Tickets',
            data: @json($data)
        }]
    }
});

// grafico por tecnico
new Chart(document.getElementById('graficoTecnico'), {
    type: 'pie',
    data: {
        labels: @json($tecnicos->pluck('name')),
        datasets: [{
            data: @json($tecnicos->pluck('asignacion_count'))
        }]
    }
});
</script>

@endsection