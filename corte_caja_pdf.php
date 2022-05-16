<?php
//error_reporting(E_ERROR | E_PARSE);
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
$direccion = utf8_decode(utf8_decode(trim($row_emp["direccion"])));
$tel1 = $row_emp['telefono'];
$nrc = $row_emp['nrc'];
$nit = $row_emp['nit'];
/*$whatsapp=$row_emp["whatsapp"];
$email=$row_emp["email"];
$depa = $row_emp["id_departamento"];
$muni = $row_emp["id_municipio"];
$telefono1 = $row_emp["telefono1"];
$telefono2 = $row_emp["telefono2"];
$sql2 = _query("SELECT dep.* FROM departamento as dep WHERE dep.id_departamento='$depa'");
$row2 = _fetch_array($sql2);
$departamento = $row2["nombre_departamento"];

$sql3 = _query("SELECT mun.* FROM municipio as mun WHERE dep.id_municipio='$muni'");
$row3 = _fetch_array($sql3);
$municipio = $row3["nombre_municipio"];
$telefonos="TEL. ".$tel1;*/

$id_corte = $_REQUEST["id_corte"];
$sql_user = _query("SELECT empleados.nombre, monto_ch, caja_chica, n_remesa, pedido_pendiente, cobro_pendiente, mensaje, id_apertura, hora_corte as hora, fecha_corte, turno FROM controlcaja JOIN empleados ON controlcaja.id_empleado=empleados.id_empleado WHERE id_corte='$id_corte'");
$row_user = _fetch_array($sql_user);
$id_apertura = $row_user["id_apertura"];
$turno = $row_user["turno"];
$nremesa = $row_user["n_remesa"];
$caja_chica = $row_user["monto_ch"];
//$saldo_caja_chica = $row_user["caja_chica"];
$cajero = utf8_decode($row_user["nombre"]);
$pedido_pendiente = utf8_decode($row_user["pedido_pendiente"]);
$cobro_pendiente = utf8_decode($row_user["cobro_pendiente"]);
$mensaje = utf8_decode($row_user["mensaje"]);
$hora_c = hora($row_user["hora"]);
$fecha_corte = $row_user["fecha_corte"];
$sql_saldoCaja=_fetch_array(_query("SELECT monto_ch_actual FROM apertura_caja WHERE id_apertura='$id_apertura'"));
$saldo_caja_chica=$sql_saldoCaja["monto_ch_actual"];
$sql_hora_ap = _query("SELECT hora FROM detalle_apertura WHERE id_apertura='$id_apertura' AND turno='$turno'");
$row_hora_ap = _fetch_array($sql_hora_ap);
$hora_ic = hora($row_hora_ap["hora"]);
$logo = "img/logo_sys.png";
$impress = "Impreso: ".date("d/m/Y").' '.hora(date("H:i:s"));
$title = $nombre_a;
$titulo = "CORTE DE CAJA TURNO ".$turno;

list($a,$m,$d) = explode("-", $fecha_corte);
$fech="$d DE ".meses($m)." DE $a";


$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->Image($logo,160,4,40,25);
$set_x = 0;
$set_y = 10;

//Encabezado General
$pdf->SetFont('Arial','',16);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(220,5,$title,0,1,'C');
//$pdf->SetXY($set_x, $set_y+11);
$pdf->SetFont('Arial','',10);
//$pdf->Cell(220,5,utf8_decode(ucwords("Depto. ".utf8_decode($departamento))),0,1,'C');
$pdf->SetXY($set_x+80, $set_y+5);
$pdf->MultiCell(60,3.5,str_replace(" Y ", " y ",ucwords(utf8_decode($direccion))),0,'C',0);
$pdf->SetXY($set_x, $set_y+11);
$pdf->Cell(220,5,"Telefono: ".$tel1,0,1,'C');
$plus = 0;

$pdf->SetXY($set_x+5, $set_y+20);
$pdf->Cell(220,5,utf8_decode($titulo),0,1,'L');
$pdf->SetXY($set_x+5, $set_y+25);
$pdf->Cell(220,5,$fech,0,1,'L');

$set_x = 5;
$set_y = 40;

$pdf->SetXY($set_x, $set_y);
$pdf->Cell(205,5,"CAJERO: ".$cajero,0,1,'L',0);
$pdf->SetXY($set_x, $set_y+5);
$pdf->Cell(30,5,"INICIO: ".$hora_ic,0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+5);
$pdf->Cell(30,5,"FIN:   ".$hora_c,0,1,'L',0);

$mm = 0;
$page = 1;
$salto = 56;
$j=0;
$set_y+=10;
/*TOTALES GENERALES DE TODAS LAS QUERYS*/
$tot_contado = 0;
$tot_cred = 0;
$tot_pend_pag = 0;
$tot_abonos = 0;
$tot_vpend = 0;
$tot_vales = 0;
$tot_viatico = 0;
$tot_ingreso = 0;

$t_tike = 0;
$t_factuta = 0;
$t_credito = 0;
/*TOTALES GENERALES DE TODAS LAS QUERYS*/

