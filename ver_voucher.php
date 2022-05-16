<?php
require('_core.php');
require('fpdf/fpdf.php');
require('num2letras.php');

$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$pr=_fetch_array(_query("SELECT empresa.razonsocial FROM empresa"));
$propietario=Mayu($pr['razonsocial']);

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));

$id_proveedor=$_REQUEST['id_proveedor'];
$fecha=$_REQUEST['fecha'];
$hora=$_REQUEST['hora'];

$sql_banco=_query("SELECT DISTINCT bancos.nombre,cuenta_bancos.numero_cuenta,abono_cxp.numero_doc FROM cxp JOIN abono_cxp ON abono_cxp.idtransace=cxp.idtransace JOIN bancos ON bancos.id_banco=abono_cxp.id_banco JOIN cuenta_bancos ON cuenta_bancos.id_cuenta=abono_cxp.id_cuenta WHERE cxp.id_proveedor='$id_proveedor' AND abono_cxp.fecha='$fecha' AND abono_cxp.hora='$hora'");
$rvc=_fetch_array($sql_banco);
$banco=$rvc['nombre'];
$numero_de_cuenta=$rvc['numero_cuenta'];
$numero_voucher=$rvc['numero_doc'];


$sql_abonos=_query("SELECT bancos.nombre,cuenta_bancos.numero_cuenta,abono_cxp.cheque ,abono_cxp.monto_cheque,abono_cxp.fecha_cheque FROM cxp JOIN abono_cxp ON abono_cxp.idtransace=cxp.idtransace JOIN bancos ON bancos.id_banco=abono_cxp.id_banco JOIN cuenta_bancos ON cuenta_bancos.id_cuenta=abono_cxp.id_cuenta WHERE cxp.id_proveedor=$id_proveedor AND abono_cxp.fecha='$fecha' AND abono_cxp.hora='$hora' GROUP BY abono_cxp.cheque ORDER BY abono_cxp.cheque ASC");

$sql_pro=_query("SELECT proveedores.nombreche,proveedores.contacto FROM proveedores WHERE id_proveedor=$id_proveedor");
$rp=_fetch_array($sql_pro);
$proveedor=$rp['nombreche'];
$contacto=$rp['contacto'];

$sql_total=_query("SELECT COUNT(DISTINCT abono_cxp.cheque) AS cheques,SUM(abono_cxp.monto) AS montocheques,abono_cxp.fecha,abono_cxp.hora FROM cxp JOIN abono_cxp ON abono_cxp.idtransace=cxp.idtransace JOIN bancos ON bancos.id_banco=abono_cxp.id_banco JOIN cuenta_bancos ON cuenta_bancos.id_cuenta=abono_cxp.id_cuenta WHERE cxp.id_proveedor=$id_proveedor AND abono_cxp.fecha='$fecha' AND abono_cxp.hora='$hora' GROUP BY abono_cxp.hora,abono_cxp.fecha");
$rt=_fetch_array($sql_total);
$total=$rt['montocheques'];
$total=number_format($total,2,'.','');
$fecha2=$rt['fecha'];
list($y,$m,$d)=explode('-',$fecha);
$fechaLetras='FECHA:  SAN MIGUEL '.$d.'  DE '.Mayu(meses(intval($m))).utf8_decode(' DEl AÑO ').$y;



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

        // Arial bold 15
        $this->SetMargins(10, 10);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 12);
        // Movernos a la derecha
        // Título
        $this->Cell(130, 6, ''.$this->a, 0, 0, 'L');/*.$this->a*/
        $this->Cell(33,6,"VOUCHER  NO:    ",0,0,'L');
        $this->Cell(33,6,$this->b,0,1,'L');

        // Salto de línea
        $this->Ln(5);
    }

    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
        $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function setear($a,$b)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$pdf->setear($banco,$numero_voucher);
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
$pdf->SetFont('latin', '', 12);
$pdf->Cell(196, 5,'', "TLR", 1, 'L');
$pdf->Cell(130, 6,''.$propietario, "L", 0, 'L');/*.$this->a*/
$pdf->Cell(33,6,"CUENTA  NO:    ",0,0,'L');
$pdf->Cell(33,6,''.$numero_de_cuenta,"R",1,'L');
$pdf->Cell(196, 5,'', "LR", 1, 'L');
$pdf->SetFont('latin', '', 8);
$pdf->Cell(130, 5,''.$fechaLetras.'   ', "L", 0, 'L');
$pdf->Cell(33,5,"SALDO USDD$   ",0,0,'L');
$total2=number_format($total,2);
$pdf->Cell(33, 5,$total2, "R", 1, 'L');
$pdf->Cell(196, 5,'', "LR", 1, 'L');
$pdf->Cell(196, 5,'PAGUESE A LA ORDEN DE:   '.utf8_decode($proveedor), "LR", 1, 'L');
list($e,$d)=explode('.',$total);
$pdf->Cell(196, 5,'', "LR", 1, 'L');
$pdf->Cell(160, 5,'LA SUMA DE:    '.Mayu( num2letras($e)).' CON '.$d.'/100'." USD DOLARES", "L", 0, 'L');
$pdf->Cell(33, 5,'', "B", 0, 'R');
$pdf->Cell(3, 5,'', "R", 1, 'R');

