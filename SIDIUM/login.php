<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <link href="https://tresplazas.com/web/img/big_punto_de_venta.png" rel="shortcut icon">
   <title>Inicio de sesión</title>
</head>

<body>
   <img class="wave" src="img/umb3.jpg">
   <div class="container">
      <div class="img">
         <!--<img src="img/umb1.jpg">  imagen de frente-->  
      </div>
      <div class="login-content">
         <form method="post" action=""> <!-- Acción vacía para enviar al mismo archivo -->
            <img src="img/avatar.svg">
            <h2 class="title">BIENVENIDO</h2>
            <?php
            include("modelo/conexion.php");

            session_start();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $usuario = trim($_POST['usuario']);
               $clave = trim($_POST['clave']);

               // Validar que los campos no estén vacíos
               if (empty($usuario) || empty($clave)) {
                   echo "<div class='alert alert-danger'>Por favor, completa todos los campos.</div>";
                   exit();
               }

               // Consulta a la base de datos
               $query = "SELECT * FROM usuarios WHERE usuario = ?";
               $stmt = $conexion->prepare($query);

               if ($stmt === false) {
                   die("Error en la preparación de la consulta: " . $conexion->error);
               }

               $stmt->bind_param("s", $usuario);
               $stmt->execute();
               $resultado = $stmt->get_result();

               if ($resultado->num_rows > 0) {
                   $usuarioData = $resultado->fetch_assoc();

                   // Aquí deberías usar password_verify() si las contraseñas están cifradas
                   if ($usuarioData['clave'] === $clave) { // Solo para propósitos de prueba
                       // Iniciar sesión y redirigir según el tipo de cuenta
                       $_SESSION['usuario'] = $usuarioData['usuario'];
                       if ($usuarioData['cuenta'] == 'maestro') {
                           header("Location: html/docentes.html");
                       } elseif ($usuarioData['cuenta'] == 'alumno') {
                           header("Location: html/alumnos.html");
                       } elseif ($usuarioData['cuenta'] == 'administrador') {
                           header("Location: html/administrador.html");
                       }
                       exit();
                   } else {
                       echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
                   }
               } else {
                   echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
               }
            }
            ?>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Usuario</h5>
                  <input id="usuario" type="text" class="input" name="usuario" required>
               </div>
            </div>
            <div class="input-div pass">
               <div class="i">
                  <i class="fas fa-lock"></i>
               </div>
               <div class="div">
                  <h5>Contraseña</h5>
                  <input type="password" id="clave" class="input" name="clave" required>
               </div>
            </div>
            <div class="view">
               <div class="fas fa-eye verPassword" onclick="togglePasswordVisibility()" id="verPassword"></div>
            </div>

            <div class="text-center">
               <a class="font-italic isai5" href="#">Olvidé mi contraseña</a>
            </div>
            <input name="btningresar" class="btn" type="submit" value="INICIAR SESION">
         </form>
      </div>
   </div>
   <script src="js/fontawesome.js"></script>
   <script src="js/main.js"></script>
   <script src="js/main2.js"></script>
   <script src="js/jquery.min.js"></script>
   <script src="js/bootstrap.js"></script>
   <script src="js/bootstrap.bundle.js"></script>
   <script>
      function togglePasswordVisibility() {
         var passwordInput = document.getElementById("clave");
         var passwordType = passwordInput.getAttribute("type") === "password" ? "text" : "password";
         passwordInput.setAttribute("type", passwordType);
         // Cambia el ícono de mostrar/ocultar si es necesario
         var eyeIcon = document.getElementById("verPassword");
         eyeIcon.classList.toggle("fa-eye");
         eyeIcon.classList.toggle("fa-eye-slash");
      }
   </script>
</body>

</html>
