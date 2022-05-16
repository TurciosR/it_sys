<?php

 include('_core.php');
 require('html_table.php');
 require('num2letras.php');
 //header("Content-type: application/pdf");
 
 //Obtener valores por $_GET
 
 $fecha= $_GET['fecha_movimiento'];
 
 // Consultar la base de datos configuracion
 
 $sql_organizacion="SELECT id_empresa, nombre, razonsocial, direccion, telefono1,
  telefono2, website, email, nit, iva FROM empresa 
 ";   

 $resultado_org=_query($sql_organizacion);
 $num_rows = _num_rows($resultado_org);

						
 while($row_org=_fetch_array($resultado_org)){		
				$empresa=utf8_decode($row_org['nombre']);	
				$telefonos=$row_org['telefono1'].'   '.$row_org['telefono2'];				
 }		
 
//Arreglo para poner El Mes en letras
             	$meses=array("1"=>'Enero',
                "2"=>'Febrero',
                "3"=>'Marzo',
                "4"=>'Abril',
                "5"=>'Mayo',
                "6"=>'Junio',
                "7"=>'Julio',
                "8"=>'Agosto',
                "9"=>'Septiembre',
                "10"=>'Octubre',
                "11"=>'Noviembre',
                "12"=>'Diciembre'
			); 
//$mes_letras=$meses[$mes];			
 	

$titulo_encabezado="REPORTE DE AJUSTE DE INVENTARIO";


$espacios=10;

$fecha_mov=ed($fecha);
$tr_head0="<TR><TD></TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="<TD> FECHA DE AJUSTE: ".$fecha_mov."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="</TR>";

$tr_head="<TR><TD> No.</TD>";
$tr_head.="<TD>"."BARCODE"."</TD>";
$tr_head.="<TD>"."     DESCRIPCION"."</TD>";
$tr_head.="<TD>STOCK SISTEMA</TD>
<TD>CONTEO FISICO</TD>
<TD>DIFERENCIA</TD>
<TD>FECHA</TD>
<TD>OBSERVACIONES</TD>
</TR>";

$htmlTable="<TABLE>";
$htmlTable.=$tr_head0;
$htmlTable.=$tr_head;
$recno=0;
$sql="SELECT producto.id_producto as id_prod,producto.descripcion,producto.barcode,ajuste_inventario.*
	FROM producto JOIN ajuste_inventario ON producto.id_producto=ajuste_inventario.id_producto
	WHERE ajuste_inventario.fecha='$fecha'";
 
$result_stock=_query($sql);
$nrows=_num_rows($result_stock);	
$cor=0;
$linea_fin=9;
for($i=0;$i<$nrows;$i++){	
	$cor=$cor+1;	
	$row=_fetch_array($result_stock);
	$id_producto=$row['barcode'];
	$descripcion=utf8_decode($row['descripcion']);
	$stock_actual=$row['stock_actual'];
	$conteo_fisico=$row['conteo_fisico'];
	$diferencia=$row['diferencia'];
	$fechamov=$row['fecha'];
	$fechaprint=ed($fechamov);
	$observaciones=utf8_decode($row['observaciones']);
	$htmlTable.=	'<TR><TD>'.$cor.'</TD>';
	$htmlTable.=	'<TD>'.$id_producto.'</TD>';								
	$htmlTable.=	'<TD>'.$descripcion.'</TD>';	
	$htmlTable.=	'<TD>'.$stock_actual.'</TD>';	
	$htmlTable.=	'<TD>'.$conteo_fisico.'</TD>';	
	$htmlTable.=	'<TD>'.$diferencia.'</TD>';	
	$htmlTable.=	'<TD>'.$fechaprint.'</TD>';	
	$htmlTable.=	'<TD>'.$observaciones.'</TD>';	
	$htmlTable.=	'</TR>';
	
	if($cor%$linea_fin==0 && $linea_fin!=$nrows){
		$htmlTable.=$tr_head0;
		$htmlTable.=$tr_head;																
	}						
}								

$htmlTable.='</TABLE>';
$fechaprint2=date('d-m-Y');	
$pdf=new PDF_HTML_Table();
$pdf->AliasNbPages();
$pdf->AddPage('L','letter');

$pdf->ln(4);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("$htmlTable");		
 $pdf->ln(1);
$pdf->Output();	
 
 
?> 
