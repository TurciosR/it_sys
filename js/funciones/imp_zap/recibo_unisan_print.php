<?php
header("Access-Control-Allow-Origin: *");
$factura = $_POST['datosrecibo'];
//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
$info = $_SERVER['HTTP_USER_AGENT'];
/*
En el cliente windows debe tener un apache corriendo
la pc debe estar en red siempre para poder imprimir.
en Apache :
crear un directorio en el document root con nombre "recibo"
poner en ese directorio el archivo : "recibo.php"
ir a dispositivos e  impresores de windows, tener el printer matricial instalado con sus drivers
y se deja como predeterminado, importante compartirlo como "recibo".
en linux:
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
if(strpos($info, 'Windows') == TRUE)
  $so_cliente='win';
else
  $so_cliente='lin';
  /*
if( $so_cliente='win'){
$tmpdir = sys_get_temp_dir();   #DIRECTORIO TEMPORAL
$file =  tempnam($tmpdir, 'ctk');

}
if( $so_cliente='lin'){
$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp1')
	$file="/dev/usb/lp1";
else
	$file="/dev/usb/lp0";
//$fp="/dev/usb/lp0";
}
*/
$printer = system('ls /dev/usb/lp*', $retval);

//$printer='/dev/usb/lp0';
$fp = fopen($printer, 'wb');
$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü',);
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","A","E","I","O","U","\x9a");

$factura_codificada = str_replace($latinchars, $encoded, $factura);
$condensed = Chr(27).Chr(33).Chr(4);
$bold1 = Chr(27).Chr(69);
$bold0 = Chr(27).Chr(70);
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
$corte = Chr(27) . Chr(109);
$font12cpi =Chr(27). chr(77);
$avancelinea=Chr(10);
$avance=Chr(27). chr(48); //1/8 pulgada
$string='';
$string.= $initialized;
$string.= $avancelinea.$avancelinea;
$string.= $condensed1;
$string.= chr(27).chr(116).chr(2); //CODE PAGE MULTILINGUAL
$string.= chr(27).chr(33).chr(1); //FONT A
$string.= $bold1;
$string.=$factura_codificada;

//windows
fwrite($fp, $string);
fclose($fp);
/*
if(strpos($info, 'Windows') == TRUE){
copy($file, "//localhost/recibo");  # enviar al printer
unlink($file);
}
*/
?>
