<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="css/bootstrap.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <title>Inicio de sesión</title>
   <style>
      * {
         padding: 0;
         margin: 0;
         box-sizing: border-box;
      }
      body {
         font-family: 'Poppins', sans-serif;
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         background-image: url('img/umb3.jpg'); /* Asegúrate de que esta ruta sea correcta */
         background-size: cover; /* Para que la imagen cubra toda la pantalla */
         background-position: center; /* Centrar la imagen en el fondo */
         color: white; /* Cambia el color del texto a blanco */
      }
      .container {
         display: grid;
         grid-template-columns: 1fr;
         padding: 2rem;
         background: rgba(0, 0, 0, 0.5); /* Fondo oscuro y semi-transparente para el formulario */
         border-radius: 8px; /* Bordes redondeados */
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      }
      .login-content {
         text-align: center;
      }
      form {
         width: 360px;
      }
      .input-div {
         position: relative;
         display: grid;
         grid-template-columns: 7% 93%;
         margin: 25px 0;
         padding: 5px 0;
         border-bottom: 2px solid #d9d9d9;
      }
      .input-div > div > h5 {
         color: #999;
         font-size: 18px;
         transition: .3s;
      }
      .input-div > div > input {
         border: none;
         outline: none;
         background: none;
         padding: 0.5rem 0.7rem;
         font-size: 1.2rem;
         color: #fff; /* Color de texto blanco */
         font-family: 'Poppins', sans-serif;
      }
      .input-div.pass {
         margin-bottom: 4px;
      }
      .verPassword {
         font-size: 20px;
         cursor: pointer;
         margin-left: -30px; /* Ajuste para una mejor posición */
         position: absolute;
         top: 50%;
         transform: translateY(-50%);
         right: 10px; /* Coloca el ojo a la derecha */
         color: #fff; /* Color del icono blanco */
      }
      .btn {
         display: block;
         width: 100%;
         height: 50px;
         border-radius: 25px;
         outline: none;
         border: none;
         background: #04a1fc;
         font-size: 1.2rem;
         color: #fff;
         margin: 1rem 0;
         cursor: pointer;
         transition: .5s;
      }
      .btn:hover {
         background: #142e3d;
      }
   </style>
</head>
<body>
   <div class="container">
      <div class="login-content">
         <form method="post" action="">
            <h2 class="title">BIENVENIDO</h2>
            <?php
            include("modelo/conexion.php");
            session_start();

            // Manejo de errores al conectar con la base de datos
            if (!$conexion) {
                die("Error de conexión: " . mysqli_connect_error());
            }

            // Generación de un token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar el token CSRF
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    die("Error de seguridad. Token CSRF inválido.");
                }

                $matricula = trim($_POST['matricula']);
                $password = trim($_POST['password']);

                // Validar que los campos no estén vacíos
                if (empty($matricula) || empty($password)) {
                    echo "<div class='alert alert-danger'>Por favor, completa todos los campos.</div>";
                    exit();
                }

                // Consulta a la base de datos
                $query = "SELECT * FROM usuarios WHERE matricula = ?";
                $stmt = $conexion->prepare($query);
                if ($stmt === false) {
                    die("Error en la preparación de la consulta: " . $conexion->error);
                }

                $stmt->bind_param("s", $matricula);
                $stmt->execute();
                $resultado = $stmt->get_result();

                // Validar los resultados de consulta
                if ($resultado->num_rows > 0) {
                    $usuarioData = $resultado->fetch_assoc();

                    // Validar la contraseña ingresada
                    if ($password === $usuarioData['password']) { // Cambiado de password_verify a comparación directa
                        $_SESSION['usuario'] = $usuarioData['matricula'];
                        $_SESSION['tipo_cuenta'] = $usuarioData['tipo_cuenta']; // Guardamos el tipo de cuenta

                        // Redirigir según el tipo de cuenta
                        if ($usuarioData['tipo_cuenta'] == 'docente') {
                            header("Location: html/DOCENTE/docentes.php");
                            exit();
                        } elseif ($usuarioData['tipo_cuenta'] == 'alumno') {
                            header("Location: html/ALUMNOS/alumnos.php");
                            exit();
                        } elseif ($usuarioData['tipo_cuenta'] == 'administrador') {
                            header("Location: html/ADMIN/modificacion_calif.php");
                            exit();
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
                }
            }
            ?>

            <div class="input-div one">
               <div class="div">
                  <h5>Matrícula</h5>
                  <input id="matricula" type="text" class="input" name="matricula" required>
               </div>
            </div>

            <div class="input-div pass">
               <div class="div">
                  <h5>Contraseña</h5>
                  <input type="password" id="password" class="input" name="password" required>
                  <span class="verPassword" onclick="togglePassword()">👁️</span>
               </div>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input name="btningresar" class="btn" type="submit" value="INICIAR SESION">
         </form>
      </div>
   </div>
   <script>
      function togglePassword() {
          const passwordInput = document.getElementById("password");
          const passwordType = passwordInput.getAttribute("type") === "password" ? "text" : "password";
          passwordInput.setAttribute("type", passwordType);
      }
   </script>
</body>
</html>