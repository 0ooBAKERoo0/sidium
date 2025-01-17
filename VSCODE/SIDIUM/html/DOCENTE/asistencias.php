<?php

include("verifica_sesion.php");

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambiar según necesario
$password = ""; // Cambiar si se ha establecido contraseña
$dbname = "sidium"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener la lista de alumnos
$sql = "SELECT matricula, nombre FROM alumnos";
$result = $conn->query($sql);
$alumnos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .navbar {
            display: flex;
            flex-direction: column; /* Cambiar a columna para permitir centrado */
            align-items: center; /* Centrar items en la columna */
            background-color: #333;
            padding: 10px;
        }

        .nav-links {
            display: flex;
            justify-content: center; /* Centramos los enlaces */
            width: 100%; /* Permitimos que tome toda la anchura */
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #4CAF50;
        }

        .menu-toggle {
            display: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            margin-bottom: 10px; /* Espacio debajo del botón */
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0 auto;
            width: 100%;
            max-width: 1200px;
        }

        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        h3 {
            margin-bottom: 20px;
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            overflow-x: auto;
            display: block;
            max-width: 100%;
            white-space: nowrap;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column; /* Cambiar a columna para el menú móvil */
                align-items: center; /* Centrar elementos en la columna */
            }

            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                text-align: center; /* Centrar texto para el menú toggle */
            }

            .nav-links.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }

            nav a {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Docente</h2>
    </header>

    <div class="navbar">
        <div class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="nav-links">
            <nav>
                <a href="docentes.php">Inicio</a>
                <a href="asistencias.php">Asistencia</a>
                <a href="calificaciones.php">Calificaciones</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <main class="main-content">
            <h3>Registro de Asistencia</h3>

            <h4>Lista de Asistencia por Día</h4>
            <form id="attendanceForm">
                <div class="table-container">
                    <table id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Matricula</th>
                                <th>Nombre</th>
                                <th>Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($alumnos as $alumno) {
                                echo "<tr>
                                        <td>{$alumno['matricula']}</td>
                                        <td>{$alumno['nombre']}</td>
                                        <td>
                                            <select name='asistencia[{$alumno['matricula']}]'>
                                                <option value=''>Seleccionar</option>
                                                <option value='asistencia'>Asistencia</option>
                                                <option value='retardo'>Retardo</option>
                                                <option value='falta'>Falta</option>
                                            </select>
                                        </td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>

            <div id="message" style="margin-top: 20px;"></div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        $(document).ready(function () {
            $('#attendanceForm').on('submit', function (e) {
                e.preventDefault(); // Evitar el envío normal del formulario

                $.ajax({
                    url: 'save_attendance.php', // Archivo PHP que procesa la solicitud
                    method: 'POST',
                    data: $(this).serialize(), // Enviar datos del formulario serializados
                    success: function (response) {
                        $('#message').html(response); // Mostrar el mensaje de respuesta
                    },
                    error: function () {
                        $('#message').html('<span style="color: red;">Hubo un error al guardar la asistencia.</span>');
                    }
                });
            });
        });
    </script>
</body>

</html>