/**VENTA CONTADO**/
/*$sql = _query("SELECT f.tipo_documento, f.id_factura, f.tipo_pago, f.num_fact_impresa, f.total, f.anulada, f.hora,
               c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
               FROM factura AS f
               LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
               LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
               LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
               WHERE f.finalizada=1
               AND f.tipo_pago!='CRE'
               AND f.tipo_pago NOT LIKE '%PEN%'
               AND f.id_apertura='$id_apertura'
               AND f.turno='$turno'
               ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");*/
               $sql = _query("SELECT f.tipo_documento, f.id_factura, f.tipo_pago, f.num_fact_impresa, f.total, f.anulada, f.hora,
                c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
                FROM factura AS f
                LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
                LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
                LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
                WHERE f.finalizada=1
                AND f.tipo_pago!='CRE'
                AND f.tipo_pago NOT LIKE '%PEN%'
                AND f.id_apertura='$id_apertura'
                AND f.devolucion=0
                ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y);
                $pdf->Cell(205,5,"VENTAS DE CONTADO/CANCELADO EN CAJA",0,1,'C',0);
                $set_y+=5;
                $pdf->Line($set_x,$set_y,$set_x+205,$set_y);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y);
                $pdf->Cell(16,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+16, $set_y);
                $pdf->Cell(16,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+32, $set_y);
                $pdf->Cell(78,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+110, $set_y);
                $pdf->Cell(41,5,"VENDEDOR",0,1,'L',0);
                $pdf->SetXY($set_x+151, $set_y);
                $pdf->Cell(18,5,"TRANSF",0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y);
                $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y);
                $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                $set_y+=5;
                $tefe = 0;
                $tcheq = 0;
                $ttrans = 0;
                $tot_parc = 0;
                while ($row = _fetch_array($sql))
                {
                  if($page)
                    $salto = 56;
                  else
                    $salto = 62;
                  if($j>=$salto)
                  {
                    $page=0;
                    $pdf->AddPage();
                    $set_x = 5;
                    $set_y = 10;
                    $mm=0;

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetXY($set_x, $set_y);
                    $pdf->Cell(16,5,"TIPO DOC",0,1,'C',0);
                    $pdf->SetXY($set_x+16, $set_y);
                    $pdf->Cell(16,5,utf8_decode("NÚMERO"),0,1,'C',0);
                    $pdf->SetXY($set_x+32, $set_y);
                    $pdf->Cell(78,5,"CLIENTE",0,1,'L',0);
                    $pdf->SetXY($set_x+110, $set_y);
                    $pdf->Cell(41,5,"VENDEDOR",0,1,'L',0);
                    $pdf->SetXY($set_x+151, $set_y);
                    $pdf->Cell(18,5,"TRANSF",0,1,'R',0);
                    $pdf->SetXY($set_x+169, $set_y);
                    $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                    $pdf->SetXY($set_x+187, $set_y);
                    $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                    $set_y = 15;
                    $j=0;
                  }
                  $tipo_pago = $row["tipo_pago"];
                  $id_factura = $row["id_factura"];

                  $tipo_doc = $row["tipo_documento"];
                  if($tipo_doc=="COF"){
                    $tipo_doc="FAC";
                  }
                  $numero_doc = $row["num_fact_impresa"];
                  $cliente = utf8_decode($row["cliente"]);

                  $vendedor = $row["vendedor"];
                  $dven = explode(" ",$row["vendedor"]);
                  if(count($dven)>3)
                    $vendedor = $dven[0]." ".$dven[2];

                  $cajero = $row["cajero"];
                  $dcaj = explode(" ",$row["cajero"]);
                  if(count($dcaj)>3)
                    $cajero = $dcaj[0]." ".$dcaj[2];

                  if($vendedor == "")
                  {
                    $vendedor = $cajero;
                  }
                  $total = $row["total"];
                  $efe = 0;
                  $cheq = 0;
                  $trans = 0;
                  if($tipo_pago == "CON")
                  {
                    $efe = $total;
                  }
                  if($tipo_pago == "CHE")
                  {
                    $cheq = $total;
                  }
                  if($tipo_pago == "TRA")
                  {
                    $trans = $total;
                  }
                  $tot_parc += $total;
                  $anulada = $row["anulada"];
                  $hora = hora($row["hora"]);
                  if($anulada)
                  {
                    $anulado = "SI";
                    $cliente = "**** ANULADA ****";
                    $pdf->setTextColor(255,0,0);
                  }
                  else
                  {
                    $anulado = "";
                    $pdf->setTextColor(0,0,0);
                    $tot_contado+=$efe;
                    $tefe += $efe;
                    $tcheq += $cheq;
                    $ttrans += $trans;
                  }
                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(16,5,$tipo_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+16, $set_y+$mm);
                  $pdf->Cell(16,5,$numero_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+32, $set_y+$mm);
                  $pdf->Cell(78,5,$cliente,0,1,'L',0);
                  $pdf->SetXY($set_x+110, $set_y+$mm);
                  $pdf->Cell(41,5,$vendedor,0,1,'L',0);
                  $pdf->SetXY($set_x+151, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($trans,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+169, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($cheq,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+187, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($efe,2,".",","),0,1,'R',0);
                  $mm+=3;
                  $j++;
                  if($tipo_doc == 'TIK')
                  {
                    $t_tike += 1;
                  }
                  else if($tipo_doc == 'FAC')
                  {
                    $t_factuta += 1;
                  }
                  else if($tipo_doc == 'CCF')
                  {
                    $t_credito += 1;
                  }
                }
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $mm+=2;
                $pdf->setTextColor(0,0,0);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(151,5,"TOTALES",0,1,'C',0);
                $pdf->SetXY($set_x+151, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($ttrans,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tcheq,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tefe,2,".",","),0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->Line($set_x+151,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $pdf->SetXY($set_x+151, $set_y+$mm+5);
                $pdf->Cell(54,5,"$".number_format($tot_parc,2,".",","),0,1,'R',0);
                $mm+=3;
                $mm+=2;
                $j++;
              }


//INICIA LAS DEVOLUCIONES
///**** INICIA DEVOLUCIONES
///**** INICIA DEVOLUCIONES

              if($turno!="")
              {
                $t=" AND f.turno='$turno' ";
              }

              $sqls ="SELECT f.tipo_documento,f.referencia, f.id_factura, f.tipo_pago, f.num_fact_impresa, f.total, f.anulada, f.hora, f.fecha, f.turno, c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
              FROM factura AS f LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
              LEFT JOIN usuario AS e1 ON f.id_empleado=e1.id_usuario
              LEFT JOIN empleados AS e ON f.id_usuario =e.id_empleado
              WHERE f.finalizada=1 AND  f.tipo_documento='TIK' $t AND f.devolucion=1 AND f.id_apertura='$id_apertura' AND f.id_sucursal='$id_sucursal'
              UNION SELECT f.tipo_documento,f.referencia, f.id_factura, f.tipo_pago, f.num_fact_impresa, f.total, f.anulada, f.hora, f.fecha, f.turno, c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
              FROM factura AS f LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
              LEFT JOIN usuario AS e1 ON f.id_empleado=e1.id_usuario
              LEFT JOIN empleados AS e ON f.id_usuario =e.id_empleado
              WHERE f.finalizada=1 AND  f.tipo_documento='DEV' $t AND f.id_apertura='$id_apertura' AND f.id_sucursal='$id_sucursal'
              UNION SELECT f.tipo_documento,f.referencia, f.id_factura, f.tipo_pago, f.num_fact_impresa, f.total, f.anulada, f.hora, f.fecha, f.turno, c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
              FROM factura AS f LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
              LEFT JOIN usuario AS e1 ON f.id_empleado=e1.id_usuario
              LEFT JOIN empleados AS e ON f.id_usuario =e.id_empleado
              WHERE f.finalizada=1 AND  f.tipo_documento='NC' $t AND f.id_apertura='$id_apertura' AND f.id_sucursal='$id_sucursal' ";

              $sqls.="";
              $sql=_query($sqls);
              $num = _num_rows($sql);
              if($num > 0)
              {
                $mm+=6;
                $j+=2;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"DEVOLUCIONES",0,1,'C',0);
                $mm+=5;
                $j++;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+40, $set_y+$mm);
                $pdf->Cell(80,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+120, $set_y+$mm);
                $pdf->Cell(25,5,"APLICADO",0,1,'L',0);
                $pdf->SetXY($set_x+145, $set_y+$mm);
                $pdf->Cell(35,5,"CAJERO",0,1,'L',0);
                $pdf->SetXY($set_x+180, $set_y+$mm);
                $pdf->Cell(25,5,"TOTAL",0,1,'R',0);
/*$pdf->SetXY($set_x+190, $set_y+$mm);
$pdf->Cell(15,5,"ANULADO",0,1,'C',0);
*/
$pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
$mm+=5;
$j++;
$total_devolucion=0;
while ($row = _fetch_array($sql))
{
  if($page)
    $salto = 58;
  else
   $salto = 70;
 if($j>=$salto)
 {
  $page=0;
  $pdf->AddPage();
  $set_x = 5;
  $set_y = 5;
  $mm=0;

  $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
  $pdf->SetFont('Arial','',8);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
  $pdf->SetXY($set_x+20, $set_y+$mm);
  $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
  $pdf->SetXY($set_x+40, $set_y+$mm);
  $pdf->Cell(80,5,"CLIENTE",0,1,'L',0);
  $pdf->SetXY($set_x+120, $set_y+$mm);
  $pdf->Cell(25,5,"APLICADO",0,1,'L',0);
  $pdf->SetXY($set_x+145, $set_y+$mm);
  $pdf->Cell(35,5,"VENDEDOR",0,1,'L',0);
  $pdf->SetXY($set_x+180, $set_y+$mm);
  $pdf->Cell(25,5,"TOTAL",0,1,'R',0);
    /*$pdf->SetXY($set_x+190, $set_y);
    $pdf->Cell(15,5,"ANULADO",0,1,'C',0);*/
    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
    $set_y = 10;
    $j=0;
  }
  $tipo_doc = $row["tipo_documento"];
  $numero_doc = $row["num_fact_impresa"];
  $aplicado = $row["referencia"];
  $cliente = utf8_decode($row["cliente"]);

  $vendedor = $row["vendedor"];
  $dven = explode(" ",$row["vendedor"]);
  if(count($dven)>3)
    $vendedor = $dven[0]." ".$dven[2];

  $cajero = $row["cajero"];
  $dcaj = explode(" ",$row["cajero"]);
  if(count($dcaj)>3)
    $cajero = $dcaj[0]." ".$dcaj[2];

  if($vendedor == "")
  {
    $vendedor = $cajero;
  }
  $total = $row["total"];
  $anulada = $row["anulada"];
  $hora = hora($row["hora"]);
  if($anulada)
  {
    $anulado = "SI";
    $cliente = "**** ANULADA ****";
    $pdf->setTextColor(255,0,0);
  }
  else
  {
    $anulado = "NO";
    $pdf->setTextColor(0,0,0);
    $tot_devo+=$total;
  }
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(20,5,$tipo_doc,0,1,'C',0);
  $pdf->SetXY($set_x+20, $set_y+$mm);
  $pdf->Cell(20,5,$numero_doc,0,1,'C',0);
  $pdf->SetXY($set_x+40, $set_y+$mm);
  $pdf->Cell(80,5,$cliente,0,1,'L',0);
  $pdf->SetXY($set_x+120, $set_y+$mm);
  $pdf->Cell(25,5,$aplicado,0,1,'L',0);
  $pdf->SetXY($set_x+145, $set_y+$mm);
  $pdf->Cell(35,5,$vendedor,0,1,'L',0);
  $pdf->SetXY($set_x+180, $set_y+$mm);
  $pdf->Cell(25,5,"$".number_format($total,2,".",","),0,1,'R',0);
  /*$pdf->SetXY($set_x+190, $set_y+$mm);
  $pdf->Cell(15,5,$anulado,0,1,'C',0);*/
  $mm+=3;
  $j++;
}
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $mm=0;
  $j=0;
}
$mm+=2;
$pdf->setTextColor(0,0,0);
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(151,5,"TOTALES",0,1,'C',0);
$pdf->SetXY($set_x+151, $set_y+$mm);
$pdf->Cell(54,5,"$".number_format($tot_devo,2,".",","),0,1,'R',0);
$pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
$mm+=3;
$j++;
}
/**VENTA PENDIENTE**/
/*$sql = _query("SELECT f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
               c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
               FROM factura AS f
               LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
               LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
               LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
               WHERE f.finalizada=1
               AND f.tipo_pago LIKE '%PEN%'
               AND f.id_apertura='$id_apertura'
               AND f.turno='$turno'
               ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");*/
               $sql = _query("SELECT f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
                c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
                FROM factura AS f
                LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
                LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
                LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
                WHERE f.finalizada=1
                AND f.tipo_pago LIKE '%PEN%'
                AND f.id_apertura='$id_apertura'
                ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"VENTAS PENDIENTES DE PAGO A UN DIA(SIN AUTORIZACION DE CREDITO)/ A CANCELAR SIGUIENTE DIA",0,1,'C',0);
                $mm+=3;
                $mm+=2;
                $j++;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+40, $set_y+$mm);
                $pdf->Cell(90,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+130, $set_y+$mm);
                $pdf->Cell(40,5,"VENDEDOR",0,1,'L',0);
                $pdf->SetXY($set_x+170, $set_y+$mm);
                $pdf->Cell(35,5,"TOTAL",0,1,'R',0);
  /*$pdf->SetXY($set_x+190, $set_y+$mm);
  $pdf->Cell(15,5,"ANULADO",0,1,'C',0);
  */
  $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
  $mm+=3;
  $mm+=2;
  $j++;
  while ($row = _fetch_array($sql))
  {
    if($page)
      $salto = 56;
    else
      $salto = 62;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_x = 5;
      $set_y = 10;
      $mm=0;

      $pdf->SetFont('Arial','',8);
      $pdf->SetXY($set_x, $set_y);
      $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
      $pdf->SetXY($set_x+20, $set_y);
      $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
      $pdf->SetXY($set_x+40, $set_y);
      $pdf->Cell(90,5,"CLIENTE",0,1,'L',0);
      $pdf->SetXY($set_x+130, $set_y);
      $pdf->Cell(40,5,"VENDEDOR",0,1,'L',0);
      $pdf->SetXY($set_x+170, $set_y);
      $pdf->Cell(35,5,"TOTAL",0,1,'R',0);
        /*$pdf->SetXY($set_x+190, $set_y);
        $pdf->Cell(15,5,"ANULADO",0,1,'C',0);*/
        $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
        $set_y = 15;
        $j=0;
      }
      $tipo_doc = $row["tipo_documento"];
      if($tipo_doc=="COF"){
        $tipo_doc="FAC";
      }
      $numero_doc = $row["num_fact_impresa"];
      $cliente = utf8_decode($row["cliente"]);

      $vendedor = $row["vendedor"];
      $dven = explode(" ",$row["vendedor"]);
      if(count($dven)>3)
        $vendedor = $dven[0]." ".$dven[2];

      $cajero = $row["cajero"];
      $dcaj = explode(" ",$row["cajero"]);
      if(count($dcaj)>3)
        $cajero = $dcaj[0]." ".$dcaj[2];

      if($vendedor == "")
      {
        $vendedor = $cajero;
      }
      $total = $row["total"];
      $anulada = $row["anulada"];
      $hora = hora($row["hora"]);
      if($anulada)
      {
        $anulado = "SI";
        $cliente = "**** ANULADA ****";
        $pdf->setTextColor(255,0,0);
      }
      else
      {
        $anulado = "";
        $pdf->setTextColor(0,0,0);
        $tot_vpend+=$total;
      }
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(20,5,$tipo_doc,0,1,'C',0);
      $pdf->SetXY($set_x+20, $set_y+$mm);
      $pdf->Cell(20,5,$numero_doc,0,1,'C',0);
      $pdf->SetXY($set_x+40, $set_y+$mm);
      $pdf->Cell(90,5,$cliente,0,1,'L',0);
      $pdf->SetXY($set_x+130, $set_y+$mm);
      $pdf->Cell(40,5,$vendedor,0,1,'L',0);
      $pdf->SetXY($set_x+170, $set_y+$mm);
      $pdf->Cell(35,5,"$".number_format($total,2,".",","),0,1,'R',0);
    /*$pdf->SetXY($set_x+190, $set_y+$mm);
    $pdf->Cell(15,5,$anulado,0,1,'C',0);*/
    $mm+=3;
    $j++;
    if($tipo_doc == 'TIK')
    {
      $t_tike += 1;
    }
    else if($tipo_doc == 'FAC')
    {
      $t_factuta += 1;
    }
    else if($tipo_doc == 'CCF')
    {
      $t_credito += 1;
    }
  }
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  $mm+=2;
  $pdf->setTextColor(0,0,0);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(151,5,"TOTALES",0,1,'C',0);
  $pdf->SetXY($set_x+151, $set_y+$mm);
  $pdf->Cell(54,5,"$".number_format($tot_vpend,2,".",","),0,1,'R',0);
  $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
  $mm+=3;
  $mm+=2;
  $j++;
}

