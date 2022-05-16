<?php
include_once "_core.php";
include ('num2letras.php');
include("escpos-php/Escpos.php");
include ('facturacion_funcion_imprimir.php');
function initial() {
	$title='Editar pedido';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_pedido= $_REQUEST['id_pedido'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
?>

    <div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
    </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
           <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
						<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						echo"
                        <div class='ibox-title'>
                            <h5>$title</h5>
                        </div>";
                        ?>
                        <!-- div para agregar productos a la consigna -->
						<div class="ibox-content">
							<div class="row">
							<div id='form_datos_cliente'>

							<?php
							//Traer sumatoria de facturas hechas con este id pedido
							$sql_facturado="SELECT sum(total) as total_consignado_fact
							FROM factura WHERE id_pedido='$id_pedido'
							AND id_sucursal='$id_sucursal'
							";
							$result_facturado=_query($sql_facturado);
							$n_facturado=_num_rows($result_facturado);
							$rows_facturado=_fetch_array($result_facturado);

							if ($n_facturado>0){
								 $abono_consignado_fact=$rows_facturado['total_consignado_fact'];
							 }
							 else
								 $abono_consignado_fact=0;
							 $abono_consignado_format=sprintf("%.2f",  $abono_consignado_fact);


							$sql="SELECT pedido.*, cliente.nombre,cliente.apellido,
							usuario.nombre as nomempleado
							FROM pedido
							JOIN cliente ON pedido.id_cliente=cliente.id_cliente
							JOIN usuario ON pedido.id_usuario=usuario.id_usuario
							WHERE finalizada=0
							AND id_pedido='$id_pedido'
							AND pedido.id_sucursal='$id_sucursal'";
							//JOIN empleado ON pedido.id_empleado=empleado.id_empleado
							/*
							SELECT pedido.*, cliente.nombre,cliente.apellido
							FROM pedido JOIN cliente ON pedido.id_cliente=cliente.id_cliente
							WHERE finalizada=0
							 */
							$result=_query($sql);
							$count1=_num_rows($result);
						if ($count1>0){
							$rows=_fetch_array($result);
							$numero_doc=$rows['numero_doc'];
							$id_cliente=$rows['id_cliente'];
							$nombre_cliente=$rows['nombre']." ".$rows['apellido'];
							$nombre_empleado=$rows['nomempleado'];
							$finalizada=$rows['finalizada'];
							$fecha=$rows['fecha'];
							$fecha_entrega=$rows['fecha_entrega'];
							$lugar_entrega=$rows['lugar_entrega'];
							$observaciones=$rows['observaciones'];

							$total_consignado=$rows['total'];
							$total_consignado_format=sprintf("%.2f",$total_consignado);
							//SALDO PENDIENTE
							$saldo_pendiente=0;
							$saldo_pendiente=$total_consignado-$abono_consignado_fact;
							$saldo_pendiente_format=sprintf("%.2f",$saldo_pendiente);
							$fecha_actual=date("Y-m-d");
							echo "<div class='col-lg-6' id='mostrar_numero_doc'>";
							echo "<div class='form-group has-info single-line'><label>Número de Pedido</label> <input type='text'  class='form-control' id='numero_doc' name='numero_doc' value='$numero_doc' readOnly></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha'></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
  						echo "<div class='form-group has-info single-line'><label>Fecha de Entrega:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha1' name='fecha1' value='$fecha_entrega'></div>";
  						echo "</div>";//<div class='col-md-4'>"
  						echo "<div class='col-lg-6'>";
  						echo "<div class='form-group has-info single-line'><label>Lugar de Entrega:</label> <input type='text' placeholder='lugar de entrega' class='form-control' id='lugar_entrega' name='lugar_entrega' value='$lugar_entrega''></div>";
  						echo "</div>";//<div class='col-md-4'>"

							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Cliente:</label><input type='text' value='$nombre_cliente' class='form-control' readOnly></div>";
							echo "</div>";
							echo "<div class='col-lg-6'>";
							echo "<div class='form-group has-info single-line'><label>Vendedor:</label><input type='text' value='$nombre_empleado' class='form-control' readOnly></div>";
							echo "</div>";
						?>

						</div>

					</div>  <!--div class="row"-->
					<!--div class="ibox "-->
						<div class="ibox-content">
							<header><h4 class="text-navy">BUSCAR PRODUCTO o SERVICIO</h4></header>
							<div class="row" id='buscador'>

								<div class="col-lg-6">
									<div class="widget style1 text-center">
										<div class='form-group has-info single-line'><label>Buscar Producto o Servicio</label>
											<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto o servicio a facturar"  data-provide="typeahead">
										</div>
									</div>
								</div>
								<!--Widgwt imagen-->
								<div class="col-xs-4">
									<div class="widget style1 gray-bg text-center">
										<div class="m-b-sm" id='imagen'>
											<img alt="image" class="img-circle" src="img/productos/white.png" width="200px" height="100px" border='1'>
											<p class="font-bold">Imagen Producto</p>
										</div>
									</div>
								</div>
								<!--Fin Widgwt imagen-->
							</div><!--div class="row" id='buscador'-->
						</div><!--div class="ibox-content"-->
						<!--/div--><!--div class="ibox "-->
                        <!--
                         <div class="row">

                        -->



					<div class="ibox-content">
					<!--load datables estructure html-->

						<header><h4 class="text-navy">PEDIDO</h4>	</header>
						<div class="row">
						<div class="col-md-4"><h5 class="text-warning"> TOTAL PEDIDO :$ <?php echo $total_consignado_format;  ?></h5></div>
						<div class="col-md-4"><h5 class="text-success"> ABONADO: $ <?php echo $abono_consignado_format;  ?></h5></div>
						<div class="col-md-4"><h5 class="text-danger">SALDO PENDIENTE: $ <?php echo $saldo_pendiente_format;  ?></h5></div>
					</div>

					<section>
					<div class="table-responsive m-t">
						<table class="table  table-bordered table-condensed" id="inventable">
							<thead>
								<tr>
									<th>Id</th>
									<th>Nombre</th>
									<th>Stock</th>
									<th>Precios</th>
									<th>Precio Vta.</th>
									<th>Cant Pedido</th>
									<th>Subt Facturar</th>
									<th>Combo</th>
									<th>Acción</th>
								</tr>
							</thead>
						<tbody>
					<?php
		if ($finalizada==0){
	     //Datos de pedido y pedido_detalle

		$sql_fact_det="SELECT  producto.id_producto,stock.stock, producto.descripcion,pedido_detalle.*
		FROM pedido_detalle
		JOIN producto ON pedido_detalle.id_prod_serv=producto.id_producto
		JOIN stock ON pedido_detalle.id_prod_serv=stock.id_producto
		WHERE  pedido_detalle.id_pedido='$id_pedido'
		AND  pedido_detalle.tipo_prod_serv='PRODUCTO'
		AND pedido_detalle.id_sucursal='$id_sucursal'
	    AND pedido_detalle.id_sucursal=stock.id_sucursal
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_cant_consigna=0;
		$total_dinero_consigna=0;
		$total_cant_facturado_ante=0;
		$dev_stock=0;
		$total_dev_stock=0;
		$filas=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$stock =$row_fact_det['stock'];
			$id_pedido_detalle =$row_fact_det['id_pedido_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$cant_facturado =$row_fact_det['cant_facturado'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
			$dev_stock=	$cantidad-$cant_facturado;
			$total_cant_consigna=$cantidad+$total_cant_consigna;
			$total_dinero_consigna=$subt+$total_dinero_consigna;
			$total_cant_facturado_ante=$cant_facturado+$total_cant_facturado_ante;
			$total_dev_stock=$total_dev_stock+$dev_stock;

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
											$descprod="[".$barcode."] ".$descripcion."(".$tipo_prod.")";

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

												$descprod.="&nbsp;"."<label class='text-danger'>--> Existencias:".$stock_fecha." Fecha prox. Caducidad: ".$fecha_caducidad."</label>";

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


											//orden descendente
											//$precios_vta0=array($pv4,$pv3,$pv2,$pv1);


											$precios_vta=array($pv4,$pv3,$pv2,$pv1);
											rsort($precios_vta);
											$precios_vta0=array($pv4,$pv3,$pv2,$pv1);
											rsort($precios_vta0);
											//$pv_minimo = array_pop($precios_vta0);
											$pv_minimo = end($precios_vta);

			$filas=$filas+1;
			?>
			<tr id="<?php echo $filas ?>">
		    <?php
		    echo "<td>$id_producto</td>";
		    echo "<td>$descripcion</td>";
			 echo "<td id='cant_stock'>$stock</td>";
		   	echo "<td>";
											echo "<select name='select_precios' id='select_precios".$filas."'  class='form-control'>";
												foreach ($precios_vta as $pv){
													echo "<option value='$pv'>$pv</option> ";
												}
												echo "</select>";
											echo "</td>";
		    echo "<td><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='".$pv_minimo."'><div class='col-xs-2'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='".$precio_venta."' style='width:80px;'></div></td>";
		   // echo "<td>$precio_venta</td>";
		    echo "<td><input type='text'  class='form-control decimal'  id='cant' value='$cantidad'></td>";
		    echo "<td id='subtot'>$subt</td>";
		  //   echo "<td><input type='text'  class='form-control decimal' id='cant_fact'></td>";
		  //  echo "<td id='dev_stock'>$dev_stock</td>";
		  //  echo "<td id='cant_facturado_ante'>$cant_facturado</td>";


		   if ($combo=='1'){
				if ($precio_venta==0)
					$combo_chk="<input type='checkbox' class='checkbox' id='check_combo' checked value='1' />";
				else
					$combo_chk="<input type='checkbox' class='checkbox' id='check_combo'  value='0' />";
				echo  "<td id='combos'  class='text-center'>".$combo_chk."</td>";
			}
			else {
				echo  "<td id='nocombos'></td>";
			}
			echo  "<td class='Delete'><a><i class='fa fa-times-circle'></i></a></td>";




		   // echo "<td id='subtot_fact'>0</td>";
		    echo "</tr>";


		 }
	 } //if ($finalizada==0){
	 $total_dinero_consigna=sprintf('%.2f',$total_dinero_consigna);
	 list($entero,$decimal)=explode('.',$total_dinero_consigna);
	$enteros_txt=num2letras($entero);
	$decimales_txt=num2letras($decimal);

	if($entero>1)
		$dolar=" dolares";
	else
		$dolar=" dolar";
	$cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";

		?>
							</tbody>
							<tfoot>
									<tr>
									<td></td>
									<td><strong>TOTALES:</strong></td>
									<td></td>
									<?php
									echo "<td id='total_cant_consigna'>$total_cant_consigna</td>";
									echo "<td id='total_dinero_consigna'>$total_dinero_consigna</td>";
									?>
									<td id='total_cant_fact'></td>
									<td  class="thick-line text-right" id='total_dinero' ><?php echo $total_dinero_consigna; ?></td>
									<td id='total_cant_facturado_ante'><?php echo $total_cant_facturado_ante; ?></td>
									<td id='total_dinero_fact'></td>

									</tr>
								</tfoot>
						</table>

							<div class="well m-t"  id='totaltexto'><?php echo $cadena_salida;?></div>
							<input type='hidden' name='totalpedido' id='totalpedido' value='0'>
							<input type='hidden' name='id_cliente' id='id_cliente'  value='<?php echo $id_cliente;?>'>
							<input type='hidden' name='facturacion' id='facturacion' value='<?php echo $filename;?>'>
						</div><!--div class="table-responsive m-t"-->
					</section>
					<div class="row">
					<div class="col-md-8">
					<div class="form-group has-info ">
						<label>Observaciones</label>
						<textarea placeholder="Observaciones"  class="form-control" id="observacion" name="observacion" rows='4'><?php echo $observaciones;?></textarea>
						</div>
					</div>
					</div>
                    <div class="title-action" id='botones'>
						<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
                    </div>
                </div>
			<?php
				}
					else{
							$saldo_pendiente=0;
							$total_consignado=0;
							$total_consignado_fact=0;
							$finalizada=1;

							echo " <div class='col-sm-4'> pedido FINALIZADA</div>";

					}
				?>

                </div><!--div class='ibox-content'-->
                <!--/div><!--div class='ibox'-->

            <!--/div-->
 	<!-- Modal -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-sm">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
				</div>
			<div class="modal-body">
				<div class="wrapper wrapper-content  animated fadeInRight">
					<div class="row" ><label>Factura No:</label><div id='fact_num'></div> </div>
					<div class="row">
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label><h4 class='text-navy'>Facturado $ </h5></label><input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
                                  </div>
								</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>Efectivo $</label><input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
                            </div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>Cambio $</label><input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
                            </div>
						</div>
					</div>
				</div>
			</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnPrintFact"><i class="fa fa-print"></i> Imprimir</button>
					<!--button type="button" class="btn btn-primary" id="btnEsc">Salir</button-->
					<button type="button"  class="btn btn-danger" id="btnEsc" ><i class="fa fa-stop"></i> Salir</button>

				</div>
			</div>
		</div>
	</div>

 	</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->


<?php
include_once ("footer.php");

//echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
//echo "<script src='js/plugins/keynavigator/keynavigator.js'></script>";
echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
echo "<script src='js/funciones/editar_pedido.js'></script>";

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id_usuario=$_SESSION["id_usuario"];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$numero_doc = $_POST['numero_doc'];
	$total_venta = $_POST['total_ventas'];
	//detalles extra del pedido
	$lugar_entrega = $_POST['lugar_entrega'];
	$fecha_entrega = $_POST['fecha_entrega'];
  $observacion=$_POST['observacion'];
	//totales agregados para validar factura en pedido
	$total_consigna=$_POST['total_cant_consigna'];
	$total_cant_fact= $_POST['total_cant_fact'];
	$total_cant_facturado_ante= $_POST['total_cant_facturado_ante'];
	$total_fact_actual_ante=$total_cant_fact+$total_cant_facturado_ante;

	$tipo_entrada_salida='FACTURACION POR pedido No.'.$numero_doc;

	$sql_user="select * from usuario where id_usuario='$id_usuario'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$_SESSION['id_sucursal'];
	//crear tabla temporal para insertar solo los valores que no existen
	#Crear tabla temporal
	$tmp_tbl='CREATE TEMPORARY TABLE IF NOT EXISTS tmp_pedido  LIKE pedido_detalle';
	$result0=_query($tmp_tbl);
	if ($cuantos>0){
	//select a la tabla cuenta
	$sql="SELECT * FROM pedido
	WHERE finalizada=0
		AND numero_doc='$numero_doc'
		AND id_sucursal='$id_sucursal'
	";
	$result=_query($sql);
	$rows=_fetch_array($result);
	$id_pedido=$rows['id_pedido'];
	$id_empleado=$rows['id_empleado'];
	$nrows=_num_rows($result);
	$cuenta_existe=false;
	if($nrows>0){
		_begin();
		$table3='pedido';
		$form_data3 = array(
						'total' => $total_venta,
						'fecha_entrega' => $fecha_entrega,
						'lugar_entrega' => $lugar_entrega,
						'observaciones' => $observacion
						);
		$where_clause3="WHERE numero_doc='$numero_doc' AND id_sucursal='$id_sucursal'";
		$insertar3 = _update($table3,$form_data3, $where_clause3 );

		$listadatos=explode('#',$stringdatos);
		for ($i=0;$i<$cuantos ;$i++){
			//	1|222|0.2 X mt2|0.2|12|2.40#
			list($id,$tipoprod,$precio,$cantidad,$subtotal)=explode('|',$listadatos[$i]);
			//id_cuenta, id_catalogo, costo_unidad, cantidad,

			$sql0="SELECT id_producto FROM producto WHERE id_producto='$id'";
			$result0=_query($sql0);
			$row=_fetch_array($result0);
			$nrow_cat=_num_rows($result0);

			$sql1="SELECT * FROM pedido_detalle
			WHERE id_pedido='$id_pedido'
			AND id_prod_serv='$id'";
			$result1=_query($sql1);
			$row1=_fetch_array($result1);
			$nrows1=_num_rows($result1);


			$table2 = 'pedido_detalle';
			$form_data2 = array(
			'id_pedido'=>$id_pedido,
			'id_empleado'=>$id_empleado,
			'id_prod_serv' => $id,
			'id_sucursal' =>$id_sucursal,
			'precio_venta'=> $precio,
			'cantidad'=> $cantidad,
			'subtotal'=>$subtotal,
			'tipo_prod_serv'=>'PRODUCTO'
			);
			if($nrow_cat>0 && $nrows1==0){
				$updates2 = _insert( $table2 , $form_data2 );
			}
			if($nrow_cat>0 && $nrows1>0){
				$where_clause=" WHERE id_prod_serv='$id'  AND id_pedido='$id_pedido'";
				$updates2 = _update( $table2 , $form_data2,$where_clause );
			}
			$table4 = 'tmp_pedido';
			$updates4 = _insert( $table4 , $form_data2 );

		}
		//Delete the tables that donot exist any more int the `otherTbl`
			$where_clause1=" WHERE pedido_detalle.id_pedido='$id_pedido' AND pedido_detalle.id_prod_serv  NOT IN (SELECT id_prod_serv FROM tmp_pedido)";
			$delete1 = _delete( $table2 ,$where_clause1);
			$drop1=" DROP TABLE tmp_pedido";
			$resultx=_query($drop1);
	}
	else{
		$cuenta_existe=true;
		$updates4 = false;
		$updates2 = false;
	}
}

	//posteriormente actualizar los valores de los registros q se necesiten


	if($updates4 && $updates2 && $insertar3){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='pedido Numero: <strong>'.$numero_doc.'</strong>  finalizada con Exito !';
					$xdatos['process']='insert';
					$xdatos['insertados']=" upd1 :".$updates1." upd2:".$updates2." upd3:".$insertar3 ;
				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
					$xdatos['insertados']="cual falla upd1 :".$updates1." upd2:".$updates2." upd3:".$insertar3 ;
				}

		echo json_encode($xdatos);

}
/*
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	if ($tipo =='PRODUCTO'){

		$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto' AND stock.id_sucursal='$id_sucursal'";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		$unidades=round($row1['unidad'],2);
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$pu1=$row1['porcentaje_utilidad1'];
		$pu2=$row1['porcentaje_utilidad2'];
		$pu3=$row1['porcentaje_utilidad3'];
		$pu4=$row1['porcentaje_utilidad4'];
		$cp=$row1['costo_promedio'];
		$existencias=$row1['stock'];
	    $precio_sugerido=$row1['precio_sugerido'];
		$imagen=$row1['imagen'];
		//$costos_pu=$pu1."|".$pu2."|".$pu3."|".$pu4."|".$cp."|";
		$costos_pu=array($pu1,$pu2,$pu3,$pu4);
		$precio_venta=0.0;

		 if( $utilidad_activa==true || $utilidad_activa==1){
			 switch ($utilidad_seleccion) {
			case 1:
				$precio_venta=$cp*(1+$pu1/100);
				//$precio_venta=$cp*(1+2.5/100);
				break;
			case 2:
				$precio_venta=$cp*(1+$pu2/100);
				break;
			case 3:
				$precio_venta=$cp*(1+$pu3/100);
				break;
			case 4:
				$precio_venta=$cp*(1+$pu4/100);
				break;
			 }//switch ($utilidad_seleccion]) {
		}//if( $utilidad_activa==true){
		else{
			$precio_venta=$precio_sugerido;
		}
		if ($unidades>1){
			//Precio para validar si tiene unidades mayores que 1 en producto
			$precio_venta=$precio_venta/$unidades;
		}
	}
	else{ //si es servicio traemos los datos de la tabla servicio

		$sql1="select * from servicio where id_servicio='$id_producto'";
			 $stock1=_query($sql1);
			 $row1=_fetch_array($stock1);
			 $nrow1=_num_rows($stock1);

			 $precio_venta=$row1['precio'];
			 $existencias='1';
			 $costos_pu=$precio_venta;
			 $unidades='1';
	}
		 $precio_venta=round($precio_venta,2);
		$xdatos['existencias'] = $existencias;
		$xdatos['precio_venta'] = $precio_venta;
		$xdatos['costos_pu'] = $costos_pu;
		$xdatos['costo_prom'] = $cp;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
	echo json_encode($xdatos); //Return the JSON Array

}
*/
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];

	$iva=0;
	$sql_iva="select iva from empresa";
	$result=_query($sql_iva);
	$row=_fetch_array($result);
	$iva=$row['iva']/100;

	if ($tipo =='PRODUCTO'){

	$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,producto.combo,producto.perecedero,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto'
		AND stock.id_sucursal='$id_sucursal'
		";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		if ($nrow1>0){
		$unidades=round($row1['unidad'],2);
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$perecedero=$row1['perecedero'];

		$pu1=$row1['porcentaje_utilidad1']/100;
		$pu2=$row1['porcentaje_utilidad2']/100;
		$pu3=$row1['porcentaje_utilidad3']/100;
		$pu4=$row1['porcentaje_utilidad4']/100;
		$combo=$row1['combo'];
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
		$precio_venta=0.0;

		 if( $utilidad_activa==true || $utilidad_activa==1){
			switch ($utilidad_seleccion) {
			case 1:
				$precio_venta=$cp*(1+$pu1/100);
				break;
			case 2:
				$precio_venta=$cp*(1+$pu2/100);
				break;
			case 3:
				$precio_venta=$cp*(1+$pu3/100);
				break;
			case 4:
				$precio_venta=$cp*(1+$pu4/100);
				break;
			 }//switch ($utilidad_seleccion]) {
		}//if( $utilidad_activa==true){
		else{
			$precio_venta=$precio_sugerido;
		}

		//consultar si es perecedero
		$fecha_caducidad="0000-00-00";
		$stock_fecha=0;
		if($perecedero==1){
			$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
			salida, estado, numero_doc, id_sucursal FROM producto_perecedero
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

		}
		}
		//si no hay stock devuelve cero a todos los valores !!!
		if ($nrow1==0){
		$existencias=0;
		$precio_venta=0;
		$costos_pu=array(0,0,0,0);
		$precios_vta=array(0,0,0,0);
		$cp=0;
		$iva=0;
		$unidades=0;
		$imagen='';
	    $combo=0;
		$fecha_caducidad='0000-00-00';
		$stock_fecha=0;

		}
	}
	else{ //si es servicio traemos los datos de la tabla servicio

		$sql1="select * from servicio where id_servicio='$id_producto'";
			 $stock1=_query($sql1);
			 $row1=_fetch_array($stock1);
			 $nrow1=_num_rows($stock1);

			 $precio_venta=$row1['precio'];
			 $existencias='1';
			 $costos_pu=$precio_venta;
			 $unidades='1';
	}
		$precio_venta=round($precio_venta,2);
		$xdatos['existencias'] = $existencias;
		$xdatos['precio_venta'] = $precio_venta;
		$xdatos['costos_pu'] = $costos_pu;
		$xdatos['costo_prom'] = $cp;
		$xdatos['precios_vta'] = $precios_vta;
		$xdatos['iva'] = $iva;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
		$xdatos['combo'] = $combo;
		$xdatos['fecha_caducidad'] = $fecha_caducidad;
		$xdatos['stock_fecha'] =$stock_fecha;
	echo json_encode($xdatos); //Return the JSON Array
}


