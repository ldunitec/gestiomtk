@php
    $logoMegatk = 'data:image/png;base64,'.base64_encode(file_get_contents(module_path('Mikrotik', 'Resources/assets/images/logo-megatk.png')));
@endphp
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="referrer" content="no-referrer"><title>Fichas Hotspot · {{ $lote ? 'Lote '.$lote->id : 'Listado general' }}</title>
<style>
@page { size:A4; margin:10mm; }
* { box-sizing:border-box; }
body { font:14px Arial,sans-serif; color:#12243a; margin:0; background:#edf2f7; }
.toolbar { padding:16px; max-width:900px; margin:auto; }
button { padding:10px 20px; background:#165ba9; color:white; border:0; border-radius:6px; cursor:pointer; }
.grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8mm; max-width:190mm; margin:0 auto 20px; }
.ficha { --acento:#526577; --suave:#f1f4f7; background:white; border:1px solid var(--acento); border-top:3mm solid var(--acento); border-radius:8px; padding:4mm; break-inside:avoid; page-break-inside:avoid; }
.ficha-dia { --acento:#1565c0; --suave:#eaf3ff; }
.ficha-dos-dias { --acento:#e65100; --suave:#fff3e0; }
.ficha-semana { --acento:#2e7d32; --suave:#edf7ed; }
.ficha-empleado { --acento:#00838f; --suave:#e0f7fa; }
.marca { display:flex; align-items:center; justify-content:space-between; gap:3mm; margin-bottom:3mm; }
.logo { display:block; width:42mm; height:auto; max-width:62%; }
.tipo { color:var(--acento); background:var(--suave); border:1px solid var(--acento); border-radius:4px; padding:2mm; font-size:12px; font-weight:bold; text-align:center; line-height:1.3; }
.tipo small { display:block; font-size:10px; }
.ficha { print-color-adjust:exact; -webkit-print-color-adjust:exact; }
h1 { font-size:18px; margin:0 0 4mm; } p { margin:6px 0; } .row { display:flex; gap:3mm; align-items:center; } .qr { flex-shrink:0; } .qr svg { width:32mm; height:32mm; } .datos { overflow-wrap:anywhere; min-width:0; } .titular { padding:3mm; border:1px solid #d9e2ec; border-radius:4px; overflow-wrap:anywhere; } .titular strong { display:block; font-size:11px; color:#465b70; margin-bottom:1mm; } .titular span { font-size:16px; font-weight:bold; } .clave { font: bold 17px monospace; } .nota { font-size:11px; line-height:1.4; }
@media(max-width:600px) { .grid { grid-template-columns:1fr; margin:10px; } }
@media print { body { background:white; } .toolbar { display:none; } .grid { margin:0; max-width:none; grid-template-columns:repeat(2,minmax(0,1fr)); } .ficha { border-radius:0; } }
</style></head><body>
<div class="toolbar"><h1>Impresión de fichas · {{ $lote ? 'Lote #'.$lote->id : 'Listado general' }}</h1><p>{{ $fichas->count() }} fichas listas para imprimir. Papel A4 · dos columnas · escala 100 %.</p><button onclick="window.print()">Imprimir / guardar PDF</button>@if($omitidas)<p>Se omitieron {{ $omitidas }} fichas no disponibles para esta impresión (impresas, vencidas, revocadas o sin confirmar).</p>@endif<p>Colores: azul · 1 día; naranja · 2 días; verde · semana; cian · empleados. Para conservar los fondos al imprimir, active «Gráficos de fondo» en el navegador.</p><p>Conéctese al Wi-Fi indicado antes de escanear el QR. El QR abre el portal con el usuario precargado; ingrese la clave impresa.</p></div>
<div class="toolbar">
@if(session('ok'))<p role="status">{{ session('ok') }}</p>@endif
@if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<form method="POST" action="{{ route('mt.fichas.impresas') }}">@csrf
<input type="hidden" name="seleccion_impresa" value="{{ $fichas->pluck('id')->values()->toJson() }}">
<p>Después de completar la impresión o guardar el PDF, confirme para registrarlas como impresas. Abrir o cancelar esta vista no las marca.</p>
<button>Confirmar impresas</button>
</form></div>
<main class="grid">
@foreach($fichas as $f)
@php
    $diasEtiqueta = (int) ($f->duracion_dias ?? $f->lote->dias);
    [$colorEtiqueta, $tipoEtiqueta, $detalleEtiqueta] = $f->inicio_vigencia === 'permanente'
        ? ['empleado', 'EMPLEADO', 'SIN VENCIMIENTO']
        : match ($diasEtiqueta) {
            1 => ['dia', '1 DÍA', '24 HORAS'],
            2 => ['dos-dias', '2 DÍAS', '48 HORAS'],
            7 => ['semana', '1 SEMANA', '168 HORAS'],
            default => ['otra', 'VISITANTE', $diasEtiqueta.' DÍAS'],
        };
@endphp
<article class="ficha ficha-{{ $colorEtiqueta }}">
<div class="marca"><img class="logo" src="{{ $logoMegatk }}" alt="MEGATK" width="407" height="157"><span class="tipo">{{ $tipoEtiqueta }}<small>{{ $detalleEtiqueta }}</small></span></div>
<h1>{{ $f->inicio_vigencia === 'permanente' ? 'Internet para empleados' : 'Internet para visitantes' }}</h1>
@if(filled($f->nombreTitular()))
<p class="titular"><strong>{{ $f->inicio_vigencia === 'permanente' ? 'Empleado' : 'Visitante / referencia' }}</strong><span>{{ $f->nombreTitular() }}</span></p>
@endif
<p><strong>Wi-Fi:</strong> {{ $f->router->ssid }}</p><div class="row"><div class="qr">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->margin(1)->generate($f->qrUrl()) !!}</div><div class="datos"><p>Usuario<br><span class="clave">{{ $f->usuario }}</span></p><p>Clave<br><span class="clave">{{ $f->clave }}</span></p></div></div>
<p><strong>Válida hasta:</strong><br>{{ $f->vigenciaVisible() }}@if($f->vence_at) · Honduras
@endif</p>
<p class="nota">@if($f->inicio_vigencia === 'permanente')Empleado: acceso hasta revocación manual
@elseif($f->inicio_vigencia === 'primer_ingreso'){{ $f->lote->dias }} {{ $f->lote->dias == 1 ? 'día' : 'días' }} desde el primer ingreso exitoso. Imprimir no activa ni reinicia el plazo
@else{{ $f->lote->dias }} días desde su creación
@endif · 1 dispositivo simultáneo.<br>1. Conéctese al Wi-Fi. 2. Escanee el QR. 3. Ingrese la clave. Conserve esta ficha; no comparta sus credenciales.</p>
<p class="nota">Lote #{{ $f->lote_id }} · {{ $f->router->nombre }}<br>{{ $f->router->portal_url }}</p></article>
@endforeach
</main></body></html>




