@extends('adminlte::page')
@section('title', 'Editar equipo')
@section('content_header')
    <h1>Editar equipo</h1>
@stop
@section('content')
<div class="net-equipment-edit">
    <header class="eq-intro">
        <div><div class="eq-eyebrow">MEGATK · Inventario de equipos</div><h2>Editar equipo</h2><p>Actualiza la asignación, los datos de red y las características del equipo.</p></div>
        <a href="{{ url('netlan/admin/equipos') }}" class="btn eq-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al inventario</a>
    </header>
    <div class="eq-identity"><span class="eq-device-icon"><i class="fas fa-desktop" aria-hidden="true"></i></span><div><strong>{{ $equipo->nombre ?: 'Equipo sin nombre' }}</strong><span>{{ $equipo->tipo ?: 'Sin tipo' }} · Serie: {{ $equipo->serial_number ?: 'Sin registrar' }}</span></div><span class="eq-current-state {{ $equipo->estado === 'Activo' ? 'is-active' : 'is-inactive' }}">{{ $equipo->estado ?: 'Sin estado' }}</span></div>
    @if ($errors->any())
        <div class="eq-errors" role="alert"><strong>Revisa los datos antes de guardar.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form id="equipment-edit-form" action="{{ url('netlan/admin/equipos', $equipo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <section class="eq-section" aria-labelledby="eq-identification-title">
            <header class="eq-section-header"><span class="eq-section-number">01</span><div><h3 id="eq-identification-title">Identificación y asignación</h3><p>Responsable y datos generales del equipo.</p></div></header>
            <div class="eq-field-grid">
                <div class="eq-field"><label for="select_emp">Asignado a</label>
                    <select name="empleado_id" id="select_emp" class="form-control select2 @error('empleado_id') is-invalid @enderror">
                        <option value="">Sin asignación</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id }}" {{ (string) old('empleado_id', optional($equipo->empleado)->id) === (string) $empleado->id ? 'selected' : '' }}>{{ $empleado->nombres . ' ' . $empleado->apellidos }}</option>
                        @endforeach
                    </select>
                    @error('empleado_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
<div class="eq-field"><label for="nombre">Nombre del equipo <span class="eq-required">*</span></label><input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="nombre" value="{{ old('nombre', $equipo->nombre) }}" required>@error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="serial_number">Número de serie <span class="eq-required">*</span></label><input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" id="serial_number" value="{{ old('serial_number', $equipo->serial_number) }}" required>@error('serial_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="tipo">Tipo de equipo</label>
 @php $selectedValue = (string) old('tipo', $equipo->tipo); $fieldOptions = ['PC'=>'PC', 'ROUTER'=>'Router', 'TELEFONO'=>'Teléfono', 'IMPRESORA'=>'Impresora', 'CAMARA'=>'Cámara', 'BIOMETRICO'=>'Biométrico', 'POS'=>'POS']; @endphp
 <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('tipo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="eq-field"><label for="marca_id">Marca</label>
                    <select name="marca_id" id="marca_id" class="form-control @error('marca_id') is-invalid @enderror">
                        <option value="">Sin especificar</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}" {{ (string) old('marca_id', optional($equipo->marca)->id) === (string) $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                    @error('marca_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="eq-field"><label id="eq-model-label" for="modelo_db">Modelo</label>
                    {{-- Se conservan ambos IDs para el cargador de modelos del parcial existente.
                         El script habilita únicamente el selector visible para evitar envíos duplicados. --}}
                    <select name="modelo_id" id="modelo_db" class="form-control @error('modelo_id') is-invalid @enderror" style="display:block">
                        <option value="">Sin especificar</option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->id }}" {{ (string) old('modelo_id', $equipo->modelo_id) === (string) $modelo->id ? 'selected' : '' }}>{{ $modelo->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="modelo_id" id="modelo" class="form-control @error('modelo_id') is-invalid @enderror" style="display:none" disabled>
                        <option value="">Seleccione un modelo</option>
                    </select>
                    @error('modelo_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>
        <section class="eq-section" aria-labelledby="eq-network-title">
            <header class="eq-section-header"><span class="eq-section-number">02</span><div><h3 id="eq-network-title">Conexión de red</h3><p>Direcciones utilizadas para identificar el equipo en la red.</p></div></header>
            <div class="eq-field-grid">
<div class="eq-field"><label for="ip_address">Dirección IP <span class="eq-required">*</span></label><input type="text" class="form-control @error('ip_address') is-invalid @enderror" name="ip_address" id="ip_address" value="{{ old('ip_address', $equipo->ip_address) }}" required>@error('ip_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror<small class="eq-help">Ejemplo: 10.10.10.50</small></div><div class="eq-field"><label for="mac_address">Dirección MAC <span class="eq-required">*</span></label><input type="text" class="form-control @error('mac_address') is-invalid @enderror" name="mac_address" id="mac_address" value="{{ old('mac_address', $equipo->mac_address) }}" required>@error('mac_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror<small class="eq-help">Ejemplo: 00:11:22:33:44:55</small></div></div></section>
        <section class="eq-section" aria-labelledby="eq-status-title">
            <header class="eq-section-header"><span class="eq-section-number">03</span><div><h3 id="eq-status-title">Estado y protección</h3><p>Estado registrado del equipo y de su antivirus.</p></div></header>
            <div class="eq-field-grid">
<div class="eq-field"><label for="estado">Estado del equipo</label>
 @php $selectedValue = (string) old('estado', $equipo->estado); $fieldOptions = ['Activo'=>'Activo', 'Inactivo'=>'Inactivo']; @endphp
 <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="antivirus">Estado del antivirus</label>
 @php $selectedValue = (string) old('antivirus', $equipo->antivirus); $fieldOptions = ['Activo'=>'Activo', 'Inactivo'=>'Inactivo']; @endphp
 <select name="antivirus" id="antivirus" class="form-control @error('antivirus') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('antivirus')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div></div></section>
        <section class="eq-section" aria-labelledby="eq-hardware-title">
            <header class="eq-section-header"><span class="eq-section-number">04</span><div><h3 id="eq-hardware-title">Características de hardware</h3><p>Procesador, memoria y almacenamiento registrados.</p></div></header>
            <div class="eq-field-grid eq-three-columns">
<div class="eq-field"><label for="procesador">Procesador</label>
 @php $selectedValue = (string) old('procesador', $equipo->procesador); $fieldOptions = ['i3'=>'Intel Core i3', 'i5'=>'Intel Core i5', 'i7'=>'Intel Core i7', 'i9'=>'Intel Core i9', 'Rizen 5'=>'Ryzen 5', 'Rizen 7'=>'Ryzen 7']; @endphp
 <select name="proce{{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('ram')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="almacenamiento">Almacenamiento</label>
 @php $selectedValue = (string) old('almacenamiento', $equipo->almacenamiento); $fieldOptions = ['240 SSD'=>'240 GB · SSD', '500 SSD'=>'500 GB · SSD', '500 HDD'=>'500 GB · HDD', '1TB SDD'=>'1 TB · SSD', '1TB HHD'=>'1 TB · HDD', '2TB SSD'=>'2 TB · SSD']; @endphp
 <select name="almacenamiento" id="almacenamiento" class="form-control @error('almacenamiento') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('almacenamiento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div></div></section>
        <footer class="eq-form-footer"><span><span class="eq-required">*</span> Campos obligatorios</span><div class="eq-save-actions"><a href="{{ url('netlan/admin/equipos') }}" class="btn eq-cancel">Cancelar</a><button type="submit" class="btn eq-save"><i class="fas fa-save" aria-hidden="true"></i> Guardar cambios</button></div></footer>
    </form>
</div>
@stopsador" id="procesador" class="form-control @error('procesador') is-invalid @enderror">
 <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>Sin especificar</option>
 @if ($selectedValue !== '' && !array_key_exists($selectedValue, $fieldOptions))<option value="{{ $selectedValue }}" selected>{{ $selectedValue }}</option>@endif
 @foreach ($fieldOptions as $optionValue => $optionLabel)<option value="{{ $optionValue }}" {{ $selectedValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>@endforeach
 </select>@error('procesador')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div><div class="eq-field"><label for="ram">Memoria RAM</label>
 @php $selectedValue = (string) old('ram', $equipo->ram); $fieldOptions = ['4 GB'=>'4 GB', '5 GB'=>'5 GB', '8 GB'=>'8 GB', '16 GB'=>'16 GB', '24 GB'=>'24 GB', '32 GB'=>'32 GB']; @endphp
 <select name="ram" id="ram" class="form-control @error('ram') is-invalid @enderror">
 <option value="" 
@section('css')
    @include('netlan::admin.equipos.parciales.css')
    <style>

.net-equipment-edit { --eq-ink:#152536; --eq-muted:#657687; --eq-line:#dbe3eb; max-width:1200px; margin:0 auto; padding:8px 0 28px; color:var(--eq-ink); }
.net-equipment-edit * { box-sizing:border-box; }
.net-equipment-edit .eq-intro { display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:22px; }
.net-equipment-edit .eq-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:7px; }
.net-equipment-edit h2 { margin:0 0 6px; font-size:25px; font-weight:700; letter-spacing:-.5px; }
.net-equipment-edit .eq-intro p { font-size:13px; color:var(--eq-muted); margin:0; }
.net-equipment-edit .btn { font-size:13px; padding:10px 15px; border-radius:7px; font-weight:600; }
.net-equipment-edit .eq-back { color:#425f74; background:#fff; border:1px solid #cbd8e3; display:inline-flex; align-items:center; gap:8px; }
.net-equipment-edit .eq-back:hover { background:#eff5f8; }
.net-equipment-edit .eq-identity { display:flex; align-items:center; gap:14px; padding:20px; background:#fff; border:1px solid var(--eq-line); border-radius:11px; margin-bottom:20px; }
.net-equipment-edit .eq-device-icon { width:45px; height:45px; flex:0 0 45px; display:flex; align-items:center; justify-content:center; background:#eaf5f5; color:#087f8c; border-radius:10px; font-size:19px; }
.net-equipment-edit .eq-identity strong { display:block; font-size:16px; margin-bottom:4px; overflow-wrap:anywhere; }
.net-equipment-edit .eq-identity div>span { font-size:12px; color:var(--eq-muted); }
.net-equipment-edit .eq-current-state { margin-left:auto; font-size:11px; font-weight:600; padding:5px 10px; border:1px solid #dbe2e9; border-radius:20px; white-space:nowrap; }
.net-equipment-edit .eq-current-state.is-active { background:#e9f8ef; color:#18734d; border-color:#c6e9d4; }
.net-equipment-edit .eq-current-state.is-inactive { background:#f1f3f6; color:#647484; }
.net-equipment-edit .eq-section { background:#fff; border:1px solid var(--eq-line); border-radius:11px; margin-bottom:18px; overflow:hidden; box-shadow:0 4px 14px #15253605; }
.net-equipment-edit .eq-section-header { padding:18px 22px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #e7edf3; }
.net-equipment-edit .eq-section-number { width:31px; height:31px; flex:0 0 31px; display:flex; align-items:center; justify-content:center; color:#087f8c; background:#eef7f8; border:1px solid #daebee; border-radius:8px; font-size:11px; font-weight:700; }
.net-equipment-edit h3 { font-size:15px; font-weight:700; margin:0 0 4px; }
.net-equipment-edit .eq-section-header p { margin:0; font-size:12px; color:var(--eq-muted); }
.net-equipment-edit .eq-field-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px 24px; padding:23px; }
.net-equipment-edit .eq-three-columns { grid-template-columns:repeat(3,minmax(0,1fr)); }
.net-equipment-edit .eq-field { min-width:0; }
.net-equipment-edit .eq-field label { display:block; color:#405b6e; font-size:12px; font-weight:600; margin-bottom:8px; }
.net-equipment-edit .eq-field .form-control { width:100%; height:43px; border:1px solid #cddbe6; border-radius:7px; color:#294357; font-size:13px; background:#fff; padding:9px 12px; }
.net-equipment-edit .eq-field .form-control:focus { border-color:#1597a5; box-shadow:0 0 0 3px #087f8c15; outline:none; }
.net-equipment-edit .eq-field .form-control.is-invalid { border-color:#cf5c70; }
.net-equipment-edit #ip_address,.net-equipment-edit #mac_address { font-family:ui-monospace,SFMono-Regular,Consolas,monospace; }
.net-equipment-edit .eq-required { color:#c14e62; }
.net-equipment-edit .eq-help { display:block; margin-top:6px; color:#8193a1; font-size:11px; }
.net-equipment-edit .eq-errors { background:#fff3f5; border:1px solid #efd0d8; border-radius:9px; color:#a33e52; font-size:13px; padding:16px 20px; margin-bottom:20px; }
.net-equipment-edit .eq-errors ul { margin:8px 0 0; padding-left:20px; }
.net-equipment-edit .invalid-feedback { font-size:11px; margin-top:6px; }
.net-equipment-edit .eq-form-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; padding:8px 0; color:var(--eq-muted); font-size:12px; }
.net-equipment-edit .eq-save-actions { display:flex; gap:9px; }
.net-equipment-edit .eq-cancel { background:#fff; border:1px solid #cbd8e3; color:#526b7e; }
.net-equipment-edit .eq-save { background:#087f8c; border:1px solid #087f8c; color:#fff; display:inline-flex; align-items:center; gap:8px; }
.net-equipment-edit .eq-save:hover { background:#066875; }
.net-equipment-edit a:focus-visible,.net-equipment-edit button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
.net-equipment-edit .select2-container { width:100%!important; }
.net-equipment-edit .select2-container .select2-selection--single { min-height:43px; border:1px solid #cddbe6; border-radius:7px; }
.net-equipment-edit .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:41px; padding-left:12px; color:#294357; font-size:13px; }
.net-equipment-edit .select2-container--default .select2-selection--single .select2-selection__arrow { height:41px; }
@media(max-width:767px) { .net-equipment-edit .eq-three-columns { grid-template-columns:1fr; } }
@media(max-width:575px) { .net-equipment-edit h2 { font-size:22px; } .net-equipment-edit .eq-field-grid { grid-template-columns:1fr; padding:18px; gap:18px; } .net-equipment-edit .eq-section-header { padding:16px 18px; } .net-equipment-edit .eq-identity { flex-wrap:wrap; padding:16px; gap:10px; } .net-equipment-edit .eq-current-state { margin-left:55px; } .net-equipment-edit .eq-intro { gap:14px; } .net-equipment-edit .eq-form-footer { align-items:flex-start; } .net-equipment-edit .eq-save-actions { width:100%; } .net-equipment-edit .eq-save-actions .btn { flex:1; text-align:center; justify-content:center; } }
    </style>
@stop
@section('js')
    @include('netlan::admin.equipos.parciales.js')
    <script>

(function () {
    function initializeModelSelects() {
        var form = document.getElementById('equipment-edit-form');
        if (!form) return;
        var saved = document.getElementById('modelo_db');
        var dynamic = document.getElementById('modelo');
        var label = document.getElementById('eq-model-label');
        if (!saved || !dynamic) return;
        function synchronize() {
            // El parcial existente decide qué selector mostrar.
            // Solo el selector visible participa en el envío del formulario.
            var useDynamic = window.getComputedStyle(dynamic).display !== 'none';
            saved.disabled = useDynamic;
            dynamic.disabled = !useDynamic;
            label.htmlFor = useDynamic ? 'modelo' : 'modelo_db';
        }
        synchronize();
        var observer = new MutationObserver(synchronize);
        observer.observe(saved, { attributes:true, attributeFilter:['style', 'class'] });
        observer.observe(dynamic, { attributes:true, attributeFilter:['style', 'class'] });
        form.addEventListener('submit', synchronize, true);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeModelSelects);
    } else {
        initializeModelSelects();
    }
})();
    </script>
@stop
