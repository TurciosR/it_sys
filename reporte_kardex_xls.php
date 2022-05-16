<?php
/**
 * PHPExcel
 *Convertir reporte kardex a excel
*/

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel-1.8/Classes/PHPExcel.php';
include('_core.php');
require('num2letras.php');
//Obtener valores por $_GET

$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];

$fi=ed($fecha_inicio);
$ff=ed($fecha_fin);

$fecha_hoy=date("d-m-Y");
$id_sucursal=$_SESSION['id_sucursal'];
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Luis J A")
							 ->setLastModifiedBy("Luis J A")
							 ->setTitle("Office 2007 XLSX")
							 ->setSubject("Office 2007 XLSX")
							 ->setDescription("Documento compatible con Office 2007 XLSX")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Reportes");

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
//Titulos
$title0="REPORTE DE MOVIMIENTO DE PRODUCTOS ";
$title1="DESDE ".$fi. " HASTA ".$ff;
$title2="FECHA GENERACION : ".$fecha_hoy;
//style border
$BStyle = array(
  'borders' => array(
    'outline' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    ),
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
  )
);
//$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->applyFromArray($BStyle);
$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
$objPHPExcel->getActiveSheet()->mergeCells('A4:J4');
//Center table
$stylefonts1 = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
				'font'  => array(
			 'bold'  => true,
			 'color' => array('rgb' => '0000FF'),
			 'size'  => 12,
			 'name'  => 'Arial'
	 )
    );
		$stylefonts2 = array(
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        ),
						'font'  => array(
					 'bold'  => true,
					 'color' => array('rgb' => '000000'),
					 'size'  => 10,
					 'name'  => 'Arial'
			 )
		  );
//$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);
for($j=2;$j<5;$j++){
$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
}
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(70);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

$objPHPExcel->getActiveSheet()->getStyle("A1:J4")->applyFromArray($stylefonts1);
$objPHPExcel->getActiveSheet()->getStyle("A7:J7")->applyFromArray($stylefonts2);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $empresa." ".$telefonos);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $title0." ".$title1);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $title2);
//Header table
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', "No.");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', "Fecha Mov.");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', "Documento");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', "Proveedor/Nacionalidad");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', "Producto");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', "Entradas");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', "Salidas");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', "Saldo");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', "Precio Compra");
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', "Valor Existencias $");
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Reporte Movimiento de Productos');

//sql stock producto
$sql_prod="SELECT producto.id_producto, stock.stock,stock.costo_promedio ,stock.ultimo_precio_compra
FROM producto,stock
WHERE producto.id_producto=stock.id_producto
AND stock.id_sucursal='$id_sucursal'
ORDER BY producto.id_producto
 ";

 $result_prod=_query($sql_prod);
 $nrows_prod=_num_rows($result_prod);
 $cor=1;
$sp=8;
for($j=0;$j<=$nrows_prod;$j++){
  $row_prod=_fetch_array($result_prod);
  $id_producto=$row_prod['id_producto'];
//sql stock antiguo
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
  $descripcion=$row['descripcion'];
  $numero_doc=$row['numero_doc'];
  $entradas=$row['entradas'];
  $salidas=$row['salidas'];
	$unidad=$row['unidad'];
  $diferencia=  $entradas-$salidas;
  $saldo=$saldo+$diferencia;
  $precio_compra=$row['ultimo_precio_compra'];
  if ($entradas>0){
    $costo_existencias=$entradas*$precio_compra;
  }
  else {
    $costo_existencias=$salidas*$precio_compra;
  }

  $stock=$row['stock'];


  if($descripcion!=""  && ($entradas>0 || $salidas>0)){

      $saldo_dinero_exist=$saldo*$precio_compra;
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
}
else{
  $pais_nac="";
}
$unidades=" Unidades";
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$sp, $cor);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$sp, $fechaprint);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$sp, $numero_doc);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$sp, $pais_nac);

 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$sp, $descripcion);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$sp, $entradas);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$sp, $salidas);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$sp, $saldo.$unidades);
 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$sp, $precio_compra);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$sp, $saldo_dinero_exist);
//$objPHPExcel->getActiveSheet()->getStyle('A1:B2')->applyFromArray($styleArray);
 $cor++;
  $sp=$sp+1;
}
}
}//for //end query to export
//style all sheet
$objPHPExcel->getActiveSheet()->getStyle('A7:J'.$sp)->applyFromArray($BStyle);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
$archivo_salida="reporte_mov_producto_".$fecha_hoy.".xls";
// Redirect output to a client’s web browser (Excel7)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$archivo_salida.'"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 07:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
