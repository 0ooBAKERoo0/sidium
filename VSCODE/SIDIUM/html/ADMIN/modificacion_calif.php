<?php

include("verifica_sesion.php");


// Conexión a la base de datos
$host = 'localhost';
$db   = 'sidium';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$alumno = null;
$docente = null;
$nombreDestinatario = '';
$matriculaDestinatario = '';

// Procesar formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Búsqueda de alumno/docente
    if (isset($_POST['matricula'])) {
        $matricula = $_POST['matricula'];

        // Buscar alumno
        $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
        $stmt->execute([$matricula]);
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no es alumno, buscar docente
        if (!$alumno) {
            $stmt = $pdo->prepare("SELECT * FROM docentes WHERE matricula = ?");
            $stmt->execute([$matricula]);
            $docente = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Establecer datos del destinatario
        if ($alumno) {
            $nombreDestinatario = $alumno['nombre'];
            $matriculaDestinatario = $alumno['matricula'];
        } elseif ($docente) {
            $nombreDestinatario = $docente['nombre'];
            $matriculaDestinatario = $docente['matricula'];
        }
    }
    
    // Envío de notificación
    if (isset($_POST['enviar_notificacion']) && isset($_POST['mensaje'])) {
        $mensaje = $_POST['mensaje'];
        $matriculaDestinatario = $_POST['matricula_destinatario'];

        if ($matriculaDestinatario) {
            try {
                $stmt = $pdo->prepare("INSERT INTO notificaciones_temporales (matricula, mensaje, fecha) VALUES (?, ?, NOW())");
                $stmt->execute([$matriculaDestinatario, $mensaje]);
                echo '<script>alert("Notificación enviada exitosamente.");</script>';
            } catch(PDOException $e) {
                echo '<script>alert("Error al enviar la notificación: ' . $e->getMessage() . '");</script>';
            }
        } else {
            echo '<script>alert("No se encontró un destinatario válido.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style2.css">
    <style>
        /* Estilos adicionales si son necesarios */
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        textarea {
            width: 100%;
            min-height: 100px;
            margin: 10px 0;
            padding: 8px;
        }

        button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
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
            <a href="modificacion_calif.php">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php">Agregar Nueva Asignatura</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
        <div class="desktop-menu">
            <a href="modificacion_calif.php">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php">Agregar Nueva Asignatura</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <main class="main-content">
            <div class="notifications">
                <h3>Notificaciones</h3>
                <p>No hay nuevas notificaciones.</p>
            </div>

            <h3>Modificar Calificaciones</h3>

            <!-- Formulario de búsqueda -->
            <form method="POST" class="dropdown">
                <label for="matricula">Buscar por Matrícula:</label>
                <input type="text" id="matricula" name="matricula" placeholder="Ingrese matrícula" required>
                <button type="submit">Buscar</button>
            </form>

            <?php if ($alumno || $docente): ?>
                <div id="alumno-info">
                    <div id="nombre-alumno">Nombre: <?= htmlspecialchars($nombreDestinatario) ?></div>
                    <?php if ($alumno): ?>
                        <div id="grupo-alumno">Grupo: <?= htmlspecialchars($alumno['grupo']) ?></div>
                    <?php endif; ?>

                    <div class="calificaciones-section">
                        <label for="asignatura">Seleccionar Asignatura:</label>
                        <select id="asignatura">
                            <option value="matematica">Matemáticas</option>
                            <option value="programacion">Programación</option>
                            <option value="historia">Historia</option>
                            <option value="quimica">Química</option>
                        </select>

                        <label for="calificacion">Seleccionar Calificación:</label>
                        <select id="calificacion">
                            <option value="10">10</option>
                            <option value="9">9</option>
                            <option value="8">8</option>
                            <option value="7">7</option>
                            <option value="6">6</option>
                        </select>

                        <button onclick="modificarCalificacion()">Modificar Calificación</button>
                    </div>

                    <!-- Formulario de notificación -->
                    <div class="notificacion-section">
                        <h3>Enviar Notificación</h3>
                        <form method="POST">
                            <input type="hidden" name="matricula_destinatario" value="<?= htmlspecialchars($matriculaDestinatario) ?>">
                            <label for="mensaje">Mensaje:</label>
                            <textarea id="mensaje" name="mensaje" required></textarea>
                            <button type="submit" name="enviar_notificacion">Enviar Notificación</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div id="alumno-info">
                    <p>No se encontró ningún registro con esa matrícula.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Función para el menú hamburguesa
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const dropdownMenu = document.getElementById('dropdown-menu');

            menuToggle.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show');
            });

            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!menuToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });

        // Función para modificar calificación
        function modificarCalificacion() {
            const asignatura = document.getElementById('asignatura').value;
            const calificacion = document.getElementById('calificacion').value;
            alert('Calificación modificada: ' + asignatura + ' - ' + calificacion);
        }
    </script>
</body>
</html>