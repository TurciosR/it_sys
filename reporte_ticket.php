<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

$fecha = $_REQUEST["fecha"];
$id_sucursal = $_REQUEST["id_sucursal"];
$n = 1;
$sql_cabezera = _query("SELECT * FROM config_pos WHERE id_sucursal = '$id_sucursal' AND alias_tipodoc = 'TIK'");
$cue = _num_rows($sql_cabezera);
$row_cabe = _fetch_array($sql_cabezera);
$header1 = $row_cabe["header1"];
$header2 = $row_cabe["header2"];
$header3 = $row_cabe["header3"];
$header4 = $row_cabe["header4"];
$header5 = $row_cabe["header5"];
$header6 = $row_cabe["header6"];
$header7 = $row_cabe["header7"];
$footer1 = $row_cabe["footer1"];
$footer3 = $row_cabe["footer3"];
$footer4 = $row_cabe["footer4"];
$footer5 = $row_cabe["footer5"];
$footer6 = $row_cabe["footer6"];
$footer7 = $row_cabe["footer7"];


$sql_lista = _query("SELECT * FROM factura WHERE fecha_doc = '$fecha' AND id_sucursal = '$id_sucursal'  AND alias_tipodoc = 'TIK' ORDER BY numero_doc ASC");
$cuenta = _num_rows($sql_lista);

