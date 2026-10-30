@extends('adminlte::page')

@section('content_header')
    <h1>Listado de conexiones</h1>
@stop

@section('content')
@php
    // Se conservan los identificadores utilizados por el controlador y el modal.
    // SW-D tiene 24 puertos en la vista original; modificar aquí si el equipo tiene 48.
    $rackDevices = [
        ['id'=>'MKRT-01', 'brand'=>'MikroTik', 'model'=>'RB1100', 'kind'=>'Router', 'service'=>'VLAN / LAN', 'ports'=>13, 'type'=>'mikrotik', 'field'=>'sw'],
        ['id'=>'SW-A', 'brand'=>'Cisco', 'model'=>'SG300-52', 'kind'=>'Switch', 'service'=>'Datos', 'ports'=>52, 'type'=>'cisco', 'field'=>'sw'],
        ['id'=>'SW-B', 'brand'=>'tp-link', 'model'=>'Switch · 24 puertos', 'kind'=>'Switch', 'service'=>'Telefonía', 'ports'=>24, 'type'=>'tplink', 'field'=>'sw'],
        ['id'=>'SW-C', 'brand'=>'tp-link', 'model'=>'Switch · 48 puertos', 'kind'=>'Switch', 'service'=>'CCTV / Datos', 'ports'=>48, 'type'=>'tplink', 'field'=>'sw'],
        ['id'=>'SW-D', 'brand'=>'tp-link', 'model'=>'Switch · 24 puertos', 'kind'=>'Switch', 'service'=>'CCTV / Varios', 'ports'=>24, 'type'=>'tplink', 'field'=>'sw'],
        ['id'=>'PP-A', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 24 puertos', 'kind'=>'Cableado estructurado', 'service'=>'Datos', 'ports'=>24, 'type'=>'patch', 'field'=>'puerto'],
        ['id'=>'PP-B', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 24 puertos', 'kind'=>'Cableado estructurado', 'service'=>'Datos', 'ports'=>24, 'type'=>'patch', 'field'=>'puerto'],
        ['id'=>'PP-C', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 24 puertos', 'kind'=>'Cableado estructurado', 'service'=>'Telefonía', 'ports'=>24, 'type'=>'patch', 'field'=>'puerto'],
        ['id'=>'PP-D', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 24 puertos', 'kind'=>'Cableado estructurado', 'service'=>'Telefonía', 'ports'=>24, 'type'=>'patch', 'field'=>'puerto'],
        ['id'=>'PP-E', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 24 puertos', 'kind'=>'Cableado estructurado', 'service'=>'CCTV', 'ports'=>24, 'type'=>'patch', 'field'=>'puerto'],
        ['id'=>'PP-F', 'brand'=>'PATCH PANEL', 'model'=>'Panel estándar · 48 puertos', 'kind'=>'Cableado estructurado', 'service'=>'CCTV / Varios', 'ports'=>48, 'type'=>'patch', 'field'=>'puerto'],
    ];
    $rackConnections = collect($conexiones ?? []);
    $rackSwitchGroups = $rackConnections->groupBy('sw');
    $rackSwitchIds = collect($switches ?? []);
    $rackPatchIds = collect($puerto ?? []);
    $rackPortInfo = function ($device, $number) use ($rackSwitchGroups, $rackSwitchIds, $rackPatchIds) {
        $value = $device['id'] . '-' . $number;
        // Patch panels: la ocupación proviene de $puerto, igual que en la vista original.
        // No se presupone el nombre de una relación de patch panel del modelo.
        $records = $device['field'] === 'sw' ? $rackSwitchGroups->get($value, collect()) : collect();
        $count = $records->count();
        $assigned = $count > 0 || ($device['field'] === 'sw' ? $rackSwitchIds : $rackPatchIds)->contains($value);
        $state = $assigned ? 'asignado' : 'libre';
        $label = $assigned ? 'Asignado' : 'Libre';
        if ($count > 1) {
            $state = 'multiple'; $label = 'Múltiples conexiones';
        } elseif ($count === 1) {
            $equipment = $records->first()->equipo;
            if ($equipment && $equipment->estado === 'Activo') {
                $state = 'activo'; $label = 'Equipo activo';
            } elseif ($equipment) {
                $state = 'inactivo'; $label = 'Equipo inactivo';
            }
        }
        return ['value'=>$value, 'count'=>$count, 'assigned'=>$assigned, 'state'=>$state, 'label'=>$label];
    };
@endphp
<div class="net-rack">
    <div class="rack-overview">
        <div><div class="rack-eyebrow">MEGATK · Infraestructura de red</div>
        <h2>Mapa de conexiones</h2><p class="rack-description">Selecciona un puerto para consultar sus conexiones.</p></div>
        <div class="rack-total"><strong>{{ count($rackDevices) }}</strong>equipos y paneles</div>
    </div>
    <div class="rack-legend" aria-label="Leyenda de estados">
        <span><i class="legend-dot"></i>Libre</span>
        <span><i class="legend-dot asignado"></i>Asignado / equipo activo</span>
        <span><i class="legend-dot inactivo"></i>Equipo inactivo</span>
        <span><i class="legend-dot multiple"></i>Múltiples conexiones</span>
        <span>Estados del inventario registrado</span>
    </div>
    @foreach ($rackDevices as $device)
        @php
            $assignedPorts = 0;
            for ($n = 1; $n <= $device['ports']; $n++) {
                if ($rackPortInfo($device, $n)['assigned']) { $assignedPorts++; }
            }
            $mainPorts = $device['type'] === 'cisco' ? 48 : $device['ports'];
        @endphp
        <section class="rack-device {{ $device['type'] }}" aria-labelledby="rack-title-{{ $device['id'] }}">
            <header class="device-heading">
                <div class="device-title"><span class="device-id">{{ $device['id'] }}</span>
                    <div><h3 id="rack-title-{{ $device['id'] }}">{{ $device['type'] === 'patch' ? 'Patch Panel ' . substr($device['id'], 3) : ($device['type'] === 'tplink' ? 'TP-Link · ' . $device['id'] : $device['brand'] . ' ' . $device['model']) }}</h3>
                    <p>{{ $device['kind'] }} · {{ $device['ports'] }} puertos</p></div>
                </div>
                <div class="device-summary"><span><strong>{{ $assignedPorts }}</strong> asignados / <strong>{{ $device['ports'] - $assignedPorts }}</strong> libres</span><span class="service-tag">{{ $device['service'] }}</span></div>
            </header>
            <div class="rack-scroll" tabindex="0" role="region" aria-label="Puertos de {{ $device['id'] }}; desplazamiento horizontal">
                <div class="rack-face">
                    <div class="device-brand"><span class="brand-name">{{ $device['brand'] }}</span><span class="brand-model">{{ $device['model'] }}</span><span class="brand-kind">{{ $device['kind'] }}</span><span class="brand-grille" aria-hidden="true"></span></div>
                    <div class="port-area"><div class="port-bank">
                        @for ($number = 1; $number <= $mainPorts; $number++)
                            @php $port = $rackPortInfo($device, $number); @endphp
<button type="button" class="rack-port btnPort {{ $port['state'] }}" data-valor="{{ $port['value'] }}"
    title="{{ $port['value'] }} — {{ $port['label'] }}" aria-label="{{ $port['value'] }}: {{ $port['label'] }}">
    <span class="port-number">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</span>
    <span class="port-jack" aria-hidden="true"><span class="port-led"></span></span>
    @if ($port['count'] > 1)<span class="port-badge">{{ $port['count'] }}</span>@endif
</button>

                        @endfor
                    </div><div class="bank-caption">{{ $device['type'] === 'patch' ? 'Conexiones RJ45 · ' . $device['id'] : 'Puertos Ethernet · ' . $device['id'] }}</div></div>
                    @if ($device['type'] === 'cisco')
                        <div class="uplink-area"><div class="port-bank">
                            @for ($number = 49; $number <= 52; $number++)
                                @php $port = $rackPortInfo($device, $number); @endphp
<button type="button" class="rack-port btnPort {{ $port['state'] }}" data-valor="{{ $port['value'] }}"
    title="{{ $port['value'] }} — {{ $port['label'] }}" aria-label="{{ $port['value'] }}: {{ $port['label'] }}">
    <span class="port-number">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</span>
    <span class="port-jack" aria-hidden="true"><span class="port-led"></span></span>
    @if ($port['count'] > 1)<span class="port-badge">{{ $port['count'] }}</span>@endif
</button>

                            @endfor
                        </div><div class="bank-caption">49 — 52</div></div>
                    @endif
                </div>
            </div>
            <footer class="device-footer"><span>{{ $device['id'] }} · {{ $device['service'] }}</span><span class="scroll-hint">Desliza para ver todos los puertos →</span><span>Clic en un puerto para ver el detalle</span></footer>
        </section>
    @endforeach
</div>
@include('netlan::admin.connections.parciales.modal')

@stop

@section('css')
    @include('netlan::admin.connections.parciales.css')
    <style>

.net-rack { --ink:#152536; --muted:#657687; --line:#dbe3eb; font-family:inherit; color:var(--ink); padding:8px 0 28px; }
.net-rack * { box-sizing:border-box; }
.net-rack .rack-overview { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; padding:22px 24px; margin-bottom:20px; background:#fff; border:1px solid var(--line); border-radius:12px; }
.net-rack .rack-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:6px; }
.net-rack .rack-overview h2 { font-size:23px; font-weight:700; margin:0 0 5px; }
.net-rack .rack-description { font-size:13px; color:var(--muted); margin:0; }
.net-rack .rack-total { text-align:right; font-size:12px; color:var(--muted); }
.net-rack .rack-total strong { display:block; color:var(--ink); font-size:27px; line-height:1.1; }
.net-rack .rack-legend { display:flex; flex-wrap:wrap; gap:12px 22px; padding:0 5px 18px; font-size:12px; color:var(--muted); }
.net-rack .rack-legend span { display:inline-flex; gap:7px; align-items:center; }
.net-rack .legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; background:#7a8692; }
.net-rack .legend-dot.asignado { background:#059669; } .net-rack .legend-dot.inactivo { background:#d28b13; } .net-rack .legend-dot.multiple { background:#d84462; }
.net-rack .rack-device { background:#fff; border:1px solid var(--line); border-radius:11px; overflow:hidden; margin-bottom:18px; box-shadow:0 4px 14px rgba(21,37,54,.04); }
.net-rack .device-heading { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:15px 20px; }
.net-rack .device-title { display:flex; align-items:center; gap:10px; }
.net-rack .device-id { font-size:11px; font-weight:700; color:#526777; background:#eff4f7; padding:6px 9px; border-radius:5px; }
.net-rack .device-heading h3 { font-size:16px; font-weight:700; margin:0; }
.net-rack .device-heading p { margin:3px 0 0; font-size:12px; color:var(--muted); }
.net-rack .device-summary { font-size:12px; color:var(--muted); display:flex; gap:14px; align-items:center; }
.net-rack .device-summary strong { color:var(--ink); }
.net-rack .service-tag { border:1px solid #dbe5ed; border-radius:20px; padding:4px 10px; color:#496174; font-size:11px; }
.net-rack .rack-scroll { overflow-x:auto; padding:0 18px 16px; scrollbar-width:thin; }
.net-rack .rack-face { position:relative; display:flex; align-items:center; gap:18px; min-width:1040px; min-height:172px; padding:22px 30px; border:1px solid #7b858e; border-radius:5px; background:linear-gradient(180deg,#e8ecef 0%,#d1d7dc 48%,#bec6cd 100%); box-shadow:inset 0 2px 0 #fff9,inset 0 -3px 6px #0002,0 3px 7px #0001; }
.net-rack .rack-face:before,.net-rack .rack-face:after { content:''; position:absolute; width:6px; height:32px; border-radius:6px; top:calc(50% - 16px); background:#27333d; box-shadow:inset 1px 1px 3px #000; }
.net-rack .rack-face:before { left:9px; } .net-rack .rack-face:after { right:9px; }
.net-rack .device-brand { flex:0 0 132px; align-self:stretch; display:flex; flex-direction:column; justify-content:center; border-right:1px solid #0002; padding-right:15px; }
.net-rack .brand-name { font-size:23px; line-height:1.1; font-weight:800; letter-spacing:-1px; color:#244968; }
.net-rack .brand-model { font-size:10px; margin-top:7px; font-weight:600; color:#425668; }
.net-rack .brand-kind { font-size:9px; color:#596c7a; margin-top:4px; text-transform:uppercase; letter-spacing:1px; }
.net-rack .brand-grille { height:13px; width:52px; margin-top:17px; background:repeating-linear-gradient(90deg,#8996a0 0 2px,transparent 2px 5px); opacity:.65; }
.net-rack .port-area { flex:1; min-width:0; }
.net-rack .port-bank { display:grid; grid-template-columns:repeat(24,minmax(0,1fr)); gap:8px 4px; padding:9px; background:#28313a; border:1px solid #121b23; border-radius:4px; box-shadow:inset 0 1px 4px #0006; }
.net-rack .rack-port { --state:#7d8b97; position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; min-width:0; height:51px; padding:2px; border:1px solid transparent; border-radius:3px; color:#d8e1e9; background:transparent; cursor:pointer; font:inherit; transition:background .15s,border-color .15s; }
.net-rack .rack-port.asignado { --state:#35d5a0; } .net-rack .rack-port.activo { --state:#35d5a0; } .net-rack .rack-port.inactivo { --state:#f6be4f; } .net-rack .rack-port.multiple { --state:#fb6b8c; }
.net-rack .rack-port:hover { border-color:var(--state); background:#ffffff12; }
.net-rack .rack-port:focus-visible { outline:2px solid #58d3ed; outline-offset:2px; }
.net-rack .port-number { font:600 10px/1.2 monospace; color:#d8e1e9; }
.net-rack .port-jack { position:relative; width:100%; max-width:31px; height:23px; background:#0a1016; border:2px solid #7a858e; border-radius:2px; box-shadow:inset 0 2px 4px #000; }
.net-rack .port-jack:before { content:''; position:absolute; top:2px; left:18%; right:18%; height:6px; background:repeating-linear-gradient(90deg,#c6a15b 0 1px,transparent 1px 3px); }
.net-rack .port-jack:after { content:''; position:absolute; bottom:-2px; left:28%; right:28%; height:5px; background:#0a1016; border:solid #7a858e; border-width:0 2px 2px; }
.net-rack .port-led { position:absolute; right:1px; bottom:2px; width:4px; height:3px; border-radius:1px; background:var(--state); }
.net-rack .rack-port:not(.libre) .port-led { box-shadow:0 0 5px var(--state); }
.net-rack .rack-port:not(.libre) .port-jack { border-color:var(--state); }
.net-rack .port-badge { position:absolute; right:-2px; top:0; background:#fb6b8c; color:#14202b; border-radius:8px; font-size:9px; font-weight:700; padding:0 4px; }
.net-rack .bank-caption { font-size:9px; letter-spacing:1.4px; margin-top:8px; color:#4b5d6a; text-align:center; text-transform:uppercase; }
.net-rack .uplink-area { flex:0 0 80px; border-left:1px solid #0002; padding-left:12px; }
.net-rack .uplink-area .port-bank { grid-template-columns:repeat(2,minmax(0,1fr)); padding:6px; gap:6px 3px; }
.net-rack .uplink-area .bank-caption { letter-spacing:.4px; }
.net-rack .device-footer { display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px; padding:10px 20px; background:#f8fafc; border-top:1px solid #e7edf3; color:var(--muted); font-size:11px; }
.net-rack .mikrotik .rack-face { background:linear-gradient(#fcfcfb,#e4e4e1); min-height:142px; }
.net-rack .mikrotik .brand-name { font-style:italic; color:#373c43; font-size:25px; }
.net-rack .mikrotik .port-bank { grid-template-columns:repeat(13,minmax(0,1fr)); gap:12px; padding:12px; }
.net-rack .mikrotik .rack-port { height:54px; } .net-rack .mikrotik .port-jack { max-width:37px; height:26px; }
.net-rack .tplink .rack-face { background:linear-gradient(#343c43,#242c33); border-color:#121b21; }
.net-rack .tplink .brand-name { color:#57d3d8; letter-spacing:-.8px; }
.net-rack .tplink .brand-model,.net-rack .tplink .brand-kind,.net-rack .tplink .bank-caption { color:#a8bac5; }
.net-rack .tplink .device-brand { border-color:#ffffff20; }
.net-rack .patch .rack-face { min-height:115px; background:linear-gradient(#262d33,#151b21); border-color:#080e13; }
.net-rack .patch .device-brand { flex-basis:132px; border-color:#ffffff20; }
.net-rack .patch .brand-name { color:#f0f3f6; font-size:16px; letter-spacing:1px; }
.net-rack .patch .brand-model,.net-rack .patch .brand-kind,.net-rack .patch .bank-caption { color:#a8bac5; }
.net-rack .patch .port-bank { background:transparent; border:0; padding:0; box-shadow:none; gap:8px 4px; }
.net-rack .patch .port-number { background:#dce1e4; color:#18212a; width:100%; max-width:31px; padding:2px 0; border-radius:1px; }
.net-rack .patch .port-led { display:none; } .net-rack .patch .rack-port:not(.libre) .port-number { box-shadow:inset 0 -2px var(--state); }
.net-rack .scroll-hint { display:none; }
@media(max-width:1199px) { .net-rack .scroll-hint { display:inline; } }
@media(max-width:767px) { .net-rack .rack-overview { padding:17px; } .net-rack .rack-overview h2 { font-size:20px; } .net-rack .device-heading { padding:13px; } .net-rack .device-summary { width:100%; justify-content:space-between; } .net-rack .rack-scroll { padding:0 10px 12px; } .net-rack .device-heading h3 { font-size:14px; } .net-rack .device-footer { padding:10px 13px; } }
@media print { .net-rack .rack-scroll { overflow:visible; } .net-rack .rack-face { min-width:0; gap:8px; padding:14px 18px; } .net-rack .device-brand { flex-basis:90px!important; } .net-rack .rack-device { break-inside:avoid; box-shadow:none; } .net-rack .scroll-hint { display:none; } .net-rack { print-color-adjust:exact; -webkit-print-color-adjust:exact; } }
    </style>
@stop

@section('js')
    @include('netlan::admin.connections.parciales.js')
@stop
