@if ($info)
    <p><strong>Puerto:</strong> {{ $info->port_number }}</p>
    <p><strong>Estado:</strong> {{ $info->id }}</p>
    <!-- etc -->
@else
    <p class="text-danger">No se encontró información para este puerto.</p>
@endif
