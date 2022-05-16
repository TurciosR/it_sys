<?php

//include("escpos-php/Escpos.php");
/* Este script es el que se redirecciona al host local donde esta el printer
y debe haber un apache corriendo con soporte php
esta version es sin el uso de la libreria escpos-php 05/12/2015
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
*/
$texto = $_REQUEST['datosventa'];
$info = $_SERVER['HTTP_USER_AGENT'];

const ESC = "\x1b";
$printer="/dev/usb/lp0";
$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$newphrase = str_replace($latinchars, $encoded, $texto); 
$string= ESC."@"; // Reset to defaults
$string.= chr(27).chr(116).chr(0); //Multilingual code page
$string.= ESC."E".chr(77); // Bold
$string.=chr(13).$newphrase."\n"; // Print text 
//send data to USB printer
$fp=fopen($printer, 'w');
fwrite($fp,$string);
fclose($fp);
?>

