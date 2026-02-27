<!DOCTYPE html>
<html>
<head>
    <title>Todoist API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>🔹 Obtener Proyectos</h2>
<button id="btnProjects">Cargar Proyectos</button>

<pre id="projectsResult" style="background:#f4f4f4;padding:10px;"></pre>

<hr>

<h2>🔹 Obtener Usuarios por Proyecto</h2>
<input type="text" id="projectId" placeholder="Ingrese Project ID">
<button id="btnUsers">Cargar Usuarios</button>

<pre id="usersResult" style="background:#f4f4f4;padding:10px;"></pre>

<script>
    $('#btnProjects').click(function(){
        $.get('/todoist/projects', function(data){
            $('#projectsResult').text(JSON.stringify(data, null, 4));
        });
    });

    $('#btnUsers').click(function(){
        let projectId = $('#projectId').val();

        if(!projectId){
            alert('Ingrese un Project ID');
            return;
        }

        $.get('/todoist/users/' + projectId, function(data){
            $('#usersResult').text(JSON.stringify(data, null, 4));
        });
    });
</script>

</body>
</html>
