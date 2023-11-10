<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require "./basedatos.php"; ?>
    <?php require "./producto.php" ?>
</head>
<body>    
    <div class="container">
        <h1 class="mt-3">Tienda online Diego's</h1>
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
            } else if (isset($_POST["anadir"])) {
                $idProducto = $_POST["anadir"];

                $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
                $res = $conexion -> query($sql);
                $resCesta = $res->fetch_assoc();
                $idCesta = $resCesta["idCesta"];

                $sql = "SELECT cantidad FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $res = $conexion -> query($sql) -> fetch_assoc();

                error_reporting(0);
                if ($res["cantidad"]) {
                    $cantidad = $res['cantidad'] + 1;
                    $sql = "UPDATE productosCestas SET cantidad = $cantidad WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                    $conexion -> query($sql);
                } else {
                    $sql = "INSERT INTO productosCestas (idProducto, idCesta, cantidad) VALUES ('$idProducto', '$idCesta', 1)";
                    $conexion -> query($sql);
                }
                error_reporting(-1);

                $sql = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
                $precioAniadido = $conexion -> query($sql) -> fetch_assoc();

                $sql = "SELECT precioTotal FROM cestas WHERE idCesta = '$idCesta'";
                $precioTotal = $conexion -> query($sql) -> fetch_assoc();

                $precioFinal = $precioTotal["precioTotal"] + $precioAniadido["precio"];

                $sql = "UPDATE cestas SET precioTotal = $precioFinal WHERE idCesta = '$idCesta'";
                $conexion -> query($sql);
            } else if (isset($_POST["verCesta"])) {
                header("Location: ./cesta.php");
            }
        }
        
        if (isset($usuario)) {?>
            <div class="mt-3">
                <h2>Bienvenid@ <?php echo $_SESSION["usuario"]; ?></h2>
                <form method="post">
                    <a href="./cesta.php">
                        <input type="hidden" name="verCesta">
                        <input type="submit" class="btn btn-primary" value="Ver mi cesta">
                    </a>
                </form>
            </div>
            <div class="mt-3">
                <form method="post">
                    <a href="./login.php">
                        <input type="hidden" name="cerrarSesion">
                        <input type="submit" class="btn btn-primary" value="Cerrar sesion">
                    </a>
                </form>
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
                                <form method="post">
                                    <?php
                                    if (!isset($usuario)) { ?>
                                        <input type="hidden" name="login"><?php
                                    } else { ?>
                                        <input type="hidden" name="anadir" value="<?php echo $producto -> idProducto?>"><?php
                                    } ?>
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