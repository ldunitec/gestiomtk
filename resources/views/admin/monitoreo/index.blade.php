@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h2 class="card-title">Monitoreo de Red</h2>
                </div>
                <div class="card-body">


                    <table class="table table-bordered" id="tabla">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>IP</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h2 class="card-title">Alertas de Red</h2>
                </div>
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let tabla = $('#tabla').DataTable({
            ajax: "{{ route('admin.monitoreo.data') }}",
            columns: [{
                    data: 'nombre'
                },
                {
                    data: 'ip'
                },
                {
                    data: 'estado',
                    render: function(data) {
                        if (data == 1) {
                            return '<span class="badge bg-success">ONLINE</span>';
                        } else {
                            return '<span class="badge bg-danger">OFFLINE</span>';
                        }
                    }
                }
            ]
        });

        // 🔄 REFRESCO AUTOMÁTICO
        setInterval(function() {
            tabla.ajax.reload(null, false);
        }, 5000); // cada 5 segundos
    </script>


    <script>
        $('#tablaAlertas').DataTable({
              "pageLength": 5,
               "order": [
                       [3, 'desc']
                   ],
            ajax: "{{ route('admin.alertas.data') }}",
            columns: [{
                    data: 'equipo.nombre'
                },
                {
                    data: 'equipo.ip_address'
                },
                {
                    data: 'mensaje'
                },
                {
                    data: 'created_at'
                }
            ]
        });
    </script>
@endsection
