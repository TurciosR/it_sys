<style type='text/css'>
    table.page_header {width: 100%;margin-left:53px;  margin-top: 15px;margin-bottom: 25px;  border:none; background-color: #FFFFFF; font-family:helvetica,serif;font-weight: bold; font-size: 14px;}
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
    table.headers_perpage{margin-top:32px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage.th{text-align:left;}*/
</style>

<?php
include("_core.php");
/*
    $fecha1=$_GET["fecha_inicio"];
    $fecha2=$_GET["fecha_fin"];
    $tipo_proceso=$_GET["tipo"];
*/
  $fecha= $_GET['fecha_movimiento'];
  $id_sucursal=$_SESSION['id_sucursal'];
  $fecha_mov= ed($fecha);
  $sql_empresa=_query("SELECT * FROM empresa");
  $array_empresa=_fetch_array($sql_empresa);
  $nombre_empresa=$array_empresa['nombre'];
  $telefono=$array_empresa['telefono1'];
  $logo_empresa=$array_empresa['logo'];
	$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
	
	
$sql="SELECT producto.id_producto as id_prod,producto.descripcion,producto.barcode,ajuste_inventario.*
	FROM producto JOIN ajuste_inventario ON producto.id_producto=ajuste_inventario.id_producto
	WHERE ajuste_inventario.fecha='$fecha'
	AND ajuste_inventario.id_sucursal='$id_sucursal'";
	
$result_stock=_query($sql);
$nrows=_num_rows($result_stock);	


$tr_header="
    <table align='center' class='headers_perpage' cellspacing='1' style='width: 88.9%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:40%;text-align:center'>Barcode y Descripci&oacute;n</th>
            <th style='width:10%'>Stock Sistema</th>
            <th style='width:10%'>Conteo F&iacute;sico</th>
            <th style='width:10%'>Diferencia</th>
            <th style='width:25%'>Observaciones</th>
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
                <td align=center style='width:90%;'>REPORTE AJUSTE DE INVENTARIO DE FECHA: <?php echo $fecha_mov;?></td>
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

 
    
   
   
    //$exec_query=_query($sql);

	//$i=0;
	for($i=1;$i<=$nrows;$i++){	
	//$cor=$cor+1;	
	$row=_fetch_array($result_stock);
	$barcode=$row['barcode'];
	if ($barcode!="")
		$barcodeprint=' ['.$barcode.'] ';
	else
		$barcodeprint=' ';
	
	$descripcion=$row['descripcion'];
	$stock_actual=$row['stock_actual'];
	$conteo_fisico=$row['conteo_fisico'];
	$diferencia=$row['diferencia'];
	$fechamov=$row['fecha'];
	$fechaprint=ed($fechamov);
	$observaciones=$row['observaciones'];
	

    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>";
	    echo "<td style='width:5%'>".$i."</td>";
        echo "<td style='width:40%;text-align:left'>".$barcodeprint." ".$descripcion."</td>";
        echo "<td style='width:10%'>".$stock_actual."</td>";
        echo "<td style='width:10%'>".$conteo_fisico."</td>";
        echo "<td style='width:10%'>".$diferencia."</td>";
       // echo "<td style='width:10%'>".$fechaprint."</td>";
        echo "<td style='width:25%;text-align:left'>".$observaciones."</td>";
        echo "</tr>";
        echo "</table>";

	//$i++;
    }
    
    /*
    echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>

        <td style='width:100%;text-align:left;'>Total de: <b>".$label."</b> = ".$total_ingresos_egresos."</td>
           
        </tr>
    </table>";
	*/
   
?> 
</page>
