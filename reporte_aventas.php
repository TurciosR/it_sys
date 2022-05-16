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

    $min = $_REQUEST["l"];
    $fini = $_REQUEST["fini"];
    $fin = $_REQUEST["fin"];
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE PRODUCTOS MAS VENDIDOS";
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
    $sql="";
    $and = "SELECT sum(df.cantidad) as venta, productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre, stock.existencias,productos.precio1 FROM detalle_factura as df, factura as f, productos, stock, colores WHERE df.idtransace=f.idtransace AND productos.id_producto=df.id_producto AND productos.id_producto=stock.id_producto AND productos.id_color=colores.id_color AND stock.id_sucursal='$id_sucursal' AND f.fecha_doc BETWEEN '$fini' AND '$fin' GROUP BY df.id_producto ORDER BY venta DESC LIMIT $min";

    $existenas = "";
    if($min>0)
    {
        $existenas = "CANTIDAD: $min";
    }
    
    $sql.=$and;
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->MultiCell(220,6,$title,0,'C',0);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+5);  
    $pdf->Cell(220,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);  
    $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+15);  
    $pdf->Cell(220,6,$dara,0,1,'C');
    $pdf->SetXY($set_x, $set_y+20);  
    $pdf->Cell(220,6,$existenas,0,1,'C');
    $pdf->SetXY($set_x, $set_y+25);  
    $pdf->Cell(220,6,$fech,0,1,'C');
    

    $set_y = 40;
    $set_x = 5;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(10,5,utf8_decode("N°"),1,1,'C',0);
    $pdf->SetXY($set_x+10, $set_y);
    $pdf->Cell(15,5,"BARCODE",1,1,'C',0);
    $pdf->SetXY($set_x+25, $set_y);
    $pdf->Cell(60,5,utf8_decode("DESCRIPCIÓN"),1,1,'C',0);
    $pdf->SetXY($set_x+85, $set_y);
    $pdf->Cell(30,5,"ESTILO",1,1,'C',0);
    $pdf->SetXY($set_x+115, $set_y);
    $pdf->Cell(20,5,"COLOR",1,1,'C',0);
    $pdf->SetXY($set_x+135, $set_y);
    $pdf->Cell(15,5,"TALLA",1,1,'C',0);
    $pdf->SetXY($set_x+150, $set_y);
    $pdf->Cell(20,5,"PRECIO",1,1,'C',0);
    $pdf->SetXY($set_x+170, $set_y);
    $pdf->Cell(15,5,"VENTA",1,1,'C',0);
    $pdf->SetXY($set_x+185, $set_y);
    $pdf->Cell(20,5,"EXISTENCIA",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 45;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 1;
    $result = _query($sql);
    if(_num_rows($result)>0)
    {
        while($row = _fetch_array($result))
        {
            if($page==0)
                $salto = 44;
            else
                $salto = 45;
            if($j==$salto)
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
                $pdf->MultiCell(220,6,$title,0,'C',0);
                $pdf->SetFont('Latin','',10);
                $pdf->SetXY($set_x, $set_y+5);  
                $pdf->Cell(220,6,$telefonos,0,1,'C');
                $pdf->SetXY($set_x, $set_y+10);  
                $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
                $pdf->SetXY($set_x, $set_y+15);  
                $pdf->Cell(220,6,$dara,0,1,'C');
                $pdf->SetXY($set_x, $set_y+20);  
                $pdf->Cell(220,6,$existenas,0,1,'C');
                $pdf->SetXY($set_x, $set_y+25);  
                $pdf->Cell(220,6,$fech,0,1,'C');
                $set_x = 5;
                $set_y = 40;
                $j=0;
                $pdf->SetFont('Latin','',8);   
            }
            $barcode = $row["barcode"];
            $descripcion = utf8_decode($row["descripcion"]);
            $nombre = utf8_decode($row["nombre"]);
            $estilo = $row["estilo"];
            $talla = $row["talla"];
            $precio1 = $row["precio1"];
            $existencias = $row["existencias"];
            $venta = $row["venta"];
            $pdf->SetXY($set_x, $set_y+$mm);
            $pdf->Cell(10,5,$i,1,1,'C',0);
            $pdf->SetXY($set_x+10, $set_y+$mm);
            $pdf->Cell(15,5,$barcode,1,1,'C',0);
            $pdf->SetXY($set_x+25, $set_y+$mm);
            $pdf->Cell(60,5,$descripcion,1,1,'L',0);
            $pdf->SetXY($set_x+85, $set_y+$mm);
            $pdf->Cell(30,5,$estilo,1,1,'C',0);
            $pdf->SetXY($set_x+115, $set_y+$mm);
            $pdf->Cell(20,5,$nombre,1,1,'C',0);
            $pdf->SetXY($set_x+135, $set_y+$mm);
            $pdf->Cell(15,5,$talla,1,1,'C',0);
            $pdf->SetXY($set_x+150, $set_y+$mm);
            $pdf->Cell(20,5,$precio1,1,1,'C',0);
            $pdf->SetXY($set_x+170, $set_y+$mm);
            $pdf->Cell(15,5,$venta,1,1,'C',0);
            $pdf->SetXY($set_x+185, $set_y+$mm);
            $pdf->Cell(20,5,$existencias,1,1,'C',0);
            $mm += 5;
            $i++;
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
    }
ob_clean();
$pdf->Output("reporte_stock.pdf","I");