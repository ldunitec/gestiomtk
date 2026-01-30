{{-- modal archivo   --}}
<div class="modal fade" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="modalFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalFileLabel">Agregar Archivo</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="brand_id">Marca</label>
                                <select name="brand_id" id="brand" class="form-control" required>
                                    <option value="">Seleccione una marca</option>

                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label>Color</label>
                                <select name="color_primary" id="colorSelect" class="form-control">
                                    <option value="">Seleccione un color</option>
                                    <option value="primary">Azul (Primary)</option>
                                    <option value="secondary">Gris (Secondary)</option>
                                    <option value="success">Verde (Success)</option>
                                    <option value="danger">Rojo (Danger)</option>
                                    <option value="warning">Amarillo (Warning)</option>
                                    <option value="info">Celeste (Info)</option>
                                    <option value="dark">Negro (Dark)</option>
                                </select>

                                <div id="colorPreview" class="mt-1 p-1 text-white rounded">
                                    
                                </div>

                                <script>
                                    const select = document.getElementById('colorSelect');
                                    const preview = document.getElementById('colorPreview');

                                    select.addEventListener('change', function() {
                                        preview.className = 'mt-1 p-1 text-white rounded bg-' + this.value;
                                    });
                                </script>

                            </div>
                        </div> --}}
                        <div class="col-md-4 mb-3">
                            <label>Categoría</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="">Seleccione una categoria</option>
                                <option value="ALL-IN-ONE"> ALL-IN-ONE </option>
                                <option value="BIOMETRICO"> BIOMETRICO </option>
                                <option value="ESCANNER"> ESCANNER </option>
                                <option value="IMPRESORA"> IMPRESORA </option>
                                <option value="IMPRESORA POS"> IMPRESORA POS </option>
                                <option value="SOFTWARE"> SOFTWARE </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Modelo</label>
                            <input name="model" class="form-control" placeholder="Modelo" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Nombre</label>
                            <input name="name" class="form-control" placeholder="Nombre" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipo</label>
                            <select name="descrip" class="form-control" required>
                                <option value="">Seleccione</option>
                                <option value="Driver">Driver</option>
                                <option value="Firmware">Firmware</option>
                                <option value="Manual">Manual</option>
                                <option value="Tool">Tool</option>
                                <option value="Software">Software</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Enlace Cloud</label>
                            <input name="link_drive" class="form-control" placeholder="Link Google Drive" required>
                        </div>



                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
{{-- modal marca   --}}
<div class="modal fade" id="modalBrand" tabindex="-1" role="dialog" aria-labelledby="modalBrandLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="modalBrandLabel">Agregar Marca</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="brand_id">Marca</label>
                                <input name="name" class="form-control" placeholder="Nombre de Marca" required>

                            </div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label>Color</label>
                                <select name="color_primary" id="colorSelect" class="form-control">
                                    <option value="">Seleccione un color</option>
                                    <option value="primary">Azul (Primary)</option>
                                    <option value="secondary">Gris (Secondary)</option>
                                    <option value="success">Verde (Success)</option>
                                    <option value="danger">Rojo (Danger)</option>
                                    <option value="warning">Amarillo (Warning)</option>
                                    <option value="info">Celeste (Info)</option>
                                    <option value="dark">Negro (Dark)</option>
                                </select>

                                <div id="colorPreview" class="mt-1 p-1 text-white rounded">

                                </div>

                                <script>
                                    const select = document.getElementById('colorSelect');
                                    const preview = document.getElementById('colorPreview');

                                    select.addEventListener('change', function() {
                                        preview.className = 'mt-1 p-1 text-white rounded bg-' + this.value;
                                    });
                                </script>

                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Logo</label>
                            <input name="logo_url " class="form-control" placeholder="URL de logo" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
