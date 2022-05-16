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
</style>

<?php
include("_core.php");
  $fecha1=$_GET["fecha_inicio"];
  $fecha2=$_GET["fecha_fin"];
  $id_sucursal=$_GET["sucursal"];
  //$tipo_proceso=$_GET["tipo"];

  $sql_empresa=_query("SELECT *FROM empresa");
  $array_empresa=_fetch_array($sql_empresa);
  $nombre_empresa=$array_empresa['nombre'];
  $telefono=$array_empresa['telefono1'];
  $logo=$array_empresa['logo'];

  $sql_sucursal="SELECT sucursal.descripcion FROM sucursal WHERE sucursal.id_sucursal='$id_sucursal'";
  $result_sucursal=_query($sql_sucursal);
  $row_sucursal=_fetch_array($result_sucursal);
  $nombre_sucursal=$row_sucursal["descripcion"];


  ?>
<page backtop="30mm" backbottom="30mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>

        <table class="page_header" >
            <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="<?php echo $logo?>">
                </td>

            </tr>
            <tr>
                <td align=center style='width:90%;'>REPORTE UTILIDADES NETAS <?php echo $nombre_sucursal?></td>
            </tr>
             <tr>
                <td align=center style='width:90%;'>FECHA: <?php echo "Desde ".ED($fecha1)." Hasta ".ED($fecha2); ?></td>
            </tr>
             <tr>
                <td align=center style='width:90%;'>FECHA DE IMPRESION: <?php echo date('d-m-Y H:i:s');?></td>
            </tr>

        </table>
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


/*
$sql="SELECT factura.fecha,producto.descripcion,producto.id_producto,stock.costo_promedio,
factura_detalle.precio_venta,sucursal.id_sucursal,factura_detalle.cantidad,factura_detalle.id_factura
FROM producto,factura,factura_detalle,stock,sucursal
WHERE factura.id_factura=factura_detalle.id_factura AND producto.id_producto=factura_detalle.id_prod_serv
AND stock.id_producto=factura_detalle.id_prod_serv AND DATE(factura.fecha) BETWEEN '$fecha1' AND '$fecha2'
AND sucursal.id_sucursal=factura_detalle.id_sucursal AND sucursal.id_sucursal='$id_sucursal' AND factura.anulada=0
AND factura_detalle.precio_venta>0 group by producto.id_producto ORDER BY factura_detalle.id_factura ASC";
*/

$sql="SELECT distinct factura.id_factura ,factura_detalle.id_factura_detalle, factura.fecha,producto.descripcion,
producto.id_producto,stock.costo_promedio, factura_detalle.precio_venta,factura_detalle.cantidad,factura_detalle.subtotal
FROM producto,factura,factura_detalle,stock,sucursal
WHERE factura.id_factura=factura_detalle.id_factura
AND producto.id_producto=factura_detalle.id_prod_serv
AND stock.id_producto=factura_detalle.id_prod_serv
AND producto.id_producto= stock.id_producto
AND stock.id_sucursal=sucursal.id_sucursal
AND DATE(factura.fecha) BETWEEN  '$fecha1' AND '$fecha2'
AND sucursal.id_sucursal=factura_detalle.id_sucursal
AND factura.id_sucursal='$id_sucursal'
AND factura.anulada=0
AND factura_detalle.subtotal>0
ORDER BY factura.id_factura,factura_detalle.id_factura_detalle, producto.id_producto ASC ";



