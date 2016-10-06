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
        return $this ->queryPreparadaSQL(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto,c.nombre as categoria
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array(idProd => $idProd));
    }
    public function actualizarProducto($Prod){
        $today=getDate();
        return $this -> query("
                UPDATE producto 
                SET nombre = :nombre,
                    marca = :marca,
                    stock = :stock,
                    stockMinimo = :stockMinimo,
                    idCategoria = :categoria,
                    proveedor = :proveedor,
                    precioVentaUnitario = :precioVentaUnitario,
                    descripcion = :descripcion, 
                    fechaAlta = :fechaAlta
                WHERE idProducto= :idProducto",
                array('nombre' => $Prod["nombre"],
                    'marca' => $Prod["marca"],
                    'stock' => $Prod["stock"],
                    'stockMinimo' => $Prod["stockMinimo"],
                    'categoria' => $Prod["categoria"],
                    'proveedor' => $Prod["proveedor"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion'=> $Prod["descripcion"], 
                   'fechaAlta' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
                    'idProducto' => $Prod["idProducto"]
                ));
    }
    public function insertarProducto($Prod){
        $today=getDate();
        return $this -> query("
            INSERT INTO producto (
                    nombre,
                    marca,
                    stock,
                    stockMinimo,
                    idCategoria,
                    proveedor,
                    precioVentaUnitario,
                    descripcion, 
                    fechaAlta,
                    eliminado)
            VALUES (:nombre,
                    :marca,
                    :stock,
                    :stockMinimo,
                    :idCategoria,
                    :proveedor,
                    :precioVentaUnitario,
                    :descripcion, 
                    :fechaAlta,
                    0)",
            array('nombre' => $Prod["nombre"],
                    'marca' => $Prod["marca"],
                    'stock' => $Prod["stock"],
                    'stockMinimo' => $Prod["stockMinimo"],
                    'idCategoria' => $Prod["categoria"],
                    'proveedor' => $Prod["proveedor"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion' => $Prod["descripcion"], 
                    'fechaAlta' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds']
                ));    
    }
    public function deleteProducto($idProd){
        return $this -> query(
                "UPDATE producto p 
                SET p.eliminado =1 
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array('idProd' => $idProd));
    }
    
    public function listarProductosStockMinimo(){
        return $this -> queryTodasLasFilas(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.proveedor, p.precioVentaUnitario, p.descripcion, p.fechaAlta, p.idProducto, c.idCategoria
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