/**VENTA AL CREDITO**/
/*$sql = _query("SELECT f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
               c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
               FROM factura AS f
               LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
               LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
               LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
               WHERE f.finalizada=1
               AND f.tipo_pago='CRE'
               AND f.id_apertura='$id_apertura'
               AND f.turno='$turno'
               ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");*/
               $sql = _query("SELECT f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
                c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
                FROM factura AS f
                LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
                LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
                LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
                WHERE f.finalizada=1
                AND f.tipo_pago='CRE'
                AND f.id_apertura='$id_apertura'
                ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"VENTAS AL CREDITO/CREDITOS AUTORIZADOS A 30 DIAS O MAS",0,1,'C',0);
                $mm+=3;
                $mm+=2;
                $j++;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+40, $set_y+$mm);
                $pdf->Cell(90,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+130, $set_y+$mm);
                $pdf->Cell(40,5,"VENDEDOR",0,1,'L',0);
                $pdf->SetXY($set_x+170, $set_y+$mm);
                $pdf->Cell(35,5,"TOTAL",0,1,'R',0);
  /*$pdf->SetXY($set_x+190, $set_y+$mm);
  $pdf->Cell(15,5,"ANULADO",0,1,'C',0);*/
  $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
  $mm+=3;
  $mm+=2;
  $j++;
  while ($row = _fetch_array($sql))
  {
    if($page)
      $salto = 56;
    else
      $salto = 62;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_x = 5;
      $set_y = 10;
      $mm=0;

      $pdf->SetFont('Arial','',8);
      $pdf->SetXY($set_x, $set_y);
      $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
      $pdf->SetXY($set_x+20, $set_y);
      $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
      $pdf->SetXY($set_x+40, $set_y);
      $pdf->Cell(90,5,"CLIENTE",0,1,'L',0);
      $pdf->SetXY($set_x+130, $set_y);
      $pdf->Cell(40,5,"VENDEDOR",0,1,'L',0);
      $pdf->SetXY($set_x+170, $set_y);
      $pdf->Cell(35,5,"TOTAL",0,1,'R',0);
        /*$pdf->SetXY($set_x+190, $set_y);
        $pdf->Cell(15,5,"ANULADO",0,1,'C',0);*/
        $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
        $set_y = 15;
        $j=0;
      }
      $tipo_doc = $row["tipo_documento"];
      if($tipo_doc=="COF"){
        $tipo_doc="FAC";
      }
      $numero_doc = $row["num_fact_impresa"];
      $cliente = utf8_decode($row["cliente"]);

      $vendedor = $row["vendedor"];
      $dven = explode(" ",$row["vendedor"]);
      if(count($dven)>3)
        $vendedor = $dven[0]." ".$dven[2];

      $cajero = $row["cajero"];
      $dcaj = explode(" ",$row["cajero"]);
      if(count($dcaj)>3)
        $cajero = $dcaj[0]." ".$dcaj[2];

      if($vendedor == "")
      {
        $vendedor = $cajero;
      }
      $total = $row["total"];
      $anulada = $row["anulada"];
      $hora = hora($row["hora"]);
      if($anulada)
      {
        $cliente = "**** ANULADA ****";
        $anulado = "SI";
        $pdf->setTextColor(255,0,0);
      }
      else
      {
        $anulado = "";
        $pdf->setTextColor(0,0,0);
        $tot_cred+=$total;
      }
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(20,5,$tipo_doc,0,1,'C',0);
      $pdf->SetXY($set_x+20, $set_y+$mm);
      $pdf->Cell(20,5,$numero_doc,0,1,'C',0);
      $pdf->SetXY($set_x+40, $set_y+$mm);
      $pdf->Cell(90,5,$cliente,0,1,'L',0);
      $pdf->SetXY($set_x+130, $set_y+$mm);
      $pdf->Cell(40,5,$vendedor,0,1,'L',0);
      $pdf->SetXY($set_x+170, $set_y+$mm);
      $pdf->Cell(35,5,"$".number_format($total,2,".",","),0,1,'R',0);
    /*$pdf->SetXY($set_x+190, $set_y+$mm);
    $pdf->Cell(15,5,$anulado,0,1,'C',0);*/
    $mm+=3;
    $j++;
    if($tipo_doc == 'TIK')
    {
      $t_tike += 1;
    }
    else if($tipo_doc == 'FAC')
    {
      $t_factuta += 1;
    }
    else if($tipo_doc == 'CCF')
    {
      $t_credito += 1;
    }
  }
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  $mm+=2;
  $pdf->setTextColor(0,0,0);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(151,5,"TOTALES",0,1,'C',0);
  $pdf->SetXY($set_x+151, $set_y+$mm);
  $pdf->Cell(54,5,"$".number_format($tot_cred,2,".",","),0,1,'R',0);
  $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
  $mm+=3;
  $mm+=2;
  $j++;
}

