<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg"/>
    <link rel="stylesheet" href="./estilos/navBar.css">
    <?php require "./basedatos.php"; ?>
    <?php require "./producto.php" ?>
</head>
<body class="h-100 w-100">
    <?php
    session_start();
    error_reporting(0);
    $usuario = $_SESSION["usuario"];
    error_reporting(-1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["cerrarSesion"])) {
            session_destroy();
            header("Location: ./login.php");
        } else if (isset($_POST["login"])) {
            header("Location: ./login.php");
        } else if (isset($_POST["verCesta"])) {
            header("Location: ./cesta.php");
        } else if (isset($_POST["verPedidos"])) {
            header("Location: ./pedidos.php");
        } else if (isset($_POST["anadir"])) {
            $idProducto = $_POST["anadir"];

            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $res = $conexion -> query($sql);
            $resCesta = $res->fetch_assoc();
            $idCesta = $resCesta["idCesta"];

            $sql = "SELECT cantidad FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
            $res = $conexion -> query($sql) -> fetch_assoc();

            // AQUI VA EL CONTROL DE CANTIDAD DEL SELECT
            $cantidadesDisponibles = ['1', '2', '3', '4', '5'];

            if (in_array($_POST["cantidadAniadida"], $cantidadesDisponibles)) {
                $cantidadAniadida = $_POST["cantidadAniadida"];
            } else {
                $cantidadAniadida = 1;
            }

            error_reporting(0);
            if ($res["cantidad"]) {
                $cantidad = $res['cantidad'] + $cantidadAniadida;
                $sql = "UPDATE productosCestas SET cantidad = $cantidad WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $conexion -> query($sql);
            } else {
                $sql = "INSERT INTO productosCestas (idProducto, idCesta, cantidad) VALUES ('$idProducto', '$idCesta', 1)";
                $conexion -> query($sql);
                if ($cantidadAniadida > 1) {
                    $cantidad = $cantidadAniadida;
                    $sql = "UPDATE productosCestas SET cantidad = $cantidad WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                    $conexion -> query($sql);
                }
            }
            error_reporting(-1);

            $sql = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
            $precioAniadido = $conexion -> query($sql) -> fetch_assoc();
            $precioAniadido = $precioAniadido["precio"];
            $precioAniadido *= $cantidadAniadida;

            $sql = "SELECT precioTotal FROM cestas WHERE idCesta = '$idCesta'";
            $precioTotal = $conexion -> query($sql) -> fetch_assoc();

            $precioFinal = $precioTotal["precioTotal"] + $precioAniadido;

            $sql = "UPDATE cestas SET precioTotal = $precioFinal WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);
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
                    <?php
                    if (isset($usuario)) { ?>
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
                    </div> <?php
                    }
                    ?>
                    
            </form>
            <form method="post">
                    <input type="hidden" name="verPedidos">
                    <button type="submit" class="btn btn-primary"
                            style="max-width:60px; max-height:60px;">
                        <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/pedidos.png">
                    </button>
            </form>
            <form method="post">
                    <input type="hidden" name="cerrarSesion">
                    <button type="submit" class="btn btn-primary"
                            style="max-width:60px; max-height:60px;">
                        <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cerrarSesion.png">
                    </button>
            </form>
        </div>
    </nav>
    <div class="container w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        
        <?php
        
        if (isset($usuario)) {?>
            <div class="mt-3">
                <h2>Bienvenid@ a Winged <?php echo $_SESSION["usuario"]; ?>!</h2>
            </div>
        <?php
        } else {?>
            <div class="mt-3">
                <a class="fs-2" href="./login.php">Parece que no has inciado sesion... Pincha aquí para entrar en tu cuenta</a>
            </div>
            <?php
        }

        $sql = "SELECT * FROM productos";
        $resultado = $conexion -> query($sql);

        $productos = [];

        while ($fila = $resultado -> fetch_assoc()) {
            $producto = new Producto ($fila["idProducto"], $fila["nombreProducto"], $fila["descripcion"], $fila["precio"], $fila["cantidad"], $fila["imagen"]);
            array_push($productos, $producto);
        }

        if($resultado -> num_rows > 0) { ?>
            <h4 class="mt-3">Lista de productos:</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Producto</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Cesta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($productos as $producto) { ?>
                        <tr>
                            <td class="align-items-center align-middle justify-content-center"
                                style="max-width:120px; max-height:120px;">
                                <img style="max-width:100px; max-height:100px;" 
                                    src="./imagenes/<?php echo $producto -> imagen?>">
                            </td>
                            <td class="fs-4 align-middle"><?php echo $producto -> nombreProducto?></td>
                            <td class="display-6 align-middle"><?php echo $producto -> precio?>€</td>
                            <td class="align-middle">
                                <form method="post" class="d-flex">
                                    <?php
                                    if (!isset($usuario)) { ?>
                                        <input type="hidden" name="login"><?php
                                    } else { ?>
                                        <input type="hidden" name="anadir" value="<?php echo $producto -> idProducto?>"><?php
                                    } ?>
                                    <select name="cantidadAniadida" class="form-select mx-3">
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary"
                                            style="max-width:60px; max-height:60px;">
                                        <img style="filter: invert(100%); max-width:40px; max-height:40px;" src="./imagenes/cesta.png" alt="">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    } ?>    
                </tbody>
            </table>
            <?php
        } else {
            $err_productos = "No hay productos";
        }

        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>