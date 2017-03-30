
<?php
include_once("Model.php");
class CompraModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    public function totalCompras(){

        $this->db->select('COUNT(*) AS total');
        $this->db->form('compra c');
        $this->db->join('proveedor pr', 'c.idProveedor=pr.idProveedor');
        $this->db->join('producto p', 'p.idProducto=c.idProducto');
        $this->db->where('c.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->order_by('c.idCompra');
        $total = $this->db->get()->result();
        return $total[0];

        /*r0eturn $this->queryPreparadaSQL('SELECT COUNT(*) AS total  FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto)
            WHERE c.eliminado = 0 and p.eliminado=0
            ORDER BY c.idCompra', array());*/
    }
    public function getAllCompras($limit, $offset){
        $this->db->select('c.idCompra, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha, c.fotoFactura');
        $this->db->form('compra c');
        $this->db->join('proveedor pr', 'c.idProveedor=pr.idProveedor');
        $this->db->join('producto p', 'p.idProducto=c.idProducto');
        $this->db->where('c.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->order_by('c.idCompra');
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();
       /* return $this -> queryOFFSET('
            SELECT c.idCompra, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha, c.fotoFactura
            FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0
            ORDER BY c.idCompra
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );*/
    }
     public function siguienteId(){
         $this->db->select('MAX(idCompra) as idCompra');
         $this->db->form('compra');
         $total = $this->db->get()->result();
         return $total[0];

         /*return $this -> queryPreparadaSQL('SELECT MAX(idCompra) as idCompra FROM compra',array());*/
    }
     public function searchIdCompra($idComp){
         $this->db->select('c.idCompra,c.idProducto,c.idProveedor, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha,c.fotoFactura');
         $this->db->form('compra c');
         $this->db->join('proveedor pr', 'c.idProveedor=pr.idProveedor');
         $this->db->join('producto p', 'p.idProducto=c.idProducto');
         $this->db->where('c.eliminado', 0);
         $this->db->where('p.eliminado', 0);
         $this->db->where('c.idCompra', $idComp);
         return $this->db->get()->result();
      /*   return $this ->queryPreparadaSQL("
            SELECT c.idCompra,c.idProducto,c.idProveedor, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha,c.fotoFactura
            FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0 and c.idCompra = :idComp" , array('idComp' => $idComp));*/
    }
    
    public function searchIdProveedor($idProv){
        $this->db->select('pr.idProveedor');
        $this->db->form('proveedor pr');
        $this->db->where('pr.idProveedor', $idProv);
        return $this->db->get()->result();

       /* return $this ->queryPreparadaSQL("
            SELECT pr.idProveedor
            FROM proveedor pr 
            WHERE  pr.idProveedor = :idProv" , array('idProv' => $idProv));*/
    }
    public function getAllProveedor($limit, $offset){

        $this->db->select('pr.idProveedor, pr.proveedor, pr.proveedorCuit');
        $this->db->form('proveedor pr');
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

      /*  return $this -> queryOFFSET('
            SELECT pr.idProveedor, pr.proveedor, pr.proveedorCuit
            FROM proveedor pr
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
            );*/
    }
    
    public function actualizarCompra($comp){

        $data = array(
            'idProducto' => $comp["producto"],
            'cantidad' => $comp["cantidad"],
            'precioUnitario' => $comp["precioUnitario"],
            'idProveedor' => $comp["proveedor"],
            'fecha' => $comp["fecha"],
            'fotoFactura' => $comp["fotoFactura"]
        );
        $this->db->where('idCompra', $comp["idCompra"]);
        $this->db->update('compra', $data);

 /*       return $this -> query("
            UPDATE compra 
            SET idProducto= :idProducto,
                cantidad= :cantidad,
                precioUnitario= :precioUnitario,
                idProveedor= :idProveedor, 
                fecha = :fecha,
                fotoFactura = :fotoFactura
            WHERE idCompra = :idCompra ",
            array('idCompra' => $comp["idCompra"],
                'idProducto' => $comp["producto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idProveedor' => $comp["proveedor"],
                'fecha' => $comp["fecha"],
                'fotoFactura' => $comp["fotoFactura"])
        );*/
    }
    
     public function insertarCompra($comp){
        $today=getDate();
        $data = array(
            'idProducto' => $comp["producto"],
            'cantidad' => $comp["cantidad"],
            'precioUnitario' => $comp["precioUnitario"],
            'idProveedor' => $comp["proveedor"],
            'fecha' =>$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
            'eliminado' => 0,
            'fotoFactura' => $comp["fotoFactura"]
        );
        $this->db->insert('compra', $data);
      /*  return $this -> query("
            INSERT INTO compra(
                idProducto,
                cantidad,
                precioUnitario,
                idProveedor,
                fecha,
                eliminado,
                fotoFactura)
            VALUES (:idProducto,
                    :cantidad,
                    :precioUnitario,
                    :idProveedor,
                    :fecha,
                    0,
                    :fotoFactura)",
            array('idProducto' => $comp["producto"],
                'cantidad' => $comp["cantidad"],
                'precioUnitario' => $comp["precioUnitario"],
                'idProveedor' => $comp["proveedor"],
                'fecha' =>$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":".$today['seconds'],
                'fotoFactura' => $comp["fotoFactura"]
                )
        );*/
    }

    public function eliminarCompra($idCompra){

        $data = array('eliminado' => 1);
        $this->db->where('idCompra',$idCompra);
        $this->db->update('compra', $data);
       /* return $this -> query(
            "UPDATE compra c 
            SET c.eliminado =1 
            WHERE c.idCompra = :idCompra" , array('idCompra' => $idCompra));*/
    }
}
?>

