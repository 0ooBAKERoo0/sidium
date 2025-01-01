<?php
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

// Manejo del formulario de agregar alumnos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_alumno'])) {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];
    $grupo = $_POST['grupo'];
    $carrera_id = $_POST['carrera_id'];
    $nivel = $_POST['nivel'];
    $password = $_POST['password']; // Guardamos la contraseña tal como se escribió
    
    // Inserción en la base de datos
    $sql = "INSERT INTO alumnos (matricula, nombre, grupo, carrera_id, nivel, password) 
            VALUES ('$matricula', '$nombre', '$grupo', $carrera_id, '$nivel', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Nuevo alumno agregado correctamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Manejo del formulario de agregar docentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_docente'])) {
    // Recoger datos del formulario
    $nombre_docente = $_POST['nombre_docente'];
    $matricula_docente = $_POST['matricula_docente'];
    $materia = $_POST['materia'];
    $password_docente = $_POST['password_docente']; // Guardamos la contraseña tal como se escribió

    // Inserción en la base de datos
    $sql = "INSERT INTO docentes (matricula, nombre, materia, password) 
            VALUES ('$matricula_docente', '$nombre_docente', '$materia', '$password_docente')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Nuevo docente agregado correctamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Obtener usuarios
$alumnos = [];
$docentes = [];

if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
    if ($tipo == 'alumnos') {
        $sql = "SELECT alumnos.*, carrera.nombre AS carrera_nombre 
                FROM alumnos 
                JOIN carrera ON alumnos.carrera_id = carrera.id"; // Unir las tablas
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $alumnos[] = $row;
            }
        }
    } elseif ($tipo == 'docentes') {
        $sql = "SELECT * FROM docentes"; // Cambia 'docentes' por el nombre de tu tabla
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $docentes[] = $row;
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

// Manejo de modificar y eliminar alumnos o docentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify_user'])) {
    // Manejo de modificación
    $id = $_POST['id'];
    $tipo_usuario = $_POST['tipo_usuario'];

    if ($tipo_usuario == 'alumnos') {
        $nombre_mod = $_POST['nombre_mod'];
        $matricula_mod = $_POST['matricula_mod'];
        $grupo_mod = $_POST['grupo_mod'];
        $carrera_id_mod = $_POST['carrera_id_mod'];
        $nivel_mod = $_POST['nivel_mod'];

        // Actualiza el alumno
        $sql = "UPDATE alumnos SET nombre='$nombre_mod', matricula='$matricula_mod', grupo='$grupo_mod', carrera_id=$carrera_id_mod, nivel='$nivel_mod' WHERE id=$id";        
    } elseif ($tipo_usuario == 'docentes') {
        $nombre_mod = $_POST['nombre_mod'];
        $matricula_mod = $_POST['matricula_mod'];
        $materia_mod = $_POST['materia_mod'];
        
        // Actualiza el docente
        $sql = "UPDATE docentes SET nombre='$nombre_mod', matricula='$matricula_mod', materia='$materia_mod' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Usuario modificado correctamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Manejo de eliminación
    $id = $_POST['id'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    if ($tipo_usuario == 'alumnos') {
        // Elimina el alumno
        $sql = "DELETE FROM alumnos WHERE id=$id";
    } elseif ($tipo_usuario == 'docentes') {
        // Elimina el docente
        $sql = "DELETE FROM docentes WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Gestión Académica</title>

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
padding: 10px;
text-align: center;
flex-wrap: wrap;
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

.container {
display: flex;
flex-direction: column;
align-items: center;
padding: 20px;
margin: 0 auto;
width: 100%;
max-width: 1200px; /* Max width for larger screens */
}

.main-content {
background: white;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
width: 100%; /* Asegurarse de que el contenido ocupe el 100% del ancho disponible */
}

h3 {
margin-bottom: 20px;
color: #4CAF50;
}

.user-selection {
display: flex;
flex-direction: column;
gap: 15px;
margin: 20px 0;
}

.user-selection button {
padding: 10px;
background-color: #007bff;
color: white;
border: none;
border-radius: 5px;
cursor: pointer;
transition: background-color 0.3s, transform 0.2s;
width: 100%; /* Botones ocupan todo el ancho */
}

.user-selection button:hover {
background-color: #0056b3;
transform: scale(1.03);
}

.user-list {
margin-top: 20px;
display: flex;
flex-direction: column;
gap: 10px; /* Espacio entre elementos de la lista */
}

.user-list p {
display: flex;
justify-content: space-between;
align-items: center;
padding: 10px;
background-color: #f9f9f9;
border-radius: 5px;
border: 1px solid #ddd; /* Borde para distinguir cada usuario */
}

footer {
text-align: center;
padding: 15px;
background-color: #4CAF50;
color: white;
position: relative;
width: 100%;
bottom: 0;
left: 0;
right: 0; /* Asegurarse de que el pie de página ocupe el ancho completo */
margin-top: 20px; /* Espacio por encima del pie de página */
}

footer p {
margin: 0;
}

@media (min-width: 768px) {
.user-selection {
flex-direction: row;
gap: 20px;
}

.user-selection button {
flex: 1; /* Hacer que los botones se distribuyan en fila */
}
}

/* Estilos CSS básicos */
body { font-family: Arial, sans-serif; }
.form-container { display: none; margin: 20px 0; } /* Ocultar formularios por defecto */
.form-container.active { display: block; } /* Mostrar formulario activo */

    </style>
</head>
<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Administrador</h2>
    </header>

    <nav>
        <a href="administrador.php">Inicio</a>
        <a href="modificacion_calif.php">Modificar Calificaciones</a>
        <a href="gestion_usuarios.php">Gestionar Usuarios</a>
        <a href="gestion_asignaturas.php">Agregar Nueva Asignatura</a>
    </nav>

    <div class="container">
        <main class="main-content">
            <h3>Gestión de Usuarios</h3>
            <div class="user-selection">
                <button id="boton-agregar-alumno">Agregar Alumno</button>
                <button id="boton-agregar-docente">Agregar Docente</button>
                <button onclick="window.location.href='?tipo=alumnos'">Ver Alumnos</button>
                <button onclick="window.location.href='?tipo=docentes'">Ver Docentes</button>
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
                    <input type="text" name="password" required><br> <!-- Cambia a tipo "text" para mostrar el texto de la contraseña -->

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
                    <input type="text" name="materia" required><br>

                    <label for="password_docente">Contraseña:</label>
                    <input type="text" name="password_docente" required><br> <!-- Cambia a tipo "text" para mostrar el texto de la contraseña -->

                    <button type="submit" name="agregar_docente">Agregar Docente</button>
                </form>
            </div>
            
            <div class="user-list" id="user-list">
                <?php if (isset($_GET['tipo'])): ?>
                    <?php if ($tipo == 'alumnos' && !empty($alumnos)): ?>
                        <h4>Alumnos</h4>
                        <?php foreach ($alumnos as $alumno): ?>
                            <p>
                                <?php echo $alumno['nombre']; ?> (Grupo: <?php echo $alumno['grupo']; ?>, Carrera: <?php echo $alumno['carrera_nombre']; ?>)
                                <span>
                                    <button onclick="showModifyForm('alumnos', <?php echo $alumno['id']; ?>, '<?php echo addslashes($alumno['nombre']); ?>', '<?php echo addslashes($alumno['matricula']); ?>', '<?php echo addslashes($alumno['grupo']); ?>', <?php echo $alumno['carrera_id']; ?>, '<?php echo $alumno['nivel']; ?>')">Modificar</button>
                                    <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este alumno?');">
                                        <input type="hidden" name="id" value="<?php echo $alumno['id']; ?>">
                                        <input type="hidden" name="tipo_usuario" value="alumnos">
                                        <button type="submit" name="delete_user">Eliminar</button>
                                    </form>
                                    <button onclick="togglePasswordVisibility('password-alumno-<?php echo $alumno['id']; ?>')">Mostrar Contraseña</button>
                                    <span id="password-alumno-<?php echo $alumno['id']; ?>" style="display:none;"> <?php echo htmlspecialchars($alumno['password']); ?> </span> <!-- Utiliza el campo de contraseña de la base de datos -->
                                </span>
                            </p>
                        <?php endforeach; ?>
                    <?php elseif ($tipo == 'docentes' && !empty($docentes)): ?>
                        <h4>Docentes</h4>
                        <?php foreach ($docentes as $docente): ?>
                            <p>
                                <?php echo $docente['nombre']; ?> (Materia: <?php echo $docente['materia']; ?>)
                                <span>
                                    <button onclick="showModifyForm('docentes', <?php echo $docente['id']; ?>, '<?php echo addslashes($docente['nombre']); ?>', '<?php echo addslashes($docente['matricula']); ?>', '<?php echo addslashes($docente['materia']); ?>')">Modificar</button>
                                    <form style="display:inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este docente?');">
                                        <input type="hidden" name="id" value="<?php echo $docente['id']; ?>">
                                        <input type="hidden" name="tipo_usuario" value="docentes">
                                        <button type="submit" name="delete_user">Eliminar</button>
                                    </form>
                                    <button onclick="togglePasswordVisibility('password-docente-<?php echo $docente['id']; ?>')">Mostrar Contraseña</button>
                                    <span id="password-docente-<?php echo $docente['id']; ?>" style="display:none;"> <?php echo htmlspecialchars($docente['password']); ?> </span> <!-- Utiliza el campo de contraseña de la base de datos -->
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
            
            // Mostrar el formulario de alumno y ocultar el de docente
            formularioAlumno.classList.toggle('active');
            formularioDocente.classList.remove('active');
        };

        // Evento para mostrar el formulario de agregar docente
        document.getElementById('boton-agregar-docente').onclick = function() {
            var formularioDocente = document.getElementById('formulario-agregar-docente');
            var formularioAlumno = document.getElementById('formulario-agregar-alumno');
            
            // Mostrar el formulario de docente y ocultar el de alumno
            formularioDocente.classList.toggle('active');
            formularioAlumno.classList.remove('active');
        };

        function showModifyForm(userType, id, nombre, matricula, grupo = '', carreraId = '', nivel = '', materia = '') {
            // Lógica para mostrar el formulario de modificación si hace falta
        // Ejemplo básico de implementación
        console.log("Modificar: ", userType, id, nombre, matricula, grupo, carreraId, nivel, materia);
        // Aquí deberías implementar la lógica para llenar el formulario con los datos del usuario seleccionado
        }
    </script>
</body>
</html>

<?php
$conn->close(); // Cerrar conexión
?>



