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

    public function insertPedidoDetalle($idPedido, $idProducto, $cantidad)
    {
        return $this->lastId("
          INSERT INTO pedidoDetalle(idPedido, idProducto, cantidad) 
          VALUES (:idPedido, :idProducto, :cantidad)",
          array("idPedido" => $idPedido, "idProducto" => $idProducto, "cantidad"=>$cantidad));
    }


    public function pedidosUsuarios($idUsuario, $limit, $offset)
    {
        $this->stmnt = $this->db->prepare(     "SELECT pe.idPedido, pe.idEstado, pe.observaciones, pe.fechaAlta,
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

}