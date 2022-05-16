<?php
//ultima modificacion:  06/05/2016
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
$msj_fin='GRACIAS POR SU COMPRA, VUELVA PRONTO !';
const ESC = "\x1b";
$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";
$printer="/dev/usb/lp0";

$string.= chr(27).chr(33).chr(1); //FONT B
$string.= chr(27).chr(97).chr(1); //Center
$string.=chr(13)."E = EXENTO G = GRAVADO \n"; // Print text
$string.=chr(13).$msj_fin.".\n"; // Print text
$string.= chr(27).chr(100).chr(2); //Line Feed
$string.=chr(13)."\n"; // Print text
//send data to USB printer
$fp=fopen($printer, 'wb');
fwrite($fp,$string);
fclose($fp);
?>
