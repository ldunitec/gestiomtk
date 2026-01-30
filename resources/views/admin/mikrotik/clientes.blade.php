@extends('adminlte::page')

@section('title', 'Clientes MikroTik')

@section('content_header')
<h1>Clientes Autorizados</h1>
@stop

@section('content')
<table id="leasesTable" class="table table-bordered table-hover table-striped">
    <thead class="bg-dark">
        <tr>
            <th>IP</th>
            <th>MAC</th>
            <th>Estado</th>
            <th>Comentario</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($leases as $l)
        <tr>
            <td>{{ $l['address'] ?? '-' }}</td>
            <td>{{ $l['mac-address'] ?? '-' }}</td>
            <td>
                @if(($l['disabled'] ?? 'false') === 'true')
                    <span class="badge badge-danger">Cortado</span>
                @else
                    <span class="badge badge-success">Activo</span>
                @endif
            </td>
            <td>{{ $l['comment'] ?? '' }}</td>
            <td>
                <button
                    class="btn btn-sm {{ ($l['disabled'] ?? 'false') === 'true' ? 'btn-success' : 'btn-danger' }}"
                    onclick="toggle('{{ $l['.id'] }}', {{ ($l['disabled'] ?? 'false') === 'false' ? 'true' : 'false' }})">
                    {{ ($l['disabled'] ?? 'false') === 'true' ? 'Habilitar' : 'Cortar' }}
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop


@section('js')
<script>
function toggle(id, disabled) {
    fetch("{{ route('admin.mikrotik.toggle') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new URLSearchParams({
            id: id,
            disabled: disabled ? 1 : 0
        })
    })
    .then(r => r.json())
    .then(r => {
        alert(r.message);
        location.reload();
    });
}
</script>

<script>
$(function () {
    $('#leasesTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ clientes",
            paginate: {
                previous: "Anterior",
                next: "Siguiente"
            },
            zeroRecords: "No hay clientes"
        },
        columnDefs: [
            { orderable: false, targets: 4 } // Acción no ordenable
        ]
    });
});
</script>
@stop
