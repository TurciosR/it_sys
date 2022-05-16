<?php
include('_core.php');
require('html_table.php');
require('num2letras.php');
//Obtener valores por $_GET

$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];
  $id_sucursal=$_SESSION['id_sucursal'];
class PDF extends PDF_HTML_Table
{
     //ancho por columna, 10 Columnas
  public $weights=array(10,20,25,70,70,20,20,20,25,25,25);

public function Header()
{
  $fecha_inicio=$_GET['fecha_inicio'];
  $fecha_fin= $_GET['fecha_fin'];
  $fi=ed($fecha_inicio);
  $ff=ed($fecha_fin);
  // Consultar la base de datos configuracion
   $sql_organizacion="SELECT id_empresa, nombre, razonsocial, direccion, telefono1,logo,
    telefono2, website, email, nit, iva FROM empresa ";

  $resultado_org=_query($sql_organizacion);
  $num_rows = _num_rows($resultado_org);
   while($row_org=_fetch_array($resultado_org)){
  				$empresa=utf8_decode($row_org['nombre']);
  				$telefonos=$row_org['telefono1'].'   '.$row_org['telefono2'];
          $logo="./".$row_org['logo'];
   }
	//Title
  $fechaprint=date('d-m-Y');
  $title0="REPORTE DE MOVIMIENTO DE PRODUCTOS ";
  $title1="DESDE ".$fi. " HASTA ".$ff;
  $title2="FECHA IMPRESION : ".$fechaprint;
	$this->SetFont('Arial','B',12);
	$this->Cell(0,6,$empresa,0,1,'C');
  $this->Cell(0,6,$title0,0,1,'C');
  $this->Cell(0,6,$title1,0,1,'C');
  $this->SetFont('Arial','B',8);
  $this->Cell(0,6,$title2,0,1,'C');

  $this->Image($logo,10,10,30,20);
	$this->Ln(6);

  $tableData=array(" No.\n","FECHA\n","NUM. COMPROB.","PROVEEDOR\n(NACIONALIDAD)"," PRODUCTO\n", "UNID. COMPR"," UNID. SALEN",
  "SALDO UNIDADES","  COSTO UNIT. ENTRAN","  COSTO UNIT. SALEN","SALDO MONET. EXIST.");

 $this->SetFillColor(192,192,192);
 $this->SetTextColor(0);
 $this->SetFont('Arial','B',8);

   $x=$this->GetX();
   $y=$this->GetY();
   $he=5; //altura
   $nb=2; //lineas
   $w=$this->weights;
    $len_array = count($w);
   for ($i=0;$i<$len_array;$i++) {
       $x=$this->GetX();
       $y=$this->GetY();
       $this->Rect($x, $y, $w[$i], $he*$nb);
      $datoss=$tableData[$i].' ';
      $this->MultiCell($w[$i], $he, $tableData[$i].' ', 0,'C',1);
   //Put the position to the right of the cell
   $this->SetXY($x+$w[$i], $y);
   }
   $this->Ln($he*$nb);
     $this->SetFont('Arial','',8);

  //Ensure table header is output
	parent::Header();
}

}
//sql stock producto
$sql_prod="SELECT producto.id_producto, stock.stock,stock.costo_promedio ,stock.ultimo_precio_compra
FROM producto,stock
WHERE producto.id_producto=stock.id_producto
AND stock.id_sucursal='$id_sucursal'
ORDER BY producto.id_producto
 ";
 $result_prod=_query($sql_prod);
 $nrows_prod=_num_rows($result_prod);
 $htmlTable="<TABLE>";
 $cor=0;
