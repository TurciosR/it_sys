<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

$array_json=$_POST['params'];

$pdf=new fPDF('L','mm', 'Letter');
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
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$tel1 = $row_emp['telefono'];
$telefonos="TEL. ".$tel1;

    $id_proveedor = $_REQUEST["id_proveedor"];
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE EXISTENCIAS POR TALLA";

    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);
    $pdf->MultiCell(280,6,$title,0,'C',0);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+6);
    $pdf->Cell(280,6,$nombre_a.": ".$direccion,0,1,'C');
    $pdf->SetXY($set_x, $set_y+11);
    $pdf->Cell(280,6,$telefonos,0,1,'C');
    $pdf->SetXY($set_x, $set_y+16);
    $pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
    $pdf->SetXY($set_x, $set_y+21);
    $pdf->Cell(280,6,$existenas,0,1,'C');


    $set_y = 30;
    $set_x = 2;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $sql_escala = _query("SELECT * FROM escala");
    $num_es = _num_rows($sql_escala);
    $ancho_es = 5 * $num_es;
    $ancho_es +=5;
    $pdf->SetFont('Latin','',7);
    $pdf->SetXY($set_x, $set_y+$ancho_es-5);
    $pdf->Cell(8,5,utf8_decode("N??"),1,1,'C',0);
    $pdf->SetXY($set_x+8, $set_y+$ancho_es-5);
    $pdf->Cell(50,5,utf8_decode("DESCRIPCI??N"),1,1,'C',0);
    $pdf->SetXY($set_x+58, $set_y+$ancho_es-5);
    $pdf->Cell(12,5,utf8_decode("ESTILO"),1,1,'C',0);
    $pdf->SetXY($set_x+70, $set_y+$ancho_es-5);
    $pdf->Cell(15,5,utf8_decode("COLOR"),1,1,'C',0);

    $mmes = 0;
    while($row_es = _fetch_array($sql_escala))
    {
        $nombre = $row_es["nombre"];
        $pdf->SetXY($set_x+85, $set_y+$mmes);
        $pdf->Cell(10,5,$nombre,1,1,'C',0);
        $valores = explode(",",$row_es["valores"]);
        $nvals = count($valores);
        $ancho_val = round((180 / $nvals),1);
        $mmva = 0;
        for($k = 0; $k<$nvals; $k++)
        {
            $pdf->SetXY($set_x+95+$mmva, $set_y+$mmes);
            $pdf->Cell($ancho_val,5,$valores[$k],1,1,'C',0);
            if($mmes == 0)
            {
                $sql_suc = _query("SELECT * FROM sucursal");
                $numsuc = _num_rows($sql_suc);
                $anchosuc = round(($ancho_val/$numsuc),1);
                $mmsuc = 0;
                $s = 1;
                if($mmva == 0)
                {
                    $pdf->SetXY($set_x+85, $set_y+$ancho_es-5);
                    $pdf->Cell(10,5,"SUC",1,1,'C',0);
                }
                while ($dsuc = _fetch_array($sql_suc))
                {
                    $pdf->SetXY($set_x+95+$mmva+$mmsuc, $set_y+$ancho_es-5);
                    $pdf->Cell($anchosuc,5,"S".$s,1,1,'C',0);
                    $s++;
                    $mmsuc += $anchosuc;
                }
            }
            $mmva+= $ancho_val;
        }
        $mmes += 5;

    }
    //$pdf->SetTextColor(0,0,0);
    $set_y = 30+$ancho_es;
    $page = 0;
    $j=0;
    $i = 1;
    $mm = 0;

    $salto = floor((205 - (30 + $ancho_es))/5);
    $salto2 = $salto + $num_es;

    $array = json_decode($array_json, true);
    foreach ($array as $fila) {
      $sql="";
      $and = "SELECT stock.id_sucursal,productos.descripcion, productos.id_color,stock.existencias,productos.id_producto, productos.escala, productos.talla, productos.estilo, colores.nombre FROM productos, stock, colores WHERE productos.id_color=colores.id_color AND productos.estilo='$fila[estilo]' AND productos.id_color='$fila[id_color]' AND productos.id_producto=stock.id_producto AND stock.existencias>0 AND productos.talla IS NOT NULL GROUP BY productos.descripcion,productos.id_color,productos.escala ORDER BY productos.descripcion ASC";
      $sql.=$and;
      $result = _query($sql);

      if(_num_rows($result)>0)
      {
          while($rowasw = _fetch_array($result))
          {
              if($page==0)
                  $salto = $salto;
              else
                  $salto = $salto2;
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
                  $pdf->Cell(280,6,$title,0,1,'C');
                  $pdf->SetFont('Latin','',10);
                  $pdf->SetXY($set_x, $set_y+6);
                  $pdf->Cell(280,6,$nombre_a." ".$direccion,0,1,'C');
                  $pdf->SetXY($set_x, $set_y+11);
                  $pdf->Cell(280,6,$telefonos,0,1,'C');
                  $pdf->SetXY($set_x, $set_y+16);
                  $pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
                  $pdf->SetXY($set_x, $set_y+21);
                  $pdf->Cell(280,6,$existenas,0,1,'C');
                  $pdf->SetXY($set_x, $set_y+26);
                  $pdf->Cell(280,6,$fech,0,1,'C');
                  $set_x = 2;
                  $set_y = 30;
                  $j=0;
                  $pdf->SetFont('Latin','',7);
              }
              $descripciona = $rowasw["descripcion"];
              $estilo = $rowasw["estilo"];
              $coloraa = $rowasw["nombre"];
              $escala = $rowasw["escala"];
              $id_color = $rowasw["id_color"];

              $descripcion = utf8_decode($descripciona);

              $sql_escala = _query("SELECT * FROM escala WHERE id_escala='$escala'");
              $num_es = _num_rows($sql_escala);
              $ancho_es = 5 * $num_es;
              $ancho_es +=5;
              $mmes = 0;
              $hay = 0;
              //aa
              $row_es = _fetch_array($sql_escala);
              $valores = explode(",",$row_es["valores"]);
              $nvals = count($valores);
              $escaa = $row_es["nombre"];
              $ancho_val = round((180 / $nvals),1);
              $mmva = 0;
              for($k = 0; $k<$nvals; $k++)
              {

                      $sql_suc = _query("SELECT * FROM sucursal");
                      $numsuc = _num_rows($sql_suc);
                      $anchosuc = round(($ancho_val/$numsuc),1);
                      $mmsuc = 0;
                      while($dsuc = _fetch_array($sql_suc))
                      {
                          $id_sucursal = $dsuc["id_sucursal"];

                          $sql_aux = _query("SELECT stock.existencias, stock.id_sucursal, productos.talla FROM productos, stock WHERE productos.descripcion='$descripciona' AND productos.id_producto=stock.id_producto AND productos.id_color=$id_color  AND productos.talla='".$valores[$k]."' AND stock.id_sucursal=$id_sucursal");
                          $row = _fetch_array($sql_aux);

                            $talla= $row["talla"];
                            $existencias= $row["existencias"];
                            $id_sucursal11= $row["id_sucursal"];

                              if($existencias > 0)
                              {
                                  $hay = 1;
                                  $pdf->SetXY($set_x+95+$mmva+$mmsuc, $set_y+$mm);
                                  $pdf->Cell($anchosuc,5,$existencias,0,0,'C',0);
                              }

                          $mmsuc += $anchosuc;
                      }
                  $mmva+= $ancho_val;
              }
              if($hay)
              {
                  $pdf->SetXY($set_x+85, $set_y+$mm);
                  $pdf->Cell(10,5,$escaa,1,1,'C',0);

                  $pdf->SetXY($set_x, $set_y+$mm);
                  $pdf->Cell(8,5,$i,1,1,'C',0);
                  $pdf->SetXY($set_x+8, $set_y+$mm);
                  $pdf->Cell(50,5,$descripcion,1,1,'L',0);
                  $pdf->SetXY($set_x+58, $set_y+$mm);
                  $pdf->Cell(12,5,$estilo,1,1,'L',0);
                  $pdf->SetXY($set_x+70, $set_y+$mm);
                  $pdf->Cell(15,5,$coloraa,1,1,'L',0);
                  $mmva = 0;
                  for($k = 0; $k<$nvals; $k++)
                  {
                      $sql_suc = _query("SELECT * FROM sucursal");
                      $numsuc = _num_rows($sql_suc);
                      $anchosuc = round(($ancho_val/$numsuc),1);
                      $mmsuc = 0;
                      while($dsuc = _fetch_array($sql_suc))
                      {
                          $pdf->SetXY($set_x+95+$mmva+$mmsuc, $set_y+$mm);
                          $pdf->Cell($anchosuc,5,"",1,1,'C',0);
                          $mmsuc+=$anchosuc;
                      }
                      $mmva+= $ancho_val;
                  }
                  $mm += 5;
                  $i++;
                  $j++;
                  if($j==1)
                  {
                      //Fecha de impresion y numero de pagina
                      $pdf->SetXY(4, 210);
                      $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
                      $pdf->SetXY(260, 210);
                      $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
                  }
              }
          }
      }
    }
ob_clean();
$pdf->Output("reporte_stock.pdf","I");
