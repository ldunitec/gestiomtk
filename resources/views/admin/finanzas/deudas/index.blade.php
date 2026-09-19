@extends('adminlte::page')

@section('content')
<div class="container">
    <h2>Deudas</h2>

    <!-- Botón agregar -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalDeuda">Nueva Deuda</button>

    <table class="table table-bordered" id="tablaDeudas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Monto Total</th>
                <th>Saldo Actual</th>
                <th>Tipo</th>
                <th>Cuenta</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDeuda" tabindex="-1" aria-labelledby="modalDeudaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formDeuda" method="POST" action="{{ route('finanzas.deudas.store') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Deuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Monto Total</label>
                    <input type="number" name="monto_total" class="form-control" required step="0.01">
                </div>
                <div class="mb-3">
                    <label>Tasa Interés (%)</label>
                    <input type="number" name="tasa_interes" class="form-control" step="0.01">
                </div>
                <div class="mb-3">
                    <label>Cuotas</label>
                    <input type="number" name="cuotas" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Cuota Mensual</label>
                    <input type="number" name="cuota_mensual" class="form-control" step="0.01">
                </div>
                <div class="mb-3">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-control">
                        <option value="prestamo">Préstamo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="personal">Personal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Cuenta</label>
                    <select name="cuenta_id" class="form-control">
                        <option value="">-- Seleccione --</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(function(){
    var tabla = $('#tablaDeudas').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('finanzas.deudas.data') }}",
        columns:[
            {data:'id'},
            {data:'nombre'},
            {data:'monto_total'},
            {data:'saldo_actual'},
            {data:'tipo'},
            {data:'cuenta'},
            {data:'acciones', orderable:false, searchable:false}
        ]
    });

    // Aquí puedes agregar tus eventos editar/eliminar usando AJAX como en ingresos
});
</script>
@endsection