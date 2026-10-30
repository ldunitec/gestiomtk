{{-- data table  --}}
<script>
    $(function() {
        $("#example1").DataTable({
            "pageLength": 100,
            "order": [
                // [0, 'desc']
            ],
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ patch",
                "infoEmpty": "Mostrando 0 a 0 de 0 patch",
                "infoFiltered": "(Filtrado de _MAX_ total patch)",
                "lengthMenu": "Mostrar _MENU_ patch",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            buttons: [{
                    text: '<i class="fas fa-copy"></i> COPIAR',
                    extend: 'copy',
                    className: 'btn btn-default'
                },
                {
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    extend: 'pdf',
                    className: 'btn btn-danger',
                    orientation: 'landscape'
                },
                {
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    extend: 'csv',
                    className: 'btn btn-info'
                },
                {
                    text: '<i class="fas fa-file-excel"></i> EXCEL',
                    extend: 'excel',
                    className: 'btn btn-success'
                },
                {
                    text: '<i class="fas fa-print"></i> IMPRIMIR',
                    extend: 'print',
                    className: 'btn btn-warning'
                }

            ]
        }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
    });
</script>

{{-- boton modal selectEquipo --}}
<script>
    $(document).ready(function() {
        $('#selectEquipo').select2();
        dropdownParent: $('#crearPortsModal.modal-body')
    });
</script>

{{-- :::::::::::::::::::::::::::SELECT 2::::::::::::::::::::::::::::::::::::: --}}
{{-- select2-selectNomb --}}
<script>
    $(function() {
        $('.selectNombre').select2({
            placeholder: 'Selecciona una opción',
            allowClear: true,
            width: '100%' // para que el select tome todo el ancho del form-control
        });
    });
</script>
{{-- select2-selectpatch  --}}
<script>
    $(function() {
        $('.selectPatch').select2({
            placeholder: 'Selecciona una opción',
            allowClear: true,
            width: '100%' // para que el select tome todo el ancho del form-control
        });
    });
</script>
{{-- select2-selectsw --}}
<script>
    $(function() {
        $('.selectsw').select2({
            placeholder: 'Selecciona una opción',
            allowClear: true,
            width: '100%' // para que el select tome todo el ancho del form-control
        });
    });
</script>

{{-- :::::::::::::::::::::::::::LLENADO DE INPUT::::::::::::::::::::::::::::::::::::: --}}

{{-- selectPatch-nombrepatch --}}
<script>
    $('.selectPatch').on('change', function() {
        let nombrepatch = $(this).find(':selected').data('nombrepatch');
        // alert (nombrepatch);
        $('#nombrepatch').val(nombrepatch);
    });
</script>

{{-- selectSW --}}
<script>
    $('.selectsw').on('change', function() {
        let nombresw = $(this).find(':selected').data('nombresw');
        // alert (nombresw);
        $('#nombresw').val(nombresw);
    });
</script>

{{-- :::::::::::::::::::::::::::ESCUCHA DE BOTON VISTA PANELES::::::::::::::::::::::::::::::::::::: --}}

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btnPort').forEach(button => {
            button.addEventListener('click', function() {

                const valor = this.dataset.valor;
                //  console.log(valor);
                const modalContent = document.getElementById('modalContent');

                // Mostrar mensaje temporal
                modalContent.textContent = 'Cargando...';
                const url = "{{ url('admin/connections/buscar-valor') }}/" + valor;
                // Hacer fetch
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        let contenido = '';

                        if (data.mensaje) {
                            contenido = `<strong>${valor}</strong><br>${data.mensaje}`;
                        } else if (data.equipo) {
                            contenido = `
                            <b>PUERTO SALIDA: </b><strong>${data.sw}</strong> 
                            <b>--/--</b> 
                            <b>PUERTO  CONEXION: </b><strong>${data.nombre }</strong>
                            <hr>
                            <b>Equipo: </b> ${data.equipo.nombre ?? 'N/A'}<br>
                                <b>Asignado a: </b> ${data.equipo.empleado.nombres} ${data.equipo.empleado.apellidos} <br>
                                <b>IP: </b> ${data.equipo.ip_address} <br>
                                <b>MAC: </b> ${data.equipo.mac_address} <br>
                              
                            `;
                        } else {
                            contenido = 'Sin datos disponibles.';
                        }

                        modalContent.innerHTML = contenido;

                        // Mostrar modal
                        const modal = new bootstrap.Modal(document.getElementById(
                            'infoModal'));
                        modal.show();
                    })

            });
        });
    });
