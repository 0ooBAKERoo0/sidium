<?php
$servername = "localhost";
$username = "root"; // Cambia este valor si es necesario
$password = ""; // Cambia este valor si es necesario
$dbname = "sidium"; // Reemplaza por el nombre de tu base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificación de la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Aquí debes manejar la lógica para obtener la matrícula del alumno logueado
$matricula = 'ALUMNO_MATRICULA'; // Cambia esto a la matrícula del alumno logueado

$sql = "SELECT docente, asignatura, clave_asignatura, horario FROM materias_alumno WHERE matricula='$matricula'";
$result = $conn->query($sql);

$schedule = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($schedule);

$conn->close();
?>