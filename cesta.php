<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cesta</title>
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
    error_reporting(-1);

    if (!isset($usuario)) {
        header("Location: ./login.php");
    }

    $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
    $resultadoProductos = $conexion -> query($sql);
    $res = $resultadoProductos -> fetch_assoc();

    $idCesta = $res["idCesta"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["eliminar"])) {
            $idProducto = $_POST["eliminar"];
            $cantidadEliminada = $_POST["cantidadEliminada"];

            $sql = "SELECT cantidad FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
            $res = $conexion -> query($sql) -> fetch_assoc();

            if ($res["cantidad"] - $cantidadEliminada == 0) {
                $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $conexion -> query($sql);
            } else {
                $cantidad = $res['cantidad'] - $cantidadEliminada;
                $sql = "UPDATE productosCestas SET cantidad = $cantidad WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $conexion -> query($sql);
            }

            $sql = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
            $precioRestar = $conexion -> query($sql) -> fetch_assoc();

            $sql = "SELECT precioTotal FROM cestas WHERE idCesta = '$idCesta'";
            $precioTotal = $conexion -> query($sql) -> fetch_assoc();

            $precioFinal = $precioTotal["precioTotal"] - ($precioRestar["precio"] * $cantidadEliminada);

            $sql = "UPDATE cestas SET precioTotal = $precioFinal WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);

            echo "
                <div id='loginMsj' class='toast bg-danger text-white text-center' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='toast-body text-white fs-6'>
                    Has eliminado un producto de tu cesta
                </div>
                </div>
                <script>
                    $('#loginMsj').fadeIn('#loginMsj');
                    setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
                </script>";
            
        } else if (isset($_POST["comprar"])) {

            $err_pedido = false;
            
            $sql = "SELECT * FROM productosCestas WHERE idCesta = '$idCesta'";
            $res = $conexion -> query($sql);

            if ($res -> num_rows == 0) {
                $err_pedido = true;
            }

            while ($fila = $res -> fetch_assoc()) {
                $idProducto = $fila["idProducto"];
                $cantidadEnCesta = $fila["cantidad"];

                $sql = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                $resProductos = $conexion -> query($sql) -> fetch_assoc();
                
                $stock = $resProductos["cantidad"];

                if ($cantidadEnCesta > $stock) {
                    echo "
                        <div id='loginMsj' class='toast bg-danger text-white text-center' role='alert' aria-live='assertive' aria-atomic='true'>
                        <div class='toast-body text-white'>
                            No hay stock para el producto con ID: $idProducto, solo quedan $stock</div>
                        </div>
                        <script>
                            $('#loginMsj').fadeIn('#loginMsj');
                            setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
                        </script>";
                        $err_pedido = true;
                }
            }

            if ($err_pedido) {
                echo "
                    <div id='loginMsj' class='toast bg-danger text-white text-center' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='toast-body text-white fs-6'>
                        La cesta está vacía
                    </div>
                    </div>
                    <script>
                        $('#loginMsj').fadeIn('#loginMsj');
                        setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
                    </script>";
            } else {
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

                    // Actualizar stock

                    $sql = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                    $resProductos = $conexion -> query($sql) -> fetch_assoc();
                    $stock = $resProductos["cantidad"];

                    $nuevaCantidad = $stock - $cantidad;

                    $sql = "UPDATE productos SET cantidad = '$nuevaCantidad' WHERE idProducto = '$idProducto'";
                    $conexion -> query($sql);

                    $lineaPedido++;
                }
    
                // Eliminar cesta
    
                $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
                $conexion -> query($sql);
    
                $sql = "UPDATE cestas SET precioTotal = '0' WHERE idCesta = '$idCesta'";
                $conexion -> query($sql);

                echo "
                <div id='loginMsj' class='toast bg-success text-white text-center' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='toast-body text-white fs-6'>
                    ¡Has realizado tu pedido con éxito!
                </div>
                </div>
                <script>
                    $('#loginMsj').fadeIn('#loginMsj');
                    setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
                </script>";
            }

        } else if (isset($_POST["eliminarTodo"])) {
            $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoProductos = $conexion -> query($sql);
            $res = $resultadoProductos -> fetch_assoc();

            $idCesta = $res["idCesta"];

            $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);

            $sql = "UPDATE cestas SET precioTotal = '0' WHERE idCesta = '$idCesta'";
            $conexion -> query($sql);

            echo "
            <div id='loginMsj' class='toast bg-danger text-white text-center' role='alert' aria-live='assertive' aria-atomic='true'>
            <div class='toast-body text-white fs-6'>
                ¡Has vaciado tu cesta!
            </div>
            </div>
            <script>
                $('#loginMsj').fadeIn('#loginMsj');
                setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
            </script>";
        } else if (isset($_POST["cerrarSesion"])) {
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
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
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
                <button type="submit" class="btn btn-primary position-relative"
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
            <div id="contenedorTabla" class="container overflow-auto" style="max-height:40%">
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
                            <img style="max-width:100px; max-height:100px;" src="./imagenes/<?php echo $res["imagen"]?>">
                        </td>
                        <td id="nombreProducto" class="fs-4 align-middle"><?php echo $res["nombreProducto"]?></td>
                        <td class="display-6 align-middle"><?php echo $res["precio"]?>€</td>
                        <td class="align-middle">
                            <form method="post" class="d-flex">
                                <select name="cantidadEliminada" class="form-select mx-3">
                                    <?php
                                    $sql = "SELECT cantidad FROM productosCestas WHERE idProducto = '$idProducto' AND idCesta = '$idCesta'";
                                    $res = $conexion -> query($sql) -> fetch_assoc();
                
                                    $cantidad = $res["cantidad"];
                                
                                    ?> <option value="1" selected>1</option> <?php
                                    for ($i=2; $i <= $cantidad; $i++) { 
                                        ?> <option value="<?php echo $i?>"><?php echo $i?></option> <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="eliminar" value="<?php echo $idProducto?>">
                                <button type="submit" class="btn btn-danger" style="max-width:60px; max-height:60px;">
                                    <img style="filter: invert(100%); max-width:30px; max-height:30px;"
                                        src="./imagenes/eliminar.png">
                                </button>
                            </form>
                        </td>
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
                $sql = "SELECT precioTotal FROM cestas WHERE usuario = '$usuario'";
                $res = $conexion -> query($sql);
                $precioTotal = $res -> fetch_assoc();

                $precioTotal = $precioTotal["precioTotal"];
                ?>
            <h3 class="display-6">Precio total: <?php echo $precioTotal ?>€</h3>
            <?php
            }
            ?>
        </div> <?php
        if (isset($usuario)) {
        ?>
        <div class="mb-3">
            <form method="post">
                <input type="hidden" name="eliminarTodo">
                <input type="submit" class="btn btn-danger" value="Vaciar Cesta">
            </form>
        </div>
        <div class="mb-3">
            <form method="post">
                <input type="hidden" name="comprar">
                <input type="submit" class="btn btn-primary" value="Finalizar compra">
            </form>
        </div>
        <?php
        } ?>
        <div class="mb-3">
            <a class="fs-4" href="./principal.php">Volver a la página principal</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>