$result=_query($sql);
$count=_num_rows($result);


    $sql_costo=_query("SELECT ROUND(SUM(stock.costo_promedio*factura_detalle.cantidad),2) as total_costo_promedio
    FROM producto,factura,factura_detalle,stock,sucursal
    WHERE factura.id_factura=factura_detalle.id_factura
    AND producto.id_producto=factura_detalle.id_prod_serv
    AND stock.id_producto=producto.id_producto
    AND stock.id_producto=factura_detalle.id_prod_serv
    AND stock.id_sucursal=sucursal.id_sucursal
    AND stock.id_sucursal= sucursal.id_sucursal
    AND DATE(factura.fecha) BETWEEN '$fecha1' AND '$fecha2'
    AND sucursal.id_sucursal=factura_detalle.id_sucursal
    AND sucursal.id_sucursal='$id_sucursal'
    AND factura.anulada=0
    AND factura.finalizada=1
    AND factura_detalle.subtotal>0");

    $arreglo_total_costo=_fetch_array($sql_costo);
    $total_costo=$arreglo_total_costo['total_costo_promedio'];


    $sql_ventas=_query("SELECT ROUND(SUM(factura_detalle.subtotal),2) as total_venta
        FROM factura,factura_detalle
        WHERE factura.id_factura=factura_detalle.id_factura
        AND DATE(factura.fecha) BETWEEN '$fecha1' AND '$fecha2'
        AND factura.id_sucursal='$id_sucursal'
        AND factura.anulada=0
        AND factura.finalizada=1
        AND factura_detalle.subtotal>0");

    $arreglo_ventas=_fetch_array($sql_ventas);
    $total_ventas=$arreglo_ventas['total_venta'];


    $sql_combo=_query("SELECT ROUND(SUM(stock.costo_promedio*factura_detalle.cantidad),2) as total_combo
    FROM producto,factura,factura_detalle,stock,sucursal WHERE factura.id_factura=factura_detalle.id_factura
    AND producto.id_producto=factura_detalle.id_prod_serv AND stock.id_producto=factura_detalle.id_prod_serv
    AND DATE(factura.fecha) BETWEEN '$fecha1' AND '$fecha2'
    AND sucursal.id_sucursal=factura_detalle.id_sucursal AND sucursal.id_sucursal='$id_sucursal'
    AND factura.anulada=0 AND factura_detalle.precio_venta=0");

    $arreglo_combo=_fetch_array($sql_combo);
    $total_combo=$arreglo_combo["total_combo"];
    $total_costos_general=$total_costo+$total_combo;

    $utilidad_neta=$total_ventas-$total_costos_general;



    echo "
    <table align='center' cellspacing='1' style='width: 100%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:5%;text-align:left'>ID</th>
            <th style='width:40%;text-align:left'>Descripcion</th>
            <th style='width: 15%'>C. P</th>
            <th style='width: 15%'>P. V</th>
            <th style='width: 10%'>Cant.</th>
            <th style='width: 10%'>ID Fact.</th>
        </tr>
    </table>";


    $exec_query=_query($sql);

    $i=1;
    while($row = _fetch_array($exec_query))
    {

    $id_producto=$row['id_producto'];
    $descripcion=$row['descripcion'];
    $costo_promedio=$row['costo_promedio'];
    $precio_venta=$row['precio_venta'];
    $fecha=$row['fecha'];
    $cantidad=$row['cantidad'];
    $id_factura=$row['id_factura'];



    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>

        <tr>";
        echo "<td style='width:5%'>$i</td>";
        echo "<td style='width:5%;text-align:left'>".$id_producto."</td>";
        echo "<td style='width:40%;text-align:left'>".$descripcion."</td>";
        echo "<td style='width:15%'>$costo_promedio</td>";
        echo "<td style='width:15%'>$precio_venta</td>";
        echo "<td style='width:10%'>$cantidad</td>";
        echo "<td style='width:10%'>$id_factura</td>
        </tr>";
        echo "</table>";

    $i++;
    }


    echo "
    <br><br>
    <table border='0' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
         <tr>
            <td style='width:100%;text-align:left;'><b>Total Ventas</b> = $ ".$total_ventas."</td>
        </tr>
        <tr>
            <td style='width:100%;text-align:left;'><b>Total Productos en Combo</b> = $ ".$total_combo."</td>
        </tr>
         <tr>
            <td style='width:100%;text-align:left;'><b>Total Costos</b> = $ ".$total_costos_general."</td>
        </tr>
        <tr>
            <td style='width:100%;text-align:left;'><b>Utilidad Neta</b> = $ ".$utilidad_neta."</td>
        </tr>
    </table>";


?>
</page>
