<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('P','mm', 'Letter');

$id_sucursal = $_SESSION["id_sucursal"];
$sql_su = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
$row_su = _fetch_array($sql_su);
$sql_su = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
$row_su = _fetch_array($sql_su);
$descripcion = $row_su["descripcion"];
$direccion = $row_su["direccion"];
$explode = explode("|", $direccion);
$d1 = $explode[0];
$d2 = $explode[1];

$telefono = $row_su["telefono"];
$id_sucursal1 = $_REQUEST["sucursal"];
$id_factura=$_REQUEST['id_factura'];
$sql="SELECT f.*, c.nombre as cliente, c.telefono1, c.telefono2, c.direccion, e.nombre as vendedor, ee.nombre as empleado, e.cargo,
c.retiene, c.retiene10, f.tipo_documento, c.nrc, ee.cargo
FROM factura AS f
JOIN clientes AS c ON c.id_cliente=f.id_cliente
JOIN empleados AS e ON e.id_empleado=f.id_vendedor
JOIN empleados AS ee ON ee.id_empleado=f.id_empleado
WHERE f.id_factura='$id_factura'";

$result=_query($sql);
$row=_fetch_array($result);

$fecha=$row['fecha'];
$total=$row['total'];
$numero_doc=$row['numero_doc'];
$exx = explode("_", $numero_doc);
$numero_doc = ltrim($exx[0], "0");
$cliente=Mayu(utf8_decode($row['cliente']));
$longitud = strlen($cliente);
$vendedor=$row['vendedor'];
$empleado=$row['empleado'];
$id_empleado=$row['id_empleado'];
$vigencia=$row['vigencia'];
$atencion = $row["atencion"];
$entrega = $row["entrega"];
$tel_cliente = $row["telefono1"];
$tipo_doc = $row["tipo_doc"];
$cargo = $row["cargo"];
$nrc = $row["nrc"];
$id_contrato = $row["id_contrato"];
$sql_contrato = _query("SELECT COUNT(*) AS cuenta, SUM(monto) AS total FROM cuota_contrato WHERE id_contrato = '$id_contrato' AND cancelada != 1");
$cuu = _num_rows($sql_contrato);
if($cuu > 0)
{
  $row_con = _fetch_array($sql_contrato);
  $n_cuotas = $row_con["cuenta"];
  $total_cuota = number_format($row_con["total"], 2,'.',',');
}
$monto = number_format($row["total"],2,'.',',');

$sql_detalle = _query("SELECT * FROM factura_detalle WHERE id_factura = '$id_factura'");
$cu = _num_rows($sql_detalle);
if($cu > 0)
{
  $row_de = _fetch_array($sql_detalle);
  $concepto = $row_de["concepto"];
}

list($entero, $decimal)=explode('.', $monto);
$enteros_txt=num2letras($entero);
$decimales_txt=num2letras($decimal);

if ($entero>1) {
  $dolar=" dolares";
} else {
  $dolar=" dolar";
}
$cadena_salida= $enteros_txt.$dolar." con ".$decimal."/100 ctvs";

$direccion_cliente = ucwords(Minu($row["direccion"]));
$atencion = str_replace(";","", $atencion);
$hora=$row['hora'];
$tipo = $row["tipo"];
$pago = $row["pago"];
if($pago == 0)
{
  $text_pago = "-   Pago al contado";
}
else
{
  $text_pago = "-   Crédito a 30 días";
}
$titulo=utf8_decode("RECIBO DE PAGO N° ").$numero_doc;
$fecha_detalle = "";

if($fecha!="")
{
    list($a,$m,$d) = explode("-", $fecha);
    $fecha_detalle= "San Miguel a los ".num2letras($d)." días del mes de ".Minu(meses($m))." de ".num2letras($a);
}

$fini = $_REQUEST["fini"];
$logo = "img/logo_sys.png";
$impress = "Impreso: ".date("d/m/Y");
//Encabezado General
class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    // Cabecera de página\
    public function Header()
    {

        //$this->Cell(185,5,"",1,1,'L');
    }

    public function Footer()
    {

    }
    public function setear($a,$b,$c)
    {
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
    }
}

$pdf=new PDF('P','mm', 'Letter');
$pdf->setear($d1,$d2,$telefono);
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(15);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,20);
$pdf->AddFont("latin","","latin.php");
$pdf->AddFont("bolt","","latin_bolt.php");
$pdf->AddPage();
//$pdf->AddPage();
$pdf->SetFont('Latin','',10);
$pdf->Image($logo,160,20,50,18);


