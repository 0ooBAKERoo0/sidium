<?php
session_start(); // Inicia la sesión

// Comprueba si el usuario ha iniciado sesión
if (isset($_SESSION['usuario'])) {
    // Elimina todas las variables de sesión
    $_SESSION = array();
    
    // Destruye la sesión
    session_destroy();
    
    // Redirige a la página de inicio o a la página de login
    header("Location: /vscode/SIDIUM/login.php"); // Cambia 'index.php' a la página que desees
    exit();
} else {
    // Si no hay sesión activa, redirige a otra página
    header("Location: /vscode/SIDIUM/login.php"); // Cambia 'index.php' si es necesario
    exit();
}
?>