<?php
//Este script es el que se redirecciona al host local donde esta el printer
// y debe haber un apache corriendo con soporte php
//este funciona de maravilla al generar el archivo txt y enviarlo a /dev/lp0
//hay que darle permiso de escritura a cualquiera a lp con
//su -c 'chmod 666 /dev/lp0'  y el permiso de escritura al archivo generado en
//este caso recibo.txt
//con ls -Z /dev/lp0 puede ver el grupo al que pertenece
//si el cable es usb /dev/usb/lp0
//fUNCIONA CON $_POST O $_REQUEST
//$nombre = $_POST['nombre'];
//$apellido = $_POST['apellido'];
//Simplemente manda la impresion al puerto con secuencis ESC EPSON
//VERSION PARA WINDOWS
/*
 Tomado de lib esc pos php
 * <?php
//* ASCII constants *
const ESC = "\x1b";
const GS="\x1d";
const NUL="\x00";

/* Output an example receipt
echo ESC."@"; // Reset to defaults
echo ESC."E".chr(1); // Bold
echo "FOO CORP Ltd.\n"; // Company
echo ESC."E".chr(0); // Not Bold
echo ESC."d".chr(1); // Blank line
echo "Receipt for whatever\n"; // Print text
echo ESC."d".chr(4); // 4 Blank lines

/* Bar-code at the end
echo ESC."a".chr(1); // Centered printing
echo GS."k".chr(4)."987654321".NUL; // Print barcode
echo ESC."d".chr(1); // Blank line
echo "987654321\n"; // Print number
echo GS."V\x41".chr(3); // Cut
exit(0);
 */

$venta = $_REQUEST['datosventa'];

$encabezado="PREMIER \n";
$vendedor="Vendedor: ".$nombre." ".$apellido."\n";
$texto=$venta;
$texto.="Ñandu sofá impresión";
$info = $_SERVER['HTTP_USER_AGENT'];


if(strpos($info, 'Windows') == TRUE){
	$FilePointer = @fopen("LPT1", "w");
	if($FilePointer == FALSE){
        die('No se puedo Imprimir, Verifique su conexion con el Terminal');
    }
	else {
		fwrite($FilePointer,$texto);
		fclose($FilePointer); // cierra el fichero
		//$salida = shell_exec('lpr USB1'); //lpr->puerto impresora, imprimir archivo PRN
	//	$salida = shell_exec('lpr LPT1'); //lpr->puerto impresora, imprimir archivo PRN
	}
}
elseif (strpos($info, 'Linux') == TRUE){
	//echo 'Usted está utilizando Linux!';
	$file="recibo.txt";
	$FilePointer=fopen($file,"w");

	$FilePointer=fopen("/dev/usb/lp0","wb");
	fwrite($FilePointer, chr(27).chr(82).chr(12));//pais code page
	fwrite($FilePointer, chr(27).chr(33).chr(8));//negrita
	fwrite($FilePointer, chr(27).chr(97).chr(1));//centrado
	fwrite($FilePointer, chr(27).chr(107).chr(4));//fuente script
	fwrite($FilePointer, chr(27).chr(33).chr(16));//fuente tamanio alto
	fwrite($FilePointer,$vendedor);
	fwrite($FilePointer, chr(27).chr(33).chr(32));//fuente tamanio ancho
	fwrite($FilePointer,$encabezado);
	//fwrite($FilePointer, chr(27).chr(97).chr(0));//alineado a la izquierda
	//fwrite($FilePointer, chr(27).chr(33).chr(0));//fuente tamanio normal
	fwrite($FilePointer, chr(27).chr(33).chr(8));//negrita
	//fwrite($FilePointer, chr(27).chr(107).chr(4));//fuente script
	//fwrite($FilePointer, chr(27).chr(33).chr(4));
	fwrite($FilePointer, chr(27).chr(15));
	//fwrite($FilePointer, chr(27).chr(97).chr(1));//centrado
	//fwrite($FilePointer, chr(27).chr(116).chr(1));//centrado


fwrite($FilePointer,$texto);
fclose($FilePointer);
//shell_exec("lpr recibo.txt"); //metod usado comentado para pruebas
//shell_exec("cat recibo.txt>/dev/usb/lp0");
}






?>
