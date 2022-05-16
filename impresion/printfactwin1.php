<?php
header("Access-Control-Allow-Origin: *");
$tmpdir = sys_get_temp_dir();   # directorio temporal
$file =  tempnam($tmpdir, 'prn0');  # nombre dir temporal
$fp = fopen($file, 'wb');
$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];
$shared_printer_win= $_REQUEST['shared_printer_win'];
$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü','°');
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a","\xf8");
	//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($fecha,$cliente,$direccion,$dui,$nit,$giro,$venta)=explode("|",$textoencodificado);
$string="";
$string.= chr(27).chr(64); //clean config
$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
//$string.= chr(27).chr(80); //10 cpi pica
//$string.= chr(27).chr(77); //12 cpi pica
$string.= chr(27).chr(33).chr(4); //FONT  Condensed
//$string.= chr(27).chr(93).chr(48); //FONT  Condensed
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text
$string.=chr(13).$giro."\n"; //  Print text
$string.=chr(13).$nit."\n"; //  Print text
for ($i = 0; $i <3; $i++) {
  $string.= chr(10); //Line Feed
}
$string.=chr(13).$venta; // Print text
$string.= chr(12); //page Feed
//windows
fwrite($fp, $string);
fclose($fp);
copy($file, $shared_printer_win);  # enviar al printer  # enviar al printer compartido con el nombre facturacion
unlink($file);
?>
