
<?php
include_once("Model.php");
class MenuModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getMenuByDia($limit, $offset, $fecha){
        return $this->queryOFFSETDIA('
            SELECT *  
            FROM menu m
            INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
            INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE m.fecha = :fecha
            AND m.eliminado = 0
            LIMIT :limit
            OFFSET :offset
        ', $limit, $offset, $fecha);
    }

    public function getProdNotInMenu($idMenu)
    {
        return $this -> queryTodasLasFilas('
        SELECT * 
        FROM producto 
        WHERE eliminado = 0
        AND stock > 0
        AND idProducto NOT IN (SELECT idProducto FROM menuProducto WHERE idMenu = :idMenu )',
        array('idMenu'=> $idMenu));
    }

    public function getMenuDia($fecha)
    {
        return $this->queryPreparadaSQL('
            SELECT *
            FROM menu
            WHERE fecha = :fecha
            AND eliminado = 0',
            array("fecha" => $fecha));
    }

    public function totalMenu(){
        return $this->queryPreparadaSQL('
            SELECT COUNT(*) AS total  
            FROM menu m INNER JOIN producto p ON (m.idProducto=p.idProducto)
            WHERE m.eliminado = 0 and p.eliminado=0
            ORDER BY m.idMenu', array());
    }
    public function searchIdMenu($idMenu){
        return $this ->queryPreparadaSQL("
            SELECT m.idMenu,m.idProducto,m.fecha,m.foto,m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
            FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto) 
            WHERE  m.eliminado=0 and p.eliminado=0 and m.idMenu = :idMenu" , array('idMenu' => $idMenu));
    }
    public function getAllMenu($limit, $offset){
        return $this -> queryOFFSET('
            SELECT m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
            FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto) 
            WHERE m.eliminado = 0 and p.eliminado=0
            ORDER BY p.nombre
            LIMIT :limit  
            OFFSET :offset', $limit, $offset
            );
    }

    public function getAllMenuDia($limit, $offset, $fecha){
        return $this -> queryOFFSETDIA('
            SELECT m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
            FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto) 
            WHERE m.eliminado = 0 and p.eliminado=0 and m.fecha = :fecha
            ORDER BY p.nombre
            LIMIT :limit  
            OFFSET :offset', $limit, $offset, $fecha
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

    public function insertarProd($idMenu, $idProd){

        return $this->query(
            "INSERT INTO menuProducto(
                idMenu,
                idProducto)
            VALUES (
                :idMenu,
                :idProd)",
            array(
                "idMenu" => $idMenu,
                "idProd" => $idProd
            )
        );

    }


    public function getMenu($limit, $offset, $idMenu){
        $this->stmnt = $this->db->prepare("
            SELECT m.idMenu,mp.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
            FROM menuProducto mp
            INNER JOIN menu m ON (mp.idMenu = m.idMenu)
            INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE mp.idMenu = :idMenu
            LIMIT :limit
            OFFSET :offset
            ");
        $this->stmnt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $this->stmnt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $this->stmnt->bindValue(':idMenu', $idMenu);
        $this->stmnt->execute();
        return $this->stmnt ->fetchAll();
    }


     public function insertarMenu($fecha, $foto){

        return $this -> lastId("
            INSERT INTO menu(
                fecha,
                foto,
                eliminado,
                habilitado)
            VALUES (
                    :fecha,
                    :foto,
                    0,
                    0)",
            array(
                'fecha' =>$fecha,
                'foto' => $foto
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

