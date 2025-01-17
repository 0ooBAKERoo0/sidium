<?php

include("verifica_sesion.php");

// Evitar que el usuario regrese a la página después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Establecer parámetros de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sidium";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar variables
$matricula = $_SESSION['usuario'];
$docente = null;
$mensaje = '';

// Realizar la consulta para obtener datos del docente incluyendo la imagen
$sql = "SELECT nombre, imagen_url, materia FROM docentes WHERE matricula = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $docente = $result->fetch_assoc();
    if (!empty($docente['imagen_url'])) {
        $_SESSION['fotografia'] = base64_encode(file_get_contents($docente['imagen_url']));
    }
}
$stmt->close();

// Manejar la subida de la imagen mediante AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['fotografia'])) {
    $response = array('success' => false, 'message' => '');
    
    if ($_FILES['fotografia']['error'] == UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['fotografia']['tmp_name']);
        
        // Validar que sea una imagen
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['fotografia']['tmp_name']);
        finfo_close($finfo);

        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            $update_sql = "UPDATE docentes SET imagen_url = ? WHERE matricula = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("bs", $_FILES['fotografia']['tmp_name'], $matricula);

            if ($update_stmt->execute()) {
                $_SESSION['fotografia'] = base64_encode($foto);
                $response['success'] = true;
                $response['message'] = 'Imagen actualizada exitosamente.';
                $response['newImage'] = 'data:image/jpeg;base64,' . base64_encode($foto);
            } else {
                $response['message'] = 'Error al actualizar la imagen: ' . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $response['message'] = 'El archivo subido no es una imagen válida.';
        }
    } else {
        $response['message'] = 'Error al subir la imagen. Código de error: ' . $_FILES['fotografia']['error'];
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz Docente - Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
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

        .navbar {
            display: flex;
            flex-direction: column; /* Cambiar a columna para el menú móvil */
            align-items: center; /* Centrar los enlaces verticalmente */
            background-color: #333;
            padding: 10px;
        }

        .nav-links {
            display: flex;
            justify-content: center; /* Centrar los enlaces */
            width: 100%; /* Permitimos que tome toda la anchura */
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

        .menu-toggle {
            display: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            margin-bottom: 10px; /* Espacio debajo del botón */
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0 auto;
            width: 100%;
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

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                text-align: center; /* Centrar texto para el menú toggle */
            }

            .menu-toggle {
                display: block;
            }

            .nav-links.active {
                display: flex;
            }

            nav a {
                padding: 15px;
            }
        }

        .profile-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        #profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .upload-section {
            margin: 10px 0;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
    <script>
        // Prevenir navegación hacia atrás
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });

        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</head>

<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Docente</h2>
    </header>

    <div class="navbar">
        <div class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="nav-links">
            <nav>
                <a href="docentes.php">Inicio</a>
                <a href="asistencias.php">Asistencia</a>
                <a href="calificaciones.php">Calificaciones</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <main class="main-content">
            <h3>Interfaz Docente</h3>

            <div class="profile-section">
                <img id="profile-image" 
                     src="<?php echo isset($_SESSION['fotografia']) ? 'data:image/jpeg;base64,' . $_SESSION['fotografia'] : 'default-avatar.jpg'; ?>" 
                     alt="Fotografía del Docente" />
                <div>
                    <h4><?php echo !empty($docente['nombre']) ? htmlspecialchars($docente['nombre']) : 'Nombre no disponible'; ?></h4>
                    <p>Matrícula: <?php echo htmlspecialchars($matricula); ?></p>
                </div>
            </div>

            <form id="upload-form" method="POST" enctype="multipart/form-data">
                <div class="upload-section">
                    <input type="file" name="fotografia" accept="image/*" required onchange="previewImage(event)">
                    <button type="submit">Actualizar Fotografía</button>
                </div>
            </form>
            <div id="mensaje-estado" style="margin-top: 10px; color: green;"></div>

            <h4>Materia Asociada</h4>
            <table>
                <thead>
                    <tr>
                        <th>Materia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($docente['materia']); ?></td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const profileImage = document.getElementById('profile-image');
                profileImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Manejar el envío del formulario con AJAX
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const mensajeEstado = document.getElementById('mensaje-estado');
                mensajeEstado.textContent = data.message;
                mensajeEstado.style.color = data.success ? 'green' : 'red';
                
                if (data.success && data.newImage) {
                    document.getElementById('profile-image').src = data.newImage;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('mensaje-estado').textContent = 'Error al actualizar la fotografía';
                document.getElementById('mensaje-estado').style.color = 'red';
            });
        });
    </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>