function mostrar_datos_cliente(){
	$id=$_REQUEST['id'];
	$sql="select * from cliente where id_cliente='$id'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$direccion=htmlentities($direccion);
	echo " <div class='col-lg-6'>";
	echo "<div class='form-group has-info single-line'><label>Dui</label> <input type='text' placeholder='DUI' class='form-control' id='dui' name='dui' value='$dui' ></div>";
	echo "</div>";
	echo " <div class='col-lg-6'>";
	echo "<div class='form-group has-info single-line'><label>Nit:</label> <input type='text' placeholder='NIT' class='form-control' id='nit' name='nit' value='$nit'></div>";
	echo "</div>";
	echo " <div class='col-lg-12'>";
	echo "<div class='form-group has-info single-line'><label>Direccion</label> <input type='text' placeholder='Direcci&oacute;n' class='form-control' id='direccion' name='direccion' value='$direccion'></div>";
	echo "</div>";

}

function cargar_empleados(){
	echo"<option value='-1'>Mostrador</option>  ";
	$qempleado=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_empleado=_fetch_array($qempleado)){
		$id_empleado=$row_empleado['id_empleado'];
		$nombres=$row_empleado['nombre']." ".$row_empleado['apellido'];
		echo "<option value='$id_empleado'>$nombres</option> ";
	}
}

