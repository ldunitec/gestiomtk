<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="referrer" content="no-referrer"><title>Fichas Hotspot · Lote {{ $lote->id }}</title>
<style>
@page { size:A4; margin:10mm; }
* { box-sizing:border-box; }
body { font:14px Arial,sans-serif; color:#12243a; margin:0; background:#edf2f7; }
.toolbar { padding:16px; max-width:900px; margin:auto; }
button { padding:10px 20px; background:#165ba9; color:white; border:0; border-radius:6px; cursor:pointer; }
.grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8mm; max-width:190mm; margin:0 auto 20px; }
.ficha { background:white; border:1px dashed #758499; border-radius:8px; padding:5mm; break-inside:avoid; page-break-inside:avoid; }
h1 { font-size:18px; margin:0 0 4mm; } p { margin:6px 0; } .row { display:flex; gap:3mm; align-items:center; } .qr { flex-shrink:0; } .qr svg { width:32mm; height:32mm; } .datos { overflow-wrap:anywhere; min-width:0; } .titular { padding:3mm; border:1px solid #d9e2ec; border-radius:4px; overflow-wrap:anywhere; } .titular strong { display:block; font-size:11px; color:#465b70; margin-bottom:1mm; } .titular span { font-size:16px; font-weight:bold; } .clave { font: bold 17px monospace; } .nota { font-size:11px; line-height:1.4; }
@media(max-width:600px) { .grid { grid-template-columns:1fr; margin:10px; } }
@media print { body { background:white; } .toolbar { display:none; } .grid { margin:0; max-width:none; grid-template-columns:repeat(2,minmax(0,1fr)); } .ficha { border-radius:0; } }
</style></head><body>
<div class="toolbar"><h1>Impresión de fichas · Lote #{{ $lote->id }}</h1><p>{{ $fichas->count() }} fichas listas para imprimir. Papel A4 · dos columnas · escala 100 %.</p><button onclick="window.print()">Imprimir / guardar PDF</button>@if($omitidas)<p>Se omitieron {{ $omitidas }} fichas vencidas, revocadas o sin confirmación.</p>@endif<p>Conéctese al Wi-Fi indicado antes de escanear el QR. El QR abre el portal con el usuario precargado; ingrese la clave impresa.</p></div>
<main class="grid">
@foreach($fichas as $f)
<article class="ficha"><h1>{{ $f->inicio_vigencia === 'permanente' ? 'Internet para empleados' : 'Internet para visitantes' }}</h1>
@if(filled($lote->visitante))
<p class="titular"><strong>{{ $f->inicio_vigencia === 'permanente' ? 'Empleado' : 'Visitante / referencia' }}</strong><span>{{ $lote->visitante }}</span></p>
@endif
<p><strong>Wi-Fi:</strong> {{ $f->router->ssid }}</p><div class="row"><div class="qr">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->margin(1)->generate($f->qrUrl()) !!}</div><div class="datos"><p>Usuario<br><span class="clave">{{ $f->usuario }}</span></p><p>Clave<br><span class="clave">{{ $f->clave }}</span></p></div></div>
<p><strong>Válida hasta:</strong><br>{{ $f->vigenciaVisible() }}@if($f->vence_at) · Honduras
@endif</p>
<p class="nota">@if($f->inicio_vigencia === 'permanente')Empleado: acceso hasta revocación manual
@elseif($f->inicio_vigencia === 'primer_ingreso'){{ $lote->dias }} {{ $lote->dias == 1 ? 'día' : 'días' }} desde el primer ingreso exitoso. Imprimir no activa ni reinicia el plazo
@else{{ $lote->dias }} días desde su creación
@endif · 1 dispositivo simultáneo.<br>1. Conéctese al Wi-Fi. 2. Escanee el QR. 3. Ingrese la clave. Conserve esta ficha; no comparta sus credenciales.</p>
<p class="nota">{{ $f->router->portal_url }}</p></article>
@endforeach
</main></body></html>


