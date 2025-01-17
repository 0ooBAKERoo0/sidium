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
         <form method="post" action="">
            <img src="img/avatar.svg">
            <h2 class="title">BIENVENIDO</h2>
            <?php
            include("modelo/conexion.php");

            session_start();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

               if ($resultado->num_rows > 0) {
                   $usuarioData = $resultado->fetch_assoc();

                   // Validar la contraseña ingresada
                   if ($usuarioData['password'] === $password) {
                       $_SESSION['usuario'] = $usuarioData['matricula'];
                       $_SESSION['tipo_cuenta'] = $usuarioData['tipo_cuenta'];

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
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Matrícula</h5>
                  <input id="matricula" type="text" class="input" name="matricula" required>
               </div>
            </div>
            <div class="input-div pass">
               <div class="i">
                  <i class="fas fa-lock"></i>
               </div>
               <div class="div">
                  <h5>Contraseña</h5>
                  <input type="password" id="password" class="input" name="password" required>
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
         var passwordInput = document.getElementById("password");
         var passwordType = passwordInput.getAttribute("type") === "password" ? "text" : "password";
         passwordInput.setAttribute("type", passwordType);
         var eyeIcon = document.getElementById("verPassword");
         eyeIcon.classList.toggle("fa-eye");
         eyeIcon.classList.toggle("fa-eye-slash");
      }
   </script>
</body>
</html>





<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <title>Inicio de sesión</title>
</head>
<body>
   <img class="wave" src="img/umb3.jpg">
   <div class="container">
      <div class="login-content">
         <form method="post" action="">
            <h2 class="title">BIENVENIDO</h2>
            <?php
            include("modelo/conexion.php");

            session_start();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

               if ($resultado->num_rows > 0) {
                   $usuarioData = $resultado->fetch_assoc();

                   // Validar la contraseña ingresada
                   if (password_verify($password, $usuarioData['password'])) {
                       $_SESSION['usuario'] = $usuarioData['matricula'];

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
               </div>
            </div>
            <input name="btningresar" class="btn" type="submit" value="INICIAR SESION">
         </form>
      </div>
   </div>
   <script src="js/bootstrap.js"></script>
</body>
</html>