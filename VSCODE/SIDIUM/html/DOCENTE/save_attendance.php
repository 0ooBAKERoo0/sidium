<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambiar según necesario
$password = ""; // Cambiar si se ha establecido contraseña
$dbname = "sidium"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Guardar asistencia
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = date('Y-m-d');
    
    // Iniciar una transacción
    $conn->begin_transaction();
    try {
        if (!empty($_POST['asistencia'])) {
            foreach ($_POST['asistencia'] as $matricula => $estado) {
                $stmt = $conn->prepare("INSERT INTO asistencia (matricula, fecha, estado) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $matricula, $fecha, $estado);
                $stmt->execute();
            }
            $stmt->close();

            // Commit de la transacción
            $conn->commit();
            echo "<span style='color: green;'>Asistencia guardada exitosamente.</span>";
        } else {
            echo "<span style='color: orange;'>No se seleccionó ninguna asistencia.</span>";
        }
    } catch (Exception $e) {
        // En caso de error, hacer rollback
        $conn->rollback();
        echo "<span style='color: red;'>Error al guardar la asistencia: {$e->getMessage()}</span>";
    }
}

$conn->close();
?>