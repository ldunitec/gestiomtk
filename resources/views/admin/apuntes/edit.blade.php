@extends('adminlte::page')

@section('title', 'Editar Apunte Seguro')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Apunte Seguro</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('admin.apuntes.update', $apunte) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" 
                       class="form-control @error('titulo') is-invalid @enderror" 
                       value="{{ old('titulo', $apunte->titulo) }}" required>
                @error('titulo')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="lan" {{ $apunte->tipo=='lan'?'selected':'' }}>LAN / Equipo</option>
                    <option value="web" {{ $apunte->tipo=='web'?'selected':'' }}>Web / Servicio</option>
                    <option value="servidor" {{ $apunte->tipo=='servidor'?'selected':'' }}>Servidor</option>
                    <option value="hosting" {{ $apunte->tipo=='hosting'?'selected':'' }}>Hosting</option>
                    <option value="dominio" {{ $apunte->tipo=='dominio'?'selected':'' }}>Dominio</option>
                    <option value="otro" {{ $apunte->tipo=='otro'?'selected':'' }}>Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ip">IP (opcional)</label>
                <input type="text" name="ip" id="ip" class="form-control" value="{{ old('ip', $apunte->ip) }}">
            </div>

            <div class="form-group">
                <label for="url">URL (opcional)</label>
                <input type="text" name="url" id="url" class="form-control" value="{{ old('url', $apunte->url) }}">
            </div>

            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="{{ old('usuario', $apunte->usuario) }}" >
            </div>
           
            <div class="form-group">
                <label for="password">Contraseña (dejar vacío si no cambia)</label>
                <div class="input-group">
                    <input type="text" name="password" id="password" class="form-control">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-info" onclick="generarPassword()">
                            <i class="fas fa-key"></i> Generar
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="motivo">Motivo del cambio de contraseña</label>
                <textarea name="motivo" id="motivo" rows="2" class="form-control" placeholder="Describe por qué se cambia la contraseña"></textarea>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción / Notas</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="form-control">{{ old('descripcion', $apunte->descripcion) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar Apunte
            </button>
            <a href="{{ route('admin.apuntes.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>
    </div>
</div>

@stop

@section('js')
<script>
// Generador de contraseña PRO seguro
function generarPassword() {
    const length = 20;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
    let password = "";
    const values = new Uint32Array(length);
    window.crypto.getRandomValues(values);
    for (let i = 0; i < length; i++) {
        password += charset[values[i] % charset.length];
    }
    document.getElementById('password').value = password;
}
</script>
@stop
