<?php

require_once (__DIR__.'/../libchart/libchart/classes/libchart.php');
require_once (__DIR__.'/../fpdf_demo/fpdf.php');


class BalanceController extends Controller
{

    private $balance;

    public function getPermission(){
        Session::init();
        return ((Session::getValue('rol') == '0' )or(Session::getValue('rol') == '1' ) );
    }

    function __construct()
    {
        parent::__contruct();
        $this->balance = new BalanceModel();
    }

    function index()
    {
        $this->token();
        $this->dispatcher->render("Backend/FormBalance.twig");
    }

    function balanceDia()
    {

        try
        {
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            $this->validator->validarFecha($_POST['fecha'], "La fecha ingresada no es válida");
            $fecha=$_POST['fecha'];
            $ingreso = $this->balance->ingresoDia($fecha);

            (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;


            $egreso = $this->balance->egresoDia($fecha);

            (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

            $balance = $ingreso - $egreso;
            if($balance == 0) throw new valException("No hay datos que mostrar");
            $this->graficoBarraDia($fecha);
            $this->graficoTortaDia($fecha);

            $this->dispatcher->fecha = $fecha;
            $this->dispatcher->render('Backend/balanceTemplate.twig');
        }
        catch (valException $e){
            $this->dispatcher->fecha = $_POST['fecha'];
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->index();
        }




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
        $chart->render('uploads/demo.png');
        $image = imagecreatefrompng('uploads/demo.png');
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
        $chart->render('uploads/demo2.png');
        $image = imagecreatefrompng('uploads/demo2.png');
    }

    function balanceRango()
    {

        try
        {
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            $this->validator->validarFecha($_POST['fechaInicio'], "La fecha ingresada no es válida");
            $this->validator->validarFecha($_POST['fechaFin'], "La fecha ingresada no es válida");
            $fechaInicio=$_POST['fechaInicio'];
            $fechaFin=$_POST['fechaFin'];
            if ($fechaFin < $fechaInicio) throw new valException("La fecha de fin no puede ser inferior a la fecha de inicio");
            //aca va a empezar la negrada de las fechas, agarranse de donde puedan porque son las 3 AM y ya no me da el bocho.

            //Puede que no existan egresos para una fecha pero si ingresos y viceversa. Hay que validar eso en ambos arreglos.
            //Traer todo de la base no es una opcion por los valores nulos y la consulta anidada enquilombada.
            //Tambien hay que validar que una vez que controlo un arreglo, cuando empiezo a controlar el otro el valor que estoy controlando no haya sido ya controlado.
            //no se si se entende pero tengo una mezcla de ansiedad, cansancio, mate y depresion y en mi cabeza las cosas funcionan de la siguiente forma:


            //me traigo los valores
            $ingresos = $this->balance->ingresoRango($fechaInicio, $fechaFin);
            $egresos = $this->balance->egresoRango($fechaInicio, $fechaFin);

            var_dump($ingresos);
            var_dump($egresos);
            die;
            $total= array();
            $balances = array();
            //creo un arreglo simple porque de la base lo traigo en objetos. Esto me sirve para despues mergear los arreglos
            foreach ($ingresos as $ingreso)
            {
                $balances['ingreso'][''.$ingreso->fecha] = $ingreso->total;
            }

            //idem anterior
            foreach ($egresos as $egreso)
            {
                $balances['egreso'][''.$egreso->fecha] = $egreso->total;
            }

            //hago la cuentita de ingreso - egreso, si no hay egreso le mando solo el ingreso
            foreach ($balances['ingreso'] as $key => $value){
                if (isset($balances['egreso'][$key])) $total[$key] = ($balances['ingreso'][$key] - $balances['egreso'][$key]).'';
                else $total[$key] = $balances['ingreso'][$key];
            }

            //hago la validacion de nuevo por si existen egresos pero no ingresos.
            foreach ($balances['egreso'] as $key => $value){
                if (!isset($balances['ingreso'][$key])) $total[$key] = (0 - $balances['egreso'][$key]).'';
            }


            $val = true;
            foreach ($total as $t){
                var_dump($t);
                if ($t != 0) $val = false;
            }


            if ($val) throw new valException("No hay valores para mostrar");

            $colores = array();

            //y ahora creo el gráifco de barras (falta el de tortas todavia pero ese es mas facil)
            $chart = new HorizontalBarChart(500, 250);
            $dataSet = new XYDataSet();
            foreach ($total as $key => $value) {

                (strcmp($value, '0') > 0)? array_push($colores, new Color(0, 0, 255)) : array_push($colores, new Color(255, 0, 0));
                $dataSet->addPoint(new Point($key, $value));
            }

            $chart->getPlot()->getPalette()->barColorSet = new ColorSet($colores, 0.7);
            $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fechaInicio . 'Hasta el dia' . $fechaFin);
            $chart->setDataSet($dataSet);
            $chart->render('uploads/demo2.png');
            $image = imagecreatefrompng('uploads/demo2.png');


            //ahora vamos con los productos, a esto le hace falta alto refactoring pero estoy re podrido.
            $productos = $this->balance->productosEgresoRango($fechaInicio, $fechaFin);
            $chart = new PieChart(500, 250);
            $dataSet = new XYDataSet();


            foreach ($productos as $producto) {
                $dataSet->addPoint(new Point($producto->nombre, $producto->cant));
            }
            $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fechaInicio .'hasta el dia' . $fechaFin);
            $chart->setDataSet($dataSet);
            $chart->render('uploads/demo.png');
            $image = imagecreatefrompng('uploads/demo.png');

            $this->dispatcher->fechaInicio = $fechaInicio;
            $this->dispatcher->fechaFin = $fechaFin;
            $this->dispatcher->render('Backend/balanceTemplate.twig');
        }
        catch (valException $e){
            $this->dispatcher->fechaInicio = $_POST['fechaInicio'];
            $this->dispatcher->fechaFin = $_POST['fechaFin'];
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->token();
            $this->dispatcher->render("Backend/FormBalance.twig");
        }


    }

