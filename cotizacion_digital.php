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
$id_cotizacion=$_REQUEST['id_cotizacion'];
$sql="SELECT co.fecha, co.id_empleado, co.atencion, co.total, co.numero_doc, co.vigencia, co.entrega, co.pago, co.hora,co.tipo, c.nombre as cliente, c.telefono1, c.telefono2, c.direccion, e.nombre as vendedor, ee.nombre as empleado,
c.retiene, c.retiene10, co.tipo_doc, c.nrc, ee.cargo, co.pago, co.dias_credito, ee.telefono1 as telefono_em
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
$id_empleado=$row['id_empleado'];
$vigencia=$row['vigencia'];
$atencion = $row["atencion"];
$entrega = $row["entrega"];
$tel_cliente = $row["telefono1"];
$tipo_doc = $row["tipo_doc"];
$cargo = $row["cargo"];
$nrc = $row["nrc"];
$pago = $row["pago"];
$dias_credito = $row["dias_credito"];
$telefono_em = $row["telefono_em"];

if($pago == "CON")
{
  $cadena_pago = "Contado.";
}
if($pago == "CHE")
{
  $cadena_pago = "Cheque.";
}
if($pago == "CRE")
{
  $cadena_pago = "Crédito ".$dias_credito." días.";
}

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
        // Posición: a 1,5 cm del final
        $this->SetY(-20);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        $this->SetFont('bolt','',9);
        $this->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
        $this->SetFillColor(30,111,158);
        $this->SetXY(4, 260);
        $this->Cell(104,10,"",0,1,'L',1);
        $this->SetXY(4, 260);
        $this->Cell(104,5,utf8_decode("Teléfonos: ".$this->c),0,1,'L',1);
        $this->SetXY(4, 265);
        $this->Cell(104,5,utf8_decode("https://opensolutionsystems.com"),0,1,'L',1);
        $this->SetFont('bolt','',9);
        $this->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
        //$pdf->SetFillColor(215,145,15);
        $this->SetFillColor(177,177,177);
        $this->SetXY(108, 260);
        $this->Cell(104,10,"",0,1,'L',1);
        $this->SetXY(108, 260);
        $this->Cell(104,5,utf8_decode($this->a),0,1,'L',1);
        $this->SetFont('bolt','',8.7);
        $this->SetXY(108, 265);
        $this->Cell(104,5,utf8_decode($this->b),0,1,'L',1);
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
$pdf->SetTopMargin(20);
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
$pdf->Cell(195,5,utf8_decode($fecha_detalle),0,1,'L');
$sy+=10;

$pdf->SetFont('bolt','',10);
$pdf->SetXY($set_x, $set_y+$sy);
$pdf->Cell(195,5,utf8_decode("SRS. ".$cliente),0,1,'L');
$sy += 5;
$pdf->SetXY($set_x, $set_y+$sy);
$pdf->Cell(195,5,utf8_decode("PRESENTE:"),0,1,'L');
$pdf->SetFont('Latin','',10);
/*$pdf->SetXY($set_x+26, $set_y+15);
$pdf->Cell(195,5,utf8_decode(": ".$cliente),0,1,'L');
*/
$sy += 5;
//if($direccion_cliente != "")
if(false)
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("DIRECCIÓN"),0,1,'L');
  $pdf->SetFont('Latin','',9);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".Mayu($direccion_cliente)),0,1,'L');
  $sy += 5;
}
//if($tel_cliente != "0000-0000")
if(false)
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("TELÉFONO"),0,1,'L');
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".$tel_cliente),0,1,'L');
  $sy += 5;
}
//if($tipo_doc == "CCF")
if(false)
{
  $pdf->SetFont('bolt','',10);
  $pdf->SetXY($set_x, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode("NRC"),0,1,'L');
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x+26, $set_y+$sy);
  $pdf->Cell(195,5,utf8_decode(": ".$nrc),0,1,'L');
  $sy += 5;
}
$saludo ="Reciban un cordial saludo, deseándoles éxitos en sus labores. Seguro de poder brindarle un servicio de alta calidad, quedamos a sus órdenes para cualquier información adicional, y en espera de una pronta y favorable respuesta a la presente oferta.";

