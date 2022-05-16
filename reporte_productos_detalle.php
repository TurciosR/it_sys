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
    table.headers_perpage{margin-top:37px; margin-left:0px; margin-right:150px;}
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


$tr_header="
 <table align='center'  class='headers_perpage' cellspacing='1' style='width: 92.6%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:10%'>N&deg;</th>
            <th style='width:25%;text-align:left';>Nombres</th>
            <th style='width:25%;text-align:left';>Descripcion Producto</th>
            <th style='width:10%;text-align:left';>Cantidad</th>
            <th style='width:15%;text-align:left';>Precio</th>
            <th style='width:15%'>Total $</th>
           
           
        </tr>
    </table>";
?>
<page backtop="40mm" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>
		
        <table class="page_header" >
              <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="./img/premier.png">
                </td>
            
            </tr>
              <tr> 
            <td align=center style='width:100%;'>REPORTE DE VENTAS POR PRODUCTO </td>
            </tr>
            <tr>
            <td align=center style='width:100%;'>DESDE  <?php echo  ed($fecha_inicio)?> HASTA <?php echo  ed($fecha_fin) ?></td>
            </tr>
            <tr>
            <td align=center style='width:100%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
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

    
    
   
   
    

    $total_productos="SELECT id_prod_serv as id_prod,descripcion,
    empleado.id_empleado,empleado.nombre,empleado.apellido,
	ROUND(SUM(factura_detalle.cantidad), 2) as cant_prod_empl, 
	ROUND(factura_detalle.precio_venta, 2) as precio_venta,
	ROUND(SUM(factura_detalle.subtotal), 2) as subtotal_empleado 
	FROM factura,factura_detalle,producto, empleado
	WHERE factura.id_factura=factura_detalle.id_factura
	AND  id_prod_serv=id_producto 
	AND  empleado.id_empleado=factura_detalle.id_empleado
	AND factura_detalle.tipo_prod_serv='PRODUCTO' 
	AND factura.anulada=0 
	AND DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin'
	GROUP BY  id_prod_serv,empleado.id_empleado
	ORDER BY id_prod_serv,empleado.id_empleado
	";
    $exec_query_total=_query($total_productos);
	$nrows=_num_rows($exec_query_total);	
	$n=0;
	$numlineas=22;
    for($i=1;$i<=$nrows;$i++){	
    $total_serv_empleado=_fetch_array($exec_query_total);
    $cant_prod_empl =$total_serv_empleado['cant_prod_empl'];
	$subtotal_empleado =$total_serv_empleado['subtotal_empleado'];
	$precio_venta =$total_serv_empleado['precio_venta'];
	$descripcion =$total_serv_empleado['descripcion'];
	$nombre=$total_serv_empleado['nombre'];
    $apellido=$total_serv_empleado['apellido'];
    $n++;
	
    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>";
	    echo "<td style='width:10%'>$i</td>";
	    echo "<td style='width:25%;text-align:left'>".$descripcion."</td>";
        echo "<td style='width:25%;text-align:left'>".$nombre." ".$apellido."</td>";
        echo "<td style='width:10%;text-align:right'>".$cant_prod_empl."</td>";
        echo "<td style='width:15%;text-align:right'>".$precio_venta."</td>";
        echo "<td style='width:15%;text-align:right'>".$subtotal_empleado."</td>";
        echo "</tr>";
        echo "</table>";
       
		$residuo=$n%$numlineas;
	if ($residuo==0){
		 echo " <br><br>";
		echo "<H1 class=SaltoDePagina> </H1>";
	}
	
    }
  
$total_empleados= "SELECT empleado.id_empleado,empleado.nombre,empleado.apellido,
ROUND(SUM(factura_detalle.cantidad), 2) as cant_prod_empl, 
ROUND(SUM(factura_detalle.subtotal), 2) as total_empleado 
FROM factura,factura_detalle,producto, empleado
WHERE factura.id_factura=factura_detalle.id_factura
AND  id_prod_serv=id_producto 
AND  empleado.id_empleado=factura_detalle.id_empleado
AND factura_detalle.tipo_prod_serv='PRODUCTO' 
AND factura.anulada=0 
and DATE( factura.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin'
group by  empleado.id_empleado order by empleado.id_empleado
";
 //inicia validar consolidado
 
    $exec_query_empl=_query($total_empleados);
	$nrows2=_num_rows($exec_query_empl);	
	
	$total_final=0;
	$cant_final=0;
	
    echo " <br><br><br>";
	 echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
     <tr>
     <th colspan='4'>Consolidado de Ventas por Empleado durante las fechas: $fecha_ini hasta $fecha_fina </th>
     </tr>
     <tr>    
            <th style='width:10%'>N&deg;</th>
            <th style='width:40%;text-align:left';>Nombres</th>
            <th style='width:20%;text-align:left';>Cant. Total Prod.</th>
            <th style='width:30%'>Total Vendido $</th>
           
           
        </tr>
    </table>";
    for($j=1;$j<=$nrows2;$j++){	
    $row_total_empleado=_fetch_array($exec_query_empl);
    $cant_producto =$row_total_empleado['cant_prod_empl'];
	$total_empleado =$row_total_empleado['total_empleado'];
	//$precio_venta =$row_total_empleado['precio_venta'];
	$nombres=$row_total_empleado['nombre'];
    $apellidos=$row_total_empleado['apellido'];
    $total_final=$total_final+$total_empleado;
	$cant_final=$cant_final+$cant_producto;
    echo " 
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>";
	    echo "<td style='width:10%'>$j</td>";
        echo "<td style='width:40%;text-align:left'>".$nombres." ".$apellidos."</td>";
        echo "<td style='width:20%;text-align:left'>".$cant_producto."</td>";
        echo "<td style='width:30%'>".$total_empleado."</td>";
        echo "</tr>";
        echo "</table>";

	$n++;
    }
 
   
?> 
</page>
