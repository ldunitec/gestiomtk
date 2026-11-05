@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header"><h2 class="h5">Preparar {{ $router->nombre }}</h2></div><div class="card-body">
<p>Se crearán o actualizarán estos objetos exclusivos de GestiónMTK en el router:</p>
<ul><li>Perfil de visitantes <code>{{ $router->perfil() }}</code>: un dispositivo simultáneo, velocidad {{ $router->velocidad }}, sin autenticación por MAC cookie.</li><li>Perfil de empleados <code>{{ $router->perfilEmpleados() }}</code>: 5M/5M, sin vencimiento y un dispositivo simultáneo.</li><li>Script y tarea <code>{{ $router->tarea() }}</code>: revisión cada 30 segundos, bloqueo de fichas vencidas y cierre de sus sesiones.</li><li>Validación de vencimiento al ingresar, usando la hora del router.</li></ul>
<p>Las fichas nuevas duran 24, 48 o 168 horas corridas desde el primer ingreso exitoso, incluso desconectadas. Imprimir no activa ni reinicia su plazo. Las fichas anteriores conservan su vencimiento. Las fichas de empleados no vencen y se revocan manualmente. Una sesión conectada puede tardar hasta 30 segundos en cerrarse al vencer. Con un reloj inválido se impide activar visitantes nuevos y se bloquean los visitantes activados hasta corregirlo y verificar de nuevo.</p>
<p>Las fichas de empleados emitidas con el perfil anterior se trasladarán al perfil 5M/5M. Se conserva su usuario, clave y condición de revocación; sus sesiones se cerrarán para que el nuevo límite se aplique al volver a ingresar.</p>
<details class="mb-3"><summary>Revisar código que se instalará</summary><h3 class="h6 mt-3">Tarea de vencimientos</h3><pre class="p-3 bg-light" style="white-space:pre-wrap">{{ $barrido }}</pre><h3 class="h6">Validación al ingresar</h3><pre class="p-3 bg-light" style="white-space:pre-wrap">{{ $login }}</pre></details>
<form method="POST" action="{{ route('mt.preparar', $router) }}">@csrf <div class="form-check mb-3"><input type="checkbox" required name="confirmar" value="1" id="confirmar" class="form-check-input"><label for="confirmar" class="form-check-label">Instalar o actualizar estos objetos de visitantes y empleados en este router.</label></div><button class="btn btn-warning">Preparar y verificar Hotspot</button></form>
</div></div>
@stop


