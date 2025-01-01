<?php
// Conexión a la base de datos (XAMPP)
$host = 'localhost'; // Dirección del servidor
$db = 'sidium'; // Nombre de la base de datos
$user = 'root'; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos (vacío por defecto en XAMPP)

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo de la solicitud para guardar recordatorios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO recordatorios (titulo, fecha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $titulo, $fecha);
        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Recordatorio guardado con éxito."]);
        } else {
            echo json_encode(["mensaje" => "Error al guardar el recordatorio."]);
        }
        $stmt->close();
    }
    // Cerrar la conexión
    $conn->close();
    exit; // Asegurarse de salir después de manejar la solicitud
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica - Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="css/style1.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js'></script>
    <script>
        let calendar;
        const events = [];

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: true,
                selectable: true,
                events: events,
                dateClick: function (info) {
                    const title = prompt('Ingrese el título del recordatorio:');
                    if (title) {
                        const eventData = {
                            title: title,
                            start: info.dateStr,
                            allDay: true
                        };

                        // Guardar recordatorio en la base de datos
                        fetch('', { // Hacemos la solicitud a la misma página
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `titulo=${encodeURIComponent(title)}&fecha=${encodeURIComponent(info.dateStr)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.mensaje); // Muestra el mensaje de respuesta
                            calendar.addEvent(eventData);
                            events.push(eventData);
                        })
                        .catch(error => console.error('Error:', error));
                    }
                },
                eventClick: function (info) {
                    if (confirm('¿Deseas eliminar este evento?')) {
                        info.event.remove();
                        const index = events.indexOf(info.event);
                        if (index > -1) {
                            events.splice(index, 1);
                        }
                    } else {
                        info.event.setProp('title', prompt('Modifica el título del recordatorio:', info.event.title) || info.event.title);
                    }
                }
            });
            calendar.render();
        });

        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</head>

<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Administrador</h2>
    </header>

    <nav>
        <a href="administrador.php" onclick="redirectTo('administrador.php');">Inicio</a>
        <a href="modifiacion_calif.php" onclick="redirectTo('modificar_calificaciones.php');">Modificar Calificaciones</a>
        <a href="gestion_usuarios.php" onclick="redirectTo('gestionar_usuarios.php');">Gestionar Usuarios</a>
        <a href="gestion_asignaturas.php" onclick="redirectTo('agregar_asignatura.php');">Agregar Nueva Asignatura</a>
    </nav>

    <div class="container">
        <main class="main-content">
            <div class="notifications">
                <h3>Notificaciones</h3>
                <p>No hay nuevas notificaciones.</p>
            </div>

            <h3>Calendario</h3>
            <div class="calendar" id="calendar"></div>

            <h3>Acciones Rápidas</h3>
            <div class="actions">
                <button onclick="redirectTo('enviar_notificacion.html');">Enviar Notificación a Estudiantes</button>
                <button onclick="redirectTo('generar_reporte.html');">Generar Reporte de Calificaciones</button>
                <button onclick="redirectTo('consultar_actividades.html');">Consultar Actividades Pendientes</button>
                <button onclick="redirectTo('exportar_datos.html');">Exportar Datos</button>
            </div>
        </main>
    </div>

</body>

</html>
