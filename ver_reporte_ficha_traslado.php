<?php
require('_core.php');
require('fpdf/fpdf.php');
$idtransace=$_REQUEST['id_traslado'];

$sql=_query("SELECT productos.descripcion,productos.barcode,productos.estilo,productos.talla,colores.nombre,detalle_traslado.cantidad from detalle_traslado INNER JOIN productos ON productos.id_producto=detalle_traslado.id_producto INNER JOIN colores ON colores.id_color=productos.id_color WHERE detalle_traslado.idtransace='$idtransace'");
$sql_algo=_query("SELECT traslado.numero_doc,traslado.pares,traslado.items FROM traslado WHERE idtransace=$idtransace");
$tn=_fetch_array($sql_algo);
$numero_doc=$tn['numero_doc'];
$pares=$tn['pares'];
$items=$tn['items'];

class PDF extends FPDF
{
  var $a;

    // Cabecera de página\
    public function Header()
    {

        // Logo
        $this->Image('img/logoopenpyme.jpg', 10, 10, 33);
        // Arial bold 15
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 10);
        // Movernos a la derecha
        // Título
        $this->Cell(196, 5, 'CALZADO MAYORGA', 0, 1, 'C');
        $this->Cell(196, 5, 'FICHA DE TRASLADO', 0, 1, 'C');
        $this->Cell(196, 5, utf8_decode('FICHA Nº '.$this->a), 0, 1, 'C');
        $this-> Cell(196, 5,utf8_decode('FECHA DE IMPRESIÓN: '.date('Y-m-d H:i:s')), 0, 0, 'C');
        // Salto de línea
        $this->Ln(10);
    }

    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this->Cell(0, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function set($value)
    {
      # code...
      $this->a=$value;
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$pdf -> set($numero_doc);
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
$pdf->Cell(8, 5, utf8_decode('Nº'), 1, 0, 'C');
$pdf->Cell(20, 5, 'COD.', 1, 0, 'L');
$pdf->Cell(88, 5, 'PRODUCTO', 1, 0, 'L');
$pdf->Cell(20, 5, 'ESTILO', 1, 0, 'L');
$pdf->Cell(30, 5, 'COLOR', 1, 0, 'L');
$pdf->Cell(15, 5, 'TALLA', 1, 0, 'L');
$pdf->Cell(15, 5, 'CANT.', 1, 1, 'L');
$n=1;
while ($row=_fetch_array($sql)) {
  # code...
  $pdf->Cell(8, 5,$n, 1, 0, 'C');
  $pdf->Cell(20, 5, utf8_decode($row['barcode']), 1, 0, 'L');
  $pdf->Cell(88, 5,utf8_decode(substr($row['descripcion'], 0, 47)), 1, 0, 'L');
  $pdf->Cell(20, 5, utf8_decode($row['estilo']), 1, 0, 'L');
  $pdf->Cell(30, 5, utf8_decode(substr($row['nombre'], 0, 13)), 1, 0, 'L');/**/
  $pdf->Cell(15, 5, utf8_decode($row['talla']), 1, 0, 'L');
  $pdf->Cell(15, 5, $row['cantidad'], 1, 1, 'L');
  $n=$n+1;
}
$pdf->Cell(181, 5, utf8_decode('TOTAL PARES: '), 1, 0, 'L');
$pdf->Cell(15, 5, utf8_decode($pares), 1, 1, 'L');
$pdf->Cell(181, 5, utf8_decode('TOTAL ITEMS: '), 1, 0, 'L');
$pdf->Cell(15, 5, utf8_decode($items), 1, 1, 'L');

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
$pdf->Output("reporte_traslado.pdf","I");
