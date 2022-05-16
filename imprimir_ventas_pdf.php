<?php
include('_core.php');
require('htmltable_ventas.php');
require('num2letras.php');
//Obtener valores por $_GET
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];
$tipo= $_GET['tipo'];
// Consultar la base de datos configuracion
 $sql_organizacion="SELECT id_empresa, nombre, razonsocial, direccion, telefono1,
  telefono2, website, email, nit, iva FROM empresa ";   

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


$fecha_inic=ed($fecha_inicio);
$fecha_fina=ed($fecha_fin);
$tr_head0="<TR><TD> </TD>";
$tr_head0.="<TD> DESDE: ".$fecha_inic."</TD>";
$tr_head0.="<TD>"." HASTA: ".$fecha_fina."</TD>";
$tr_head0.="<TD>"." "."</TD>";
$tr_head0.="</TR>";

$tr_head="<TR><TD> No.</TD>";
$tr_head.="<TD>"."  FECHA"."</TD>";
$tr_head.="<TD>"."  VENTAS DIA"."</TD>";
$tr_head.="<TD>"."  TIPO"."</TD>";
$tr_head.="</TR>";
$htmlTable="<TABLE>";

$htmlTable.=$tr_head0;
$htmlTable.=$tr_head;
$recno=0;

switch ($tipo) {
	case 'CONSOLIDADO':
		$sql="SELECT fecha, ROUND(SUM(factura.total), 2) as total_diario FROM factura where  
		factura.anulada=0 and DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin' group by fecha";
		break;
	case 'PRODUCTO':
		$sql="SELECT factura.fecha, ROUND(SUM(factura_detalle.subtotal), 2) as total_diario FROM factura,factura_detalle
		where  factura.id_factura=factura_detalle.id_factura AND factura_detalle.tipo_prod_serv='PRODUCTO'
		AND factura.anulada=0 and DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin' group by fecha";
		break;
	case 'SERVICIO':
		$sql="SELECT factura.fecha, ROUND(SUM(factura_detalle.subtotal), 2) as total_diario FROM factura,factura_detalle
		where  factura.id_factura=factura_detalle.id_factura AND factura_detalle.tipo_prod_serv='SERVICIO'
		AND factura.anulada=0 and DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin' group by fecha";
		break;
 }
 
 //$sql="SELECT fecha, ROUND(SUM(factura.total), 2) as total_diario FROM factura where  
//		factura.anulada=0 and DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin' group by fecha";
		
$result=_query($sql);
$nrows=_num_rows($result);	
$cor=0;
$linea_fin=6;
if($tipo=='CONSOLIDADO'){
	$tipo='PRODUCTOS Y SERVICIOS'	;
}
for($i=0;$i<$nrows;$i++){	
	$cor=$cor+1;	
	$row=_fetch_array($result);
	$fecha=$row['fecha'];
	$total_diario=$row['total_diario'];
	$fechaprint=ed($fecha);
	
	$htmlTable.=	'<TR><TD>'.$cor.'</TD>';
	$htmlTable.=	'<TD>'.$fechaprint.'</TD>';								
	$htmlTable.=	'<TD>$ '.$total_diario.'</TD>';	
	$htmlTable.=	'<TD>'.$tipo.'</TD>';	
	$htmlTable.=	'</TR>';
	
	if($cor%$linea_fin==0 && $linea_fin!=$nrows){
						$htmlTable.=$tr_head0;
						$htmlTable.=$tr_head;																
						//$cor=$cor+1;
	}						
}								

$htmlTable.='</TABLE>';
$fechaprint2=date('d-m-Y');	
$pdf=new PDF_HTML_Table();
$pdf->AliasNbPages();
$pdf->AddPage('P','letter');

$pdf->ln(4);
$pdf->SetFont('Arial','',8);
//$pdf->WriteHTML("$htmlTable0");	
$pdf->WriteHTML("$htmlTable");		
 $pdf->ln(1);
$pdf->Output();	
  
?> 
