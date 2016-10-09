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
            INNER JOIN ingresoTipo it ON (i.idTipoIngreso=it.idIngresoTipo) 
            WHERE p.eliminado = 0 '
            ,array());
    }


    public function getAllVenta($limit, $offset){
        return $this -> queryOFFSET('
            SELECT p.idProducto p.nombre, p.marca,i.cantidad, i.precioUnitario,i.descripcion, i.fecha, it.Nombre
            FROM producto p INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto) INNER JOIN ingresoTipo it ON (i.idTipoIngreso=it.idIngresoTipo) 
            WHERE p.eliminado = 0 
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
                idTipo)
            VALUES (:idProducto,
                    :cantidad,
                    :precioUnitario,
                    :descripcion,
                    :fecha,
                    :idTipo,
                    0)",
            array('idProducto' => $vent["idProducto"],
                'cantidad' => $vent["cantidad"],
                'precioUnitario' => $vent["precioUnitario"],
                'descripcion' => $vent["descripcion"],
                'fecha' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
                'idTipo' => $vent["idTipo"],)
        );
    }
    
    public function actualizarVenta($vent){
        return $this -> query("
            UPDATE ingresoDetalle 
            SET idProducto = :idProducto,
                cantidad = :cantidad,
                precioUnitario = :precioUnitario,
                descripcion = :descripcion,
                fecha = :fecha,
                idTipo = :idtipo
            WHERE idIngresoDetalle = idIngresoDetalle ",
            array('idProducto' => $vent["idProducto"],
                'cantidad' => $vent["cantidad"],
                'precioUnitario' => $vent["precioUnitario"],
                'descripcion' => $vent["descripcion"],
                'fecha' => $vent["fecha"],
                'idTipo' => $vent["idTipo"],)
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