/**RECUPERACION**/
/*$sql = _query("SELECT f.fecha, f.id_factura, f.tipo_pago, f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
               c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
               FROM factura AS f
               LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
               LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
               LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
               WHERE f.finalizada=1
               AND f.tipo_pago LIKE '%PEN|%'
               AND f.id_apertura_pagada='$id_apertura'
               AND f.turno_pagado='$turno'
               ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");*/
               $sql = _query("SELECT f.fecha, f.id_factura, f.tipo_pago, f.tipo_documento, f.num_fact_impresa, f.total, f.anulada, f.hora,
                c.nombre AS cliente, e1.nombre AS vendedor, e.nombre AS cajero
                FROM factura AS f
                LEFT JOIN cliente AS c ON f.id_cliente=c.id_cliente
                LEFT JOIN empleados AS e1 ON f.id_vendedor=e1.id_empleado
                LEFT JOIN empleados AS e ON f.id_empleado=e.id_empleado
                WHERE f.finalizada=1
                AND f.tipo_pago LIKE '%PEN|%'
                AND f.id_apertura_pagada='$id_apertura'
                ORDER BY f.tipo_documento ASC, f.num_fact_impresa ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"RECUPERACION(CANCELACION)/DE VENTAS PENDIENTES DE PAGO A UN DIA(SIN CREDITO AUTORIZADO)",0,1,'C',0);
                $mm+=3;
                $mm+=2;
                $j++;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"FECHA",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+40, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+60, $set_y+$mm);
                $pdf->Cell(91,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+151, $set_y+$mm);
                $pdf->Cell(18,5,"TRANSF.",0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y+$mm);
                $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y+$mm);
                $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $tefe = 0;
                $tcheq = 0;
                $ttrans = 0;
                $mm+=3;
                $mm+=2;
                $j++;
                while ($row = _fetch_array($sql))
                {
                  if($page)
                    $salto = 56;
                  else
                    $salto = 62;
                  if($j>=$salto)
                  {
                    $page=0;
                    $pdf->AddPage();
                    $set_x = 5;
                    $set_y = 10;
                    $mm=0;

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetXY($set_x, $set_y+$mm);
                    $pdf->Cell(20,5,"FECHA",0,1,'C',0);
                    $pdf->SetXY($set_x+20, $set_y+$mm);
                    $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                    $pdf->SetXY($set_x+40, $set_y+$mm);
                    $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                    $pdf->SetXY($set_x+60, $set_y+$mm);
                    $pdf->Cell(91,5,"CLIENTE",0,1,'L',0);
                    $pdf->SetXY($set_x+151, $set_y+$mm);
                    $pdf->Cell(18,5,"TRANSF.",0,1,'R',0);
                    $pdf->SetXY($set_x+169, $set_y+$mm);
                    $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                    $pdf->SetXY($set_x+187, $set_y+$mm);
                    $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                    $set_y = 15;
                    $j=0;
                  }
                  $tipo_pago = explode("|",$row["tipo_pago"])[1];
                  $id_factura = $row["id_factura"];

                  $tipo_doc = $row["tipo_documento"];
                  if($tipo_doc=="COF"){
                    $tipo_doc="FAC";
                  }
                  $numero_doc = $row["num_fact_impresa"];
                  $cliente = utf8_decode($row["cliente"]);

                  $vendedor = $row["vendedor"];
                  $dven = explode(" ",$row["vendedor"]);
                  if(count($dven)>3)
                    $vendedor = $dven[0]." ".$dven[2];

                  $cajero = $row["cajero"];
                  $dcaj = explode(" ",$row["cajero"]);
                  if(count($dcaj)>3)
                    $cajero = $dcaj[0]." ".$dcaj[2];

                  if($vendedor == "")
                  {
                    $vendedor = $cajero;
                  }
                  $total = $row["total"];
                  $fecha = ED($row["fecha"]);
                  $anulada = $row["anulada"];
                  $hora = hora($row["hora"]);
                  $efe = 0;
                  $cheq = 0;
                  $trans = 0;
                  if($tipo_pago == "CON")
                  {
                    $efe = $total;
                  }
                  if($tipo_pago == "CHE")
                  {
                    $cheq = $total;
                  }
                  if($tipo_pago == "TRA")
                  {
                    $trans = $total;
                  }
                  $tefe += $efe;
                  $tcheq += $cheq;
                  $ttrans += $trans;
                  $tot_pend_pag+=$total;

                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(20,5,$fecha,0,1,'C',0);
                  $pdf->SetXY($set_x+20, $set_y+$mm);
                  $pdf->Cell(20,5,$tipo_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+40, $set_y+$mm);
                  $pdf->Cell(20,5,$numero_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+60, $set_y+$mm);
                  $pdf->Cell(91,5,$cliente,0,1,'L',0);
                  $pdf->SetXY($set_x+151, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($trans,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+169, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($cheq,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+187, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($efe,2,".",","),0,1,'R',0);
                  $mm+=3;
                  $j++;
                  if($tipo_doc == 'TIK')
                  {
                    $t_tike += 1;
                  }
                  else if($tipo_doc == 'FAC')
                  {
                    $t_factuta += 1;
                  }
                  else if($tipo_doc == 'CCF')
                  {
                    $t_credito += 1;
                  }
                }
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $mm+=3;
                $pdf->setTextColor(0,0,0);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(151,5,"TOTALES",0,1,'C',0);
                $pdf->SetXY($set_x+151, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($ttrans,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tcheq,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tefe,2,".",","),0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->Line($set_x+151,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $pdf->SetXY($set_x+151, $set_y+$mm+5);
                $pdf->Cell(54,5,"$".number_format($tot_pend_pag,2,".",","),0,1,'R',0);
                $mm+=3;
                $mm+=2;
                $j++;
              }

              /**ABONOS A CREDITO**/
/*$sql = _query("SELECT m.tipo_doc, m.numero_doc, m.hora,
               c.nombre AS cliente, m.valor AS abono, cre.fecha, ac.tipo_pago, ac.num_doc_pago
               FROM mov_caja AS m
               INNER JOIN abono_credito AS ac ON m.idtransace=ac.id_abono_credito
               INNER JOIN credito AS cre ON cre.id_credito = ac.id_credito
               LEFT JOIN cliente AS c ON cre.id_cliente=c.id_cliente
               WHERE m.idtransace!=''
               AND m.concepto='POR ABONO A CREDITO'
               AND m.id_apertura='$id_apertura'
               AND m.turno='$turno'
               ORDER BY m.tipo_doc ASC, m.numero_doc ASC");*/
               $sql = _query("SELECT m.tipo_doc, m.numero_doc, m.hora,
                c.nombre AS cliente, m.valor AS abono, cre.fecha,ac.tipo_doc AS doc_abono,ac.tipo_pago, ac.num_doc_pago
                FROM mov_caja AS m
                INNER JOIN abono_credito AS ac ON m.idtransace=ac.id_abono_credito
                INNER JOIN credito AS cre ON cre.id_credito = ac.id_credito
                LEFT JOIN cliente AS c ON cre.id_cliente=c.id_cliente
                WHERE m.idtransace!=''
                AND m.concepto='POR ABONO A CREDITO'
                AND m.id_apertura='$id_apertura'
                ORDER BY m.tipo_doc ASC, m.numero_doc ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"RECUPERACION(CANCELACION)/ABONOS DE VENTAS AL CREDITO AUTORIZADOS 30 DIAS O MAS",0,1,'C',0);
                $mm+=5;
                $j+=1;
  //$mm+=2;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"FECHA",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+40, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+60, $set_y+$mm);
                $pdf->Cell(91,5,"CLIENTE",0,1,'L',0);
                $pdf->SetXY($set_x+151, $set_y+$mm);
                $pdf->Cell(18,5,"TRANSF.",0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y+$mm);
                $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y+$mm);
                $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $mm+=6;
                $j+=2;
  //$mm+=2;
                $tefe = 0;
                $tcheq = 0;
                $ttrans = 0;
                $tot_parc = 0;
                $tot_rec_ab = 0;
                $tret = "";
                while ($row = _fetch_array($sql))
                {
                  if($page)
                    $salto = 60;
                  else
                    $salto = 75;
                  if($j>=$salto)
                  {
                    $page=0;
                    $pdf->AddPage();
                    $set_x = 5;
                    $set_y = 10;
                    $mm=0;
      //$mm+=2;
                    $pdf->SetFont('Arial','',8);
                    $pdf->SetXY($set_x, $set_y+$mm);
                    $pdf->Cell(20,5,"FECHA",0,1,'C',0);
                    $pdf->SetXY($set_x+20, $set_y+$mm);
                    $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                    $pdf->SetXY($set_x+40, $set_y+$mm);
                    $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                    $pdf->SetXY($set_x+60, $set_y+$mm);
                    $pdf->Cell(91,5,"CLIENTE",0,1,'L',0);
                    $pdf->SetXY($set_x+151, $set_y+$mm);
                    $pdf->Cell(18,5,"TRANSF.",0,1,'R',0);
                    $pdf->SetXY($set_x+169, $set_y+$mm);
                    $pdf->Cell(18,5,"CHEQUE",0,1,'R',0);
                    $pdf->SetXY($set_x+187, $set_y+$mm);
                    $pdf->Cell(18,5,"EFECTIVO",0,1,'R',0);
                    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                    $set_y = 15;
                    $j=0;
                  }
                  $tipo_doc = $row["tipo_doc"];
                  if($tipo_doc == "COF")
                  {
                    $tipo_doc = "FAC";
                  }
                  else
                  {
                    $tipo_doc = $tipo_doc;
                  }
                  $numero_doc = $row["numero_doc"];
                  $cliente = utf8_decode($row["cliente"]);

                  $tipo_pago = $row["tipo_pago"];
                  $doc_abono = utf8_decode(trim($row["doc_abono"]));
                  $num_doc_pago = $row["num_doc_pago"];
                  $total = $row["abono"];
                  $fecha = ED($row["fecha"]);
                  $hora = hora($row["hora"]);
                  $efe = 0;
                  $cheq = 0;
                  $trans = 0;
                  if($tipo_pago == "Efectivo")
                  {
                    $efe = $total;
                  }
                  if($tipo_pago == "Cheque")
                  {
                    $cheq = $total;
                  }
                  if($tipo_pago == "Transferencia")
                  {
                    $trans = $total;
                  }
                  $tcheq += $cheq;
                  $ttrans += $trans;


                  $tot_rec_ab += $total;
                  $tot_parc += $total;
                  $tefe += $efe;
                  if(stripos($doc_abono,"Nota")=== false){

                    $tot_abonos += $efe;
                  }
                  else {
                    $cliente.=" (N RET.)";
                    $efe = "-".$efe;
                    $trans = "-".$trans;
                    $cheq = "-".$cheq;
                    $tret+=$efe;
                  }
                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(20,5,$fecha,0,1,'C',0);
                  $pdf->SetXY($set_x+20, $set_y+$mm);
                  $pdf->Cell(20,5,$tipo_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+40, $set_y+$mm);
                  $pdf->Cell(20,5,$numero_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+60, $set_y+$mm);
                  $pdf->Cell(91,5,$cliente,0,1,'L',0);
                  $pdf->SetXY($set_x+151, $set_y+$mm);
                  if($trans==""){
                    $pdf->Cell(18,5,"",0,1,'R',0);
                  }else{
                    $pdf->Cell(18,5,"$".number_format($trans,2,".",","),0,1,'R',0);
                  }
                  $pdf->SetXY($set_x+169, $set_y+$mm);
                  if($cheq==""){
                    $pdf->Cell(18,5,"",0,1,'R',0);
                  }else{
                    $pdf->Cell(18,5,"$".number_format($cheq,2,".",","),0,1,'R',0);
                  }
                  $pdf->SetXY($set_x+187, $set_y+$mm);
                  $pdf->Cell(18,5,"$".number_format($efe,2,".",","),0,1,'R',0);
                  $mm+=3;
                  $j++;
                }
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $mm+=3;
                $j++;
                $pdf->setTextColor(0,0,0);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(151,5,"SUBTOTALES",0,1,'R',0);
                $pdf->SetXY($set_x, $set_y+$mm+5);
                $pdf->Cell(151,5,"TOTAL",0,1,'R',0);
                $pdf->SetXY($set_x+151, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($ttrans,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+169, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tcheq,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+187, $set_y+$mm);
                $pdf->Cell(18,5,"$".number_format($tefe,2,".",","),0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->Line($set_x+151,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $pdf->SetXY($set_x+151, $set_y+$mm+5);
                $pdf->Cell(54,5,"$".number_format($tot_parc,2,".",","),0,1,'R',0);
                $mm+=6;
                $j+=2;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->SetXY($set_x, $set_y+$mm+5);
                $pdf->Cell(151,5,"MENOS: NOTAS RETENCION",0,1,'R',0);
                $pdf->Line($set_x+151,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $pdf->SetXY($set_x+151, $set_y+$mm+5);
                $pdf->Cell(54,5,"$".number_format($tret,2,".",","),0,1,'R',0);
                $mm+=6;
                 $j+=2;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->SetXY($set_x, $set_y+$mm+5);
                $pdf->Cell(151,5,"TOTAL ABONOS",0,1,'R',0);
                $pdf->Line($set_x+151,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $pdf->SetXY($set_x+151, $set_y+$mm+5);
                $pdf->Cell(54,5,"$".number_format($tot_parc+$tret,2,".",","),0,1,'R',0);
                $mm+=6;
                $j+=2;
              }

              /**VALES**/
/*$sql = _query("SELECT m.numero_doc, m.tipo_doc, m.iva, m.concepto,
               m.valor, m.hora
               FROM mov_caja AS m
               WHERE m.salida =1
               AND m.id_apertura='$id_apertura'
               AND m.turno='$turno'
               ORDER BY m.hora ASC");*/
               $sql = _query("SELECT m.numero_doc, m.tipo_doc, m.iva, m.concepto,
                m.valor, m.hora
                FROM mov_caja AS m
                WHERE m.salida =1
                AND m.id_apertura='$id_apertura'
                ORDER BY m.hora ASC");
               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"VALES",0,1,'C',0);
                $mm+=3;
                $mm+=2;
                $j++;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(15,5,"HORA",0,1,'C',0);
                $pdf->SetXY($set_x+15, $set_y+$mm);
                $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                $pdf->SetXY($set_x+35, $set_y+$mm);
                $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                $pdf->SetXY($set_x+55, $set_y+$mm);
                $pdf->Cell(90,5,"CONCEPTO",0,1,'L',0);
                $pdf->SetXY($set_x+145, $set_y+$mm);
                $pdf->Cell(20,5,"MONTO",0,1,'R',0);
                $pdf->SetXY($set_x+165, $set_y+$mm);
                $pdf->Cell(20,5,"IVA",0,1,'R',0);
                $pdf->SetXY($set_x+185, $set_y+$mm);
                $pdf->Cell(20,5,"TOTAL",0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $mm+=3;
                $mm+=2;
                $j++;
                $tot_iva = 0;
                $tot_sum = 0;
                while ($row = _fetch_array($sql))
                {
                  if($page)
                    $salto = 56;
                  else
                    $salto = 62;
                  if($j>=$salto)
                  {
                    $page=0;
                    $pdf->AddPage();
                    $set_x = 5;
                    $set_y = 10;
                    $mm=0;

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetXY($set_x, $set_y+$mm);
                    $pdf->Cell(15,5,"HORA",0,1,'C',0);
                    $pdf->SetXY($set_x+15, $set_y+$mm);
                    $pdf->Cell(20,5,"TIPO DOC",0,1,'C',0);
                    $pdf->SetXY($set_x+35, $set_y+$mm);
                    $pdf->Cell(20,5,utf8_decode("NÚMERO"),0,1,'C',0);
                    $pdf->SetXY($set_x+55, $set_y+$mm);
                    $pdf->Cell(90,5,"CONCEPTO",0,1,'L',0);
                    $pdf->SetXY($set_x+145, $set_y+$mm);
                    $pdf->Cell(20,5,"MONTO",0,1,'R',0);
                    $pdf->SetXY($set_x+165, $set_y+$mm);
                    $pdf->Cell(20,5,"IVA",0,1,'R',0);
                    $pdf->SetXY($set_x+185, $set_y+$mm);
                    $pdf->Cell(20,5,"TOTAL",0,1,'R',0);
                    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                    $set_y = 15;
                    $j=0;
                  }
                  $tipo_doc = $row["tipo_doc"];
                  if($tipo_doc=="COF"){
                    $tipo_doc="FAC";;
                  }
                  $numero_doc = $row["numero_doc"];
                  $concepto=$row["concepto"];
                  if(strlen($concepto)>75)
                  {
                    $h=ceil($concepto/75);
                    $concep=divtextlin($concepto,75,$h);
                    $nn = 0;
                    $jj=1;
                    foreach ($concep as $val)
                    {

                      $pdf->SetXY($set_x+55, $set_y+$mm+$nn);
                      $pdf->Cell(90,5,utf8_decode(ucFirst(strtolower($val))),0,1,'L',0);
                      $nn+= 5;
                      $jj++;
                    }
                    $lwidth = $nn;
                    if($ancho>$lwidth)
                    {
                      $anchof=$ancho;
                    }else{
                      $anchof=$lwidth;
                    }
                    $pdf->SetXY($set_x+55, $set_y+$mm);
                    $pdf->Cell(90,$anchof,"",0,1,'C',0);
                  }
                  else
                  {
                    $anchof=5;
                    $pdf->SetXY($set_x+55, $set_y+$mm);
                    $pdf->Cell(90,5,utf8_decode(ucFirst(strtolower($concepto))),0,1,'L',0);
                  }
                  $total = $row["valor"];
                  $iva = $row["iva"];
                  $monto = $total - $iva;
                  $hora = hora($row["hora"]);
                  $tot_vales+=$total;
                  $tot_sum+=$monto;
                  $tot_iva+=$iva;

                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(15,$anchof,$hora,0,1,'C',0);
                  $pdf->SetXY($set_x+15, $set_y+$mm);
                  $pdf->Cell(20,$anchof,$tipo_doc,0,1,'C',0);
                  $pdf->SetXY($set_x+35, $set_y+$mm);
                  $pdf->Cell(20,$anchof,$numero_doc,0,1,'C',0);
                  /*$pdf->SetXY($set_x+55, $set_y+$mm);
                  $pdf->Cell(90,$anchof,$concepto,0,1,'L',0);*/
                  $pdf->SetXY($set_x+145, $set_y+$mm);
                  $pdf->Cell(20,$anchof,"$".number_format($monto,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+165, $set_y+$mm);
                  $pdf->Cell(20,$anchof,"$".number_format($iva,2,".",","),0,1,'R',0);
                  $pdf->SetXY($set_x+185, $set_y+$mm);
                  $pdf->Cell(20,$anchof,"$".number_format($total,2,".",","),0,1,'R',0);
                  /*$mm+=3;
                  $j++;*/
                  $mm+=$anchof;
                  $j+=$anchof/5;
                }
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $mm+=2;
                $pdf->setTextColor(0,0,0);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(145,5,"TOTAL VALES",0,1,'C',0);
                $pdf->SetXY($set_x+145, $set_y+$mm);
                $pdf->Cell(20,5,"$".number_format($tot_sum,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+165, $set_y+$mm);
                $pdf->Cell(20,5,"$".number_format($tot_iva,2,".",","),0,1,'R',0);
                $pdf->SetXY($set_x+185, $set_y+$mm);
                $pdf->Cell(20,5,"$".number_format($tot_vales,2,".",","),0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $mm+=3;
                $mm+=3;
                $j++;
              }


              /**VIATICO**/
/*$sql = _query("SELECT m.numero_doc, m.tipo_doc, m.concepto, m.nombre_recibe,
               m.valor, m.hora, m.tipo_delige
               FROM mov_caja AS m
               WHERE m.viatico =1
               AND m.id_apertura='$id_apertura'
               AND m.turno='$turno'
               ORDER BY m.hora ASC");*/
               $sql = _query("SELECT m.numero_doc, m.tipo_doc, m.concepto, m.nombre_recibe,
                m.valor, m.hora, m.tipo_delige, m.id_movimiento, m.valor
                FROM mov_caja AS m
                WHERE m.viatico =1
                AND m.id_apertura='$id_apertura'
                ORDER BY m.hora ASC");

               $num = _num_rows($sql);
               if($num > 0)
               {
                $mm+=3;
                $j++;
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(205,5,"VIATICOS",0,1,'C',0);
                $mm+=3;
                $j++;
                $mm+=2;
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(20,5,"HORA",0,1,'C',0);
                $pdf->SetXY($set_x+20, $set_y+$mm);
                $pdf->Cell(30,5,"TIPO",0,1,'C',0);
                $pdf->SetXY($set_x+50, $set_y+$mm);
                $pdf->Cell(60,5,"RECIBE",0,1,'L',0);
                $pdf->SetXY($set_x+110, $set_y+$mm);
                $pdf->Cell(75,5,"CONCEPTO",0,1,'L',0);
                $pdf->SetXY($set_x+185, $set_y+$mm);
                $pdf->Cell(20,5,"TOTAL",0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm+5,$set_x+205,$set_y+$mm+5);
                $mm+=6;
  //$mm+=2;
                $j+=2;
                while ($row = _fetch_array($sql))
                {
                  if($page)
                    $salto = 60;
                  else
                    $salto = 75;
                  if($j>=$salto)
                  {
                    $page=0;
                    $pdf->AddPage();
                    $set_x = 5;
                    $set_y = 10;
                    $mm=0;
      //$mm+=2;
                    $pdf->SetFont('Arial','',8);
                    $pdf->SetXY($set_x, $set_y+$mm);
                    $pdf->Cell(20,5,"HORA",0,1,'C',0);
                    $pdf->SetXY($set_x+20, $set_y+$mm);
                    $pdf->Cell(30,5,"TIPO",0,1,'C',0);
                    $pdf->SetXY($set_x+50, $set_y+$mm);
                    $pdf->Cell(60,5,"RECIBE",0,1,'L',0);
                    $pdf->SetXY($set_x+110, $set_y+$mm);
                    $pdf->Cell(75,5,"CONCEPTO",0,1,'L',0);
                    $pdf->SetXY($set_x+185, $set_y+$mm);
                    $pdf->Cell(20,5,"TOTAL",0,1,'R',0);
                    $pdf->Line($set_x,$set_y+5,$set_x+205,$set_y+5);
                    $set_y = 15;
                    $j=0;
                  }
                  $concepto = utf8_decode($row["concepto"]);
                  $nombre_recibe = utf8_decode($row["nombre_recibe"]);

                  $tipo = ucfirst($row["tipo_delige"]);
                  $total = $row["valor"];
                  $hora = hora($row["hora"]);
                  $tot_viatico+=$total;
                  if(strlen($concepto)>65)
                  {
                    $h=ceil($concepto/65);
                    $concep=divtextlin($concepto,65,$h);
                    $nn = 0;
                    $jj=1;
                    foreach ($concep as $val)
                    {

                      $pdf->SetXY($set_x+110, $set_y+$mm+$nn);
                      $pdf->Cell(75,5,utf8_decode(ucFirst(strtolower($val))),0,1,'L',0);
                      $nn+= 5;
                      $jj++;
                    }
                    $lwidth = $nn;
                    if($ancho>$lwidth)
                    {
                      $anchof=$ancho;
                    }else{
                      $anchof=$lwidth;
                    }
                    $pdf->SetXY($set_x+110, $set_y+$mm);
                    $pdf->Cell(75,$anchof,"",0,1,'C',0);
                  }
                  else
                  {
                    $anchof=5;
                    $pdf->SetXY($set_x+110, $set_y+$mm);
                    $pdf->Cell(75,5,utf8_decode(ucFirst(strtolower($concepto))),0,1,'L',0);
                  }

                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(20,$anchof,$hora,0,1,'C',0);
                  $pdf->SetXY($set_x+20, $set_y+$mm);
                  $pdf->Cell(30,$anchof,$tipo,0,1,'C',0);
                  $pdf->SetXY($set_x+50, $set_y+$mm);
                  $pdf->Cell(60,$anchof,$nombre_recibe,0,1,'L',0);
                  $pdf->SetXY($set_x+185, $set_y+$mm);
                  $pdf->Cell(20,$anchof,"$".number_format($total,2,".",","),0,1,'R',0);
                  $mm+=$anchof;
                  $j+=$anchof/5;
                }
                if($j>=$salto)
                {
                  $page=0;
                  $pdf->AddPage();
                  $set_y = 10;
                  $mm=0;
                  $j=0;
                }
                $pdf->setTextColor(0,0,0);
                $pdf->SetXY($set_x, $set_y+$mm);
                $pdf->Cell(185,5,"TOTAL VIATICOS",0,1,'C',0);
                $pdf->SetXY($set_x+185, $set_y+$mm);
                $pdf->Cell(20,5,"$".number_format($tot_viatico,2,".",","),0,1,'R',0);
                $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
                $mm+=3;
  //$mm+=2;
                $j++;
              }
////__________________
//////////////////////////////Comienza ingresos
//$pdf->SetTextColor(0,0,0);
//$sqll = "SELECT  *FROM mov_caja WHERE entrada='1' AND  date(fecha) BETWEEN '$fini' AND '$ffin' AND idtransace='0' AND id_sucursal='$id_sucursal'";
              $sqls ="SELECT *FROM mov_caja
              WHERE entrada =1
              AND idtransace='0'
              AND id_apertura='$id_apertura'
              ORDER BY hora ASC";
/*if($turno_r!=""){
  $sqls.=" AND turno='$turno_r'";
}*/
//$set_y = 55;
$resul = _query($sqls);
if(_num_rows($resul)>0)
{
  $mm+= 10;
  $j+=2;
  if($j>=$salto)
  {
    $page++;
    $pdf->AddPage();
    $mm=0;
    $set_x = 10;
    $set_y = 10;
    $j=0;
    $i=0;
    $pdf->SetFont('Arial','',8);
  }
  $pdf->SetFont('Arial','',9);
  $pdf->SetXY($set_x, $set_y+$mm-5);
  $pdf->Cell(18,5,utf8_decode("INGRESOS"),0,1,'C',0);
  $pdf->SetFont('Arial','',8);
  $pdf->Line($set_x,$set_y+$mm,$set_x+205,$set_y+$mm);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(15,5,utf8_decode("N°"),'B',1,'L',0);
  $pdf->SetXY($set_x+15, $set_y+$mm);
  $pdf->Cell(18,5,"FECHA",'B',1,'L',0);
  $pdf->SetXY($set_x+33, $set_y+$mm);
  $pdf->Cell(156,5,"CONCEPTO",'B',1,'L',0);
  $pdf->SetXY($set_x+189, $set_y+$mm);
  $pdf->Cell(18,5,"MONTO",'B',1,'R',0);
  $j+=1;
  $mm+=5;

  if($j==1)
  {
  //Fecha de impresion y numero de pagina
    $pdf->SetXY(4, 210);
    $pdf->Cell(10, 0.4,$titulo, 0, 0, 'L');
    $pdf->SetXY(70, 210);
    $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
    $pdf->SetXY(258, 210);
    $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
  }

  $suma_m3=0;
  $count3=1;
  $ingreso_caja=0;
  while($row2 = _fetch_array($resul))
  {
    if($page==0)
      $salto = 43;
    else
      $salto = 63;
    if($j>=$salto)
    {
      $page++;
      $pdf->AddPage();
      /*$pdf->SetFont('Latin','',10);
      $pdf->Image($logo,9,4,50,18);
      //$pdf->Image($logo1,245,8,24.5,24.5);
      $set_x = 0;
      */
      $set_y = 5;
      $mm=0;
      //Encabezado General
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(15,5,utf8_decode("N°"),'B',1,'L',0);
      $pdf->SetXY($set_x+15, $set_y+$mm);
      $pdf->Cell(18,5,"FECHA",'B',1,'L',0);
      $pdf->SetXY($set_x+33, $set_y+$mm);
      $pdf->Cell(156,5,"CONCEPTO",'B',1,'L',0);
      $pdf->SetXY($set_x+189, $set_y+$mm);
      $pdf->Cell(18,5,"MONTO",'B',1,'R',0);
      //$set_x = 10;
      $set_y = 10;
      $j=0;
      $i=0;
      $pdf->SetFont('Arial','',8);
    }
    $fecha2 = $row2["fecha"];

    $ultcosto2 = "cuenta";
    $empleado2 = $row2["nombre_recibe"];
    $diligencia2 = $row2["tipo_delige"];
    $concepto2 = $row2["concepto"];
    $pdf->SetFont('Arial','',8);
    if(strlen($concepto2)>110)
    {
      $h2=ceil($concepto2/110);
      $concep2=divtextlin($concepto2,110,$h2);
      $nn2 = 0;
      foreach ($concep2 as $val2)
      {
        if($j>=$salto)
        {
          $page++;
          $pdf->AddPage();
          $set_x = 10;
          $set_y = 10;
          $i=0;
          $j=0;
          $pdf->SetFont('Arial','',8);
        }
        $pdf->SetXY($set_x+33, $set_y+$mm+$nn2);
        $pdf->Cell(156,5,utf8_decode(ucFirst(strtolower($val2))),0,1,'L',0);
        $nn2 += 5;
        $j++;
      }
      $lwidth2 = $nn2;
      $pdf->SetXY($set_x+33, $set_y+$mm);
      $pdf->Cell(156,$lwidth2,"",0,1,'C',0);
    }
    else
    {
      $lwidth2=5;
      $pdf->SetXY($set_x+33, $set_y+$mm);
      $pdf->Cell(156,$lwidth2,utf8_decode(ucFirst(strtolower($concepto2))),0,1,'L',0);
      $j++;
    }
    $monto3 = $row2["valor"];
    $caja2 = $row2["caja"];
    $apertura2 = $row2["id_apertura"];
    $turno2 = $row2["turno"];
    $pdf->SetXY($set_x, $set_y+$mm);
    $pdf->Cell(15,$lwidth2,$count3,0,1,'L',0);
    $pdf->SetXY($set_x+15, $set_y+$mm);
    $pdf->Cell(18,$lwidth2,ED($fecha2),0,1,'L',0);
    //$pdf->SetXY($set_x+33, $set_y+$mm);
    //$pdf->Cell(66,$lwidth2,utf8_decode(ucFirst(strtolower($empleado2))),1,1,'L',0);
    //$pdf->SetXY($set_x+99, $set_y+$mm);
    //$pdf->Cell(20,$lwidth2,ucFirst($diligencia2),1,1,'L',0);

    /*if(strlen($detalle2)>=90){
    $detalle2=substr($detalle2,0,90);
  }
  $detal2=divtextlin($detalle2,90,2);
  foreach ($detal2 as $deta2) {
  $pdf->SetXY($set_x+140, $set_y+$mm+20);
  $pdf->Cell(100,5,ucFirst($deta2),1,1,'L',0);
}*/
$pdf->SetXY($set_x+189, $set_y+$mm);
$pdf->Cell(18,$lwidth2,"$".number_format($monto3,2,".",","),0,1,'R',0);
$ingreso_caja+=$monto3;
$count3+=1;
//$mm+=5;
$mm+=$lwidth2;
//$j++;
$pdf->line($set_x, $set_y+$mm,$set_x+207, $set_y+$mm);
if($j==1)
{
  //Fecha de impresion y numero de pagina
  $pdf->SetXY(4, 210);
  $pdf->Cell(10, 0.4,$titulo, 0, 0, 'L');
  $pdf->SetXY(70, 210);
  $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
  $pdf->SetXY(258, 210);
  $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
}
$suma_m3+=$monto3;
}
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(240,5,"TOTAL",0,1,'C',0);
$pdf->SetXY($set_x+189, $set_y+$mm);
$pdf->Cell(18,5,"$".number_format($suma_m3,2,".",","),0,1,'R',0);
$mm+= 5 ;
$j+= 1 ;
$tot_ingreso=$suma_m3;
}

////_______________FINALIZA INGRESO A CAJA CHICA
$mm+=5;
$j+=1;
if($page)
  $salto = 43;
else
  $salto = 60;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $mm=0;
  $j=0;
}
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(90,5,"TOTAL FACTURADO:",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($tot_contado+$tot_cred+$tot_vpend),2,".",","),0,1,'R',0);
$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(95,5,"APERTURA CAJA CHICA:",0,1,'L',0);
$pdf->SetXY($set_x+130, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($caja_chica),2,".",","),0,1,'R',0);
$mm+=3;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $j = 0;
  $mm = 0;
}
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(90,5,"TOTAL RECUPERACION:",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($tot_pend_pag+$tot_rec_ab+$tret),2,".",","),0,1,'R',0);
$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(95,5,"TOTAL VALES:",0,1,'L',0);
$pdf->SetXY($set_x+130, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format($tot_vales,2,".",","),0,1,'R',0);

$mm+=3;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $j = 0;
  $mm = 0;
}

$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(90,5,"TOTAL CONTADO:",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($tot_contado),2,".",","),0,1,'R',0);
$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(95,5,"TOTAL VIATICOS:",0,1,'L',0);
$pdf->SetXY($set_x+130, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format($tot_viatico,2,".",","),0,1,'R',0);
$mm+=3;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $j = 0;
  $mm = 0;
}
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(90,5,"REMESA (SISTEMA):",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($tot_contado+$tot_pend_pag+$tot_abonos+$tcheq),2,".",","),0,1,'R',0);
$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(95,5,"FONDO POR LIQUIDAR:",0,1,'L',0);
$pdf->SetXY($set_x+130, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($tot_vales+$tot_viatico-$ingreso_caja),2,".",","),0,1,'R',0);
$mm+=3;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $j = 0;
  $mm = 0;
}
$sql_rem_f = _query("SELECT sum(remesa) as monto FROM remesa_corte WHERE id_apertura='$id_apertura' AND turno='$turno'");
$dats_rem_f = _fetch_array($sql_rem_f);
$nremesa = $dats_rem_f["monto"];
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(90,5,"FONDO A REMESAR:",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format($nremesa,2,".",","),0,1,'R',0);
$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(95,5,"EFECTIVO EN CAJA:",0,1,'L',0);
$pdf->SetXY($set_x+130, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format(($caja_chica-$tot_vales-$tot_viatico+$tot_ingreso),2,".",","),0,1,'R',0);
$mm+=3;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $j = 0;
  $mm = 0;
}
$text = "";
//$dif = round($tot_contado+$tot_cred+$tot_vpend-$nremesa,2);
$dif = round($tot_contado+$tot_pend_pag+$tot_abonos+$tcheq-$nremesa,2);

