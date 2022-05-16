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
$sql_su = _query("SELECT * FROM sucursal WHERE id_sucursal = '$id_sucursal'");
$row_su = _fetch_array($sql_su);
$descripcion = $row_su["descripcion"];
$direccion = $row_su["direccion"];
$explode = explode("|", $direccion);
$d1 = $explode[0];
$d2 = $explode[1];

$telefono = $row_su["telefono"];
$id_sucursal1 = $_REQUEST["sucursal"];
$id_cotizacion=$_REQUEST['id_cotizacion'];
$sql="SELECT co.fecha, co.atencion, co.total, co.numero_doc, co.vigencia, co.entrega, co.pago, co.hora,co.tipo, c.nombre as cliente, c.telefono1, c.telefono2, c.direccion, e.nombre as vendedor, ee.nombre as empleado, c.retiene, c.retiene10,
co.tipo_doc, c.nrc, ee.cargo
FROM cotizacion AS co
JOIN clientes AS c ON c.id_cliente=co.id_cliente
JOIN empleados AS e ON e.id_empleado=co.id_vendedor
JOIN empleados AS ee ON ee.id_empleado=co.id_empleado
WHERE co.id_cotizacion='$id_cotizacion'";

$result=_query($sql);
$row=_fetch_array($result);

$fecha=$row['fecha'];
$total=$row['total'];
$numero_doc=$row['numero_doc'];
$exx = explode("_", $numero_doc);
$numero_doc = ltrim($exx[1], "0");
$cliente=Mayu(utf8_decode($row['cliente']));
$longitud = strlen($cliente);
$vendedor=$row['vendedor'];
$empleado=$row['empleado'];
$vigencia=$row['vigencia'];
$atencion = $row["atencion"];
$entrega = $row["entrega"];
$tel_cliente = $row["telefono1"];
$tipo_doc = $row["tipo_doc"];
$cargo = $row["cargo"];
$nrc = $row["nrc"];

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
$titulo=utf8_decode("COTIZACIÓN N° ").$numero_doc;
$fecha_detalle = "";

if($fecha!="")
{
    list($a,$m,$d) = explode("-", $fecha);
    $fecha_detalle= "San Miguel, ".$d." de ".ucwords(Minu(meses($m)))." de ".$a;
}

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
$sy = 10;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(195,5,utf8_decode($fecha_detalle),0,1,'L');
$pdf->SetXY($set_x, $set_y+$sy);
$pdf->Cell(195,5,$titulo,0,1,'L');
$sy+=5;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+$sy);
$pdf->Cell(195,5,utf8_decode("CLIENTE:"),0,1,'L');
$pdf->SetFont('Latin','',9);
$pdf->SetXY($set_x+26, $set_y+15);
$pdf->Cell(195,5,utf8_decode(": ".$cliente),0,1,'L');
$sy += 5;
if($direccion_cliente != "")
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("DIRECCIÓN"),0,1,'L');
  $pdf->SetFont('Latin','',9);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".Mayu($direccion_cliente)),0,1,'L');
  $sy += 5;
}
if($tel_cliente != "0000-0000")
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("TELÉFONO"),0,1,'L');
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".$tel_cliente),0,1,'L');
  $sy += 5;
}
if($tipo_doc == "CCF")
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("NRC"),0,1,'L');
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".$nrc),0,1,'L');
  $sy += 5;
}
$saludo = "Reciban un cordial saludo, deseándoles éxitos en sus labores. Seguro de poder brindarle un servicio de alta calidad,
quedamos a sus órdenes para cualquier información adicional, y en espera de una pronta y favorable respuesta a la presente oferta.";

$pdf->SetXY($set_x, $set_y+$sy);
$pdf->MultiCell(195,5,utf8_decode($saludo),0,'L',0);
$set_y = 45+$sy;
$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(25,5,utf8_decode("CANTIDAD"),0,1,'L');
$pdf->SetXY($set_x+30, $set_y);
$pdf->Cell(110,5,utf8_decode("DESCRIPCIÓN"),0,1,'L');
$pdf->SetXY($set_x+140, $set_y);
$pdf->Cell(30,5,utf8_decode("PRECIO"),0,1,'R');
$pdf->SetXY($set_x+165, $set_y);
$pdf->Cell(30,5,utf8_decode("TOTAL"),0,1,'R');
$set_y = 50+$sy;
$pdf->Line($set_x, $set_y, 205, $set_y);

/*$result1 = _query("SELECT dc.id_prod_serv, pr.descripcion, pr.marca, pr.modelo, pr.serie, dc.proviene, dc.vencimiento, dc.cantidad, dc.precio_venta, dc.id_presentacion, dc.subtotal
  FROM cotizacion_detalle as dc, productos as pr
  WHERE pr.id_producto=dc.id_prod_serv AND dc.id_cotizacion='$id_cotizacion'");*/
