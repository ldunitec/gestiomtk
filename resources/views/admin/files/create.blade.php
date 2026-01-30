@extends('adminlte::page')

@section('content')
<form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-3">
            <input name="brand" class="form-control" placeholder="Marca">
        </div>
        <div class="col-md-3">
            <input name="category" class="form-control" placeholder="Categoría">
        </div>
        <div class="col-md-3">
            <input name="model" class="form-control" placeholder="Modelo">
        </div>
        <div class="col-md-3">
            <select name="descrip" id="" class="form-control">
                <option value="Driver">Driver</option>
                <option value="Firmware">Firmware</option>
                <option value="Manual">Manual</option>
                <option value="Tool">Tool</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="file" name="file" class="form-control">
        </div>
    </div>

    <button class="btn btn-primary mt-3">Guardar</button>
</form>
@endsection
