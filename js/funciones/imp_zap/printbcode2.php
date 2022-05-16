<?php
header("Access-Control-Allow-Origin: *");
//windows
$tmpdir = sys_get_temp_dir();   # directorio temporal

/*
$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];

$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
*/
$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";

//send data to USB printer zebra zpl
$string=""; // ";Print text
$barcode='84886';
$color="MOR AZ ";
$rango="28-34";
$estilo="9226";
$precio="$ 7.99";
$posx=30; //x,y posicion
$posy=50;  //margen inicial
$string.="^XA";
$string.="^CFA,30";
$string.="^FO".$posx.",".$posy."^FD CALZADO MAYORGA ^FS";
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD CALZADO MAYORGA ^FS";
$string.="^CFA,15";
$posx-=390;
$posy+=25;
$string.="^FO".$posx.",".$posy."^FD"."FROZEN GINA CHONGA"."^FS";
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD"."FROZEN GINA CHONGA"."^FS";
$posy+=25;
$posx=50;
$string.="^FO".$posx.",".$posy."^BY3";
$string.="^BCN,65,Y,N,N";
$string.="^FD".$barcode."^FS";

$posx+=390;
$string.="^FO".$posx.",".$posy."^BY3";
$string.="^BCN,65,Y,N,N";
$string.="^FD".$barcode."^FS";
$posx-=390;
$posy+=105;
$posx=30;
$string.="^FO".$posx.",".$posy."^FD".$color. "30" ."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$precio."^FS";
$posx-=150;
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD".$color. "30" ."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$precio."^FS";
$posx-=390;
$posx=30;$posy+=20;
$string.="^FO".$posx.",".$posy. "^FD".$rango."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";
$posx-=150;
$posx+=390;
$string.="^FO".$posx.",".$posy. "^FD".$rango."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";

$posx=30; //x,y posicion
$posy+=200;
$string.="^CFA,30";
$string.="^FO".$posx.",".$posy."^FD CALZADO MAYORGA ^FS";
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD CALZADO MAYORGA ^FS";
$string.="^CFA,15";
$posx-=390;
$posy+=25;
$string.="^FO".$posx.",".$posy."^FD"."FROZEN GINA CHONGA"."^FS";
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD"."FROZEN GINA CHONGA"."^FS";
$posy+=25;
$posx=50;
$string.="^FO".$posx.",".$posy."^BY3";
$string.="^BCN,65,Y,N,N";
$string.="^FD".$barcode."^FS";

$posx+=390;
$string.="^FO".$posx.",".$posy."^BY3";
$string.="^BCN,65,Y,N,N";
$string.="^FD".$barcode."^FS";
$posx-=390;
$posy+=105;
$posx=30;
$string.="^FO".$posx.",".$posy."^FD".$color. "30" ."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$precio."^FS";
$posx-=150;
$posx+=390;
$string.="^FO".$posx.",".$posy."^FD".$color. "30" ."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$precio."^FS";
$posx-=390;
$posx=30;$posy+=20;
$string.="^FO".$posx.",".$posy. "^FD".$rango."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";
$posx-=150;
$posx+=390;
$string.="^FO".$posx.",".$posy. "^FD".$rango."^FS";
$posx+=150;
$string.="^FO".$posx.",".$posy."^FD".$estilo."^FS";

//$posy+=200;
//$posx=30;
//logo debian
/*
$string.="^FO".$posx.",".$posy;
$logo="
^GFA,2048,2048,16,,U08,S03FF,S0IFCFF8,R07MF,Q01NFE,Q07OF8,P01RF,P07RF8,P0SFC,O03TF8,O0UFE,N01VF,N03KFEJ07KF8,N07KFL07JFC,N0KFCL01KF,M01KF8M07JF8,M07JFEN01JF8,M07JFCO07IFC,M0JF8P03IFE,L01JFQ01JF,L03IFER0JF8,L03IFCR07IF8,L07IFCR03IFC,L0IFES01IFE,L0IF8T0IFE,K01IFU0JF,K01FFCU07IF,J083FF8U03IF8,J083FFV03IF8,K07FEV01IF8,K0FFCW0FF18,J01FF8W0FF18,J01FFX07F08,J03FFX07F8,J03FEP07CL07FC,J07FCO07FFCK03FE,J07F8N01JFK03FE,I047F8N03F801CJ03FE,J0FFCN0FCI06J03FE,J0FF8M01FJ01J01FC,I01FF8M03EO01FC,I01FFN038O01FE,I01FFN07P01FE,I03FEN0FP01FE,I03FCM01EP01FE,I03FCM01CP01FE,I03FCM038P01FE,I03F8M038P01FF,I03F8M078P01FF8,I03F8M07Q01FF,:I03F8M0FN04001FE,I03F8M0EN04001FE,I03F8M0EQ01FE,I03F8M0EQ01FC,I03FN0EQ01F8,:I03FN0FQ01F8,::I03FN0FQ03F,:I03FN0F8L04I07E,I03FN078P0FE,I03FN078O01FC,I03FN07CO01FC,I03FN03CO01F4,I03FN03EI03CJ01E,I03FN01FO03E,I03F8M01FO0FE,I03F8L010F8M01FE,I03FCL0107CM03F8,I03FCM083EM07F,I03F8M091FM0FE,I01F8M050FCK07FC,I01FCN087EK0FF,I01FCN063F8I03FE,I01FCN018FF003FF8,I01FCO0C7LF,J0FEO073KF8,J0FEO01C7IFE,J0FFP0E07FC,J07FP018,J07FEP078,J07FFQ07F,J03FF8,:J03FFC,J01FF4,J01FF8,K0FF8,K0FFC,K07FC,K07FE,K03FE,:K01FF,:L0FF,L07F8,L07FC,L03FC,L01FE,L01FF,M0FF8,M07FC,M03FC,M01FE,N0FF,N07F,N03F8,N01FF,O0FF,O07F8,O03FE,O01FF,P0FF,P03FC,P01FE,Q07FA,Q03FF,R0FF8,R03FC,S0FE,S01F8,T03F8,U01FC,^FS
";
$string.=$logo;
$posx+=300;
$string.="^FO".$posx.",".$posy;
$string.=$logo;

*/
$string.="^XZ";



//linux
$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
