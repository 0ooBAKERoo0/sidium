<?php
$servername = "localhost";
$username = "root"; // Cambia esto si es necesario
$password = ""; // Cambia esto si es necesario
$dbname = "sidium"; // Cambia esto por el nombre correcto

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificación de la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado un archivo
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK && isset($_POST['matricula'])) {
    $file = $_FILES['file']['tmp_name'];
    $foto = addslashes(file_get_contents($file));
    $matricula = intval($_POST['matricula']);

    // Consulta para actualizar la foto
    $sql = "UPDATE materias_alumno SET foto='$foto' WHERE matricula='$matricula'";
    
    if ($conn->query($sql) === TRUE) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => $conn->error];
    }
} else {
    $response = ['success' => false, 'error' => 'No se recibió ningún archivo válido'];
}

header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión
$conn->close();
?>