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
        $this->db->select('ROUND(SUM(precioUnitario * cantidad), 2) AS total');
        $this->db->from('ingresoDetalle');
        $this->db->where('fecha', $fecha);
        $this->db->where('eliminado', 0);
        $total = $this->db->get()->result();
        return $total[0];

    /*   return $this->queryPreparadaSQL("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total
            FROM ingresoDetalle
            WHERE fecha=:fecha AND eliminado = 0",
            array("fecha" => $fecha)
        );*/
    }

    function egresoDia($fecha)
    {
        $this->db->select('ROUND(SUM(precioUnitario * cantidad), 2) AS total');
        $this->db->from('compra');
        $this->db->where('fecha', $fecha);
        $this->db->where('eliminado', 0);
        $total = $this->db->get()->result();
        return $total[0];

      /*  return $this->queryPreparadaSQL("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total
            FROM compra
            WHERE fecha=:fecha AND eliminado = 0",
            array("fecha" => $fecha)
        );*/
    }


    function ingresoRango($fechaInicio, $fechaFin)
    {
        $this->db->select('ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha');
        $this->db->from('ingresoDetalle');
        $this->db->where('eliminado', 0);
        $this->db->where('fecha >=', $fechaInicio);
        $this->db->where('fecha <=', $fechaFin);
        $this->db->group_by('fecha');
        $total = $this->db->get()->result();
        return $total[0];

/*        return $this->queryTodasLasFilas("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha
            FROM ingresoDetalle
            WHERE eliminado = 0
            AND fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY fecha",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin)
        );*/
    }

    function egresoRango($fechaInicio, $fechaFin)
    {
        $this->db->select('ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha');
        $this->db->from('compra');
        $this->db->where('eliminado', 0);
        $this->db->where('fecha >=', $fechaInicio);
        $this->db->where('fecha <=', $fechaFin);
        $this->db->group_by('fecha');
        $total = $this->db->get()->result();
        return $total[0];

    /*    return $this->queryTodasLasFilas("
            SELECT ROUND(SUM(precioUnitario * cantidad), 2) AS total, fecha
            FROM compra
            WHERE eliminado = 0
            AND fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY fecha",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin)
        );*/
    }

    function productosEgresoDia($fecha)
    {
        $this->db->select('cantidad AS cant, p.nombre');
        $this->db->from('ingresoDetalle i');
        $this->db->join('producto p', 'p.idProducto = i.idProducto');
        $this->db->where('i.fecha', $fecha);
        $this->db->where('i.eliminado', 0);
        $this->db->group_by('i.idProducto');
        return $this->db->get()->result();
     /*   return $this->queryTodasLasFilas("
            SELECT cantidad AS cant, p.nombre
            FROM ingresoDetalle i
            INNER JOIN producto p 
            ON (p.idProducto = i.idProducto)
            WHERE i.fecha =:fecha AND i.eliminado = 0
            GROUP BY i.idProducto",
            array("fecha" => $fecha));*/
    }

    function productosEgresoRango($fechaInicio, $fechaFin)
    {
        $this->db->select('SUM(cantidad) AS cant, p.nombre');
        $this->db->from('ingresoDetalle i');
        $this->db->join('producto p', 'p.idProducto = i.idProducto');
        $this->db->where('i.fecha >=', $fechaInicio);
        $this->db->where('i.fecha <=', $fechaFin);
        $this->db->where('i.eliminado', 0);
        $this->db->group_by('i.idProducto');
        return $this->db->get()->result();
       /* return $this->queryTodasLasFilas("
            SELECT SUM(cantidad) AS cant, p.nombre
            FROM ingresoDetalle i
            INNER JOIN producto p 
            ON (p.idProducto = i.idProducto)
            WHERE i.fecha BETWEEN :fechaInicio AND :fechaFin
            AND i.eliminado = 0
            GROUP BY i.idProducto",
            array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin));*/
    }
}