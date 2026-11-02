<div class="row">
@foreach(['nombres' => 'Nombres', 'apellidos' => 'Apellidos', 'documento' => 'Documento', 'telefono' => 'Teléfono', 'email' => 'Correo'] as $campo => $etiqueta)
<div class="form-group col-md-6"><label for="{{ $campo }}">{{ $etiqueta }}</label><input id="{{ $campo }}" name="{{ $campo }}" class="form-control" type="{{ $campo === 'email' ? 'email' : 'text' }}" maxlength="{{ ['documento'=>50,'telefono'=>40,'email'=>255][$campo] ?? 100 }}" value="{{ old($campo, $socio->$campo ?? '') }}" @if(in_array($campo, ['nombres','apellidos'])) required @endif></div>
@endforeach
<div class="form-group col-md-6"><label for="estado">Estado</label><select id="estado" name="estado" class="form-control">@foreach(['activo','suspendido','inactivo'] as $estado)<option value="{{ $estado }}" @selected(old('estado', $socio->estado ?? 'activo') === $estado)>{{ ucfirst($estado) }}</option>@endforeach</select></div>
</div><button class="btn btn-primary">Guardar socio</button>