function cargar_precios(){
	//echo"<option value='-1'>Mostrador</option>  ";
	$q_precios=_query('SELECT * FROM empleado ORDER BY apellido');
	while($row_precios=_fetch_array($q_precios)){
		$id_precios=$row_precios['id_precios'];
		$nombres=$row_precios['nombre']." ".$row_precios['apellido'];
		echo "<option value='$id_precios'>$nombres</option> ";
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

function print1(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];

		$tipo_entrada_salida='pedido';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$total_venta = $_POST['total_ventas'];

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	$Fecha=date("d-m-Y");
	$Hora=date("H:i");

	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$nombres=$row1['nombre']." ".$row1['apellido'];
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_pedido="";


	$numfact=espacios_izq($numero_doc,81);
	//Columnas y posiciones base
	$base1=7;
	$col0=15;
	$col1=6;
	$col2=4;
	$col3=15;
	$col4=12;
	$sp1=$base1;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_enc2=espacios_izq(" ",3);
	$esp_init2=espacios_izq(" ",23);
	$nombre_ape=texto_espacios($nombres,60);
	$dir_txt=texto_espacios($direccion,60);
	$total_final=0;
	for($j=0;$j<2;$j++){
		$info_pedido.="\n";
	}
	$info_pedido.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
		$info_pedido.="\n";
	}
	$info_pedido.=$esp_init2.$nombre_ape.$esp_enc2.$fecha_movimiento."\n";
	$info_pedido.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
	for($j=0;$j<3;$j++){
		$info_pedido.="\n";
	}
	$listadatos=explode('#',$stringdatos);
	$lineas=6;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
			$descrip=texto_espacios($descripcion,63);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}

			$info_pedido.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_pedido.="\n";
		}
	}
	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
	$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$info_pedido.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_pedido.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_pedido.="\n";
	$info_pedido.=$esp_init.$esp." $ ".$total_value."\n";
	$nreg_encode['pedidor'] = $info_pedido;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}

