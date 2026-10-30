{{-- Crear marca --}}
<div class="modal fade brand-modal" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearMarcaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('netlan.admin.marcas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div><span class="bm-eyebrow">MEGATK · Catálogo de equipos</span><h5 class="modal-title" id="crearMarcaModalLabel">Nueva marca</h5><p>Agrega una marca al catálogo del inventario.</p></div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="create_nombre">Nombre de la marca</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="create_nombre" value="{{ old('nombre') }}" placeholder="Ejemplo: Cisco, TP-Link, Dell" required>
                        @error('nombre')<div class="invalid-feedback d-block" role="alert">{{ $message }}</div>@enderror
                        <small class="bm-help">Utiliza el nombre comercial de la marca.</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn bm-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn bm-save"><i class="fas fa-save" aria-hidden="true"></i> Guardar marca</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Editar marca: el parcial JS existente asigna la URL a #editForm y llena los campos. --}}
<div class="modal fade brand-modal" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editMarcaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <div><span class="bm-eyebrow">MEGATK · Catálogo de equipos</span><h5 class="modal-title" id="editMarcaModalLabel">Editar marca</h5><p>Actualiza el nombre de la marca seleccionada.</p></div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group mb-0"><label for="edit_nombre">Nombre de la marca</label><input type="text" class="form-control" name="nombre" id="edit_nombre" required><small class="bm-help">Revisa el nombre antes de guardar los cambios.</small></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn bm-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn bm-save"><i class="fas fa-save" aria-hidden="true"></i> Guardar cambios</button></div>
            </div>
        </form>
    </div>
</div>

<style>
/* Ancho del modal: cambia max-width para hacerlo más ancho o más estrecho. */
.brand-modal .modal-dialog { max-width:540px; }
.brand-modal .modal-content { border:0; border-radius:12px; overflow:hidden; color:#152536; box-shadow:0 16px 55px #14263830; }
/* Color de la cabecera: cambia background y color. */
.brand-modal .modal-header { background:#f8fbfc; color:#152536; border-bottom:1px solid #e2eaf0; padding:22px; align-items:flex-start; }
.brand-modal .bm-eyebrow { display:block; font-size:10px; font-weight:700; letter-spacing:1.5px; color:#087f8c; text-transform:uppercase; margin-bottom:7px; }
.brand-modal .modal-title { font-size:21px; font-weight:700; margin:0; line-height:1.2; }
.brand-modal .modal-header p { font-size:12px; color:#657687; margin:6px 0 0; }
.brand-modal .modal-header .close { color:#657687; opacity:1; font-weight:400; padding:12px; margin:-7px -9px 0 10px; }
.brand-modal .modal-body { background:#fff; padding:24px 22px; }
.brand-modal label { display:block; font-size:12px; font-weight:600; color:#405b6e; margin-bottom:8px; }
.brand-modal .form-control { min-height:43px; border:1px solid #cddbe6; border-radius:7px; padding:9px 12px; font-size:14px; color:#294357; }
.brand-modal .form-control:focus { border-color:#1597a5; box-shadow:0 0 0 3px #087f8c15; }
.brand-modal .form-control.is-invalid { border-color:#cf5c70; }
.brand-modal .invalid-feedback { font-size:11px; }
.brand-modal .bm-help { display:block; font-size:11px; color:#8193a1; margin-top:7px; }
.brand-modal .modal-footer { background:#f8fafc; border-top:1px solid #e2eaf0; padding:15px 22px; gap:8px; }
.brand-modal .modal-footer .btn { font-size:12px; font-weight:600; border-radius:7px; padding:10px 14px; margin:0; }
.brand-modal .bm-cancel { color:#526b7e; background:#fff; border:1px solid #cbd8e3; }
/* Color del botón Guardar. */
.brand-modal .bm-save { color:#fff; background:#087f8c; border:1px solid #087f8c; }
.brand-modal .bm-save:hover { background:#066875; border-color:#066875; }
.brand-modal .bm-save i { margin-right:5px; }
.brand-modal button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
@media(max-width:575px) { .brand-modal .modal-dialog { margin:12px; } .brand-modal .modal-header { padding:19px; } .brand-modal .modal-body { padding:22px 19px; } .brand-modal .modal-footer { padding:15px 19px; } }
</style>
