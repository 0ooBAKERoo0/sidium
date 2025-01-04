<?php
// Conexión a la base de datos
$host = 'localhost'; // o el nombre de tu host
$db   = 'sidium';
$user = 'root'; // usuario predeterminado de XAMPP
$pass = ''; // contraseña predeterminada de XAMPP (debería estar vacía)

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$alumno = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    
    // Buscar alumno por matrícula
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
    $stmt->execute([$matricula]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Calificaciones - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style2.css">
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
            <a href="administrador.php" onclick="redirectTo('administrador.php');">Inicio</a>
            <a href="modificacion_calif.php" onclick="redirectTo('modificar_calificaciones.php');">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php" onclick="redirectTo('gestionar_usuarios.php');">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php" onclick="redirectTo('agregar_asignatura.php');">Agregar Nueva Asignatura</a>
        </div>
        <div class="desktop-menu">
            <a href="administrador.php" onclick="redirectTo('administrador.php');">Inicio</a>
            <a href="modificacion_calif.php" onclick="redirectTo('modificar_calificaciones.php');">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php" onclick="redirectTo('gestionar_usuarios.php');">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php" onclick="redirectTo('agregar_asignatura.php');">Agregar Nueva Asignatura</a>
        </div>
    </nav>

    <div class="container">
        <main class="main-content">
            <div class="notifications">
                <h3>Notificaciones</h3>
                <p>No hay nuevas notificaciones.</p>
            </div>

            <h3>Modificar Calificaciones</h3>

            <form method="POST" class="dropdown">
                <label for="matricula">Buscar Alumno por Matrícula:</label>
                <input type="text" id="matricula" name="matricula" placeholder="Ingrese matrícula" required />
                <button type="submit">Buscar Alumno</button>
            </form>

            <?php if ($alumno): ?>
            <div id="alumno-info">
                <div id="nombre-alumno">Nombre: <?= htmlspecialchars($alumno['nombre']) ?></div>
                <div id="grupo-alumno">Grupo: <?= htmlspecialchars($alumno['grupo']) ?></div>

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

                <button onclick="alert('Calificación modificada.');">Modificar Calificación</button>
            </div>
            <?php else: ?>
            <div id="alumno-info">
                <p id="nombre-alumno">Nombre: No encontrado</p>
                <p id="grupo-alumno">Grupo: -</p>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Función para redirigir al hacer clic en un enlace
        function redirectTo(url) {
            window.location.href = url;
        }

        // Menú hamburguesa
        const menuToggle = document.getElementById('menu-toggle');
        const dropdownMenu = document.getElementById('dropdown-menu');

        menuToggle.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });

        // Cerrar el menú al hacer clic en un enlace
        dropdownMenu.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
    </script>

</body>
</html>