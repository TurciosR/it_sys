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
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
//$printer = system('ls /dev/usb/lp*', $retval);

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($empresa,$sucursal,$razonsocial,$giro1,$fecha,$cliente,$direccion,$dui,$nit,$registro,$giro_cte,$venta,$total)=explode("|",$textoencodificado);
$empresa=trim($empresa)."\n";
$razonsocial="\xa4 \xa5 ".trim($razonsocial)."\n";
$sucursal=trim($sucursal)."\n";
$giro1=trim($giro1)."\n";

$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
$string.= chr(10); //Line Feed
$string.= chr(15); //condensed mode
$string.= chr(27).chr(77); //12 cpi
//headers cc ff
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text
$string.=chr(13).$direccion."\n"; //  Print text
$string.=chr(13).$dui."\n"; //  Print text
$string.=chr(13).$nit."\n"; //  Print text
//$string.=chr(13).$nit."\n"; //  Print text
$string.=chr(13).$registro."\n"; //  Print text
$string.=chr(13).$giro_cte."\n"; //  Print text
//datos venta factura
$string.= chr(10); //Line Feed
$string.= chr(10); //Line Feed
for($h=0;$h<7;$h++){
  $string.= chr(10); //Line Feed
}
$string.=chr(13).$venta; // Print text
$string.= chr(10); //Line Feed
$string.= chr(10); //Line Feed
$string.=chr(13).$totaltxt; // Print text
$string.= chr(10); //Line Feed
$string.= chr(12); //page Feed
//send data to USB printer
//windows
fwrite($fp, $string);
fclose($fp);
copy($file, $shared_printer_pos);  # enviar al printer
unlink($file);
?>
