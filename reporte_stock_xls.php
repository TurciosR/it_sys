<?php
/**
 * PHPExcel
 *Convertir reporte kardex a excel
*/
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel-1.8/Classes/PHPExcel.php';
include('_core.php');
require('num2letras.php');

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

$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$tel1 = $row_emp['telefono'];
$telefonos="TEL. ".$tel1;

    $min = $_REQUEST["min"];
    $max = $_REQUEST["max"];
    $logo = "img/logoopenpyme.jpg";
    $impress = "Impreso: ".date("d/m/Y");
    $title = "CALZADO MAYORGA";
    $titulo = "REPORTE DE EXISTENCIAS";
    $fech = "AL ".date("d")." DE ".utf8_decode(Mayu(utf8_decode(meses(date("m")))))." DEL ".date("Y");
    $sql="SELECT productos.id_producto,productos.descripcion,productos.barcode,productos.talla,productos.estilo,colores.nombre, stock.existencias,productos.precio1 FROM productos,stock, colores WHERE productos.id_producto=stock.id_producto AND productos.id_color=colores.id_color AND stock.id_sucursal='$id_sucursal'";
    $and = "";
    if($max != "" && $max>0)
    {
        $and .= " AND stock.existencias <= '$max'";
        if($min !="")
        {

            $existenas = "PRODUCTOS CON EXISTENCIAS DE ".$min." A ".$max;
            if($min == $max)
            {
                $existenas = "PRODUCTOS CON ".$min." EXISTENCIA";
            }
            $and .= " AND stock.existencias >= '$min'";
        }
        else
        {
            $existenas = "PRODUCTOS CON MENOS DE ".$max." EXISTENCIAS";
        }
    }
    else if($min !="")
    {

        $and .= " AND stock.existencias >= '$min'";
        $existenas = "PRODUCTOS CON MAS DE ".$min." EXISTENCIAS";
    }
    else
    {
        $existenas = "GENERAL";  
    }
    $sql.=$and;
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

  $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
  $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
  $objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
  $objPHPExcel->getActiveSheet()->mergeCells('A4:H4');
  $objPHPExcel->getActiveSheet()->mergeCells('A5:H5');
  $objPHPExcel->getActiveSheet()->mergeCells('A6:H6');
  //Center table
  $stylefonts1 = array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          ),
  				'font'  => array(
  			 'bold'  => false,
  			 'color' => array('rgb' => '0000FF'),
  			 'size'  => 11,
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
  //altura de algunas filas
  for($j=2;$j<5;$j++){
  $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
  }
  //Ancho de algunas filas
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
  //Esrilo de fuentes
  $objPHPExcel->getActiveSheet()->getStyle("A1:H6")->applyFromArray($stylefonts1);
  $objPHPExcel->getActiveSheet()->getStyle("A7:H7")->applyFromArray($stylefonts2);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $title);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $nombre_a." ".$direccion);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $telefonos);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $titulo);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', $existenas);
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', $fech);

  //Encabezados de la tabla
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', "No.");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', "Barcode");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', "Descripción");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', "Estilo");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', "Color");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', "Talla");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', "Precio");
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', "Existencia");

  // Rename worksheet
  $objPHPExcel->getActiveSheet()->setTitle('Stock Productos');

  $cor=0;
  $sp=8;
  $result = _query($sql);
  $nrows = _num_rows($result);
	for($i=0;$i<$nrows;$i++){
    $cor++;
    $row=_fetch_array($result);
    $barcode = $row["barcode"];
    $descripcion = $row["descripcion"];
    $nombre = $row["nombre"];
    $estilo = $row["estilo"];
    $talla = $row["talla"];
    $precio1 = $row["precio1"];
    $existencias = $row["existencias"];

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$sp, $cor);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$sp, $barcode);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$sp, $descripcion);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$sp, $estilo);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$sp, $nombre);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$sp, $talla);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$sp, $precio1);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$sp, $existencias);

    $sp++;
    }
    //estilo para hacer borde a cada celda de datos
    $objPHPExcel->getActiveSheet()->getStyle('A7:H'.$sp)->applyFromArray($BStyle);
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $archivo_salida="reporte_stock.xls";
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
?>
