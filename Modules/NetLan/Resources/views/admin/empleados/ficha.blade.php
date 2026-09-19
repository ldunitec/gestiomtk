<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha de Empleado</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .foto {
            width: 120px;

            /* height: 100px; */
            background-color: #000000;
            border-radius: 10%;
            /* Esto lo hace ovalado automáticamente */
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .section {
            background: #eaeaea;
            font-weight: bold;
            padding: 5px;
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        .no-border td {
            border: none;
        }

        .center {
            text-align: center;
        }

        .firma {
            margin-top: 30px;
            text-align: center;
        }

        .linea {
            width: 200px;
            border-top: 1px solid #000;
            margin: auto;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <table class="no-border">
        <tr>
            <td width="20%">
                <img src="{{ public_path('/img/logo mega.png') }}" width="150">
                {{-- H:\xampp\htdocs\mtk\public\img\logo mega.png --}}
            </td>

            <td width="60%" class="center">
                <div class="title">FICHA DE EMPLEADO</div>
                <div class="subtitle">MEGATK HN</div>
            </td>

            <td width="20%" class="center">
                <img class="foto" src="{{ public_path('storage/' . $empleado->foto) }}" width="80">
            </td>
        </tr>
    </table>

    <!-- DATOS PERSONALES -->
    <div class="section">Datos Personales</div>
    <table>
        <tr>
            <td><strong>Nombre:</strong> {{ $empleado->nombres }}</td>
            <td><strong>Apellidos:</strong> {{ $empleado->apellidos }}</td>
        </tr>
        <tr>
            <td><strong>Identidad:</strong> {{ $empleado->dni }}</td>
            <td><strong>Teléfono:</strong> {{ $empleado->telefono }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Dirección:</strong> {{ $empleado->direccion }}</td>
        </tr>
    </table>

    <!-- DATOS LABORALES -->
    <div class="section">Datos Laborales</div>
    <table>
        <tr>
            <td><strong>Puesto:</strong> {{ $empleado->puesto }}</td>
            <td><strong>Departamento:</strong> {{ $empleado->departamento->nombre ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Fecha de Ingreso:</strong> {{ $empleado->fecha_ingreso }}</td>
        </tr>
    </table>

    <!-- CONTACTO -->
    <div class="section">Contacto de Emergencia</div>
    <table>
        <tr>
            <td><strong>Nombre:</strong> {{ $empleado->contacto_nombre }}</td>
            <td><strong>Teléfono:</strong> {{ $empleado->contacto_telefono }}</td>
        </tr>
    </table>

    <!-- FIRMA -->
    <div class="firma">
        <div class="linea"></div>
        Firma del Empleado
    </div>

    <!-- DOCUMENTOS -->
    <div class="section">Documentos</div>
    <table>
        <tr>
            <!-- DNI -->
            <td width="50%" class="center">
                <strong>DNI (Copia)</strong><br><br>
                <img src="{{ public_path('storage/' . $empleado->fotoDni) }}"
                    style="width:90%; height:180px; border:1px solid #000;">
            </td>

            <!-- LICENCIA -->
            <td width="50%" class="center">
                <strong>Licencia de Conducir (Copia)</strong><br><br>
                <img src="{{ public_path('storage/' . $empleado->fotoLic) }}"
                    style="width:90%; height:180px; border:1px solid #000;">
            </td>
        </tr>
    </table>

</body>

</html>