if($dif > 0)
{
  $text = "FALTANTE: ";
  $vals = "(";
  $vals1 = ")";
}
if($dif < 0)
{
  $text = "SOBRANTE: ";
  $vals = "";
  $vals1 = "";
}
if($text != "")
{
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(90,5,$text,0,1,'L',0);
  $pdf->SetXY($set_x+30, $set_y+$mm);
  $pdf->Cell(20,5,$vals."$".number_format($dif,2,".",",").$vals1,0,1,'R',0);
}
//$difc = round(($caja_chica+$ingreso_caja)-$tot_vales-$tot_viatico-$saldo_caja_chica,2);
$difc = round($caja_chica-($tot_vales+$tot_viatico+$saldo_caja_chica-$tot_ingreso),2);
$texta = "";

if($difc > 0)
{
  $texta = "FALTANTE: ";
  $valsa = "(";
  $valsa1 = ")";
}
if($difc < 0)
{
  $texta = "SOBRANTE: ";
  $valsa = "";
  $valsa1 = "";
}

if($texta != "")
{
  $pdf->SetXY($set_x+90, $set_y+$mm);
  $pdf->Cell(95,5,$texta,0,1,'L',0);
  $pdf->SetXY($set_x+130, $set_y+$mm);
  $pdf->Cell(20,5,$valsa."$".number_format($difc,2,".",",").$valsa1,0,1,'R',0);
}
$sql_rem = _query("SELECT * FROM remesa_corte WHERE id_apertura='$id_apertura' AND turno='$turno'");
if(_num_rows($sql_rem)>0)
{
  $mm+=10;
  $j+=2;
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  $pdf->SetFont('Arial','',8);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(65,5,utf8_decode("REMESAS"),0,1,'C',0);
                ///TIEMPO INACTIVO DE COBRO
  $tmp = 0;
  $cont = 0;
  $ht = 0;
  $mt = 0;
  $st = 0;
  $lista_difs = "";
  $sql_ctime ="SELECT * FROM factura WHERE id_apertura='$id_apertura'";
  if($turno!=""){
    $sql_ctime.=" AND turno = '$turno'";
  }
  $sql_ctime.=" ORDER BY hora ASC";
  $sql_ctimee=_query($sql_ctime);
  while($row = _fetch_array($sql_ctimee))
  {
    $dif = $row["hora"];
                  //$lista_difs.= $tmp." + ".$dif." + ";
    if($cont>0)
    {
      $diferencia = RestarHoras($tmp,$dif);
                    //$lista_difs.= $diferencia."  -  ";
      list($h,$m,$s) = explode(":",$diferencia);
      if($h>0 || $m>10)
      {
        $ht += $h;
        $mt += $m;
        $st += $s;
      }
    }
    $tmp = $dif;
    $cont++;
  }
  /*
  $sql_timeu = _query("SELECT max(hora) as hora FROM factura WHERE fecha='$fecha_r' AND turno = '$turno_r'");
  $dats_timeu = _fetch_array($sql_timeu);
  if($dats_timeu["hora"] != "" && $dats_timeu["hora"] != "00:00:00")
  {
    $hora_co = $dats_timeu["hora"];
    $diffa = RestarHoras($hora_co,$hora_c);
    list($ha,$ma,$sa) = explode(":",$diffa);
    $ht += $ha;
    $mt += $ma;
    $st += $sa;
  }*/
  if(intdiv($mt,60)>0)
  {
    $ht += intdiv($mt,60);
    $mt = $mt%60;
  }
  $pdf->SetXY($set_x+90, $set_y+$mm);
  $pdf->Cell(55,4,"TIEMPO INACTIVO DE COBRO: ".$ht.":".$mt,0,0,'L',0);
  $sql_ctime_pre ="SELECT * FROM factura WHERE id_apertura='$id_apertura' AND numero_ref>0";
  if($turno!=""){
    $sql_ctime_pre.=" AND turno='$turno'";
  }
  $sql_ctime_pre.=" ORDER BY hora_preventa ASC";
  $sql_ctimee_pree=_query($sql_ctime_pre);
  if($sql_ctimee_pree){
   $tmp = 0;
   $cont = 0;
   $ht = 0;
   $mt = 0;
   $st = 0;
   $lista_difs = "";
   while($row = _fetch_array($sql_ctimee_pree))
   {
    $dif = $row["hora_preventa"];
                  //$lista_difs.= $tmp." + ".$dif." + ";
    if($cont>0)
    {
      $diferencia = RestarHoras($tmp,$dif);
                    //$lista_difs.= $diferencia."  -  ";
      list($h,$m,$s) = explode(":",$diferencia);
      if($h>0 || $m>10)
      {
        $ht += $h;
        $mt += $m;
        $st += $s;
      }
    }
    $tmp = $dif;
    $cont++;
  }
  if(intdiv($mt,60)>0)
  {
    $ht += intdiv($mt,60);
    $mt = $mt%60;
  }
  $pdf->SetXY($set_x+150, $set_y+$mm);
  $pdf->Cell(55,4,"TIEMPO INACTIVO PREVENTA: ".$ht.":".$mt,0,0,'L',0);
}
                ////FINALIZA EL TIEMPO DE COBRO
$mm+=3;
$mm+=2;
$j++;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $mm=0;
  $j=0;
}
                /////INICIA EL NUMERO DE TIK FAC CCF

