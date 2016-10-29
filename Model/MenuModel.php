
<?php
include_once("Model.php");
class MenuModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    /*public function totalMenu(){
        return $this->queryPreparadaSQL('SELECT COUNT(*) AS total  FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0
            ORDER BY c.idCompra', array());
    }
    public function getAllCompras($limit, $offset){
        return $this -> queryOFFSET('
            SELECT c.idCompra, p.nombre, p.marca, c.cantidad, c.precioUnitario, pr.proveedor, c.fecha, c.fotoFactura
            FROM compra c INNER JOIN proveedor pr ON(c.idProveedor=pr.idProveedor)INNER JOIN producto p ON (p.idProducto=c.idProducto) 
            WHERE c.eliminado = 0 and p.eliminado=0
            ORDER BY c.idCompra
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
        );
    }*/
    public function searchIdMenu($idMenu){
        return $this ->queryPreparadaSQL("
            SELECT m.idMenu,m.idProducto,m.fecha,m.foto,m.eliminado,p.nombre,p.stock,p.stockMinimo,p.PrecioVentaUnitario,p.descripcion,p.eliminado
            FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto) 
            WHERE  m.eliminado=0 and p.eliminado=0 and m.idMenu = :idMenu" , array('idMenu' => $idMenu));
    }
    public function getAllMenu($limit, $offset){
        return $this -> queryOFFSET('
            SELECT m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.PrecioVentaUnitario,p.descripcion,p.eliminado
            FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto) 
            WHERE m.eliminado = 0 and p.eliminado=0
            ORDER BY p.nombre
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
            );
    }
    
    public function actualizarMenu($menu){
        return $this -> query("
            UPDATE menu 
            SET idProducto= :idProducto,
                fecha= :fecha,
                foto= :foto
            WHERE idMenu = :idMenu ",
            array('idProducto' => $menu["idProducto"],
                'fecha' => $menu["fecha"],
                'foto' => $menu["foto"],
                'idMenu' => $menu["idMenu"])
        );
    }
    
     public function insertarMenu($menu){
        return $this -> query("
            INSERT INTO menu(
                idProducto,
                fecha,
                foto,
                eliminado,
                habilitado)
            VALUES (:idProducto,
                    :fecha,
                    :foto,
                    0,
                    0)",
            array('idProducto' => $menu["producto"],
                'fecha' =>$menu["fecha"],
                'foto' =>$menu["foto"]
                )
        );
    }
     
    public function eliminarMenu($idMenu){
        return $this -> query(
            "UPDATE menu m 
            SET m.eliminado =1 
            WHERE m.idMenu = :idMenu" , array('idMenu' => $idMenu));
    }
}
?>

