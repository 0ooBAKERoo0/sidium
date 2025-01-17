<?php

include("verifica_sesion.php");

// Conexión a la base de datos
$servername = "localhost"; // Host de la base de datos
$username = "root"; // Cambia si tienes otro usuario
$password = ""; // Cambia si tienes una contraseña
$dbname = "sidium"; // Cambia por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Insertar asignatura
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_subject') {
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];

    // Inserción de datos
    $sql = "INSERT INTO asignaturas (nombre, matricula) VALUES ('$nombre', '$matricula')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Asignatura agregada con éxito');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Eliminar asignatura
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_subject') {
    $id = $_POST['id'];

    // Eliminación de datos
    $sql = "DELETE FROM asignaturas WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Asignatura eliminada con éxito');</script>";
    } else {
        echo "<script>alert('Error al eliminar: " . $conn->error . "');</script>";
    }
}

// Insertar carrera
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_career') {
    $nombre = $_POST['nombre_carrera'];

    // Inserción de datos
    $sql = "INSERT INTO carrera (nombre) VALUES ('$nombre')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Carrera agregada con éxito');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Eliminar carrera
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_career') {
    $id = $_POST['id'];

    // Eliminación de datos
    $sql = "DELETE FROM carrera WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Carrera eliminada con éxito');</script>";
    } else {
        echo "<script>alert('Error al eliminar: " . $conn->error . "');</script>";
    }
}

// Obtener lista de asignaturas
$sql_subjects = "SELECT * FROM asignaturas";
$result_subjects = $conn->query($sql_subjects);

// Obtener lista de carreras
$sql_careers = "SELECT * FROM carrera";
$result_careers = $conn->query($sql_careers);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaturas y Carreras - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style4.css">
</head>
<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Administrador</h2>
    </header>

    <nav>
        <div class="desktop-menu">
            <a href="modificacion_calif.php">Modificar Calificaciones</a>
            <a href="gestion_usuarios.php">Gestionar Usuarios</a>
            <a href="gestion_asignaturas.php">Agregar Nueva Asignatura</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
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
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Agregar Nueva Asignatura</h2>
            <form action="" method="post">
                <input type="text" name="nombre" placeholder="Título de la Asignatura" required />
                <input type="text" name="matricula" placeholder="Matrícula" required />
                <input type="hidden" name="action" value="add_subject">
                <button type="submit">Agregar Asignatura</button>
            </form>
        </div>

        <div class="list-container">
            <h2>Lista de Asignaturas</h2>
            <div id="assignment-list" class="assignment-list">
                <?php
                if ($result_subjects->num_rows > 0) {
                    while ($row = $result_subjects->fetch_assoc()) {
                        echo '<div class="assignment-item">';
                        echo "<p>" . $row['nombre'] . " - Matrícula: " . $row['matricula'] . "</p>";
                        echo '<form action="" method="post" style="display: inline;">';
                        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                        echo '<input type="hidden" name="action" value="delete_subject">';
                        echo '<button type="submit">Eliminar</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "No hay asignaturas.";
                }
                ?>
            </div>
        </div>

        <div class="form-container">
            <h2>Agregar Nueva Carrera</h2>
            <form action="" method="post">
                <input type="text" name="nombre_carrera" placeholder="Nombre de la Carrera" required />
                <input type="hidden" name="action" value="add_career">
                <button type="submit">Agregar Carrera</button>
            </form>
        </div>

        <div class="list-container">
            <h2>Lista de Carreras</h2>
            <div id="career-list" class="career-list">
                <?php
                if ($result_careers->num_rows > 0) {
                    while ($row = $result_careers->fetch_assoc()) {
                        echo '<div class="career-item">';
                        echo "<p>" . $row['nombre'] . "</p>";
                        echo '<form action="" method="post" style="display: inline;">';
                        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                        echo '<input type="hidden" name="action" value="delete_career">';
                        echo '<button type="submit">Eliminar</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "No hay carreras.";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const dropdownMenu = document.getElementById('dropdown-menu');

            menuToggle.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show'); // Alternar la clase 'show'
            });

            // Cerrar el menú al hacer clic en un enlace
            dropdownMenu.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    </script>
</body>
</html>

<?php
$conn->close(); // Cerrar conexión al final
?>