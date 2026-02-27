@extends('adminlte::page')

@section('title', 'Diseños CR80')

@section('content')
<h4>🎴 Diseños CR80</h4>

<a href="{{ route('idcards.create') }}" class="btn btn-primary mb-3">
    ➕ Nuevo diseño
</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Elementos</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($designs as $design)
        <tr>
            <td>{{ $design->id }}</td>
            <td>{{ $design->name }}</td>
            <td>
                {{ is_array($design->design_json) ? count($design->design_json) : 0 }}
            </td>
            <td>
                <a href="{{ route('idcards.edit', $design->id) }}"
                   class="btn btn-primary btn-sm">
                    ✏️ Editar
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

@section('js')
<script>
    $(function () {
        $('.table').DataTable({
            order: [[0, 'desc']],
            responsive: true
        });
    });
</script>
@stop