    function exportarPDFdia()
    {

        try
        {
            $this->validator->validarFecha($_POST['fecha'], "La fecha ingresada no es válida");
            $fecha=$_POST['fecha'];

            $productos = $this->balance->productosEgresoDia($fecha);
            $ingreso = $this->balance->ingresoDia($fecha);

            (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;


            $egreso = $this->balance->egresoDia($fecha);

            (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

            $balance = $ingreso - $egreso;
            $image = imagecreatefrompng('uploads/demo.png');
            $image2 = imagecreatefrompng('uploads/demo2.png');
            imagejpeg($image, 'uploads/demo3.jpg', 100);
            imagejpeg($image2, 'uploads/demo4.jpg', 100);


            ob_start();
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Balance del dia');
            $pdf->Ln();
            $pdf->Cell(30,10,'Fecha');
            $pdf->Cell(30,10,'Balance');
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,10,$fecha);
            $pdf->Cell(30,10,$balance);
            $pdf->Ln();

            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Grafico Barra');
            $pdf->Ln();
            $pdf->Image('uploads/demo4.jpg');

            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Productos vendidos');
            $pdf->Ln();
            $pdf->Cell(30,10,'Producto');
            $pdf->Cell(30,10,'Total vendido');
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);


            foreach ($productos as $producto) {


                $pdf->Cell(50,10,$producto->nombre);
                $pdf->Cell(50,10,$producto->cant);
                $pdf->Ln();
            }

            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Grafico Torta');
            $pdf->Ln();
            $pdf->Image('uploads/demo3.jpg');


            $pdf->Output();
            ob_end_flush();

        }
        catch (valException $e){
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->token();
            $this->dispatcher->render("Backend/FormBalance.twig");
        }

    }
    function exportarPDF()
    {
        try
        {
            $this->validator->validarFecha($_POST['fechaInicio'], "La fecha ingresada no es válida");
            $this->validator->validarFecha($_POST['fechaFin'], "La fecha ingresada no es válida");
            $fechaInicio=$_POST['fechaInicio'];
            $fechaFin=$_POST['fechaFin'];
            if ($fechaFin < $fechaInicio) throw new valException("La fecha de fin no puede ser inferior a la fecha de inicio");
            $ingresos = $this->balance->ingresoRango($fechaInicio, $fechaFin);
            $egresos = $this->balance->egresoRango($fechaInicio, $fechaFin);
            $total= array();
            $balances = array();

            foreach ($ingresos as $ingreso)
            {
                $balances['ingreso'][''.$ingreso->fecha] = $ingreso->total;
            }

            foreach ($egresos as $egreso)
            {
                $balances['egreso'][''.$egreso->fecha] = $egreso->total;
            }

            foreach ($balances['ingreso'] as $key => $value){
                if (isset($balances['egreso'][$key])) $total[$key] = ($balances['ingreso'][$key] - $balances['egreso'][$key]).'';
                else $total[$key] = $balances['ingreso'][$key];
            }


            foreach ($balances['egreso'] as $key => $value){
                if (!isset($balances['ingreso'][$key])) $total[$key] = (0 - $balances['egreso'][$key]).'';
            }



            $image = imagecreatefrompng('uploads/demo.png');
            $image2 = imagecreatefrompng('uploads/demo2.png');
            imagejpeg($image, 'uploads/demo3.jpg', 100);
            imagejpeg($image2, 'uploads/demo4.jpg', 100);

            ob_start();
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Listado de balances para cada dia');
            $pdf->Ln();
            $pdf->Cell(30,10,'Fecha');
            $pdf->Cell(30,10,'Balance');
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            while ( list ( $key, $va ) = each ( $total ) ) {
                $pdf->Cell(30,10,$key);
                $pdf->Cell(30,10,$va);
                $pdf->Ln();
            }
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Grafico Barra');
            $pdf->Ln();
            $pdf->Image('uploads/demo4.jpg');

            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Listado de productos vendidos');
            $pdf->Ln();
            $pdf->Cell(30,10,'Producto');
            $pdf->Cell(30,10,'Total vendido');
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);

            $productos = $this->balance->productosEgresoRango($fechaInicio, $fechaFin);

            foreach ($productos as $producto) {


                $pdf->Cell(50,10,$producto->nombre);
                $pdf->Cell(50,10,$producto->cant);
                $pdf->Ln();
            }

            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(30,10,'Grafico Torta');
            $pdf->Ln();
            $pdf->Image('uploads/demo3.jpg');


            $pdf->Output();
            ob_end_flush();
        }
        catch (valException $e){
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->token();
            $this->dispatcher->render("Backend/FormBalance.twig");  
        }



    }
}