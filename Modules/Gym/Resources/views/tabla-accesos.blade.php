<div class="table-responsive"><table class="table table-sm table-striped">
<thead><tr><th>Fecha y hora</th><th>Socio / código</th><th>Movimiento</th><th>Resultado</th></tr></thead><tbody>
@forelse($accesos as $a)
<tr><td>{{ $a->momento->timezone(config('gym.timezone'))->format('d/m/Y H:i:s') }}</td><td>{{ $a->socio?->nombre ?? $a->codigo }}</td><td>{{ ucfirst($a->movimiento) }}</td><td><span class="badge badge-{{ $a->permitido ? 'success' : 'danger' }}">{{ $a->motivo }}</span></td></tr>
@empty<tr><td colspan="4">No hay movimientos.</td></tr>@endforelse
</tbody></table></div>
