<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg"/>
    <link rel="stylesheet" href="./estilos/navBar.css">
    <?php require "./basedatos.php"; ?>
</head>
<body class="h-100 w-100">
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
            <a class="fs-6 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./cesta.php">Ver Cesta</a>
            <a class="fs-6 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./pedidos.php">Ver Pedidos</a>
            <a class="fs-6 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formUsuarios.php">Cerrar Sesión</a>
        </div>
    </nav>
    <?php
    session_start();
    error_reporting(0);
    $usuario = $_SESSION["usuario"];
    error_reporting(-1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["eliminar"])) {
            $idProducto = $_POST["eliminar"];

            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoProductos = $conexion -> query($sql);
            $res = $resultadoProductos -> fetch_assoc();

            $idCesta = $res["idCesta"];

            $sql = "SELECT cantidad FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
            $res = $conexion -> query($sql) -> fetch_assoc();

            if ($res["cantidad"] == 1) {
                $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $conexion -> query($sql);
            } else {
                $cantidad = $res['cantidad'] - 1;
                $sql = "UPDATE productosCestas SET cantidad = $cantidad WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $conexion -> query($sql);
            }

            $sql = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
            $precioRestar = $conexion -> query($sql) -> fetch_assoc();

            $sql = "SELECT precioTotal FROM cestas WHERE idCesta = '$idCesta'";
            $precioTotal = $conexion -> query($sql) -> fetch_assoc();

            $precioFinal = $precioTotal["precioTotal"] - $precioRestar["precio"];

            $sql = "UPDATE cestas SET precioTotal = $precioFinal WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);
            
        } else if (isset($_POST["comprar"])) {
            // Crear un pedido con la info de la cesta

            $hoy = getdate();
            $fecha = $hoy['year'] . "-" . $hoy['mon'] . "-" . $hoy['mday'];

            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoProductos = $conexion -> query($sql);
            $res = $resultadoProductos -> fetch_assoc();

            $idCesta = $res["idCesta"];

            $sql = "SELECT precioTotal FROM cestas WHERE idCesta = '$idCesta'";
            $precioTotal = $conexion -> query($sql) -> fetch_assoc();
            $precioTotal = $precioTotal["precioTotal"];

            $sql = "INSERT INTO pedidos (fecha, precioTotal, usuario) VALUES ('$fecha', '$precioTotal', '$usuario')";
            $conexion -> query($sql);

            $sql = "SELECT idPedido FROM pedidos WHERE usuario = '$usuario' ORDER BY idPedido DESC";
            $res = $conexion -> query($sql) -> fetch_assoc();
            $idPedido = $res["idPedido"];

            $sql = "SELECT * FROM productosCestas WHERE idCesta = '$idCesta'";
            $res = $conexion -> query($sql);

            $lineaPedido = 1;
            while ($fila = $res -> fetch_assoc()) {
                $idProducto = $fila["idProducto"];
                $cantidad = $fila["cantidad"];
                $sql = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
                $resPrecio = $conexion -> query($sql) -> fetch_assoc();
                $precioUnitario = $resPrecio["precio"];
                $sql = "INSERT INTO lineasPedidos (idPedido, lineaPedido, idProducto, cantidad, precioUnitario) 
                        VALUES ('$idPedido', '$lineaPedido', '$idProducto', '$cantidad', '$precioUnitario')";
                $conexion -> query($sql);
                $lineaPedido++;
            }

            // Eliminar cesta

            $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);

            $sql = "UPDATE cestas SET precioTotal = '0' WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);
        }
    }    
    ?>

    <div class="container w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        <?php
        if (isset($usuario)) { ?>
            <h1 class="mt-3">Cesta de <?php echo $usuario ?></h1> <?php

            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoProductos = $conexion -> query($sql);
            $res = $resultadoProductos -> fetch_assoc();

            $idCesta = $res["idCesta"];

            $sql = "SELECT * FROM productosCestas WHERE idCesta = '$idCesta'";
            $resultadoProductosCestas = $conexion -> query($sql);

            if($resultadoProductos -> num_rows > 0) { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Cant.</th>
                            <th scope="col"></th>
                            <th scope="col">Producto</th>
                            <th scope="col">Precio</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($fila = $resultadoProductosCestas -> fetch_assoc()) { ?>
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
                                    <img style="max-width:100px; max-height:100px;" 
                                        src="./imagenes/<?php echo $res["imagen"]?>">
                                </td>
                                <td class="fs-4 align-middle"><?php echo $res["nombreProducto"]?></td>
                                <td class="display-6 align-middle"><?php echo $res["precio"]?>€</td>
                                <td class="align-middle">
                                    <form method="post">
                                        <input type="hidden" name="eliminar" value="<?php echo $idProducto?>">
                                        <button type="submit" class="btn btn-primary"
                                                style="max-width:60px; max-height:60px;">
                                            <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/eliminar.png" alt="">
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
        } else { ?>
            <h1>No puedes acceder a tu cesta si no has iniciado sesion</h1>
            <a href="./login.php">Pincha aquí para ir al login</a> <?php
        }
        ?>
        <div class="mb-3">
            <?php
            $sql = "SELECT precioTotal FROM cestas WHERE usuario = '$usuario'";
            $res = $conexion -> query($sql);
            $precioTotal = $res -> fetch_assoc();

            $precioTotal = $precioTotal["precioTotal"];
            ?>
            <h3 class="display-6">Precio total: <?php echo $precioTotal ?>€</h3>
        </div>
        <div class="mb-3">
            <form method="post">
                <input type="hidden" name="comprar">
                <input type="submit" class="btn btn-primary" value="Finalizar compra">      
            </form>
        </div>
        <div class="mb-3">
            <a class="fs-4" href="./principal.php">Volver a la página principal</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>