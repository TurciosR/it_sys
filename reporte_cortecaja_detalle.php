<style type='text/css'>
    table.page_header {width: 100%;margin-left:53px;  margin-top: 14px;margin-bottom: 25px;  border:none; background-color: #FFFFFF; font-family:helvetica,serif;font-weight: bold; font-size: 14px;}
    table.page_footer {width: 100%; border: none; background-color: #A9A9A9;  padding: 2mm;color:#FFFFFF; font-family:helvetica,serif; font-weight:bold;}
    div.note {border: solid 1mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm; width: 100%; }
    ul.main { width: 95%; list-style-type: square; }
    ul.main li { padding-bottom: 2mm; }
    h1 { text-align: center; font-size: 20mm}
    h3 { text-align:right; font-size: 14px; color:#000080}
    table { vertical-align: middle; }
    tr    { vertical-align: middle; }
    p {margin: 0px 5px 0px 5px;}
    span {margin: 5px;}
    img { border: 1px #000000;}  
    table.headers_perpage_portrait{margin-top:38px; margin-left:0px; margin-right:150px;}
    table.headers_perpage_landscape{margin-top:15px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage.th{text-align:left;}*/
</style>

<?php
include("_core.php");
//Obtener valores por $_GET
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];
$id_sucursal=$_SESSION['id_sucursal'];

$fecha_ini= ed($fecha_inicio);
$fecha_fina=ed($fecha_fin);

$sql_empresa=_query("SELECT * FROM empresa");
$array_empresa=_fetch_array($sql_empresa);
$nombre_empresa=$array_empresa['nombre'];
$telefono=$array_empresa['telefono1'];
$logo_empresa=$array_empresa['logo'];
$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
$array_sucursal=_fetch_array($sql_sucursal);
$nombre_sucursal=$array_sucursal['descripcion'];

$sql="SELECT id_corte_caja, fecha, efectivo, tarjeta, cheque, observaciones, 
total_corte, total_sistema, diferencia,hora,nombre 
FROM corte_caja, usuario
WHERE DATE(fecha) BETWEEN '$fecha_inicio' 
AND '$fecha_fin' 
AND corte_caja.id_usuario=usuario.id_usuario 
AND corte_caja.id_sucursal='$id_sucursal'
";

$result=_query($sql);
$nrows=_num_rows($result);	

//<table align='center' class='headers_perpage_portrait' cellspacing='1' style='width: 85.5%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
$tr_header="<table align='center' class='headers_perpage_landscape' cellspacing='1' style='width: 88.9%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:4%'>N&deg;</th>
            <th style='width:10%;text-align:center'>Fecha y Hora</th>
            <th style='width:12%;text-align:center'>Efectivo </th> 
            <th style='width:10%;text-align:center'>Tarjeta</th>
            <th style='width:8%;text-align:center'>Cheque</th> 
            <th style='width:12%;text-align:center'>Total Corte Caja</th>
            <th style='width:12%;text-align:center'>Total Facturas Sistema</th> 
            <th style='width:10%;text-align:center'>Diferencia</th>
            <th style='width:12%;text-align:center'>Usuario</th>
            <th style='width:10%;text-align:center'>Observacion</th> 
        </tr>
    </table>";

  ?>
<page backtop="40mm" backbottom="40mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>
		
        <table class="page_header" >
             <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                  <?php
                    echo "<img style='width: 10%;' src='./".$logo_empresa."'>";
                    ?>
                </td>
            </tr>
			 <tr>
                <td align=center style='width:90%;'><?php echo  $nombre_empresa. " " .$nombre_sucursal?></td>
            </tr>
            <tr> 	
                <td align=center style='width:90%;'>REPORTE CORTE DE CAJA POR RANGO DE FECHAS </td>
            </tr>
			<tr>
                <td align=center style='width:90%;'>DESDE: <?php echo $fecha_ini;?> HASTA <?php echo $fecha_fina;?></td>
            </tr>
            <tr>
                <td align=center style='width:90%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
            </tr>

        </table>
     <?php echo $tr_header?>   
    </page_header>
    
    <page_footer>
        <table class="page_footer">
        
            <tr>
                <td style="width: 40%; text-align: left;">
                    <?php echo $nombre_empresa?>
                </td>
                <td style="width: 30%; text-align: center">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 30%; text-align: right;">
                    Tel: <?php echo $telefono?>
                </td>
            </tr>
        </table>
    </page_footer>
<?php
$totales_sistema=0;
$totales_corte=0;
$totales_efectivo=0;
$totales_tarjeta=0;
$totales_cheque=0;
	for($i=1;$i<=$nrows;$i++){	

	$row=_fetch_array($result);
	$fecha=$row['fecha'];
	$hora=$row['hora'];
	$fecha_print=ed($fecha);
	$efectivo =$row['efectivo'];
	$efect_print=sprintf("%.2f",$efectivo);
	$tarjeta =$row['tarjeta'];
	$tarjeta_print=sprintf("%.2f",$tarjeta);
	$cheque =$row['cheque'];
	$cheque_print=sprintf("%.2f",$cheque);
	$total_corte =$row['total_corte'];
	$total_corte_print=sprintf("%.2f",$total_corte);
	$total_sistema =$row['total_sistema'];
	$total_sistema_print=sprintf("%.2f",$total_sistema);	
	$diferencia =$row['diferencia'];
	$diferencia_print=sprintf("%.2f",$diferencia);
	$observaciones =$row['observaciones'];
	$nombre=$row['nombre'];
	
	$fechaprint=ed($fecha);
	
    echo "
		<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
		<tr>";
	echo "<td style='width:4%'>".$i."</td>";
    echo "<td style='width:10%;text-align:center'>".$fecha_print."<br>".$hora."</td>";
    echo "<td style='width:12%;text-align:center'>$ ".$efect_print."</td>";
    echo "<td style='width:10%;text-align:center;font-size:10pt'>$ ".$tarjeta_print."</td>";
    echo "<td style='width:8%;text-align:center;font-size:10pt'>$ ".$cheque_print."</td>";
    echo "<td style='width:12%;text-align:center'>$ ".$total_corte_print."</td>";
    echo "<td style='width:12%;text-align:center'>$ ".$total_sistema_print."</td>";
    echo "<td style='width:10%;text-align:center'>$ ".$diferencia_print."</td>";
    echo "<td style='width:12%;text-align:left;font-size:10pt'>".$nombre."</td>";
    echo "<td style='width:10%;text-align:left;font-size:10pt'>".$observaciones."</td>";
    echo "</tr>";
    echo "</table>";
    
	$totales_sistema=$totales_sistema+$total_sistema;
	$totales_corte=$totales_corte+$total_corte;
	$totales_efectivo=$totales_efectivo+$efectivo;
	$totales_tarjeta=$totales_tarjeta+$tarjeta;
	$totales_cheque=$totales_cheque+$cheque;
	
    }
    $totales_sistema_print=sprintf("%.2f",$totales_sistema);
	$totales_corte_print=sprintf("%.2f",$totales_corte);
	$totales_efectivo_print=sprintf("%.2f",$totales_efectivo);
	$totales_tarjeta_print=sprintf("%.2f",$totales_tarjeta);
	$totales_cheque_print=sprintf("%.2f",$totales_cheque);
    echo "<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
    echo "<tr>";
	echo "<td style='width:14%;text-align:right;font-weight:bold'>"." TOTALES "."</td>";
	echo "<td style='width:12%;text-align:center;font-weight:bold'>$ ".$totales_efectivo_print."</td>";
	echo "<td style='width:10%;text-align:center;font-weight:bold;font-size:10pt'>$ ".$totales_tarjeta_print."</td>";
	echo "<td style='width:8%;text-align:center;font-weight:bold;font-size:10pt'>$ ".$totales_cheque_print."</td>";
	echo "<td style='width:12%;text-align:center;font-weight:bold'>$ ".$totales_corte_print."</td>";
    echo "<td style='width:12%;text-align:center;font-weight:bold'>$ ".$totales_sistema_print."</td>";
    echo "<td style='width:32%;text-align:center;font-weight:bold'>"."&nbsp;"."</td>";
    echo "</tr>";
    echo "</table>";  
       
?> 
</page>