function print2(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
	if ($id=='1'){
		$tipo_entrada_salida='pedido CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_COF";
		$id_cliente=$_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];
	}
	if ($id=='2'){
		$tipo_entrada_salida='pedido CREDITO FISCAL';
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CCF";
		$total_venta = $_POST['total_ventas'];
	}

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	//$Fecha=date("d-m-Y");
	//$Hora=date("H:i");

	$sql="select * from cliente where id_cliente='$id_cliente'";
	$result= _query($sql);
	$row1=_fetch_array($result);
	$nrow1=_num_rows($result);
	$nombres=$row1['nombre']." ".$row1['apellido'];
	$dui=$row1['dui'];
	$nit=$row1['nit'];
	$direccion=$row1['direccion'];
	$info_pedido="";


	$numfact=espacios_izq($numero_doc,65);
	$base1=7;
	//Columnas y posiciones base
	$col0=17;
	$col1=7;
	$col2=3;
	$col3=12;
	$col4=12;
	$sp1=$base1;
	$sp=espacios_izq(" ",$sp1);
	$sp2=espacios_izq(" ",12);
	$esp_init=espacios_izq(" ",$col0);
	$esp_init2=espacios_izq(" ",23);
	$nombre_ape=texto_espacios($nombres,56);
	$dir_txt=texto_espacios($direccion,56);
	$total_final=0;
	//$num_fact=texto_espacios($numero_doc,60);
	for($j=0;$j<2;$j++){
			$info_pedido.="\n";
	}
	$info_pedido.=$esp_init.$numfact."\n";
	for($j=0;$j<2;$j++){
			$info_pedido.="\n";
	}
	$info_pedido.=$esp_init2.$nombre_ape.$fecha_movimiento."\n";
	$info_pedido.=$esp_init2.$dir_txt.$dui."\n";
	for($j=0;$j<2;$j++){
			$info_pedido.="\n";
	}
	$listadatos=explode('#',$stringdatos);
	$lineas=8;
	$salto_linea=$lineas-$cuantos;
	if ($cuantos<=$lineas){
		for ($i=0;$i<$cuantos ;$i++){
			list($id_prod_serv,$descripcion,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
			$descrip=texto_espacios($descripcion,55);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}

			$info_pedido.=$esp_init.$cantidad."  ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
		}
		for($j=0;$j<$salto_linea;$j++){
			$info_pedido.="\n";
		}
	}

	list($entero,$decimal)=explode('.',$total_venta);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
		$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,65);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total_final);
	$esp_init2=espacios_izq(" ",25);
	$info_pedido.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_pedido.="\n";
	$info_pedido.=$esp_init.$esp." $ ".$total_final."\n";
	$nreg_encode['pedidor'] = $info_pedido;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);

}
function reimprimir() {
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = $_POST['id'];
		$tipo_entrada_salida='pedido CONSUMIDOR';
		$fecha_movimiento= $_POST['fecha_movimiento'];
		$numero_doc = $_POST['numero_doc'];
		$numero_docx = $numero_doc."_CSN";
		$id_cliente= $_POST['id_cliente'];
		$total_venta = $_POST['total_ventas'];


	//$id_pedido = $_REQUEST['id_pedido'];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	//Obtener informacion de tabla pedido
	$info_pedido="";
	$sql_fact="SELECT * FROM pedido WHERE numero_doc='$numero_docx'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$id_pedido = $row_fact['id_pedido'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

		$len_numero_doc=strlen($numero_doc)-4;
		$num_fact=substr($numero_doc,0,$len_numero_doc);
		$tipo_fact=substr($numero_doc,$len_numero_doc,4);
		$numfact=espacios_izq($num_fact,81);

		//Datos del Cliente
		$sql="select * from cliente where id_cliente='$id_cliente'";
		$result= _query($sql);
		$row1=_fetch_array($result);
		$nrow1=_num_rows($result);
		$nombres=$row1['nombre']." ".$row1['apellido'];
		$dui=$row1['dui'];
		$nit=$row1['nit'];
		$direccion=$row1['direccion'];

		//Columnas y posiciones base
		$base1=7;
		$col0=15;
		$col1=6;
		$col2=4;
		$col3=15;
		$col4=12;
		$sp1=$base1;
		$sp=espacios_izq(" ",$sp1);
		$sp2=espacios_izq(" ",12);
		$esp_init=espacios_izq(" ",$col0);
		$esp_enc2=espacios_izq(" ",3);
		$esp_init2=espacios_izq(" ",23);
		$nombre_ape=texto_espacios($nombres,60);
		$dir_txt=texto_espacios($direccion,60);
		$total_final=0;
		for($j=0;$j<2;$j++){
			$info_pedido.="\n";
		}
		$info_pedido.=$esp_init.$numfact."\n";
		for($j=0;$j<2;$j++){
			$info_pedido.="\n";
		}
		$info_pedido.=$esp_init2.$nombre_ape.$esp_enc2.$fecha."\n";
		$info_pedido.=$esp_init2.$dir_txt.$esp_enc2.$dui."\n";
		for($j=0;$j<3;$j++){
			$info_pedido.="\n";
		}

		//Obtener informacion de tabla pedido_detalle y producto o servicio
		$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,pedido_detalle.*
		FROM pedido_detalle JOIN producto ON pedido_detalle.id_prod_serv=producto.id_producto
		WHERE  pedido_detalle.id_pedido='$id_pedido' AND  pedido_detalle.tipo_prod_serv='PRODUCTO'
		UNION ALL
		SELECT  servicio.id_servicio, servicio.descripcion,pedido_detalle.*
		FROM pedido_detalle JOIN servicio ON pedido_detalle.id_prod_serv=servicio.id_servicio
		WHERE  pedido_detalle.id_pedido='$id_pedido' AND  pedido_detalle.tipo_prod_serv='SERVICIO'
		";

		$result_fact_det=_query($sql_fact_det);
		$nrows_fact_det=_num_rows($result_fact_det);
		$total_final=0;
		$lineas=6;
		$cuantos=0;
		for($i=0;$i<$nrows_fact_det;$i++){
			$row_fact_det=_fetch_array($result_fact_det);
			$id_producto =$row_fact_det['id_producto'];
			$descripcion =$row_fact_det['descripcion'];
			$id_pedido_detalle =$row_fact_det['id_pedido_detalle'];
			$id_prod_serv =$row_fact_det['id_prod_serv'];
			$cantidad =$row_fact_det['cantidad'];
			$precio_venta =$row_fact_det['precio_venta'];
			$subt =$row_fact_det['subtotal'];
			$id_empleado =$row_fact_det['id_empleado'];
			$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];

			//linea a linea
			$descrip=texto_espacios($descripcion,63);
			$subt=$precio_venta*$cantidad;
			$precio_unit=sprintf("%.2f",$precio_venta);
			$subtotal=sprintf("%.2f",$subt);
			$total_final=$total_final+$subtotal;

			if(strlen($cantidad)>1){
				$sp0=$col0-1;
				$esp_init=espacios_izq(" ",$sp0);
			}else{
				$sp0=$col0;
				$esp_init=espacios_izq(" ",$sp0);
			}

			if(strlen($precio_unit)>4){
				$sp2=$col2-1;
				$esp_col2=espacios_izq(" ",$sp2);
			}else{
				$sp2=$col2;
				$esp_col2=espacios_izq(" ",$sp2);
			}

			if(strlen($subtotal)>4){
				$sp3=$col3-1;
				$esp_col3=espacios_izq(" ",$sp3);
			}else{
				$sp3=$col3;
				$esp_col3=espacios_izq(" ",$sp3);
			}
			$info_pedido.=$esp_init.$cantidad."   ".$descrip.$esp_col2.$precio_unit.$esp_col3.$subtotal."\n";
			$cuantos=$cuantos+1;
		}
	}
	$salto_linea=$lineas-$cuantos;
	for($j=0;$j<$salto_linea;$j++){
		$info_pedido.="\n";
	}
	$total_final_format=sprintf("%.2f",$total_final);
	list($entero,$decimal)=explode('.',$total_final_format);
	$enteros_txt=num2letras($entero);
	if(strlen($decimal)==1){
	$decimales_txt=$decimal."0";
	}
	else{
		$decimales_txt=$decimal;
	}
	$info_pedido.="\n";
	$cadena_salida_txt= " ".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
	$salida_txt_total=texto_espacios($cadena_salida_txt,70);
	$esp=espacios_izq(" ",7);
	$total_value=sprintf("%.2f",$total);
	$esp_init2=espacios_izq(" ",25);
	$info_pedido.=$esp_init2.$salida_txt_total.$esp."$ ".$total_value."\n";
	$esp=espacios_izq(" ",86);
	$info_pedido.="\n";
	$info_pedido.=$esp_init.$esp." $ ".$total_value."\n";

	$nreg_encode['pedidor'] = $info_pedido;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);

}
/*
function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	print_fact($numero_doc,"COF");
}
*/
function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	//$id_ccf_cof= $_POST['id'];

	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	$id_ccf_cof= '1';
	$tipo_fact="";
	if ($id_ccf_cof=='1'){
		$numero_docx = $numero_doc."_COF";
		$tipo_fact="COF";
	}
	else{
		$numero_docx = $numero_doc."_CCF";
		$tipo_fact="CCF";
	}
	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	if($nrows_fact>0){
		$id_factura=trim($row_fact['id_factura']);
		$table_fact= 'factura';
		$form_data_fact = array(
			'finalizada' => '1'
		);
		$where_clause="WHERE numero_doc='$numero_docx' AND id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
	}


	$info_factura=print_fact($numero_doc,$tipo_fact);
	$nreg_encode['facturar'] = $info_factura;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}