if($cuenta > 0)
{
        $pdf=new fPDF('P','mm');
        $pdf->SetMargins(10,5);
        $pdf->SetTopMargin(2);
        $pdf->SetLeftMargin(10);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true,1);
        $pdf->AddFont("courier new","","courier.php");
        
    while ($row = _fetch_array($sql_lista)) 
    {
        //$numero_docx = $rrr["numero_doc"];
        $idtransace = $row["idtransace"];

        $sql_detalles = _query("SELECT * FROM detalle_factura WHERE idtransace = '$idtransace'");
        $cuen_ss = _num_rows($sql_detalles);
        $ff = 10 * $cuen_ss;
        $nn = $ff+155;

        //$id_sucursal = $_SESSION["id_sucursal"];
        $pdf->AddPage('P', array(80, $nn));
        
        //$pdf->Image($logob,160,4,50,15);
        $set_x = 0;
        $set_y = 12;

        //Encabezado General
        $pdf->SetFont('courier new','',10);
        $pdf->SetXY($set_x, $set_y);  
        $pdf->Cell(80,5,utf8_decode($header1),0,1,'C');
        $pdf->SetXY($set_x, $set_y+5);  
        $pdf->Cell(80,5,utf8_decode($header2),0,1,'C');
        $pdf->SetXY($set_x, $set_y+10);  
        $pdf->Cell(80,5,utf8_decode($header3),0,1,'C');
        $pdf->SetXY($set_x, $set_y+15);  
        $pdf->Cell(80,5,utf8_decode($header4),0,1,'C');
        $pdf->SetXY($set_x, $set_y+20);  
        $pdf->Cell(80,5,utf8_decode($header5),0,1,'C');
        $pdf->SetXY($set_x, $set_y+25);  
        $pdf->Cell(80,5,utf8_decode($header6),0,1,'C');
        $pdf->SetXY($set_x, $set_y+30);  
        $pdf->Cell(80,5,utf8_decode($header7),0,1,'C');
        

        $set_y = 46;
        $set_x = 5;
        $numero_doc = intval($row["numero_doc"]);
        $numm = $row["numero_doc"];
        $hora = $row["hora"];
        $id_cliente = $row["id_cliente"];
        $id_empleado = $row["id_empleado"];
        $total_exento = $row["total_exento"];
        $total_gravado = $row["total_gravado"];
        $total = $row["total"]; 
        $descuento = $row["descuento"];
        $total_pago = $total - $descuento;
        $id_vendedor = $row["id_vendedor"];
        $sql_vendedor = _query("SELECT * FROM empleados WHERE id_empleado = '$id_vendedor'");
        $row_ven = _fetch_array($sql_vendedor);
        $nombre_ven = $row_ven["nombre"];
        $nv = explode(" ", $nombre_ven);
        $codv = count($nv);
        if($codv > 2)
        {
          $nv1 = $nv[0]." ".$nv[2];  
        }
        else
        {
            $nv1 = $nv[0]." ".$nv[1];
        }
        $efectivo = $row["efectivo"];
        $cambio = $row["cambio"];

        $sql_cliente = _query("SELECT * FROM clientes WHERE id_cliente = '$id_cliente'");
        $row_cliente = _fetch_array($sql_cliente);
        $nombre_cli = $row_cliente["nombre"];

        $sql_empleado = _query("SELECT * FROM empleados WHERE id_empleado = '$id_empleado'");
        $row_empleado = _fetch_array($sql_empleado);
        $nombre_em = $row_empleado["nombre"];
        $ne = explode(" ", $nombre_em);
        $code = count($ne);
        if ($code > 2) 
        {
            $ne1 = $ne[0]." ".$ne[2];
        }
        else
        {
            $ne1 = $ne[0]." ".$ne[1];
        }

        $pdf->SetXY($set_x, $set_y);  
        $pdf->Cell(70,5,utf8_decode("FECHA: ".ED($fecha)."      HORA:".$hora),0,1,'C');
        $pdf->SetXY($set_x, $set_y+5);  
        $pdf->Cell(70,5,utf8_decode("TIQUETE: ".$numm),0,1,'C');
        $pdf->SetXY($set_x, $set_y+10);  
        $pdf->Cell(70,5,utf8_decode("VENDEDOR: ".Mayu($nv1)),0,1,'C');
        $pdf->SetXY($set_x, $set_y+15);  
        $pdf->Cell(70,5,utf8_decode("CAJERO: ".Mayu($ne1)),0,1,'C');

        $set_y = 61;
        $set_x = 5;
        $pdf->SetFont('courier new','',8);

        $pdf->SetXY($set_x, $set_y+5);  
        $pdf->Cell(10,5,utf8_decode("CANT."),0,1,'L');
        $pdf->SetXY($set_x+10, $set_y+5);  
        $pdf->Cell(40,5,utf8_decode("DESCRIPCION."),0,1,'L');
        $pdf->SetXY($set_x+50, $set_y+5);  
        $pdf->Cell(10,5,utf8_decode("P.V."),0,1,'L');
        $pdf->SetXY($set_x+60, $set_y+5);  
        $pdf->Cell(10,5,utf8_decode("SUBT."),0,1,'L');

        $set_y = 71;
        $pdf->Line($set_x,$set_y,75,$set_y);
        $dd = 0;
        if($cuen_ss > 0)
        {
            while ($row_detalle = _fetch_array($sql_detalles)) 
            {
                $id_producto = $row_detalle["id_producto"];
                $cantidad = $row_detalle["cantidad"];
                $precio = $row_detalle["precio"];
                $gravado = $row_detalle["gravado"];
                $exento = $row_detalle["exento"];
                if($gravado > 0)
                {
                    $lee = "G";
                }
                else if($exento > 0)
                {
                    $lee = "E";
                }
                $sub = $precio * $cantidad;
                $sql_producto = _query("SELECT * FROM productos WHERE id_producto = '$id_producto'");
                $r_p = _fetch_array($sql_producto);
                $nombre = $r_p["descripcion"];
                $estilo = $r_p["estilo"];
                $id_color = $r_p["id_color"];
                $talla = $r_p["talla"];
                $sql_color = _query("SELECT * FROM colores WHERE id_color = '$id_color'");
                $row_color = _fetch_array($sql_color);
                $nombre_color = $row_color["nombre"];
                $xdatos['color'] = $nombre_color;

                $pdf->SetXY($set_x, $set_y+ $dd);  
                $pdf->Cell(10,5,utf8_decode($cantidad),0,1,'L');
                $pdf->SetXY($set_x+10, $set_y+ $dd);  
                $pdf->Cell(40,5,utf8_decode($nombre),0,1,'L');
                $pdf->SetXY($set_x+50, $set_y+ $dd);  
                $pdf->Cell(10,5,utf8_decode($precio),0,1,'L');
                $pdf->SetXY($set_x+60, $set_y+ $dd);  
                $pdf->Cell(10,5,utf8_decode($sub),0,1,'L');
                $pdf->SetXY($set_x+10, $set_y+ $dd+4);  
                $pdf->Cell(40,5,utf8_decode(Mayu($estilo." ".$nombre_color." ".$talla." (".$lee.")")),0,1,'L');

                $dd += 10;
            }
        }
        $pdf->Line($set_x,$set_y+$ff,75,$set_y+$ff);

        $set_y = 71+$ff;
        $pdf->SetFont('courier new','',10);
        $pdf->SetXY($set_x, $set_y);  
        $pdf->Cell(40,5,utf8_decode("TOTAL GRAVADO"),0,1,'L');
        $pdf->SetXY($set_x+40, $set_y);  
        $pdf->Cell(15,5,utf8_decode("$".$total_gravado),0,1,'L');
        $pdf->SetXY($set_x, $set_y+5);  
        $pdf->Cell(40,5,utf8_decode("TOTAL EXENTO"),0,1,'L');
        $pdf->SetXY($set_x+40, $set_y+5);  
        $pdf->Cell(15,5,utf8_decode("$".$total_exento),0,1,'L');
        $pdf->SetXY($set_x, $set_y+10);  
        $pdf->Cell(40,5,utf8_decode("TOTAL"),0,1,'L');
        $pdf->SetXY($set_x+40, $set_y+10);  
        $pdf->Cell(15,5,utf8_decode("$".$total),0,1,'L');
        $pdf->SetXY($set_x, $set_y+15);  
        $pdf->Cell(40,5,utf8_decode("TOTAL DESC"),0,1,'L');
        $pdf->SetXY($set_x+40, $set_y+15);  
        $pdf->Cell(15,5,utf8_decode("$".$descuento),0,1,'L');
        $pdf->SetXY($set_x, $set_y+20);  
        $pdf->Cell(40,5,utf8_decode("A PAGAR"),0,1,'L');
        $pdf->SetXY($set_x+40, $set_y+20);  
        $pdf->Cell(15,5,utf8_decode("$".$total_pago),0,1,'L');
        $pdf->SetXY($set_x, $set_y+30);  
        $pdf->Cell(35,5,utf8_decode("EFECTIVO $".$efectivo),0,1,'L');
        $pdf->SetXY($set_x+35, $set_y+30);  
        $pdf->Cell(35,5,utf8_decode("CAMBIO $".$cambio),0,1,'L');

        $set_y = 106+$ff;
        $pdf->SetFont('courier new','',8);
        $pdf->SetXY($set_x, $set_y);  
        $pdf->Cell(70,5,utf8_decode("E = EXENTO G = GRAVADO"),0,1,'C');
        $pdf->SetXY($set_x, $set_y+5);  
        $pdf->Cell(70,5,utf8_decode($footer1),0,1,'C');
        $pdf->SetXY($set_x, $set_y+10);  
        $pdf->Cell(70,5,utf8_decode($footer3),0,1,'C');
        $pdf->SetXY($set_x, $set_y+15);  
        $pdf->Cell(70,5,utf8_decode($footer4),0,1,'C');
        $pdf->SetXY($set_x, $set_y+20);  
        $pdf->Cell(70,5,utf8_decode($footer5),0,1,'C');
        $pdf->SetXY($set_x, $set_y+25);  
        $pdf->Cell(70,5,utf8_decode($footer6),0,1,'C');
        $pdf->SetXY($set_x, $set_y+30);  
        $pdf->Cell(70,5,utf8_decode($footer7),0,1,'C');
        $mm += 5;
    }
}
else
{
    $pdf=new fPDF('P','mm');
    $pdf->SetMargins(10,5);
    $pdf->SetTopMargin(2);
    $pdf->SetLeftMargin(10);
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(true,1);
    $pdf->AddFont("courier new","","courier.php");
    $pdf->AddPage('P', array(80, 80));
    $set_x = 0;
    $set_y = 12;

    //Encabezado General
    $pdf->SetFont('courier new','',9);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->Cell(80,5,utf8_decode("NO SE HA ENCONTRADO NINGUN TIQUETE"),0,1,'C');
}
    
ob_clean();
$pdf->Output("reporte_ticket.pdf","I");