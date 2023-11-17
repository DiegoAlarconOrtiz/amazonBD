<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./estilos/navBar.css">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg" />
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
        
        $usuario = depurar($_POST["usuario"]);
        $contrasena = depurar($_POST["contrasena"]);

        #   Validación del usuario
        if (strlen($usuario) == 0) {
            $err_usuario = "El nombre de usuario es obligatorio";
        } else {
            $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
            $resultado = $conexion -> query($sql);

            if($resultado -> num_rows > 0) {
                while ($fila = $resultado -> fetch_assoc()) {
                    $contrasena_cifrada = $fila["contrasena"];
                    $rol = $fila["rol"];
                }
                $acceso_valido = password_verify($contrasena, $contrasena_cifrada);
                if($acceso_valido) {
                    session_start();
                    $_SESSION["usuario"] = $usuario;
                    $_SESSION["rol"] = $rol;
                    header('location: principal.php');
                } else {
                    $err_login = "Contrasena incorrecta";
                }
            } else {
                $err_login = "El usuario no existe";
            }
        }

        #   Validación de contrasena
        if (strlen($contrasena) == 0) {
            $err_contrasena = "La contrasena es obligatoria";
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
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="logoMenu" src="./imagenes/logo.png" alt="">
                </button>
                <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="dropdownMenuButton2">
                  <li><a class="dropdown-item" href="./principal.php">Principal</a></li>
                  <li><a class="dropdown-item active" href="./login.php">Inicia Sesión</a></li>
                  <li><a class="dropdown-item" href="./formUsuarios.php">Regístrate</a></li>
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
                <button type="submit" class="btn btn-primary" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cesta.png">
                </button>
            </form>
            <form method="post">
                <input type="hidden" name="verPedidos">
                <button type="submit" class="btn btn-primary" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/pedidos.png">
                </button>
            </form>
            <form method="post">
                <input type="hidden" name="cerrarSesion">
                <button type="submit" class="btn btn-primary" style="max-width:60px; max-height:60px;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;"
                        src="./imagenes/cerrarSesion.png">
                </button>
            </form>
        </div>
    </nav>
    <div class="w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        <h1 class="text-center mb-4">Bienvenido a Winged!</h1>
        <h1 class="display-6 mb-4">Inicia sesión</h1>
        <form class="" method="post">
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
                <a href="./formUsuarios.php">¿No tienes una cuenta? Regístrate aquí</a>
            </div>
            <input class="btn btn-primary" type="submit" value="Enviar">
        </form>
        <?php
        if(isset($err_login)) {
            echo "<h3 class='text-danger'>" . $err_login . "</h3>";
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>