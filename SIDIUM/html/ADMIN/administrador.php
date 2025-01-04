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
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js'></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        nav {
            background-color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            position: relative;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .bar {
            height: 3px;
            width: 25px;
            background-color: white;
            margin: 3px 0;
            transition: 0.3s;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #4CAF50;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #333;
            top: 60px;
            left: 0;
            width: 100%;
            z-index: 1;
            align-items: center;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-align: left;
        }

        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .calendar {
            background: #eaeaea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .actions button {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
        }

        .actions button:hover {
            background-color: #0056b3;
            transform: scale(1.03);
        }

        .notifications {
            background-color: #f1f8e9;
            padding: 15px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex; /* Mostrar menú hamburguesa en móviles */
            }

            nav a {
                display: none; /* Ocultar links de navegación en móviles */
            }

            .dropdown-menu {
                display: flex; /* No mostrar inicialmente */
                flex-direction: column;
                visibility: hidden; /* Ocultar inicialmente */
                opacity: 0;
                transition: visibility 0s 0.2s, opacity 0.2s; /* Agregar transición */
            }

            .dropdown-menu.show {
                visibility: visible;
                opacity: 1;
                transition: opacity 0.2s; /* Solo mostrar */
            }
        }
    </style>
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

        // Menú hamburguesa
        document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menu-toggle');
        const dropdownMenu = document.getElementById('dropdown-menu');

        menuToggle.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show'); // Alternar clase 'show' para mostrar u ocultar el menú
        });

        // Cerrar el menú al hacer clic en un enlace
        dropdownMenu.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
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
        <div class="menu-toggle" id="menu-toggle">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <div class="dropdown-menu" id="dropdown-menu">
            <a href="administrador.php" onclick="redirectTo('administrador.php'); return false;">Inicio</a>
            <a href="modificacion_calif.php" onclick="redirectTo('modificacion_calif.php'); return false;">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php" onclick="redirectTo('gestion_usuarios.php'); return false;">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php" onclick="redirectTo('gestion_asignaturas.php'); return false;">Agregar Nueva Asignatura</a>
        </div>
        <div class="desktop-menu">
            <a href="administrador.php" onclick="redirectTo('administrador.php'); return false;">Inicio</a>
            <a href="modificacion_calif.php" onclick="redirectTo('modificacion_calif.php'); return false;">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php" onclick="redirectTo('gestion_usuarios.php'); return false;">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php" onclick="redirectTo('gestion_asignaturas.php'); return false;">Agregar Nueva Asignatura</a>
        </div>
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