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


    function ingresoRango($fechaInicio, $fechaFin)
    {
        return $this->queryTodasLasFilas("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha
            FROM ingresoDetalle
            WHERE eliminado = 0
            AND fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY fecha",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin)
        );
    }

    function egresoRango($fechaInicio, $fechaFin)
    {
        return $this->queryTodasLasFilas("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha
            FROM compra
            WHERE eliminado = 0
            AND fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY fecha",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin)
        );
    }

    function productosEgresoDia($fecha)
    {
        return $this->queryTodasLasFilas("
            SELECT cantidad AS cant, p.nombre
            FROM ingresoDetalle i
            INNER JOIN producto p 
            ON (p.idProducto = i.idProducto)
            WHERE i.fecha =:fecha AND i.eliminado = 0
            GROUP BY i.idProducto",
            array("fecha" => $fecha));
    }

    function productosEgresoRango($fechaInicio, $fechaFin)
    {
        return $this->queryTodasLasFilas("
            SELECT SUM(cantidad) AS cant, p.nombre
            FROM ingresoDetalle i
            INNER JOIN producto p 
            ON (p.idProducto = i.idProducto)
            WHERE i.fecha BETWEEN :fechaInicio AND :fechaFin
            AND i.eliminado = 0
            GROUP BY i.idProducto",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin));
    }
}