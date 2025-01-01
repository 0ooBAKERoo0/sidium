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

// Aquí debes manejar la lógica para obtener la matrícula del alumno logueado
$matricula = 123456; // Cambia esto a la matrícula real del alumno logueado

// Obtener el horario y información del alumno
$schedule = array();
$alumno_info = null;
$sql = "SELECT docente, asignatura, clave_asignatura, horario, foto FROM alumnos WHERE matricula='$matricula'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
        // Guardar información del alumno (asumiendo que solo hay uno)
        $alumno_info = $row; // Por simplicidad, usamos la misma fila para obtener datos
    }
} else {
    echo "No hay datos disponibles";
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica - Alumno</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style1.css">
</head>

<body>
    <header>
        <h1>Sistema de Gestión Académica</h1>
        <h2>Bienvenido, Alumno</h2>
    </header>

    <nav>
        <a href="#">Inicio</a>
        <a href="#">Asistencia</a>
        <a href="#">Calificaciones</a>
        <a href="#">Calendario Escolar</a>
    </nav>

    <div class="container">
        <main class="main-content">
            <h3>Tu Fotografía</h3>
            <?php if (!empty($schedule) && isset($schedule[0]['foto'])): ?>
                <img id="photo-preview" src="data:image/jpeg;base64,<?= base64_encode($schedule[0]['foto']) ?>" alt="Tu Fotografía" />
            <?php else: ?>
                <img id="photo-preview" src="" alt="Tu Fotografía" style="display: none;" />
            <?php endif; ?>
            <div class="upload-area">
                <label for="file-upload">Elegir Fotografía</label>
                <input type="file" id="file-upload" accept="image/*" onchange="previewPhoto(event)">
                <button id="upload-photo" onclick="uploadPhoto()">Subir Foto</button>
            </div>
            <h3>Nombre del Alumno: <?= htmlspecialchars($alumno_info['asignatura']) ?></h3>
            <h3>Matrícula: <?= htmlspecialchars($matricula) ?></h3>

            <h3>Tu Horario de Clases</h3>
            <table>
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Asignatura</th>
                        <th>Clave de Asignatura</th>
                        <th>Horario</th>
                    </tr>
                </thead>
                <tbody id="schedule-list">
                    <?php foreach ($schedule as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['docente']) ?></td>
                            <td><?= htmlspecialchars($item['asignatura']) ?></td>
                            <td><?= htmlspecialchars($item['clave_asignatura']) ?></td>
                            <td><?= htmlspecialchars($item['horario']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        let selectedFile;

        function previewPhoto(event) {
            selectedFile = event.target.files[0];
            if (selectedFile) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const photoPreview = document.getElementById('photo-preview');
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block'; // Mostrar la imagen una vez que se ha cargado
                };
                reader.readAsDataURL(selectedFile);
            }
        }

        async function uploadPhoto() {
            if (!selectedFile) {
                alert('Por favor, elige una fotografía antes de subir.');
                return;
            }

            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('matricula', <?= $matricula ?>); // Incluir la matrícula en el envío

            const response = await fetch('subir_foto.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                alert('Foto subida correctamente.');
                location.reload(); // Recargar para mostrar la nueva foto
            } else {
                alert('Error al subir la foto: ' + result.error);
            }
        }
    </script>

</body>

</html>