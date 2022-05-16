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
    $titulo = "REPORTE FISCAL";
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
    

    $existenas = "";
    if($min>0)
    {
        $existenas = "CANTIDAD: $min";
    }
    
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,45,18);
    $set_x = 5;
    $set_y = 6;

    //Encabezado General
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
    $pdf->SetXY($set_x, $set_y+26);  
    $pdf->Cell(220,6,"NRC: ".$nrc."  NIT: ".$nit,0,1,'C');
    ///////////////////////////////////////////////////////////////////////

    /*$pdf->SetXY($set_x+130, $set_y+7);  
    $pdf->Cell(75,6,$fech,0,1,'L');
    $pdf->SetXY($set_x+130, $set_y+14);  
    $pdf->Cell(40,6,"FECHA: ".date('d-m-Y'),0,1,'L');
    $pdf->SetXY($set_x+170, $set_y+14);  
    $pdf->Cell(35,6,"HORA: ".date('H-i-s'),0,1,'L');*/

    /*$pdf->Line($set_x,$set_y,$set_x+100,$set_y);
    $pdf->Line($set_x,$set_y+20,$set_x+100,$set_y+20);

    $pdf->Line($set_x,$set_y,$set_x+100,$set_y);
    $pdf->Line($set_x,$set_y+20,$set_x+100,$set_y+20);

    $pdf->Line($set_x,$set_y,$set_x,$set_y+20);
    $pdf->Line($set_x+205,$set_y,$set_x+205,$set_y+20);

    $pdf->Line($set_x+100,$set_y,105,$set_y+20);
    $pdf->Line($set_x+130,$set_y,135,$set_y+20);*/

    /*$pdf->SetXY($set_x, $set_y+16);  
    $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+21);  
    $pdf->Cell(220,6,$existenas,0,1,'C');
    $pdf->SetXY($set_x, $set_y+26);  
    $pdf->Cell(220,6,$fech,0,1,'C');*/
    

    $set_y = 40;
    $set_x = 5;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(19,10,utf8_decode("FECHA"),1,1,'C',0);
    $pdf->SetXY($set_x+19, $set_y);
    $pdf->Cell(19,10,utf8_decode("SUCURSAL"),1,1,'C',0);
    $pdf->SetXY($set_x+38, $set_y);
    $pdf->Cell(49,5,utf8_decode("TIQUETE"),1,1,'C',0);
    $pdf->SetXY($set_x+87, $set_y);
    $pdf->Cell(49,5,"FACTURA",1,1,'C',0);
    $pdf->SetXY($set_x+136, $set_y);
    $pdf->Cell(49,5,"CREDITO FISCAL",1,1,'C',0);
    $pdf->SetXY($set_x+185, $set_y);
    $pdf->MultiCell(19,5,"TOTAL GENERAL",1,'C',0);
    /////////////////////////////////////////////////////////
    /////////////////
    $pdf->SetXY($set_x+38, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),1,1,'C',0);
    $pdf->SetXY($set_x+54.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),1,1,'C',0);
    $pdf->SetXY($set_x+70.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",1,1,'C',0);
    //////////////////
    $pdf->SetXY($set_x+87, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),1,1,'C',0);
    $pdf->SetXY($set_x+103.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),1,1,'C',0);
    $pdf->SetXY($set_x+119.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",1,1,'C',0);
    //////////////////
    $pdf->SetXY($set_x+136, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),1,1,'C',0);
    $pdf->SetXY($set_x+152.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),1,1,'C',0);
    $pdf->SetXY($set_x+168.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 50;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 1;
    $fk = $fini1;
    $fs = 1;
    $f1 = 0;
    while(strtotime($fk) <= strtotime($fin1))
    {
        if($page==0)
            $salto = 43;
        else
            $salto = 51;
        if($j==$salto)
        {
            $page++;
            $pdf->AddPage();
            //$pdf->SetFont('Latin','',10);
            //$pdf->Image($logo,9,4,50,18);
            //$pdf->Image($logo1,245,8,24.5,24.5);
            $set_y = 6;
            $f1=0;
            $set_x = 5;
            $mm=0;
            //Encabezado General
            $j=0;
            //$pdf->SetFont('Latin','',8);   
        }
        $fk = MD($fk);
        $sql_efectivo = _query("SELECT * FROM factura WHERE fecha_doc = '$fk' AND id_sucursal = '$id_sucursal'");
        $cuenta = _num_rows($sql_efectivo);
        $sql_min_max=_query("SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha_doc = '$fk' AND alias_tipodoc = 'TIK' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha_doc = '$fk' AND alias_tipodoc = 'FAC' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha_doc = '$fk' AND alias_tipodoc = 'CCF' AND id_sucursal = '$id_sucursal' AND anulada = 0 UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha_doc = '$fk' AND alias_tipodoc = 'DEV' AND id_sucursal = '$id_sucursal' UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha_doc = '$fk' AND alias_tipodoc = 'RES' AND id_sucursal = '$id_sucursal'");
        $cuenta_min_max = _num_rows($sql_min_max);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $total_tike_e = 0;
        $total_factura_e = 0;
        $total_credito_fiscal_e = 0;
        $total_reserva_e = 0;
        $total_dev_e = 0;
        $total_tike_g = 0;
        $total_factura_g = 0;
        $total_credito_fiscal_g = 0;
        $total_reserva_g = 0;
        $total_dev_g = 0;
        $total_tike = 0;
        $total_factura = 0;
        $total_credito_fiscal = 0;
        $tike_min = 0;
        $tike_max = 0;
        $factura_min = 0;
        $factura_max = 0;
        $credito_fiscal_min = 0;
        $credito_fiscal_max = 0;
        $dev_min = 0;
        $dev_max = 0;
        $res_min = 0;
        $res_max = 0;
        $t_tike = 0;
        $t_factuta = 0;
        $t_credito = 0;
        $t_dev = 0;
        $t_res = 0;
        $t_recerva = 0;
        $total_contado = 0;
        $total_tarjeta = 0;
        $lista_dev = "";
        if($cuenta > 0)
        {
            while ($row_corte = _fetch_array($sql_efectivo))
            {
                $id_trans = $row_corte["idtransace"];
                $alias_tipodoc = $row_corte["alias_tipodoc"];
                $anulada = $row_corte["anulada"];
                if(!$anulada)
                {
                    $anulada = "";
                }
                $total_exento = $row_corte["total_exento"];
                $total_gravado = $row_corte["total_gravado"];
                $numero_doc = $row_corte["numero_doc"];
                $tipo_pago = $row_corte["tipopago"];
                $total_iva = $row_corte["total_iva"];
                $total = $row_corte["total"];

                if($alias_tipodoc == 'TIK' && $anulada != 1 )
                {
                    $total_tike_e = $total_tike_e + $total_exento;
                    $total_tike_g = $total_tike_g + $total_gravado;
                    $t_tike += 1;
                    if($tipo_pago == 1)
                    {
                        $total_contado += $total;
                    }
                    else if($tipo_pago == 2)
                    {
                        $total_tarjeta += $total;
                    }
                    $total_tike += $total_tike_e + $total_tike_g;
                }
                else if($alias_tipodoc == 'FAC' && $anulada == "")
                {
                    $total_factura_e = $total_factura_e + $total_exento;
                    $total_factura_g = $total_factura_g + $total_gravado;
                    $t_factuta += 1;
                    if($tipo_pago == 1)
                    {
                        $total_contado += $total;
                    }
                    else if($tipo_pago == 2)
                    {
                        $total_tarjeta += $total;
                    }
                    $total_factura += $total_factura_e + $total_factura_g;
                }
                else if($alias_tipodoc == 'CCF' && $anulada == "")
                {
                    $total_credito_fiscal_e = $total_credito_fiscal_e + $total_exento;
                    $total_credito_fiscal_g = $total_credito_fiscal_g + $total_gravado + $total_iva;
                    $t_credito += 1;
                    if($tipo_pago == 1)
                    {
                        $total_contado += $total;
                    }
                    else if($tipo_pago == 2)
                    {
                        $total_tarjeta += $total;
                    }
                    $total_credito_fiscal += $total_credito_fiscal_e + $total_credito_fiscal_g;
                }
                else if($alias_tipodoc == "DEV" && $anulada == "")
                {
                    $total_dev_e = $total_dev_e + $total_exento;
                    $total_dev_g = $total_dev_g + $total_gravado;
                    $lista_dev .= $numero_doc.",".$total."|";
                    $t_dev += 1;

                }
                else if($alias_tipodoc == "RES" && $anulada == "")
                {
                    $total_reserva_e = $total_reserva_e + $total_exento;
                    $total_reserva_g = $total_reserva_g + $total_gravado;
                    $t_res += 1;
                    if($tipo_pago == 1)
                    {
                        $total_contado += $total;
                    }
                    else if($tipo_pago == 2)
                    {
                        $total_tarjeta += $total;
                    }
                }



            }
        }
        if($cuenta_min_max)
        {
            $i = 1;
            while ($row_min_max = _fetch_array($sql_min_max))
            {
                if($i == 1)
                {
                    $tike_min = $row_min_max["minimo"];
                    $tike_max = $row_min_max["maximo"];
                    if($tike_min != "")
                    {
                        $tike_min = $tike_min;
                    }
                    else
                    {
                        $tike_min = 0;
                    }

                    if($tike_max != "")
                    {
                        $tike_max = $tike_max;
                    }
                    else
                    {
                        $tike_max = 0;
                    }
                }
                if($i == 2)
                {
                    $factura_min = $row_min_max["minimo"];
                    $factura_max = $row_min_max["maximo"];
                    if($factura_min != "")
                    {
                        $factura_min = $factura_min;
                    }
                    else
                    {
                        $factura_min = 0;
                    }

                    if($factura_max != "")
                    {
                        $factura_max = $factura_max;
                    }
                    else
                    {
                        $factura_max = 0;
                    }
                }
                if($i == 3)
                {
                    $credito_fiscal_min = $row_min_max["minimo"];
                    $credito_fiscal_max = $row_min_max["maximo"];
                    if($credito_fiscal_min != "")
                    {
                        $credito_fiscal_min = $credito_fiscal_min;
                    }
                    else
                    {
                        $credito_fiscal_min = 0;
                    }

                    if($credito_fiscal_max != "")
                    {
                        $credito_fiscal_max = $credito_fiscal_max;
                    }
                    else
                    {
                        $credito_fiscal_max = 0;
                    }
                }
                if($i == 4)
                {
                    $dev_min = $row_min_max["minimo"];
                    $dev_max = $row_min_max["maximo"];
                }
                if($i == 5)
                {
                    $res_min = $row_min_max["minimo"];
                    $res_max = $row_min_max["maximo"];
                }
                $i += 1;
            }
        }
        /*$total_tike = $total_tike_e + $total_tike_g;
        $total_factura = $total_factura_e + $total_factura_g;
        $total_credito_fiscal = $total_credito_fiscal_e + $total_credito_fiscal_g;*/
        $total_general = $total_tike + $total_factura + $total_credito_fiscal;
        $fk = ED($fk);
        $pdf->SetXY($set_x, $set_y+$f1);
        $pdf->Cell(19,5,utf8_decode($fk),1,1,'C',0);
        $pdf->SetXY($set_x+19, $set_y+$f1);
        $pdf->Cell(19,5,utf8_decode($id_sucursal),1,1,'C',0);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+38, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($tike_min)),1,1,'C',0);
        $pdf->SetXY($set_x+54.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($tike_max)),1,1,'C',0);
        $pdf->SetXY($set_x+70.6, $set_y+$f1);
        $pdf->Cell(16.4,5,$total_tike,1,1,'C',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+87, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode($factura_min),1,1,'C',0);
        $pdf->SetXY($set_x+103.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode($factura_max),1,1,'C',0);
        $pdf->SetXY($set_x+119.6, $set_y+$f1);
        $pdf->Cell(16.4,5,$total_factura,1,1,'C',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+136, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode($credito_fiscal_min),1,1,'C',0);
        $pdf->SetXY($set_x+152.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode($credito_fiscal_max),1,1,'C',0);
        $pdf->SetXY($set_x+168.6, $set_y+$f1);
        $pdf->Cell(16.4,5,$total_credito_fiscal,1,1,'C',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+185, $set_y+$f1);
        $pdf->Cell(19,5,$total_general,1,1,'C',0);
        /////////////////////////////////
        $fk = sumar_dias($fk,1);
        $f1+=5;
        $fs += 1;
        $j++;
        if($j==1)
        {    
            //Fecha de impresion y numero de pagina
            $pdf->SetXY(4, 270);
            $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
            $pdf->SetXY(193, 270);
            $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
        }
    } 
   

ob_clean();
$pdf->Output("reporte_fiscak.pdf","I");