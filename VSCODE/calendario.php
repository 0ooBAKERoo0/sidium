<?php
// Conexión a la base de datos
$server = "localhost"; // Cambia esto si tu servidor es diferente
$username = "root"; // Cambia esto si tu usuario es diferente
$password = ""; // Cambia esto si tu contraseña es diferente
$database = "sidium"; // Nombre de la base de datos

$conn = new mysqli($server, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener el calendario
$sql = "SELECT * FROM calendario"; // Cambia 'calendario' a tu nombre de tabla
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Escolar - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos generales */
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
            max-width: 1200px;
        }
        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        h3 {
            margin-bottom: 20px;
            color: #4CAF50;
        }
        .calendar-container {
            text-align: center;
            margin-top: 20px;
        }
        .calendar-img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
        }
        .download-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .download-btn:hover {
            background-color: #45a049;
        }
        .download-container {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Docente</h2>
    </header>

    <nav>
        <a href="docentes.php">Inicio</a>
        <a href="asistencias.php">Asistencia</a>
        <a href="calificaciones.php">Calificaciones</a>
        <a href="calendario.php">Calendario Escolar</a>
        <a href="modificacion_califi_docente.php">Solicitud para Modificar Calificaciones</a>
    </nav>

    <div class="container">
        <main class="main-content">
            <h3>Calendario Escolar</h3>

            <div class="calendar-container">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php if ($row['tipo'] == 'imagen'): ?>
                            <img src="<?php echo htmlspecialchars($row['url']); ?>" alt="Calendario Escolar" class="calendar-img" />
                        <?php elseif ($row['tipo'] == 'pdf'): ?>
                            <iframe src="<?php echo htmlspecialchars($row['url']); ?>" width="600" height="400" style="border: none;"></iframe>
                        <?php endif; ?>
                        
                        <div class="download-container">
                            <a href="<?php echo htmlspecialchars($row['url']); ?>" class="download-btn" download> Descargar <?php echo $row['tipo'] == 'imagen' ? 'Imagen' : 'PDF'; ?></a>
                            <a href="<?php echo htmlspecialchars($row['url']); ?>" class="download-btn" target="_blank> Abrir <?php echo $row['tipo'] == 'imagen' ? 'Imagen' : 'PDF'; ?></a>
                        </div>
                        
                        <p class="info"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay calendarios disponibles.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php $conn->close(); ?>
</body>
</html>