@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Cambio obligatorio de contraseña')

@section('auth_body')

<div class="alert alert-warning text-center">
    Debes cambiar tu contraseña para continuar
</div>

<form method="POST" action="{{ route('password.change.update') }}">
    @csrf
<div class="mb-2"> 
    <input class="form-control" type="password" name="password" required>
</div>
<div class="mb-3"> 
      <input class="form-control" type="password" name="password_confirmation" required>
</div>
<button class="form-control btn btn-success" type="submit">Actualizar</button>
</form>





@endsection