</script> --}}

<script>
$(document).on('click', '.btnPort', function () {

    const valor = $(this).data('valor');

    $('#infoModal').modal('show');

    $('#modalContent').html(`
        <div class="text-center p-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <br>
            <span class="mt-2 d-block">
                Consultando ${valor}...
            </span>
        </div>
    `);

    $.ajax({
        url: "{{ url('netlan/admin/connections/buscar-valor') }}/" + valor,
        type: "GET",
        dataType: "json",

        success: function (respuesta) {

            let html = '';

            const cantidad = respuesta.cantidad || 0;
            const datos = respuesta.datos || [];

            /*
            |--------------------------------------------------------------------------
            | PUERTO LIBRE
            |--------------------------------------------------------------------------
            */

       if (cantidad === 0) {
    html = `
        <div class="alert alert-info text-center">
            <h5>Puerto libre</h5>
            <p>No hay conexiones registradas en este puerto.</p>
        </div>
    `;
}

            /*
            |--------------------------------------------------------------------------
            | PUERTO OCUPADO
            |--------------------------------------------------------------------------
            */

            else {

                html += `
                    <div class="mb-3">

                        <h4>
                            <i class="fas fa-network-wired"></i>
                            ${valor}
                        </h4>

                        <span class="badge badge-success">

                            ${cantidad}

                            ${cantidad === 1
                                ? 'conexión'
                                : 'conexiones'}

                        </span>

                    </div>
                `;

                datos.forEach(function (conexion, index) {

                    const equipo = conexion.equipo || {};
                    const empleado = equipo.empleado || {};
                    const departamento = empleado.departamento || {};

                    const nombreEmpleado =
                        (
                            (empleado.nombres || '') +
                            ' ' +
                            (empleado.apellidos || '')
                        ).trim();

                    html += `

                        <div class="card card-outline card-primary mb-3">

                            <div class="card-header">

                                <h5 class="card-title mb-0">

                                    <b>
                                        Conexión ${index + 1}
                                    </b>

                                </h5>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <p>
                                            <strong>
                                                <i class="fas fa-server"></i>
                                                Puerto Cisco:
                                            </strong>

                                            ${conexion.sw || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-plug"></i>
                                                Patch Panel:
                                            </strong>

                                            ${conexion.nombre || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-desktop"></i>
                                                Equipo:
                                            </strong>

                                            ${equipo.nombre || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-network-wired"></i>
                                                Tipo:
                                            </strong>

                                            ${equipo.tipo || '—'}
                                        </p>

                                    </div>

                                    <div class="col-md-6">

                                        <p>
                                            <strong>
                                                <i class="fas fa-user"></i>
                                                Empleado:
                                            </strong>

                                            ${nombreEmpleado || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-building"></i>
                                                Departamento:
                                            </strong>

                                            ${departamento.name || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-network-wired"></i>
                                                IP:
                                            </strong>

                                            ${equipo.ip_address || '—'}
                                        </p>

                                        <p>
                                            <strong>
                                                <i class="fas fa-barcode"></i>
                                                MAC:
                                            </strong>

                                            ${equipo.mac_address || '—'}
                                        </p>

                                    </div>

                                </div>

                                <hr>

                                <div class="row">

                                    <div class="col-md-6">

                                        <p class="mb-0">

                                            <strong>
                                                Estado:
                                            </strong>

                                            ${
                                                equipo.estado === 'Activo'

                                                ? `
                                                    <span class="badge badge-success">
                                                        Activo
                                                    </span>
                                                  `

                                                : `
                                                    <span class="badge badge-danger">
                                                        Inactivo
                                                    </span>
                                                  `
                                            }

                                        </p>

                                    </div>

                                    <div class="col-md-6">

                                        <p class="mb-0">

                                            <strong>
                                                Comentario:
                                            </strong>

                                            ${conexion.comentario || 'Sin comentario'}

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    `;
                });
            }

            $('#modalContent').html(html);
        },

        error: function (xhr) {

            let mensaje =
                'No fue posible consultar el puerto.';

            if (
                xhr.responseJSON &&
                xhr.responseJSON.mensaje
            ) {
                mensaje = xhr.responseJSON.mensaje;
            }

            $('#modalContent').html(`

                <div class="alert alert-warning text-center">

                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>

                    <h5>
                        ${valor}
                    </h5>

                    <p>
                        ${mensaje}
                    </p>

                </div>

            `);
        }

    });

});

