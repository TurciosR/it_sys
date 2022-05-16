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
$turno = $_REQUEST["turno"];
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
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE VENTA DIARIA";
    if($fini!="")
    {   
        list($a,$m,$d) = explode("-", $fini);
        
        $fech="$d DE ".meses($m)." DEL $a";
        
    }
    $sql="SELECT * FROM letras";
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
    $pdf->Cell(220,6,$fech,0,1,'C');
    $pdf->SetXY($set_x, $set_y+25);  
    $pdf->Cell(220,6,"TURNO: ".$turno,0,1,'C');
    

    $set_y = 40;
    $set_x = 6;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(20,5,utf8_decode("VENDEDOR"),1,1,'C',0);
    $pdf->SetXY($set_x+20, $set_y);
    $pdf->Cell(60,5,utf8_decode("DESCRIPCIÓN"),1,1,'C',0);
    $pdf->SetXY($set_x+80, $set_y);
    $pdf->Cell(30,5,"ESTILO",1,1,'C',0);
    $pdf->SetXY($set_x+110, $set_y);
    $pdf->Cell(20,5,"COLOR",1,1,'C',0);
    $pdf->SetXY($set_x+130, $set_y);
    $pdf->Cell(15,5,"TALLA",1,1,'C',0);
    $pdf->SetXY($set_x+145, $set_y);
    $pdf->Cell(20,5,"LINEA",1,1,'C',0);
    $pdf->SetXY($set_x+165, $set_y);
    $pdf->Cell(20,5,"CENTRO",1,1,'C',0);
    $pdf->SetXY($set_x+185, $set_y);
    $pdf->Cell(20,5,"METRO",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 45;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 0;
    $result = _query($sql);
    if(_num_rows($result)>0)
    {
        while($row1 = _fetch_array($result))
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
                $pdf->Cell(220,6,$fech,0,1,'C');
                $pdf->SetXY($set_x, $set_y+25);  
                $pdf->Cell(220,6,"TURNO: ".$turno,0,1,'C');
                $set_x = 6;
                $set_y = 40;
                $j=0;
                $pdf->SetFont('Latin','',8);   
            }
            $ltr = $row1["letra"];
            $letra = $row1["descrip"];

            $sql2 = _query("SELECT df.cantidad, df.id_producto, f.id_vendedor, productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre FROM detalle_factura as df, factura as f, productos, colores WHERE df.idtransace=f.idtransace AND productos.id_producto=df.id_producto AND productos.letra='$ltr' AND productos.id_color=colores.id_color AND f.id_sucursal='$id_sucursal' AND f.turno = '$turno' AND CAST(f.fecha_doc AS DATE) = '$fini' ORDER BY productos.descripcion ASC, productos.estilo ASC");
            if(_num_rows($sql2)>0)
            {
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,utf8_decode($letra),1,1,'C',0);
                $mm += 5;
                $j++;
            }
            while($row = _fetch_array($sql2))
            {
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
                    $pdf->Cell(220,6,$fech,0,1,'C');
                    $pdf->SetXY($set_x, $set_y+25);  
                    $pdf->Cell(220,6,"TURNO: ".$turno,0,1,'C');
                    $set_x = 6;
                    $set_y = 40;
                    $j=0;
                    $i=0;
                    $pdf->SetFont('Latin','',8);   
                }
                $descripcion = utf8_decode($row["descripcion"]);
                $nombre = utf8_decode($row["nombre"]);
                $estilo = $row["estilo"];
                $talla = $row["talla"];
                $id_vendedor = $row["id_vendedor"];
                $id_producto = $row["id_producto"];
                $existencias = $row["existencias"];
                $stoc1 = 0;
                $stoc2 = 0;
                $sqle1 = _query("SELECT existencias FROM stock WHERE id_producto='$id_producto' AND id_sucursal=1");
                $sqle2 = _query("SELECT existencias FROM stock WHERE id_producto='$id_producto' AND id_sucursal=2");
                if(_num_rows($sqle1)>0)
                {
                    $exis1 = _fetch_array($sqle1);
                    $stoc1 = $exis1["existencias"];
                }
                if(_num_rows($sqle2)>0)
                {
                    $exis2 = _fetch_array($sqle2);
                    $stoc2 = $exis2["existencias"];
                }
                $venta = $row["venta"];
            
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,$id_vendedor,1,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(60,5,$descripcion,1,1,'L',0);
                $pdf->SetXY($set_x+80, $set_y+$mm);
                $pdf->Cell(30,5,$estilo,1,1,'C',0);
                $pdf->SetXY($set_x+110, $set_y+$mm);
                $pdf->Cell(20,5,$nombre,1,1,'C',0);
                $pdf->SetXY($set_x+130, $set_y+$mm);
                $pdf->Cell(15,5,$talla,1,1,'C',0);
                $pdf->SetXY($set_x+145, $set_y+$mm);
                $pdf->Cell(20,5,"",1,1,'C',0);
                $pdf->SetXY($set_x+165, $set_y+$mm);
                $pdf->Cell(20,5,$stoc1,1,1,'C',0);
                $pdf->SetXY($set_x+185, $set_y+$mm);
                $pdf->Cell(20,5,$stoc2,1,1,'C',0);
                $mm += 5;
                $j++;
            }
            $i++;
            if($i==1)
            {    
                //Fecha de impresion y numero de pagina
                $pdf->SetXY(4, 270);
                $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
                $pdf->SetXY(193, 270);
                $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
            }
        }
    }
    $sql3 = _query("SELECT SUM(df.cantidad) as tot, f.id_vendedor FROM detalle_factura as df, factura as f WHERE df.idtransace=f.idtransace AND f.id_sucursal='$id_sucursal' AND f.turno = '$turno' AND CAST(f.fecha_doc AS DATE) = '$fini' GROUP BY  f.id_vendedor");
    $tot = "";
    while($dats = _fetch_array($sql3))
    {
        $tot .= "V ".$dats["id_vendedor"]." -> ".$dats["tot"]." P   ";  
    }
    $pdf->SetXY($set_x, $set_y+$mm);
    $pdf->Cell(205,5,$tot,1,1,'C',0);
ob_clean();
$pdf->Output("reporte_venta_diaria.pdf","I");