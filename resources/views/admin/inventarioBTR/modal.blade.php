<div class="modal fade" id="modalInventario">
    <div class="modal-dialog modal-lg">
        <form id="formInventario" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="method">
            <input type="hidden" name="id" id="id">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Inventario BTR</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- localidad  --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Localidad</label>
                                <select name="localidad" id="localidad" class="form-control">
                                    <option value="">Seleccione</option>
                                    @foreach ($localidades as $loc)
                                        <option value="{{ $loc['nombre'] }}" data-ip="{{ $loc['ip'] }}">
                                            {{ $loc['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Direccion ip    --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>IP</label>
                                <input type="text" id="ip" name="ip" class="form-control" readonly>
                            </div>
                        </div>
                          {{-- Fases    --}}
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>FASES</label>
                                <input type="number" id="fases" name="fases" class="form-control" >
                            </div>
                        </div>
                          {{-- rc-308    --}}
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>RC-308</label>
                                <input type="number" id="rc" name="rc" class="form-control" >
                            </div>
                        </div>
                        {{-- fibra activa  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Fibra Activa</label><br>
                                <label><input type="radio" name="fibra" value="Si" checked> SI</label>
                                <label class="ml-3"><input type="radio" name="fibra" value="No"> NO</label>
                            </div>
                        </div>
                        {{-- ping  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ping</label><br>
                                <label><input type="radio" name="ping" value="Si" checked> SI</label>
                                <label class="ml-3"><input type="radio" name="ping" value="No"> NO</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <Strong>
                                <Center>
                                    <h3>ACCESORIOS PARA CAMARA</h3>
                                </Center>
                            </Strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                    <div class="col-md-1"></div>
    {{-- Camaras --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>CAMARAS</label>
                                <input type="number" id="camaras" name="camaras" class="form-control" >
                            </div>
                        </div>
                     
                        {{-- fuente  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>FUENTE</label><br>
                                <label><input type="radio" name="fuente" value="Si" checked> SI</label>
                                <label class="ml-3"><input type="radio" name="fuente" value="No"> NO</label>
                            </div>
                        </div>
                        {{-- switchPoE  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>SWITCH</label><br>
                                <label><input type="radio" name="switchPoE" value="Si"> SI</label>
                                <label class="ml-3"><input type="radio" name="switchPoE" value="No" checked> NO</label>
                            </div>
                        </div>
                        {{-- xcom  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>X-COM</label><br>
                                <label><input type="radio" name="xcom" value="Si" checked> SI</label>
                                <label class="ml-3"><input type="radio" name="xcom" value="No"> NO</label>
                            </div>
                        </div>
                        {{-- sg116  --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>SG-116</label><br>
                                <label><input type="radio" name="sg116" value="Si" checked> SI</label>
                                <label class="ml-3"><input type="radio" name="sg116" value="No"> NO</label>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
