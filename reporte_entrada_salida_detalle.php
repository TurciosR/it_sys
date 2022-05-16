<style type='text/css'>
    table.page_header {width: 100%;margin-left:53px;  margin-top: 25px;margin-bottom: 25px;  border:none; background-color: #FFFFFF; font-family:helvetica,serif;font-weight: bold; font-size: 14px;}
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
    table.headers_perpage{margin-top:18px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage{margin-top:38px; margin-left:0px; margin-right:150px;}*/
    /*table.headers_perpage.th{text-align:left;}*/
    
     H1.SaltoDePagina
 {
     PAGE-BREAK-AFTER: always
 }
</style>

<?php
	include("_core.php");   
	$fecha_inicio= $_GET['fecha_inicio'];
    $fecha_fin= $_GET['fecha_fin'];
	$fecha_ini= ed($fecha_inicio);
	$fecha_fina=ed($fecha_fin);
	$sql_empresa=_query("SELECT * FROM empresa");
	$array_empresa=_fetch_array($sql_empresa);
	$nombre_empresa=$array_empresa['nombre'];
	$telefono=$array_empresa['telefono1'];
	$logo_empresa=$array_empresa['logo'];
	
	$id_user=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
		
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];

	$tr_header="
	<table align='center'  class='headers_perpage' cellspacing='1' style='width: 92.6%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:25%'>Total Existencias Anterior</th>
            <th style='width:25%>Total Existencias Periodo Seleccionado </th>
            <th style='width:25%;text-align:left';>Total Vendido Anterior</th>
            <th style='width:25%'>Total VendidoPeriodo Seleccionado</th>
        </tr>
    </table>";
?>
<page backtop="40mm" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
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
            <td align=center style='width:100%;'>REPORTE DE  COMPRAS Y VENTAS POR RANGO DE FECHA </td>
            </tr>
            <tr>
            <td align=center style='width:100%;'>DESDE  <?php echo  ed($fecha_inicio)?> HASTA <?php echo  ed($fecha_fin) ?></td>
            </tr>
            <tr>
            <td align=center style='width:100%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
			</tr>
        </table>
         <!--?php echo $tr_header?-->   
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

$total_compras_ante= "SELECT  ROUND(SUM(precio_compra*entrada), 2) as total_adquirido_dinero, 
ROUND(SUM(entrada), 2) as total_producto_adquirido
FROM movimiento_producto 
WHERE precio_compra>0
AND entrada>0
AND id_sucursal_origen='$id_sucursal'
AND DATE( movimiento_producto.fecha_movimiento )< '$fecha_inicio'
";

$exec_sql_compras_ante=_query($total_compras_ante);


$total_compras_rango= "SELECT  ROUND(SUM(precio_compra*entrada), 2) as total_adquirido_dinero, 
ROUND(SUM(entrada), 2) as total_producto_adquirido
FROM movimiento_producto 
WHERE precio_compra>0
AND entrada>0
AND id_sucursal_origen='$id_sucursal'
AND DATE(movimiento_producto.fecha_movimiento) BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
$exec_sql_compras_rango=_query($total_compras_rango);

$total_ventas_ante="
SELECT ROUND(SUM(factura_detalle.subtotal), 2) as total_vendido, ROUND(SUM(cantidad), 2) as total_producto   
FROM factura,factura_detalle
WHERE  factura.id_factura=factura_detalle.id_factura 
AND factura_detalle.tipo_prod_serv='PRODUCTO'
AND factura.anulada=0 
AND factura.finalizada=1
AND  factura.id_sucursal='$id_sucursal'
AND DATE( factura.fecha ) < '$fecha_inicio'
";
$exec_sql_ventas_ante=_query($total_ventas_ante);

$total_ventas="
SELECT ROUND(SUM(factura_detalle.subtotal), 2) as total_vendido, ROUND(SUM(cantidad), 2) as total_producto   
FROM factura,factura_detalle
WHERE  factura.id_factura=factura_detalle.id_factura 
AND factura_detalle.tipo_prod_serv='PRODUCTO'
AND factura.anulada=0 
AND factura.finalizada=1
AND  factura.id_sucursal='$id_sucursal'
AND DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
$exec_sql_ventas_rango=_query($total_ventas);

 //inicia consolidado
$row_total_compras_ante=_fetch_array($exec_sql_compras_ante);
$row_total_compras_rango=_fetch_array($exec_sql_compras_rango);
$row_total_ventas_ante=_fetch_array($exec_sql_ventas_ante);
$row_total_ventas_rango=_fetch_array($exec_sql_ventas_rango);
 
$total_compras_dinero_ante=$row_total_compras_ante['total_adquirido_dinero']; 
$total_producto_adquirido_ante=$row_total_compras_ante['total_producto_adquirido'];
 
$total_compras_dinero_rango=$row_total_compras_rango['total_adquirido_dinero']; 
$total_producto_adquirido_rango=$row_total_compras_rango['total_producto_adquirido'];

$total_ventas_dinero_ante=$row_total_ventas_ante['total_vendido']; 
$total_producto_vendido_ante=$row_total_ventas_ante['total_producto'];

$total_ventas_dinero_rango=$row_total_ventas_rango['total_vendido']; 
$total_producto_vendido_rango=$row_total_ventas_rango['total_producto']; 

echo " <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
	echo "<tr>";	        
        echo "<td style='width:35%;text-align:left'>"."Total existencias producto adquirido anteriormente"."</td>";
        echo "<td style='width:15%;text-align:right'>".$total_producto_adquirido_ante."</td>";
        echo "<td style='width:35%;text-align:left'>"."Total en dinero adquirido anteriormente"."</td>";           
        echo "<td style='width:15%;text-align:right'> $".$total_compras_dinero_ante."</td>";
    echo "</tr>"; 
      echo "<tr>"; 
		
		echo "<td style='width:35%;text-align:left'>"."Total existencias producto adquirido desde: ".$fecha_ini." hasta ".$fecha_fina."</td>";
		echo "<td style='width:15%;text-align:right'>".$total_producto_adquirido_rango."</td>";
		echo "<td style='width:35%;text-align:left'>"."Total en dinero adquirido periodo desde: ".$fecha_ini." hasta ".$fecha_fina."</td>";
		echo "<td style='width:15%;text-align:right'>$ ".$total_compras_dinero_rango."</td>";
	echo "</tr>";
    
    echo "<tr>";	 
		echo "<td style='width:35%;text-align:left'>"."Total cantidad  de producto vendido desde: ".$fecha_ini." hasta ".$fecha_fina."</td>";
		echo "<td style='width:15%;text-align:right'>".$total_producto_vendido_rango."</td>";
		echo "<td style='width:35%;text-align:left'>"."Total en dinero producto vendido desde: ".$fecha_ini." hasta ".$fecha_fina."</td>";
		echo "<td style='width:15%;text-align:right'>$ ".$total_ventas_dinero_rango."</td>";
		
	echo "</tr>";
      
        echo "</table>";

	/*

	 $total_final_print=sprintf("%0.2f",$total_final);
	echo " <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>";
    echo "<td style='width:70%;text-align:right;font-weight:bold'>"." TOTAL  "."</td>";
	echo "<td style='width:30%;text-align:center;font-weight:bold'>$ ".$total_final_print."</td>";
    echo "</tr>";
    echo "</table>";
   */
?> 
</page>
