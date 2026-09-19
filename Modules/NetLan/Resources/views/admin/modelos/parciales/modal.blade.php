   <!-- Modal create -->
   <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearModeloModalLabel"
       aria-hidden="true">
       <div class="modal-dialog" role="document">
           <form action="{{ route('netlan.admin.modelos.store') }}" method="POST">
               @csrf
               <div class="modal-content">
                   <div class="modal-header bg-primary">
                       <h5 class="modal-title" id="crearModeloModalLabel">Agregar Nueva modelo</h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                           <span aria-hidden="true">&times;</span>
                       </button>
                   </div>
                   <div class="modal-body">
                       <div class="form-group">
                           <label for="nombre">Marca</label>
                           <select name="marca_id" id="marca_id" class="form-control">
                               @foreach ($marcas as $marca)
                                   <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                               @endforeach
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="nombre">Nombre de la modelo</label>
                           <input type="text" class="form-control" name="nombre" required>
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                       <button type="submit" class="btn btn-primary">Guardar</button>
                   </div>
               </div>
           </form>
       </div>
   </div>

   {{-- modal edit  --}}
   <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editmodeloModalLabel"
       aria-hidden="true">
       <div class="modal-dialog" role="document">
           <form id="editForm" method="POST">
               @csrf
               @method('PUT')
               <div class="modal-content">
                   <div class="modal-header bg-success">
                       <h5 class="modal-title" id="crearModeloModalLabel"><b>Modificar el modelo</b></h5>

                   </div>
                   <div class="modal-body">
                       <input type="" name="id" id="edit_id">
                       {{-- <input type="hidden" name="id" id="marca_id"> --}}
                       <div class="form-group">
                           <label for="nombre">Marca</label>
                           <select name="marca_id" id="edit_marca" class="form-control">
                               @foreach ($marcas as $marca)
                                   <option value="{{ $marca->id }}" >
                                       {{ $marca->nombre }}
                                       {{-- {{ $modelo->marca_id == $marca->id ? 'selected' : '' }} --}}
                                   </option>
                               @endforeach
                           </select>
                       </div>
                       <div class="form-group">
                           <label for="nombre">Nombre del modelo</label>
                           <input type="text" class="form-control" name="nombre" id="edit_nombre">
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                       <button type="submit" class="btn btn-success">Guardar</button>
                   </div>
               </div>
           </form>
       </div>
   </div>
