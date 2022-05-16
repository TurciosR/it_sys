<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('L','mm', 'Letter');
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,1);
$pdf->AddFont("latin","","latin.php");
$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$tel1 = $row_emp['telefono'];
$telefonos="TEL. ".$tel1;

$id_sucursal1 = $_REQUEST["sucursal"];
$dara = "TODAS LAS SUCURSALES";
if($id_sucursal1 != "General")
{
    $sql_empresa1 = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal1'";
    $resultado_emp1=_query($sql_empresa1);
    $row_emp1=_fetch_array($resultado_emp1);
    $direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp1["descripcion"]))));
    $dara = "SUCURSAL: ".$direccion;
}

    $id_producto = $_REQUEST["id_producto"];
    $fini = $_REQUEST["fini"];
    $fin = $_REQUEST["fin"];
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "KARDEX DE PRODUCTO";
    if($fini!="" && $fin!="")
    {   
        list($a,$m,$d) = explode("-", $fini);
        list($a1,$m1,$d1) = explode("-", $fin);
        if($a ==$a1)
        {
            if($m==$m1)
            {
                $fech="DEL $d AL $d1 DE ".meses($m)." DE $a";
            }
            else
            {
                $fech="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        }
        else
        {
            $fech="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }
    }
    $sql = "SELECT * FROM kardex WHERE id_producto='$id_producto' AND id_sucursal_origen='$id_sucursal1' AND CAST(fechadoc AS DATE) BETWEEN '$fini' AND '$fin' ORDER BY CAST(fechadoc as DATE) ASC";
    
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->MultiCell(280,6,$title,0,'C',0);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+5);  
    $pdf->Cell(280,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);  
    $pdf->Cell(280,6,"NIT: 1217-090236-001-0",0,1,'C');
    $pdf->SetXY($set_x, $set_y+15);  
    $pdf->Cell(280,6,"NRC: 2404-0",0,1,'C');
    $pdf->SetXY($set_x, $set_y+20);  
    $pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+25);  
    $pdf->Cell(280,6,$dara,0,1,'C');
    $pdf->SetXY($set_x, $set_y+30);  
    $pdf->Cell(280,6,$fech,0,1,'C');
    
    $sql_aux = _query("SELECT pr.descripcion, pr.estilo, cl.nombre, pr.talla FROM productos AS pr LEFT JOIN colores AS cl ON pr.id_color=cl.id_color WHERE id_producto='$id_producto'");
    $dats_aux = _fetch_array($sql_aux);
    $pdf->SetXY($set_x+4, $set_y+37);
    $pdf->Cell(100,5,"PRODUCTO: ".utf8_decode($dats_aux["descripcion"]),0,1,'L',0);
    $pdf->SetXY($set_x+4, $set_y+42);
    $pdf->Cell(40,5,"ESTILO: ".utf8_decode($dats_aux["estilo"]),0,1,'L',0);
    $pdf->SetXY($set_x+44, $set_y+42);
    $pdf->Cell(40,5,"COLOR: ".utf8_decode($dats_aux["nombre"]),0,1,'L',0);
    $pdf->SetXY($set_x+84, $set_y+42);
    $pdf->Cell(40,5,"TALLA: ".utf8_decode($dats_aux["talla"]),0,1,'L',0);

    $set_y = 55;
    $set_x = 4;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(18,10,"FECHA",1,1,'C',0);
    $pdf->SetXY($set_x+18, $set_y);
    $pdf->Cell(18,10,"TIPO DOC",1,1,'C',0);
    $pdf->SetXY($set_x+36, $set_y);
    $pdf->Cell(18,10,"NUM. DOC",1,1,'C',0);
    $pdf->SetXY($set_x+54, $set_y);
    $pdf->Cell(54,5,"ENTRADA",1,1,'C',0);
    $pdf->SetXY($set_x+54, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+72, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+90, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+108, $set_y);
    $pdf->Cell(54,5,"SALIDA",1,1,'C',0);
    $pdf->SetXY($set_x+108, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+126, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+144, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+162, $set_y);
    $pdf->Cell(54,5,"SALDO",1,1,'C',0);
    $pdf->SetXY($set_x+162, $set_y+5);
    $pdf->Cell(18,5,"CANTIDAD",1,1,'C',0);
    $pdf->SetXY($set_x+180, $set_y+5);
    $pdf->Cell(18,5,"COSTO",1,1,'C',0);
    $pdf->SetXY($set_x+198, $set_y+5);
    $pdf->Cell(18,5,"SUBTOTAL",1,1,'C',0);
    $pdf->SetXY($set_x+216, $set_y);
    $pdf->Cell(56,10,"PROVEEDOR",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 65;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 0;
    $result = _query($sql);
    if(_num_rows($result)>0)
    {
        $entrada = 0;
        $salida = 0;
        while($row = _fetch_array($result))
        {
            if($page==0)
                $salto = 28;
            else
                $salto = 32;
            if($j>=$salto)
            {
                $page++;
                $pdf->AddPage();
                $pdf->SetFont('Latin','',10);
                $pdf->Image($logo,9,4,50,18);
                //$pdf->Image($logo1,245,8,24.5,24.5);
                $set_x = 0;
                $set_y = 6;
                $mm=0;
                //Encabezado General
                $pdf->SetFont('Latin','',12);
                $pdf->SetXY($set_x, $set_y);  
                $pdf->MultiCell(280,6,$title,0,'C',0);
                $pdf->SetFont('Latin','',10);
                $pdf->SetXY($set_x, $set_y+5);  
                $pdf->Cell(280,6,$telefonos,0,1,'C');
                $pdf->SetXY($set_x, $set_y+10);  
                $pdf->Cell(280,6,"NIT: 1217-090236-001-0",0,1,'C');
                $pdf->SetXY($set_x, $set_y+15);  
                $pdf->Cell(280,6,"NRC: 2404-0",0,1,'C');
                $pdf->SetXY($set_x, $set_y+20);  
                $pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
                $pdf->SetXY($set_x, $set_y+25);  
                $pdf->Cell(280,6,$dara,0,1,'C');
                $pdf->SetXY($set_x, $set_y+30);  
                $pdf->Cell(280,6,$fech,0,1,'C');
                $set_x = 4;
                $set_y = 45;
                $j=0;
                $i=0;
                $pdf->SetFont('Latin','',8);   
            }
            $fechadoc = ED($row["fechadoc"]);
            $centr = $row["cantidade"];
            $csal = $row["cantidads"];
            $alias_tipodoc = $row["alias_tipodoc"];
            $numero_doc = intval($row["numero_doc"]);
            $ultcosto = $row["ultcosto"];
            $stock_actual = $row["stock_actual"];
            $id_proveedor = $row["id_proveedor"];
            $entrada += $centr;
            $salida += $csal;
            $lwidth = 5;
            if($id_proveedor>0)
            {
                $sql2 = _query("SELECT nombre FROM proveedores WHERE id_proveedor='".$id_proveedor."'");
                $datos2 = _fetch_array($sql2);
                $nombr = utf8_decode($datos2["nombre"]);
                if(ceil(strlen($nombr))/2 > 20)
                {
                    $nom = divtextlin($nombr, 30);
                    $nn = 0;
                    foreach ($nom as $nnon)
                    {
                        $pdf->SetXY($set_x+216, $set_y+$mm+$nn); 
                        $pdf->Cell(56,5,$nnon,0,0,'L',0);
                        $nn += 5;
                        $j++;
                    }
                    $lwidth = $nn;
                    $pdf->SetXY($set_x+216, $set_y+$mm);
                    $pdf->Cell(56,$lwidth,"",1,1,'C',0);
                }
                else
                {
                    $pdf->SetXY($set_x+216, $set_y+$mm); 
                    $pdf->Cell(56,$lwidth,$nombr,1,1,'C',0); 
                }
            }
            else
            {
                $pdf->SetXY($set_x+216, $set_y+$mm); 
                $pdf->Cell(56,$lwidth,"",1,1,'C',0);    
            }
            $pdf->SetXY($set_x, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$fechadoc,1,1,'C',0);
            $pdf->SetXY($set_x+18, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$alias_tipodoc,1,1,'C',0);
            $pdf->SetXY($set_x+36, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$numero_doc,1,1,'C',0);
            $pdf->SetXY($set_x+54, $set_y+$mm);
            if($centr * $ultcosto > 0)
            {
                $pdf->Cell(18,$lwidth,$centr,1,1,'C',0);
                $pdf->SetXY($set_x+72, $set_y+$mm);
                $pdf->Cell(18,$lwidth,$ultcosto,1,1,'C',0);
                $pdf->SetXY($set_x+90, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format(($centr * $ultcosto), 2),1,1,'C',0);    
            }
            else
            {
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
                $pdf->SetXY($set_x+72, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
                $pdf->SetXY($set_x+90, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
            }
            $pdf->SetXY($set_x+108, $set_y+$mm);
            if($csal * $ultcosto > 0)
            {
                $pdf->Cell(18,$lwidth,$csal,1,1,'C',0);
                $pdf->SetXY($set_x+126, $set_y+$mm);
                $pdf->Cell(18,$lwidth,$ultcosto,1,1,'C',0);
                $pdf->SetXY($set_x+144, $set_y+$mm);
                $pdf->Cell(18,$lwidth,number_format(($csal * $ultcosto), 2),1,1,'C',0);    
            }
            else
            {
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
                $pdf->SetXY($set_x+126, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
                $pdf->SetXY($set_x+144, $set_y+$mm);
                $pdf->Cell(18,$lwidth,"",1,1,'C',0);
            }
            $pdf->SetXY($set_x+162, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$stock_actual,1,1,'C',0);
            $pdf->SetXY($set_x+180, $set_y+$mm);
            $pdf->Cell(18,$lwidth,$ultcosto,1,1,'C',0);
            $pdf->SetXY($set_x+198, $set_y+$mm);
            $pdf->Cell(18,$lwidth,number_format(($stock_actual * $ultcosto), 2),1,1,'C',0);
            
            $mm += $lwidth;
            $j++;
            $i++;
            if($i==1)
            {    
                //Fecha de impresion y numero de pagina
                $pdf->SetXY(4, 210);
                $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
                $pdf->SetXY(258, 210);
                $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
            }
        }
        $pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(18,6,"",1,1,'C',0);
        $pdf->SetXY($set_x+18, $set_y+$mm);
        $pdf->Cell(36,6,"TOTAL ENTRADA",1,1,'C',0);
        $pdf->SetXY($set_x+54, $set_y+$mm);
        $pdf->Cell(18,6,$entrada,1,1,'C',0);
        $pdf->SetXY($set_x+72, $set_y+$mm);
        $pdf->Cell(36,6,"TOTAL SALIDA",1,1,'C',0);
        $pdf->SetXY($set_x+108, $set_y+$mm);
        $pdf->Cell(18,6,$salida,1,1,'C',0);
        $pdf->SetXY($set_x+126, $set_y+$mm);
        $pdf->Cell(146,6,"",1,1,'C',0);
    }
ob_clean();
$pdf->Output("kardex.pdf","I");