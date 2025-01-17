<?php
session_start(); // Inicia la sesión

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si no existe, redirige al formulario de inicio de sesión
    header("Location: /vscode/sidium/login.php"); // Cambia "login.php" por la ruta de tu archivo de inicio de sesión
    exit();
}

// Aquí puedes agregar lógica adicional dependiendo de si el usuario es docente, alumno o administrador
// Ejemplo: verifica el tipo de cuenta
$userType = $_SESSION['tipo_cuenta'] ?? ''; // Si tienes un tipo de cuenta guardado en la sesión
if ($userType == 'docente') {
    // Cargar datos específicos o permitir acceso a funcionalidades para docentes
} elseif ($userType == 'alumno') {
    // Cargar datos específicos o permitir acceso a funcionalidades para alumnos
} elseif ($userType == 'administrador') {
    // Cargar datos específicos o permitir acceso a funcionalidades para administradores
}
?>