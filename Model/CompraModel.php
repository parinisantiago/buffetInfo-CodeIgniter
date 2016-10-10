
<?php
include_once("Model.php");
class CompraModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    public function totalCompras(){
        return $this->queryPreparadaSQL('SELECT COUNT(*) AS total FROM compra', array());
    }
    public function getAllCompras($limit, $offset){
        return $this -> queryOFFSET('
            SELECT c.idCompra, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha
            FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0
            ORDER BY c.idCompra
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );
    }
     public function siguienteId(){
        return $this -> queryPreparadaSQL('SELECT MAX(idCompra) as idCompra FROM compra',array());
    }
     public function searchIdCompra($idComp){
        return $this ->queryPreparadaSQL("
            SELECT c.idCompra,c.idProducto,c.idProveedor, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha
            FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0 and c.idCompra = :idComp" , array(idComp => $idComp));
    }
    
    public function getAllProveedor($limit, $offset){
        return $this -> queryOFFSET('
            SELECT pr.idProveedor, pr.proveedor, pr.proveedorCuit
            FROM proveedor pr
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
            );
    }
    
    public function actualizarCompra($comp){
        return $this -> query("
            UPDATE compra 
            SET idProducto= :idProducto,
                cantidad= :cantidad,
                precioUnitario= :precioUnitario,
                idProveedor= :idProveedor, 
                fecha = :fecha
            WHERE idCompra = :idCompra ",
            array('idCompra' => $comp["idCompra"],
                'idProducto' => $comp["idProducto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idProveedor' => $comp["idProveedor"],
                'fecha' => $comp["fecha"])
        );
    }
    
     public function insertarCompra($comp){
        $today=getDate();
        return $this -> query("
            INSERT INTO compra(
                idProducto,
                cantidad,
                precioUnitario,
                idProveedor,
                fecha)
            VALUES (:idProducto,
                    :cantidad,
                    :precioUnitario,
                    :idProveedor,
                    :fecha)",
            array('idProducto' => $comp["idProducto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idProveedor' => $comp["idProveedor"],
                'fecha' =>$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds']
                )
        );
    }
     
    public function eliminarCompra($idCompra){
        return $this -> query(
            "UPDATE compra c 
            SET c.eliminado =1 
            WHERE c.idCompra = :idCompra" , array('idCompra' => $idCompra));
    }
}
?>

