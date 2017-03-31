<?php
include_once("Model.php");
class VentaModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    /******OJO YA LISTA LOS NO ELIMINADOS, SACARLO LA PREGUNTA DE TWIG*/

    public function totalVenta(){

        $this->db->select('COUNT(*) AS total');
        $this->db->from('producto p');
        $this->db->join('ingresoDetalle i', 'p.idProducto=i.idProducto');
        $this->db->where('i.eliminado', 0);
        $total = $this->db->get()->result();
        return $total;
       /* return $this->queryPreparadaSQL('
            SELECT COUNT(*) AS total 
            FROM producto p 
            INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto) 
            WHERE i.eliminado = 0 '
            ,array());*/
    }

    public function getVentaById($id){

        $this->db->select('*');
        $this->db->from('ingresoDetalle i');
        $this->db->where('i.idIngresoDetalle', $id);
        return $this->db->get()->result();
    /*    return $this->queryPreparadaSQL('
            SELECT * 
            FROM ingresoDetalle i 
            WHERE i.idIngresoDetalle = :id',
            array("id" => $id));*/
    }

    public function getAllVenta($limit, $offset){
        $this->db->select('i.idIngresoDetalle ,p.idProducto, p.nombre, p.marca,i.cantidad, i.precioUnitario,i.descripcion, i.fecha');
        $this->db->from('producto p');
        $this->db->join('ingresoDetalle i', 'p.idProducto=i.idProducto');
        $this->db->where('i.eliminado', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();
     /*   return $this -> queryOFFSET('
            SELECT i.idIngresoDetalle ,p.idProducto, p.nombre, p.marca,i.cantidad, i.precioUnitario,i.descripcion, i.fecha
            FROM producto p
            INNER JOIN ingresoDetalle i ON (p.idProducto=i.idProducto)
            WHERE i.eliminado = 0 
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );*/
    }

    public function getAlltotales(){
        $this->db->select('COUNT(i.idProducto) AS total,ROUND(SUM(i.precioUnitario * i.cantidad), 2) AS cant,SUM(i.cantidad) AS vent, i.idProducto, p.nombre ');
        $this->db->from('ingresoDetalle i');
        $this->db->join('producto p', 'i.idProducto = p.idProducto');
        $this->db->group_by('p.idProducto');
        return $this->db->get()->result();
        /*return $this -> queryTodasLasFilas('
            SELECT COUNT(i.idProducto) AS total,ROUND(SUM(i.precioUnitario * i.cantidad), 2) AS cant,SUM(i.cantidad) AS vent, i.idProducto, p.nombre 
            FROM ingresoDetalle i 
            INNER JOIN producto p 
            ON (i.idProducto = p.idProducto) 
            GROUP BY p.idProducto',array());*/
    }
    
    public function insertarVenta($vent){
/*actualiza solo la tabla de vetnas, tambien hay qye hacer la actualizcion en 
 * la tabla de productos*/
        $today=getDate();
        $data= array(
            'idProducto' => $vent["idProducto"],
            'cantidad' => $vent["cant"],
            'precioUnitario' => $vent["precioVentaUnitario"],
            'descripcion' =>  '',
            'fecha' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
            'eliminado' => 0
        );
        $this->db->insert('ingresoDetalle', $data);

/*        return $this -> query("
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
        );*/
    }

    public function insertarVentaId($vent)
    {
        $today=getDate();
        $data= array(
            'idProducto' => $vent["idProducto"],
            'cantidad' => $vent["cant"],
            'precioUnitario' => $vent["precioVentaUnitario"],
            'descripcion' =>  '',
            'fecha' => $today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
            'eliminado' => 0
        );
        $this->db->insert('ingresoDetalle', $data);
        return $this->db->insert_id();
      /*  return $this -> lastId("
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
        );*/
    }
    public function actualizarVenta($cantidad, $id){

        $data = array(
            'cantidad' => $cantidad
        );

        $this->db->where('idIngresoDetalle', $id);
        $this->db->update('ingresoDetalle', $data);
       /* return $this -> query("
            UPDATE ingresoDetalle 
            SET cantidad = :cantidad
            WHERE idIngresoDetalle = :id ",
            array('cantidad' => $cantidad,
                'id' => $id)
                );*/
    }
    public function eliminarVenta($idVenta){
        $data = array(
            'eliminado' => 1
        );

        $this->db->where('idIngresoDetalle', $idVenta);
        $this->db->update('ingresoDetalle', $data);

 /*       return $this -> query(
            "UPDATE ingresoDetalle id
            SET id.eliminado =1 
            WHERE id.idIngresoDetalle = :idIngresoDetalle" , array('idIngresoDetalle' => $idVenta));*/
    }



}
?>
