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




$keyval="";
$n=1;
foreach ($array as $fila){
	foreach ($fila as $key => $val){

	if($key!='fin'){
		$keyval.=$val."|";

	}
	if($key=='fin'){
  	$keyval.=";";
		$n+=1;
	}

}

}
$listadatos=explode(';',$keyval);
$string="";
for ($i=0;$i<$n ;$i++){
		 list($barcode,$descrip,$precio,$talla,$estilo,$color,$rango,$id_proveedor,$qty)=explode('|',$listadatos[$i]);
		 for ($j=0;$j<$qty;$j++){

		// $string.=$barcode."-".$descrip."-".$precio."-".$talla."-".$estilo."-".$color."-".$rango."-".$id_proveedor."-".$qty."\n";

			 	$posx=260; //x,y posicion
				$string.="^XA";
 				$posy=12;

		 		$string.="^CF0,30";
				$string.="^FO".$posx.",".$posy."^FD"."CALZADO MAYORGA"."^FS";

				$posx=250;
				$posy+=32;
				$string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";
		 		$posx+=100;
		 		$string.="^CF0,25";
		 		$string.="^FO".$posx.",".$posy."^FD".$color."^FS";
		 		$posx+=215;
				$string.="^CF0,35";
				$string.="^FO".$posx.",".$posy."^FD".$talla."^FS";
				$string.="^CF0,30";
				$string.="^BY3,1";
				$posx=250; $posy=70;
				$string.="^FO".$posx.",".$posy;
				$string.="^BCN,70,N";
				$string.="^FD".$barcode."^FS";
				$string.="^CF0,25";
				$posx=552; $posy=80;
				$string.="^FO".$posx.",".$posy."^FD".$rango."^FS";
				$posx=250; $posy=150;
				$string.="^CF0,20";
				$string.="^FO".$posx.",".$posy."^FD".$descrip."^FS";
				$string.="^CF0,25";
				$posx=250; $posy=180;
				$string.="^FO".$posx.",".$posy."^FD".$barcode."^FS";
				$posx=410; $posy=180;
			  $string.="^FO".$posx.",".$posy."^FD".$id_proveedor."^FS";
				$posx=510; $posy=170;
				$string.="^CF0,35";
				$string.="^FO".$posx.",".$posy."^FD"."$".$precio."^FS";
				$string.="^XZ";

	}

}

$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
