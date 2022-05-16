
<?php
header("Access-Control-Allow-Origin: *");
$tmpdir = sys_get_temp_dir();   # directorio temporal
$file =  tempnam($tmpdir, 'prn0');  # nombre dir temporal
$fp = fopen($file, 'wb');

$texto = strtoupper($_REQUEST['datosvale']);
$info = $_SERVER['HTTP_USER_AGENT'];

$shared_printer_win= $_REQUEST['shared_printer_win'];
$shared_printer_pos= $_REQUEST['shared_printer_pos'];
$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
//$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
//$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
//iniciar string
$string="";

$line1=str_repeat("_",55)."\n";


$string.= chr(27).chr(64); // Reset to defaults
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
//$string.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada
$string.= chr(27).chr(116).chr(0); //Multilingual code page
$string.= chr(27).chr(77)."0"; //FONT A
$string.= chr(27).chr(97).chr(1); //Center

$string.=chr(13).$textoencodificado ."\n";

$string.= chr(27).chr(100).chr(2); //Line Feed

for($n=0;$n<3;$n++){
	$string.=chr(13)."\n"; // Print text
}
$string.=chr(29).chr(86)."1";  // CORTAR PAPEL AUTOMATICO
$string.=chr(27).chr(112)."0"."25"."250";  // Abrir cajon
//$string.= chr(10); //Line Feed

//send data to USB printer

//windows
fwrite($fp, $string);
fclose($fp);
copy($file, $shared_printer_pos);  # enviar al printer  # enviar al printer compartido con el nombre facturacion
unlink($file);
?>
