<?php
include_once("Model.php");
class ProductosModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getAllProducto($limit, $offset){
        return $this -> queryOFFSET(
                'SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria) 
                WHERE p.eliminado = 0 
                LIMIT :limit  
                OFFSET :offset', $limit, $offset);
    }

    public function searchIdProducto($idProd){
        return $this ->queryPreparadaSQL(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, p.precioVentaUnitario, p.descripcion, p.idProducto,c.nombre as categoria
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array('idProd' => $idProd));
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
                    precioVentaUnitario = :precioVentaUnitario,
                    descripcion = :descripcion 
                WHERE idProducto= :idProducto",
                array('nombre' => $Prod["nombre"],
                    'marca' => $Prod["marca"],
                    'stock' => $Prod["stock"],
                    'stockMinimo' => $Prod["stockMinimo"],
                    'categoria' => $Prod["categoria"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion'=> $Prod["descripcion"], 
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
                    precioVentaUnitario,
                    descripcion, 
                    eliminado)
            VALUES (:nombre,
                    :marca,
                    :stock,
                    :stockMinimo,
                    :idCategoria,
                    :precioVentaUnitario,
                    :descripcion, 
                    0)",
            array('nombre' => $Prod["nombre"],
                    'marca' => $Prod["marca"],
                    'stock' => $Prod["stock"],
                    'stockMinimo' => $Prod["stockMinimo"],
                    'idCategoria' => $Prod["categoria"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion' => $Prod["descripcion"]
                ));    
    }
    public function deleteProducto($idProd){
        return $this -> query(
                "UPDATE producto p 
                SET p.eliminado =1 
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array('idProd' => $idProd));
    }
    
    public function listarProductosStockMinimo($limit, $offset){
        return $this -> queryOFFSET(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto, c.idCategoria
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.stock <= p.stockMinimo LIMIT :limit OFFSET :offset", $limit, $offset);
    }

    public function totalProductosStockMinimo(){
        return $this -> queryPreparadaSQL(
            "SELECT COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.stock <= p.stockMinimo",array());
    }

    public function listarProductosFaltantes($limit, $offset){
        return $this -> queryOFFSET(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                 WHERE p.eliminado = 0 and p.stock = 0 LIMIT :limit OFFSET :offset ", $limit, $offset);
    }

    public function totalProductosFaltantes(){
        return $this -> queryPreparadaSQL(
            "SELECT COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                 WHERE p.eliminado = 0 and p.stock = 0", array());
    }

    public function totalProductos(){
        return $this->queryPreparadaSQL('SELECT COUNT(*) AS total FROM producto', array());
    }
    
    public function actualizarCantProductos($id, $cant){
/*actualiza solo la tabla de productos, tambien hay qye hacer la actualizcion en la tabla de ventas*/
        var_dump($id); var_dump($cant);
        return $this -> query(
                "UPDATE producto p 
                SET p.stock = :cant 
                WHERE p.idProducto = :idProd",
        array('cant' => $cant,
              'idProd' =>$id));
    }

}

?>