$pdf->SetXY($set_x, $set_y+$sy);
$pdf->MultiCell(195,5,utf8_decode($saludo),0,'J',0);
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
    $salto = 34;
    else
    $salto = 34;

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
      $sql_ps = _query("SELECT pr.descripcion, pr.marca, pr.modelo, pr.serie, pr.tiempo_garantia, pr.tipo_tiempo, pr.tiene_garantia
      FROM productos as pr
      WHERE pr.id_producto= '$id_producto'");
      $row_ps = _fetch_array($sql_ps);
      $marca = $row_ps["marca"];
      $modelo = $row_ps["modelo"];
      $serie = $row_ps["serie"];
      $descripcion_ps =$row_ps["descripcion"];
      $tiene_garantia = $row_ps["tiene_garantia"];
      $dias_garantia = $row_ps["tiempo_garantia"];
      $tipo_periodo = $row_ps["tipo_tiempo"];
      if($tipo_periodo == "0")
      {
        if($dias_garantia <= 1)
        {
          $tex_perido = "DÍA";
        }
        else
        {
          $tex_perido = "DÍAS";
        }
      }
      if($tipo_periodo == "1")
      {
        if($dias_garantia <= 1)
        {
          $tex_perido = "MES";
        }
        else
        {
          $tex_perido = "MESES";
        }
      }
      if($tipo_periodo == "2")
      {
        if($dias_garantia <= 1)
        {
          $tex_perido = "AÑO";
        }
        else
        {
          $tex_perido = "AÑOS";
        }
      }
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
      // $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(25,5,utf8_decode($cantidad_s),0,0,'C');
      $pdf->SetFont('bolt','',9);
      // $pdf->SetXY($set_x+30, $set_y+$mm);
      $set_x1 = $pdf->GetX();
      $set_y1 = $pdf->GetY();
      $pdf->SetXY($set_x1+115, $set_y1);
      $pdf->Cell(30,5,$precio_producto,0,0,'R');
      // $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(25,5,$subt_mostrar,0,0,'R');
      $set_x1 = $pdf->GetX();
      $set_y1 = $pdf->GetY();
      $pdf->SetXY($set_x1-170, $set_y1);
      $pdf->MultiCell(110,5,utf8_decode(Mayu(utf8_decode($descripcion_ps))),0,'L',0);
      $pdf->SetFont('bolt','',9);



      if($tipo_prod_serv == "PRODUCTO")
      {
        if($marca != "")
        {
          $pdf->SetFont('bolt','',9);
          // $pdf->SetXY($set_x, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
          // $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
          $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
          $pdf->SetFont('Latin','',9);
          // $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("MARCA: ".$marca),0,0,'L');
          // $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
          // $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $rr += 5;
          $rrr  += 1;
        }
        if($modelo != "")
        {

          $pdf->SetFont('bolt','',9);
          // $pdf->SetXY($set_x, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
          // $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
          $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
          $pdf->SetFont('Latin','',9);
          // $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("MODELO: ".$modelo),0,0,'L');
          // $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
          // $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $rr += 5;
          $rrr  += 1;
        }
        /////////////////////////////// Caracteristicas///////////////////////////////////////////
        $sql_detalles1 = _query("SELECT * FROM producto_detalle WHERE id_producto = '$id_producto'");
        $cuenta_deta1 = _num_rows($sql_detalles1);
        if($cuenta_deta1 > 0)
        {
          while ($row_de1 = _fetch_array($sql_detalles1))
          {
            $descripcion = $row_de1["descripcion"];
            $pdf->SetFont('bolt','',9);
            // $pdf->SetXY($set_x, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
            // $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
            $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
            $pdf->SetFont('Latin','',9);
            // $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode(Mayu(utf8_decode($descripcion))),0,0,'L');
            // $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
            // $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $rr += 5;
            $rrr  += 1;
          }
        }
        if($tiene_garantia == 1)
        {
          $pdf->SetFont('bolt','',9);
          //$pdf->SetXY($set_x, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
          //$pdf->SetXY($set_x+35, $set_y+$mm+$rr);
          $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
          $pdf->SetFont('Latin','',9);
          //$pdf->SetXY($set_x+40, $set_y+$mm+$rr);
          $pdf->Cell(110,5,utf8_decode("TIEMPO DE GARANTIA: ".$dias_garantia." ".$tex_perido),0,0,'L');
          //$pdf->SetXY($set_x+135, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
          //$pdf->SetXY($set_x+165, $set_y+$mm+$rr);
          $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
          $rr += 5;
          $rrr  += 1;
        }
        /////////////////////////////////////////// Fin caracteristicas/////////////////////////
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
            // $pdf->SetXY($set_x, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'C');
            // $pdf->SetXY($set_x+35, $set_y+$mm+$rr);
            $pdf->Cell(5,5,utf8_decode("»"),0,0,'L');
            $pdf->SetFont('Latin','',9);
            // $pdf->SetXY($set_x+40, $set_y+$mm+$rr);
            $pdf->Cell(110,5,utf8_decode(Mayu(utf8_decode($descripcion))),0,0,'L');
            // $pdf->SetXY($set_x+135, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,0,'R');
            // $pdf->SetXY($set_x+165, $set_y+$mm+$rr);
            $pdf->Cell(30,5,utf8_decode(""),0,1,'R');
            $rr += 5;
            $rrr  += 1;
          }
        }
      }


      $sum_subt += $subt;
      $mm += $rr;
      $i++;
      $j += $rrr + 1;
      $suma_subtotal+= $subt;
      $suma_iva+=$iva_cu;
    }

    // if ($j >= 38)
    // {
    //   $pdf->SetFont('bolt','',9);
    //   $pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
    //   $pdf->SetFillColor(30,111,158);
    //   $pdf->SetXY(4, 260);
    //   $pdf->Cell(104,10,"",0,1,'L',1);
    //   $pdf->SetXY(4, 260);
    //   $pdf->Cell(104,5,utf8_decode("Teléfonos: ".$telefono),0,1,'L',1);
    //   $pdf->SetXY(4, 265);
    //   $pdf->Cell(104,5,utf8_decode("www.opensolutionsystems.com"),0,1,'L',1);
    //   $pdf->SetFont('bolt','',9);
    //   $pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
    //   //$pdf->SetFillColor(215,145,15);
    //   $pdf->SetFillColor(177,177,177);
    //   $pdf->SetXY(108, 260);
    //   $pdf->Cell(104,10,"",0,1,'L',1);
    //   $pdf->SetXY(108, 260);
    //   $pdf->Cell(104,5,utf8_decode($d1),0,1,'L',1);
    //   $pdf->SetFont('bolt','',8.7);
    //   $pdf->SetXY(108, 265);
    //   $pdf->Cell(104,5,utf8_decode($d2),0,1,'L',1);
    //   $pdf->SetTextColor(0,0,0);
    //   $page++;
    //   $pdf->AddPage();
    //   $set_x = 10;
    //   $set_y = 20;
    //   //Encabezado General
    //   $mm = 0;
    //   $i = 0;
    //   $j = 0;
    // }
    $set_x1 = $pdf->GetX();
    $set_y1 = $pdf->GetY();
    $pdf->Line($set_x1, $set_y1, 205, $set_y1);
    $mm+=5;

    if($tipo_doc == "CCF")
    {
      $pdf->SetFont('bolt','',10);
      $iva_total = round($sum_subt * $iva, 3);
      $total_final = $sum_subt + $suma_iva;
      // $pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->SetFont('Latin','',9);
      list($entero, $decimal)=explode('.', number_format($total_final,2));
      $enteros_txt=num2letras(str_replace(",","",$entero));
      $decimales_txt=num2letras($decimal);

      if ($entero>1) {
        $dolar=" dolares";
      } else {
        $dolar=" dolar";
      }
      $cadena_salida= Mayu("Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.");
      //echo $cadena_salida;
      //$pdf->SetXY($set_x, $set_y+$mm+15);
      $pdf->Cell(135,5,utf8_decode($cadena_salida),0,0,'L');
      $pdf->SetFont('bolt','',10);
      $pdf->Cell(30,5,utf8_decode("SUMA (SIN IVA): "),0,0,'R');
      // $pdf->SetXY($set_x+165, $set_y+$mm);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($sum_subt, 2)),0,1,'R');

      $pdf->Cell(135,5,utf8_decode(""),0,0,'L');
      $pdf->SetFont('bolt','',10);
      $pdf->Cell(30,5,utf8_decode("IVA: "),0,0,'R');
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($suma_iva, 2)),0,1,'R');
      $pdf->SetFont('bolt','',10);
      $pdf->Cell(135,5,utf8_decode(""),0,0,'L');
      $pdf->Cell(30,5,utf8_decode("TOTAL: "),0,0,'R');
      // $pdf->SetXY($set_x+165, $set_y+$mm+10);
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $pdf->Cell(135,5,utf8_decode("CONDICIONES DE LA OFERTA"),0,1,'L');

      // $pdf->SetXY($set_x+165, $set_y+$mm+5);

      if($prr > 0)
      {
        $pdf->SetFont('bolt','',9);
        //$pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(35,5,utf8_decode("*Validez de la oferta:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(135,5,utf8_decode($vigencia." días o mientras duren existencias."),0,1,'L');
      }
      else
      {
        $pdf->SetFont('bolt','',9);
        //$pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(35,5,utf8_decode("*Validez de la oferta:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(135,5,utf8_decode($vigencia." días."),0,1,'L');
        $mm = $mm - 5;
      }
      // $pdf->SetXY($set_x+135, $set_y+$mm+5);
      if($prr > 0 || $entrega > 0)
      {
        $pdf->SetFont('bolt','',9);
        $pdf->Cell(35,5,utf8_decode("*Tiempo de entrega:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(135,5,utf8_decode($entrega." días hábiles."),0,1,'L');
      }


      $pdf->SetFont('bolt','',9);
      $pdf->Cell(35,5,utf8_decode("*Forma de pago:"),0,0,'L');
      $pdf->SetFont('Latin','',10);
      $pdf->Cell(115,5,utf8_decode($cadena_pago),0,1,'L');
    }
    else
    {
      $pdf->SetFont('bolt','',10);
      $total_final = $sum_subt;
      //$pdf->SetXY($set_x+135, $set_y+$mm);
      $pdf->SetFont('Latin','',10);
      list($entero, $decimal)=explode('.', number_format($total_final,2));
      $enteros_txt=num2letras(str_replace(",","",$entero));
      $decimales_txt=num2letras($decimal);

      if ($entero>1) {
        $dolar=" dolares";
      } else {
        $dolar=" dolar";
      }
      $cadena_salida= Mayu("Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.");
      //echo $cadena_salida;
      //$pdf->SetXY($set_x, $set_y+$mm+15);
      $pdf->Cell(135,5,utf8_decode($cadena_salida),0,0,'L');
      $pdf->SetFont('bolt','',10);
      $pdf->Cell(30,5,utf8_decode("TOTAL: "),0,0,'R');
      $pdf->Cell(30,5,utf8_decode("$ ".number_format($total_final, 2)),0,1,'R');
      $pdf->Cell(135,5,utf8_decode(""),0,1,'L');
      $pdf->SetFont('bolt','',10);
      $pdf->Cell(135,5,utf8_decode("CONDICIONES DE LA OFERTA"),0,1,'L');
      if($prr > 0)
      {
        $pdf->SetFont('bolt','',9);
        //$pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(35,5,utf8_decode("*Validez de la oferta:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(135,5,utf8_decode($vigencia." días o mientras duren existencias."),0,1,'L');
      }
      else
      {
        $pdf->SetFont('bolt','',9);
        //$pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(35,5,utf8_decode("*Validez de la oferta:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(135,5,utf8_decode($vigencia." días."),0,1,'L');
        $mm = $mm - 5;
      }

      if($prr > 0 || $entrega > 0)
      {
        $pdf->SetFont('bolt','',9);
        $pdf->Cell(35,5,utf8_decode("*Tiempo de entrega:"),0,0,'L');
        $pdf->SetFont('Latin','',10);
        $pdf->Cell(115,5,utf8_decode($entrega." dias hábiles."),0,1,'L');
      }
      $pdf->SetFont('bolt','',9);
      $pdf->Cell(35,5,utf8_decode("*Forma de pago:"),0,0,'L');
      $pdf->SetFont('Latin','',10);
      $pdf->Cell(115,5,utf8_decode($cadena_pago),0,1,'L');
    }

    $pdf->SetMargins(10,5);
    $pdf->SetTopMargin(20);
    $pdf->SetLeftMargin(10);
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(true,20);
    $pdf->AddFont("latin","","latin.php");
    $pdf->AddFont("bolt","","latin_bolt.php");
    $pdf->AddPage();
    $pdf->Cell(135,5,utf8_decode(""),0,1,'L');
    $pdf->SetFont('Latin','',10);
    $pdf->Cell(195,5,utf8_decode("Agradeciendo la atención al presente, y a la espera de poder servirle con nuestros productos y servicios."),0,1,'L');
    $pdf->Cell(135,5,utf8_decode(""),0,1,'L');
    $pdf->SetFont('Latin','',10);
    $set_x = $pdf->GetX();
    $set_y = $pdf->GetY()+20;
    // if($set_y >= 220)
    // {
    //   $pdf->SetMargins(10,5);
    //   $pdf->SetTopMargin(20);
    //   $pdf->SetLeftMargin(10);
    //   $pdf->AliasNbPages();
    //   $pdf->SetAutoPageBreak(true,20);
    //   $pdf->AddFont("latin","","latin.php");
    //   $pdf->AddFont("bolt","","latin_bolt.php");
    //   $pdf->AddPage();
    //   $set_x = $pdf->GetX();
    //   $set_y = $pdf->GetY()+10;
    // }
    $pdf->Cell(195,5,utf8_decode("Atentamente."),0,1,'L');

    if($id_empleado==33)
    {
      $pdf->Image("img/neson.png",20,$set_y,50,18);
    }
    else
    {
      $pdf->Image("img/firma_ing.jpeg",20,$set_y,50,18);
    }
    $pdf->Image("img/sello.jpeg",95,$set_y,50,18);


    $set_x = $pdf->GetX();
    $set_y = $pdf->GetY()+30;

    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(115,5,utf8_decode("F:"),0,1,'L');
    $pdf->Line($set_x+5, $set_y+5, 80, $set_y+5);
    //
    $pdf->Cell(75,5,utf8_decode(Mayu(utf8_decode($empleado))),0,1,'C');
    // $pdf->SetXY($set_x, $set_y+10);
    $pdf->Cell(75,5,utf8_decode(Mayu($cargo)),0,1,'C');
    // $pdf->SetXY($set_x, $set_y+15);
    $pdf->Cell(75,5,utf8_decode("TEL: ".$telefono_em),0,1,'C');
  }


$cliente = str_replace(" ", "_", quitar_tildes($cliente));
ob_clean();
$pdf->Output("Cotizacion_".$cliente."_".date('d-m-Y').".pdf","I");
