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
$id_sucursalr = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursalr'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$tel1 = $row_emp['telefono'];
$telefonos="TEL. ".$tel1;

    $id_sucursal = $_REQUEST["id_sucursal"];
    $min = $_REQUEST["min"];
    $max = $_REQUEST["max"];
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE EXISTENCIAS";
    $fech = "AL ".date("d")." DE ".utf8_decode(Mayu(utf8_decode(meses(date("m")))))." DEL ".date("Y");
    
    if($id_sucursal == "General")
    {
        $titt = "TODAS LAS SUCURSALES";
        $and = "";
        $sql = "SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.talla,productos.estilo, productos.ultcosto, productos.descuento, colores.nombre, stock1.existencias AS suc1, stock2.existencias AS suc2
        FROM productos 
        LEFT JOIN stock AS stock1 
        ON stock1.id_producto=productos.id_producto 
        LEFT JOIN stock as stock2 
        ON stock2.id_producto=productos.id_producto
        JOIN colores ON productos.id_color=colores.id_color";
        $and.= " WHERE stock1.id_sucursal=1";
        if($max != "" && $max>0)
        {
            $and .= " AND stock1.existencias <= '$max'";
            if($min !="")
            {

              $and .= " AND stock1.existencias >= '$min'";
            }
        }
         else if($min !="")
        {

              $and .= " AND stock2.existencias >= '$min'";
        } 
        $and.=" AND stock2.id_sucursal=2";
        if($max != "" && $max>0)
        {
            $and .= " AND stock2.existencias <= '$max'";
            if($min !="")
            {

                $and .= " AND stock2.existencias >= '$min'";
            }
        }
        else if($min !="")
        {

            $and .= " AND stock2.existencias >= '$min'";
        }
        $sql.=$and." ORDER BY productos.descripcion ASC, productos.talla ASC, stock1.existencias ASC, stock2.existencias ASC";
    }
    else
    {
        $sql_empresa1 = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";
        $resultado_emp1=_query($sql_empresa1);
        $row_emp1=_fetch_array($resultado_emp1);
        $titt = "SUCURSAL: ".utf8_decode(Mayu(utf8_decode(trim($row_emp1["descripcion"]))));
        $sql="SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre, stock.existencias,productos.ultcosto, productos.descuento FROM productos,stock, colores WHERE productos.id_producto=stock.id_producto AND productos.id_color=colores.id_color";
        $sql .= " AND stock.id_sucursal='$id_sucursal'";
        $and = "";
        if($max != "" && $max>0)
        {
          $and .= " AND stock.existencias <= '$max'";
          if($min !="")
          {

            $and .= " AND stock.existencias >= '$min'";
          }
        }
        else if($min !="")
        {

          $and .= " AND stock.existencias >= '$min'";
        }
        $sql.=$and." ORDER BY productos.descripcion ASC, productos.talla ASC, stock.existencias ASC";
    }
    if($max != "" && $max>0)
    {
        if($min !="")
        {

            $existenas = "PRODUCTOS CON EXISTENCIAS DE ".$min." A ".$max;
            if($min == $max)
            {
                $existenas = "PRODUCTOS CON ".$min." EXISTENCIA";
            }
        }
        else
        {
            $existenas = "PRODUCTOS CON ".$max." EXISTENCIA O MENOS";
        }
    }
    else if($min !="")
    {

        $existenas = "PRODUCTOS CON MAS DE ".$min." EXISTENCIAS";
    }
    else
    {
        $existenas = "GENERAL";  
    }
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);  
    $pdf->Cell(220,6,$title,0,1,'C');
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+5);  
    $pdf->Cell(220,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);  
    $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+15);  
    $pdf->Cell(220,6,$titt,0,1,'C');
    $pdf->SetXY($set_x, $set_y+20);  
    $pdf->Cell(220,6,$fech,0,1,'C');
    

    $set_y = 35;
    $set_x = 5;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(10,5,utf8_decode("N°"),1,1,'C',0);
    $pdf->SetXY($set_x+10, $set_y);
    $pdf->Cell(15,5,"BARCODE",1,1,'C',0);
    $pdf->SetXY($set_x+25, $set_y);
    $pdf->Cell(54,5,utf8_decode("DESCRIPCIÓN"),1,1,'C',0);
    $pdf->SetXY($set_x+79, $set_y);
    $pdf->Cell(26,5,"ESTILO",1,1,'C',0);
    $pdf->SetXY($set_x+105, $set_y);
    $pdf->Cell(20,5,"COLOR",1,1,'C',0);
    $pdf->SetXY($set_x+125, $set_y);
    $pdf->Cell(16,5,"TALLA",1,1,'C',0);
    $pdf->SetXY($set_x+141, $set_y);
    $pdf->Cell(16,5,"PRECIO",1,1,'C',0);
    $pdf->SetXY($set_x+157, $set_y);
    $pdf->Cell(13,5,"% DESC",1,1,'C',0);
    if($id_sucursal == "General")
    {
        $pdf->SetXY($set_x+170, $set_y);
        $pdf->Cell(10,5,"SUC1",1,1,'C',0);
        $pdf->SetXY($set_x+180, $set_y);
        $pdf->Cell(10,5,"SUC2",1,1,'C',0);
    }  
    else
    {
        $pdf->SetXY($set_x+170, $set_y);
        $pdf->Cell(20,5,"EXISTENCIA",1,1,'C',0);
    }
    $pdf->SetXY($set_x+190, $set_y);
    $pdf->Cell(15,5,"VALOR",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 40;
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
                $salto = 45;
            else
                $salto = 46;
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
                $pdf->Cell(220,6,$title,0,1,'C');
                $pdf->SetFont('Latin','',10);
                $pdf->SetXY($set_x, $set_y+5);  
                $pdf->Cell(220,6,$telefonos,0,1,'C');
                $pdf->SetXY($set_x, $set_y+10);  
                $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
                $pdf->SetXY($set_x, $set_y+15);  
                $pdf->Cell(220,6,$titt,0,1,'C');
                $pdf->SetXY($set_x, $set_y+20);  
                $pdf->Cell(220,6,$fech,0,1,'C');
                $set_x = 5;
                $set_y = 35;
                $j=0;
                $pdf->SetFont('Latin','',8);   
            }
            $barcode = $row["barcode"];
            $descripcion = utf8_decode($row["descripcion"]);
            $nombre = utf8_decode(ucwords(strtolower($row["nombre"])));
            $estilo = $row["estilo"];
            $talla = $row["talla"];
            $ultcosto = $row["ultcosto"];
            $descuento = $row["descuento"];
            $pdf->SetXY($set_x, $set_y+$mm);
            $pdf->Cell(10,5,$i,1,1,'C',0);
            $pdf->SetXY($set_x+10, $set_y+$mm);
            $pdf->Cell(15,5,$barcode,1,1,'C',0);
            $pdf->SetXY($set_x+25, $set_y+$mm);
            $pdf->Cell(54,5,$descripcion,1,1,'L',0);
            $pdf->SetXY($set_x+79, $set_y+$mm);
            $pdf->Cell(26,5,$estilo,1,1,'C',0);
            $pdf->SetXY($set_x+105, $set_y+$mm);
            $pdf->Cell(20,5,$nombre,1,1,'C',0);
            $pdf->SetXY($set_x+125, $set_y+$mm);
            $pdf->Cell(16,5,$talla,1,1,'C',0);
            $pdf->SetXY($set_x+141, $set_y+$mm);
            $pdf->Cell(16,5,$ultcosto,1,1,'C',0);
            $pdf->SetXY($set_x+157, $set_y+$mm);
            $pdf->Cell(13,5,$descuento,1,1,'C',0);
            $pdf->SetXY($set_x+170, $set_y+$mm);
            if($id_sucursal == "General")
            {
                $suc1 = $row["suc1"];
                $suc2 = $row["suc2"];
                $valoo = ($suc1+$suc2) * $ultcosto;
                $pdf->Cell(10,5,$suc1,1,1,'C',0);
                $pdf->SetXY($set_x+180, $set_y+$mm);
                $pdf->Cell(10,5,$suc2,1,1,'C',0);  
            }
            else
            {
                $existencias = $row["existencias"];
                $valoo = $existencias * $ultcosto;
                $pdf->Cell(20,5,$existencias,1,1,'C',0);   
            }
            $pdf->SetXY($set_x+190, $set_y+$mm);
            $pdf->Cell(15,5,$valoo,1,1,'C',0);
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