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
{{-- boton eliminar  --}}
<script>
    function preguntar{{ $patch_panel->id }}(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Desea eliminar esta registro?',
            text: '',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: 'Eliminar',
            confirmButtonColor: '#a5161d',
            denyButtonColor: '#270a0a',
            denyButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#miFormulario{{ $patch_panel->id }}');
                form.submit();
            }
        });
    }
</script>

  {{-- llenado del Editar --}}

  <script>
    const patchPanelBaseUrl = "{{ url('admin/patch_panels') }}";

    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var location = button.data('location');
        var total_ports = button.data('total_ports');

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_location').val(location);
        modal.find('#edit_name').val(name);
        modal.find('#edit_total_ports').val(total_ports);

        // Usar la variable JS para construir la acción
        var action = patchPanelBaseUrl + '/' + id;
        modal.find('#editForm').attr('action', action);
    });
</script>

