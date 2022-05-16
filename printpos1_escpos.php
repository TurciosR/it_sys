<?php
include("escpos-php/Escpos.php");
/* Este script es el que se redirecciona al host local donde esta el printer
 y debe haber un apache corriendo con soporte php
 * esta version es con el uso de la libreia escpos-php 05/12/2015
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
 */
$venta = $_REQUEST['datosventa'];
$info = $_SERVER['HTTP_USER_AGENT'];

$texto=$venta;

if(strpos($info, 'Windows') == TRUE){
	//INSTALAR Como Generico de texto aunque sea USB con este nombre Receipt_Printer
	//luego compartirlo ejemplo de nombre Receipt_Printer
	$connector = new WindowsPrintConnector("Receipt");
	//al LPT1
	//$connector = new WindowsPrintConnector("LPT1");
}
elseif (strpos($info, 'Linux') == TRUE){
	$connector = new FilePrintConnector("/dev/usb/lp0");
}

$printer = new Escpos($connector);
$profile = StarCapabilityProfile::getInstance();
$printer -> setEmphasis(true);
$printer -> text($venta);
$printer -> cut();
$printer -> close();

?>