/*
function texto_espacios($texto,$long){
	$countchars=0;
	$countch=0;
	$texto=trim($texto);
	$len_txt=strlen($texto);
	$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','Ñ','Á','É','Í','Ó','Ú');

    foreach($latinchars as $value){
		$countchars=substr_count($texto,$value);
        $countch= $countchars+$countch;
    }

	if($len_txt<=$long){
	 if($countch>0)
		$n=($long+$countch)-$len_txt;
	 else
		$n=$long-$len_txt;

		$texto_repeat=str_repeat(" ",$n);
		$texto_salida=$texto.$texto_repeat;
	}
	else{
		$long=$long-1;
		$texto_salida=substr($texto,0,$long).".";
	}
	return $texto_salida;
}

function espacios_izq($texto,$long){
	$len_txt=strlen($texto);

	if($len_txt<=$long){

			$alinear='STR_PAD_LEFT';
	 $texto_salida=str_pad($texto, $long, " ",STR_PAD_LEFT );
	}
	else{
	$texto_salida=substr($texto,0,$long);
	}
	return $texto_salida;
}
*/
//mostrar el num de pedido de consumidor o credito fiscal
function mostrar_numfact(){
	$id=$_REQUEST['id'];

	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	$sql="select * from ultimo_numdoc AND id_sucursal='$id_sucursal'";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);
	$ult_cof=$rows['ult_cof'];
	$ult_ccf=$rows['ult_ccf'];

	$fecha_actual=date("Y-m-d");
	if ($nrows>0){
		if ($id=='1')
			$ult_doc=$ult_cof+1;
		else
			$ult_doc=$ult_ccf+1;
	}
	else{
		if ($id=='1')
			$ult_doc=1;
		else
			$ult_doc=1;
	}
	$len_ult_valor=strlen($ult_doc);
	$long_num_fact=4;
	$len_ult_valor=strlen($ult_doc);
	$long_increment=$long_num_fact-$len_ult_valor;
	$valor_txt="";
	for ($j=0;$j<$long_increment;$j++){
		$valor_txt.="0";
	}
	$valor_txt=$valor_txt.$ult_doc;

	/*
	switch ($len_ult_valor) {
		case 1:
			$valor_txt='000'.$ult_doc;
			break;
		case 2:
			$valor_txt='00'.$ult_doc;
			break;
		case 3:
			$valor_txt='0'.$ult_doc;
			break;
		case 4:
			$valor_txt=$ult_doc;
			break;
		}
	*/
	echo "<div class='form-group has-info single-line'><label>Numero de pedido</label> <input type='text' placeholder='Numero de pedido' class='form-control' id='numero_doc' name='numero_doc' value='$valor_txt'></div>";

	//echo "<div class='col-md-4'>";
	//echo "<div class='form-group has-info single-line'><label>Numero de pedido</label> <input type='text' placeholder='Numero de pedido' class='form-control' id='numero_doc' name='numero_doc' value='$ult_doc'></div>";
	//echo "</div>";
	//echo "<div class='col-md-4'>";
	//echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
	//echo "</div>";
}
//finalizar la factura
function finalizar_fact(){

	$id_factura = $_POST['id_factura'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql_fact="SELECT * FROM factura WHERE numero_doc='$id_factura' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	if($nrows_fact>0){
		$table_fact= 'factura';
		$form_data_fact = array(
			'finalizada' => '1'
		);
		$where_clause="WHERE numero_doc='$id_factura' AND id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
		$numero_doc=trim($row_fact['numero_doc']);
	}



	if ($actualizar){
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Venta Numero: <strong>'.$numero_doc.'</strong>  Finalizada con Exito !';
		$xdatos['process']='Finalizar';

	}
	else{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Venta Numero: <strong>'.$numero_doc.'</strong>  no pudo ser Finalizada !';
		$xdatos['process']='Finalizar';

	}
	echo json_encode($xdatos);
}
//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {


	switch ($_REQUEST['process']) {
	case 'insert':
		insertar();
		break;
	case 'mostrar_datos_cliente':
		mostrar_datos_cliente();
		break;
	case 'consultar_stock':
		consultar_stock();
		break;
	case 'cargar_empleados':
		cargar_empleados();
		break;
	case 'cargar_precios':
		cargar_precios();
		break;
	case 'total_texto':
		total_texto();
		break;
	case 'print1':
		print1();
		break;
	case 'print2':
		print2(); //Generacion de los datos de pedido que se retornan para otro script que imprime!!!
		break;
	case 'mostrar_numfact':
		mostrar_numfact();
		break;
	case 'reimprimir' :
		reimprimir();
		break;
	case 'imprimir_fact':
		imprimir_fact();
		break;
	case 'finalizar_fact' :
			finalizar_fact();
			break;
	}

 //}
}
?>
