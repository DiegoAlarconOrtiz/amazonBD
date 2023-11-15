<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <?php require "./basedatos.php"; ?>
    <link rel="stylesheet" href="./estilos/navBar.css">
    <link rel="shortcut icon" href="./imagenes/logoFondoBlanco.jpg"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
    <div class="container w-100 h-100 d-flex align-items-center justify-content-center flex-column">
        <?php

        if (isset($usuario)) { ?>
            <h1>Pedidos</h1> <?php

            $sql = "SELECT * FROM pedidos WHERE usuario = '$usuario'";
            $res = $conexion -> query($sql);
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">TOTAL</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($fila = $res -> fetch_assoc()) { ?>
                        <tr>
                            <td class="fs-4 align-middle"><?php echo $fila["idPedido"]?></td>
                            <td class="fs-4 align-middle"><?php echo $fila["fecha"]?></td>
                            <td class="display-6 align-middle"><?php echo $fila["precioTotal"]?>€</td>
                            <td class="align-middle">
                                <form method="post">
                                    <?php
                                    if (!isset($usuario)) { ?>
                                        <input type="hidden" name="login"><?php
                                    } else { ?>
                                        <input type="hidden" name="detalles" value="<?php echo $fila["idPedido"]?>"><?php
                                    } ?>
                                    <button type="submit" class="btn btn-primary">Detalles</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    } ?>    
                </tbody>
            </table>
            <?php
        } else {
            header("Location: ./login.php");
        }
        ?>
    <div class="mb-3">
        <a class="fs-4" href="./principal.php">Volver a la página principal</a>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>