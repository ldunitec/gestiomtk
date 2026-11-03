@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header"><h2 class="h5">{{ $router->nombre }} · {{ $datos['identity'] }}</h2></div><div class="card-body"><div class="row">
@foreach(['version'=>'RouterOS','board-name'=>'Equipo','uptime'=>'Tiempo encendido','cpu-load'=>'Uso de CPU (%)','free-memory'=>'Memoria libre (bytes)'] as $key => $label)<div class="col-md-4 mb-3"><strong>{{ $label }}</strong><br>{{ $datos['resource'][$key] ?? 'No disponible' }}</div>@endforeach
</div><p>Hotspot {{ $router->servidor_hotspot }}: {{ count($datos['servers']) ? 'Encontrado' : 'No encontrado' }}</p></div></div>
<div class="card"><div class="card-header">Interfaces</div><div class="table-responsive"><table class="table"><thead><tr><th>Nombre</th><th>Tipo</th><th>Enlace activo</th><th>Deshabilitada</th></tr></thead><tbody>
@foreach($datos['interfaces'] as $i)<tr><td>{{ $i['name'] ?? '' }}</td><td>{{ $i['type'] ?? '' }}</td><td>{{ in_array($i['running'] ?? '', ['true','yes',true], true) ? 'Sí' : 'No' }}</td><td>{{ in_array($i['disabled'] ?? '', ['true','yes',true], true) ? 'Sí' : 'No' }}</td></tr>@endforeach
</tbody></table></div></div>
@stop
