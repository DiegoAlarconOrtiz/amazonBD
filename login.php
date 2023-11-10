<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require '../funciones.php' ?>
    <?php require './basedatos.php' ?>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    <div class="container mt-3">
        <h1>Iniciar sesion</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario: </label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($err_usuario)) { ?>
                    <div class="text-danger">
                        <?php echo $err_usuario ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña: </label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) { ?>
                    <div class="text-danger">
                        <?php echo $err_contrasena ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>