<h2>Viático de Visita</h2>
<hr>

<p><b>Cliente:</b> {{ $viatico->cliente }}</p>
<p><b>Detalle de la visita:</b><br>
{{ $viatico->detalle_visita }}</p>

<hr>

<p><b>Origen:</b> {{ $viatico->origen }}</p>
<p><b>Destino:</b> {{ $viatico->destino }}</p>
<p><b>Vehículo:</b> {{ $viatico->vehiculo }}</p>
<p><b>Kilómetros:</b> {{ $viatico->km }}</p>
<p><b>Total:</b> <b>L. {{ $viatico->total }}</b></p>

<hr>
<p>Fecha: {{ $viatico->created_at->format('d/m/Y') }}</p>
