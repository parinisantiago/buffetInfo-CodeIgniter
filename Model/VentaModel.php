<?php
include_once("Model.php");
class VentaModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    /******OJO YA LISTA LOS NO ELIMINADOS, SACARLO LA PREGUNTA DE TWIG*/

    public function totalVenta(){
        return $this->queryPreparadaSQL('
            SELECT COUNT(*) AS total 
            FROM producto p 
            INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto) 
            WHERE i.eliminado = 0 '
            ,array());
    }

    public function getVentaById($id){
        return $this->queryPreparadaSQL('
            SELECT * 
            FROM ingresoDetalle i 
            WHERE i.idIngresoDetalle = :id',
            array("id" => $id));
    }

    public function getAllVenta($limit, $offset){
        return $this -> queryOFFSET('
            SELECT i.idIngresoDetalle ,p.idProducto, p.nombre, p.marca,i.cantidad, i.precioUnitario,i.descripcion, i.fecha
            FROM producto p
            INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto)
            WHERE i.eliminado = 0 
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );
    }
    
    public function insertarVenta($vent){
/*actualiza solo la tabla de vetnas, tambien hay qye hacer la actualizcion en 
 * la tabla de productos*/
        $today=getDate();
        return $this -> query("
            INSERT INTO ingresoDetalle(
                idProducto,
                cantidad,
                precioUnitario,
                descripcion,
                fecha,
                eliminado)
            VALUES (:idProducto,
                    :cantidad,
                    :precioUnitario,
                    :descripcion,
                    :fecha,
                    0)",
            array('idProducto' => $vent["idProducto"],
                'cantidad' => $vent["cant"],
                'precioUnitario' => $vent["precioVentaUnitario"],
                'descripcion' =>  '',
                'fecha' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds']
                )
        );
    }
    
    public function actualizarVenta($cantidad, $id){

        return $this -> query("
            UPDATE ingresoDetalle 
            SET cantidad = :cantidad
            WHERE idIngresoDetalle = :id ",
            array('cantidad' => $cantidad,
                'id' => $id)
                );
    }
    public function eliminarVenta($idVenta){
        return $this -> query(
            "UPDATE ingresoDetalle id
            SET id.eliminado =1 
            WHERE id.idIngresoDetalle = :idIngresoDetalle" , array('idIngresoDetalle' => $idVenta));
    }
}
?>
