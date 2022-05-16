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
$pdf->AddFont("bolt","","latin_bolt.php");
$id_sucursal = $_SESSION["id_sucursal"];
$sql_su = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
$row_su = _fetch_array($sql_su);
$descripcion = $row_su["descripcion"];
$direccion = $row_su["direccion"];
$explode = explode("|", $direccion);
$d1 = $explode[0];
$d2 = $explode[1];

$telefono = $row_su["telefono"];
$id_sucursal1 = $_REQUEST["sucursal"];
$id_garantia=$_REQUEST['id_garantia'];
$sql="SELECT g.*, c.nombre as cliente, c.direccion, c.telefono1, c.telefono2, c.nrc FROM garantia_cliente as g, clientes as c WHERE id_garantia = '$id_garantia' AND c.id_cliente = g.id_cliente ";

$result=_query($sql);
$row=_fetch_array($result);

$fecha=$row['fecha'];
$total=$row['total'];
$numero_doc=$row['numero_doc'];
$cliente= ucwords(Minu($row["cliente"]));
//$cliente= ucwords(Minu($row['']));
$alias_tipodoc = $row["alias_tipodoc"];
$nrc = $row["nrc"];
$tel_cliente = $row["telefono1"];
if($alias_tipodoc == "TIK")
{
  $texto_doc = "TIQUETE";
  $longitud_doc = strlen($numero_doc);
}
if($alias_tipodoc == "COF")
{
  $texto_doc = "FACTURA";
  $longitud_doc = strlen($numero_doc);
}
if($alias_tipodoc == "CCF")
{
  $texto_doc = "CREDITO FISCAL";
  $longitud_doc = strlen($numero_doc);
}
$correlativo = $row["correlativo"];
$direccion_cliente = ucwords(Minu($row["direccion"]));
$longitud_dir = strlen($direccion_cliente);
$longitud = strlen($cliente);
$titulo=utf8_decode("GARANTíA N° ").$correlativo;
$fecha_detalle = "";

if($fecha!="")
{
    list($a,$m,$d) = explode("-", $fecha);
    $fecha_detalle= "San Miguel, ".$d." de ".ucwords(Minu(meses($m)))." de ".$a;
}

$txt_c = "Cliente: ";

$fini = $_REQUEST["fini"];
$logo = "img/logo_sys.png";
$impress = "Impreso: ".date("d/m/Y");
//Encabezado General
$pdf->AddPage();
$pdf->SetFont('Latin','',10);
$pdf->Image($logo,160,20,50,18);


$set_y = 12;
$pdf->SetFont('bolt','',15);
$pdf->SetXY($set_x, $set_y);
$pdf->MultiCell(220,6,utf8_decode(Mayu($descripcion)),0,'C',0);

$set_x = 10;
$set_y = 25;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(195,5,utf8_decode($fecha_detalle),0,1,'L');
$pdf->SetXY($set_x, $set_y+10);
$pdf->Cell(195,5,$titulo,0,1,'L');
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+15);
$pdf->Cell(195,5,utf8_decode($texto_doc." #".$numero_doc),0,1,'L');

$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+20);
$pdf->Cell(195,5,utf8_decode("CLIENTE"),0,1,'L');
$pdf->SetFont('Latin','',10);
$pdf->SetXY($set_x+26, $set_y+20);
$pdf->Cell(195,5,utf8_decode(": ".$cliente),0,1,"L");
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+25);
$pdf->Cell(195,5,utf8_decode("DIRECCIÓN"),0,1,'L');
$pdf->SetFont('Latin','',10);
$pdf->SetXY($set_x+26, $set_y+25);
$pdf->Cell(195,5,utf8_decode(": ".$direccion_cliente),0,1,'L');
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+30);
$pdf->Cell(195,5,utf8_decode("TELÉFONO"),0,1,'L');
$pdf->SetFont('Latin','',10);
$pdf->SetXY($set_x+26, $set_y+30);
$pdf->Cell(195,5,utf8_decode(": ".$tel_cliente),0,1,'L');
$si = 0;
if($alias_tipodoc == "CCF")
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+35);
  $pdf->Cell(195,5,utf8_decode("NRC"),0,1,'L');
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x+26, $set_y+35);
  $pdf->Cell(195,5,utf8_decode(": ".$nrc),0,1,'L');
  $si = 5;
}

$set_y = 65+$si;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(25,5,utf8_decode("CANTIDAD"),0,1,'C');
$pdf->SetXY($set_x+30, $set_y);
$pdf->Cell(110,5,utf8_decode("DESCRIPCIÓN"),0,1,'L');
$pdf->SetXY($set_x+135, $set_y);
#$pdf->Cell(30,5,utf8_decode("PRECIO"),0,1,'R');
#$pdf->SetXY($set_x+165, $set_y);
#$pdf->Cell(30,5,utf8_decode("TOTAL"),0,1,'R');
$set_y = 70+$si;
$pdf->Line($set_x, $set_y, 205, $set_y);

