<?php
require_once "../../controladores/veterinarios.controlador.php";
require_once " ../../../../modelos/veterinarios.modelo.php";
require_once "../../controladores/productores.controlador.php";
require_once " ../../../../modelos/productores.modelo.php";
require_once "../../controladores/aftosa.controlador.php";
require_once " ../../../../modelos/aftosa.modelo.php";
require_once "../../controladores/actas.controlador.php";
require_once " ../../../../modelos/actas.modelo.php";
require_once "../../controladores/animales.controlador.php";
require_once " ../../../../modelos/animales.modelo.php";

function formatearFecha($fecha){
    
    $fechaExplode = explode('-',$fecha);
    
    $fechaFormateada = $fechaExplode[2]."-".$fechaExplode[1]."-".$fechaExplode[0];
    
    return $fechaFormateada;
    
}

$informe = $_GET['informe'];

class informePDF{

    public $matricula;

    public function informe1(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Animales Totales Vacunados por Vacunador';

        $cabezera = "Sistema integrado de Vacunacion Anti-Aftosa \n Animales Totales Vacunados por Vacunador";

        include 'cabezera.php';

        $pdf->Ln(5);
        $pdf->SetFont('Times','B',14);
        $pdf->SetX(40);
        $pdf->Cell(190,10,'(Incluyendo establecimientos de distintos Distritos)',0,1,'L',0);
        $pdf->SetX(40);
        $pdf->Cell(65,8,'VACUNADOR',1,0,'C',0);
        $pdf->Cell(45,8,'TOTAL',1,1,'C',0);
        $pdf->SetFont('Times','',12);
        
        $total = 0;

        $veterinarios = ControladorVeterinarios::ctrMostrarVeterinarios(null,null);

        foreach ($veterinarios as $key => $value) {
         
            $item = 'matricula';

            $totalVacunados = ControladorActas::ctrContarActas($item,$value['matricula']);

            $pdf->SetX(40);
            
            $pdf->Cell(85,8,utf8_decode($value['nombre']),0,0,'L',0);

            if ($totalVacunados[0] != null) {
            
                $pdf->Cell(45,8,number_format($totalVacunados[0],0,'','.'),0,1,'L',0);
                
                $total += $totalVacunados[0];
                
            }else{
                
                $pdf->Cell(45,8,number_format(0,0,'','.'),0,1,'L',0);

            }
        

        }
    
        $pdf->SetX(40);
        $pdf->SetFont('times','B',11);
        $pdf->Cell(85,8,'Total',0,0,'L',0);
        $pdf->Cell(45,8,number_format($total,0,'','.'),0,1,'L',0);
    
        $pdf->Output();
        

    }

