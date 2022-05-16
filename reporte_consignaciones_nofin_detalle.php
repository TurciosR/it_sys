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
    table.headers_perpage{margin-top:20px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage.th{text-align:left;}*/
</style>

<?php
include("_core.php");
//Obtener valores por $_GET


$fecha_inicio= $_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];
$fecha_ini= ed($fecha_inicio);
$fecha_fina=ed($fecha_fin);
$id_usuario=$_SESSION["id_usuario"];
$id_sucursal=$_SESSION['id_sucursal'];
$sql_empresa=_query("SELECT * FROM empresa");
$array_empresa=_fetch_array($sql_empresa);
$nombre_empresa=$array_empresa['nombre'];
$telefono=$array_empresa['telefono1'];

$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
$array_sucursal=_fetch_array($sql_sucursal);
$nombre_sucursal=$array_sucursal['descripcion'];

$tr_header="
    <table align='center' class='headers_perpage' cellspacing='1' style='width: 85.5%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:5%'>N&deg;</th>
            <th style='width:40%;text-align:center'>Cliente</th>
            <th style='width:15%;text-align:center'>Consignacion</th>
            <th style='width:20%;text-align:center'>Tot. Producto</th>
            <th style='width:20%;text-align:center'>$ Consignado</th>

        </tr>
    </table>";

  ?>
<page backtop="40mm" backbottom="40mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>

        <table class="page_header" >
            <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="./img/variedades.png">
                </td>

            </tr>
             <tr>
                <td align=center style='width:90%;'><?php echo  $nombre_empresa. " " .$nombre_sucursal?></td>
            </tr>
            <tr>
                <td align=center style='width:90%;'>REPORTE DE CONSIGNACIONES NO FINALIZADAS</td>
            </tr>
			  <tr>
            <td align=center style='width:100%;'>DESDE  <?php echo  ed($fecha_inicio)?> HASTA <?php echo  ed($fecha_fin) ?></td>
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
$totales=0;
///para consignaciones
$total_consignaciones="
SELECT cliente.id_cliente, cliente.dui,cliente.nombre,cliente.apellido,consignacion.id_consignacion,
consignacion.numero_doc, ROUND(SUM(consignacion_detalle.subtotal), 2) as total_consignado,
ROUND(SUM(cantidad), 2) as total_producto
FROM consignacion,consignacion_detalle, cliente
WHERE consignacion.id_consignacion=consignacion_detalle.id_consignacion
AND consignacion_detalle.tipo_prod_serv='PRODUCTO'
AND consignacion.id_cliente=cliente.id_cliente
AND consignacion.anulada=0
AND consignacion.finalizada=0
AND  consignacion.id_sucursal='$id_sucursal'
AND DATE( consignacion.fecha ) BETWEEN '$fecha_inicio' AND '$fecha_fin'
group by  consignacion.id_consignacion
";
$exec_sql_consignaciones_rango=_query($total_consignaciones);
$nrows=_num_rows($exec_sql_consignaciones_rango);
$total_dinero_fin=0;
$total_producto_fin=0;
$n=0;
for($i=0;$i<$nrows;$i++){
	$rows=_fetch_array($exec_sql_consignaciones_rango);
	$id_cliente =$rows['id_cliente'];
	$dui =$rows['dui'];
	$nombre =$rows['nombre'];
	$apellido =$rows['apellido'];
	$id_consignacion =$rows['id_consignacion'];
	$numero_doc=$rows['numero_doc'];
	$total_consignado =$rows['total_consignado'];
	$total_producto =$rows['total_producto'];
	$n=$n+1;
	$total_producto_fin=$total_producto+$total_producto_fin;
	$total_dinero_fin=$total_consignado+$total_dinero_fin;

    echo "
		<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>";
	echo "<td style='width:5%'>".$n."</td>";
	echo "<td style='width:40%;text-align:left'>".$dui." ".$nombre." ".$apellido."</td>";
    echo "<td style='width:15%;text-align:left'>".$numero_doc."</td>";
    echo "<td style='width:20%;text-align:right'> ".$total_producto."</td>";
    echo "<td style='width:20%;text-align:right'>$ ".$total_consignado."</td>";
    echo "</tr>";
    echo "</table>";
  }
	$total_dinero_final=sprintf("%.2f",$total_dinero_fin);
	$total_producto_final=sprintf("%.2f",$total_producto_fin);
    echo "<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
    echo "<tr>";
	echo "<td style='width:60%;text-align:right;font-weight:bold'>"." TOTALES "."</td>";
	echo "<td style='width:20%;text-align:right;font-weight:bold'> ".$total_producto_final."</td>";
    echo "<td style='width:20%;text-align:right;font-weight:bold'>$ ".$total_dinero_final."</td>";
    echo "</tr>";
    echo "</table>";
?>
</page>
