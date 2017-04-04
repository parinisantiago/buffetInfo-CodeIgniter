
<?php
include_once("Model.php");
class MenuModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function totalProd($id){

        $this->db->select('COUNT(*) as total');
        $this->db->from('menuProducto mp');
        $this->db->join('menu m');
        $this->db->where('m.fecha',$id);
        $this->db->where('m.eliminado', 0);
        $total = $this->db->get()->result();
        return $total[0];
        /* return $this->queryPreparadaSQL('
             SELECT COUNT(*) AS total
             FROM menuProducto mp
             INNER JOIN menu m
             WHERE m.fecha = : id
             AND m.eliminado = 0
             ', array("id" => $id));*/
    }

    public function getMenuToday2(){
        $today=getDate();
        $this->db->select('m.idMenu, m.foto, p.nombre, p.precioVentaUnitario, p.descripcion');
        $this->db->from('menu m');
        $this->db->join('menuProducto mp', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('m.fecha',$today['year']."-".$today['mon']."-".$today['mday']);
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->where('m.habilitado', 0);
        return $this->db->get()->result();
       /* return $this->queryTodasLasFilas('
            SELECT m.idMenu, m.foto, p.nombre, p.precioVentaUnitario, p.descripcion
            FROM menu m
            INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
            INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE m.fecha = :fecha and m.eliminado = 0 and p.eliminado =0 and m.habilitado = 0',
            array('fecha' => $today['year']."-".$today['mon']."-".$today['mday']));*/
    }

    public function getMenuByDia2($limit, $offset, $fecha){
        $this->db->select('*');
        $this->db->from('menu m');
        $this->db->join('menuProducto mp', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('m.fecha', $fecha);
        $this->db->where('m.eliminado', 0);
        $this->db->where('m.habilitado', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();
/*
        return $this->queryOFFSETDIA('
            SELECT *  
            FROM menu m
            INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
            INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE m.fecha = :fecha
            AND m.eliminado = 0
            AND m.habilitado = 0
            LIMIT :limit
            OFFSET :offset
        ', $limit, $offset, $fecha);*/
    }

    public function getMenuByDia($limit, $offset, $fecha){

        $this->db->select('*');
        $this->db->from('menu m');
        $this->db->join('menuProducto mp', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('m.fecha', $fecha);
        $this->db->where('m.eliminado', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $menu = $this->db->get()->result();
        if(empty($menu)) return false;
        else return $menu;
        /*
        return $this->queryOFFSETDIA('
            SELECT *  
            FROM menu m
            INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
            INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE m.fecha = :fecha
            AND m.eliminado = 0
            LIMIT :limit
            OFFSET :offset
        ', $limit, $offset, $fecha);*/
    }
    public function getMenuToday(){
        $today=getDate();
        $this->db->select('m.idMenu, m.foto, p.nombre, p.precioVentaUnitario, p.descripcion');
        $this->db->from('menu m');
        $this->db->join('menuProducto mp', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('m.fecha', $today['year']."-".$today['mon']."-".$today['mday']);
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        return $this->db->get()->result();

        /*
                return $this->queryTodasLasFilas('
                    SELECT m.idMenu, m.foto, p.nombre, p.precioVentaUnitario, p.descripcion
                    FROM menu m
                    INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
                    INNER JOIN producto p ON (mp.idProducto = p.idProducto)
                    WHERE m.fecha = :fecha and m.eliminado = 0 and p.eliminado =0 ',
                    array('fecha' => $today['year']."-".$today['mon']."-".$today['mday'])); */
    }
    
    public function getProdNotInMenu($idMenu)
    {
        $subquery = $this->db->select('idProducto')->from('menuProducto')->where('idMenu',$idMenu)->get()->result_id->queryString;
        $this->db->select('*');
        $this->db->from('producto');
        $this->db->where('eliminado', 0);
        $this->db->where('eliminado', 0);
        $this->db->where('stock >', 0);
        $this->db->where("idProducto NOT IN ($subquery)", NULL, FALSE);

        return $this->db->get()->result();

  /*      return $this -> queryTodasLasFilas('
        SELECT * 
        FROM producto 
        WHERE eliminado = 0
        AND stock > 0
        AND idProducto NOT IN (SELECT idProducto FROM menuProducto WHERE idMenu = :idMenu )',
        array('idMenu'=> $idMenu));*/
    }

    public function getMenuDia($fecha)
    {
        $this->db->select('*');
        $this->db->from('menu');
        $this->db->where('fecha', $fecha);
        $this->db->where('eliminado', 0);
        return $this->db->get()->result();

        /*   return $this->queryPreparadaSQL('
               SELECT *
               FROM menu
               WHERE fecha = :fecha
               AND eliminado = 0',
               array("fecha" => $fecha));*/
    }

    public function totalMenu(){
        $this->db->select('COUNT(*) AS total');
        $this->db->from('menu m');
        $this->db->join('producto p', 'm.idProducto=p.idProducto');
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->order_by('m.idMenu', 'ASC');
        $total = $this->db->get()->result();
        return $total[0];
        /*return $this->queryPreparadaSQL('
            SELECT COUNT(*) AS total  
            FROM menu m INNER JOIN producto p ON (m.idProducto=p.idProducto)
            WHERE m.eliminado = 0 and p.eliminado=0
            ORDER BY m.idMenu', array());*/
    }

    public function idMenu($id)
    {
        $this->db->select('*');
        $this->db->from('menu');
        $this->db->where('idMenu', $id);
        $this->db->where('eliminado', 0);
        return $this->db->get()->result();

        /*  return $this->queryPreparadaSQL('
              SELECT *
              FROM menu
              WHERE idMenu = :id
              AND eliminado = 0',
              array("id" => $id));*/
    }

    public function searchIdMenu($idMenu){
        $this->db->select('m.idMenu,m.idProducto,m.fecha,m.foto,m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado');
        $this->db->from('menu m');
        $this->db->join('producto p', 'm.idProducto=p.idProducto');
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->where('m.idMenu', $idMenu);
        return $this->db->get()->result();


        /*    return $this ->queryPreparadaSQL("
                SELECT m.idMenu,m.idProducto,m.fecha,m.foto,m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
                FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto)
                WHERE  m.eliminado=0 and p.eliminado=0 and m.idMenu = :idMenu" , array('idMenu' => $idMenu));*/
    }

    public function getAllMenu($limit, $offset){
        $this->db->select('m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado');
        $this->db->from('menu m');
        $this->db->join('producto p', 'm.idProducto=p.idProducto');
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->order_by('p.nombre', 'ASC');
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

        /* return $this -> queryOFFSET('
             SELECT m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
             FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto)
             WHERE m.eliminado = 0 and p.eliminado=0
             ORDER BY p.nombre
             LIMIT :limit
             OFFSET :offset', $limit, $offset
             ); */
    }

    public function getAllMenuDia($limit, $offset, $fecha){
        $this->db->select('m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado');
        $this->db->from('menu m');
        $this->db->join('producto p', 'm.idProducto=p.idProducto');
        $this->db->where('m.eliminado', 0);
        $this->db->where('p.eliminado', 0);
        $this->db->where('m.fecha', $fecha);
        $this->db->order_by('p.nombre', 'ASC');
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

        /*   return $this -> queryOFFSETDIA('
               SELECT m.idMenu,m.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado
               FROM menu m INNER JOIN producto p ON(m.idProducto=p.idProducto)
               WHERE m.eliminado = 0 and p.eliminado=0 and m.fecha = :fecha
               ORDER BY p.nombre
               LIMIT :limit
               OFFSET :offset', $limit, $offset, $fecha
           );*/
    }
    
    public function actualizarMenu($menu){

        $data=array('idProducto' => $menu["idProducto"],
                    'fecha' => $menu["fecha"],
                    'foto' => $menu["foto"]);
        $this->db->where('idMenu',$menu['idMenu']);
        $this->db->update('menu',$data);
        /*return $this -> query("
            UPDATE menu 
            SET idProducto= :idProducto,
                fecha= :fecha,
                foto= :foto
            WHERE idMenu = :idMenu ",
            array('idProducto' => $menu["idProducto"],
                'fecha' => $menu["fecha"],
                'foto' => $menu["foto"],
                'idMenu' => $menu["idMenu"])
        );*/
    }

    public function insertarProd($idMenu, $idProd){

        $data = array(  "idMenu" => $idMenu,
                        "idProducto" => $idProd);

        $this->db->insert('menuProducto', $data);

        /*return $this->query(
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
        );*/

    }


    public function getMenu($limit, $offset, $idMenu){

        $this->db->select('m.idMenu,mp.idProducto,m.fecha,m.foto, m.eliminado,p.nombre,p.stock,p.stockMinimo,p.precioVentaUnitario,p.descripcion,p.eliminado');
        $this->db->from('menuProducto mp');
        $this->db->join('menu m', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('mp.idMenu', $idMenu);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();
       /* $this->stmnt = $this->db->prepare("
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
        return $this->stmnt ->fetchAll();*/
    }


     public function insertarMenu($fecha, $foto){

         $data= array(
             'fecha' =>$fecha,
             'foto' => $foto,
             'eliminado' => 0,
             'habilitado' => 0
         );
         $this->db->insert('menu', $data);
         return $this->db->insert_id();
   /*     return $this -> lastId("
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
        );*/
    }

    public function insertarMenu2($fecha, $foto, $hab){
        $data= array(
            'fecha' =>$fecha,
            'foto' => $foto,
            'eliminado' => 0,
            'habilitado' => $hab
        );

       $this->db->insert('menu', $data);
        /*return $this -> lastId("
            INSERT INTO menu(
                fecha,
                foto,
                eliminado,
                habilitado)
            VALUES (
                    :fecha,
                    :foto,
                    0,
                    :hab)",
            array(
                'fecha' =>$fecha,
                'foto' => $foto,
                'hab' => $hab
            )
        );*/
    }

    public function eliminarMenu($idMenu){

        $data=array('m.eliminado' => 1);
        $this->db->where('m.idMenu', $idMenu);
        $this->db->update('menu m', $data);

     /*   return $this -> query(
            "UPDATE menu m 
            SET m.eliminado =1 
            WHERE m.idMenu = :idMenu" , array('idMenu' => $idMenu)); */
    }
    public function getMinimoMenu($fecha){
        $this->db->select('MIN(p.stock) as minimo');
        $this->db->from('menu m');
        $this->db->join('menuProducto mp', 'mp.idMenu = m.idMenu');
        $this->db->join('producto p', 'mp.idProducto = p.idProducto');
        $this->db->where('m.fecha',$fecha);
        $this->db->where('m.eliminado',0);
        return $this->db->get()->insert();

    /*    return $this -> queryPreparadaSQL("
            SELECT MIN(p.stock) as minimo
            FROM menu m INNER JOIN menuProducto mp ON (mp.idMenu = m.idMenu)
                        INNER JOIN producto p ON (mp.idProducto = p.idProducto)
            WHERE m.fecha = :fecha
            AND m.eliminado = 0
            ", array('fecha' => $fecha));
       */
    }
}
?>

