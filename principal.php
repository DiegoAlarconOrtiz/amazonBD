<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg" />
    <link rel="stylesheet" href="./estilos/navBar.css">
    <?php require "./basedatos.php"; ?>
    <?php require "./producto.php"; ?>
    <script src="./jquery-3.7.1.min.js"></script>
</head>

<body class="d-flex justify-content-center h-100 w-100">
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

            echo "
            <div id='loginMsj' class='toast bg-success text-center' role='alert' aria-live='assertive' aria-atomic='true'>
            <div class='toast-body text-white fs-6'>
                ¡Has añadido un producto a tu cesta!
            </div>
            </div>
            <script>
                $('#loginMsj').fadeIn('#loginMsj');
                setTimeout(()=>$('#loginMsj').fadeOut('#loginMsj'), 3000);
            </script>";
        }
    }
    ?>

    <nav class="navBar">
        <div class="navTitulo">
            <img id="logo" src="./imagenes/logo.png">
            <h2 id="tituloPrincipal" class="display-6">Winged</h2>
        </div>
        <div class="navEnlaces">
            <div id="menuDesplegable" class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="logoMenu" src="./imagenes/logo.png" alt="">
                </button>
                <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="dropdownMenuButton2">
                  <li><a class="dropdown-item active" href="./principal.php">Principal</a></li>
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
            error_reporting(0);
            $rol = $_SESSION["rol"];
            if ($rol == "admin") {
                ?><a class="fs-4 m-3 link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-25-hover"
                href="./formProductos.php">Stockaje</a><?php
            }
            error_reporting(-1);
            ?>
        </div>
        <div class="navOpciones">
            <form method="post" class="position-relative">
                <input type="hidden" name="verCesta">
                <button type="submit" class="btn btn-primary position-relative"
                    style="max-width:60px; max-height:60px; margin: 0.3rem;">
                    <img style="filter: invert(100%); max-width:30px; max-height:30px;" src="./imagenes/cesta.png">
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
        
        if (isset($usuario)) {?>
        <div class="mt-3">
            <h2>Bienvenid@ a Winged <?php echo $_SESSION["usuario"]; ?>!</h2>
        </div>
        <?php
        } else {?>
        <div class="mt-3">
            <a class="fs-2" href="./login.php">Parece que no has inciado sesion... Pincha aquí para entrar en tu
                cuenta</a>
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
        <div id="contenedorTabla" class="container overflow-auto" style="max-height:60%">
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
                        style="max-width:120px; max-height:120px; position:relative">
                        <img style="max-width:100px; max-height:100px;"
                            src="./imagenes/<?php echo $producto -> imagen?>">
                        <?php
                        if ($producto -> cantidad == 0) {
                            ?>
                            <img style="max-width:65px; max-height:65px;position:absolute;top:0;left:0;"
                                src="./imagenes/agotado.png">
                            <?php
                        } else if ($producto -> cantidad < 5) {
                            ?>
                            <img id="imgUltimasUnidades" style="max-width:65px; max-height:65px;position:absolute;top:0;left:0;"
                                src="./imagenes/ultimasunidades.png">
                            <?php
                        }
                        ?>
                    </td>
                    <td id="nombreProducto" class="fs-4 align-middle"><?php echo $producto -> nombreProducto?></td>
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
                                <?php
                                $idProducto = $producto -> idProducto;
                                $sql = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                                $res = $conexion -> query($sql) -> fetch_assoc();
                
                                $cantidad = $res["cantidad"];
                                
                                ?> <option value="1" selected>1</option> <?php
                                if ($cantidad < 5) {
                                    for ($i=2; $i <= $cantidad; $i++) { 
                                        ?> <option value="<?php echo $i?>"><?php echo $i?></option> <?php
                                    }
                                } else {
                                    ?>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <?php
                                }
                                ?>
                                
                            </select>
                            <?php
                            $sql = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                            $resProductos = $conexion -> query($sql) -> fetch_assoc();
                            $stock = $resProductos["cantidad"];

                            if ($stock == 0) {
                                echo "Sin Stock";
                            } else { ?>
                            <button type="submit" class="btn btn-primary" style="max-width:60px; max-height:60px;">
                                <img style="filter: invert(100%); max-width:40px; max-height:40px;"
                                    src="./imagenes/cesta.png" alt="">
                            </button>
                            <?php
                            }
                            ?>
                            
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

        ?>
    </div>
    
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>