$pdf->Cell(160, 5,'', "L", 0, 'L');
$pdf->Cell(33, 5,'FIRMA', "", 0, 'C');
$pdf->Cell(3, 5,'', "R", 1, 'R');
$pdf->Cell(196, 5,'', "BLR", 1, 'L');
$pdf->Ln(5);

$pdf->SetFillColor(221, 221, 221);
$pdf->Cell(19, 5,'FECHA', 0, 0, 'R',true);
$pdf->Cell(25, 5,'NUMERO', 0, 0, 'R',true);
$pdf->Cell(19, 5,'CARGO', 0, 0, 'R',true);
$pdf->Cell(19, 5,'DESC', 0, 0, 'R',true);
$pdf->Cell(19, 5,'DEVOL', 0, 0, 'R',true);
$pdf->Cell(19, 5,'BONIF', 0, 0, 'R',true);
$pdf->Cell(19, 5,'RETEN', 0, 0, 'R',true);
$pdf->Cell(19, 5,'VINET', 0, 0, 'R',true);
$pdf->Cell(19, 5,'ABONO', 0, 0, 'R',true);
$pdf->Cell(19, 5,'SALDO', 0, 1, 'R',true);
$i=1;
$sql_facturas=_query("SELECT DISTINCT cxp.idtransace,cxp.fecha,cxp.numero_doc,cxp.monto from abono_cxp INNER JOIN cxp ON cxp.idtransace=abono_cxp.idtransace AND abono_cxp.numero_doc='$numero_voucher'");
while ($row = _fetch_array($sql_facturas)) {
  $idtransace=$row['idtransace'];
  # code...
  $saldo=round($row['monto'],2);
  /*Descuentos efectuados el mismo dia de la factura*/
  $sql_sum_d_d=_query("SELECT descuento.id_tipo_descuento as id, SUM(descuento.monto)AS monto FROM descuento WHERE descuento.fecha='$row[fecha]' AND descuento.idtransace='$idtransace' GROUP BY id_tipo_descuento");
  $array_a = array('', '', '', '', '');
  while($rsdd=_fetch_array($sql_sum_d_d))
  {
    switch ($rsdd['id']) {
      case '1':
        # code...
        $array_a[0]=number_format( $rsdd['monto'],2);
        $saldo=round(($saldo-$rsdd['monto']),2);
        break;
      case '2':
      $array_a[1]=number_format( $rsdd['monto'],2);
      $saldo=round(($saldo-$rsdd['monto']),2);

        # code...
        break;
      case '3':
      $array_a[2]=number_format( $rsdd['monto'],2);
      $saldo=round(($saldo-$rsdd['monto']),2);
        # code...
        break;
      case '4':
      $array_a[3]=number_format( $rsdd['monto'],2);
      $saldo=round(($saldo-$rsdd['monto']),2);
        # code...
        break;
      case '5':
      $array_a[4]=number_format( $rsdd['monto'],2);
      $saldo=round(($saldo-$rsdd['monto']),2);
        # code...
        break;

      default:
        # code...
        break;
    }
  }/*fin while descuento en el mismo dia de la factura*/

  $pdf->Cell(19, 5,$row['fecha'], 0, 0, '');
  $pdf->Cell(25, 5,$row['numero_doc'], 0, 0, '');
  $pdf->Cell(19, 5,number_format($row['monto'],2), 0, 0, 'R');
  $pdf->Cell(19, 5,$array_a[0], 0, 0, 'R');
  $pdf->Cell(19, 5,$array_a[1], 0, 0, 'R');
  $pdf->Cell(19, 5,$array_a[2], 0, 0, 'R');
  $pdf->Cell(19, 5,$array_a[3], 0, 0, 'R');
  $pdf->Cell(19, 5,$array_a[4], 0, 0, 'R');
  $pdf->Cell(19, 5,'', 0, 0, 'R');
  $pdf->Cell(19, 5,number_format($saldo,2), 0, 1, 'R');



  /*fechas con descuentos en esta factura sin incluir los descuentos efectuados en el mismo dia de la factura*/
  $sql_desc_fecha=_query("SELECT DISTINCT descuento.fecha FROM descuento WHERE idtransace='$idtransace' AND descuento.fecha<'$fecha' AND descuento.fecha!='$row[fecha]' UNION SELECT DISTINCT descuento.fecha FROM descuento WHERE idtransace='$idtransace' AND descuento.fecha='$fecha' AND descuento.fecha!='$row[fecha]' and descuento.hora<'$hora'");

  while ($rowf=_fetch_array($sql_desc_fecha)) {
    # code...
    $fecha_suma_facturas=$rowf['fecha'];
    $array = array('', '', '', '', '');

    $sql_descuentos=_query("SELECT descuento.id_tipo_descuento as id, SUM(descuento.monto)AS monto FROM descuento WHERE idtransace='$idtransace' AND descuento.fecha='$fecha_suma_facturas' AND descuento.fecha<'$fecha' GROUP BY id_tipo_descuento UNION SELECT descuento.id_tipo_descuento as id, SUM(descuento.monto)AS monto FROM descuento WHERE idtransace='$idtransace' AND descuento.fecha='$fecha_suma_facturas' AND descuento.fecha='$fecha' and descuento.hora<'$hora' GROUP BY id_tipo_descuento");
    while ($rowdes=_fetch_array($sql_descuentos)) {
      # code...


      switch ($rowdes['id']) {
        case '1':
          # code...
          $array[0]=number_format( $rowdes['monto'],2);
          $saldo=round(($saldo-$rowdes['monto']),2);
          break;
        case '2':
        $array[1]=number_format( $rowdes['monto'],2);
        $saldo=round(($saldo-$rowdes['monto']),2);
          # code...
          break;
        case '3':
        $array[2]=number_format( $rowdes['monto'],2);
        $saldo=round(($saldo-$rowdes['monto']),2);
          # code...
          break;
        case '4':
        $array[3]=number_format( $rowdes['monto'],2);
        $saldo=round(($saldo-$rowdes['monto']),2);
          # code...
          break;
        case '5':
        $array[4]=number_format( $rowdes['monto'],2);
        $saldo=round(($saldo-$rowdes['monto']),2);
          # code...
          break;

        default:
          # code...
          break;
      }
    }/*fin descuentos sumados*/

    $pdf->Cell(19, 5,$fecha_suma_facturas, 0, 0, '');
    $pdf->Cell(25, 5,'', 0, 0, '');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,$array[0], 0, 0, 'R');
    $pdf->Cell(19, 5,$array[1], 0, 0, 'R');
    $pdf->Cell(19, 5,$array[2], 0, 0, 'R');
    $pdf->Cell(19, 5,$array[3], 0, 0, 'R');
    $pdf->Cell(19, 5,$array[4], 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,number_format($saldo,2), 0, 1, 'R');

  }/*fin fechas con descuentos*/


  /*abonos anteriores a la fecha de estos abonos*/
  $sql_abonito=_query("SELECT abono_cxp.monto,abono_cxp.fecha FROM abono_cxp WHERE  idtransace='$idtransace' AND abono_cxp.fecha<'$fecha' ORDER by abono_cxp.fecha ASC");

  while ($rowabon=_fetch_array($sql_abonito)) {
    # code...
    $saldo=round(($saldo-$rowabon['monto']),2);
    $fa=$rowabon['fecha'];
    $pdf->Cell(19, 5,$fa, 0, 0, '');
    $pdf->Cell(25, 5,'', 0, 0, '');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,number_format($rowabon['monto'],2), 0, 0, 'R');
    $pdf->Cell(19, 5,number_format($saldo,2), 0, 1, 'R');
  }

  /*abonos efectuados el mismo dia pero antes de estos*/

  $sql_abonito=_query("SELECT abono_cxp.monto,abono_cxp.fecha FROM abono_cxp WHERE  idtransace='$idtransace' AND abono_cxp.fecha='$fecha' AND abono_cxp.hora<='$hora' ORDER by abono_cxp.fecha ASC");

  while ($rowabon=_fetch_array($sql_abonito)) {
    # code...
    $saldo=round(($saldo-$rowabon['monto']),2);
    $fa=$rowabon['fecha'];
    $pdf->Cell(19, 5,$fa, 0, 0, '');
    $pdf->Cell(25, 5,'', 0, 0, '');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,'', 0, 0, 'R');
    $pdf->Cell(19, 5,number_format($rowabon['monto'],2), 0, 0, 'R');
    $pdf->Cell(19, 5,number_format($saldo,2), 0, 1, 'R');
  }
}/*fin whilw facturas*/