$pdf->SetXY($set_x+90, $set_y+$mm);
$pdf->Cell(70,4,"TICKET: ".$t_tike."   FACTURA:".$t_factuta."   CREDITO FISCAL:".$t_credito,0,0,'L',0);
                //////FINALIZA EL NUMERO DE TIK FAC CCF
$mm+=3;
$mm+=2;
$j++;
if($page)
  $salto = 53;
else
  $salto = 63;
if($j>=$salto)
{
  $page=0;
  $pdf->AddPage();
  $set_y = 10;
  $mm=0;
  $j=0;
}
$pdf->Line($set_x,$set_y+$mm,$set_x+65,$set_y+$mm);
$pdf->SetFont('Arial','',8);
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(30,5,"NUMERO",0,1,'L',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"MONTO",0,1,'R',0);
$pdf->Line($set_x,$set_y+$mm+5,$set_x+65,$set_y+$mm+5);
$mm+=2;
$mm+=3;
$j++;
$n = 1;
$total_remesaa = 0;
while ($row_lab = _fetch_array($sql_rem))
{
  if($page)
    $salto = 55;
  else
    $salto = 60;
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_x = 5;
    $set_y = 5;
    $mm=0;
    $j=0;
  }
  $banco = $row_lab["banco"];
  $cuenta = $row_lab["cuenta"];
  $n_remesam = $row_lab["n_remesa"];
  $remesam = $row_lab["remesa"];
  $pdf->SetFont('Arial','',8);
  $pdf->SetXY($set_x, $set_y+$mm);
  $pdf->Cell(30,5,utf8_decode($n_remesam),0,1,'L',0);
  $pdf->SetXY($set_x+30, $set_y+$mm);
  $pdf->Cell(20,5,"$".number_format($remesam,2,".",","),0,1,'R',0);
  $total_remesaa += $remesam;
  $n+=1;
  $mm+=3;
  $j++;
}
$mm+=2;
$pdf->setTextColor(0,0,0);
$pdf->SetXY($set_x, $set_y+$mm);
$pdf->Cell(30,5,"TOTAL REMESA",0,1,'C',0);
$pdf->SetXY($set_x+30, $set_y+$mm);
$pdf->Cell(20,5,"$".number_format($total_remesaa,2,".",","),0,1,'R',0);
$pdf->Line($set_x,$set_y+$mm,$set_x+65,$set_y+$mm);
$mm+=3;
$j++;
}
$mm+=10;
$j++;
$j++;
if($pedido_pendiente !="")
{
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  $pdf->SetXY($set_x, $set_y+$mm);
  $pedido_pendiente_imp = divtextlin($pedido_pendiente,100);
  $npen = count($pedido_pendiente_imp);
  $pdf->Cell(185,5,"PEDIDOS PENDIENTES:  ".$pedido_pendiente_imp[0],0,1,'L',0);
  $mm+=3;
  $j++;
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  if($npen >1)
  {
    $pdf->SetXY($set_x, $set_y+$mm);
    $pdf->Cell(185,5,$pedido_pendiente_imp[1],0,1,'L',0);
    $mm+=10;
    $j++;
    $j++;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_y = 10;
      $mm=0;
      $j=0;
    }
  }
  else
  {
    $mm+=3;
    $j++;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_y = 10;
      $mm=0;
      $j=0;
    }
  }
}
if($cobro_pendiente !="")
{
  $pdf->SetXY($set_x, $set_y+$mm);
  $cobro_pendiente_imp = divtextlin($cobro_pendiente, 100);
  $ncob = count($cobro_pendiente_imp);
  $pdf->Cell(185,5,"COBROS PENDIENTES:  ".$cobro_pendiente_imp[0],0,1,'L',0);
  $mm+=3;
  $j++;
  if($j>=$salto)
  {
    $page=0;
    $pdf->AddPage();
    $set_y = 10;
    $mm=0;
    $j=0;
  }
  if($ncob >1)
  {
    $pdf->SetXY($set_x, $set_y+$mm);
    $pdf->Cell(185,5,$cobro_pendiente_imp[1],0,1,'L',0);
    $mm+=10;
    $j++;
    $j++;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_y = 10;
      $mm=0;
      $j=0;
    }
  }
  else
  {
    $mm+=3;
    $j++;
    if($j>=$salto)
    {
      $page=0;
      $pdf->AddPage();
      $set_y = 10;
      $mm=0;
      $j=0;
    }
  }
}
if($mensaje !="")
{
  $pdf->SetXY($set_x, $set_y+$mm);
  $mensaje_imp = divtextlin($mensaje, 110);
  $nmen = count($mensaje_imp);
  $pdf->Cell(185,5,"MENSAJE:  ".$mensaje_imp[0],0,1,'L',0);
  $mm+=3;
  $j++;
  if($nmen >1)
  {
    $pdf->SetXY($set_x, $set_y+$mm);
    $pdf->Cell(185,5,$mensaje_imp[1],0,1,'L',0);
  }
}
ob_clean();
$pdf->Output("corte_caja.pdf","I");
