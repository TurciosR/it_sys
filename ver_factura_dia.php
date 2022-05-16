<?php
include_once "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$id_factura=$_REQUEST["id_factura"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//$numero_docx=$_REQUEST['numero_doc'];
		//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//$id_sucursal=$_SESSION['id_sucursal'];

	echo "<style type='text/css'>
    #inventable{
    	font-family: 'Open Sans';
    	 font-style: normal;
    	 font-size: small;
		font-weight: 400;
		src: local('Open Sans'), local('OpenSans'), url(fonts/apache/opensans/OpenSans-Regular.ttf) format('truetype'), url(fonts/apache/opensans/OpenSans.woff) format('woff');
    }
    .table thead tr > th.success{
		background-color: #428bca !important;
		color: white !important;
	}
	.table > tfoot > tr > .thick-line {
		border-top: 2px solid;
	}
	</style>";


	$sql="SELECT * FROM producto";
	$result=_query($sql);
	$count=_num_rows($result);

	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	$sql_iva="select iva from empresa";
	$result=_query($sql_iva);
	$row=_fetch_array($result);
	$iva=$row['iva']/100;

	$sql_fact="SELECT factura_dia.*, cliente.nombre,cliente.apellido
	FROM factura_dia JOIN cliente ON factura_dia.id_cliente=cliente.id_cliente
	WHERE id_factura_dia='$id_factura'
	AND factura_dia.id_sucursal='$id_sucursal'
	";
	$result_fact = _query( $sql_fact);
	$count_fact = _num_rows( $result_fact);
	if ($count_fact==0){
		$sql="SELECT *,'Cliente' as nombre, 'General' as apellido
		FROM factura_dia
		WHERE id_factura='$id_factura'
		AND id_sucursal='$id_sucursal'
		";
	}


