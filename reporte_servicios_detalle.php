<style type='text/css'>

    table.page_header {width: 100%; margin-top: 20px;margin-bottom: 20px;    border: none; background-color: #FFFFFF; text-align:right;font-family:helvetica,serif;}
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


</style>

<?php
include("_core.php");
  $sql_empresa=_query("SELECT *FROM empresa");
  $array_empresa=_fetch_array($sql_empresa);
  $nombre_empresa=$array_empresa['nombre'];
  $telefono=$array_empresa['telefono1'];

  $fecha1= $_GET['fecha_inicio'];
  $fecha2= $_GET['fecha_fin'];
  $servicio=$_GET['servicio'];

  $sql="SELECT producto.id_producto,producto.descripcion,producto.barcode,producto.presentacion,producto.unidad,producto.marca,producto.color, stock.stock,stock.costo_promedio FROM producto,stock WHERE producto.id_producto=stock.id_producto";

  ?>
<page backtop="10mm" backbottom="15mm" backleft="15mm" backright="10mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    
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


<table cellspacing='0' style='width: 100%; text-align: left;font-size: 14pt; font-family:Times,serif;'>
        <tr>
            <td colspan="3" style="width: 20%; color: #444444;">
                <img style="width: 15%;" src="./img/premier.png">
            </td>
            
        </tr>
        <tr>
            <td align=center style='width:100%;'>REPORTE DE SERVICIOS </td>
        </tr>
        <tr>
            <td align=center style='width:100%;'>REPORTE DE FECHA <?php echo  ED($fecha1)?> HASTA <?php echo  ED($fecha2) ?></td>
        </tr>
        <tr>
            <td align=center style='width:100%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
        </tr>
    </table>
     <br><br>

<?php
if($servicio!="CONSOLIDADO"){
    echo "
    
    <table align='center' cellspacing='1' style='width: 100%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:10%'>N&deg;</th>
            <th style='width:50%;text-align:left';>Nombre y Apellido</th>
            <th style='width:30%;text-align:left';>Servicio</th>
            <th style='width: 10%'>Ctd.</th>
           
           
        </tr>
    </table>";
   
    $servicios=_query("select *from servicio where id_servicio='$servicio'");
    $serv_row=_fetch_array($servicios);
    $descripcion=$serv_row['descripcion'];


    $q="SELECT DISTINCT nombre,empleado.apellido,empleado.id_empleado FROM factura_detalle,empleado,servicio WHERE factura_detalle.id_empleado=empleado.id_empleado and factura_detalle.id_prod_serv=servicio.id_servicio order by nombre";
    $exec_query=_query($q);

	$i=1;
	while($row = mysql_fetch_array($exec_query))
    {
    $id_empleado=$row['id_empleado'];
    $nombre=$row['nombre'];
    $total_servicios="SELECT count(factura_detalle.id_empleado) as total from factura_detalle,empleado,servicio,factura where factura_detalle.id_empleado=empleado.id_empleado and factura_detalle.id_factura=factura.id_factura and factura_detalle.id_prod_serv=servicio.id_servicio and DATE( factura.fecha ) BETWEEN '$fecha1' AND '$fecha2' and factura_detalle.id_prod_serv='$servicio' and factura_detalle.id_empleado='$id_empleado' and factura.anulada=0";
    $exec_query_total=_query($total_servicios);
    $total_serv_empleado=_fetch_array($exec_query_total);
    $total_final=$total_serv_empleado['total'];

    $t_serv="SELECT count(factura_detalle.id_empleado) as total1 from factura_detalle,empleado,servicio,factura where factura_detalle.id_empleado=empleado.id_empleado and factura_detalle.id_factura=factura.id_factura and factura_detalle.id_prod_serv=servicio.id_servicio and DATE( factura.fecha ) BETWEEN '$fecha1' AND '$fecha2' and factura_detalle.id_prod_serv='$servicio' and factura.anulada=0";
    $query_totales=_query($t_serv);
    $total_general_array=_fetch_array($query_totales);
    $total_general=$total_general_array['total1'];

    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>";
	    echo "<td style='width:10%'>$i</td>";
        echo "<td style='width:50%;text-align:left'>$nombre</td>";
        echo "<td style='width:30%;text-align:left'>".utf8_decode($descripcion)."</td>";
        echo "<td style='width:10%'>$total_final</td>
        </tr>";
        echo "</table>";

	$i++;
    }
    
    echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>

        <td style='width:100%;text-align:left;'>Total General de Servicio: <b>".$descripcion."</b> = ".$total_general."</td>
           
        </tr>
    </table>";
 }

 //inicia validar consolidado
if($servicio=="CONSOLIDADO") {
    $t_serv="SELECT count(factura_detalle.id_empleado) as total1 from factura_detalle,empleado,servicio,factura where factura_detalle.id_empleado=empleado.id_empleado and factura_detalle.id_factura=factura.id_factura and factura_detalle.id_prod_serv=servicio.id_servicio and DATE( factura.fecha ) BETWEEN '$fecha1' AND '$fecha2' and factura.anulada=0";
    $query_totales=_query($t_serv);
    $total_general_array=_fetch_array($query_totales);
    $total_general=$total_general_array['total1'];
      
     echo "
    <table align='center' cellspacing='1' style='width: 100%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:10%'>N&deg;</th>
            <th style='width:50%;text-align:left';>Servicios</th>
            <th style='width:30%;text-align:left';>Cantidad</th>
           
        </tr>
    </table>";

    $query_servicios=_query("SELECT servicio.descripcion, servicio.id_servicio FROM servicio");
    $i=1;
    while($row_servicios=_fetch_array($query_servicios))
    {
        $id_servicio=$row_servicios['id_servicio'];
        $nombre_servicio=$row_servicios['descripcion'];

        $query_general=_query("SELECT count(factura_detalle.id_empleado) as total from factura_detalle,empleado,servicio,factura where factura_detalle.id_empleado=empleado.id_empleado and factura_detalle.id_factura=factura.id_factura and factura_detalle.id_prod_serv=servicio.id_servicio and DATE( factura.fecha ) BETWEEN '$fecha1' AND '$fecha2' and factura_detalle.id_prod_serv='$id_servicio' and factura.anulada=0");
        $row_totales=_fetch_array($query_general);
        $total_general_consolidado=$row_totales['total'];

        echo "
        <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
        echo" <tr>";
        echo "<td style='width:10%'>$i</td>";
        echo "<td style='width:50%;text-align:left'>".utf8_decode($nombre_servicio)."</td>";
        echo "<td style='width:30%;text-align:left'>".$total_general_consolidado."</td>";
 
        echo "</tr>";
        echo "</table>";

    $i++;

    }

     echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
    
        <tr>

        <td style='width:100%;text-align:left;'>Total General de Servicios: = ".$total_general."</td>
           
        </tr>
    </table>";
}   

   
?> 
</page>
