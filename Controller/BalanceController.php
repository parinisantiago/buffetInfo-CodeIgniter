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
            $fecha=$_POST['fecha'];
        }
        catch (valException $e){
            echo "apa la bocha";
        }


        $this->graficoBarraDia($fecha);
        $this->graficoTortaDia($fecha);

        $this->dispatcher->render('Backend/balanceTemplate.twig');

    }

    protected function graficoTortaDia($fecha)
    {
        $totalProductos = $this->balance->productosEgresoDia($fecha);


        $chart = new PieChart(500, 250);
        $dataSet = new XYDataSet();


        foreach ($totalProductos as $producto) {
            $dataSet->addPoint(new Point($producto->nombre, $producto->cant));
        }

        $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fecha);
        $chart->setDataSet($dataSet);
        $chart->render('libchart/demo/generated/demo.png');
        $image = imagecreatefrompng('libchart/demo/generated/demo.png');
    }

    protected function graficoBarraDia($fecha)
    {
        $ingreso = $this->balance->ingresoDia($fecha);

        (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;


        $egreso = $this->balance->egresoDia($fecha);

        (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

        $balance = $ingreso - $egreso;


        $chart = new VerticalBarChart(500,250);
        $dataSet = new XYDataSet();
        $dataSet->addPoint(new Point($fecha, $balance));
        $chart->setTitle('Balance para el dia ' . $fecha);
        $chart->setTitle('Balance para el dia ' . $fecha);
        $chart->setDataSet($dataSet);
        $chart->render('libchart/demo/generated/demo2.png');
        $image = imagecreatefrompng('libchart/demo/generated/demo2.png');
    }

}