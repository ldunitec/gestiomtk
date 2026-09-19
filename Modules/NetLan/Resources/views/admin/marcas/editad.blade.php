@extends('adminlte::page')

@section('title', 'Editar Materia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><b>Editar Materia: {{ $materia->nombre }}</b></h1>
        <a href="{{ route('materias.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver a la lista
        </a>
    </div>
    <hr class="mt-2 mb-3">
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Modifique los datos necesarios</h3>
                </div>
                
                <form action="{{ route('materias.update', $materia->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Select Carrera --}}
                        <div class="form-group">
                            <label for="carrera_id">Nombre de la carrera <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                </div>
                                <select name="carrera_id" id="carrera_id" class="form-control @error('carrera_id') is-invalid @enderror" required>
                                    <option value="" disabled>-- Seleccione una carrera --</option>
                                    @foreach ($carreras as $carrera)
                                        <option value="{{ $carrera->id }}" {{ old('carrera_id', $materia->carrera_id) == $carrera->id ? 'selected' : '' }}>
                                            {{ $carrera->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('carrera_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Nombre de la Materia --}}
                        <div class="form-group">
                            <label for="nombre">Nombre de la materia <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                </div>
                                <input type="text" 
                                       id="nombre"
                                       name="nombre" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $materia->nombre) }}" 
                                       placeholder="Ingrese el nombre de la materia" 
                                       required>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Código de la Materia --}}
                        <div class="form-group">
                            <label for="codigo">Código de la materia <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                </div>
                                <input type="text" 
                                       id="codigo"
                                       name="codigo" 
                                       class="form-control @error('codigo') is-invalid @enderror" 
                                       value="{{ old('codigo', $materia->codigo) }}" 
                                       placeholder="Ej: MAT-101" 
                                       required>
                                @error('codigo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('materias.index') }}" class="btn btn-secondary mr-1">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-sync-alt mr-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop