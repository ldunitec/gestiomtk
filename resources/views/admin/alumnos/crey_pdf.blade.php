<h2>Curso: {{ $curso }} - Sección: {{ $seccion }}</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>DNI</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alumnos as $alumno)
        <tr>
            <td>{{ $alumno->dni }}</td>
            <td>{{ $alumno->nombres }}</td>
            <td>{{ $alumno->apellidos }}</td>
            <td>{{ $alumno->observaciones }}</td>
        </tr>
        @endforeach
    </tbody>
</table>