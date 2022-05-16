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
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
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
list($empresa,$sucursal,$razonsocial,$giro,$fecha,$tiquete,$cliente,$direccion,$dui,$nit,$venta,$total,$totaltxt)=explode("|",$textoencodificado);
$empresa=trim($empresa)."\n";
$razonsocial="\xa4 \xa5 ".trim($razonsocial)."\n";
$sucursal=trim($sucursal)."\n";
$giro=trim($giro)."\n";
$string= chr(27).chr(64); // Reset to defaults printer lx-350
//27 107 49    ESC k 1	  Select NLQ Sans Serif font

//espacio entre linea
//27 51 n      ESC 3 n	  Select n/216 inch line spacing (n=0..255)
$string.= chr(27).chr(51).chr(49); //espacio entre lineas n/216
//$string.= chr(15); //condensed mode
$string.= chr(27).chr(33).chr(4); //FONT  Condensed
//$string.= chr(27).chr(82).chr(12); //Region Latinoamerica
//$string.= chr(27).chr(33).chr(16); //FONT double height
//$string.= chr(27).chr(97).chr(1); //Center
$avancelinea=chr(10);
/*
for($i=0;$i<6;$i++){
	$avancelinea.=$avancelinea;
}
*/
//$string.= $avancelinea.$avancelinea.$avancelinea.$avancelinea.$avancelinea;
$string.=$condensed1;
$string.= chr(27).chr(97).chr(0); //Left

$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text
$string.=chr(13).$dui."\n"; //  Print text
$string.= $avancelinea;
//$string.=chr(13).$line; // Print text
$string.=chr(13).$venta; // Print text
//$string.=chr(13).$line; // Print text Line
//$string.=chr(13).$total; // Print text
$string.=chr(13).$totaltxt; // Print text
//$string.=chr(13)."EFECTIVO $ ".$efectivo.".\n"; // Print text
//$string.=chr(13)."CAMBIO   $ ".$cambio.".\n"; // Print text
$string.= chr(27).chr(33).chr(0); //FONT Elite
$string.=chr(27).chr(33).chr(1); //FONT Elite


//send data to USB printer
$fp0=fopen($printer, 'wb');
//$fp1=fopen($printer1, 'wb');
fwrite($fp0,$string);
fwrite($fp1,$string);
fclose($fp);
?>
