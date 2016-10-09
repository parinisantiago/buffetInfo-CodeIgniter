
<?php
include_once("Model.php");
class CompraModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    public function totalCompras(){
        return $this->queryPreparadaSQL('SELECT COUNT(*) AS total FROM egresoDetalle', array());
    }
    public function getAllCompras($limit, $offset){
        return $this -> queryOFFSET('
            SELECT p.nombre, p.marca, e.cantidad, e.precioUnitario, c.proveedor, c.fecha
            FROM compra c INNER JOIN egresoDetalle e ON(c.idCompra=e.idCompra)INNER JOIN producto p ON (p.idProducto=e.idProducto) 
            WHERE p.eliminado = 0 and c.eliminado=0 and e.eliminado=0
            ORDER BY c.idCompra
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );
    }
    
    public function insertarCompra($comp){
/*una compra incluye varios egresos detalle, poner un boton para agregar mas egresos*/
        $today=getDate();
        return $this -> query("
            INSERT INTO egresoDetalle(
                proveedor,
                proveedorCuit,
                fecha)
            VALUES (:proveedor,
                    :proveedorCuit,
                    :fecha)",
            array('proveedor' => $comp["proveedor"],
                'proveedorCuit' => $comp["proveedorCuit"],
                'precioUnitario' => $comp["precioUnitario"],
                'fecha' =>$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds']
                )
        );
    }
    public function insertarEgresoDetalle($comp){
        $today=getDate();
        return $this -> query("
            INSERT INTO egresoDetalle(
                idCompra,
                idProducto,
                cantidad,
                precioUnitario,
                idTipo)
            VALUES (:idCompra,
                    :idProducto,
                    :cantidad,
                    :precioUnitario,
                    :idTipo,
                    )",
            array('idCompra' => $comp["idCompra"],
                'idProducto' => $comp["idProducto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idTipo' => $comp["idTipo"],)
        );
    }
    
    public function actualizarCompra($comp){
        return $this -> query("
            UPDATE egresoDetalle 
            SET idCompra = :idCompra,
                idProducto= :idProducto,
                cantidad= :cantidad,
                precioUnitario= :precioUnitario,
                idTipo= idTipo: 
            WHERE idEgresoDetalle = idEgresoDetalle ",
            array('idCompra' => $comp["idCompra"],
                'idProducto' => $comp["idProducto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idTipo' => $comp["idTipo"],)
        );
    }
    public function eliminarEgresoDetalle($idEgresoDetalle){
        return $this -> query(
            "UPDATE egresoDetalle ed 
            SET ed.eliminado =1 
            WHERE ed.idEgresoDetalle = :idEgresoDetalle" , array('idEgresoDetalle' => $idEgresoDetalle));
    }
}
?>

