<?php

include("verifica_sesion.php");

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sidium";  // Cambia esto por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$message = ""; // Variable para almacenar mensajes

// Manejo del formulario de agregar administradores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_admin'])) {
    // Recoger datos del formulario
    $nombre_admin = $_POST['nombre_admin'];
    $matricula_admin = $_POST['matricula_admin'];
    $password_admin = $_POST['password_admin']; // Guardamos la contraseña tal como se escribió

    // Inserción en la base de datos para administradores
    $sql_admin = "INSERT INTO admin_users (matricula, nombre, password) 
                  VALUES ('$matricula_admin', '$nombre_admin', '$password_admin')";
    
    if ($conn->query($sql_admin) === TRUE) {
        // También insertar en la tabla usuarios
        $sql_usuario = "INSERT INTO usuarios (matricula, password, tipo_cuenta) 
                        VALUES ('$matricula_admin', '$password_admin', 'administrador')";
        
        if ($conn->query($sql_usuario) === TRUE) {
            $message = "Nuevo administrador agregado correctamente.";
        } else {
            $message = "Error al agregar el usuario en la tabla usuarios: " . $conn->error;
        }
    } else {
        $message = "Error al agregar el administrador: " . $conn->error;
    }
}

// Manejo del formulario de agregar alumnos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_alumno'])) {
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];
    $grupo = $_POST['grupo'];
    $carrera_id = $_POST['carrera_id'];
    $nivel = $_POST['nivel'];
    $password = $_POST['password'];
    
    $sql = "INSERT INTO alumnos (matricula, nombre, grupo, carrera_id, nivel, password) 
            VALUES ('$matricula', '$nombre', '$grupo', $carrera_id, '$nivel', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        $sql_usuario = "INSERT INTO usuarios (matricula, password, tipo_cuenta) 
                        VALUES ('$matricula', '$password', 'alumno')";
        
        if ($conn->query($sql_usuario) === TRUE) {
            $message = "Nuevo alumno agregado correctamente.";
        } else {
            $message = "Error al agregar el usuario en la tabla usuarios: " . $conn->error;
        }
    } else {
        $message = "Error al agregar el alumno: " . $conn->error;
    }
}

// Manejo del formulario de agregar docentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_docente'])) {
    $nombre_docente = $_POST['nombre_docente'];
    $matricula_docente = $_POST['matricula_docente'];
    $materia = $_POST['materia'];
    $password_docente = $_POST['password_docente'];

    $sql = "INSERT INTO docentes (matricula, nombre, materia, password) 
            VALUES ('$matricula_docente', '$nombre_docente', '$materia', '$password_docente')";
    
    if ($conn->query($sql) === TRUE) {
        $sql_usuario = "INSERT INTO usuarios (matricula, password, tipo_cuenta) 
                        VALUES ('$matricula_docente', '$password_docente', 'docente')";
        
        if ($conn->query($sql_usuario) === TRUE) {
            $message = "Nuevo docente agregado correctamente.";
        } else {
            $message = "Error al agregar el usuario en la tabla usuarios: " . $conn->error;
        }
    } else {
        $message = "Error al agregar el docente: " . $conn->error;
    }
}

// Obtener usuarios
$alumnos = [];
$docentes = [];
$admins = [];

if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
    if ($tipo == 'alumnos') {
        $sql = "SELECT alumnos.*, carrera.nombre AS carrera_nombre 
                FROM alumnos 
                JOIN carrera ON alumnos.carrera_id = carrera.id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $alumnos[] = $row;
            }
        }
    } elseif ($tipo == 'docentes') {
        $sql = "SELECT * FROM docentes"; 
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $docentes[] = $row;
            }
        }
    } elseif ($tipo == 'admins') {
        $sql = "SELECT * FROM admin_users"; 
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $admins[] = $row;
            }
        }
    }
}

// Obtener las carreras para mostrarlas en el formulario
$carreras = [];
$sql = "SELECT * FROM carrera";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $carreras[] = $row;
    }
}

// Obtener las asignaturas para mostrarlas en el formulario de agregar docente
$asignaturas = [];
$sql = "SELECT * FROM asignaturas";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }
}

