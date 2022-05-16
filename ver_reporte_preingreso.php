<?php
require('_core.php');
require('fpdf/fpdf.php');
$id_pedido=$_REQUEST['idtransace'];
$sql="SELECT idtransace,fecha,pares,items,proveedores.nombre,proveedores.id_proveedor,monto FROM pedidos INNER JOIN proveedores ON proveedores.id_proveedor=pedidos.id_proveedor WHERE idtransace='$id_pedido'";
$result=_query($sql);
$count=_num_rows($result);
for ($i=0;$i<$count;$i++) {
    $row=_fetch_array($result);
    $fecha=$row['fecha'];
    $pares=$row['pares'];
    $items=$row['items'];
    $id_proveedor=$row['id_proveedor'];
}

$cabebera=utf8_decode("PREINGRESO Nº ").$row['idtransace'].", ".trim(utf8_decode($row['nombre'])).", PARES: ".$row['pares'].", ITEMS: ".$row['items'].", MONTO:$".number_format($row['monto'],2).", FECHA:$row[fecha]";

$iva=0;
$sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
$result_IVA=_query($sql_iva);
$row_IVA=_fetch_array($result_IVA);
$iva=$row_IVA['iva']/100;
$monto_retencion1=$row_IVA['monto_retencion1'];
$monto_retencion10=$row_IVA['monto_retencion10'];
$monto_percepcion=$row_IVA['monto_percepcion'];

$sql0="SELECT percibe, retiene, retiene10 FROM proveedores  WHERE id_proveedor='$id_proveedor'";
$resultados = _query($sql0);
$numrows= _num_rows($resultados);
$rws = _fetch_array($resultados);
$retiene1=$rws['retiene'];
$retiene10=$rws['retiene10'];
$percibe=$rws['percibe'];
if ($percibe==1) {
    $percepcion=round(1/100, 2);
} else {
    $percepcion=0;
}

if ($retiene1==1) {
    $retencion1=round(1/100, 2);
} else {
    $retencion1=0;
}

