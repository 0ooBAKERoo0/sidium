<?php
session_start();
include("modelo/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: /SIDIUM/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fotografia'])) {
    $matricula = $_SESSION['usuario'];
    $fotografia = file_get_contents($_FILES['fotografia']['tmp_name']);

    $query = "UPDATE docentes SET fotografia = ? WHERE matricula = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("bs", $fotografia, $matricula);
    
    if ($stmt->execute()) {
        echo "Fotografía actualizada correctamente.";
    } else {
        echo "Error al actualizar la fotografía: " . $conexion->error;
    }

    $stmt->close();
}
?>