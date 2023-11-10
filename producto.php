<?php
class Producto {
    public int $idProducto;
    public string $nombreProducto;
    public string $descripcion;
    public float $precio;
    public int $cantidad;
    public string $imagen;

    function __construct(int $idProducto, string $nombreProducto, string $descripcion, float $precio, int $cantidad, string $imagen){
        $this -> idProducto = $idProducto;
        $this -> nombreProducto = $nombreProducto;
        $this -> descripcion = $descripcion;
        $this -> precio = $precio;
        $this -> cantidad = $cantidad;
        $this -> imagen = $imagen;
    }

}
?>