$set_y = 12;
$pdf->SetFont('bolt','',15);
$pdf->SetXY($set_x, $set_y);
$pdf->MultiCell(220,6,utf8_decode(Mayu($descripcion)),0,'C',0);
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+6);
$pdf->Cell(220,5,$titulo,0,1,'C');

$set_x = 10;
$set_y = 25;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$sy+=10;
$pdf->SetFont('Latin','',10);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$text = "Yo ".trim($empleado).", recibí de ".trim(ucwords(strtolower($cliente))).", la cantidad de ".$cadena_salida." ($".$monto."), en concepto de ".$concepto.".";
$pdf->MultiCell(199,6,utf8_decode($text),0,'J',0);



list($entero1, $decimal1)=explode('.', $total_cuota);
$enteros_txt1=num2letras($entero1);
$decimales_txt1=num2letras($decimal1);

if ($entero1>1) {
  $dolar1=" dolares";
} else {
  $dolar1=" dolar";
}
$cadena_salida1= $enteros_txt1.$dolar1." con ".$decimal1."/100 ctvs";
$text_c = "Quedando pendiente ".num2letras($n_cuotas)." cuotas y la cantidad de ".$cadena_salida1;


if($id_contrato > 0)
{

  if($n_cuotas > 0)
  {
    if($total_cuota > 0)
    {
      $pdf->MultiCell(199,6,utf8_decode($text_c),0,'J',0);
    }
  }
}
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$text = "Por tanto y para los efectos que se estime convenientes se extiende en ".$fecha_detalle;
$pdf->MultiCell(199,6,utf8_decode($text),0,'J',0);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->SetFont('bolt','',10);
$set_y = $pdf->GetY();
$pdf->Image("img/firma_ing.jpeg",10,$set_y-16,50,18);
$pdf->Image("img/sello.jpeg",80,$set_y-16,50,18);
$pdf->Cell(195,5,utf8_decode($empleado),0,1,'L');
$pdf->Cell(195,5,utf8_decode(trim(ucfirst(strtolower($cargo)))),0,1,'L');
//$pdf->Image("img/sello.jpeg",70,90,50,18);

/////////////////////Segunda parte
$set_y = 151;
$set_x = 0;
$pdf->SetFont('Latin','',10);
$pdf->Image($logo,160,159,50,18);
$pdf->SetFont('bolt','',15);
$pdf->SetXY($set_x, $set_y);
$pdf->MultiCell(220,6,utf8_decode(Mayu($descripcion)),0,'C',0);
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+6);
$pdf->Cell(220,5,$titulo,0,1,'C');

$set_y = $set_y-1;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->SetFont('Latin','',10);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$text = "Yo ".trim($empleado).", recibí de ".trim(ucwords(strtolower($cliente))).", la cantidad de ".$cadena_salida." ($".$monto."), en concepto de ".$concepto.".";
$pdf->MultiCell(199,6,utf8_decode($text),0,'J',0);


if($id_contrato > 0)
{

  if($n_cuotas > 0)
  {
    if($total_cuota > 0)
    {
      $pdf->MultiCell(199,6,utf8_decode($text_c),0,'J',0);
    }
  }
}

$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$text = "Por tanto y para los efectos que se estime convenientes se extiende en ".$fecha_detalle;
$pdf->MultiCell(199,6,utf8_decode($text),0,'J',0);
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->Cell(195,5,utf8_decode(""),0,1,'L');
$pdf->SetFont('bolt','',10);
$set_y = $pdf->GetY();
$pdf->Image("img/firma_ing.jpeg",10,$set_y-16,50,18);
$pdf->Image("img/sello.jpeg",80,$set_y-16,50,18);
$pdf->Cell(195,5,utf8_decode($empleado),0,1,'L');
$pdf->Cell(195,5,utf8_decode(trim(ucfirst(strtolower($cargo)))),0,1,'L');
//$pdf->Image("img/sello.jpeg",70,230,50,18);
/*$pdf->SetXY($set_x+26, $set_y+15);
$pdf->Cell(195,5,utf8_decode(": ".$cliente),0,1,'L');
*/



/*$result1 = _query("SELECT dc.id_prod_serv, pr.descripcion, pr.marca, pr.modelo, pr.serie, dc.proviene, dc.vencimiento, dc.cantidad, dc.precio_venta, dc.id_presentacion, dc.subtotal
  FROM cotizacion_detalle as dc, productos as pr
  WHERE pr.id_producto=dc.id_prod_serv AND dc.id_cotizacion='$id_cotizacion'");*/


$cliente = str_replace(" ", "_", quitar_tildes($cliente));
ob_clean();
$pdf->Output("Recibo_".$cliente."_".date('d-m-Y').".pdf","I");
