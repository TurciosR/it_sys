<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('P','mm', 'Letter');
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
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$tel1 = $row_emp['telefono'];
$nrc = $row_emp['nrc'];
$nit = $row_emp['nit'];
$telefonos="TEL. ".$tel1;

    $min = $_REQUEST["l"];
    $fini = $_REQUEST["fini"];
    $fin = $_REQUEST["ffin"];
    $fini1 = ED($_REQUEST["fini"]);
    $fin1 = ED($_REQUEST["ffin"]);
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE COSTOS Y GANANCIAS";
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
    

    
    
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    //$pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,45,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->MultiCell(220,6,$title,0,'C',0);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+6);  
    $pdf->Cell(220,6,$nombre_a.": ".$direccion,0,1,'C');
    $pdf->SetXY($set_x, $set_y+11);  
    $pdf->Cell(220,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+16);  
    $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+21);  
    $pdf->Cell(220,6,$fech,0,1,'C');
    


    $set_y = 40;
    $set_x = 15;

    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->MultiCell(20,5,utf8_decode("LETRA"),0,'L',0);
    $pdf->SetXY($set_x+20, $set_y);  
    $pdf->Cell(115,5,utf8_decode("DESCRIPCIÓN"),0,1,'L');
    $pdf->SetXY($set_x+135, $set_y);  
    $pdf->MultiCell(25,5,utf8_decode("VENTA C/IVA"),0,'R',0);
    $pdf->SetXY($set_x+160, $set_y);  
    $pdf->MultiCell(25,5,utf8_decode("COSTO VENTA"),0,'R',0);

    $pdf->Line($set_x,$set_y,$set_x+185,$set_y);
    $pdf->Line($set_x,$set_y+5,$set_x+185,$set_y+5);

    $set_y = 46;
    $set_x = 15;
    $fila = 0;
    $i = 1;
    $sql_letra = _query("SELECT * FROM letras");
    $cuenta_letras = _num_rows($sql_letra);
    $all_costo = 0;
    $all_venta = 0;
    if($cuenta_letras > 0)
    {
        while ($row = _fetch_array($sql_letra)) 
        {
            $letra = $row["letra"];
            $text = $row["descrip"];

            $pdf->SetXY($set_x, $set_y+$fila);  
            $pdf->MultiCell(20,5,utf8_decode($letra),0,'L',0);
            $pdf->SetXY($set_x+20, $set_y+$fila);  
            $pdf->Cell(115,5,utf8_decode($text),0,1,'L');

            $sql_costo = _query("SELECT SUM(df.gravado) as total_venta, SUM(df.costo) as total_costo, p.letra FROM detalle_factura as df, productos as p, letras as l, factura as f WHERE f.idtransace = df.idtransace AND f.fecha_doc BETWEEN '$fini' AND '$fin' AND p.id_producto = df.id_producto AND p.letra = '$letra' GROUP by p.letra ORDER BY l.letra");
            $cuenta_costo = _num_rows($sql_costo);
            if($cuenta_costo > 0)
            {
                while ($row_costo = _fetch_array($sql_costo)) 
                {
                    $total_venta = $row_costo["total_venta"];
                    $total_costo = $row_costo["total_costo"];
                    $letra_x = $row_costo["letra"];

                    $pdf->SetXY($set_x+135, $set_y+$fila);  
                    $pdf->MultiCell(25,5,number_format(round($total_venta, 2),2),0,'R',0);
                    $pdf->SetXY($set_x+160, $set_y+$fila);  
                    $pdf->MultiCell(25,5,number_format(round($total_costo, 2),2),0,'R',0);
                    $all_venta += $total_venta;
                    $all_costo += $total_costo;
                }
            }
            else
            {
                $pdf->SetXY($set_x+135, $set_y+$fila);  
                $pdf->MultiCell(25,5,"0.00",0,'R',0);
                $pdf->SetXY($set_x+160, $set_y+$fila);  
                $pdf->MultiCell(25,5,"0.00",0,'R',0);
            }

            $fila+=5;
        }
    }
    $pdf->SetXY($set_x+135, $set_y+$fila);  
    $pdf->MultiCell(25,5,"$".number_format(round($all_venta, 2), 2),0,'R',0);
    $pdf->SetXY($set_x+160, $set_y+$fila);  
    $pdf->MultiCell(25,5,"$".number_format(round($all_costo, 2), 2),0,'R',0);
    $pdf->Line($set_x,$set_y+$fila,$set_x+185,$set_y+$fila);
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
   

ob_clean();
$pdf->Output("reporte_venta_costo.pdf","I");