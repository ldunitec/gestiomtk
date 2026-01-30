{{-- data table  --}}

<script>
    $(function() {
        $("#example1").DataTable({
            "pageLength": 5,
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
            "responsive": false,
            "scrollX": true,
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
                    className: 'btn btn-danger'
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

{{-- // Inicialización de Select2 --}}
<script>
    $(document).ready(function() {
        $('#select2').select2({
            placeholder: "Selecciona un empleado",
            allowClear: true,
            // para que se ajuste al contenedor padre
        });
    });
</script>

{{-- llenado del Editar --}}

<script>
    const portsPanelBaseUrl = "{{ url('admin/ports') }}";

    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var port_number = button.data('port_number');
        var patch_panel_id = button.data('patch_panel_id');

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_patch_panel_id').val(patch_panel_id);
        modal.find('#edit_port_number').val(port_number);

        // Usar la variable JS para construir la acción
        var action = portsPanelBaseUrl + '/' + id;
        modal.find('#editForm').attr('action', action);
    });
</script>
