<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg"/>
    <link rel="stylesheet" href="./estilos/navBar.css">
    <?php require '../funciones.php' ?>
    <?php require './basedatos.php' ?>
</head>
<body class="container">

    <?php
    session_start();
    $usuario = $_SESSION["usuario"];
    $rol = $_SESSION["rol"];

    if ($rol != "admin") {
        header("Location: ./login.php");
    }
    ?>

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
        
        $temp_nombre = depurar($_POST["nombre"]);
        $temp_precio = depurar($_POST["precio"]);
        $temp_descripcion = depurar($_POST["descripcion"]);
        $temp_cantidad = depurar($_POST["cantidad"]);
        $temp_imagen = $_FILES["imagen"];

        #   Validación del nombre
        if (strlen($temp_nombre) == 0) {
            $err_nombre = "El nombre de producto es obligatorio";
        } else {
            $regex = "/^[a-zA-ZñÑ0-9][a-zA-ZñÑ0-9 ]{1,40}$/";
            if (!preg_match($regex, $temp_nombre)) {
                $err_nombre = "El nombre de producto debe contener
                     unicamente letras numeros y espacios max 40 caracteres";
            } else {
                $nombre = $temp_nombre;
            }
        }

        #   Validación del precio
        if (strlen($temp_precio) == 0) {
            $err_precio = "El precio de producto es obligatorio";
        } else {
            if (!filter_var($temp_precio, FILTER_VALIDATE_FLOAT)) {
                $err_precio = "El precio debe ser un numero";
            } else {
                if ($temp_precio < 0 || $temp_precio > 99999.99) {
                    $err_precio = "El precio de producto debe estar
                         entre 0 y 99999.99";
                } else {
                    $precio = $temp_precio;
                }
            }
        }

        #   Validación del descripcion
        if (strlen($temp_descripcion) == 0) {
            $err_descripcion = "La descripcion de producto es obligatorio";
        } else {
            $regex = "/^[a-zA-ZñÑ0-9 ]{1,255}$/";
            if (!preg_match($regex, $temp_descripcion)) {
                $err_descripcion = "La descripcion de producto debe contener
                     unicamente letras numeros y espacios max 255 caracteres";
            } else {
                $descripcion = $temp_descripcion;
            }
        }

        #   Validación de cantidad
        if (strlen($temp_cantidad) == 0) {
            $err_cantidad = "La cantidad de producto es obligatorio";
        } else {
            if (!filter_var($temp_cantidad, FILTER_VALIDATE_INT)) {
                $err_cantidad = "La cantidad debe ser un numero";
            } else {
                if ($temp_cantidad < 0 || $temp_cantidad > 99999) {
                    $err_cantidad = "La cantidad de producto debe estar
                         entre 0 y 99999";
                } else {
                    $cantidad = $temp_cantidad;
                }
            }
        }

        #   Validación de imagen
        if (strlen($temp_imagen["name"]) == 0) {
            $err_imagen = "La imagen de producto es obligatoria";
        } else {
            if (!getimagesize($temp_imagen["tmp_name"])) {
                $err_imagen = "Debes subir una imagen";
            } else {
                $imagen = $temp_imagen["name"];
            }
        }
    }

    ?>
    <nav class="navBar">
        <div class="navTitulo">
            <img id="logo" src="./imagenes/logo.png">
            <h2 class="display-6">Winged</h2>
        </div>
        <div class="navEnlaces">
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./principal.php">Principal</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./login.php">Inicia Sesión</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formUsuarios.php">Regístrate</a>
        </div>
        <div class="navOpciones">
            <form method="post" class="position-relative">
                    <input type="hidden" name="verCesta">
                    <button type="submit" class="btn btn-primary position-relative"
                            style="max-width:60px; max-height:60px; margin: 0.3rem;">
                        <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cesta.png" >
                    </button>
                    <div class="cantidadCestaNav">
                        <?php
                        $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
                        $res = $conexion -> query($sql);
                        $resCesta = $res->fetch_assoc();
                        $idCesta = $resCesta["idCesta"];
        
                        $sql = "SELECT cantidad FROM productosCestas WHERE idCesta = '$idCesta'";
                        $res = $conexion -> query($sql);

                        $totalCantidadCesta = 0;

                        while ($elem = $res -> fetch_assoc()) {
                            $totalCantidadCesta += $elem["cantidad"];
                        }

                        echo $totalCantidadCesta;
                        ?>
                    </div>
            </form>
            <form method="post">
                    <input type="hidden" name="verPedidos">
                    <button type="submit" class="btn btn-primary"
                            style="max-width:60px; max-height:60px;">
                        <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/pedidos.png">
                    </button>            </form>
            <form method="post">
                    <input type="hidden" name="cerrarSesion">
                    <button type="submit" class="btn btn-primary"
                            style="max-width:60px; max-height:60px;">
                        <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cerrarSesion.png">
                    </button>
            </form>
        </div>
    </nav>
    <h1 class="mt-3 mb-3">Nuevo producto</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">NOMBRE: </label>
            <input class="form-control" type="text" name="nombre">
            <?php if(isset($err_nombre)) { ?>
                <div class="text-danger">
                    <?php echo $err_nombre ?>
                </div>
            <?php
            } ?>
        </div>
        <div class="mb-3">
            <label class="form-label">PRECIO (Entre 0 y 99999.99): </label>
            <input class="form-control" type="text" name="precio">
            <?php if(isset($err_precio)) { ?>
                <div class="text-danger">
                    <?php echo $err_precio ?>
                </div>
            <?php
            } ?>
        </div>
        <div class="mb-3">
            <label class="form-label">DESCRIPCION: </label>
            <input class="form-control" type="text" name="descripcion">
            <?php if(isset($err_descripcion)) { ?>
                <div class="text-danger">
                    <?php echo $err_descripcion ?>
                </div>
            <?php
            } ?>
        </div>
        <div class="mb-3">
            <label class="form-label">CANTIDAD: </label>
            <input class="form-control" type="number" name="cantidad">
            <?php if(isset($err_cantidad)) { ?>
                <div class="text-danger">
                    <?php echo $err_cantidad ?>
                </div>
            <?php
            } ?>
        </div>
        <div class="mb-3">
            <label class="form-label">IMAGEN: </label>
            <input class="form-control" type="file" name="imagen">
            <?php if(isset($err_imagen)) { ?>
                <div class="text-danger">
                    <?php echo $err_imagen ?>
                </div>
            <?php
            } ?>
        </div>
        <input class="btn btn-primary" type="submit" value="Enviar">
    </form>
    <?php
    if(isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($imagen)) {
        echo "<h3>Producto registrado con exito!</h3>";

        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen)
            VALUES ('$nombre', '$precio', '$descripcion', '$cantidad', '$imagen')";
        $conexion->query($sql);
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>