$pdf->Ln(5);
$pdf->Cell(196, 5, utf8_decode($contacto), 0, 1, 'L');
$pdf->Cell(10, 5, utf8_decode('N.'), 1, 0, 'C');
$pdf->Cell(80, 5, 'BANCO', 1, 0, 'L');
$pdf->Cell(36, 5, 'CUENTA.', 1, 0, 'L');
$pdf->Cell(36, 5, 'CHEQUE', 1, 0, 'L');
$pdf->Cell(34, 5, 'MONTO (USD)', 1, 1, 'L');
$i=1;
while ($row=_fetch_array($sql_abonos)) {
  # code...
  $pdf->Cell(10, 5, ''.$i, 1, 0, 'C');
  $pdf->Cell(80, 5, ''.$row['nombre'], 1, 0, 'L');
  $pdf->Cell(36, 5, ''.$row['numero_cuenta'], 1, 0, 'L');
  $pdf->Cell(36, 5, ''.$row['cheque'], 1, 0, 'L');
  $pdf->Cell(34, 5, ''.number_format($row['monto_cheque'],2), 1, 1, 'R');
  $i=$i+1;
}
$pdf->Cell(162, 5, 'TOTAL', 1, 0, 'L');
$pdf->Cell(34, 5, ''.number_format($total,2), 1, 1, 'R');

