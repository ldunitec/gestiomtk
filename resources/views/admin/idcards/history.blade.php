@extends('adminlte::page')
@section('content')
<h4>Historial de impresiones</h4>
<table class="table table-bordered" id="historyTable">
<thead><tr>
<th>Fecha</th><th>Usuario</th><th>Diseño</th><th>Tipo</th><th>Total</th><th>Acciones</th>
</tr></thead>
<tbody>
@foreach($histories as $h)
<tr>
<td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
<td>{{ $h->user->name }}</td>
<td>{{ $h->design->name }}</td>
<td>{{ strtoupper($h->type) }}</td>
<td>{{ $h->total }}</td>
<td>
<button class="btn btn-sm btn-info" onclick="showPersons({{ json_encode($h->person_ids) }})">👁 Ver</button>
<form method="POST" action="{{ route('idcards.print.mass') }}" style="display:inline;">
@csrf
<input type="hidden" name="design_id" value="{{ $h->card_design_id }}">
<input type="hidden" name="ids" value='{{ json_encode($h->person_ids) }}'>
<button class="btn btn-warning btn-sm">🔁 Reimprimir</button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>

{{-- Modal --}}
<div class="modal fade" id="modalPersons"><div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header"><h5>Personas impresas</h5></div>
<div class="modal-body"><ul id="personList"></ul></div>
</div></div></div>

<script>
function showPersons(ids){
    let ul=document.getElementById('personList'); ul.innerHTML='';
    ids.forEach(id=>{ ul.innerHTML+=`<li>ID Persona: ${id}</li>`; });
    $('#modalPersons').modal('show');
}
</script>
@endsection
k