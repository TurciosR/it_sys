<?php
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/

require __DIR__ . '/escpos-php/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;

header("Access-Control-Allow-Origin: *");
$texto = strtoupper($_REQUEST['datosvale']);
$info = $_SERVER['HTTP_USER_AGENT'];
const ESC = "\x1b";
$line=str_repeat("_",55)."\n";
$line1=str_repeat("_",40)."\n";
$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";
//$printer="/dev/usb/lp0";
//NOMBRE DE EQUIPO ASIGNADO POR UNA REGLA UDEV
//$printer="/dev/Bematech";
$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($text1, $text4, $text2, $text3)=explode("|",$textoencodificado);
//iniciar string
//send data to USB printer
try {
    // Enter the device file for your USB printer here
    $connector = new FilePrintConnector($printer);
# Vamos a alinear al centro lo próximo que imprimamos
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

$logo = EscposImage::load("logo/sg.png", false);
$printer->bitImage($logo);
//cargar e imprimir el logo
//$logo = EscposImage::load("logo/lbsm.png", false);
//$printer->bitImage($logo);

$printer -> setTextSize(1, 1); //tamanio texto entre 1 y 8 combinado ancho alto
//justificar a la izquierda
$printer->setJustification(Printer::JUSTIFY_CENTER);

$string.=$text1;
if($text4 != "")
	$string.=$text4; //  Print text
$printer->text($string);
$string="";
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->setTextSize(2,2);
$string.="\n".$text2; //  Print text
$printer->text($string);
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->setTextSize(1,1);
$string = "";
//$string.=$line1; // Print text Line
$string.=$text3;
$printer->text($string);
	$printer -> cut();
	/* Close printer */
	$printer -> close();
	} catch (Exception $e) {
	    echo "NO SE PUDO IMPRIMIR: " . $e -> getMessage() . "\n";
	}
?>
