<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
function initial() {
	$_PAGE = array ();
	$_PAGE ['title'] = 'Reserva de Producto';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style_table3.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	//ver si se ha hecho la apertura de caja !!!
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'");
	$cuenta = _num_rows($sql_apertura);

	$turno_vigente=0;
	if($cuenta>0){
		$row_apertura = _fetch_array($sql_apertura);
		$id_apertura = $row_apertura["id_apertura"];
		$turno = $row_apertura["turno"];
		$fecha_apertura = $row_apertura["fecha"];
		$hora_apertura = $row_apertura["hora"];
		$turno_vigente = $row_apertura["vigente"];
	}

	//crear array clientes
  $sql0="SELECT id_cliente,nombre FROM clientes";
  $result0=_query($sql0);
  $count0=_num_rows($result0);
  //$array0 =array(-1=>"Seleccione");
  for ($x=0;$x<$count0;$x++){
    $row0=_fetch_array($result0);
    $id0=$row0['id_cliente'];
    $description=$row0['nombre'];
    $array0[$id0] = $description;
  }
	//crear array tipo_pagos
	  $array1 = array(-1=>"Seleccione");
		//crear array colores
		$sql2="SELECT * FROM colores";
		$result2=_query($sql2);
		$count2=_num_rows($result2);
		$array2= array(-1=>"Seleccione");
		for ($y=0;$y<$count2;$y++){
			$row2=_fetch_array($result2);
			$id2=$row2['id_color'];
			$description2=$row2['nombre'];
			$array2[$id2] = $description2;
		}
	$iva=0;
	$sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
	$result_IVA=_query($sql_iva);
	$row_IVA=_fetch_array($result_IVA);
	$iva=$row_IVA['iva']/100;
	$monto_retencion1=$row_IVA['monto_retencion1'];
	$monto_retencion10=$row_IVA['monto_retencion10'];
	$monto_percepcion=$row_IVA['monto_percepcion'];
//array de tipos Documento
$sql3="SELECT idtipodoc, nombredoc, provee,  alias FROM tipodoc WHERE cliente=1
AND alias='RES'
";
$result3=_query($sql3);
$count3=_num_rows($result3);
//$array3= array(-1=>"Seleccione");
for ($z=0;$z<$count3;$z++){
	$row3=_fetch_array($result3);
	$id3=$row3['alias'];
	$description3=$row3['nombredoc'];
	$array3[$id3] = $description3;
}
//array de tipo_pagos
$sql4="SELECT * FROM tipo_pago WHERE  inactivo=0 AND alias_tipopago!='CRE' ";
$result4=_query($sql4);
$count4=_num_rows($result4);
for ($a=0;$a<$count4;$a++){
	$row4=_fetch_array($result4);
	$id4=$row4['id_tipopago'];
	$alias_tp=$row4['alias_tipopago'];
	$description4=$row4['descripcion']." |".$alias_tp;
	$array4[$id4] = $description4;
}
//array de vales x devolucion no cobrados
$sql5="SELECT * FROM mov_caja WHERE  alias_tipodoc='DEV' AND cobrado=0 ";
$result5=_query($sql5);
$count5=_num_rows($result5);
$array5 =array(-1=>"Seleccione");
for ($v=0;$v<$count5;$v++){
	$row5=_fetch_array($result5);
	$id5=$row5['id_movimiento'];
	$fecha=$row5['fecha'];
	$valor=$row5['valor'];
	$description5=$row5['id_movimiento']." |".$fecha." |".$valor;
	$array5[$id5] = $description5;
}
//array de empleados
$sql6='SELECT id_empleado, nombre FROM empleados WHERE  inactivo=0  AND vendedor=1';
$result6=_query($sql6);
$count6=_num_rows($result6);
for ($a=0;$a<$count6;$a++){
	$row6=_fetch_array($result6);
	$id6=$row6['id_empleado'];
	$description6=$row6['nombre'];
	$array6[$id6] = $description6;
}
?>

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
	</div>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<!--Primero si e si es inv. inicial ,factura de compra, compra caja chica, traslado de otra sucursal; luego Registrar No. de Factura , lote, cliente -->
				<div class="ibox">
					<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
							if ($turno_vigente=='1' ){
						?>
					<div class="ibox-content">
						<input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
						<input type='hidden' name='monto_retencion1' id='monto_retencion1' value='<?php echo $monto_retencion1; ?>'>
						<input type='hidden' name='monto_retencion10' id='monto_retencion10' value='<?php echo $monto_retencion10; ?>'>
						<input type='hidden' name='monto_percepcion' id='monto_percepcion' value='<?php echo $monto_percepcion; ?>'>
						<input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
						<input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
						<input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
						<?php
						//1 INVENTARIO INICIAL
						$fecha_actual=date("Y-m-d");
						?>

						<div class="row">
							<div class="widget">
								<div class="row">
									 <div class="col-md-12">

										 <!-- --><!--reordenamiento header factura-->
										 <div class="row">
													<div class="col-md-12">
												<div class="col-md-12 border_div">
													<div class="col-md-12">
																			<div class="row search-header">
																				<table class="table-condensed table4">
																					<tr>
																						<td class='td_peq'><label>Fecha</label></td>
																						<td class='td_med0'>
																							<input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fecha_actual;?>' id='fecha2' name='fecha2'>
																						</td>
																						<td class='td_peq0'><label>Cliente</label></td>
																					<td class='td_med2'>
																						<?php
																						$nombre_select0="select_clientes";
																						$idd0=-1;
																						$style='';
																						$select0=crear_select2($nombre_select0,$array0,$idd0,$style);
																						echo $select0;
																						?>
																					</td>
																					<td class='td_peq0'><label>Pago</label></td>
																					<td class='td_med2'>
																						<?php
																						$nombre_select1="select_tipo_pago";
																						$idd1=1;
																						$style='';
																						$select1=crear_select2($nombre_select1,$array4,$idd1,$style);
																						echo $select1;
																						?>
																					</td>
																					</tr>
																				</table>
																				<table class="table-condensed table4">
																					<tr>
																					<td class='td_peq'><label>Vendedor</label></td>
																					<td class='td_med2'>
																						<?php
																						$nombre_select0="select_vendedor";
																						$idd0=-1;
																						$style='';
																						$select0=crear_select2($nombre_select0,$array6,$idd0,$style);
																						echo $select0;
																						?>
																					</td>
																					<td class='td_peq0'><label>Items</label></td>
																					<td class='td_peq0'>
																							<input type="text"  class='form-control input_header_panel'  id="items" value=0 readOnly /></label>
																					</td>
																					<td class='td_peq0'><label>Pares</label></td>
																					<td class='td_peq0'>
																							<input type="text"  class='form-control input_header_panel'  id="pares" value=0 readOnly /></label>
																					</td>
																					</tr>
																				</table>
																		</div>
														</div>
														</div>
														</div>
														</div>

														<div class="widget-content">
																	<!--div class='container'-->

																	<table class="table table2 table-fixed table-striped "id="inventable">
																			<thead class='thead1'>
																				<tr class='tr1'>
																					<th class="text-success col1 th1" >Código</th>
																				 <th class="text-success col2 th1">Nombre</th>
																				 <th class="text-success col3 th1">Existencias</th>
																				 <th class="text-success col3 th1">Precio</th>
																				 <th class="text-success col4 th1">Cantidad</th>
																					<th class="text-success col5 th1">Subtotal</th>
																				 <th class="text-success col5 th1">$ Descto.</th>

																				 <th class="text-success col6 th1">Exento</th>
																				 <th class="text-success col7 th1">Gravado </th>
																				 <th class="text-success col8 th1">Acci&oacute;n</th>
																				</tr>
																		</thead>
																		<tbody class='tbody1 tbody2'>
																		</tbody>
																</table>
																<table class="table table3">
																	<tbody class='tbody5'>
																		<tr>
																		<td  class='col1' ></td>
																		<td class='col2' id='totaltexto'></td>
																		<td class='col3'>&nbsp;</td>
																		<td class='col3'>Totales</td>
																		<td class='col4' id='totcant'>0.00</td>
																	  <td  class='col5' id='total_sin_descto'>$0.00</td>
																		<td class='col7' id='totdescto' >$0.00 </td>
																		<td  class='col8' id='total_exento'>$0.00</td>
																		<td  class='col9' id='total_gravado'>$0.00</td>
																		<td class='col1'> </td>
																		</tr>
																	</tbody>
																</table>
											</div> <!-- /widget-content -->
											</div>
											</div>
											<div class="row">
														<div class="col-md-12">
													<div class="col-md-9 border_div">
														<div class="col-md-12">
																				<div class="row search-header">
																					<table class="table-condensed table4">
																						<tr>
																							<td class='td_med'><label>Límite búsqueda</label></td>
																							<td class='td_peq'><input type="text"  class=' input_header_panel'  id="limite" value=400 /></td>
																							<td class='td_med'><label>Reg. Encontrados</label></td>
																							<td class='td_peq'>	<input type="text"  class='input_header_panel' id='reg_count' value=0 readOnly /></td>

																							<!--td class='td_med'><a class="btn btn-primary" data-target="#viewModal1" data-toggle="modal" data-refresh="true" href="consultar_stock.php" id="cstok" ><i class="fa fa-search"></i> Ver Inventario F4</a></td-->
																							<td class='td_med'><button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button></td>
																						</tr>
																					</table>
																					<table class="table-condensed table4">
																						<tr>
																							<td class='td_gde'><input type="text" id="keywords" class='form-control' placeholder="Descripción"/></td>
																							<td class='td_med'> <input type="text" id="barcode" class='form-control' placeholder="Código Barra" /></td>
																							<td class='td_med'><input type="text" id="estilo" class='form-control' placeholder="Estilo" /></td>
																							<td class='td_peq'> <input type="text" id="talla" class='form-control' placeholder="Talla" /></td>
																							<td class='td_med2'>
																								<?php
																								// se va filtrar por descripcion, estilo, talla, color, barcode
																								$nombre_select="select_colores";
																								$id_val=-1;
																								$style='';
																								$select=crear_select2($nombre_select,$array2,$id_val,$style);
																								echo $select;
																								?>
																							</td>

																						</tr>
																					</table>

																				</div>

																				<div class="widget-content2">

																			<div class="row">
																				<div class="loading-overlay col-md-6">
																					<div class="overlay-content " id='reg_count0'>Cargando.....</div>
																				</div>

																			</div>
																			</div>

																			<div  class='widget-content2' id="content">
																				<div class="row">
																			<div class="col-md-12">

																				<table class="table" id='loadtable'>
																					<thead class='thead1'>
																						<tr class='tr1'>
																						<th class="text-success col12 th1" >Código</th>
																						<th class="text-success col13 th1">Nombre</th>
																						<th class="text-success col12 th1">Precio</th>
																						<th class="text-success col12 th1">Estilo</th>
																						<th class="text-success col12 th1">Talla</th>
																						<th class="text-success col12 th1">Color</th>
																						<th class="text-success col12 th1">% Desc.</th>
																				</tr>
																				</thead>
																					<tbody class='tbody1 tbody4' id="mostrardatos">
																					</tbody>
																				</table>
																		</div>
																	</div>
																	</div>

																			</div>
																			</div>
																			<div class="col-md-3 border_div">

																				<table class='invoice-total2'>
																					<tbody>
																						<tr>
																							<td class="text-warning altura_td"><strong>SUMAS (SIN IVA) $:</strong></td>
																							<td  class="red1  altura_td" id='total_gravado_sin_iva'></td>
																						</tr>
																						<tr>
																							<td class="text-warning altura_td"><strong>TOTAL DESCUENTO $:</strong></td>
																							<td  class="red1  altura_td" id='total_descuento_final'></td>
																						</tr>
																						<tr>
																					<tr>
																						<td class="text-navy altura_td"><strong>TOTAL $:</strong></td>
																						<td class="red1 altura_td"  id='total_final'></td>
																					</tr>
																					<tr>
																				</tbody>
																			</table>
																			<table class="table table2">
																				<tbody class='tbody3'>
																				<tr>
																					<td><div class='col-md-12' id="totaltexto">
																						<h5 class='red1'></h5>
																					</div>
																				</td>
																				</tr>
																				</tbody>
																				</table>
																	</div>
																			</div>
																			</div>
											</div> <!-- /widget-->
											</div>
<div class="row">
		<div id="paginador"></div>
					<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
					<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename;?>">
          <input type="hidden" name="process" id="process" value="insert"><br>
					<!-- Modales-->
	  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-md">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Abono Reserva de Producto</h4>
				</div>
				<div class="modal-body">
					<div class="wrapper wrapper-content  animated fadeInRight">
						<div class="row">
							<input type='hidden' name='id_factura' id='id_factura' value=''>
							<div class="col-md-6">
								<div class="form-group">
									<label><h5 class='text-navy'>Numero Reserva:</h5></label>
								</div>
							</div>
							<div class="col-md-6" >
								<div class="form-group" id='fact_num'></div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
			        	<div class="form-group">
			          	<label><h5 class='text-navy'>Total Productos $:</h5></label>
			          </div>
							</div>
							<div class="col-md-6">
			        	<div class="form-group">
			          	<input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
			          </div>
							</div>
					  </div>
						<div class="row" id='ccf'>
							<div class="col-md-4">
								<div class="form-group">
									<label><strong><h5 class='text-navy'>Nombre Cliente: </h5></strong></label>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
								 <input type="text" id='nombreape' name='nombreape' value=''  class="form-control" >
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>DUI Cliente</label>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type='text' placeholder='dui' class='form-control' id='dui2' name='dui2' value=''>
								</div>
							</div>
							<div class="col-md-4">
											<div class="form-group">
												<label>Telefonos Cliente</label>
											</div>
							</div>
							<div class="col-md-4">
											<div class="form-group">
												<input type='text' placeholder='tel1' class='form-control' id='tele1' name='tele1' value=''>
											</div>
							</div>
							<div class="col-md-4">
											<div class="form-group">
												<input type='text' placeholder='tel2' class='form-control' id='tele2' name='tele2' value=''>
											</div>
							</div>
						</div>
						<div class="row" id='div_abono'>
											<div class="col-md-6">
												<div class="form-group">
													<label>Abono $</label>
											</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<input type="text" id="abono" name="abono" value=""  class="form-control decimal">
											</div>
											</div>
						</div>
											<div class="row" id='tipo_pago_efectivo'>
											<div class="col-md-6">
												<div class="form-group">
													<label>Efectivo $</label>
											</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
											</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Cambio $</label>
											</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
											</div>
								</div>
							</div>
							<div class="row" id='tipo_pago_tarjeta'>
								<div class="col-md-6">
									<div class="form-group">
										<label>Número Tarjeta</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="Número Tarjeta" value=""  class="form-control decimal">
									</div>
								</div>
							<!--/div>
							<div class="row"-->
								<div class="col-md-6">
									<div class="form-group">
										<label>Emisor</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" id="emisor" name="emisor" value=0 placeholder="Emisor" class="form-control" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>No. Transacción (Voucher)</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" id="voucher" name="voucher" value=0 placeholder="No. Transacción (Voucher)" class="form-control decimal"  >
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id='mensajes'></div>
							</div>
						</div>
					</div>
						<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
						<button type="button" class="btn btn-warning" disabled id="btnEsc">Salir</button>
					</div>
					</div>
				</div>
			</div>

			<div class="modal-container">
				<div class="modal fade" id="clienteModal" tabindex="-2" role="dialog" aria-labelledby="myModalCliente" aria-hidden="true">
		  <div class="modal-dialog model-sm">
				        <div class="modal-content"> </div>
				    </div>
				</div>
			</div>
				<!-- Modales -->
			</div>
			</div>
<?php
}   //apertura de caja
else {
	echo "<div></div><br><br><div class='alert alert-warning'><h3 class='text-danger'>No Hay Apertura de Caja vigente para este turno!!!</h3></div>";
}  //apertura de caja

	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

?>
</div>
</div>
</div>
</div>
<?php
include_once ("footer.php");
echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
echo "<script src='js/funciones/reservas.js'></script>";
}


