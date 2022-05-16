<?php
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
$line=str_repeat("_",55)."\n";
$line1=str_repeat("_",40)."\n";
/*
$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";
	*/
$printer="archivo.txt";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($empresa,$sucursal,$razonsocial,$giro,$nit,$nrc,$tiquete,$datos,$encab_tabla,$venta,$total,$totaltxt,$vendedor)=explode("|",$textoencodificado);
$empresa=trim($empresa)."\n";
$razonsocial=trim($razonsocial)."\n";
$sucursal=trim($sucursal)."\n";
$giro=trim($giro)."\n";
//iniciar string
$string="";

//DESCOMENTAR PARA BEMATECH
$line1=str_repeat("_",55)."\n";
//PARA TMU 220
//$line1=str_repeat("_",40)."\n";

$string.= chr(27).chr(64); // Reset to defaults
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
//$string.= chr(27).chr(51)."1"; //espacio entre lineas 6 x pulgada
$string.= chr(27).chr(116).chr(0); //Multilingual code page
$string.= chr(27).chr(77)."0"; //FONT A
$string.= chr(27).chr(97).chr(1); //Center
$string.=chr(13).$empresa; //  Print text
$string.=chr(13).$razonsocial; // Print text
$string.=chr(13).$sucursal; //  Print text
$string.=chr(13).$giro; //  Print text
$string.=chr(13).$nit."\n";; //  Print text
$string.=chr(13).$nrc."\n";; //  Print text
$string.=chr(13).$tiquete."\n"; //  Print text
$string.=chr(27).chr(33).chr(1); //FONT B
$string.=chr(13).$line1; // Print text Line
$string.= chr(27).chr(97).chr(0); //Left
$string.=chr(13).$datos; // Print text
$string.=chr(13)."\n"; // Print text
//COMENTAR PARA BEMATECH
//$string.=chr(13)."DESCRIPCION      CANT.   P.U.    SUBT. "."\n";
//COMENTAR PARA EPSON TMU 220
$string.=chr(13)."   DESCRIPCION               CANT.     P.U.     SUBT. "."\n";

$string.=chr(13).$line1; // Print text
$string.=chr(13).$venta; // Print text
$string.=chr(13).$line1; // Print text Line
$string.= chr(27).chr(33).chr(0); //FONT A
$string.=chr(13).$total; // Print text
if ($efectivo>0){
	$efectivo=sprintf("%.2f", $efectivo);
	$cambio=sprintf("%.2f", $cambio);
	$string.=chr(13)."\n"; // Print text
	$string.=chr(13)."EFECTIVO $ ".$efectivo."  CAMBIO   $ ".$cambio."\n"; // Print text
}
$string.= chr(27).chr(33).chr(1); //FONT B
$string.= chr(27).chr(97).chr(1); //Center
$string.=chr(13)."E = EXENTO G = GRAVADO \n"; // Print text
$string.=chr(13).$vendedor.".\n"; // Print text
$string.=chr(13).$msj_fin.".\n"; // Print text
$string.= chr(27).chr(100).chr(2); //Line Feed

//$string.=chr(13)."\n"; // Print text
//$string.=chr(13)."\n"; // Print text
//$string.= chr(27).chr(100).chr(2); //Line Feed
for($n=0;$n<5;$n++){
	$string.=chr(13)."\n"; // Print text
}

$string.=chr(29).chr(86)."1";  // CORTAR PAPEL AUTOMATICO
$string.=chr(27).chr(112)."0"."25"."250";  // Abrir cajon
//FIN ENVIO DATOS COMUN LINUX WIN
//send data to USB printer
$fp0=fopen($printer, 'wb');

fwrite($fp0,$string);
//fwrite($fp1,$string);
fclose($fp0);
system('lpr archivo.txt');
?>