$pdf->SetFont('latin', '', 10);
$ylinea=$pdf->GetY();
if ($ylinea<225) {/*255*/
    # code...

} else {
    # code...
    $pdf->AddPage();
}

$pdf->SetY(-46);
$set_x = 10;
$set_y=$pdf->GetY();
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(49, 5, "AUTORIZADO", 0,0, 'C');
$pdf->SetXY($set_x, $set_y-5);
$pdf->Cell(49, 5, "N._________________", 0,0, 'C');

$pdf->SetXY($set_x+49, $set_y);
$pdf->Cell(49, 5, "HECHO POR", 0,0, 'C');
$pdf->SetXY($set_x+49, $set_y-5);
$pdf->Cell(49, 5, "N._________________", 0,0, 'C');

$pdf->SetXY($set_x+98, $set_y);
$pdf->Cell(49, 5, "REVISADO", 0,0, 'C');
$pdf->SetXY($set_x+98, $set_y-5);
$pdf->Cell(49, 5, "N._________________", 0,0, 'C');

$pdf->SetXY($set_x+147, $set_y);
$pdf->Cell(49, 5, "F._________________", 0,0, 'C');
$pdf->SetXY($set_x+147, $set_y-5);
$pdf->Cell(49, 5, "N._________________", 0,0, 'C');
$pdf->SetXY($set_x+147, $set_y+5);
$pdf->Cell(49, 5, "DUI:_______________", 0,0, 'C');
$pdf->SetXY($set_x+147, $set_y+10);
$pdf->Cell(49, 5, "TEL:_______________", 0,0, 'C');
$pdf->SetXY($set_x+147, $set_y+15);
$pdf->Cell(49, 5, "RECIBI CONFORME", 0,0, 'C');


$pdf->Output("voucher.pdf", "I");
