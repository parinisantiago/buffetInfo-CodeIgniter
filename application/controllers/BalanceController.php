<?php

require_once (__DIR__ . '/../third_party/libchart/libchart/classes/libchart.php');
require_once (__DIR__ . '/../third_party/fpdf_demo/fpdf.php');
include_once ("Controller.php");

class BalanceController extends Controller
{

    private $balance;

    public function getPermission(){
        Session::init();
        return ((Session::getValue('rol') == '0' )or(Session::getValue('rol') == '1' ) );
    }

    function __construct()
    {
        parent::__construct();
        $this->load->model('BalanceModel');
    }

    function index()
    {
        if($this->permissions())
        {
            $this->token();
            $this->display("FormBalance.twig");
        }
    }
/*
    function balanceDia()
    {

        try
        {
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            $this->validator->validarFecha($_POST['fecha'], "La fecha ingresada no es válida");
            $fecha=$_POST['fecha'];
            $ingreso = $this->BalanceModel->ingresoDia($fecha);
            $egreso = $this->BalanceModel->egresoDia($fecha);


            if(is_null($egreso->total) && is_null($ingreso->total)) throw new valException("No hay datos que mostrar");

            (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;



            (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

            
            $balance = $ingreso - $egreso;
            $this->graficoBarraDia($fecha);
            $this->graficoTortaDia($fecha);

            $this->addData('fecha', $fecha);
            $this->display('balanceTemplate.twig');
        }
        catch (Exception $e){
            $this->addData('fecha', $_POST['fecha']);
            $this->addData('mensajeError', $e -> getMessage());
            $this->index();
        }




    }

    protected function graficoTortaDia($fecha)
    {
        $totalProductos = $this->BalanceModel->productosEgresoDia($fecha);


        $chart = new PieChart(500, 250);
        $dataSet = new XYDataSet();


        foreach ($totalProductos as $producto) {
            $dataSet->addPoint(new Point($producto->nombre, $producto->cant));
        }

        $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fecha);
        $chart->setDataSet($dataSet);
        $chart->render(files.'demo.png');
        $image = imagecreatefrompng(files.'demo.png');
        imagejpeg($image, files.'demo3.jpg', 100);

    }

    protected function graficoBarraDia($fecha)
    {
        $ingreso = $this->BalanceModel->ingresoDia($fecha);

        (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;


        $egreso = $this->BalanceModel->egresoDia($fecha);

        (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

        $balance = $ingreso - $egreso;


        $chart = new VerticalBarChart(500,250);
        $dataSet = new XYDataSet();
        $dataSet->addPoint(new Point($fecha, $balance));
        $chart->setTitle('Balance para el dia ' . $fecha);
        $chart->setTitle('Balance para el dia ' . $fecha);
        $chart->setDataSet($dataSet);
        $chart->render(files.'demo2.png');
        $image = imagecreatefrompng(files.'demo2.png');
        imagejpeg($image, files.'demo4.jpg', 100);

    }*/

