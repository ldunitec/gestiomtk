@extends('adminlte::page')

@section('title', 'Dashboard Inventario BTR')

@section('content_header')
    <h1>Dashboard Inventario BTR</h1>
@section('content')

    <div class="row">

        {{-- Total Registros --}}
        <div class="col-md-2">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $inventarios->count() }}</h3>
                    <p>Total Localidades</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>

        {{-- Total Cámaras --}}
        <div class="col-md-2">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $inventarios->sum('camaras') }}</h3>
                    <p>Total Cámaras</p>
                </div>
                <div class="icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
        </div>

        {{-- Total Switch PoE --}}
        <div class="col-md-2">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inventarios->sum('switchPoE') }}</h3>
                    <p>Total Switch PoE</p>
                </div>
                <div class="icon">
                    <i class="fas fa-network-wired"></i>
                </div>
            </div>
        </div>

        {{-- Total XCOM --}}
        <div class="col-md-2">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $inventarios->sum('xcom') }}</h3>
                    <p>Total XCOM</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ethernet"></i>
                </div>
            </div>

        </div>

        {{-- Total sg116 --}}
        <div class="col-md-2">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $inventarios->sum('sg116') }}</h3>
                    <p>Total SG-116</p>
                </div>
                <div class="icon">
                    <i class="fas fa-microchip"></i>
                </div>
            </div>
        </div>
        {{-- Total fuente --}}
        <div class="col-md-2">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $inventarios->sum('fuente') }}</h3>
                    <p>Total Fuente</p>
                </div>
                <div class="icon">
                    <i class="fas fa-car-battery"></i>
                </div>
            </div>
        </div>
    </div>



    {{-- grafica  --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Totales Generales</h3>
        </div>

        <div class="card-body" style="height:300px;">
            <canvas id="graficaTotales"></canvas>
        </div>
    </div>


    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado General</h3>
        </div>

        <div class="card-body">
            <table id="tablaInventario" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Localidad</th>
                        <th>IP</th>
                        <th>Cámaras</th>
                        <th>Switch PoE</th>
                        <th>XCOM</th>
                        <th>SG116</th>
                        <th>Fuente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventarios as $item)
                        <tr>
                            <td>{{ $item->localidad }}</td>
                            <td>{{ $item->ip }}</td>
                            <td>{{ $item->camaras }}</td>
                            <td>{{ $item->switchPoE }}</td>
                            <td>{{ $item->xcom }}</td>
                            <td>{{ $item->sg116 }}</td>
                            <td>{{ $item->fuente }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>




@stop

{{-- @section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            $("#tablaInventario").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
        });



        document.addEventListener("DOMContentLoaded", function() {

            const canvas = document.getElementById('graficaInventario');

            if (!canvas) {
                console.log("No existe el canvas");
                return;
            }

            const ctx = canvas.getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($inventarios->pluck('localidad')),
                    datasets: [{
                            label: 'Cámaras',
                            data: @json($inventarios->pluck('camaras')),
                            backgroundColor: 'rgba(54, 162, 235, 0.7)'
                        },
                        {
                            label: 'Switch PoE',
                            data: @json($inventarios->pluck('switchPoE')),
                            backgroundColor: 'rgba(255, 206, 86, 0.7)'
                        },
                        {
                            label: 'XCOM',
                            data: @json($inventarios->pluck('xcom')),
                            backgroundColor: 'rgba(255, 99, 132, 0.7)'
                        },
                        {
                            label: 'SG116',
                            data: @json($inventarios->pluck('sg116')),
                            backgroundColor: 'rgba(75, 192, 192, 0.7)'
                        },
                        {
                            label: 'Fuente',
                            data: @json($inventarios->pluck('fuente')),
                            backgroundColor: 'rgba(153, 102, 255, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        });
    </script>

@stop --}}

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(function() {
            $("#tablaInventario").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
        });



        document.addEventListener("DOMContentLoaded", function() {

            const ctx = document.getElementById('graficaTotales').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Cámaras', 'Switch PoE', 'XCOM', 'SG116', 'Fuente'],
                    datasets: [{
                        label: 'Cantidad Total',
                        data: [
                            {{ $totales['camaras'] }},
                            {{ $totales['switchPoE'] }},
                            {{ $totales['xcom'] }},
                            {{ $totales['sg116'] }},
                            {{ $totales['fuente'] }}
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        });
    </script>

@stop
