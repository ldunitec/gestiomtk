@extends('adminlte::page')

@section('title', 'Subir Drivers')

@section('content_header')
    <h1>Subir Drivers y Manuales</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-4">
                        <label>Marca</label>
                        <div class="input-group">
                            <select id="brand_id" class="form-control">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-success" data-toggle="modal" data-target="#brandModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label>Categoría</label>
                        <div class="input-group">
                            <select id="category_id" class="form-control">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-success" data-toggle="modal" data-target="#categoryModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label>Modelo</label>
                        <div class="input-group">
                            <select name="model_id" id="model_id" class="form-control" required>
                                @foreach ($models as $model)
                                    <option value="{{ $model->id }}">
                                        {{ $model->brand->name }} - {{ $model->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-success" data-toggle="modal" data-target="#modelModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <label>Driver Windows</label>
                        <input type="file" name="windows" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Driver Mac</label>
                        <input type="file" name="mac" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Manual (PDF)</label>
                        <input type="file" name="manual" class="form-control">
                    </div>
                </div>

                <button class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Guardar
                </button>

            </form>

        </div>
    </div>

{{-- Modal MArca  --}}

<div class="modal fade" id="brandModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nueva Marca</h5>
      </div>
      <div class="modal-body">
        <input type="text" id="brand_name" class="form-control">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="saveBrand()">Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Categorias --}}

<div class="modal fade" id="categoryModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nueva Categoría</h5>
      </div>
      <div class="modal-body">
        <input type="text" id="category_name" class="form-control">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="saveCategory()">Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Modelos  --}}

<div class="modal fade" id="modelModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nuevo Modelo</h5>
      </div>
      <div class="modal-body">
        <input type="text" id="model_name" class="form-control">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="saveModel()">Guardar</button>
      </div>
    </div>
  </div>
</div>


@stop

@section('js')
<script>
const csrf = '{{ csrf_token() }}';

function saveBrand() {
    fetch('{{ route("brands.ajax.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            name: document.getElementById('brand_name').value
        })
    }).then(r=>r.json()).then(data=>{
        document.getElementById('brand_id')
            .insertAdjacentHTML('beforeend',
            `<option value="${data.id}" selected>${data.name}</option>`);
        $('#brandModal').modal('hide');
    });
}

function saveCategory() {
    fetch('{{ route("categories.ajax.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            name: document.getElementById('category_name').value
        })
    }).then(r=>r.json()).then(data=>{
        document.getElementById('category_id')
            .insertAdjacentHTML('beforeend',
            `<option value="${data.id}" selected>${data.name}</option>`);
        $('#categoryModal').modal('hide');
    });
}

function saveModel() {
    fetch('{{ route("models.ajax.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            name: document.getElementById('model_name').value,
            brand_id: document.getElementById('brand_id').value,
            category_id: document.getElementById('category_id').value
        })
    }).then(r=>r.json()).then(data=>{
        document.getElementById('model_id')
            .insertAdjacentHTML('beforeend',
            `<option value="${data.id}" selected>${data.name}</option>`);
        $('#modelModal').modal('hide');
    });
}
</script>
@stop

