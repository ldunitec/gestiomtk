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

<script>
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
