<?php
include_once("Model.php");
class ProductosModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getAllProducto(){
        return $this -> queryTodasLasFilas(
                'SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )', array());
    }
    public function searchIdProducto($idProd){
        return $this -> queryTodasLasFilas(
                'SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.idProducto='.$idProd, array());
    }
}
?>