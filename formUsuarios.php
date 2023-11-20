<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg" />
    <link rel="stylesheet" href="./estilos/navBar.css">
    <?php require '../funciones.php' ?>
    <?php require './basedatos.php' ?>
</head>

<body class="h-100 w-100">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["cerrarSesion"])) {
            session_destroy();
            header("Location: ./login.php");
        } else if (isset($_POST["verCesta"])) {
            header("Location: ./cesta.php");
        } else if (isset($_POST["verPedidos"])) {
            header("Location: ./pedidos.php");
        }
        
        $temp_usuario = depurar($_POST["usuario"]);
        $temp_contrasena = depurar($_POST["contrasena"]);
        $temp_fechaNacimiento = depurar($_POST["fechaNacimiento"]);

        #   Validación del usuario
        if (strlen($temp_usuario) == 0) {
            $err_usuario = "El nombre de usuario es obligatorio";
        } else {
            $regex = "/^[a-zA-ZñÑ_][a-zA-ZñÑ_ ]{3,11}$/";
            if (!preg_match($regex, $temp_usuario)) {
                $err_usuario = "El nombre de usuario debe contener de 4 a 12 
                    caracteres o barrabaja";
            } else {
                $usuario = $temp_usuario;
            }
        }

        #   Validación de contrasena
        if (strlen($temp_contrasena) == 0) {
            $err_contrasena = "El nombre de usuario es obligatorio";
        } else {
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/';
            if (strlen($temp_contrasena) > 255) {
                $err_contrasena = "La contrasena no puede superar los 255 caracteres";
            } else if (!preg_match($regex, $temp_contrasena)) {
                $err_contrasena = "La contrasena debe contener de 8 a 20 caracteres,
                 contener una minuscula, una mayuscula, un numero y un caracter especial";
            } else {
                $contrasena = password_hash($temp_contrasena, PASSWORD_DEFAULT);
            }
        }

        #   Validación de la fecha de nacimiento
        if (strlen($temp_fechaNacimiento) == 0) {
            $err_fechaNacimiento = "La fecha de nacimiento es obligatoria";
        } else {
            $dt = DateTime::createFromFormat("Y-m-d", $temp_fechaNacimiento);
            $fecha_actual = new DateTime();
            $diferencia = $fecha_actual->diff($dt);
            $anios = $diferencia->y;

            if ($anios < 12 || $anios > 120) {
                $err_fechaNacimiento = "La edad debe comprenderse entre 12 y 120";
            } else {
                $fechaNacimiento = $temp_fechaNacimiento;
            }
        }
    }
    ?>
    <nav class="navBar">
        <div class="navTitulo">
            <img id="logo" src="./imagenes/logo.png">
            <h2 id="tituloPrincipal"  class="display-6">Winged</h2>
        </div>
        <div class="navEnlaces">
            <div id="menuDesplegable" class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="logoMenu" src="./imagenes/logo.png" alt="">
                </button>
                <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="dropdownMenuButton2">
                  <li><a class="dropdown-item" href="./principal.php">Principal</a></li>
                  <li><a class="dropdown-item" href="./login.php">Inicia Sesión</a></li>
                  <li><a class="dropdown-item active" href="./formUsuarios.php">Regístrate</a></li>
                </ul>
            </div>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./principal.php">Principal</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./login.php">Inicia Sesión</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formUsuarios.php">Regístrate</a>
        </div>
        <div class="navOpciones">
            <form method="post">
                <input type="hidden" name="verCesta">
                <button type="submit" class="btn" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cesta.png">
                </button>
            </form>
            <form method="post">
                <input type="hidden" name="verPedidos">
                <button type="submit" class="btn" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/pedidos.png">
                </button>
            </form>
            <form method="post">
                <input type="hidden" name="cerrarSesion">
                <button type="submit" class="btn" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;"
                        src="./imagenes/cerrarSesion.png">
                </button>
            </form>
        </div>
    </nav>
    <div class="w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        <h1 class="text-center mb-4">Bienvenido a Winged!</h1>
        <h1 class="display-6 mb-4">Registrarse</h1>
        <form action="" method="post">
            <div class="mb-4">
                <label class="form-label">Usuario: </label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($err_usuario)) { ?>
                <div class="text-danger">
                    <?php echo $err_usuario ?>
                </div>
                <?php
                } ?>
            </div>
            <div class="mb-4">
                <label class="form-label">Contraseña: </label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) { ?>
                <div class="text-danger">
                    <?php echo $err_contrasena ?>
                </div>
                <?php
                } ?>
            </div>
            <div class="mb-4">
                <label>Fecha nacimiento: </label>
                <input class="form-control" type="date" name="fechaNacimiento">
                <?php if(isset($err_fechaNacimiento)) { ?>
                <div class="text-danger">
                    <?php echo $err_fechaNacimiento ?>
                </div>
                <?php
                } ?>
            </div>
            <div class="mb-4">
                <a href="./login.php">¿Ya tienes una cuenta? Inicia sesión aquí</a>
            </div>
            <input class="btn btn-primary" type="submit" value="Enviar">
        </form>
        <?php
        if(isset($usuario) && isset($contrasena) && isset($fechaNacimiento)) {
            echo "<h3>Exito!</h3>";

            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento, rol)
                VALUES ('$usuario', '$contrasena', '$fechaNacimiento', 'cliente')";
            $conexion->query($sql);
            $sql_cesta = "INSERT INTO cestas (usuario, precioTotal)
                VALUES ('$usuario', 0)";
            $conexion->query($sql_cesta);
        }
    ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>