$(document).on('click', '#btnAsignarPuerto', function () {

    const sw = $(this).data('sw');
    const connectionId = $('#conexionSelect').val();

    if (!connectionId) {

        Swal.fire({
            icon: 'warning',
            title: 'Seleccione una conexión',
            text: 'Debe seleccionar una conexión antes de asignar el puerto.'
        });

        return;
    }

    Swal.fire({
        title: '¿Asignar puerto?',
        html: `
            Se asignará la conexión seleccionada al puerto
            <strong>${sw}</strong>.
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({

            url: "{{ url('netlan/admin/connections/asignar-puerto') }}",

            type: "POST",

            data: {
                _token: "{{ csrf_token() }}",
                connection_id: connectionId,
                sw: sw
            },

            dataType: "json",

            beforeSend: function () {

                $('#btnAsignarPuerto')
                    .prop('disabled', true)
                    .html(`
                        <i class="fas fa-spinner fa-spin"></i>
                        Asignando...
                    `);

            },

            success: function (respuesta) {

                Swal.fire({
                    icon: 'success',
                    title: 'Puerto asignado',
                    text: respuesta.mensaje,
                    timer: 1800,
                    showConfirmButton: false
                });

                $('#infoModal').modal('hide');

                /*
                 * Actualizamos visualmente el botón
                 * que acabamos de asignar.
                 */

              const boton = $(
    `.btnPort[data-valor="${sw}"]`
);

if (boton.length) {

    boton
        .removeClass(
            'btn-secondary btn-warning btn-danger'
        )
        .addClass('btn-success');

    /*
     * Buscamos el indicador de cantidad.
     */

    let badge = boton.find('.badge');

    if (badge.length) {

        badge.text('1');

    } else {

        boton.append(`
            <span class="badge badge-light ml-1">
                1
            </span>
        `);

    }
}
                boton
                    .removeClass('btn-secondary btn-warning btn-danger')
                    .addClass('btn-success');

                /*
                 * Mostramos que ahora tiene una conexión.
                 */

                if (boton.find('.badge').length === 0) {

                    boton.append(`
                        <span class="badge badge-light ml-1">
                            1
                        </span>
                    `);

                }

            },

            error: function (xhr) {

                let mensaje =
                    'No fue posible asignar el puerto.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.mensaje
                ) {
                    mensaje = xhr.responseJSON.mensaje;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje
                });

                $('#btnAsignarPuerto')
                    .prop('disabled', false)
                    .html(`
                        <i class="fas fa-link"></i>
                        Asignar puerto
                    `);
            }

        });

    });

});

$(function () {

    $('[data-toggle="tooltip"]').tooltip();

});


$(function () {
    $('.rack-port.btnPort').tooltip({
        container: 'body',
        placement: 'top',
        trigger: 'hover focus',
        html: false
    });

    $(document).on('click', '.rack-port.btnPort', function () {
        $(this).tooltip('hide');
    });
});
</script>

{{-- cambiar estado   --}}
<script>
    $(document).on('click', '.toggle-estado', function() {
        let id = $(this).data('id');

        $.ajax({
            url: '{{ url('admin/connections/cambiar-estado') }}/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire('Éxito', response.message, 'success');
                // $('#miTabla').DataTable().ajax.reload(null, false);
                location.reload();
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cambiar el estado', 'error');
            }
        });
    });
</script>
