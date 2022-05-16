<?php
header("Access-Control-Allow-Origin: *");
//windows
$tmpdir = sys_get_temp_dir();   # directorio temporal


$texto = strtoupper($_REQUEST['datosventa']);
//$efectivo = $_REQUEST['efectivo'];
//$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];

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

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü','°');
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a","\xf8");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($fecha,$cliente,$giro_cte,$direccion,$nit,$registro,$venta)=explode("|",$textoencodificado);

$string="";
$string.= chr(27).chr(64); //clean config
$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
$string.= chr(27).chr(103); //10 cpi pica
//headers cc ff
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text
$string.=chr(13).$giro_cte."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text
$string.=chr(13).$nit."\n"; //  Print text
$string.=chr(13).$registro."\n"; //  Print text
$string.=chr(13).$venta; // Print text
$string.= chr(12); //page Feed
//send data to USB printer
//linux
$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
