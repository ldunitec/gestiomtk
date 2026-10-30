{{-- Modal único de creación de equipos. La edición permanece en edit.blade.php. --}}
<div class="modal fade equipment-create-modal" id="crearEquipoModal" tabindex="-1" role="dialog" aria-labelledby="crearEquipoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <form action="{{ route('netlan.admin.equipos.store') }}" method="POST" id="equipment-create-form">
            @csrf
            <div class="modal-content">
                <header class="modal-header"><div><div class="eq-eyebrow">MEGATK · Inventario de equipos</div><h5 class="modal-title" id="crearEquipoModalLabel">Nuevo equipo</h5><p>Registra la asignación, la conexión de red y las características del equipo.</p></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></header>
                <div class="modal-body">
        <section class="eq-section" aria-labelledby="eq-identification-title">
            <header class="eq-section-header"><span class="eq-section-number">01</span><div><h3 id="eq-identification-title">Identificación y asignación</h3><p>Responsable y datos generales del equipo.</p></div></header>
            <div class="eq-field-grid">
                <div class="eq-field"><label for="eq-create-select_emp">Asignado a</label>
                    <select name="empleado_id" id="eq-create-select_emp" class="form-control select2 select_emp @error('empleado_id') is-invalid @enderror">
                        <option value="">Sin asignación</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id }}" {{ (string) old('empleado_id') === (string) $empleado->id ? 'selected' : '' }}>{{ $empleado->nombres . ' ' . $empleado->apellidos }}</option>
                        @endforeach
                    </select>
                    @error('empleado_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
<div class="eq-field"><label for="eq-create-nombre">Nombre del equipo <span class="eq-required">*</span></label><input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="eq-create-nombre" value="{{ old('nombre', '') }}" required>@error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="eq-create-serial_number">Número de serie <span class="eq-required">*</span></label><input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" id="eq-create-serial_number" value="{{ old('serial_number', '') }}" required>@error('serial_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="eq-create-tipo">Tipo de equipo</label>
 @php $selectedValue = (string) old('tipo', 'PC'); $fieldOptions = ['PC'=>'PC', 'TELEFONO'=>'Teléfono', 'IMPRESORA'=>'Impresora', 'CAMARA'=>'Cámara', 'BIOMETRICO'=>'Biométrico', 'POS'=>'POS']; @endphp
 <select name="tipo" id="eq-create-tipo" class="form-control @error('tipo') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('tipo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="eq-field"><label for="marca_id">Marca</label>
                    <select name="marca_id" id="marca_id" class="form-control @error('marca_id') is-invalid @enderror">
                        <option value="">Seleccione una opción</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}" {{ (string) old('marca_id') === (string) $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                    @error('marca_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="eq-field"><label for="modelo">Modelo</label>
                    {{-- El parcial JS existente carga las opciones al cambiar #marca_id. --}}
                    <select name="modelo_id" id="modelo" class="form-control @error('modelo_id') is-invalid @enderror" data-old-model="{{ old('modelo_id') }}">
                        <option value="">Seleccione un modelo</option>
                        @foreach (($modelos ?? []) as $modelo)
                            @if ((string) old('modelo_id') === (string) $modelo->id)
                                <option value="{{ $modelo->id }}" selected>{{ $modelo->nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('modelo_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>
        <section class="eq-section" aria-labelledby="eq-network-title">
            <header class="eq-section-header"><span class="eq-section-number">02</span><div><h3 id="eq-network-title">Conexión de red</h3><p>Direcciones utilizadas para identificar el equipo en la red.</p></div></header>
            <div class="eq-field-grid">
<div class="eq-field"><label for="eq-create-ip_address">Dirección IP <span class="eq-required">*</span></label><input type="text" class="form-control @error('ip_address') is-invalid @enderror" name="ip_address" id="eq-create-ip_address" value="{{ old('ip_address', '') }}" required>@error('ip_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror<small class="eq-help">Ejemplo: 10.10.10.50</small></div><div class="eq-field"><label for="eq-create-mac_address">Dirección MAC <span class="eq-required">*</span></label><input type="text" class="form-control @error('mac_address') is-invalid @enderror" name="mac_address" id="eq-create-mac_address" value="{{ old('mac_address', '') }}" required>@error('mac_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror<small class="eq-help">Ejemplo: 00:11:22:33:44:55</small></div></div></section>
        <section class="eq-section" aria-labelledby="eq-status-title">
            <header class="eq-section-header"><span class="eq-section-number">03</span><div><h3 id="eq-status-title">Estado y protección</h3><p>Estado registrado del equipo y de su antivirus.</p></div></header>
            <div class="eq-field-grid">
<div class="eq-field"><label for="eq-create-estado">Estado del equipo</label>
 @php $selectedValue = (string) old('estado', 'Activo'); $fieldOptions = ['Activo'=>'Activo', 'Inactivo'=>'Inactivo']; @endphp
 <select name="estado" id="eq-create-estado" class="form-control @error('estado') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="eq-create-antivirus">Estado del antivirus</label>
 @php $selectedValue = (string) old('antivirus', 'Activo'); $fieldOptions = ['Activo'=>'Activo', 'Inactivo'=>'Inactivo', 'N/A'=>'No aplica']; @endphp
 <select name="antivirus" id="eq-create-antivirus" class="form-control @error('antivirus') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('antivirus')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div></div></section>
        <section class="eq-section" aria-labelledby="eq-hardware-title">
            <header class="eq-section-header"><span class="eq-section-number">04</span><div><h3 id="eq-hardware-title">Características de hardware</h3><p>Procesador, memoria y almacenamiento registrados.</p></div></header>
            <div class="eq-field-grid eq-three-columns">
<div class="eq-field"><label for="eq-create-procesador">Procesador</label>
 @php $selectedValue = (string) old('procesador', 'i5'); $fieldOptions = ['i3'=>'Intel Core i3', 'i5'=>'Intel Core i5', 'i7'=>'Intel Core i7', 'i9'=>'Intel Core i9', 'Rizen 5'=>'Ryzen 5', 'Rizen 7'=>'Ryzen 7', 'N/A'=>'No aplica']; @endphp
 <select name="procesador" id="eq-create-procesador" class="form-control @error('procesador') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('procesador')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="eq-create-ram">Memoria RAM</label>
 @php $selectedValue = (string) old('ram', '4 GB'); $fieldOptions = ['4 GB'=>'4 GB', '6 GB'=>'6 GB', '8 GB'=>'8 GB', '16 GB'=>'16 GB', '24 GB'=>'24 GB', '32 GB'=>'32 GB', 'N/A'=>'No aplica']; @endphp
 <select name="ram" id="eq-create-ram" class="form-control @error('ram') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('ram')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="eq-create-almacenamiento">Almacenamiento</label>
 @php $selectedValue = (string) old('almacenamiento', '240 SSD'); $fieldOptions = ['240 SSD'=>'240 GB · SSD', '500 SSD'=>'500 GB · SSD', '500 HDD'=>'500 GB · HDD', '1TB SDD'=>'1 TB · SSD', '1TB HHD'=>'1 TB · HDD', '2TB SSD'=>'2 TB · SSD', 'N/A'=>'No aplica']; @endphp
 <select name="almacenamiento" id="eq-create-almacenamiento" class="form-control @error('almacenamiento') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Seleccione una opción</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('almacenamiento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div></div></section>
                </div>
                <footer class="modal-footer"><span class="eq-footnote"><span class="eq-required">*</span> Campos obligatorios</span><button type="button" class="btn eq-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn eq-save"><i class="fas fa-save" aria-hidden="true"></i> Guardar equipo</button></footer>
            </div>
        </form>
    </div>
</div>
<style>


.equipment-create-modal { --eq-ink:#152536; --eq-muted:#657687; --eq-line:#dbe3eb; max-width:1200px; margin:0 auto; padding:8px 0 28px; color:var(--eq-ink); }
.equipment-create-modal * { box-sizing:border-box; }
.equipment-create-modal .eq-intro { display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:22px; }
.equipment-create-modal .eq-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:7px; }
.equipment-create-modal h2 { margin:0 0 6px; font-size:25px; font-weight:700; letter-spacing:-.5px; }
.equipment-create-modal .eq-intro p { font-size:13px; color:var(--eq-muted); margin:0; }
.equipment-create-modal .btn { font-size:13px; padding:10px 15px; border-radius:7px; font-weight:600; }
.equipment-create-modal .eq-back { color:#425f74; background:#fff; border:1px solid #cbd8e3; display:inline-flex; align-items:center; gap:8px; }
.equipment-create-modal .eq-back:hover { background:#eff5f8; }
.equipment-create-modal .eq-identity { display:flex; align-items:center; gap:14px; padding:20px; background:#fff; border:1px solid var(--eq-line); border-radius:11px; margin-bottom:20px; }
.equipment-create-modal .eq-device-icon { width:45px; height:45px; flex:0 0 45px; display:flex; align-items:center; justify-content:center; background:#eaf5f5; color:#087f8c; border-radius:10px; font-size:19px; }
.equipment-create-modal .eq-identity strong { display:block; font-size:16px; margin-bottom:4px; overflow-wrap:anywhere; }
.equipment-create-modal .eq-identity div>span { font-size:12px; color:var(--eq-muted); }
.equipment-create-modal .eq-current-state { margin-left:auto; font-size:11px; font-weight:600; padding:5px 10px; border:1px solid #dbe2e9; border-radius:20px; white-space:nowrap; }
.equipment-create-modal .eq-current-state.is-active { background:#e9f8ef; color:#18734d; border-color:#c6e9d4; }
.equipment-create-modal .eq-current-state.is-inactive { background:#f1f3f6; color:#647484; }
.equipment-create-modal .eq-section { background:#fff; border:1px solid var(--eq-line); border-radius:11px; margin-bottom:18px; overflow:hidden; box-shadow:0 4px 14px #15253605; }
.equipment-create-modal .eq-section-header { padding:18px 22px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #e7edf3; }
.equipment-create-modal .eq-section-number { width:31px; height:31px; flex:0 0 31px; display:flex; align-items:center; justify-content:center; color:#087f8c; background:#eef7f8; border:1px solid #daebee; border-radius:8px; font-size:11px; font-weight:700; }
.equipment-create-modal h3 { font-size:15px; font-weight:700; margin:0 0 4px; }
.equipment-create-modal .eq-section-header p { margin:0; font-size:12px; color:var(--eq-muted); }
.equipment-create-modal .eq-field-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px 24px; padding:23px; }
.equipment-create-modal .eq-three-columns { grid-template-columns:repeat(3,minmax(0,1fr)); }
.equipment-create-modal .eq-field { min-width:0; }
.equipment-create-modal .eq-field label { display:block; color:#405b6e; font-size:12px; font-weight:600; margin-bottom:8px; }
.equipment-create-modal .eq-field .form-control { width:100%; height:43px; border:1px solid #cddbe6; border-radius:7px; color:#294357; font-size:13px; background:#fff; padding:9px 12px; }
.equipment-create-modal .eq-field .form-control:focus { border-color:#1597a5; box-shadow:0 0 0 3px #087f8c15; outline:none; }
.equipment-create-modal .eq-field .form-control.is-invalid { border-color:#cf5c70; }
.equipment-create-modal #eq-create-ip_address,.equipment-create-modal #eq-create-mac_address { font-family:ui-monospace,SFMono-Regular,Consolas,monospace; }
.equipment-create-modal .eq-required { color:#c14e62; }
.equipment-create-modal .eq-help { display:block; margin-top:6px; color:#8193a1; font-size:11px; }
.equipment-create-modal .eq-errors { background:#fff3f5; border:1px solid #efd0d8; border-radius:9px; color:#a33e52; font-size:13px; padding:16px 20px; margin-bottom:20px; }
.equipment-create-modal .eq-errors ul { margin:8px 0 0; padding-left:20px; }
.equipment-create-modal .invalid-feedback { font-size:11px; margin-top:6px; }
.equipment-create-modal .eq-form-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; padding:8px 0; color:var(--eq-muted); font-size:12px; }
.equipment-create-modal .eq-save-actions { display:flex; gap:9px; }
.equipment-create-modal .eq-cancel { background:#fff; border:1px solid #cbd8e3; color:#526b7e; }
.equipment-create-modal .eq-save { background:#087f8c; border:1px solid #087f8c; color:#fff; display:inline-flex; align-items:center; gap:8px; }
.equipment-create-modal .eq-save:hover { background:#066875; }
.equipment-create-modal a:focus-visible,.equipment-create-modal button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
.equipment-create-modal .select2-container { width:100%!important; }
.equipment-create-modal .select2-container .select2-selection--single { min-height:43px; border:1px solid #cddbe6; border-radius:7px; }
.equipment-create-modal .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:41px; padding-left:12px; color:#294357; font-size:13px; }
.equipment-create-modal .select2-container--default .select2-selection--single .select2-selection__arrow { height:41px; }
@media(max-width:767px) { .equipment-create-modal .eq-three-columns { grid-template-columns:1fr; } }
@media(max-width:575px) { .equipment-create-modal h2 { font-size:22px; } .equipment-create-modal .eq-field-grid { grid-template-columns:1fr; padding:18px; gap:18px; } .equipment-create-modal .eq-section-header { padding:16px 18px; } .equipment-create-modal .eq-identity { flex-wrap:wrap; padding:16px; gap:10px; } .equipment-create-modal .eq-current-state { margin-left:55px; } .equipment-create-modal .eq-intro { gap:14px; } .equipment-create-modal .eq-form-footer { align-items:flex-start; } .equipment-create-modal .eq-save-actions { width:100%; } .equipment-create-modal .eq-save-actions .btn { flex:1; text-align:center; justify-content:center; } }
    
.equipment-create-modal { max-width:none; padding:0; margin:0; }
.equipment-create-modal .modal-dialog { max-width:1000px; }
.equipment-create-modal .modal-content { border:0; border-radius:12px; overflow:hidden; background:#f1f5f8; color:#152536; }
.equipment-create-modal .modal-header { padding:22px 25px; background:#fff; border-bottom:1px solid #e2eaf0; display:flex; align-items:flex-start; }
.equipment-create-modal .modal-title { font-size:22px; font-weight:700; margin:0; }
.equipment-create-modal .modal-header p { color:#657687; font-size:12px; margin:6px 0 0; }
.equipment-create-modal .modal-header .close { color:#657687; opacity:1; padding:12px; margin:-7px -9px 0 10px; }
.equipment-create-modal .modal-body { padding:22px; }
.equipment-create-modal .eq-section:last-child { margin-bottom:0; }
.equipment-create-modal .modal-footer { background:#fff; border-top:1px solid #e2eaf0; padding:15px 25px; display:flex; flex-wrap:wrap; gap:9px; align-items:center; }
.equipment-create-modal .modal-footer>.eq-footnote { margin-right:auto; font-size:11px; color:#657687; }
.equipment-create-modal .modal-footer .btn { margin:0; }
@media(max-width:575px) { .equipment-create-modal .modal-dialog { margin:8px; } .equipment-create-modal .modal-header { padding:18px; } .equipment-create-modal .modal-body { padding:12px; } .equipment-create-modal .modal-footer { padding:15px 18px; } .equipment-create-modal .eq-footnote { width:100%; margin-bottom:3px; } }
</style>
<script>

(function () {
    function initializeEquipmentCreateModal() {
        if (typeof window.jQuery === 'undefined') return;
        var $ = window.jQuery, $modal = $('#crearEquipoModal');
        var model = $modal.find('#modelo')[0];
        if (!model) return;
        var pendingModel = model.getAttribute('data-old-model') || '';
        var $brand = $modal.find('#marca_id');
        function restoreModel() {
            if (!pendingModel) return;
            var available = Array.prototype.some.call(model.options, function (option) { return option.value === pendingModel; });
            if (available) {
                $(model).val(pendingModel).trigger('change');
                pendingModel = '';
            }
        }
        var observer = new MutationObserver(restoreModel);
        observer.observe(model, { childList:true, subtree:true });
        // Al cambiar la marca por elección del usuario no se recupera el modelo anterior.
        $brand.on('change.equipmentCreateView', function (event) {
            if (event.originalEvent) pendingModel = '';
        });
        $modal.on('shown.bs.modal.equipmentCreateView', function () {
            if ($.fn.select2) {
                $modal.find('select.select2').each(function () {
                    var $select = $(this);
                    if (!$select.data('select2')) $select.select2({ dropdownParent:$modal, width:'100%' });
                });
            }
            restoreModel();
        });
        // El cargador de modelos permanece en el parcial JS del proyecto.
        restoreModel();
        if (pendingModel && $brand.val()) {
            // Deja que el parcial registre sus manejadores antes de recuperar las opciones.
            window.setTimeout(function () { $brand.trigger('change'); }, 0);
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeEquipmentCreateModal);
    else initializeEquipmentCreateModal();
})();
</script>
