<?php

header("Access-Control-Allow-Origin: *");

require __DIR__ . '/escpos-php/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;

$texto = strtoupper($_REQUEST['datosventa']);
$headers = $_REQUEST['headers'];
$footers = $_REQUEST['footers'];
$efectivo = 0;//$_REQUEST['efectivo'];
$cambio = 0;//$_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];

$line=str_repeat("_",55)."\n";
$line1=str_repeat("_",40)."\n";

$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
//$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
$foot = str_replace($latinchars, $encoded, $footers);
$head = str_replace($latinchars, $encoded, $headers);
list($tipoa,$tiquete,$fecha,$caja,$cliente,$datos,$total,$apagar,$totaltxt)=explode("|",$textoencodificado);

list($h1,$h2,$h3,$h4,$h5,$h6,$h7,$h8,$h9,$h10 )=explode("|",$head);
list($f1,$f2,$f3,$f4,$f5,$f6,$f7,$f8,$f9,$f10 )=explode("|",$foot);
//iniciar string
$tipoa = trim($tipoa);
$string="";

$line1=str_repeat("_",40)."\n";

try {
    // Enter the device file for your USB printer here
    $connector = new FilePrintConnector($printer);
# Vamos a alinear al centro lo próximo que imprimamos
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

//cargar e imprimir el logo
if($tipoa == "TICKET")
{
	$logo = EscposImage::load("logo/sg.png", false);
	$printer->bitImage($logo);

	$printer -> setTextSize(1, 1); //tamanio texto entre 1 y 8 combinado ancho alto
	//justificar a la izquierda
	$printer->setJustification(Printer::JUSTIFY_CENTER);
	$string.=$h1."\n";
	if($h2!='')
		$string.=$h2."\n";
	if($h3!='')
		$string.=$h3."\n";
	if($h4!='')
		$string.=$h4."\n";
	if($h5!='')
		$string.=$h5."\n";
	if($h6!='')
		$string.=$h6."\n";
	if($h7!='')
		$string.=$h7."\n";
	if($h8!='')
		$string.=$h8."\n";
	if($h9!='')
		$string.=$h9."\n";
	if($h10!='')
	$string.=$h10."\n";
	$printer->text($string);
}

$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->setTextSize(2,2);
if($tipoa == "TICKET")
{
	$string = "\n";
}
$string.=$tiquete."\n"; //  Print text
$printer->text($string);
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(1,1);
$string = "";
//$string.=$line1; // Print text Line
if($fecha!="")
{
	$string.="\n";
	$string.=$fecha."\n";
} //  Print text
if($caja!="")
{
	$string.=$caja."\n"; //  Print text
}
if($cliente!="")
{
	$string.=$cliente."\n"; //  Print text
	$string.="\n";
}
$string.=$datos; // Print text
$string.="\n"; // Print text
//$string.=$line1; // Print text
//$string.=$total; // Print text
$printer->text($string);
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(2,2);
$string = "";
if($apagar !="")
{
	$string = "\n";
	$string.= $apagar;
	$string.="\n";
}
$printer->text($string);
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(1,1);
 // Print text
$string = "";
if($totaltxt !="")
{
	$string.=$totaltxt; // Print text
	$string.="\n";
}
//$string.=$line1; // Print text Line
if($tipoa=="TICKET")
{
	$string.=$f1."\n";
	if($f2!='')
	$string.=$f2."\n";
	$printer->text($string);
	$printer->setJustification(Printer::JUSTIFY_CENTER);
	$printer->setTextSize(1,1);
	if($f3!='')
		$string="\n".$f3."\n";
	$printer->text($string);
	$printer->setJustification(Printer::JUSTIFY_LEFT);
	$printer->setTextSize(1,1);
	$string ="";
	if($f4!='')
		$string.=$f4."\n";
	if($f5!='')
		$string.=$f5."\n";
	if($f6!='')
		$string.=$f6."\n";
	if($f7!='')
		$string.=$f7."\n";
	if($f8!='')
		$string.=$f8."\n";
	if($f9!='')
		$string.=$f9."\n";
	if($f10!='')
		$string.=$f10."\n";
	$printer->text($string);
}
	$printer -> cut();
	/* Close printer */
	$printer -> close();
	} catch (Exception $e) {
	    echo "NO SE PUDO IMPRIMIR: " . $e -> getMessage() . "\n";
	}
?>