for($j=0;$j<=$nrows_prod;$j++){
  $row_prod=_fetch_array($result_prod);
  $id_producto=$row_prod['id_producto'];

$sql_stock="SELECT  producto.id_producto,producto.descripcion,producto.unidad, '' as fecha_movimiento,
SUM(movimiento_producto.entrada) AS entradas, SUM(movimiento_producto.salida) AS salidas,
movimiento_producto.numero_doc, movimiento_producto.precio_compra, movimiento_producto.precio_venta,
stock.stock,stock.costo_promedio,stock.ultimo_precio_compra
FROM producto, movimiento_producto,stock
WHERE fecha_movimiento<'$fecha_inicio'
AND producto.id_producto=movimiento_producto.id_producto
AND producto.id_producto='$id_producto'
AND stock.id_sucursal='$id_sucursal'
AND movimiento_producto.anulado=0
AND movimiento_producto.id_producto=stock.id_producto
GROUP BY producto.id_producto
UNION ALL
SELECT  producto.id_producto,producto.descripcion,producto.unidad, movimiento_producto.fecha_movimiento,
movimiento_producto.entrada AS entradas, movimiento_producto.salida AS salidas,
movimiento_producto.numero_doc, movimiento_producto.precio_compra, movimiento_producto.precio_venta,
stock.stock,stock.costo_promedio,stock.ultimo_precio_compra
FROM producto, movimiento_producto,stock
WHERE  fecha_movimiento BETWEEN '$fecha_inicio' AND '$fecha_fin'
AND producto.id_producto=movimiento_producto.id_producto
AND producto.id_producto='$id_producto'
AND stock.id_sucursal='$id_sucursal'
AND movimiento_producto.anulado=0
AND movimiento_producto.id_producto=stock.id_producto
";
//GROUP BY  producto.id_producto,movimiento_producto.fecha_movimiento,movimiento_producto.numero_doc
$result=_query($sql_stock);
$nrows=_num_rows($result);

//Create Table
$saldo=0;
$diferencia=0;
$saldo_dinero_exist=0;

for($i=0;$i<=$nrows;$i++){

  $row=_fetch_array($result);
  $fecha=$row['fecha_movimiento'];
  $fechaprint=ed($fecha);
  $descripcion=utf8_decode($row['descripcion']);
  $numero_doc=$row['numero_doc'];
  $entradas=$row['entradas'];
  $salidas=$row['salidas'];
  $unidad=$row['unidad'];
  $unidades=" Unidades";
  $diferencia=  $entradas-$salidas;
  $saldo=$saldo+$diferencia;
  $precio_compra=$row['ultimo_precio_compra'];
  if($unidad==0)
    $unidad=1;
  if ($entradas>0){
    $costo_existencias=$entradas/$unidad*$precio_compra;
  }
  else {
    $costo_existencias=$salidas/$unidad*$precio_compra;
  }

  $stock=$row['stock'];

  if($descripcion!=""  && ($entradas>0 || $salidas>0)){
    $cor++;
      $saldo_dinero_exist=$saldo/$unidad*$precio_compra;
//proveedores y nacionalidades
  $sql_proveedor="SELECT movimiento_producto.id_producto,proveedor.nombre_proveedor,
  paises.nombre AS nombrepais
   FROM proveedor,paises,movimiento_producto
   WHERE movimiento_producto.id_producto='$id_producto'
   AND movimiento_producto.id_proveedor=proveedor.id_proveedor
   AND proveedor.id_pais=paises.id";
   $result2=_query($sql_proveedor);
   $count2=_num_rows($result2);
   $row2=_fetch_array($result2);
   if($count2>0){
   $nombre_proveedor=$row2['nombre_proveedor'];
   $pais_proveedor=$row2['nombrepais'];

  if($pais_proveedor="El Salvador"){
    $pais_nac=  $nombre_proveedor." (Salvadoreña)";
  }
  else{
    $pais_nac=  $nombre_proveedor." (".$pais_proveedor.")";
  }

  $pais_nac=utf8_decode($pais_nac);
}
else{
  $pais_nac=" ";
}
//fin proveedores y nacionalidades
  $htmlTable.=	'<TR><TD>'.$cor.'</TD>';
  $htmlTable.=	'<TD>'.$fechaprint.'</TD>';
  $htmlTable.=	'<TD>'.$numero_doc.'</TD>';
  $htmlTable.=	'<TD>'.$pais_nac.'</TD>';
  $htmlTable.=	'<TD>'.$descripcion.'</TD>';

  $htmlTable.=	'<TD>'.$entradas.'</TD>';
  $htmlTable.=	'<TD>'.$salidas.'</TD>';
  //
  $htmlTable.=	'<TD>'.$saldo.$unidades .'</TD>';
  $htmlTable.=	'<TD>'.$precio_compra.'</TD>';
  $htmlTable.=	'<TD>'.$precio_compra.'</TD>';
  $htmlTable.=	'<TD>'.  $saldo_dinero_exist.'</TD>';
  $htmlTable.=	'</TR>';
}
}
}//for
$htmlTable.='</TABLE>';
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','Legal');
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("$htmlTable");
$pdf->Output();
?>