// Manejo de modificar y eliminar alumnos o docentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify_user'])) {
    // Similar a lo que ya tenías
    // ...
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Similar a lo que ya tenías
    // ...
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="css/style3.css">
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
            <h3>Gestión de Usuarios</h3>
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="user-selection">
                <button id="boton-agregar-alumno">Agregar Alumno</button>
                <button id="boton-agregar-docente">Agregar Docente</button>
                <button id="boton-agregar-admin">Agregar Admin</button>
                <button onclick="window.location.href='?tipo=alumnos'">Ver Alumnos</button>
                <button onclick="window.location.href='?tipo=docentes'">Ver Docentes</button>
                <button onclick="window.location.href='?tipo=admins'">Ver Admins</button>
            </div>

            <div class="form-container" id="formulario-agregar-alumno">
                <h4>Agregar Alumno</h4>
                <form method="POST">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" name="nombre" required><br>
                    
                    <label for="matricula">Matrícula:</label>
                    <input type="text" name="matricula" required><br>
                    
                    <label for="grupo">Grupo:</label>
                    <input type="text" name="grupo" required><br>
                    
                    <label for="carrera_id">Carrera:</label>
                    <select name="carrera_id" required>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera['id']; ?>"><?php echo $carrera['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    
                    <label for="nivel">Nivel:</label>
                    <select name="nivel" required>
                        <option value="Maestría">Maestría</option>
                        <option value="Doctorado">Doctorado</option>
                    </select><br>
                    
                    <label for="password">Contraseña:</label>
                    <input type="text" name="password" required><br>

                    <button type="submit" name="agregar_alumno">Agregar Alumno</button>
                </form>
            </div>

            <div class="form-container" id="formulario-agregar-docente">
                <h4>Agregar Docente</h4>
                <form method="POST">
                    <label for="nombre_docente">Nombre Completo:</label>
                    <input type="text" name="nombre_docente" required><br>
                    
                    <label for="matricula_docente">Matrícula:</label>
                    <input type="text" name="matricula_docente" required><br>
                    
                    <label for="materia">Materia:</label>
                    <select name="materia" required>
                        <?php foreach ($asignaturas as $asignatura): ?>
                            <option value="<?php echo $asignatura['nombre']; ?>"><?php echo $asignatura['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="password_docente">Contraseña:</label>
                    <input type="text" name="password_docente" required><br>

                    <button type="submit" name="agregar_docente">Agregar Docente</button>
                </form>
            </div>

            <div class="form-container" id="formulario-agregar-admin">
                <h4>Agregar Administrador</h4>
                <form method="POST">
                    <label for="nombre_admin">Nombre Completo:</label>
                    <input type="text" name="nombre_admin" required><br>
                    
                    <label for="matricula_admin">Matrícula:</label>
                    <input type="text" name="matricula_admin" required><br>

                    <label for="password_admin">Contraseña:</label>
                    <input type="text" name="password_admin" required><br>

                    <button type="submit" name="agregar_admin">Agregar Administrador</button>
                </form>
            </div>

            <div class="user-list" id="user-list">
                <?php if (isset($_GET['tipo'])): ?>
                    <?php if ($tipo == 'alumnos' && !empty($alumnos)): ?>
                        <h4>Alumnos por Nivel</h4>
                        <h5>Maestría</h5>
                        <?php foreach ($alumnos as $alumno): ?>
                        <?php if ($alumno['nivel'] === 'Maestría'): ?>
                        <p>
                        <?php echo $alumno['nombre']; ?> (Matrícula: <?php echo $alumno['matricula']; ?>, Grupo: <?php echo $alumno['grupo']; ?>, Carrera: <?php echo $alumno['carrera_nombre']; ?>)
                        <span>
                        <button onclick="showModifyForm('alumnos', <?php echo $alumno['id']; ?>, '<?php echo addslashes($alumno['nombre']); ?>', '<?php echo addslashes($alumno['matricula']); ?>', '<?php echo addslashes($alumno['grupo']); ?>', <?php echo $alumno['carrera_id']; ?>, '<?php echo $alumno['nivel']; ?>')">Modificar</button>
                        <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este alumno?');">
                        <input type="hidden" name="id" value="<?php echo $alumno['id']; ?>">
                        <input type="hidden" name="tipo_usuario" value="alumnos">
                        <button type="submit" name="delete_user">Eliminar</button>
                        </form>
                        <button onclick="togglePasswordVisibility('password-alumno-<?php echo $alumno['id']; ?>')">Mostrar Contraseña</button>
                        <span id="password-alumno-<?php echo $alumno['id']; ?>" style="display:none;"> <?php echo htmlspecialchars($alumno['password']); ?> </span>
                        </span>
                        </p>
                        <?php endif; ?>
                        <?php endforeach; ?>

                        <h5>Doctorado</h5>
                        <?php foreach ($alumnos as $alumno): ?>
                        <?php if ($alumno['nivel'] === 'Doctorado'): ?>
                        <p>
                        <?php echo $alumno['nombre']; ?> (Matrícula: <?php echo $alumno['matricula']; ?>, Grupo: <?php echo $alumno['grupo']; ?>, Carrera: <?php echo $alumno['carrera_nombre']; ?>)
                        <span>
                        <button onclick="showModifyForm('alumnos', <?php echo $alumno['id']; ?>, '<?php echo addslashes($alumno['nombre']); ?>', '<?php echo addslashes($alumno['matricula']); ?>', '<?php echo addslashes($alumno['grupo']); ?>', <?php echo $alumno['carrera_id']; ?>, '<?php echo $alumno['nivel']; ?>')">Modificar</button>
                        <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este alumno?');">
                        <input type="hidden" name="id" value="<?php echo $alumno['id']; ?>">
                        <input type="hidden" name="tipo_usuario" value="alumnos">
                        <button type="submit" name="delete_user">Eliminar</button>
                        </form>
                        <button onclick="togglePasswordVisibility('password-alumno-<?php echo $alumno['id']; ?>')">Mostrar Contraseña</button>
                        <span id="password-alumno-<?php echo $alumno['id']; ?>" style="display:none;"> <?php echo htmlspecialchars($alumno['password']); ?> </span>
                        </span>
                        </p>
                        <?php endif; ?>
                        <?php endforeach; ?>

                    <?php elseif ($tipo == 'docentes' && !empty($docentes)): ?>
                        <h4>Docentes</h4>
                        <?php foreach ($docentes as $docente): ?>
                        <p>
                        <?php echo $docente['nombre']; ?> (Matrícula: <?php echo $docente['matricula']; ?>, Materia: <?php echo $docente['materia']; ?>)
                        <span>
                        <button onclick="showModifyForm('docentes', <?php echo $docente['id']; ?>, '<?php echo addslashes($docente['nombre']); ?>', '<?php echo addslashes($docente['matricula']); ?>', '<?php echo addslashes($docente['materia']); ?>')">Modificar</button>
                        <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este docente?');">
                        <input type="hidden" name="id" value="<?php echo $docente['id']; ?>">
                        <input type="hidden" name="tipo_usuario" value="docentes">
                        <button type="submit" name="delete_user">Eliminar</button>
                        </form>
                        <button onclick="togglePasswordVisibility('password-docente-<?php echo $docente['id']; ?>')">Mostrar Contraseña</button>
                        <span id="password-docente-<?php echo $docente['id']; ?>" style="display:none;"> <?php echo htmlspecialchars($docente['password']); ?> </span>
                        </span>
                        </p>
                        <?php endforeach; ?>

                    <?php elseif ($tipo == 'admins' && !empty($admins)): ?>
                        <h4>Administradores</h4>
                        <?php foreach ($admins as $admin): ?>
                        <p>
                        <?php echo $admin['nombre']; ?> (Matrícula: <?php echo $admin['matricula']; ?>)
                        <span>
                        <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este administrador?');">
                        <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                        <input type="hidden" name="tipo_usuario" value="admins">
                        <button type="submit" name="delete_user">Eliminar</button>
                        </form>
                        </span>
                        </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay registros disponibles.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Muestra/Esconde la contraseña
        function togglePasswordVisibility(passwordElementId) {
            var passwordElement = document.getElementById(passwordElementId);
            if (passwordElement.style.display === "none") {
                passwordElement.style.display = "inline";  // Mostrar la contraseña
            } else {
                passwordElement.style.display = "none";    // Ocultar la contraseña
            }
        }

        // Evento para mostrar el formulario de agregar alumno
        document.getElementById('boton-agregar-alumno').onclick = function() {
            var formularioAlumno = document.getElementById('formulario-agregar-alumno');
            var formularioDocente = document.getElementById('formulario-agregar-docente');
            var formularioAdmin = document.getElementById('formulario-agregar-admin');

            // Mostrar el formulario de alumno y ocultar los demás
            formularioAlumno.classList.toggle('active');
            formularioDocente.classList.remove('active');
            formularioAdmin.classList.remove('active');
        };

        // Evento para mostrar el formulario de agregar docente
        document.getElementById('boton-agregar-docente').onclick = function() {
            var formularioDocente = document.getElementById('formulario-agregar-docente');
            var formularioAlumno = document.getElementById('formulario-agregar-alumno');
            var formularioAdmin = document.getElementById('formulario-agregar-admin');

            // Mostrar el formulario de docente y ocultar los demás
            formularioDocente.classList.toggle('active');
            formularioAlumno.classList.remove('active');
            formularioAdmin.classList.remove('active');
        };

        // Evento para mostrar el formulario de agregar admin
        document.getElementById('boton-agregar-admin').onclick = function() {
            var formularioAdmin = document.getElementById('formulario-agregar-admin');
            var formularioAlumno = document.getElementById('formulario-agregar-alumno');
            var formularioDocente = document.getElementById('formulario-agregar-docente');

            // Mostrar el formulario de admin y ocultar los demás
            formularioAdmin.classList.toggle('active');
            formularioAlumno.classList.remove('active');
            formularioDocente.classList.remove('active');
        };

        function showModifyForm(userType, id, nombre, matricula, grupo = '', carreraId = '', nivel = '', materia = '') {
            console.log("Modificar: ", userType, id, nombre, matricula, grupo, carreraId, nivel, materia);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const dropdownMenu = document.getElementById('dropdown-menu');

            menuToggle.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show'); // Alternar clase 'show' para mostrar u ocultar el menú
            });

            dropdownMenu.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    </script>
</body>
</html>

<?php
$conn->close(); // Cerrar conexión
?>