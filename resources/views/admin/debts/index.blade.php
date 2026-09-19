@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <a href="{{ route('admin.debts.create') }}" class="btn btn-primary mb-3">
        Nueva Deuda
    </a>

    <td>

    </td>


    <table id="tabla" class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Saldo</th>
                <th>Cuotas</th>
                <th>Estado</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debts as $debt)
                <tr>
                    <td>{{ $debt->name }}</td>
                    <td>{{ $debt->type }}</td>
                    <td>{{ $debt->current_balance }}</td>
                    <td>{{ $debt->paid_installments }}/{{ $debt->total_installments }}</td>
                    <td>{{ $debt->status ? 'Activa' : 'Pagada' }}</td>
                    <td><button class="btn btn-success btn-sm btnPagar" data-id="{{ $debt->id }}"
                            data-nombre="{{ $debt->name }}">
                            Pagar
                        </button></td>
                </tr>
            @endforeach
        </tbody>
    </table>

{{-- MOdal modalPago --}}
    <div class="modal fade" id="modalPago">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="tituloDeuda"></h5>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="debt_id">

                    <div class="mb-3">
                        <label>Monto</label>
                        <input type="number" step="0.01" id="amount" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Comentario</label>
                        <input type="text" id="comment" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="guardarPago">
                        Guardar Pago
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#tabla').DataTable();
        });


        $(document).on('click', '.btnPagar', function() {

            let id = $(this).data('id');
            let nombre = $(this).data('nombre');

            $('#debt_id').val(id);
            $('#tituloDeuda').text('Pago a: ' + nombre);

            $('#modalPago').modal('show');
        });



        $('#guardarPago').click(function() {

            let id = $('#debt_id').val();

            $.ajax({
                url: '/admin/debts/' + id + '/pay',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: $('#amount').val(),
                    comment: $('#comment').val()
                },
                success: function(response) {

                    $('#modalPago').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Pago registrado',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    location.reload(); // luego lo optimizamos
                }
            });
        });
    </script>
@stop
