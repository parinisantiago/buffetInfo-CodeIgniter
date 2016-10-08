<?php
include_once("Model.php");
class VentaModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    
    public function getAllVentas($limit, $offset){
        return $this -> queryOFFSET(
                'SELECT p.nombre, p.marca, i.cantidad, i.precioUnitario, i.descripcion, i.fecha, it.Nombre
                FROM producto p INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto) INNER JOIN ingresoTipo it ON (i.idIngresoTipo=it.idIngresoTipo) 
                WHERE p.eliminado = 0 
                LIMIT :limit  
                OFFSET :offset', $limit, $offset);
    }
    public function actualizarVenta($vent){
        $today=getDate();
        return $this -> query("
                UPDATE producto 
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
                    'idTipo' => $vent["idTipo"],
                ));
    }
}
?>
