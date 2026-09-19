@extends('adminlte::page')

@section('content')
    <h3>Dashboard de Red (NOC)</h3>

    <div class="row" id="contenedorEquipos"></div>
@endsection

@section('js')
    <script>
        function cargarEquipos() {

            $.get("{{ route('admin.dashboard.red.data') }}", function(data) {

                let html = '';

                data.forEach(eq => {

                    let color = eq.estado == 1 ? 'success' : 'danger';
                    let estadoTexto = eq.estado == 1 ? 'ONLINE' : 'OFFLINE';
                    html +=
                        `<div class="col-md-3">
    <div class="info-box bg-${color}">
        <span class="info-box-icon">
            <i class="fas fa-server"></i>
        </span>

        <div class="info-box-content">
            <span class="info-box-text">${eq.nombre}</span>
            <span class="info-box-number">${eq.ip}</span>

            <div>${estadoTexto}</div>

            <small>CPU: ${eq.cpu ?? '-'}%</small><br>
            <small>RAM: ${eq.ram ?? '-'}%</small><br>
            <small>${eq.fecha ?? ''}</small>
        </div>
    </div>
</div>
`;
                });

                $('#contenedorEquipos').html(html);
            });
        }

        // 🔄 refresco cada 5 segundos
        setInterval(cargarEquipos, 5000);

        // cargar al iniciar
        cargarEquipos();
    </script>
@endsection