$result1 = _query("SELECT gd.*, p.descripcion, p.modelo, p.marca FROM garantia_cte_det as gd, productos as p WHERE gd.id_garantia = '$id_garantia' AND gd.id_producto = p.id_producto");
if(_num_rows($result1)>0)
{
  $iva=$row_su['iva']/100;//iva por sucursal
  $suma_subtotal=0;
  $suma_iva=0;
  $iva_cu=0;
  $sum_subt = 0;
  while($row = _fetch_array($result1))
  {
    $rr = 5;
    if($page==0)
    $salto = 40;
    else
    $salto = 46;
    if($j==$salto)
    {
      $page++;
      $pdf->AddPage();
      $pdf->SetFont('Arial','',10);
      $pdf->Image($logo,9,4,50,18);
      $set_x = 0;
      $set_y = 6;
      //Encabezado General
      $pdf->SetFont('Arial','',12);
      $pdf->SetXY($set_x, $set_y);
      $pdf->Cell(220,10,$empresa,0,1,'C');
      $pdf->SetFont('Latin','',10);
      $pdf->SetXY($set_x, $set_y+5);
      $pdf->Cell(220,10,$sucursal,0,1,'C');
      $pdf->SetXY($set_x, $set_y+10);
      $pdf->Cell(220,10,$telefonos,0,1,'C');
      $pdf->SetXY($set_x, $set_y+15);
      $pdf->Cell(220,10,$titulo,0,1,'C');
      $mm = 0;
      $i = 0;
    }
    $dias_garantia = $row["dias_garantia"];
    $tipo_periodo = $row["tipo_periodo"];
    if($tipo_periodo == "0")
    {
      if($dias_garantia <= 1)
      {
        $tex_perido = "Día";
      }
      else
      {
        $tex_perido = "Días";
      }
    }
    if($tipo_periodo == "1")
    {
      if($dias_garantia <= 1)
      {
        $tex_perido = "Mes";
      }
      else
      {
        $tex_perido = "Meses";
      }
    }
    if($tipo_periodo == "2")
    {
      if($dias_garantia <= 1)
      {
        $tex_perido = "Año";
      }
      else
      {
        $tex_perido = "Años";
      }
    }
    $nombre_producto = $row["nombre_producto"];
    $modelo = $row["modelo"];
    $marca = $row["marca"];
    $serie = $row["serie"];
    $cantidad = $row["cantidad"];
    $precio = $row["precio"];
    $subt = round($precio * $cantidad, 2);
    if($alias_tipodoc=="COF"){
      $subt_mostrar = $subt;
      $precio_producto=$precio_venta;
    }
    if($alias_tipodoc=="CCF"){
      $subt_mostrar = ($subt/(1+$iva));
      $iva_cu=$subt*$iva;
      $precio_producto=($precio_venta/(1+$iva));
    }
    if($alias_tipodoc=="TIK"){
      $subt_mostrar = $subt;
      $precio_producto=$precio_venta;
    }
    $descripcion = utf8_decode(Mayu(utf8_decode($row["descripcion"])));

      $pdf->SetFont('bolt','',10);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(25,5,utf8_decode($cantidad),0,1,'C');
      $pdf->SetXY($set_x+30, $set_y+$mm);
      $pdf->Cell(110,5,ucwords(Minu($descripcion)),0,1,'L');
      $pdf->SetXY($set_x+135, $set_y+$mm);
      #$pdf->Cell(30,5,utf8_decode("$ ".number_format($precio, 2)),0,1,'R');
      #$pdf->SetXY($set_x+165, $set_y+$mm);
      #$pdf->Cell(30,5,utf8_decode("$ ".number_format($subt, 2)),0,1,'R');
      if($marca != "")
      {
        $pdf->SetFont('bolt','',10);
        $pdf->SetXY($set_x, $set_y+$mm+$rr);
        $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
        $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("Marca: ".$marca),0,1,'L');
        $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $rr += 5;
      }
      if($modelo != "")
      {

        $pdf->SetFont('bolt','',10);
        $pdf->SetXY($set_x, $set_y+$mm+$rr);
        $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
        $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("Modelo: ".$modelo),0,1,'L');
        $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $rr += 5;
      }
      if($serie != "")
      {
        $pdf->SetFont('bolt','',10);
        $pdf->SetXY($set_x, $set_y+$mm+$rr);
        $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
        $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("Serie: ".$serie),0,1,'L');
        $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $rr += 5;
      }
      if($dias_garantia != "")
      {
        $pdf->SetFont('bolt','',10);
        $pdf->SetXY($set_x, $set_y+$mm+$rr);
        $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
        $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
        $pdf->Cell(110,5,utf8_decode("Tiempo de garantia: ".$dias_garantia." ".$tex_perido),0,1,'L');
        $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
        $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
        $rr += 5;
      }
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(20,$rr,utf8_decode(""),0,1,'C');
      $pdf->SetXY($set_x+20, $set_y+$mm);
      $pdf->Cell(115,$rr,utf8_decode(""),0,1,'L');
      $pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->Cell(30,$rr,utf8_decode(""),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,$rr,utf8_decode(""),0,1,'R');
      $sum_subt += $subt;
      $mm += $rr;
      $i++;
      $j++;
      $suma_subtotal+= $subt_mostrar;
      $suma_iva+=$iva_cu;
    }
    $pdf->Line($set_x, $set_y+$mm, 205, $set_y+$mm);
    if($alias_tipodoc == "CCF")
    {
      $iva_total = round($sum_subt * $iva, 3);
      $total_final = $sum_subt + $iva_total;

      $pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("SUMA (SIN IVA): "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($sum_subt, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+5);
      $pdf->Cell(30,5,utf8_decode("IVA: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+5);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($iva_total, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+10);
      $pdf->Cell(30,5,utf8_decode("SUB-TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+10);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+15);
      $pdf->Cell(30,5,utf8_decode("TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+15);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $set_y = $mm + 100;
    }
    else
    {
      $total_final = $sum_subt;
      $pdf->SetXY($set_x+135, $set_y+$mm);
      #$pdf->Cell(30,5,utf8_decode("TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      #$pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $set_y = $mm + 85;
    }



    //$set_y = $mm + 100;
    $pdf->SetFont('bolt','',10);
    $pdf->SetXY($set_x, $set_y-3);
    $pdf->Cell(195,5,utf8_decode("Términos y condiciones de garantía"),0,1,'L');
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(195,5,utf8_decode("-------------------------------------------------------------------------------------------------------------------------------------------------------------------------"),0,1,'L');

    $sql_politicas = _query("SELECT * FROM politicas_ga WHERE id_garantia = '$id_garantia'");
    $cc = _num_rows($sql_politicas);
    if($cc > 0)
    {
      while ($row_p = _fetch_array($sql_politicas))
      {
        $descip_p = $row_p["descripcion"];

        $ylinea1f=$pdf->GetY();
        $xlinea1f=$pdf->GetX();
        $pdf->SetXY($xlinea1f, $ylinea1f);
        $pdf->Cell(3,5,"* ",0,1,'L');
        $pdf->SetXY($xlinea1f+3, $ylinea1f);
        $pdf->MultiCell(195,5,utf8_decode($descip_p),0,'L',0);
        $rw+=5;
      }
    }


    $set_y = $mm + 150;
    /*$pdf->SetXY($set_x, $set_y);
    $pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    $pdf->Line($set_x+5, $set_y+5, 70, $set_y+5);*/


    $pdf->SetXY($set_x, $set_y);
    #$pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    #$pdf->Line($set_x+5, $set_y+5, 70, $set_y+5);
    $pdf->SetXY($set_x, $set_y+5);
    #$pdf->Cell(65,5,utf8_decode("ACEPTADO CLIENTE"),0,1,'C');
    $pdf->Image("img/sello.jpeg",80,$set_y,50,18);
    $pdf->Image("img/firma_ing.jpeg",20,$set_y,50,18);

    // $pdf->SetXY($set_x, $set_y+10);
    // $pdf->Cell(65,5,utf8_decode(Mayu($cargo)),0,1,'C');
    // $pdf->SetXY($set_x+80, $set_y);
    // $pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    // $pdf->Line($set_x+85, $set_y+5, 155, $set_y+5);
    // $pdf->SetXY($set_x+80, $set_y+5);
    // $pdf->Cell(65,5,utf8_decode("ACEPTADO CLIENTE"),0,1,'C');



  }
  $pdf->SetFont('bolt','',9);
  $pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
  $pdf->SetFillColor(30,111,158);
  $pdf->SetXY(4, 260);
  $pdf->Cell(104,10,"",0,1,'L',1);
  $pdf->SetXY(4, 260);
  $pdf->Cell(104,5,utf8_decode("Teléfonos: ".$telefono),0,1,'L',1);
  $pdf->SetXY(4, 265);
  $pdf->Cell(104,5,utf8_decode("www.opensolutionsystems.com"),0,1,'L',1);
  $pdf->SetFont('bolt','',9);
  $pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
  //$pdf->SetFillColor(215,145,15);
  $pdf->SetFillColor(177,177,177);
  $pdf->SetXY(108, 260);
  $pdf->Cell(104,10,"",0,1,'L',1);
  $pdf->SetXY(108, 260);
  $pdf->Cell(104,5,utf8_decode($d1),0,1,'L',1);
  $pdf->SetFont('bolt','',8.7);
  $pdf->SetXY(108, 265);
  $pdf->Cell(104,5,utf8_decode($d2),0,1,'L',1);


$cliente = str_replace(" ", "_", $cliente);
ob_clean();
$pdf->Output("Garantia_".$cliente.".pdf","I");
