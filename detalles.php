<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg" />
    <link rel="stylesheet" href="./estilos/navBar.css">
    <script src="./jquery-3.7.1.min.js"></script>
    <?php require "./basedatos.php"; ?>
</head>

<body class="d-flex justify-content-center h-100 w-100">
    <?php
    session_start();
    error_reporting(0);
    $usuario = $_SESSION["usuario"];
    $idPedido = $_SESSION["idPedido"];
    error_reporting(-1);

    if (!isset($usuario)) {
        header("Location: ./login.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["cerrarSesion"])) {
            session_destroy();
            header("Location: ./login.php");
        } else if (isset($_POST["verCesta"])) {
            header("Location: ./cesta.php");
        } else if (isset($_POST["verPedidos"])) {
            header("Location: ./pedidos.php");
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
                  <li><a class="dropdown-item" href="./formUsuarios.php">Regístrate</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="./formProductos.php">Stock</a></li>
                </ul>
            </div>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./principal.php">Principal</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./login.php">Inicia Sesión</a>
            <a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formUsuarios.php">Regístrate</a>
            <?php
            $rol = $_SESSION["rol"];
            if ($rol == "admin") {
                ?><a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formProductos.php">Stockaje</a><?php
            }
            ?>
        </div>
        <div class="navOpciones">
            <form method="post" class="position-relative">
                <input type="hidden" name="verCesta">
                <button type="submit" class="btn position-relative"
                    style="max-width:60px; max-height:60px; margin: 0.3rem;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cesta.png">
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

    <div class="container w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        <?php
        if (isset($usuario)) { ?>
            <h1 class="mt-3">Pedido nº <?php echo $idPedido ?></h1> <?php

            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoProductos = $conexion -> query($sql);
            $res = $resultadoProductos -> fetch_assoc();

            $idCesta = $res["idCesta"];

            $sql = "SELECT * FROM lineasPedidos WHERE idPedido = '$idPedido'";
            $resultadoProductosPedido = $conexion -> query($sql);

            if($resultadoProductos -> num_rows > 0) { ?>
            <div id="contenedorTabla" class="container overflow-auto" style="max-height:40%">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Cant.</th>
                        <th scope="col"></th>
                        <th scope="col">Producto</th>
                        <th scope="col">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($fila = $resultadoProductosPedido -> fetch_assoc()) { ?>
                    <tr>
                        <td class="align-middle fs-3"><?php echo $fila["cantidad"]?></td>
                            <?php
                                $idProducto = $fila["idProducto"];
                                $sql = "SELECT * FROM productos WHERE idProducto = '$idProducto'";
                                $resultadoProductos = $conexion -> query($sql);
                                $res = $resultadoProductos -> fetch_assoc();
                            ?>
                        <td class="align-items-center align-middle justify-content-center"
                            style="max-width:120px; max-height:120px;">
                            <img style="max-width:100px; max-height:100px;" src="./imagenes/<?php echo $res["imagen"]?>">
                        </td>
                        <td id="nombreProducto" class="fs-4 align-middle"><?php echo $res["nombreProducto"]?></td>
                        <td class="display-6 align-middle"><?php echo $res["precio"]?>€</td>
                    </tr>
                    <?php
                    } ?>
                </tbody>
            </table>
            </div>
            <?php
            } else {
                $err_productos = "No hay productos";
            }
        } else { ?>
        <h1>No puedes acceder a tu cesta si no has iniciado sesion</h1>
        <a href="./login.php">Pincha aquí para ir al login</a> <?php
        }
        ?>
        <div class="mb-3">
            <?php
            if (isset($usuario)) {
                $sql = "SELECT precioTotal FROM pedidos WHERE idPedido = '$idPedido'";
                $res = $conexion -> query($sql);
                $precioTotal = $res -> fetch_assoc();

                $precioTotal = $precioTotal["precioTotal"];
                ?>
            <h3 class="display-6">Precio total: <?php echo $precioTotal ?>€</h3>
            <?php
            }
            ?>
        </div>
        <div class="mb-3">
            <a class="fs-4" href="./principal.php">Volver a la página principal</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>