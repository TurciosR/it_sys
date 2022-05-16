<?php
header("Access-Control-Allow-Origin: *");
//windows
$tmpdir = sys_get_temp_dir();   # directorio temporal
$file =  tempnam($tmpdir, 'prn3');  # nombre dir temporal
$fp = fopen($file, 'wb');
$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];
$shared_printer_win= $_REQUEST['shared_printer_win'];
$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü','°');
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a","\xf8");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($fecha,$cliente,$giro_cte,$direccion,$nit,$registro,$venta)=explode("|",$textoencodificado);

$string="";
$string.= chr(27).chr(64); //clean config
$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
$string.= chr(27).chr(80); //10 cpi pica
//headers cc ff
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text
$string.=chr(13).$giro_cte."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text
$string.= chr(10); //Line Feed
$string.=chr(13).$nit.""; //  Print text
$string.=chr(13).$registro."\n"; //  Print text
$string.=chr(13).$venta; // Print text
$string.= chr(12); //page Feed
//send data to USB printer
//windows
fwrite($fp, $string);
fclose($fp);
copy($file,$shared_printer_win);  # enviar al printer
unlink($file);
?>
