@extends('gym::layout')
@section('gym_content')
<p>La duración se cuenta en días calendario. Cambiar un plan no modifica las membresías ya vendidas.</p>
@foreach($planes as $plan)
<div class="card"><div class="card-header">{{ $plan->nombre }}</div><div class="card-body"><form method="POST" action="{{ route('gym.planes.actualizar', $plan) }}">@csrf @method('PUT') @include('gym::form-plan', ['p' => $plan, 'prefijo' => 'plan-'.$plan->id])</form></div></div>
@endforeach
{{ $planes->links('pagination::bootstrap-4') }}
<div class="card"><div class="card-header">Nuevo plan</div><div class="card-body"><form method="POST" action="{{ route('gym.planes.guardar') }}">@csrf @include('gym::form-plan', ['p' => null, 'prefijo' => 'nuevo'])</form></div></div>
@stop
