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
    $set_x = 5;

    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->MultiCell(20,10,utf8_decode("N°"),0,'C',0);
    $pdf->SetXY($set_x+20, $set_y);  
    $pdf->Cell(75,10,utf8_decode("NOMBRE DEL EMPLEADO"),0,1,'L');
    $pdf->SetXY($set_x+95, $set_y);  
    $pdf->MultiCell(22,10,utf8_decode("VENTA C/IVA"),0,'R',0);
    $pdf->SetXY($set_x+117, $set_y);  
    $pdf->MultiCell(22,10,utf8_decode("DESCUENTO"),0,'R',0);
    $pdf->SetXY($set_x+139, $set_y);  
    $pdf->MultiCell(22,5,utf8_decode("VENTA-DESCUENTO S/IVA"),0,'R',0);
    $pdf->SetXY($set_x+161, $set_y);  
    $pdf->MultiCell(22,5,utf8_decode("COSTO VENTA"),0,'R',0);
    $pdf->SetXY($set_x+183, $set_y);  
    $pdf->MultiCell(22,10,utf8_decode("GANANCIA"),0,'R',0);

    $pdf->Line($set_x,$set_y,$set_x+205,$set_y);
    $pdf->Line($set_x,$set_y+10,$set_x+205,$set_y+10);

    $set_y = 52;
    $set_x = 5;
    $fila = 0;
    $i = 1;
    $sql_sucursal = _query("SELECT * FROM sucursal");
    $cuenta_sucuarsal = _num_rows($sql_sucursal);
    if($cuenta_sucuarsal > 0)
    {
        while ($row = _fetch_array($sql_sucursal)) 
        {
            $idsu = $row["id_sucursal"];
            $nombre_su = $row["descripcion"];

            if ($i > 1) {
                $fila = $fila+ 15;
            }
            
            $sql_empleado = _query("SELECT * FROM empleados WHERE id_sucursal = '$idsu' AND vendedor = 1 order by nombre");
            $cuenta_empleado = _num_rows($sql_empleado);
            $all_venta = 0;
            $all_descuento = 0;
            $all_venta_descuento = 0;
            $all_costo = 0;
            $all_ganancia = 0;
            $r = 1;
            if($cuenta_empleado)
            {
                $pdf->SetXY($set_x, $set_y+$fila);  
                $pdf->Cell(220,6,utf8_decode(Mayu($nombre_su)),0,1,'C');
                $pdf->Line($set_x,$set_y+6+$fila,$set_x+205,$set_y+6+$fila);
                $set_y = $set_y + 6;
                while ($row_empleado = _fetch_array($sql_empleado)) 
                {
                    $id_empleado = $row_empleado["id_empleado"];
                    $nombre = $row_empleado["nombre"];

                    $sql_datos = _query("SELECT sum(total) AS total, sum(descuento) AS descuento FROM factura WHERE id_vendedor = '$id_empleado' AND fecha_doc BETWEEN '$fini' AND '$fin' AND alias_tipodoc != 'DEV' AND id_sucursal = '$idsu'");
                    $row_datos = _fetch_array($sql_datos);

                    $total_venta = $row_datos["total"];
                    $descuento_venta = $row_datos["descuento"];

                    $sql_dev = _query("SELECT sum(total) AS total_dev, sum(descuento) AS descuento FROM factura WHERE id_vendedor = '$id_empleado' AND fecha_doc BETWEEN '$fini' AND '$fin' AND alias_tipodoc = 'DEV' AND id_sucursal = '$idsu'");
                    $row_dev = _fetch_array($sql_dev);

                    $total_dev = $row_dev["total_dev"];

                    $total_liquido = $total_venta - $total_dev;
                    $total_des = ($total_liquido/1.13)-$descuento_venta;

                    $sql_costo = _query("SELECT idtransace FROM factura WHERE id_vendedor = '$id_empleado' AND fecha_doc BETWEEN '$fini' AND '$fin' AND alias_tipodoc != 'DEV' AND id_sucursal = '$idsu'");
                    $cuenta_costo = _num_rows($sql_costo);
                    $costo_general = 0;
                    if($cuenta_costo > 0)
                    {
                        while ($row_costo = _fetch_array($sql_costo)) 
                        {
                            $idtransace = $row_costo["idtransace"];
                            $sql_detalle = _query("SELECT sum(costo) AS costo FROM detalle_factura WHERE idtransace = '$idtransace'");
                            $row_ss = _fetch_array($sql_detalle);
                            $costo_total = $row_ss["costo"];
                            $costo_general += $costo_total;
                        }
                    }


                    $pdf->SetXY($set_x, $set_y+$fila);  
                    $pdf->MultiCell(20,5,utf8_decode($r),0,'C',0);
                    $pdf->SetXY($set_x+20, $set_y+$fila);  
                    $pdf->Cell(75,5,utf8_decode(Mayu($nombre)),0,1,'L');
                    $pdf->SetXY($set_x+95, $set_y+$fila);  
                    $pdf->MultiCell(22,5,number_format(round($total_liquido, 2),2),0,'R',0);
                    $pdf->SetXY($set_x+117, $set_y+$fila);  
                    $pdf->MultiCell(22,5,number_format(round($descuento_venta, 2),2),0,'R',0);
                    $pdf->SetXY($set_x+139, $set_y+$fila);  
                    $pdf->MultiCell(22,5,number_format(round($total_des, 2),2),0,'R',0);
                    $pdf->SetXY($set_x+161, $set_y+$fila);  
                    $pdf->MultiCell(22,5,number_format(round($costo_general, 2),2),0,'R',0);

                    $ganancia = $total_des - $costo_general;

                    $pdf->SetXY($set_x+183, $set_y+$fila);  
                    $pdf->MultiCell(22,5,number_format(round($ganancia, 2),2),0,'R',0);

                    $all_venta += $total_liquido;
                    $all_descuento += $descuento_venta;
                    $all_venta_descuento += $total_des;
                    $all_costo += $costo_general;
                    $all_ganancia += $ganancia;
                    $fila += 5;
                    $r += 1;
                }
                
                $pdf->SetXY($set_x+95, $set_y+$fila);  
                $pdf->MultiCell(22,5,"$".number_format(round($all_venta, 2),2),0,'R',0);
                $pdf->SetXY($set_x+117, $set_y+$fila);  
                $pdf->MultiCell(22,5,"$".number_format(round($all_descuento, 2),2),0,'R',0);
                $pdf->SetXY($set_x+139, $set_y+$fila);  
                $pdf->MultiCell(22,5,"$".number_format(round($all_venta_descuento, 2),2),0,'R',0);
                $pdf->SetXY($set_x+161, $set_y+$fila);  
                $pdf->MultiCell(22,5,"$".number_format(round($all_costo, 2),2),0,'R',0);
                $pdf->SetXY($set_x+183, $set_y+$fila);  
                $pdf->MultiCell(22,5,"$".number_format(round($all_ganancia, 2),2),0,'R',0);

                $pdf->Line($set_x,$set_y+$fila,$set_x+205,$set_y+$fila);
            }
            $i += 1;
        }
    }
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
   

ob_clean();
$pdf->Output("reporte_costo_ganancia.pdf","I");