    function balanceRango()
    {
        if($this->permissions())
        {
            try
            {
                if (isset($_POST['fecha']))
                {
                    $_POST['fechaInicio'] = $_POST['fecha'];
                    $_POST['fechaFin'] = $_POST['fecha'];
                }

                if (!isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");

                $this->validator->validarFecha($_POST['fechaInicio'], "La fecha inicio no es válida");
                $this->validator->validarFecha($_POST['fechaFin'], "La fecha fin no es válida");
                $fechaInicio = $_POST['fechaInicio'];
                $fechaFin = $_POST['fechaFin'];

                if ($fechaFin < $fechaInicio) throw new Exception("La fecha de fin no puede ser inferior a la fecha de inicio");
                //aca va a empezar la negrada de las fechas, agarranse de donde puedan porque son las 3 AM y ya no me da el bocho.

                //Puede que no existan egresos para una fecha pero si ingresos y viceversa. Hay que validar eso en ambos arreglos.
                //Traer todo de la base no es una opcion por los valores nulos y la consulta anidada enquilombada.
                //Tambien hay que validar que una vez que controlo un arreglo, cuando empiezo a controlar el otro el valor que estoy controlando no haya sido ya controlado.
                //no se si se entende pero tengo una mezcla de ansiedad, cansancio, mate y depresion y en mi cabeza las cosas funcionan de la siguiente forma:


                //me traigo los valores
                $ingresos = $this->BalanceModel->ingresoRango($fechaInicio, $fechaFin);
                $egresos = $this->BalanceModel->egresoRango($fechaInicio, $fechaFin);
                if (empty($ingresos) && empty($egreso)) throw new valException("NO hay datos para mostrar");

                $total = array();
                $balances = array();
                //creo un arreglo simple porque de la base lo traigo en objetos. Esto me sirve para despues mergear los arreglos


                foreach ($ingresos as $ingreso)
                {
                    $balances['ingreso']['' . $ingreso->fecha] = $ingreso->total;
                }

                //idem anterior
                foreach ($egresos as $egreso)
                {
                    $balances['egreso']['' . $egreso->fecha] = $egreso->total;
                }
                //hago la cuentita de ingreso - egreso, si no hay egreso le mando solo el ingreso

                if (isset($balances['ingreso']))
                {
                    foreach ($balances['ingreso'] as $key => $value)
                    {
                        if (isset($balances['egreso'][$key])) $total[$key] = ($balances['ingreso'][$key] - $balances['egreso'][$key]) . '';
                        else $total[$key] = $balances['ingreso'][$key];
                    }
                }
                //hago la validacion de nuevo por si existen egresos pero no ingresos
                if (isset($balances['egreso']))
                {
                    foreach ($balances['egreso'] as $key => $value)
                    {
                        if (!isset($balances['ingreso'][$key])) $total[$key] = (0 - $balances['egreso'][$key]) . '';
                    }
                }

                $val = true;
                foreach ($total as $t)
                {
                    if ($t != 0) $val = false;
                }


                if ($val) throw new Exception("No hay valores para mostrar");

                $colores = array();

                //y ahora creo el gráifco de barras (falta el de tortas todavia pero ese es mas facil)
                $chart = new HorizontalBarChart(500, 250);
                $dataSet = new XYDataSet();
                foreach ($total as $key => $value)
                {
                    (strcmp($value, '0') > 0) ? array_push($colores, new Color(0, 0, 255)) : array_push($colores, new Color(255, 0, 0));
                    $dataSet->addPoint(new Point($key, $value));
                }

                $chart->getPlot()->getPalette()->barColorSet = new ColorSet($colores, 0.7);
                $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fechaInicio . 'Hasta el dia' . $fechaFin);
                $chart->setDataSet($dataSet);
                $chart->render(files . 'demo2.png');
                $image = imagecreatefrompng(files . 'demo2.png');
                imagejpeg($image, files . 'demo3.jpg', 100);


                //ahora vamos con los productos, a esto le hace falta alto refactoring pero estoy re podrido.
                $productos = $this->BalanceModel->productosEgresoRango($fechaInicio, $fechaFin);
                $chart = new PieChart(500, 250);
                $dataSet = new XYDataSet();


                foreach ($productos as $producto)
                {
                    $dataSet->addPoint(new Point($producto->nombre, $producto->cant));
                }

                $chart->setTitle('Cantidad de productos vendidos para el dia ' . $fechaInicio . 'hasta el dia' . $fechaFin);
                $chart->setDataSet($dataSet);
                $chart->render(files . 'demo.png');
                $image = imagecreatefrompng(files . 'demo.png');
                imagejpeg($image, files . 'demo3.jpg', 100);


                $this->addData('fechaInicio', $fechaInicio);
                $this->addData('fechaFin', $fechaFin);
                $this->display('balanceTemplate.twig');
            }
            catch (Exception $e)
            {
                $this->addData('fechaInicio', $_POST['fechaInicio']);
                $this->addData('fechaFin', $_POST['fechaFin']);
                $this->addData('mensajeError', $e->getMessage());
                $this->token();
                $this->display("FormBalance.twig");
            }
        }

    }
/*
    function exportarPDFdia()
    {

        try
        {
            $this->validator->validarFecha($_POST['fecha'], "La fecha ingresada no es válida");
            $fecha=$_POST['fecha'];

            $productos = $this->BalanceModel->productosEgresoDia($fecha);
            $ingreso = $this->BalanceModel->ingresoDia($fecha);

            (empty($ingreso->total)) ? $ingreso = "0" : $ingreso = $ingreso->total;


            $egreso = $this->BalanceModel->egresoDia($fecha);

            (empty($egreso->total)) ? $egreso = "0" : $egreso = $egreso->total;

            $balance = $ingreso - $egreso;
            $image = imagecreatefrompng(files.'demo.png');
            $image2 = imagecreatefrompng(files.'demo2.png');
            imagejpeg($image, files.'demo3.jpg', 100);
            imagejpeg($image2, files.'demo4.jpg', 100);


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
            $pdf->Image(files.'demo4.jpg');

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
            $pdf->Image(files.'demo3.jpg');


            $pdf->Output();
            ob_end_flush();

        }
        catch (Exception $e){
            $this->addData('mensajeError', $e -> getMessage());
            $this->token();
            $this->display("FormBalance.twig");
        }

    }*/

