<?php
header("Access-Control-Allow-Origin: *");
//windows
//$tmpdir = sys_get_temp_dir();   # directorio temporal
$array = $_REQUEST['datosproductos'];

$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";

//$printer="archivo.txt";

$string="";


$keyval="";
$n=0;
foreach ($array as $fila){
	foreach ($fila as $key => $val){
	if($key!='fin'){
		$keyval.=$val."|";
		$n+=1;
	}
	else{
  $keyval.=";";
	}

}

}
	$listadatos=explode(';',$keyval);

	for ($i=0;$i<$n ;$i++){

		 list($barcode,$descrip,$precio,$talla,$estilo,$color,$rango,$qty)=explode('|',$listadatos[$i]);
		 for ($j=0;$j<$qty;$j++){
			 	$posx=250; //x,y posicion
 				$posy=10;
			 	$string.="^XA";
		 		$string.="^CFA,30";
		 		$string.="^FO".$posx.",".$posy."^FD CALZADO MAYORGA ^FS";
		 		$string.="^CFA,15";
		 		$posy+=25;
		 		$string.="^FO".$posx.",".$posy."^FD".$descrip."^FS";
		 		$posy+=25;
		 $string.="^FO".$posx.",".$posy."^BY3";
		 $posx+=10;
		 $string.="^BCN,65,Y,N,N";
		 $string.="^FD".$barcode."^FS";
		 $posx-=10;
		 $posy+=105;
		 $string.="^FO".$posx.",".$posy."^FD"."$".$precio."^FS";
		 $posx+=150;
		 $string.="^FO".$posx.",".$posy."^FD".$color ."^FS";
		 $posx-=150;
		 $posy+=20;
		 $string.="^FO".$posx.",".$posy. "^FD".$rango."^FS";
		 $posx+=150;
		 $string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";
		 $string.="^XZ";
	}

}
$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