function insertar(){
	//ACA se va insertar a las sig tablas:
	// factura y detale_factura, correlativos, stock y kardex ,(otras probables cuenta_cliente,)
	$cuantos = $_POST['cuantos'];
	$fecha_movimiento= $_POST['fecha_movimiento'];

	$id_tipo_pago = $_POST['id_tipo_pago'];
	$id_cliente= $_POST['id_cliente'];
	$total_venta = $_POST['total_venta'];
	$total_descuento= $_POST['total_descuento'];
  $array_json=$_POST['json_arr'];

	$id_usuario=$_SESSION["id_usuario"];
	$id_empleado=$id_usuario;
	$id_sucursal= $_SESSION['id_sucursal'];
	$items= $_POST['items'];
	$pares = $_POST['pares'];
  $monto_pago= $_POST['monto_pago'];
  $abono= $_POST['abono'];
	$total_descuento=$_POST['total_descuento'];
	$id_vendedor= $_POST['id_vendedor'];
	$saldo=$total_venta-$abono;
	$insertar1=false;
	$insertar2=false;
	$insertar3=false;


	if ($cuantos>0){
		 _begin();
		 $hora=date("H:i:s");
		 $fecha_ing=date('Y-m-d');
		 //turno de caja
		 $sql_turno="SELECT  turno,id_apertura FROM apertura_caja
		 WHERE  vigente=1 AND  id_sucursal='$id_sucursal'";
		 $result_turno= _query($sql_turno);
		$rows_turno=_fetch_array($result_turno);
		$nrows_turno=_num_rows($result_turno);
		$turno=$rows_turno['turno'];
		$id_apertura=$rows_turno['id_apertura'];

		$alias_tipodoc='RES';
		//tipo=1 interno, tipo=2 cliente, 3 es proveedor
		$table_corr="correlativos";
		$where_clause_n=" WHERE alias='$alias_tipodoc'
 		AND tipo=2
 		AND id_sucursal='$id_sucursal'";
			 $sql_corr="SELECT id_correlativo, alias, numero, tipo, id_sucursal
			 FROM $table_corr ".$where_clause_n;
			 $result_corr= _query($sql_corr);
	 		$rows_corr=_fetch_array($result_corr);
	 		$nrows_corr=_num_rows($result_corr);
	 		$ult_corr=$rows_corr['numero']+1;

			$numero_doc=zfill($ult_corr,15);

			$data_corr = array(
			'numero' => $ult_corr
			);
			$insertar_numdoc = _update($table_corr,$data_corr,$where_clause_n );
		// }
		//SELECT id_reserva, fecha, nombre, telefono, id_empleado,
		// hora, total, abono, saldo, pares, items, comentario FROM reservas WHERE 1
		 $sql_ventas="SELECT * FROM reservas WHERE numero_doc='$numero_doc'
		  AND  fecha_doc='$fecha_movimiento'
			AND id_cliente='$id_cliente'
		  AND id_sucursal='$id_sucursal'";
			$result_fc=_query($sql_ventas);
			$row_fc=_fetch_array($result_fc);
			$nrows_fc=_num_rows($result_fc);
			if($nrows_fc==0){
				$table_fc= 'reservas';
				$form_data_fc = array(
				'id_cliente' => $id_cliente,
				//'alias_tipodoc'=>$alias_tipodoc,
				'fecha_doc' => $fecha_movimiento,
				'numero_doc' => $numero_doc,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'total' => $total_venta,
				'descuento'=> $total_descuento,
				'abono'=>$abono,
				'pares' => $pares,
				'hora' => $hora,
				'tipopago'=> $id_tipo_pago,
				'finalizada' => 0,
				'turno'=>   $turno,
				'id_vendedor'=> $id_vendedor,
				);
				//falta en compras vencimiento a 30, 60, 90 dias y vence iva
				$insertar_fc = _insert($table_fc,$form_data_fc );
				$id_fact= _insert_id();
			}

			$array = json_decode($array_json,true);
			foreach ($array as $fila){
				 $table_dc= 'detalle_reservas';
				 if( $fila['precio_venta']>0 && $fila['cantidad']>0 ){
					 $id_producto=$fila['id'];
					 $cantidad=$fila['cantidad'];
					 $precio_venta=$fila['precio_venta'];
					 $descto=$fila['descto'];

					 $sql_producto="SELECT estilo,talla,id_color FROM productos WHERE id_producto='$id_producto'";

					 $result_prod=_query($sql_producto);
					 $row_prod=_fetch_array($result_prod);
					 $estilo=$row_prod['estilo'];
					 $talla=$row_prod['talla'];
					 $id_color=$row_prod['id_color'];
					 $subt=$precio_venta*$cantidad;
					 $form_data_dc = array(
	 		 			'id_reserva' => $id_fact,
	 		 			'id_producto' => $id_producto,
	 		 			'cantidad' => $cantidad,
	 		 			'precio' => $precio_venta,
						'descuento' => $descto,
	 		 			'subtotal' => $subt,
	 		 			);
				 //detalle de factura
				$insertar_dc = _insert($table_dc,$form_data_dc );
			 }
}
$concepto='RESERVA PRODUCTO';
//Insertar para generar vale por devolucion
$table_vale='mov_caja';
$form_data_vale = array(
'idtransace' => $id_fact,
'alias_tipodoc'=>$alias_tipodoc,
'numero_doc'=>$numero_doc,
'fecha' => $fecha_movimiento,
'hora' => $hora,
'valor' => $abono,
'concepto' => $concepto,
'turno' =>$turno,
'id_apertura' => $id_apertura,
'id_empleado' =>  $id_empleado,
'id_sucursal' => $id_sucursal,
'entrada'=>1,
);
$insertar_vale = _insert($table_vale, $form_data_vale);
}//if $cuantos>0

//  if ($insertar1 && $insertar2 && $insertar3){
if ($insertar_fc ){
	_commit(); // transaction is committed
	$xdatos['typeinfo']='Success';
		 $xdatos['msg']='Registro de Reservacion Actualizado !';
		 $xdatos['process']='insert';
		$xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
		$xdatos['factura']=$id_fact;
		$xdatos['numero_doc']=$numero_doc;
	}
	else{
	_rollback(); // transaction not committed
		 $xdatos['typeinfo']='Error';
		 $xdatos['msg']='Registro de Reservacion no pudo ser Actualizado !';
		 $xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
}

	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$id_usuario=$_SESSION["id_usuario"];
   $id_sucursal=$_SESSION['id_sucursal'];
	$sql_user="select * from usuario where id_usuario='$id_usuario'";
	$existencias=0;
	 $sql2="select * from stock where id_producto='$id_producto' and id_sucursal ='$id_sucursal'";
			 $stock2=_query($sql2);
			 $row2=_fetch_array($stock2);
			 $nrow2=_num_rows($stock2);
			 $existencias=$row2['existencias'];


	$sql3="select p.*,c.nombre from productos AS p
		JOIN colores AS c ON (p.id_color=c.id_color)
		where id_producto='$id_producto'
		";
	$result3=_query($sql3);
  $count3=_num_rows($result3);
	if($count3>0){
		$row3=_fetch_array($result3);
		$cp=$row3['costopro'];
		$descuento=$row3['descuento'];
		$pv_base=$row3['precio1'];

		$talla=$row3['talla'];
		$color=$row3['nombre'];
		$exento=$row3['exento'];
		$descripcion=$row3['descripcion'];

		$xdatos['descrip'] =$descripcion;
		$xdatos['costo_prom'] = $cp;
		$xdatos['pv_base'] = $pv_base;
		$xdatos['existencias'] = $existencias;
		$xdatos['color'] = $color;
		$xdatos['talla'] = $talla;
		$xdatos['exento'] = $exento;
		$xdatos['precios_vta']= $pv_base;
		$xdatos['descuento'] = $descuento;
	echo json_encode($xdatos); //Return the JSON Array
 }
}
function buscarBarcode(){
	$query = trim($_POST['id_producto']);
	$sql0="SELECT id_producto as id, descripcion, barcode, estilo FROM productos  WHERE barcode='$query'";
	$result = _query($sql0);
	$numrows= _num_rows($result);

	$array_prod = array();
  $array_prod="";
	while ($row = _fetch_array($result)) {
				$barcod=" [".$row['barcode']."] ";
				$array_prod =$row['id']."|".$barcod.$row['descripcion']."|1";
	}
	$xdatos['array_prod']=$array_prod;
	echo json_encode ($xdatos); //Return the JSON Array
}
function buscartipo_pago(){
	$id_tipo_pago= trim($_POST['id_tipo_pago']);
	$sql0="SELECT dp.id_producto, dp.cantidad
	FROM tipo_pagos AS p
	JOIN detalle_tipo_pagos AS dp ON(p.idtransace=dp.idtransace)
	WHERE p.idtransace='$id_tipo_pago'";
	$result = _query($sql0);
	$array_prod = array();
	$numrows= _num_rows($result);
 for ($i=0;$i<$numrows;$i++){
	 $row = _fetch_array($result);
	$id_producto =$row['id_producto'];
	$cantidad =$row['cantidad'];
	$array_prod[] = array(
 		 'id_producto' => $row['id_producto'],
 		 'cantidad' =>  $row['cantidad'],
  );
 }
	//$xdatos['array_prod']=$array_prod;
	echo json_encode ($array_prod); //Return the JSON Array
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
	//$cadena_salida= "<h3 class='text-danger'>Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>&nbsp;&nbsp;</h3>";
	$cadena_salida= "<h5 class='red1'>Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>&nbsp;&nbsp;</h5>";
	echo $cadena_salida;
}
function datos_clientes(){
	$id_cliente = $_POST['id_cliente'];
	$sql0="SELECT percibe, retiene, retiene10 FROM clientes  WHERE id_cliente='$id_cliente'";


	$result = _query($sql0);
	$numrows= _num_rows($result);
	$row = _fetch_array($result);
	$retiene1=$row['retiene'];
	$retiene10=$row['retiene10'];
	$percibe=$row['percibe'];
	$sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion from empresa";
	$result_IVA=_query($sql_iva);
	$row_IVA=_fetch_array($result_IVA);
	$iva=$row_IVA['iva']/100;
	$monto_retencion1=$row_IVA['monto_retencion1'];
	$monto_retencion10=$row_IVA['monto_retencion10'];
	$monto_percepcion=$row_IVA['monto_percepcion'];
	if ($percibe==1)
		$percepcion=round($monto_percepcion/100,2);
	else
		$percepcion=0;

	if ($retiene1==1)
			$retencion1=round($monto_retencion1/100,2);
	else
			$retencion1=0;

	if ($retiene10==1)
				$retencion10=round($monto_retencion10/100,2);
			else
					$retencion10=0;

	$xdatos['retencion1'] = $retencion1;
	$xdatos['retencion10'] = $retencion10;
	$xdatos['percepcion'] = $percepcion;
	echo json_encode($xdatos); //Return the JSON Array
}
function traerdatos() {
    $keywords = $_POST['keywords'];
    $estilo = $_POST['estilo'];
    $talla= $_POST['talla'];
    $id_color= $_POST['id_color'];
    $barcode= $_POST['barcode'];
		$limite= $_POST['limite'];
		//if(strlen(trim($keywords))>=0) {
    $sqlJoined="SELECT pr.id_producto,pr.descripcion, pr.precio1, pr.costopro, pr.talla,
		 pr.exento, pr.estilo, pr.barcode, co.id_color,co.nombre,pr.descuento
		 FROM productos AS pr, colores AS co, stock AS st
		";
    $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode,$limite);
    $sql_final= $sqlJoined." ".$sqlParcial." ";
    $query = _query($sql_final);

		$num_rows = _num_rows($query);
		$filas=0;
    if($num_rows > 0){
            while($row = _fetch_array($query)) {
                $id_producto = $row['id_producto'];
                $descripcion=$row["descripcion"];
                $estilo = $row['estilo'];
								$exento = $row['exento'];
								$cp = $row['costopro'];
								$precio = $row['precio1'];
                $talla = $row['talla'];
								$id_color2=$row['id_color'];
								$nombre = $row['nombre'];
								$barcode = $row['barcode'];
								$descuento = $row['descuento'];

								//<i class="fa fa-check"></i>
								$btnSelect='<input type="button" id="btnSelect" class="btn btn-primary fa" value="&#xf00c;">';
						?>
				   <tr class='tr1'  tabindex="<?php echo $filas;?>">
					  <td class='col12 td1'><input type='hidden'  id='exento' name='exento' value='<?php echo $exento;?>'> <h5><?php echo $id_producto;?></h5></td>
            <td class='col13 td1'><h5><?php echo $descripcion;?></h5></td>
						<td class='col12 td1'><h5><?php echo $precio;?></h5></td>
            <td class='col12 td1'><h5 class='text-success'><?php echo $estilo;?></h5></td>
            <td class='col12 td1'><h5 class='text-success'><?php echo $talla;?></h5></td>
						<td class='col12 td1'><h5 class='text-success'><?php echo $nombre;?></h5></td>
            <td class='col12 td1'><?php echo $descuento;?></td>
          </tr>

          <?php
					$filas+=1;
          }
				}
  echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