?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver factura</h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php

						if ($links!='NOT' || $admin=='1' ){
					?>
				<!--table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripcion</th>
						</tr>
					</thead>
					<tbody-->

						<?php
						if ($count_fact > 0) {
									for($i = 0; $i < $count_fact; $i ++) {
										//$row = _fetch_array ( $result_fact, $i );
										$row = _fetch_array ( $result_fact);
										$cliente=$row['nombre']." ".$row['apellido'];
										$numero=$row['numero']. " del d&iacute;a";
										$fecha=$row['fecha'];
										/*
										echo "<tr><td>Id Cliente</td><td><h5 class='text-warning'>".$cliente."</h5></td>";
										echo "<tr><td>Numero Doc</td><td><h5 class='text-danger'>".$row['numero_doc']."</h5></td>";
										*/
							}
						}
						?>
		<!--/div-->


						<!--div class="ibox "-->
						<div>
							<!--load datables estructure html-->
							<header><h4 class="text-danger">Factura No: &nbsp;<?php $fecha1=ed($fecha); echo $numero." ".$fecha1;  ?></h4>

							Cliente:<?php echo $cliente; ?></h4>
							</header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Precio Vta.</th>
										<th class='success'>Cantidad</th>
										<th class='success' >Subtotal</th>

										</tr>
									</thead>
									<tbody>
										<?php
										$sql_fact_det="SELECT factura_dia.id_factura_dia, factura_dia.id_cliente, factura_dia.fecha,  factura_dia.total,
										factura_dia.impresa, factura_dia.id_sucursal,
										factura_detalle_dia.id_factdet_dia,factura_detalle_dia.id_producto, factura_detalle_dia.cantidad,
										factura_detalle_dia.precio_venta, factura_detalle_dia.subtotal
										FROM factura_dia
										JOIN factura_detalle_dia  ON factura_dia.id_factura_dia=factura_detalle_dia.id_factura_dia
										WHERE
										factura_dia.id_factura_dia='$id_factura'
										AND factura_dia.id_sucursal='$id_sucursal'";

										$result_fact_det=_query($sql_fact_det);
										$count_fact_det=_num_rows($result_fact_det);
										for($i=0;$i<$count_fact_det;$i++){
											$row=_fetch_array($result_fact_det);
										/*	$numero_doc=$row['numero_doc'];
											$id_factura=$row['id_factura'];
											*/
											$id_producto=$row['id_producto'];
											$tipo_prod='';
											$impresa=$row['impresa'];
											$cantidad=$row['cantidad'];
											$precio_venta=$row['precio_venta'];
											$subtotal=$row['subtotal'];
											$total=$row['total'];
											//$id_usuario=$row['id_usuario'];
											$total=sprintf("%.2f", $total);

											$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,producto.barcode,
											producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,producto.perecedero,
											producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
											producto.porcentaje_utilidad4,producto.imagen,producto.combo,
											stock.stock,stock.costo_promedio,stock.precio_sugerido
											FROM producto JOIN stock ON producto.id_producto=stock.id_producto
											WHERE producto.id_producto='$id_producto'
											AND stock.id_sucursal='$id_sucursal'
											";
											$stock1=_query($sql1);
											$row1=_fetch_array($stock1);
											$nrow1=_num_rows($stock1);
											$unidades=round($row1['unidad'],2);
											$utilidad_activa=$row1['utilidad_activa'];
											$utilidad_seleccion=$row1['utilidad_seleccion'];
											$id_producto=$row1['id_producto'];
											$descripcion=$row1['descripcion'];
											$barcode=$row1['barcode'];
											$combo=$row1['combo'];
											$perecedero=$row1['perecedero'];
											if($barcode!="")
												$descprod="[".$barcode."] ".$descripcion;
											else
												$descprod=$descripcion;
											if($unidades==0){
												$unidades=1;
											}

											//consultar si es perecedero
											$fecha_caducidad="0000-00-00";
											$stock_fecha=0;
											if($perecedero==1){
												$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
												salida, estado, numero_doc, id_sucursal
												FROM producto_perecedero
												WHERE id_producto='$id_producto'
												AND id_sucursal='$id_sucursal'
												AND entrada>salida
												AND estado='VIGENTE'
												ORDER BY fecha_caducidad";
												$result_perecedero=_query($sql_perecedero);
												$row_perecedero=_fetch_array($result_perecedero);
												$nrow_perecedero=_num_rows($result_perecedero);
												if($nrow_perecedero>0){
													$entrada=$row_perecedero['entrada'];
													$salida=$row_perecedero['salida'];
													$fecha_caducidad=$row_perecedero['fecha_caducidad'];
													$fecha_caducidad=ED($fecha_caducidad);
													$stock_fecha=$entrada-$salida;
												}

												//$descprod.="&nbsp;"."<label class='text-danger'>--> Existencias:".$stock_fecha." Fecha prox. Caducidad: ".$fecha_caducidad."</label>";

											}
											//consultar si es perecedero

											//agregar la descrip de caducidad y fecha
											//agregar el orden de precios de mayor a menor
											$pu1=$row1['porcentaje_utilidad1']/100;
											$pu2=$row1['porcentaje_utilidad2']/100;
											$pu3=$row1['porcentaje_utilidad3']/100;
											$pu4=$row1['porcentaje_utilidad4']/100;

											$cp=$row1['costo_promedio']/$unidades;
											$existencias=$row1['stock'];
											$exento=$row1['exento'];
											$precio_sugerido=$row1['precio_sugerido'];
											$imagen=$row1['imagen'];
											$costos_pu=array($pu1,$pu2,$pu3,$pu4);

											$pv1=$cp+($cp*$pu1) ;
											$pv2=$cp+($cp*$pu2) ;
											$pv3=$cp+($cp*$pu3) ;
											$pv4=$cp+($cp*$pu4) ;

											if ($iva>0 && $exento==0){
												$pv1=$pv1+($pv1*$iva);
												$pv2=$pv2+($pv2*$iva);
												$pv3=$pv3+($pv3*$iva);
												$pv4=$pv4+($pv4*$iva);
											}
											$pv1=round($pv1,2);
											$pv2=round($pv2,2);
											$pv3=round($pv3,2);
											$pv4=round($pv4,2);

											$precios_vta=array($pv4,$pv3,$pv2,$pv1);
											//orden descendente
											rsort($precios_vta);
											if($combo==1){
												if($subtotal==0){
													$combo_chk="<input type='checkbox' id='check_combo' name='check_combo' value='0' checked>";
												}
												else{
													$combo_chk="<input type='checkbox' id='check_combo' name='check_combo' value='0'>";
												}
											}
											else{
												$combo_chk="";
											}

											echo "<tr>";
											echo "<td>".$id_producto."</td>";
											echo "<td>".$descprod."</td>";
											//echo "<td>".$existencias."</td>";

											echo "<td id='pv' class='text-right'>".$precio_venta."</td>";
											echo "<td id='cant1' class='text-right'>".$cantidad."</td>";
											echo "<td id='subtot' class='text-right'>".$subtotal."</td>";
											//echo "<td id='combos' class='text-center'>".$combo_chk."</td>";

											echo "</tr>";
										}
										?>

									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ><strong><?php echo $total; ?></strong></td>
										<!--td class="thick-line"></td>
										<td class="thick-line"></td-->
										</tr>
									</tfoot>
								</table>
								<?php
								list($entero,$decimal)=explode('.',$total);
								$enteros_txt=num2letras($entero);
							$decimales_txt=num2letras($decimal);

							if($entero>1)
								$dolar=" dolares";
							else
								$dolar=" dolar";
							$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
							echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";

								?>


					</section>

						</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
				</div>
		</div>
	</div>


<?php
//include_once ("footer.php");
//echo "<script src='js/funciones/genera_venta.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}


function total_texto(){
	$total=$_REQUEST['total'];
	list($entero,$decimal)=explode('.',$total);
	$enteros_txt=num2letras($entero);
	$decimales_txt=num2letras($decimal);

	if($entero>1)
		$dolar=" dolares";
	else
		$dolar=" dolar";
	$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
	echo $cadena_salida;
}

//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {


	switch ($_REQUEST['process']) {
	case 'formEdit':
		initial();
		break;
	case 'consultar_stock':
		consultar_stock();
		break;
	}

 //}
}
?>
