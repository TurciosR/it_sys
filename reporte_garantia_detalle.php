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

$numero_doc = $_GET['numero_doc'];
$numero_docx = $numero_doc."_GAR";

$id_sucursal=$_SESSION['id_sucursal'];

$sql_empresa=_query("SELECT * FROM empresa");
$array_empresa=_fetch_array($sql_empresa);
$nombre_empresa=$array_empresa['nombre'];
$telefono=$array_empresa['telefono1'];

$id_usuario=$_SESSION["id_usuario"];
$id_sucursal=$_SESSION['id_sucursal'];

$sql_sucursal=_query("SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'");
	$array_sucursal=_fetch_array($sql_sucursal);
	$nombre_sucursal=$array_sucursal['descripcion'];
//Obtener informacion de tabla garantia
$info_garantia="";
$sql_fact="SELECT * FROM garantia WHERE numero_doc='$numero_docx' and id_sucursal='$id_sucursal' ";
$result_fact=_query($sql_fact);
$row_fact=_fetch_array($result_fact);
$nrows_fact=_num_rows($result_fact);
if($nrows_fact>0){
		$id_proveedor=$row_fact['id_proveedor'];
		$id_garantia = $row_fact['id_garantia'];
		$fecha=$row_fact['fecha'];
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);

		//Datos del proveedor
		$sql="select * from proveedor where id_proveedor='$id_proveedor' ";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombres=$row1['nombre_proveedor'];
		/*
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];
		*/
}


$tr_header="
    <table align='center' class='headers_perpage' cellspacing='1' style='width: 85.5%; border: solid 1px black; background: #A9A9A9; text-align: center; font-size: 11pt; color:#FFFFFF; font-family:helvetica,serif;'>
        <tr>
            <th style='width:10%'>N&deg;</th>
            <th style='width:10%;text-align:center'>Id</th>
            <th style='width:70%;text-align:center'>Barcode y Descripcion</th>
            <th style='width:10%;text-align:center'>Cantidad</th>
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
                <td align=center style='width:90%;'>GARANTIA No. <?php echo $numero_doc;?></td>
            </tr>
			<tr>
                <td align=center style='width:90%;'>PROVEEDOR:  <?php echo  $nombres;?> </td>
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

//Obtener informacion de tabla garantia_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,producto.barcode,garantia_detalle.*
		FROM garantia_detalle JOIN producto ON garantia_detalle.id_prod_serv=producto.id_producto
		WHERE  garantia_detalle.id_garantia='$id_garantia' AND  garantia_detalle.tipo_prod_serv='PRODUCTO'

		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		$n=0;
		$tot_prod=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$barcode =$row_fact_det['barcode'];
			$id_garantia_detalle =$row_fact_det['id_garantia_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			$n=$n+1;
			//linea a linea

			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$tot_prod=$cantidad+$tot_prod;
			$tot_prod_print=sprintf("%.2f",$tot_prod);

			//$info_garantia.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$cuantos=$cuantos+1;




    echo "
    <table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>
        <tr>";
	    echo "<td style='width:10%'>".$n."</td>";
	    echo "<td style='width:10%'>".$id_producto."</td>";
        echo "<td style='width:70%;text-align:left'>".$barcode." ".$descripcion."</td>";
        echo "<td style='width:10%;text-align:right'> ".$cantidad."</td>";
      /* echo "<td style='width:20%;text-align:right'>$ ".$precio_unit."</td>";
        echo "<td style='width:20%;text-align:right'> $ ".$subtotal."</td>";*/
        echo "</tr>";

         echo "</table>";
	$totales=$totales+$subtotal;
    }
    $total_final=sprintf("%.2f",$totales);
    echo "<table border='0.5px' align='center' cellspacing='0'  style='width: 100%; border:none; text-align: center; font-size: 11pt; color:#000; font-family:helvetica,serif;'>";
    echo "<tr>";
	echo "<td style='width:75%;text-align:right;font-weight:bold'>"." TOTALES "."</td>";
    echo "<td style='width:25%;text-align:right;font-weight:bold'>".$tot_prod_print."</td>";
    echo "</tr>";
    echo "</table>";


?>
</page>
