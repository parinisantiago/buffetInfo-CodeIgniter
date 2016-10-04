<?php
include_once("Model.php");
class ProductosModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getAllProducto(){
        return $this -> queryTodasLasFilas(
                'SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria) 
                WHERE p.eliminado = 0 ', array());
    }
    
    public function searchIdProducto($idProd){
        return $this -> query(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array(idProd => $idProd));
    }
    public function deleteProducto($idProd){
        return $this -> query(
                "UPDATE producto p 
                SET p.eliminado=1
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array(idProd => $idProd));
    }
    
    public function listarProductosStockMinimo(){
        return $this -> queryTodasLasFilas(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.stock <= p.stockMinimo ", array());
    }
    public function listarProductosFaltantes(){
        return $this -> queryTodasLasFilas(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                 WHERE p.eliminado = 0 and p.stock = 0 ", array());
    }
}

?>
