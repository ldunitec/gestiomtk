<style>
body { font-family: DejaVu Sans; }

footer {
    position: fixed;
    bottom: 0;
    text-align: center;
    font-size: 12px;
}
</style>

<h1>{{ $documentacion->titulo }}</h1>
<p>Fecha: {{ now()->format('d/m/Y') }}</p>

<h3>Contenido</h3>
<ul>
@foreach($secciones as $sec)
    <li>{{ $sec->titulo }}</li>
@endforeach
</ul>

@foreach($secciones as $sec)
    <h2>{{ $sec->titulo }}</h2>
    <div>{!! $sec->contenido !!}</div>
@endforeach

<footer>
    Página <span class="page"></span>
</footer>