if ($retiene10==1) {
    $retencion10=round(10/100, 2);
} else {
    $retencion10=0;
}
$sql1=_query("SELECT p.descripcion,p.estilo,p.talla,c.nombre,p.id_producto,d.cantidad,  p.exento,p.ultcosto,d.precio1,d.ultcosto from productos AS p JOIN colores AS c ON (p.id_color=c.id_color) JOIN detalle_pedidos AS d ON p.id_producto=d.id_producto where d.idtransace='$id_pedido'");
class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    // Cabecera de página\
    public function Header()
    {

        // Logo
        $this->Image('img/logoopenpyme.jpg', 10, 10, 33);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 10);
        // Movernos a la derecha
        // Título
        $this->SetX(43);
        $this->Cell(130, 4, 'CALZADO MAYORGA ', 0, 1, 'C');
        $this->SetX(43);
        $this->Cell(130, 4, ''.utf8_decode("PREINGRESO Nº ").$this->a, 0, 1, 'C');
        $this->SetX(43);
        $this->Cell(130, 4, ''.$this->b, 0, 1, 'C');
        $this->SetX(43);
        $this->Cell(130, 4, 'PARES: '.$this->c." , ".'ITEMS: '.$this->d, 0, 1, 'C');

        $this->SetX(43);
        $this->Cell(130, 4, 'MONTO: $'.number_format($this->e,2), 0, 1, 'C');
        // Salto de línea
        $this->Ln(5);
    }

    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
        $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function setear($preingreso,$empresa,$pares,$items,$monto,$fecha)
    {
      # code...
      $this->a=$preingreso;
      $this->b=$empresa;
      $this->c=$pares;
      $this->d=$items;
      $this->e=$monto;
      $this->f=$fecha;
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$pdf->setear($row['idtransace'],trim(utf8_decode($row['nombre'])),$row['pares'],$row['items'],$row['monto'],$row['fecha']);
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();
$set_y=$pdf->GetY();
$set_x=$pdf->GetX();
$pdf->SetXY($set_x, $set_y);
$pdf->AddFont('latin','','latin.php');
$pdf->SetFont('latin', '', 9);
$pdf->Cell(20, 5, 'COD.', 1, 0, 'L');
$pdf->Cell(51, 5, 'PRODUCTO', 1, 0, 'L');
$pdf->Cell(20, 5, 'ESTILO.', 1, 0, 'L');
$pdf->Cell(30, 5, 'COLOR.', 1, 0, 'L');
$pdf->Cell(15, 5, 'TALLA.', 1, 0, 'L');
$pdf->Cell(20, 5, 'P.VENTA.', 1, 0, 'L');
$pdf->Cell(20, 5, 'CANT.', 1, 0, 'L');
$pdf->Cell(20, 5, 'COSTO.', 1, 1, 'L');

$totalfactura=0;
while ($row=_fetch_array($sql1)) {
    $pdf->Cell(20, 5, $row['id_producto'], 1, 0, 'L');
    $pdf->Cell(51, 5, "".utf8_decode(Mayu(utf8_decode($row['descripcion']))), 1, 0, 'L');
    $pdf->Cell(20, 5, utf8_decode($row['estilo']), 1, 0, 'L');
    $pdf->Cell(30, 5, utf8_decode(substr($row['nombre'], 0, 13)), 1, 0, 'L');/**/
    $pdf->Cell(15, 5, utf8_decode($row['talla']), 1, 0, 'L');
    $pdf->Cell(20, 5, "".number_format($row['precio1'] , 2 ,"." , ","), 1, 0, 'R');
    $pdf->Cell(20, 5, $row['cantidad'], 1, 0, 'R');
    $pdf->Cell(20, 5, "".number_format($row['ultcosto'], 2 ,"." , ","), 1, 1, 'R');

    $totalfactura=$totalfactura+round(($row['ultcosto']* $row['cantidad']),2);
}

$totalcosto=$totalfactura;
$total_retencion1=0;
$total_percepcion=0;
$total_retencion10=0;




$iva = round(($totalfactura * $iva), 4);

if ($totalfactura >= $monto_percepcion) {
    $total_percepcion = round(($totalfactura * $percepcion), 4);
}
if ($totalfactura >= $monto_retencion1) {
    $total_retencion1 = round(($totalfactura * $retencion1), 4);
}
if ($totalfactura >= $monto_retencion10) {
    $total_retencion10 = round(($totalfactura * $retencion10), 4);
}

$totalfactura =$totalfactura+ $total_percepcion;
$totalfactura =$totalfactura+ $total_retencion1;
$totalfactura =$totalfactura+ $total_retencion10;
$totalfactura =$totalfactura+ $iva;

$totalfactura = round($totalfactura, 2);

$pdf->Cell(156, 5, "".utf8_decode('TOTAL CANT, COSTO.'), 1, 0, 'L');
$pdf->Cell(20, 5, $pares, 1, 0, 'R');
$pdf->Cell(20, 5,"".number_format( $totalcosto,2), 1, 1, 'R');

$pdf->Cell(176, 5, "".utf8_decode('IVA'), 1, 0, 'L');
$pdf->Cell(20, 5,"". number_format(round($iva,2 ),2), 1, 1, 'R');

$pdf->Cell(176, 5, "".utf8_decode('PERCEPCION'), 1, 0, 'L');
$pdf->Cell(20, 5,"". number_format(round($total_percepcion,2 ),2), 1, 1, 'R');

$pdf->Cell(176, 5, "".utf8_decode('RETENCION'), 1, 0, 'L');
$pdf->Cell(20, 5,"". number_format(round(($total_retencion1+$total_retencion10),2 ),2), 1, 1, 'R');

$pdf->Cell(176, 5, "".utf8_decode('TOTAL PREINGRESO'), 1, 0, 'L');
$pdf->Cell(20, 5,"". number_format($totalfactura,2), 1, 1, 'R');

$ylinea=$pdf->GetY();
if ($ylinea<255) {
    # code...
    $pdf->SetY(-20);
    $set_x = 20;
    $ylinea=$pdf->GetY();
    $pdf->Line(70, $ylinea, 146, $ylinea);
    $set_y=$pdf->GetY();
    $pdf->SetXY($set_x+49, $set_y-5);
    $pdf->MultiCell(78, 5, "F.", 0, 'J', 0);

    $set_y=$pdf->GetY();
    $pdf->SetXY($set_x+49, $set_y);
    $pdf->MultiCell(78, 5, 'N'.".", 0, 'F', 0);
} else {
    # code...
    $pdf->AddPage();
    $pdf->SetY(-20);
    $set_x = 20;
    $ylinea=$pdf->GetY();
    $pdf->Line(69, $ylinea, 147, $ylinea);
    $set_y=$pdf->GetY();
    $pdf->SetXY($set_x+49, $set_y-5);
    $pdf->MultiCell(78, 5, "F.", 0, 'J', 0);

    $set_y=$pdf->GetY();
    $pdf->SetXY($set_x+49, $set_y);
    $pdf->MultiCell(78, 5, 'N'.".", 0, 'F', 0);
}
$pdf->Output("reporte_preingreso.pdf", "I");
