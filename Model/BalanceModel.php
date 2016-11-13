<?php

require_once('Model.php');

class BalanceModel extends Model
{
    function __construct()
    {
        parent::__construct();
    }

    function ingresoDia($fecha)
    {
        return $this->queryPreparadaSQL("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total
            FROM ingresoDetalle
            WHERE fecha=:fecha AND eliminado = 0",
            array("fecha" => $fecha)
        );
    }

    function egresoDia($fecha)
    {
        return $this->queryPreparadaSQL("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total
            FROM compra
            WHERE fecha=:fecha AND eliminado = 0",
            array("fecha" => $fecha)
        );
    }

    function productosEgresoDia($fecha)
    {
        return $this->queryTodasLasFilas("
            SELECT ROUND(SUM(cantidad),2) AS cant, p.nombre
            FROM ingresoDetalle i
            INNER JOIN producto p 
            ON (p.idProducto = i.idProducto)
            WHERE i.fecha =:fecha AND i.eliminado = 0
            GROUP BY i.idProducto",
            array("fecha" => $fecha));
    }
}