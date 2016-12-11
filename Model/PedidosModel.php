<?php

include_once("Model.php");

class PedidosModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function insertarPedido($idUsuario){

        $today = date("Y-m-d H:i:s");
        $fecha = date('Y-m-d');
        $observaciones = "sin observaciones";

        return $this->lastId(
          "INSERT INTO pedido(idEstado, fechaAlta,idUsuario, observaciones, fechaBusqueda)
           VALUES ('pendiente', :fechaAlta, :idUsuario, :observaciones, :fechaBusqueda)",
            array('fechaAlta'=>$today, 'idUsuario'=>$idUsuario, 'observaciones'=>$observaciones, 'fechaBusqueda'=>$fecha)
        );
    }

    public function getPedido($id)
    {
        return $this->queryPreparadaSQL(
            "SELECT TIME_TO_SEC(TIMEDIFF(NOW(), fechaAlta)) AS intervalo, idPedido, idEstado, idUsuario
            FROM pedido
            WHERE idPedido = :id",
            array("id" => $id));
    }

    public function insertPedidoDetalle($idPedido, $idProducto, $cantidad)
    {
        return $this->lastId("
          INSERT INTO pedidoDetalle(idPedido, idProducto, cantidad) 
          VALUES (:idPedido, :idProducto, :cantidad)",
          array("idPedido" => $idPedido, "idProducto" => $idProducto, "cantidad"=>$cantidad));
    }

    public function getDetalle($id)
    {
        return $this->queryTodasLasFilas(
          " SELECT * 
            FROM pedido pe
            INNER JOIN pedidoDetalle d
            ON (pe.idPedido = d.idPedido)
            INNER JOIN producto p
            ON (d.idProducto = p.idProducto)
            WHERE pe.idPedido = :id",
            array("id" => $id)
        );
    }

    public function pedidosUsuarios($idUsuario, $limit, $offset)
    {
        $this->stmnt = $this->db->prepare("SELECT TIME_TO_SEC(TIMEDIFF(NOW(), pe.fechaAlta)) AS intervalo, pe.idPedido, pe.idEstado, pe.observaciones, pe.fechaAlta,
            ROUND(SUM(p.precioVentaUnitario * d.cantidad), 2) AS total
            FROM pedido pe
            INNER JOIN pedidoDetalle d
            ON (pe.idPedido = d.idPedido)
            INNER JOIN producto p
            ON (d.idProducto = p.idProducto)
            WHERE pe.idUsuario = :idUsuario
            GROUP BY idPedido
            LIMIT :limit
            OFFSET :offset
            ");
        $this->stmnt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $this->stmnt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $this->stmnt->bindValue(':idUsuario', $idUsuario);
        $this->stmnt->execute();
        return $this->stmnt ->fetchAll();
    }

    public function  totalPedidos($idUsuario)
    {
        return $this->queryPreparadaSQL("SELECT COUNT(*) AS total FROM pedido WHERE idUsuario = :idUsuario", array("idUsuario" => $idUsuario));
    }

    public function actualizarEstado($idEstado, $idPedido)
    {
        return $this->query(
            "UPDATE pedido
             SET idEstado = :idEstado
             WHERE idPedido = :idPedido",
            array("idEstado" => $idEstado, "idPedido" => $idPedido));
    }

    public function actualizarComentario($idEstado, $idPedido)
    {
        return $this->query(
            "UPDATE pedido
             SET observaciones = :idEstado
             WHERE idPedido = :idPedido",
            array("idEstado" => $idEstado, "idPedido" => $idPedido));
    }

    public function  totalPedidosRango($idUsuario,$inicio,$fin)
    {
        return $this->queryPreparadaSQL("SELECT COUNT(*) AS total FROM pedido WHERE idUsuario = :idUsuario AND fechaBusqueda BETWEEN :inicio AND :fin", array("idUsuario" => $idUsuario, "inicio"=>$inicio, "fin"=>$fin));
    }

    public function pedidosUsuariosRango($idUsuario, $limit, $offset, $inicio, $fin)
    {
        $this->stmnt = $this->db->prepare("
            SELECT TIME_TO_SEC(TIMEDIFF(NOW(), pe.fechaAlta)) AS intervalo, pe.idPedido, pe.idEstado, pe.observaciones, pe.fechaAlta,
            ROUND(SUM(p.precioVentaUnitario * d.cantidad), 2) AS total
            FROM pedido pe
            INNER JOIN pedidoDetalle d
            ON (pe.idPedido = d.idPedido)
            INNER JOIN producto p
            ON (d.idProducto = p.idProducto)
            WHERE pe.idUsuario = :idUsuario
            AND pe.fechaBusqueda
            BETWEEN :inicio
            AND :fin
            GROUP BY idPedido
            LIMIT :limit
            OFFSET :offset
            ");
        $this->stmnt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $this->stmnt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $this->stmnt->bindValue(':idUsuario', $idUsuario);
        $this->stmnt->bindValue(':inicio', $inicio);
        $this->stmnt->bindValue(':fin', $fin);
        $this->stmnt->execute();
        return $this->stmnt ->fetchAll();
    }

    public function getPedidosPendientes($limit, $offset)
    {
        return $this->queryOFFSET(
            "SELECT pedido.idPedido, idEstado, fechaAlta, observaciones, usuario, ubicacion.nombre, 
            ROUND(SUM(producto.precioVentaUnitario * pedidoDetalle.cantidad), 2) AS total
            FROM pedido
            INNER JOIN usuario
            ON (pedido.idUsuario = usuario.idUsuario)
            INNER JOIN ubicacion
            ON (usuario.idUbicacion = ubicacion.idUbicacion)
            INNER JOIN pedidoDetalle
            ON (pedido.idPedido = pedidoDetalle.idPedido)
            INNER JOIN producto
            ON (pedidoDetalle.idProducto = producto.idProducto)
            WHERE idEstado = 'pendiente'
            GROUP BY pedido.idPedido
            LIMIT :limit
            OFFSET :offset",$limit, $offset);
    }
public function totalPedidosPendientes()
{
return $this->queryPreparadaSQL(
"SELECT COUNT(*) as total
            FROM pedido
            INNER JOIN usuario
            ON (pedido.idUsuario = usuario.idUsuario)
            INNER JOIN ubicacion
            ON (usuario.idUbicacion = ubicacion.idUbicacion)
            
            WHERE idEstado = 'pendiente'",array());
}
}