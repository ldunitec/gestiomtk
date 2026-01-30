@extends('adminlte::page')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-2">
 <div class="icon">
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-width:120px;">
                    </div>
        </div>

        <div class="col-md-8 text-center">
            <h1><b>Listado de equipos {{ strtoupper($brand->name) }}</b></h1>
        </div>

    </div>
    <hr>
@stop

@section('content')

    {{-- SECCIONES DINÁMICAS --}}
    @php
        $sections = config('brands.' . $brand->slug, []);
    @endphp

    <div class="row">
        @foreach ($sections as $type => $title)
            @if (isset($files[$type]))
                <div class="col-md-12">
                    <div class="card card-{{ $brand->color_primary }} collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>


                        <div class="card-body">
                            @foreach ($files[$type]->groupBy('model') as $modelo => $items)
                                <div class="card card-outline card-{{ $brand->color_primary }} mb-2">
                                {{-- <div class="card card-outline card-primary collapsed-card mb-2"> --}}
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Modelo: <b>{{ $modelo }}</b>
                                            <span class="badge badge-{{ $brand->color_primary }} ml-2">{{ $items->count() }}</span>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-sm table-bordered mb-0">
                                            @foreach ($items as $file)
                                                <tr>
                                                    <td>{{ $file->name }}</td>
                                                    <td width="180">
                                                       
                                                        <a href="{{ $file->link_drive }}"
                                                            class="btn btn-{{ $brand->color_primary }}  w-50 btn-sm"><i class="fas fa-solid fa-download"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- NOTAS --}}
    <div class="card mt-3">
        <div class="card-body">
            <form id="formNota">
                @csrf
                <input type="hidden" name="brand_id" value="{{ $brand->id }}">

                <div class="row">
                    <div class="col-md-2">
                        <input type="text" name="titulo" class="form-control" placeholder="Título">
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="nota" class="form-control" placeholder="Nota" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-{{ $brand->color_primary }} w-100">Agregar</button>
                    </div>
                </div>
            </form>

            <hr>

            <table class="table table-bordered">
                @foreach ($notes as $note)
                    <tr>
                        <td>{{ $note->id }}</td>
                        <td>{{ $note->titulo }}</td>
                        <td>{{ $note->nota }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{-- @include('admin.parciales.modal') --}}
@stop

@section('js')
    <script>
        $('#formNota').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('admin.notes.store') }}", $(this).serialize())
                .done(() => location.reload())
                .fail(() => alert('Error'));
        });
    </script>
@stop
