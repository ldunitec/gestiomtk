       {{-- // Inicialización de Select2 --}}
       <script>
           $(document).ready(function() {
               $('#select_emp').select2({
                   placeholder: "Selecciona un empleado",
                   allowClear: true,
                   // para que se ajuste al contenedor padre
               });
           });
       </script>
       {{-- dataTable --}}
       <script>
           $(function() {
               $("#equipoActivo").DataTable({
                   "pageLength": 10,
                   "response": true,
                   "order": [
                       [5, 'asc']
                   ],
                   "language": {
                       "emptyTable": "No hay información",
                       "info": "Mostrando _START_ a _END_ de _TOTAL_ equipos",
                       "infoEmpty": "Mostrando 0 a 0 de 0 equipos",
                       "infoFiltered": "(Filtrado de _MAX_ total equipos)",
                       "lengthMenu": "Mostrar _MENU_ equipos",
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
                   buttons: [
                    // {
                    //        text: '<i class="fas fa-copy"></i> COPIAR',
                    //        extend: 'copy',
                    //        className: 'btn btn-default'
                    //    },
                       {
                           text: '<i class="fas fa-file-pdf"></i> PDF',
                           extend: 'pdf',
                           className: 'btn btn-danger'
                       },
                    //    {
                    //        text: '<i class="fas fa-file-csv"></i> CSV',
                    //        extend: 'csv',
                    //        className: 'btn btn-info'
                    //    },
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
               }).buttons().container().appendTo('#equipoActivo_wrapper .row:eq(0)');
           });
       </script>
        <script>
           $(function() {
               $("#equipoInactivo").DataTable({
                   "pageLength": 10,
                   "response": true,
                   "order": [
                       [5, 'asc']
                   ],
                   "language": {
                       "emptyTable": "No hay información",
                       "info": "Mostrando _START_ a _END_ de _TOTAL_ equipos",
                       "infoEmpty": "Mostrando 0 a 0 de 0 equipos",
                       "infoFiltered": "(Filtrado de _MAX_ total equipos)",
                       "lengthMenu": "Mostrar _MENU_ equipos",
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
                   buttons: [
                    // {
                    //        text: '<i class="fas fa-copy"></i> COPIAR',
                    //        extend: 'copy',
                    //        className: 'btn btn-default'
                    //    },
                       {
                           text: '<i class="fas fa-file-pdf"></i> PDF',
                           extend: 'pdf',
                           className: 'btn btn-danger'
                       },
                    //    {
                    //        text: '<i class="fas fa-file-csv"></i> CSV',
                    //        extend: 'csv',
                    //        className: 'btn btn-info'
                    //    },
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
               }).buttons().container().appendTo('#equipoInactivo_wrapper .row:eq(0)');
           });
       </script>
       {{-- cambiar estado   --}}
       <script>
           $(document).on('click', '.toggle-estado', function() {
               let id = $(this).data('id');

               $.ajax({
                   url: '{{ url('admin/equipos/cambiar-estado') }}/' + id,
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
       <script>
           function preguntar{{ $equipo->id }}(event) {
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
                       var form = $('#miFormulario{{ $equipo->id }}');
                       form.submit();
                   }
               });
           }
       </script>
       <script>
           $('#marca_id').on('change', function() {
               let marcaID = $(this).val();
               // alert(marcaID)
               if (marcaID) {
                   $('#modelo_db').css('display', 'none')
                   $('#modelo').css('display', 'block')
                   $.get("{{ url('netlan/admin/equipos/getModelos') }}/" + marcaID, function(data) {
                       $('#modelo').empty().append('<option value="">Seleccione un modelo</option>');
                       data.forEach(getmodelo => {
                           $('#modelo').append('<option value="' + getmodelo.id + '">' + getmodelo
                               .nombre + '</option>');
                       });
                   });
               } else {

                   $('#modelo').empty().append('<option value="">Seleccione un modelo</option>');
               }
               console.log(getmodelo)
           });
       </script>
