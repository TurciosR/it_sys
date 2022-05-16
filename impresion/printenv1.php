<?php
//ultima modificacion:  06/05/2016
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
header("Access-Control-Allow-Origin: *");
$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];
$msj_fin='GRACIAS POR SU COMPRA, VUELVA PRONTO !';
const ESC = "\x1b";
$line=str_repeat("_",40)."\n";

$line1=str_repeat("_",30)."\n";
$initialized = chr(27).chr(64);

$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";
//echo puerto;

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($fecha,$cliente,$direccion,$dui,$nit,$venta)=explode("|",$textoencodificado);

$string="";
$string.= chr(27).chr(64); //clean config
$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
$string.= chr(27).chr(80); //10 cpi pica
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente." "; //  Print text
if(empty($nit))
	$string.=chr(13).$dui."\n"; //  Print text
else
	$string.=chr(13).$nit."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text

$string.=chr(13).$venta; // Print text
$string.= chr(10); //Line Feed
$string.= chr(12); //page Feed
//send data to USB printer
$fp0=fopen($printer, 'wb');
fwrite($fp0,$string);
fclose($fp0);
?>