    function exportarPDF()
    {
        if($this->permissions()) {

            try
            {
                $this->validator->validarFecha($_POST['fechaInicio'], "La fecha ingresada no es válida");
                $this->validator->validarFecha($_POST['fechaFin'], "La fecha ingresada no es válida");
                $fechaInicio = $_POST['fechaInicio'];
                $fechaFin = $_POST['fechaFin'];
                if ($fechaFin < $fechaInicio) throw new Exception("La fecha de fin no puede ser inferior a la fecha de inicio");
                $ingresos = $this->BalanceModel->ingresoRango($fechaInicio, $fechaFin);
                $egresos = $this->BalanceModel->egresoRango($fechaInicio, $fechaFin);
                $total = array();
                $balances = array();

                foreach ($ingresos as $ingreso)
                {
                    $balances['ingreso']['' . $ingreso->fecha] = $ingreso->total;
                }

                foreach ($egresos as $egreso)
                {
                    $balances['egreso']['' . $egreso->fecha] = $egreso->total;
                }

                if (isset($balances['ingreso']))
                {

                    foreach ($balances['ingreso'] as $key => $value)
                    {
                        if (isset($balances['egreso'][$key])) $total[$key] = ($balances['ingreso'][$key] - $balances['egreso'][$key]) . '';
                        else $total[$key] = $balances['ingreso'][$key];
                    }
                }

                if (isset($balances['egreso']))
                {

                    foreach ($balances['egreso'] as $key => $value)
                    {
                        if (!isset($balances['ingreso'][$key])) $total[$key] = (0 - $balances['egreso'][$key]) . '';
                    }

                }

                $image = imagecreatefrompng(files . 'demo.png');
                $image2 = imagecreatefrompng(files . 'demo2.png');
                imagejpeg($image, files . 'demo3.jpg', 100);
                imagejpeg($image2, files . 'demo4.jpg', 100);

                ob_start();
                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(30, 10, 'Listado de balances para cada dia');
                $pdf->Ln();
                $pdf->Cell(30, 10, 'Fecha');
                $pdf->Cell(30, 10, 'Balance');
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                while (list ($key, $va) = each($total))
                {
                    $pdf->Cell(30, 10, $key);
                    $pdf->Cell(30, 10, $va);
                    $pdf->Ln();
                }
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(30, 10, 'Grafico Barra');
                $pdf->Ln();
                $pdf->Image(files . 'demo4.jpg');

                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(30, 10, 'Listado de productos vendidos');
                $pdf->Ln();
                $pdf->Cell(30, 10, 'Producto');
                $pdf->Cell(30, 10, 'Total vendido');
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);

                $productos = $this->BalanceModel->productosEgresoRango($fechaInicio, $fechaFin);

                foreach ($productos as $producto)
                {
                    $pdf->Cell(50, 10, $producto->nombre);
                    $pdf->Cell(50, 10, $producto->cant);
                    $pdf->Ln();
                }

                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(30, 10, 'Grafico Torta');
                $pdf->Ln();
                $pdf->Image(files . 'demo3.jpg');


                $pdf->Output();
                ob_end_flush();
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->token();
                $this->display("FormBalance.twig");
            }


        }
    }
}