$result1 = _query("SELECT dc.id_prod_serv, dc.proviene, dc.vencimiento, dc.cantidad, dc.precio_venta, dc.id_presentacion, dc.subtotal, dc.tipo_prod_serv
  FROM cotizacion_detalle as dc
  WHERE dc.id_cotizacion='$id_cotizacion'");
if(_num_rows($result1)>0)
{
  $iva=$row_su['iva']/100;//iva por sucursal
  $suma_subtotal=0;
  $suma_iva=0;
  $iva_cu=0;
  $sum_subt = 0;
  $prr = 0;
  while($row = _fetch_array($result1))
  {
    $rr = 5;
    $rrr = 0;
    if($page==0)
    $salto = 39;
    else
    $salto = 39;
    if($j>=$salto)
    {
      $page++;
      $pdf->AddPage();
      $set_x = 0;
      $set_y = 6;
      $mm = 0;
      $i = 0;
      $j = 0;
    }
    $id_producto = $row["id_prod_serv"];
    $cantidad_s = $row["cantidad"];
    $subt=$row["subtotal"];
    $precio_venta = $row["precio_venta"];
    $id_presentacion = $row["id_presentacion"];
    $procedencia = $row["proviene"];
    $vencimiento = $row["vencimiento"];

    $tipo_prod_serv = $row["tipo_prod_serv"];
    if($tipo_prod_serv == "PRODUCTO")
    {
      $sql_ps = _query("SELECT pr.descripcion, pr.marca, pr.modelo, pr.serie
      FROM productos as pr
      WHERE pr.id_producto= '$id_producto'");
      $row_ps = _fetch_array($sql_ps);
      $marca = $row_ps["marca"];
      $modelo = $row_ps["modelo"];
      $serie = $row_ps["serie"];
      $descripcion_ps =$row_ps["descripcion"];
      $prr+=1;
    }
    if($tipo_prod_serv == "SERVICIO")
    {
      $sql_s = _query("SELECT sr.descripcion
      FROM servicios as sr
      WHERE sr.id_servicio= '$id_producto'");
      $row_s = _fetch_array($sql_s);
      $descripcion_ps = $row_s["descripcion"];
      $marca = "";
      $modelo = "";
      $serie = "";
    }
    if($tipo_doc=="COF"){
      if($tipo_prod_serv == "PRODUCTO")
      {
        $subt_mostrar = utf8_decode("$ ".number_format($subt, 2));
        $precio_producto= utf8_decode("$ ".number_format($precio_venta, 2));
      }
      if($tipo_prod_serv == "SERVICIO")
      {
        if($subt > 0)
        {
          $subt_mostrar = utf8_decode("$ ".number_format($subt, 2));
          $precio_producto= utf8_decode("$ ".number_format($precio_venta, 2));
        }
        else
        {
          $subt_mostrar = "";
          $precio_producto= "";
          $cantidad_s = "";
        }
      }
    }
    if($tipo_doc=="CCF"){
      $subt_mostrar = utf8_decode("$ ".number_format($subt, 2));
      $iva_cu=$subt*$iva;
      $precio_producto=utf8_decode("$ ".number_format($precio_venta, 2));
    }


    $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio
      FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion
      WHERE presentacion_producto.id_presentacion='$id_presentacion' AND presentacion_producto.activo=1");
    $row2=_fetch_array($sql_p);
    $presentacion = utf8_decode(Mayu(utf8_decode($row2['nombre'])));
    $descripcionp = utf8_decode(Mayu(utf8_decode($row2['descripcion'])));

      $pdf->SetFont('bolt','',9);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(25,5,utf8_decode($cantidad_s),0,1,'C');
      $pdf->SetFont('bolt','',9);
      $pdf->SetXY($set_x+30, $set_y+$mm);
      $pdf->Cell(110,5,utf8_decode(Mayu(utf8_decode($descripcion_ps))),0,1,'L');
      $pdf->SetFont('bolt','',10);
      $pdf->SetXY($set_x+140, $set_y+$mm);
      $pdf->Cell(30,5,$precio_producto,0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,5,$subt_mostrar,0,1,'R');
      if($tipo_prod_serv == "PRODUCTO")
      {
        if($marca != "")
        {
          $pdf->SetFont('bolt','',9);
          $pdf->SetXY($set_x, $set_y+$mm+$rr);
          $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
          $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
          $pdf->SetFont('Latin','',9);
          $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("MARCA: ".$marca),0,1,'L');
          $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $rr += 5;
          $rrr  += 1;
        }
        if($modelo != "")
        {
          $pdf->SetFont('bolt','',9);
          $pdf->SetXY($set_x, $set_y+$mm+$rr);
          $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
          $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
          $pdf->SetFont('Latin','',9);
          $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("MODELO: ".$modelo),0,1,'L');
          $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $rr += 5;
          $rrr  += 1;
        }
        //////////////////////////////////////// Caracteristicas del producto ///////////////////////////////////////////
        $sql_detalles1 = _query("SELECT * FROM producto_detalle WHERE id_producto = '$id_producto'");
        $cuenta_deta1 = _num_rows($sql_detalles1);
        if($cuenta_deta1 > 0)
        {
          while ($row_de1 = _fetch_array($sql_detalles1))
          {
            $descripcion = $row_de1["descripcion"];
            $pdf->SetFont('bolt','',9);
            $pdf->SetXY($set_x, $set_y+$mm+$rr);
            $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
            $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
            $pdf->SetFont('Latin','',9);
            $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode(Mayu(utf8_decode($descripcion))),0,1,'L');
            $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $rr += 5;
            $rrr  += 1;
          }
        }
        ////////////////////////////////// Fin Caracteristicas ///////////////////////////////
      }
      if ($tipo_prod_serv == "SERVICIO")
      {
        $sql_detalles = _query("SELECT * FROM servicio_detalle WHERE id_servicio = '$id_producto'");
        $cuenta_deta = _num_rows($sql_detalles);
        if($cuenta_deta > 0)
        {
          while ($row_de = _fetch_array($sql_detalles))
          {
            $descripcion = $row_de["descripcion"];
            $pdf->SetFont('bolt','',9);
            $pdf->SetXY($set_x, $set_y+$mm+$rr);
            $pdf->Cell(25,5,utf8_decode(""),0,1,'C');
            $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode("»"),0,1,'L');
            $pdf->SetFont('Latin','',9);
            $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode(Mayu(utf8_decode($descripcion))),0,1,'L');
            $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $rr += 5;
            $rrr  += 1;
          }
        }
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
      $j += $rrr + 1;
      $suma_subtotal+= $subt;
      $suma_iva+=$iva_cu;

    }

    if ($j >= 38)
    {
      $page++;
      $pdf->AddPage();
      $set_x = 10;
      $set_y = 20;
      //Encabezado General
      $mm = 0;
      $i = 0;
      $j = 0;
    }
    $pdf->Line($set_x, $set_y+$mm, 205, $set_y+$mm);

    if($tipo_doc == "CCF")
    {
      $pdf->SetFont('bolt','',10);
      $iva_total = round($sum_subt * $iva, 3);
      $total_final = $sum_subt + $suma_iva;
      $pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("SUMA (SIN IVA): "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($sum_subt, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+5);
      $pdf->Cell(30,5,utf8_decode("IVA: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+5);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($suma_iva, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+10);
      $pdf->Cell(30,5,utf8_decode("SUB-TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+10);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $pdf->SetXY($set_x+135, $set_y+$mm+15);
      $pdf->Cell(30,5,utf8_decode("TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm+15);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
    }
    else
    {
      $pdf->SetFont('bolt','',10);
      $total_final = $sum_subt;
      $pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("TOTAL: "),0,1,'R');
      $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
    }




    if($prr > 0)
    {
      $pdf->SetFont('Latin','',10);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(115,5,utf8_decode("*Oferta valida por ".$vigencia." días o mientras duren existencias."),0,1,'L');
      $pdf->SetXY($set_x, $set_y+$mm+5);
      $pdf->Cell(115,5,utf8_decode("*Entrega de equipos ".$entrega." dias hábiles."),0,1,'L');
    }
    else
    {
      $pdf->SetFont('Latin','',10);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(115,5,utf8_decode("*Oferta valida por ".$vigencia." días."),0,1,'L');
      $mm = $mm - 5;
    }
    list($entero, $decimal)=explode('.', number_format($total_final,2));
    $enteros_txt=num2letras(str_replace(",","",$entero));
    $decimales_txt=num2letras($decimal);

    if ($entero>1) {
      $dolar=" dolares";
    } else {
      $dolar=" dolar";
    }
    $cadena_salida= "Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.";
    echo $cadena_salida;
    $pdf->SetXY($set_x, $set_y+$mm+15);
    $pdf->Cell(115,5,utf8_decode($cadena_salida),0,1,'L');

    if($mm >= 135)
    {
      $pdf->AddPage();
      $mm = 0;
      $set_y = 35;
    }
    else
    {
      $set_y = $mm + 135;
    }


    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    $pdf->Line($set_x+5, $set_y+5, 70, $set_y+5);
    $pdf->SetXY($set_x, $set_y+5);
    $pdf->Cell(65,5,utf8_decode(Mayu(utf8_decode($empleado))),0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);
    $pdf->Cell(65,5,utf8_decode(Mayu($cargo)),0,1,'C');
    $pdf->SetXY($set_x+80, $set_y);
    $pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    $pdf->Line($set_x+85, $set_y+5, 155, $set_y+5);
    $pdf->SetXY($set_x+80, $set_y+5);
    $pdf->Cell(65,5,utf8_decode("ACEPTADO CLIENTE"),0,1,'C');



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
$pdf->Output("Cotizacion_".$cliente.".pdf","I");
