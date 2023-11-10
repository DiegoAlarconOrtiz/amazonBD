<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <?php require "./basedatos.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-3">
        <?php
        session_start();
        error_reporting(0);
        $usuario = $_SESSION["usuario"];
        error_reporting(-1);

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
        <a class="fs-4" href="./principal.php">Voler a la página principal</a>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>