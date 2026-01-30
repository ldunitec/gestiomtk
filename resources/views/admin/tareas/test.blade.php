<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Prueba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 900px;
            margin: 40px auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mt-4">📅 Agenda Semanal (Eventos de Prueba)</h3>
    <div id="calendar"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'es',
        height: 'auto',
        events: [
            {
                id: 1,
                title: 'Reunión con equipo',
                start: '2025-08-19T09:00:00',
                end:   '2025-08-19T10:30:00'
            },
            {
                id: 2,
                title: 'Entrega de proyecto',
                start: '2025-08-20T13:00:00',
                end:   '2025-08-20T15:00:00'
            },
            {
                id: 3,
                title: 'Almuerzo con cliente',
                start: '2025-08-21T12:00:00',
                end:   '2025-08-21T13:00:00'
            }
        ]
    });

    calendar.render();
});
</script>
</body>
</html>
