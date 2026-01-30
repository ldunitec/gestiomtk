  {{-- boton eliminar --}}
  <script>
      function preguntar{{ $marca->id }}(event) {
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
                  var form = $('#miFormulario{{ $marca->id }}');
                  form.submit();
              }
          });
      }
  </script>

  {{-- datatable --}}
     <script>
        $(function() {
            $("#example1").DataTable({
                "pageLength": 10,
                // "order":[1,'asc'],
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ marcas",
                    "infoEmpty": "Mostrando 0 a 0 de 0 marcas",
                    "infoFiltered": "(Filtrado de _MAX_ total marcas)",
                    "lengthMenu": "Mostrar _MENU_ marcas",
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

    {{-- llenado del Editar --}}

     <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nombre = button.data('nombre');

            var modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_nombre').val(nombre);

            // Actualizar la acción del formulario dinámicamente
            var action = '{{ url('admin/marcas') }}/' + id;
            modal.find('#editForm').attr('action', action);
        });
    </script>
