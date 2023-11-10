<?php

$servidor="localhost";
$usuario="root";
$contrasena="medac";
$base_datos="amazon";

$conexion = new Mysqli($servidor, $usuario, $contrasena, $base_datos)
    or die("Error de conexion");

?>