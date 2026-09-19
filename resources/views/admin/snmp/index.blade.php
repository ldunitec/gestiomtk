@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
 <h3>Equipos en la red</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>IP</th>
            <th>Uptime</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($devices as $device)
        <tr>
            <td>{{ $device->id }}</td>
            <td>{{ $device->name }}</td>
            <td>{{ $device->ip }}</td>
            <td>{{ $device->uptime }}</td>
            <td>
                <button class="btn btn-primary btn-sm"
                    onclick="verDetalle({{ $device->id }})">
                    Ver
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- MODAL -->
<div class="modal fade" id="deviceModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Información del equipo</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalContent">
        Cargando...
      </div>
    </div>
  </div>
</div>



</body>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
function verDetalle(id) {
    fetch(`/admin/snmp/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalContent').innerHTML = `
                <b>Hostname:</b> ${data.hostname}<br>
                <b>Descripción:</b> ${data.description}<br>
                <b>Uptime:</b> ${data.uptime}<br>
                <b>Contacto:</b> ${data.contact}<br>
                <b>Ubicación:</b> ${data.location}
            `;
            new bootstrap.Modal(
                document.getElementById('deviceModal')
            ).show();
        });
}
</script>
@stop


