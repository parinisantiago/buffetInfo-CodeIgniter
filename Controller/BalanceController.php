<?php

require_once (__DIR__.'/../libchart/libchart/classes/libchart.php');

class BalanceController extends Controller
{

    private $balance;

    function __construct()
    {
        parent::__contruct();
        $this->balance = new BalanceModel();
    }

    function index()
    {
        $this->dispatcher->render("Backend/FormBalance.twig");
    }

    function balanceDia()
    {

        try
        {
            $this->validator->validarFecha($_POST['fecha'], "La fecha ingresada no es válida");
        }
        catch (valException $e){
            echo "apa la bocha";
        }


        $ingreso = $this->balance->ingresoDia($_POST['fecha']);

        (empty($ingreso->total))? $ingreso = "0" : $ingreso = $ingreso->total;


        $egreso = $this->balance->egresoDia($_POST['fecha']);

        (empty($egreso->total))? $egreso = "0" : $egreso = $egreso->total;

        $totalProductos = $this->balance->productosEgresoDia($_POST['fecha']);

        $chart = new PieChart(500,250);
        $dataSet = new XYDataSet();


        foreach ($totalProductos as $producto)
        {
            $dataSet->addPoint(new Point($producto->nombre,$producto->cant));
        }

        $chart->setTitle('Cantidad de productos vendidos para el dia '.$_POST['fecha']);
        $chart->setDataSet($dataSet);
        $chart->render('libchart/demo/generated/demo.png');
        $image = imagecreatefrompng('libchart/demo/generated/demo.png');

        $this->dispatcher->render('Backend/balanceTemplate.twig');

    }

}