    public function informe2(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Total Bovinos Vacunados por localidad y total departamental';

        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Total Bovinos Vacunados por localidad y total departamental";

        include 'cabezera.php';

        $pdf->Ln(10);
        $pdf->SetFont('Times','B',12);
        $pdf->SetX(10);
        $pdf->Cell(50,7,'IRIONDO',0,0,'L',0);
        $pdf->Cell(60,7,'DISTRITO',0,0,'L',0);
        $pdf->Cell(40,7,'TOTAL animales',0,0,'L',0);
        $pdf->Cell(50,7,'Cant. Establ.',0,1,'L',0);
        $pdf->Cell(185,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',10);
        $pdf->SetFillColor(0,0,0);
    
        $distinct = 'distrito';

        $distritos = ControladorProductores::ctrMostrarProductoresDistinct(null,null,$distinct);

        $renspasPorDistrito = array();

        $totalEstablecimientosGral = 0;

        $totalVacunadoGral = 0;

        $totalParcialGral = 0;

        $totales = array('establecimientos'=>0,'vacunados'=>0,'parcial'=>0,'animales'=>0);

        foreach ($distritos as $key => $value) {
            
            if($value[0] != null){      

                $item = 'distrito';
                
                $tabla = 'productores';
                
                $orden = 'propietario';
                
                $renspaDistrito = ControladorAftosa::ctrMostrarDatos($tabla,$item,$value[0],$orden);

                $totalAnimales = 0;

                $totalVacunados = 0;
                
                $totalParcial = 0;

                $totalEstablecimientos = sizeof($renspaDistrito);

                $totales['establecimientos'] += $totalEstablecimientos;

                $item = 'renspa';

                $item2 = 'campania';

                $valor2 = $_COOKIE['campania'];
                
                for ($i=0; $i < $totalEstablecimientos ; $i++) { 

                    $animales = ControladorAnimales::ctrMostrarAnimales($item,$renspaDistrito[$i]['renspa'],$item2,$valor2);
                    
                    $parcial =  ($animales['vaquillonas']  + $animales['toritos'] + $animales['terneros'] + $animales['terneras'] + $animales['novillos'] + $animales['novillitos']);

                    $totalParcial += $parcial;

                    $totales['parcial'] += $parcial;

                    $totalAnimales += ($animales['vacas'] + $animales['toros'] + $parcial);
                    
                    $totales['animales'] += ($animales['vacas'] + $animales['toros'] + $parcial);

                    $vacunados = ControladorActas::ctrMostrarActa($item,$renspaDistrito[$i]['renspa']);

                    $vacunados = ($vacunados) ? $vacunados['cantidadPar'] : 0;

                    $totalVacunados += $vacunados;

                    $totales['vacunados'] += $vacunados;

                }

                $nombreDistrito = ControladorProductores::ctrMostrarLocation('departamento',8,'localidad',$value[0]);

                $pdf->Cell(50,7,'',0,0,'L',0);
                $pdf->Cell(40,7,utf8_decode($nombreDistrito[0]['nombre']),0,0,'L',0);
                $pdf->Cell(30,7,'Total Vacunado',0,0,'L',0);
                $pdf->Cell(40,7,$totalVacunados,0,0,'L',0);
                $pdf->Cell(50,7,$totalEstablecimientos,0,1,'L',0);
                $pdf->Cell(90,7,'',0,0,'L',0);
                $pdf->Cell(30,7,'Total Animales',0,0,'L',0);
                $pdf->Cell(40,7,$totalAnimales,0,1,'L',0);
                $pdf->Cell(90,7,'',0,0,'L',0);
                $pdf->SetFont('Times','b',10);
                $pdf->Cell(30,7,'Parcial:',0,0,'L',0);
                $pdf->SetFont('Times','',10);
                $pdf->Cell(40,7,$totalParcial,0,1,'L',0);
                $pdf->SetX(60);
                $pdf->Cell(120,.1,'',0,1,'L',1);

            }

        }
    
        
        $pdf->SetTextColor(0,4,162);
        $pdf->SetFont('helvetica','',10);
        $pdf->Cell(40,7,'Promedio de Animales:',0,0,'L',0);
        
        $promedio = number_format(($totales['vacunados'] / $totales['establecimientos']),2,',','.');
        
        $pdf->Cell(45,7,$promedio,0,0,'L',0);
        $pdf->Cell(35,7,'Total Vacunado:',0,0,'L',0);
        $pdf->Cell(40,7,$totales['vacunados'],0,0,'L',0);
        $pdf->Cell(40,7,$totales['establecimientos'],0,1,'L',0);
        $pdf->Cell(90,7,'',0,0,'L',0);
        $pdf->SetFont('helvetica','b',10);
        $pdf->Cell(30,7,'Total Animales:',0,0,'L',0);
        $pdf->SetFont('helvetica','',10);
        $pdf->Cell(40,7,$totales['animales'],0,1,'L',0);
        $pdf->SetFont('helvetica','b',10);
        $pdf->Cell(90,7,'',0,0,'L',0);
        $pdf->Cell(30,7,'Parcial:',0,0,'L',0);
        $pdf->SetFont('helvetica','',10);
        $pdf->Cell(40,7,$totales['parcial'],0,1,'L',0);
    
        $pdf->Output();
        
    }

    public function informe3(){
        
        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Detalle de Animales Vacunados por Vacunador con Bufalos/as';

        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Detalle de Animales Vacunados por Vacunador con Bufalos/as";

        include 'cabezeraLand.php';

        $matricula = $this->matricula;

        $item = 'matricula';

        $veterinario = ControladorVeterinarios::ctrMostrarVeterinarios($item, $matricula);

        $nombreVeterinario = $veterinario['nombre'];

        $pdf->Cell(40,7,'Vacunador:',0,0,'L',0);
        $pdf->Cell(40,7,$nombreVeterinario,0,1,'L',0);
        $pdf->Ln(3);
        $pdf->SetFont('Times','B',11);
        $pdf->SetX(10);
        $pdf->Cell(15,7,'Acta',0,0,'L',0);
        $pdf->Cell(25,7,'Fecha Vac.',0,0,'L',0);
        $pdf->Cell(70,7,'Propietario',0,0,'L',0);
        $pdf->Cell(65,7,'Establecimiento',0,0,'L',0);
        $pdf->Cell(40,7,'Renspa',0,0,'L',0);
        $pdf->Cell(25,7,'Cantidad',0,0,'L',0);
        $pdf->Cell(20,7,'Estado',0,0,'L',0);
        $pdf->Cell(40,7,'Debe',0,1,'L',0);
        $pdf->Cell(278,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',11);


        $dataPorVacunador = ControladorActas::ctrMostrarActa($item,$matricula);

        $dataCampania = ControladorAftosa::ctrMostrarDatosCampania('numero',$_COOKIE['campania']);

        $totalAnimalesVacunados = 0;

        foreach ($dataPorVacunador as $key => $dataProductor) {

            $item = 'renspa';

            $productor = ControladorProductores::ctrMostrarProductores($item,$dataProductor['renspa']);

            $pdf->Cell(15,7,$dataProductor['acta'],0,0,'L',0);
            $pdf->Cell(25,7,formatearFecha($dataProductor['fechaVacunacion']),0,0,'L',0);
            $pdf->Cell(70,7,$productor['propietario'],0,0,'L',0);
            $pdf->Cell(65,7,$productor['establecimiento'],0,0,'L',0);
            $pdf->Cell(40,7,$dataProductor['renspa'],0,0,'L',0);
            $pdf->Cell(25,7,$dataProductor['cantidadPar'],0,0,'L',0);

            $totalAnimalesVacunados += $dataProductor['cantidadPar'];
            
            $pdf->SetFont('helvetica','B',9);

            if($dataProductor['pago']){

                $pdf->SetTextColor(0,175,12);
                $pdf->Cell(20,7,utf8_decode("Pagó"),0,1,'L',0);
                $pdf->SetFont('times','',11);
                $pdf->SetTextColor(0,0,0);
            
            }else{

                $pdf->SetTextColor(255,0,0);
                $pdf->Cell(20,7,utf8_decode("NO Pagó"),0,0,'L',0);
                $debe = ($dataProductor['cantidadPar'] * $dataCampania['vacunadorA']);
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFont('times','',11);
                $pdf->Cell(40,7,"$ ".number_format($debe, 2, ",", "."),0,1,'L',0);

            }
        }
        
    
        $pdf->SetFont('times','B',11);
        $pdf->Cell(215,7,'',0,0,'L',0);
        $pdf->Cell(20,.5,'',0,1,'L',1);
        $pdf->Cell(215,7,'',0,0,'L',0);
        $pdf->Cell(40,7,$totalAnimalesVacunados,0,1,'L',0);	

        $pdf->Output();

    }

    public function informe4(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Entrega de vacunas por Vacunador';
        
        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Entrega de Vacunas por Productor incluida la de Búfalos/as";

        include 'cabezeraLand.php';

        $pdf->SetFont('Times','B',14);
        $pdf->SetFillColor(0,4,162);
        $pdf->SetX(10);
        $pdf->Cell(40,7,'Vacunador',0,0,'L',0);
        $pdf->Cell(40,7,utf8_decode('Matrícula'),0,0,'L',0);
        $pdf->Cell(40,7,'UEL',0,0,'L',0);
        $pdf->Cell(40,7,'Marca',0,0,'L',0);
        $pdf->Cell(50,7,'Fecha Entrega',0,0,'L',0);
        $pdf->Cell(40,7,'Dosis',0,1,'L',0);
        $pdf->Cell(250,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',11);

        $veterinarios = ControladorVeterinarios::ctrMostrarVeterinarios(null,null);

        $item = 'matricula';

        $item2 = 'campania';

        $campania = $_COOKIE['campania'];

        $total = 0;

        foreach ($veterinarios as $key => $value) {
            
            $distribuciones = ControladorAftosa::ctrMostrarDistribucion($item,$value['matricula'],$item2,$campania);
            
            if(!empty($distribuciones)){

                $pdf->Cell(40,7,utf8_decode($value['nombre']),0,0,'L',0);
                $pdf->Cell(40,7,$value['matricula'],0,0,'L',0);
                $pdf->Cell(40,7,'F.I.S.S.A Iriondo Sur',0,0,'L',0);


                $first = true;
                
                $totalDosis = 0;

                foreach ($distribuciones as $key => $distribucion) {
                    
                    if($first){

                        $first = false;

                    }else{

                        $pdf->Cell(120,7,'',0,0,'L',0);

                    }
                    
                    $pdf->Cell(40,7,$distribucion['marca'],0,0,'L',0);
                    $pdf->Cell(40,7,formatearFecha($distribucion['fechaEntrega']),0,0,'L',0);
                    $pdf->Cell(40,7,$distribucion['cantidad'],0,1,'L',0);
                
                    $totalDosis += $distribucion['cantidad'];
                    $total += $distribucion['cantidad'];
                    
                };

                $pdf->Cell(200,7,'',0,0,'L',0);
                $pdf->Cell(40,.5,'',0,1,'L',1);
                $pdf->Cell(200,7,'',0,0,'L',0);
                $pdf->SetFont('Times','B',11);
                $pdf->Cell(40,7,$totalDosis,0,1,'L',0);
                $pdf->SetFont('Times','',11);
                $pdf->Ln(1);



            }

        }

        $pdf->Ln(15);
        $pdf->SetFont('times','B',11);
        $pdf->Cell(80,7,'',0,0,'L',0);
        $pdf->SetTextColor(0,4,162);
        $pdf->Cell(75,7,'Total Dosis Entregadas:',0,0,'L',0);
        $pdf->Cell(100,7,$total,0,0,'L',0);

	    $pdf->Output();

    }

    public function informe5(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Relacion Dosis entregadas y Vacuna Aplicada';
        
        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Relación Dosis entregadas y Vacuna Aplicada";

        include 'cabezeraLand.php';

        $pdf->SetFont('helvetica','B',10);
        $pdf->SetTextColor(0,4,162);
        $pdf->SetFillColor(0,4,162);
        $pdf->SetX(160);
        $pdf->Cell(100,7,utf8_decode('Totales'),0,1,'C',0);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Times','B',12);
        $pdf->Cell(140,.2,'',0,0,'L',0);
        $pdf->Cell(110,.2,'',0,1,'L',1);
        $pdf->Ln(5);
        $pdf->SetFont('Times','B',12);
        $pdf->SetX(10);
        $pdf->Cell(40,7,'Veterinario',0,0,'L',0);
        $pdf->Cell(35,7,utf8_decode('Matrícula'),0,0,'L',0);
        $pdf->Cell(40,7,'Entrega',0,0,'L',0);
        $pdf->Cell(30,7,'Dosis',0,0,'L',0);
        $pdf->Cell(35,7,'Entregadas',0,0,'L',0);
        $pdf->Cell(35,7,'Aplicadas',0,0,'L',0);
        $pdf->Cell(35,7,'Sin Aplicar',0,1,'L',0);
        $pdf->Cell(250,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',11);
        $pdf->SetFillColor(0,0,0);

        $veterinarios = ControladorVeterinarios::ctrMostrarVeterinarios(null,null);

        $item = 'matricula';

        $item2 = 'campania';

        $campania = $_COOKIE['campania'];

        $totales = array('totalDosis'=>0,'totalVacunado'=>0);

        foreach ($veterinarios as $key => $value) {

            $distribuciones = ControladorAftosa::ctrMostrarDistribucion($item,$value['matricula'],$item2,$campania);
            
            if(!empty($distribuciones)){

                $pdf->Cell(40,7,utf8_decode($value['nombre']),0,0,'L',0);
                $pdf->Cell(35,7,$value['matricula'],0,0,'L',0);

                $first = true;
                
                $totalDosis = 0;

                foreach ($distribuciones as $key => $distribucion) {
                    
                    if($first){

                        $first = false;

                    }else{

                        $pdf->Cell(75,7,'',0,0,'L',0);

                    }
                    
                    $pdf->Cell(40,7,formatearFecha($distribucion['fechaEntrega']),0,0,'L',0);
                    $pdf->Cell(40,7,$distribucion['cantidad'],0,1,'L',0);
                
                    $totalDosis += $distribucion['cantidad'];

                    $totales['totalDosis'] += $distribucion['cantidad'];
                    
                };
                
                $pdf->Cell(145,7,'',0,0,'L',0);
                $pdf->SetFont('Times','B',11);
                $pdf->Cell(40,7,$totalDosis,0,0,'L',0);

                $tabla = 'actas';

                $campo = 'cantidadPar';

                $item = 'matricula';

                $totalVacunado = ControladorAftosa::ctrSumarDatos($tabla,$campo,$item,$value['matricula'],$item2,$campania);

                $totalVacunado = ($totalVacunado[0] != NULL) ? $totalVacunado[0] : 0;

                $totales['totalVacunado'] += $totalVacunado;

                $pdf->Cell(40,7,$totalVacunado,0,0,'L',0);
                $pdf->Cell(40,7,($totalDosis - $totalVacunado),0,1,'L',0);
                $pdf->Cell(250,.5,'',0,1,'L',1);
                $pdf->SetFont('Times','',11);
                $pdf->Ln(1);

            }

        }

        $pdf->Ln(15);
        $pdf->SetFont('times','B',11);
        $pdf->Cell(80,7,'',0,0,'L',0);
        $pdf->SetTextColor(0,4,162);
        $pdf->Cell(65,7,'Datos Finales de la relacion:',0,0,'L',0);
        $pdf->Cell(40,7,$totales['totalDosis'],0,0,'L',0);
        $pdf->Cell(40,7,$totales['totalVacunado'],0,0,'L',0);
        $pdf->Cell(40,7,($totales['totalDosis'] - $totales['totalVacunado']),0,0,'L',0);  
    
        $pdf->Output();

    }

    public function informe6(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Cant. de Establecimientos por distrito con detalle de categorías';
        
        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Cant. de Establecimientos por distrito con detalle de categorías del rodeo y total de hacienda";

        include 'cabezeraLand.php';

        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Times','B',11);
        $pdf->SetX(10);
        $pdf->Cell(40,7,'Localidad',0,0,'L',0);
        $pdf->Cell(30,7,'Cant. Estable.',0,0,'L',0);
        $pdf->Cell(15,7,'Vacas',0,0,'L',0);
        $pdf->Cell(25,7,'Vaquillonas',0,0,'L',0);
        $pdf->Cell(20,7,'Terneros',0,0,'L',0);
        $pdf->Cell(20,7,'Terneras',0,0,'L',0);
        $pdf->Cell(20,7,'Novillos',0,0,'L',0);
        $pdf->Cell(25,7,'Novillitos',0,0,'L',0);
        $pdf->Cell(20,7,'Toros',0,0,'L',0);
        $pdf->Cell(25,7,'Toritos',0,0,'L',0);
        $pdf->Cell(20,7,'TOTAL',0,1,'L',0);
        $pdf->Cell(260,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',11);
        
        $distritos = ControladorProductores::ctrMostrarProductoresDistinct(null,null,'distrito');

        $item = 'distrito';

        $item2 = 'campania';

        $campania = $_COOKIE['campania'];

        $campo = 'renspa';

        $totales = array('establecimientos'=>0,'vacas'=>0,'vaquillonas'=>0,'toros'=>0,'toritos'=>0,'novillos'=>0,'novillitos'=>0,'terneros'=>0,'terneras'=>0);


        foreach ($distritos as $key => $value) {

            if($value[0] != NULL){

                $data = ControladorAnimales::ctrSumarAnimalesInnerProductor($item,$value[0],$item2,$campania,$campo);

                $nombreDistrito = ControladorProductores::ctrMostrarLocation('departamento',8,'localidad',$value[0]);

                $pdf->Cell(40,7,utf8_decode($nombreDistrito[0]['nombre']),0,0,'L',0);
                $pdf->Cell(30,7,$data['establecimientos'],0,0,'L',0);
                $pdf->Cell(15,7,$data['vacas'],0,0,'L',0);
                $pdf->Cell(25,7,$data['vaquillonas'],0,0,'L',0);
                $pdf->Cell(20,7,$data['terneros'],0,0,'L',0);
                $pdf->Cell(20,7,$data['terneras'],0,0,'L',0);
                $pdf->Cell(20,7,$data['novillos'],0,0,'L',0);
                $pdf->Cell(25,7,$data['novillitos'],0,0,'L',0);
                $pdf->Cell(20,7,$data['toros'],0,0,'L',0);
                $pdf->Cell(25,7,$data['toritos'],0,0,'L',0);
                $pdf->Cell(20,7,($data['vacas'] + $data['vaquillonas'] + $data['terneros'] + $data['terneras'] + $data['novillo'] + $data['novillitos'] + $data['toros'] + $data['toritos']),0,1,'L',0);
                $pdf->Cell(255,.2,'',0,1,'L',1);

                $totales['establecimientos'] += $data['establecimientos'];
                $totales['vacas'] += $data['vacas'];
                $totales['vaquillonas'] += $data['vaquillonas'];
                $totales['terneros'] += $data['terneros'];
                $totales['terneras'] += $data['terneras'];
                $totales['novillos'] += $data['novillos'];
                $totales['novillitos'] += $data['novillitos'];
                $totales['toros'] += $data['toros'];
                $totales['toritos'] += $data['toritos'];

            }

        }

     



        
        $pdf->SetFont('helvetica','B',10);
        $pdf->SetTextColor(0,4,162);
        $pdf->Cell(40,7,'TOTALES',0,0,'L',0);
        $pdf->SetFont('Times','B',11);
        $pdf->Cell(30,7,$totales['establecimientos'],0,0,'L',0);
        $pdf->Cell(15,7,$totales['vacas'],0,0,'L',0);
        $pdf->Cell(25,7,$totales['vaquillonas'],0,0,'L',0);
        $pdf->Cell(20,7,$totales['terneros'],0,0,'L',0);
        $pdf->Cell(20,7,$totales['terneras'],0,0,'L',0);
        $pdf->Cell(20,7,$totales['novillos'],0,0,'L',0);
        $pdf->Cell(25,7,$totales['novillitos'],0,0,'L',0);
        $pdf->Cell(20,7,$totales['toros'],0,0,'L',0);
        $pdf->Cell(25,7,$totales['toritos'],0,0,'L',0);

        $totalAnimales = ($totales['vacas']+$totales['vaquillonas']+$totales['terneros']+$totales['terneras']+$totales['novillos']+$totales['novillitos']+$totales['toros']+$totales['toritos']);

        $pdf->Cell(25,7,$totalAnimales,0,1,'L',0);

    	$pdf->Output();


    }

    public function informe7(){

        //REQUERIMOS LA CLASE TCPDF

        include('fpdf.php');

        // ---------------------------------------------------------

        $titulo = 'Nómina de Vacunadores ordenada Alfabéticamente';
        
        $cabezera = "Sistema integrado de Vacunación Anti-Aftosa \n Nómina de Vacunadores ordenada Alfabéticamente";

        include 'cabezera.php';
        
        $pdf->SetFont('Times','B',12);
        $pdf->SetX(10);
        $pdf->Cell(60,7,'Nombre',0,0,'L',0);
        $pdf->Cell(25,7,'Matricula',0,0,'L',0);
        $pdf->Cell(20,7,'Tipo',0,0,'L',0);
        $pdf->Cell(50,7,'Domicilio',0,0,'L',0);
        $pdf->Cell(30,7,'Telefono',0,1,'L',0);
        $pdf->Cell(185,.5,'',0,1,'L',1);
        $pdf->SetFont('Times','',10);
        $pdf->SetFillColor(0,0,0);

        $veterinarios = ControladorVeterinarios::ctrMostrarVeterinarios(null,null);
        
        while ($fila = mysqli_fetch_array($query)) {
        $pdf->Cell(60,7,utf8_decode($fila['nombre']),0,0,'L',0);
        $pdf->Cell(25,7,$fila['matricula'],0,0,'L',0);
        $pdf->Cell(20,7,$fila['tipo'],0,0,'L',0);
        $pdf->Cell(50,7,$fila['domicilio'],0,0,'L',0);
        $pdf->Cell(30,7,$fila['telefono'],0,1,'L',0);	
        }

        $pdf->Output();

    
    }


}


if($informe){

    $informeGeneral = new informePDF();

    if($informe == 'informe3')
        $informeGeneral->matricula = $_GET['matricula'];

    $informeGeneral -> $informe();

}



?>