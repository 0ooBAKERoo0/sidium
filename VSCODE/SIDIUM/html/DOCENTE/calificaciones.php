<?php

include("verifica_sesion.php");

// Configuración de la base de datos
$host = 'localhost'; 
$user = 'root'; 
$password = ''; 
$dbname = 'sidium'; 

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_asignatura = null;
$sql_asignaturas = "SELECT id, nombre, matricula FROM asignaturas"; 
$result_asignaturas = $conn->query($sql_asignaturas);
if ($result_asignaturas->num_rows > 0) {
    $row_asignatura = $result_asignaturas->fetch_assoc();
    $id_asignatura = $row_asignatura['id'];
}

$sql_alumnos = "SELECT nombre, matricula FROM alumnos"; 
$result_alumnos = $conn->query($sql_alumnos);

// Procesar datos de calificaciones al guardar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['calificaciones'] as $matricula_alumno => $calificacion) {
        $parcial1 = $conn->real_escape_string($calificacion['parcial1']);
        $parcial2 = $conn->real_escape_string($calificacion['parcial2']);
        $calificacionFinal = $conn->real_escape_string($calificacion['final']);

        // Inserta en la tabla calificaciones
        $sql_insert = "INSERT INTO calificaciones (id_asignatura, matricula_alumno, parcial1, parcial2, calificacion_final) 
                       VALUES ('$id_asignatura', '$matricula_alumno', '$parcial1', '$parcial2', '$calificacionFinal')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "Calificaciones guardadas para el alumno con matrícula $matricula_alumno.<br>";
        } else {
            echo "Error al guardar calificaciones: " . $conn->error . "<br>";
        }
    }
}

function get_calificaciones($conn, $id_asignatura) {
    $sql_calificaciones = "SELECT matricula_alumno, parcial1, parcial2, calificacion_final FROM calificaciones WHERE id_asignatura = '$id_asignatura'";
    return $conn->query($sql_calificaciones);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones - Sistema de Gestión Académica</title>
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

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
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
                <a href="docentes.php" onclick="redirectTo('docentes.php');">Inicio</a>
                <a href="asistencias.php" onclick="redirectTo('asistencia.php');">Asistencia</a>
                <a href="calificaciones.php" onclick="redirectTo('calificaciones.php');">Calificaciones</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <main class="main-content">
            <h3>Registro de Calificaciones</h3>

            <?php
            if ($id_asignatura !== null) {
                echo "<h4>Asignatura: " . htmlspecialchars($row_asignatura['nombre']) . "</h4>";
                echo "<h4>Clave de Asignatura: " . htmlspecialchars($row_asignatura['matricula']) . "</h4>";
            }
            ?>

            <div class="table-container">
                <form method="POST" action="">
                    <table id="gradesTable">
                        <thead>
                            <tr>
                                <th>Nombre del Alumno</th>
                                <th>Calificación Parcial 1</th>
                                <th>Calificación Parcial 2</th>
                                <th>Calificación Final</th>
                                <th>Guardar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtener las calificaciones guardadas
                            $calificaciones_result = get_calificaciones($conn, $id_asignatura);
                            $calificaciones_guardadas = [];
                            if ($calificaciones_result->num_rows > 0) {
                                while ($row_calificacion = $calificaciones_result->fetch_assoc()) {
                                    $calificaciones_guardadas[$row_calificacion['matricula_alumno']] = $row_calificacion;
                                }
                            }

                            if ($result_alumnos->num_rows > 0) {
                                while ($row_alumno = $result_alumnos->fetch_assoc()) {
                                    $matricula = htmlspecialchars($row_alumno['matricula']);
                                    echo "<tr>";
                                    echo "<td><input type='text' name='$matricula' value='" . htmlspecialchars($row_alumno['nombre']) . "' disabled /></td>";

                                    if (isset($calificaciones_guardadas[$matricula])) {
                                        // Muestra las calificaciones guardadas
                                        $calif_parcial1 = htmlspecialchars($calificaciones_guardadas[$matricula]['parcial1']);
                                        $calif_parcial2 = htmlspecialchars($calificaciones_guardadas[$matricula]['parcial2']);
                                        $calif_final = htmlspecialchars($calificaciones_guardadas[$matricula]['calificacion_final']);
                                        echo "<td><input type='number' value='$calif_parcial1' disabled /></td>";
                                        echo "<td><input type='number' value='$calif_parcial2' disabled /></td>";
                                        echo "<td><input type='number' value='$calif_final' disabled /></td>";
                                        echo "<td>-</td>"; // Columna para guardar vacía
                                    } else {
                                        // Campos para ingresar calificaciones
                                        echo "<td><input type='number' name='calificaciones[$matricula][parcial1]' placeholder='0.00' step='0.01' /></td>";
                                        echo "<td><input type='number' name='calificaciones[$matricula][parcial2]' placeholder='0.00' step='0.01' /></td>";
                                        echo "<td><input type='number' name='calificaciones[$matricula][final]' placeholder='0.00' step='0.01' /></td>";
                                        echo "<td><button type='submit'>Guardar</button></td>";
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay registros disponibles.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>