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
    table.headers_perpage{margin-top:38px; margin-left:0px; margin-right:150px;}
    /*table.headers_perpage.th{text-align:left;}*/
</style>

<?php
include("_core.php");
//Obtener valores por $_GET
//permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);
//permiso del script
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin= $_GET['fecha_fin'];
$tipo= $_GET['tipo'];

$fecha_ini= ed($fecha_inicio);
$fecha_fina=ed($fecha_fin);

$sql_empresa=_query("SELECT * FROM empresa");
$array_empresa=_fetch_array($sql_empresa);
$nombre_empresa=$array_empresa['nombre'];
$telefono=$array_empresa['telefono1'];

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

$result=_query($sql);
$nrows=_num_rows($result);

if($tipo=='CONSOLIDADO'){
	$tipo='PRODUCTOS Y SERVICIOS'	;
}


$tr_header="
    <table align='center' class='headers_perpage' cellspacing='1' style='width: 85.5%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:20%'>N&deg;</th>
            <th style='width:40%;text-align:center'>Fecha</th>
            <th style='width:40%;text-align:center'>Ventas Diarias </th>
        </tr>
    </table>";

  ?>
<page backtop="40mm" backbottom="40mm" backleft="15mm" backright="15mm" style="font-size: 12pt" backimgx="center" backimgy="bottom" backimgw="100%">
    <page_header>

        <table class="page_header" >
            <tr>
                <td colspan="3" style="width: 10%; color: #444444;">
                    <img style="width: 10%;" src="./img/premier.png">
                </td>

            </tr>
            <tr>
                <td align=center style='width:90%;'>REPORTE VENTAS DIARIAS DE <?php echo $tipo;?></td>
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
$totales=0;
	for($i=1;$i<=$nrows;$i++){

	$row=_fetch_array($result);
	$fecha=$row['fecha'];
	$total_diario=$row['total_diario'];
	$fechaprint=ed($fecha);


    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>";
	    echo "<td style='width:20%'>".$i."</td>";
        echo "<td style='width:40%;text-align:center'>".$fecha."</td>";
        echo "<td style='width:40%;text-align:center'>$ ".$total_diario."</td>";
        echo "</tr>";

         echo "</table>";
	$totales=$totales+$total_diario;
    }
    echo "<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
    echo "<tr>";
	echo "<td style='width:60%;text-align:right;font-weight:bold'>"." TOTALES "."</td>";
    echo "<td style='width:40%;text-align:center;font-weight:bold'>$ ".$totales."</td>";
    echo "</tr>";
    echo "</table>";


?>
</page>