function get_sql( $keywords, $id_color, $estilo, $talla, $barcode,$limite){
	$andSQL='';
		$id_sucursal= $_SESSION['id_sucursal'];
 $whereSQL="  WHERE pr.id_color=co.id_color
	AND pr.id_producto=st.id_producto
	AND st.id_sucursal='$id_sucursal'
	AND st.existencias>0 
	";

	$keywords=trim($keywords);
	//$andSQL.= " AND co.id_color='$id_color'";

	if(!empty($barcode)){
			$andSQL.= " AND  pr.barcode LIKE '{$barcode}%'";
	}
	else{
  if(!empty($keywords)){
  $andSQL.= "AND  pr.descripcion LIKE '%".$keywords."%'";
      if(!empty($estilo)){
          $andSQL.= " AND pr.estilo LIKE '{$estilo}%' ";
      }
      if(!empty($talla)){
          $andSQL.= " AND pr.talla LIKE '%{$talla}%'";
      }
			if($id_color!=-1){
					$andSQL.= " AND co.id_color='$id_color'";
			}
  }

  if(empty($keywords)  && !empty($estilo)){
		$andSQL.= "AND  pr.estilo LIKE '".$estilo."%' ";
		if(!empty($talla)){
				$andSQL.= " AND pr.talla LIKE '%".$talla."%' ";
		}
		if($id_color!=-1){
				$andSQL.= " AND co.id_color='$id_color'";
		}
   }
	 if(empty($keywords)  && empty($estilo) && !empty($talla)){
		$andSQL.= "AND pr.talla LIKE '%".$talla."%' ";
		if($id_color!=-1){
				$andSQL.= " AND co.id_color='$id_color'";
		}
	 }
	 if(empty($keywords)  && empty($estilo) && empty($talla) && ($id_color!=-1)) {
		$limite=1000;
	 	$andSQL.= " AND co.id_color='".$id_color."'";
 	}
	}

	$orderBy=" ";
	$limitSQL=" LIMIT ".$limite;
	$orderBy=" ORDER BY pr.id_producto,pr.descripcion, pr.barcode,pr.estilo,pr.talla,co.id_color ";

	$sql_parcial=$whereSQL.$andSQL.$orderBy.$limitSQL;
  return $sql_parcial;
}
//datos clientes
function mostrar_datos_cliente(){
    $id_cliente=$_POST['id_client'];

    $sql="SELECT * FROM clientes WHERE	id_cliente='$id_cliente'";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i ++) {
            $row = _fetch_array($result);
            $id_cliente=$row["id_cliente"];
            $nombre=$row["nombre"];
            $nit=$row["nit"];
            $dui=$row["dui"];
            $telefono1=$row["telefono1"];
						$telefono2=$row["telefono2"];
            $registro=$row["nrc"];
						$direccion=$row["direccion"];

        }
    }
    $xdatos['dui']= $dui;
    $xdatos['tele1']= $telefono1;
		$xdatos['tele2']= $telefono2;
		$xdatos['nombreape']=   $nombre;
    echo json_encode($xdatos); //Return the JSON Array
}
//Impresion
function imprimir_fact() {

	$hora=date("H:i:s");
	$fecha=date("Y-m-d");

	$numero_doc = $_POST['numero_doc'];
	$totalfact = $_POST['total'];
  $tipo_impresion= $_POST['tipo_impresion'];
  $id_factura= $_POST['num_doc_fact'];
	$abono= $_POST['abono'];
	$dui= $_POST['dui'];
	$tel1= $_POST['tel1'];
	$tel2= $_POST['tel2'];
	$nombreape= $_POST['nombreape'];

  $tipo_entrada_salida="RESERVA";
	$id_sucursal=$_SESSION['id_sucursal'];

	$voucher=-1;
	$id_pago=0;
  if (isset($_POST['numero_tarjeta'])){
		$numero_tarjeta=$_POST['numero_tarjeta'];
	}
	if (isset($_POST['emisor'])){
		$emisor=$_POST['emisor'];
	}
	if (isset($_POST['voucher'])){
		$voucher=$_POST['voucher'];
		// SELECT id_pago_tarjeta, idtransace, alias_tipodoc, fecha, voucher, numero_tarjeta, emisor, monto FROM pago_tarjeta WHERE 1
		$sql_pt="SELECT * FROM pago_tarjeta  WHERE idtransace='$id_factura'
		AND alias_tipodoc='RES'";
		$result_pt=_query($sql_pt);
		$row_pt=_fetch_array($result_pt);
		$nrows_pt=_num_rows($result_pt);
		if($nrows_pt==0){
			$fecha_movimiento=$row_pt['fecha_doc'];
			$table_pt= 'pago_tarjeta';

			$form_data_pt = array(
					'idtransace' => $id_factura,
					'voucher' => $voucher,
					'emisor' => $emisor,
					'numero_tarjeta' => $numero_tarjeta,
					'monto' => $abono,
					'fecha' => $fecha,
					'alias_tipodoc'=>'RES',
			);

			$where_clause="WHERE idtransace='$id_factura' AND alias_tipodoc='RES'";
			$actualizar = _insert($table_pt,$form_data_pt);
				$id_pago= _insert_id();
		}
	}

	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$sql_fact="SELECT * FROM reservas WHERE id_reserva='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$fecha_movimiento=$row_fact['fecha_doc'];
		$total=$row_fact['total'];
		$saldo=$total-$abono;
		$table_fact= 'reservas';

		$id_cliente=$row_fact['id_cliente'];

		if ($id_cliente=='1') {
			# code...
			$var1=preg_match('/\x{27}/u', $nombreape);
			$var2=preg_match('/\x{22}/u', $nombreape);
			if($var1==true || $var2==true){
			 $nombreape =stripslashes($nombreape);
			}
			 //'id_cliente' => $id_cliente,
			$table = 'clientes';
			$form_data = array(
			'nombre' => $nombreape,
			'dui' => $dui,
			'telefono1' => $tel1,
			'telefono2' => $tel2,
			);

			if(trim($nombreape)!=''){
				$insertar = _insert($table,$form_data );
				$id_cliente=_insert_id();
				if($insertar){

					$form_data_fact = array(
						'finalizada' => '0',
						'id_pago_tarjeta' => $id_pago,
						'nombre' => $nombreape,
						'telefono'=>$tel1,
						'id_cliente'=> $id_cliente,
						'abono'=>$abono,
						'saldo'=>$saldo,

					);
					$where_clause="WHERE id_reserva='$id_factura'";
					$actualizar = _update($table_fact,$form_data_fact, $where_clause );

				}
			}
		}
		else
		{
			$form_data_fact = array(
				'finalizada' => '0',
				'id_pago_tarjeta' => $id_pago,
				'nombre' => $nombreape,
				'telefono'=>$tel1,
				'abono'=>$abono,
				'saldo'=>$saldo,

			);
			$where_clause="WHERE id_reserva='$id_factura'";
			$actualizar = _update($table_fact,$form_data_fact, $where_clause );
		}


		$table_vale='mov_caja';
		$form_data_vale = array(
		'valor' => $abono,
		);

		$where1="WHERE idtransace='$id_factura' AND alias_tipodoc='RES' AND id_sucursal='$id_sucursal'";
		$actualizar_vale = _update($table_vale, $form_data_vale, $where1);

	}
  $headers=""; $footers="";
	$info_facturas=print_ticket_res($id_factura,$dui,$nombreape,$tel1,$tel2);

	$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='RES'";

	$result_pos=_query($sql_pos);
	$row1=_fetch_array($result_pos);

	$headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
	$headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
	$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
	$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['facturar'] =$info_facturas;
	$nreg_encode['sist_ope'] =$so_cliente;
	$nreg_encode['headers'] =$headers;
	$nreg_encode['footers'] =$footers;

	echo json_encode($nreg_encode);
}
function agregar_cliente(){
    $nombre=$_POST["nombress"];
    $dui=$_POST["dui"];
    $tel1=$_POST["tel1"];
		$tel2=$_POST["tel2"];
  	$var1=preg_match('/\x{27}/u', $nombre);
		$var2=preg_match('/\x{22}/u', $nombre);
		if($var1==true || $var2==true){
		 $nombre =stripslashes($nombre);
		}
    $sql_result=_query("SELECT * FROM clientes WHERE nombre='$nombre'");
		$numrows=_num_rows($sql_result);
    $row_update=_fetch_array($sql_result);
    $id_cliente=$row_update["id_cliente"];
    $name_cliente=$row_update["nombre"];


     //'id_cliente' => $id_cliente,
    $table = 'clientes';
    $form_data = array(
    'nombre' => $nombre,
    'dui' => $dui,
    'telefono1' => $tel1,
		'telefono2' => $tel2,
    );

    if($numrows == 0 && trim($nombre)!=''){

    	$insertar = _insert($table,$form_data );
    	$id_cliente=_insert_id();
    	if($insertar){
       	$xdatos['typeinfo']='Success';
       	$xdatos['msg']='Registro insertado con exito!';
       	$xdatos['process']='insert';
        $xdatos['id_client']=  $id_cliente;
    	}
    	else{
       	$xdatos['typeinfo']='Error';
       	$xdatos['msg']='Registro no insertado !';
			}
    }
		else{
			 $xdatos['typeinfo']='Error';
			 $xdatos['msg']='Registro no insertado !';
		}
	echo json_encode($xdatos);
}
function mostrar_datos_cliente2()
{
    $id_cliente=$_POST['id_client'];

    $sql="SELECT * FROM cliente
	WHERE
	id_cliente='$id_cliente'";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i ++) {
            $row = _fetch_array($result);
            $id_cliente=$row["id_cliente"];
            $nombre=$row["nombre"];
            $apellido=$row["apellido"];
            $nit=$row["nit"];
            $dui=$row["dui"];
            $direccion=$row["direccion"];
            $telefono1=$row["telefono1"];
            $giro=$row["giro"];
            $registro=$row["registro"];
            $email=$row["email"];
            $facebook=$row["facebook"];
        }
    }
    $xdatos['nit']= $nit;
    $xdatos['registro']= $registro;
		$xdatos['nombreape']=   $nombre." ".$apellido;
    echo json_encode($xdatos); //Return the JSON Array
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
	case 'consultar_stock':
		consultar_stock();
		break;
	case 'buscarBarcode' :
		buscarBarcode();
		break;
	case 'total_texto':
		total_texto();
		break;
	case 'datos_clientes':
		datos_clientes();
		break;
	case 'traerdatos':
		traerdatos();
		break;
	case 'buscartipo_pago' :
		buscartipo_pago();
		break;
	case 'imprimir_fact':
		imprimir_fact();
		break;
	case 'mostrar_datos_cliente':
			mostrar_datos_cliente();
			break;
	case 'agregar_cliente':
	    agregar_cliente();
	    break;
	}
}
?>
