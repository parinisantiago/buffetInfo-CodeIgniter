<?php
include_once("Model.php");
class ProductosModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getAllProducto($limit, $offset){
        $this->db->select('p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.stock >', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

     /*   return $this -> queryOFFSET(
                'SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria) 
                WHERE p.eliminado = 0 AND p.stock > 0
                LIMIT :limit  
                OFFSET :offset', $limit, $offset);*/
    }

    public function getProductos(){
        $this->db->select('p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        return $this->db->get()->result();

        /*return $this->queryTodasLasFilas('SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria) WHERE p.eliminado = 0', array());*/
    }

    public function searchIdProducto($idProd){
        $this->db->select('p.nombre, p.marca, p.stock, p.stockMinimo, p.precioVentaUnitario, p.descripcion, p.idProducto,c.nombre as categoria, c.idCategoria');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.idProducto', $idProd);
        $prod = $this->db->get()->result();
        return $prod[0];
      /*  return $this ->queryPreparadaSQL(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, p.precioVentaUnitario, p.descripcion, p.idProducto,c.nombre as categoria, c.idCategoria
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array('idProd' => $idProd));*/
    }

    public function actualizarProducto($Prod){
        $today=getDate();
        $data = array(
            'nombre' => $Prod["nombre"],
            'marca' => $Prod["marca"],
            'stock' => $Prod["stock"],
            'stockMinimo' => $Prod["stockMinimo"],
            'idCategoria' => $Prod["idCategoria"],
            'precioVentaUnitario' => $Prod["precioVentaUnitario"],
            'descripcion'=> $Prod["descripcion"],
        );
        $this->db->where('idProducto', $Prod["idProducto"]);
        $this->db->update('producto', $data);

       /* return $this -> query("
                UPDATE producto 
                SET nombre = :nombre,
                    marca = :marca,
                    stock = :stock,
                    stockMinimo = :stockMinimo,
                    idCategoria = :idCategoria,
                    precioVentaUnitario = :precioVentaUnitario,
                    descripcion = :descripcion 
                WHERE idProducto= :idProducto",
                array('nombre' => $Prod["nombre"],
                    'marca' => $Prod["marca"],
                    'stock' => $Prod["stock"],
                    'stockMinimo' => $Prod["stockMinimo"],
                    'idCategoria' => $Prod["idCategoria"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion'=> $Prod["descripcion"], 
                    'idProducto' => $Prod["idProducto"]
                ));*/
    }

    public function insertarProducto($Prod){
        $today=getDate();
        $data= array(
            'nombre' => $Prod["nombre"],
            'marca' => $Prod["marca"],
            'stock' => $Prod["stock"],
            'stockMinimo' => $Prod["stockMinimo"],
            'idCategoria' => $Prod["idCategoria"],
            'precioVentaUnitario' => $Prod["precioVentaUnitario"],
            'descripcion' => $Prod["descripcion"],
            'eliminado' => 0
        );
        $this->db->insert('producto', $data);
     /*   return $this -> query("
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
                    'idCategoria' => $Prod["idCategoria"],
                    'precioVentaUnitario' => $Prod["precioVentaUnitario"],
                    'descripcion' => $Prod["descripcion"]
                ));    */
    }
    public function deleteProducto($idProd){
        $data = array('eliminado' => 1);
        $this->db->where('eliminado', 0);
        $this->db->where('idProducto', $idProd);
        $this->db->update('producto', $data);

   /*     return $this -> query(
                "UPDATE producto p 
                SET p.eliminado =1 
                WHERE p.eliminado = 0 and p.idProducto = :idProd" , array('idProd' => $idProd));*/
    }
    
    public function listarProductosStockMinimo($limit, $offset){
        $this->db->select('p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto, c.idCategoria');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.stock <= p.stockMinimo');
        $this->db->where('p.stock !=', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();


  /*      return $this -> queryOFFSET(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto, c.idCategoria
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE p.eliminado = 0 and p.stock <= p.stockMinimo and not p.stock = 0 LIMIT :limit OFFSET :offset", $limit, $offset);
   */ }

    public function totalProductosStockMinimo(){
        $this->db->select('COUNT(*) AS total');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado !=', 0);
        $this->db->where('p.stock <= p.stockMinimo');
        $this->db->where('p.stock !=', 0);
        $total = $this->db->get()->result();
        return $total[0];
 /*       return $this -> queryPreparadaSQL(
            "SELECT COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                WHERE NOT p.eliminado = 0 and p.stock <= p.stockMinimo and not p.stock = 0",array());*/
    }

    public function listarProductosFaltantes($limit, $offset){
        $this->db->select('p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.stock', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

       /* return $this -> queryOFFSET(
                "SELECT p.nombre, p.marca, p.stock, p.stockMinimo, c.nombre as categoria, p.precioVentaUnitario, p.descripcion, p.idProducto
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                 WHERE p.eliminado = 0 and p.stock = 0 LIMIT :limit OFFSET :offset ", $limit, $offset);*/
    }

    public function totalProductosFaltantes(){
        $this->db->select('COUNT(*) AS total');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.stock', 0);
        $total = $this->db->get()->result();
        return $total[0];

/*        return $this -> queryPreparadaSQL(
            "SELECT COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria )
                 WHERE p.eliminado = 0 and p.stock = 0", array());*/
    }

    public function totalProductos(){
        $this->db->select('COUNT(*) AS total');
        $this->db->from('producto p');
        $this->db->join('categoria c', 'p.idCategoria = c.idCategoria');
        $this->db->where('p.eliminado', 0);
        $this->db->where('p.stock >', 0);
        $total = $this->db->get()->result();
        return $total[0];
        /*  return $this->queryPreparadaSQL('SELECT COUNT(*) AS total   FROM producto p INNER JOIN categoria c ON (p.idCategoria = c.idCategoria)
                  WHERE p.eliminado = 0 AND p.stock > 0', array());*/
    }
    
    public function actualizarCantProductos($id, $cant){
/*actualiza solo la tabla de productos, tambien hay qye hacer la actualizcion en la tabla de ventas*/

        $data= array(
            'stock' => $cant
        );
        $this->db->where('idProducto', $id);
        $this->db->update('producto', $data);
       /*return $this -> query(
                "UPDATE producto p 
                SET p.stock = :cant 
                WHERE p.idProducto = :idProd",
        array('cant' => $cant,
              'idProd' =>$id));*/
    }


}

?>
