<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
function initial() {
	$_PAGE = array ();
	$_PAGE ['title'] = 'Venta de Producto';
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style_pos.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

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
AND alias!='DEV' AND alias!='RES'
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
$sql4='SELECT * FROM tipo_pago WHERE  inactivo=0 ';
$result4=_query($sql4);
$count4=_num_rows($result4);
for ($a=0;$a<$count4;$a++){
	$row4=_fetch_array($result4);
	$id4=$row4['id_tipopago'];
	$alias_tp=trim($row4['alias_tipopago']);
	$description4=trim($row4['descripcion'])." |".$alias_tp;
	$array4[$id4] = $description4;
}
//array de vales x devolucion no cobrados
$sql5="SELECT * FROM mov_caja WHERE  alias_tipodoc='DEV' AND cobrado=0 AND anulado=0
			UNION ALL
			SELECT * FROM mov_caja WHERE  alias_tipodoc='RES' AND cobrado=0 AND anulado=0 ";
$result5=_query($sql5);
$count5=_num_rows($result5);
$array5 =array(-1=>"Seleccione");
for ($v=0;$v<$count5;$v++){
	$row5=_fetch_array($result5);
	$id5=$row5['id_movimiento'];
	$fecha=$row5['fecha'];
	$valor=$row5['valor'];
	$description5=$row5['id_movimiento']." |".$fecha." |".$valor."|".$row5['concepto'];
	$array5[$id5] = $description5;
}
//array de empleados=''
$sql6='SELECT id_empleado, nombre FROM empleados WHERE  inactivo=0  AND vendedor=1';
$result6=_query($sql6);
$array6 =array(-1=>"Seleccione Vendedor");
$count6=_num_rows($result6);
for ($a=0;$a<$count6;$a++){
	$row6=_fetch_array($result6);
	$id6=$row6['id_empleado'];
	$description6=$row6['nombre'];
	$array6[$id6] = $description6;
}

?>

	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
					<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
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
						//VENTA
						$fecha_actual=date("Y-m-d");
						?>
				<div class="row">
					<div class="widget">

						<div class="row">
							 <div class="col-md-12 border_div">

								 <table class=" blueTable" id="inventable">
									 <thead class='thead1'>
										 <tr class='tr1'>
											<th class="text-success col5 th1" >Id.</th>
						 				 	<th class="text-success col20 th1">Nombre</th>
						 			 		<th class="text-success col5 th1">Exist.</th>
						 		 			<th class="text-success col10 th1">Pr. List</th>
						 	 				<th class="text-success col10 th1">Precio</th>
						 					<th class="text-success col10 th1">Cant.</th>
						 					<th class="text-success col10 th1">Subtotal</th>
						 					<th class="text-success col10 th1">$ Descto</th>
						 					<th class="text-success col10 th1">Total</th>
						 					<th class="text-success col10 th1">Acci&oacute;n</th>
											</tr>
										</thead>
										<tbody class='tbody1 tbody2 filasfactura'></tbody>
										<tfoot>
											<tr>
												<td class='col50' colspan=5 id='totaltexto'></td>
												<td class='col10' id='totcant'>0.00</td>
			  							<td class='col10' id='total_sin_descto'>$0.00</td>
											<td class='col10'  id='totdescto' >$0.00 </td>
											<td class='col10' id='total_gravado'>$0.00</td>
											<td class='col10'><input id="btnFact" type="button" class="btn btn-primary fa"  value="&#xf00c;"></td>
										</tr>
									</tfoot>
								</table>
							</div>
		</div>
		<div class="row">
					<div class="col-md-12 border_div">
											<div class="search-header">
												<table class="table-condensed table4">
													<tr>
														<td class='td_med'><label>Límite búsqueda</label></td>
														<td class='td_peq'><input type="text"  class=' input_header_panel'  id="limite" value=400 /></td>
														<td class='td_med'><label>Reg. Encontrados</label></td>
														<td class='td_peq'>	<input type="text"  class='input_header_panel' id='reg_count' value=0 readOnly /></td>
														<td class='td_med'><a class="btn btn-primary" data-target="#viewModal1" data-toggle="modal" data-refresh="true" href="consultar_stock.php" id="cstok" ><i class="fa fa-search"></i> Ver Inventario F4</a></td>
														<td class='td_med'><button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button></td>
													</tr>
												</table>
												<table class="table-condensed table4">
													<tr>
														<td class='td_gde'><input type="text" id="keywords" class='form-control' placeholder="Descripción"/></td>
														<td class='td_med'> <input type="text" id="barcode" class='form-control' placeholder="Código Barra" /></td>
														<td class='td_med'><input type="text" id="marca" class='form-control' placeholder="marca" /></td>
														<td class='td_peq'> <input type="text" id="modelo" class='form-control' placeholder="modelo" /></td>
														<td class='td_peq'> <input type="text" id="serie" class='form-control' placeholder="serie" /></td>
													</tr>
												</table>
											</div>
										</div>
			</div> <!-- <div class="row"> -->
			<div class="row"  id="content">
				<div class="col-md-12 border_div">
					<div class="loading-overlay col-md-6">
						<div class="overlay-content " id='reg_count0'>Cargando.....</div>
					</div>
					<table class="greyTable" id='loadtable'>
					<thead class='thead1'>
						<tr class='tr1'>
						<th class="text-success col10 th1" >Código</th>
						<th class="text-success col40 th1">Nombre</th>
						<th class="text-success col10 th1">Precio</th>
						<th class="text-success col10 th1">Marca</th>
						<th class="text-success col10 th1">Modelo</th>
						<th class="text-success col10 th1">Serie</th>
						<th class="text-success col10 th1">% Desc.</th>
						</tr>
					</thead>
					<tbody class='tbody1 tbody4' id="mostrardatos"></tbody>
				</table>
			</div>
		</div>
		<!-- --><!--reordenamiento header factura-->
		<div class="row" id="datos_encabezado">
			<div class="col-md-12 border_div">
				<div class="search-header">
					<table class="table-condensed table4">
						<tr>
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
																					<td class='td_peq0'><label>Doc.</label></td>
																					<td class='td_med2'>
																						<?php
																						$nombre_select0="select_documento";
																						$idd0=-1;
																						$style='';
																						$select0=crear_select2($nombre_select0,$array3,$idd0,$style);
																						echo $select0;
																						?>
																					</td>
																					<td class='td_med1'>
																					<input type='text' placeholder='Num. Doc' class='form-control' id='numero_doc2' name='numero_doc2'>
																					</td>

								 													</tr>
								 												</table>

																				<table class="table-condensed table4">
								 													<tr>
																						<td class='td_peq'><label>Fecha</label></td>
																						<td class='td_med0'>
																							<input type='text' placeholder='Fecha' class='datepick form-control' value='<?php echo $fecha_actual;?>' id='fecha2' name='fecha2'>
																						</td>
																					<td class='td_med0'>
																						<input type="text" name="ver_vales" id="ver_vales" placeholder='Aplicar Vale ' class="form-control">
																						<input type="hidden" name="select_vales" id="select_vales">
																					</td>
																					<!--td class='td_peq'><label>Vale Reserva</label></td-->
																					<td class='td_med0'>
																						<input type="text" name="ver_reserva" id="ver_reserva" placeholder='Aplicar Reserva' class="form-control">
																						<input type="hidden" name="select_reserva" id="select_reserva">
																					</td>
																					<!--td class='td_peq'><label>Vendedor</label></td-->
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
								 													</tr>
								 												</table>
																					<table class="table-condensed table4">
																					<tr>
																					<td class="text-success altura_td"  id='tipo_vale'><strong>* VALOR VALE $:</strong></td>
																						<td class="text-success altura_td"  id='valor_vale'>0.0</td>

																					<td class="text-success  altura_td"   id='tipo_reserva'><strong>* VALOR RESERVA $:</strong></td>
																					<td class="text-success altura_td" id='valor_reserva'>0.0</td>

																						<td class="text-warning altura_td"><strong>TOTAL $:</strong></td>
																						<td class="red1 altura_td"  id='total_final'></td>

																					<td class="text-navy altura_td"><strong>A PAGAR $:</strong></td>
																						<td class="text-navy altura_td"  id='monto_pago'></td>
																					</tr>
																				</table>

																		</div>

														</div>
			</div>
<!--datos cliente ex encabezados -->
 </div> <!-- /widget -->
					<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
					<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename;?>">
          <input type="hidden" name="process" id="process" value="insert">

																		<!-- Modal -->
	  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		   <div class="modal-dialog">
		   <div class="modal-content modal-md">
			  <div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
							</div>
							<div class="modal-body">
							<div class="wrapper wrapper-content  animated fadeInRight">
								<div class="row">
									<input type='hidden' name='id_factura' id='id_factura' value=''>
									<input type='hidden' name='id_garantia' id='id_garantia' value=''>
									<div class="col-md-6">
													<div class="form-group">
																<label><h5 class='text-navy'>Numero factura Interno:</h5></label>
														</div>
									</div>
									<div class="col-md-6" >
													<div class="form-group" id='fact_num'></div>
									</div>
						</div>
								<div class="row">
											<div class="col-md-6">
			                        <div class="form-group">
			                              <label><h5 class='text-navy'>Facturado $:</h5></label>
			                          </div>
											</div>
											<div class="col-md-6">
			                        <div class="form-group">
			                            <input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
			                          </div>
											</div>
											<div class="col-md-6">
			                        <div class="form-group">
			                              <label><h5 class='text-navy'>A Pagar $:</h5></label>
			                          </div>
											</div>
											<div class="col-md-6">
			                        <div class="form-group">
			                            <input type="text" id="a_pagar" name="a_pagar" value=0  class="form-control decimal" readonly >
			                          </div>
											</div>
								</div>
								<div class="row" id='garantia_cte'>
											<div class="col-md-6">
															<div class="form-group">
																		<label><strong><h5 class='text-danger'>Nombre Para Garantia: </h5></strong></label>
																</div>
											</div>
											<div class="col-md-6">
															<div class="form-group">
																	 <input type="text" id='nombre_garantia' name='nombre_garantia' value=''  class="form-control" >
															</div>
													</div>
										</div>
									<div class="row" id='fact_cf'>
												<div class="col-md-6">
				                        <div class="form-group">
				                              <label><strong><h5 class='text-danger'>Numero Factura o Credito Fiscal: </h5></strong></label>
				                          </div>
												</div>
												<div class="col-md-6">
				                        <div class="form-group">
				                             <input type="text" id='num_doc_fact' name='num_doc_fact' value=''  class="form-control" >
																</div>
														</div>
											</div>
											<div class="row" id='ccf'>
												<div class="col-md-6">
																<div class="form-group">
																			<label><strong><h5 class='text-navy'>Nombre de Cliente Credito Fiscal: </h5></strong></label>
																	</div>
												</div>
												<div class="col-md-6">
																<div class="form-group">
																		 <input type="text" id='nombreape' name='nombreape' value=''  class="form-control" >
																	</div>
												</div>
											<div class="col-md-6">
											 <div class="form-group">
												 <label>NIT Cliente</label>
											 </div>
											</div>
											<div class="col-md-6">
														 <div class="form-group">
																	 <input type='text' placeholder='NIT Cliente' class='form-control' id='nit' name='nit' value=''>
															 </div>
											</div>


											<div class="col-md-6">
											<div class="form-group">
												<label>Registro Cliente(NRC)</label>
											</div>
											</div>
											<div class="col-md-6">
											<div class="form-group">
												<input type='text' placeholder='Registro (NRC) Cliente' class='form-control' id='nrc' name='nrc' value=''>
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
											<!--/div>
											<div class="row"-->
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
						<button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
					</div>
					</div>
				</div>
			</div><!-- Modal -->
			<div class="modal fade" id="viewModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
			   		<div class="modal-content modal-md">
			   		</div>
			   	</div>
			</div>
    </div>
    </div>

<?php

	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

?>
<!--/div-->
</div>
</div>
</div>
<?php
include_once ("footer.php");
echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
echo "<script src='js/plugins/awesomplete/awesomplete.js'></script>";
echo "<script src='js/funciones/ventas5.js'></script>";
}

function insertar(){
	//ACA se va insertar a las sig tablas:
	// factura y detale_factura, correlativos, stock y kardex ,(otras probables cuenta_cliente,)
	$cuantos = $_POST['cuantos'];
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$numero_doc = $_POST['numero_doc'];
	$alias_tipodoc = $_POST['alias_tipodoc'];
	$id_tipo_pago = $_POST['id_tipo_pago'];
	$id_cliente= $_POST['id_cliente'];
	$total_venta = $_POST['total_venta'];
	$total_gravado= $_POST['total_gravado'];
  	$total_exento= $_POST['total_exento'];
  	$total_iva= $_POST['total_iva'];
	$total_descuento= $_POST['total_descuento'];
  	$total_retencion= $_POST['total_retencion'];
  	$total_percepcion= $_POST['total_percepcion'];
  	$array_json=$_POST['json_arr'];

	$id_usuario=$_SESSION["id_usuario"];
	$id_empleado=$id_usuario;
	$id_sucursal= $_SESSION['id_sucursal'];
	$numero_dias= $_POST['numero_dias'];
	$items= $_POST['items'];
	//$pares = $_POST['pares'];
  	$monto_pago= $_POST['monto_pago'];
  	$id_vale= $_POST['id_vale'];
  	$id_reserva= $_POST['id_reserva'];
  	$valor_vale= $_POST['valor_vale'];
  	$valor_reserva= $_POST['valor_reserva'];
	$id_vendedor= $_POST['id_vendedor'];
	$insertar1=false;
	$insertar2=false;
	$insertar3=false;

	$saldo = 0;
	$credito = 0;
	$abono = 0;
	if($id_tipo_pago==3)
	{
		$credito = 1;
		$saldo=$total_venta;
	}

  	$fecha_vencimiento=sumar_dias_Ymd($fecha_movimiento, $numero_dias);

	if ($cuantos>0){
		 _begin();
		 $hora=date("H:i:s");
		 $fecha_ing=date('Y-m-d');

		 //turno de caja
		 $sql_turno="SELECT * FROM apertura_caja
		 WHERE  vigente=1 AND  id_sucursal='$id_sucursal'";
		 $result_turno= _query($sql_turno);
		$rows_turno=_fetch_array($result_turno);
		$nrows_turno=_num_rows($result_turno);
		$turno=$rows_turno['turno'];
		$id_apr = $rows_turno["id_apertura"];

		 if($alias_tipodoc!='TIK'){
		$numero_doc=$numero_doc;
		 }
		 else {
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
		 }
		 $sql_ventas="SELECT * FROM factura WHERE numero_doc='$numero_doc'
		  	AND  fecha_doc='$fecha_movimiento'
			AND id_cliente='$id_cliente'
			AND alias_tipodoc='$alias_tipodoc'
		  	AND id_sucursal='$id_sucursal'";
			$result_fc=_query($sql_ventas);
			$row_fc=_fetch_array($result_fc);
			$nrows_fc=_num_rows($result_fc);
			if($nrows_fc==0)
			{
				$table_fc= 'factura';
				$form_data_fc = array(
				'id_cliente' => $id_cliente,
				'alias_tipodoc'=>$alias_tipodoc,
				'fecha_doc' => $fecha_movimiento,
				'numero_doc' => $numero_doc,
				'total_retencion'=>$total_retencion,
				'total_percepcion'=>$total_percepcion,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'total_iva' => $total_iva,
				'total_gravado' => $total_gravado,
				'total_exento' => $total_exento,
				'descuento' => $total_descuento,
				'total' => $total_venta,
				'total_bruto' => $total_gravado,
				'hora' => $hora,
				'tipopago'=> $id_tipo_pago,
				'id_sucursal' => $id_sucursal,
				'finalizada' => 1,
        'monto_pago' => $monto_pago,
        'valor_vale' => $valor_vale,
				'turno'=>   $turno,
				'id_vendedor'=> $id_vendedor,
				'id_apertura' => $id_apr,
				'credito' => $credito,
				'abono'=>$abono,
				'saldo' => $saldo,

				);
				$insertar_fc = _insert($table_fc,$form_data_fc );
				$id_fact= _insert_id();
				echo _error();
			}


      //ver si se usa vale ponerlo como cobrado en mov_caja
	  if($id_vale != "" && $id_vale>0)
	  {
	    $table_vale='mov_caja';
	    $form_data_vale = array(
	 	 	'cobrado' =>1,
	    );
	    $where_vale="WHERE id_movimiento='$id_vale'";
	    $insertar_vale = _update($table_vale, $form_data_vale,$where_vale);
	  }
	  if($id_reserva != "" && $id_reserva>0)
	  {
	    $table_reserva='mov_caja';
	    $form_data_reserva = array(
	 	 	'cobrado' =>1,
	    );
	    $where_reserva="WHERE id_movimiento='$id_reserva'";
	    $insertar_reserva = _update($table_reserva, $form_data_reserva,$where_reserva);
	  }

		$array = json_decode($array_json,true);
		foreach ($array as $fila)
		{
			 if( $fila['precio_venta']>0 && $fila['cantidad']>0 )
			 {
				 $id_producto=$fila['id'];
				 $cantidad=$fila['cantidad'];
				 $precio_venta=$fila['precio_venta'];
				 $descto=$fila['descto'];
				 $subt_exento=$fila['subt_exento'];
				 $subt_gravado=$fila['subt_gravado'];
				 if(	$subt_exento>0){
					 $subtotal=$subt_exento;
					 $exento=1;
				 }
				 else{
					 $subtotal=$subt_gravado;
					 $exento=0;
				 }
				 $sql_producto="SELECT *
				 FROM productos WHERE id_producto='$id_producto'";

				 $result_prod=_query($sql_producto);
				 $row_prod=_fetch_array($result_prod);
				 $costo=$row_prod['ultcosto'];
				 $modelo=$row_prod['modelo'];
				 $tiene_garantia=$row_prod['tiene_garantia'];
				 $dias_garantia=$row_prod['dias_garantia'];


				 if ($tiene_garantia==1){
					 $vendido=1;
				 }else{
					 $vendido=0;
				 }
				 $subt_descto=$precio_venta*$descto;
				 $form_data_dc = array(
			 			'idtransace' => $id_fact,
			 			'id_producto' => $id_producto,
			 			'cantidad' => $cantidad,
			 			'precio' => $precio_venta,
						'costo' => $costo,
			 			'descuento' => $descto, //es el valor descontado por item
			 			'gravado' => $subt_gravado,
			 			'exento' => $subt_exento,
			 		);
			 	//detalle de factura
			 	$table_dc= 'detalle_factura';
		 		$insertar_dc = _insert($table_dc,$form_data_dc );

		 // Insertar en stock
		 	$sql_stock="SELECT id_stock, id_producto, existencias, minimo, id_sucursal, retirado, comentario
		 		FROM stock
		 		WHERE  id_producto='$id_producto'
		 		AND id_sucursal='$id_sucursal'
		 		";
		 	$result_st=_query($sql_stock);
		 	$row_st=_fetch_array($result_st);
		 	$nrows_st=_num_rows($result_st);
		 	$comentario=$alias_tipodoc."  ".$numero_doc." ".$fecha_movimiento." ".$hora;

		 	$table_stock= 'stock';
	 		if($nrows_st>0){
				$existencia_actual=$row_st['existencias'];
		 		$nueva_existencia=$existencia_actual-$cantidad;
				if ($nueva_existencia<0){
					$nueva_existencia=0;
				}
		 		$form_data_st = array(
			 		'existencias' => $nueva_existencia,
			 		'id_sucursal' => $id_sucursal,
			 	'comentario' => $comentario,
				'vendido'=> $vendido
		 		);
		 		$where_clause_st=" WHERE  id_producto='$id_producto'
		 		AND id_sucursal='$id_sucursal'
		 		";
		 		$insertar_st = _update($table_stock,$form_data_st,$where_clause_st );
	 	}

		// Insertar en kardex
		$table_kardex= 'kardex';
		$where_clause_ka="WHERE  id_producto='$id_producto'
		AND fechadoc='$fecha_movimiento'
		AND id_cliente='$id_cliente'
		AND alias_tipodoc='$alias_tipodoc'
		AND numero_doc='$numero_doc'
		AND id_sucursal_origen='$id_sucursal'
		";
		$sql_ka="SELECT *
			FROM $table_kardex ".$where_clause_ka;


			$result_ka=_query($sql_ka);
			$row_ka=_fetch_array($result_ka);
			$nrows_ka=_num_rows($result_ka);

			if($nrows_ka>0){

				$form_data_ka = array(
					'ultcosto' =>  $precio_venta,
					'cantidads' => $cantidad,
					'stock_anterior' =>$existencia_actual,
					'stock_actual' => $nueva_existencia,
					'fechadoc' => $fecha_movimiento,
					'id_empleado'=> $id_empleado,
				);

				$insertar_ka = _update($table_kardex, $form_data_ka, $where_clause_ka );
		}
		else{
			$form_data_ka = array(
			'id_producto' => $id_producto,
			'id_cliente' => $id_cliente,
			'id_transacc' => $id_fact,
			'id_sucursal_origen' => $id_sucursal,
			'numero_doc'=>$numero_doc,
			'alias_tipodoc'=>$alias_tipodoc,
			'ultcosto' =>  $precio_venta,
			'cantidads' => $cantidad,
			'stock_anterior' =>$existencia_actual,
			'stock_actual' => $nueva_existencia,
			'fechadoc' => $fecha_movimiento,
			'id_empleado'=> $id_empleado,
			);
			$insertar_ka = _insert($table_kardex, $form_data_ka);
			}
			//garantia detalle por cada item que tenga garantia
			//validar si tiene garantia y crear el doc de Garantia e insertar junto con el detalle
				$id_garantia=-1;
			if($tiene_garantia){

			$sql_gc="SELECT id_garantia, id_cliente, fecha, numero_doc, alias_tipodoc, total, anulada,
			id_sucursal, finalizada, id_empleado, nombre_garantia
			FROM garantia_cliente
			WHERE id_sucursal='$id_sucursal' AND numero_doc='$numero_doc'";
			$result_gc=_query($sql_gc);
			$row_gc=_fetch_array($result_gc);
			$nrows_gc=_num_rows($result_gc);
			if($nrows_gc==0){
				$t_gc="garantia_cliente";
				$fd_gc = array(
					'id_cliente' => $id_cliente,
					'id_sucursal' => $id_sucursal,
					'numero_doc'=>$numero_doc,
					'fecha' => $fecha_movimiento,
					'alias_tipodoc'=>$alias_tipodoc,
					'id_empleado'=> $id_empleado,
				);
				$insertar_gc = _insert($t_gc,$fd_gc);
				$id_garantia= _insert_id();
		  }

			$sql_gcd="SELECT id_garantia_cte_det, id_garantia, id_producto, fecha_ini,
			fecha_fin, dias_garantia FROM garantia_cte_det
			WHERE id_garantia='$id_garantia'
			AND id_producto='$id_producto'";
			$result_gcd=_query($sql_gcd);
			$row_gcd=_fetch_array($result_gcd);
			$nrows_gcd=_num_rows($result_gcd);
			if($nrows_gcd==0){
      	$fecha_fin_gar=sumar_dias_Ymd($fecha_movimiento, $dias_garantia);
				$t_gcd="garantia_cte_det";
				$fd_gcd = array(
					' id_garantia'=>$id_garantia,
					'id_producto' => $id_producto,
					'fecha_ini' => $fecha_movimiento,
					'fecha_fin' => $fecha_fin_gar,
					'dias_garantia'=>$dias_garantia,
				);
				$insertar_gcd = _insert($t_gcd,$fd_gcd);
			}
			}
		} //foreach
 }

	}//if $cuantos>0

	if ($insertar_fc ){
		_commit(); // transaction is committed
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Registro de Inventario Actualizado !';
		$xdatos['process']='insert';
		$xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
		$xdatos['factura']=$id_fact;
		$xdatos['numero_doc']=$numero_doc;
		$xdatos['id_garantia']=$id_garantia;
	}
	else{
		_rollback(); // transaction not committed
		 $xdatos['typeinfo']='Error';
		 $xdatos['msg']='Registro de Inventario no pudo ser Actualizado !';
		 $xdatos['guardar']="compras: ".$insertar_fc." det compra: ".$insertar_dc." ";
	 }
	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$id_usuario=$_SESSION["id_usuario"];
  $id_sucursal=$_SESSION['id_sucursal'];
	$sql_user="select * from usuario where id_usuario='$id_usuario'";

	$sql2="select * from stock where id_producto='$id_producto' and id_sucursal ='$id_sucursal'";
	$stock2=_query($sql2);
	$row2=_fetch_array($stock2);
	$nrow2=_num_rows($stock2);
	$existencias=$row2['existencias'];

	$sql3="select p.* from productos AS p
		where id_producto='$id_producto'
		";
	$result3=_query($sql3);
  $count3=_num_rows($result3);
	if($count3>0){
	$row3=_fetch_array($result3);
	$cp=$row3['costopro'];
	$pv_base=$row3['precio1'];
	$precio1=$row3['precio1'];
	$precio2=$row3['precio2'];
	$precio3=$row3['precio3'];
	$descuento=$row3['descuento'];
	$exento=$row3['exento'];
	$descripcion=$row3['descripcion'];
	$marca=$row3['marca'];
	$modelo=$row3['modelo'];
	$serie=$row3['serie'];
	$tg=$row3['tiene_garantia'];
  $precios=array();
	if ($precio1>0) {
			$precios[] = $precio1; // agrego el precio1
	}
	if ($precio2>0) {
			$precios[] = $precio2; // agrego el precio2
	}
	if ($precio3>0) {
			$precios[] = $precio3; // agrego el precio1
	}
  //crear el select2 de precios
	$nombre_select="select_precios";
	$id=-1;
	$style='';
	$select=crear_select2($nombre_select,$precios,$id,$style,' sel_prec');
	//echo $select0;

	$xdatos['descrip'] =$descripcion;
	$xdatos['costo_prom'] = $cp;
	$xdatos['pv_base'] = $pv_base;
	$xdatos['existencias'] = $existencias;
	$xdatos['marca'] = $marca;
	$xdatos['modelo'] = $modelo;
	$xdatos['serie'] = $serie;
	$xdatos['exento'] = $exento;
	$xdatos['descuento'] = $descuento;
	$xdatos['precios_vta']= $precios;
	$xdatos['precio1']= $precio1;
	$xdatos['precio2']= $precio2;
	$xdatos['precio3']= $precio3;
	$xdatos['precios']= $precios;
	$xdatos['tg']= $tg;
	$xdatos['select_precios'] = $select;
	echo json_encode($xdatos); //Return the JSON Array
 }
}
function buscarBarcode(){
	$query = trim($_POST['id_producto']);
	$sql0="SELECT id_producto as id, descripcion, barcode, marca,modelo,serie FROM productos  WHERE barcode='$query'";
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
	$cadena_salida= $enteros_txt.$dolar." con ".$decimal."/100 ctvs.";
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
    $marca = $_POST['marca'];
    $modelo= $_POST['modelo'];
    $serie= $_POST['serie'];
    $barcode= $_POST['barcode'];
		$limite= $_POST['limite'];

    $sqlJoined="SELECT pr.id_producto,pr.barcode, pr.descripcion, pr.precio1, pr.precio2, pr.precio3,
		 pr.costopro,  pr.exento, pr.marca,  pr.modelo,pr.serie, pr.tiene_garantia, pr.descuento,
		 st.existencias
		 FROM productos AS pr,  stock AS st
		";
    $sqlParcial=get_sql($keywords, $barcode, $marca, $modelo, $serie,$limite);
    $sql_final= $sqlJoined." ".$sqlParcial." ";
    $query = _query($sql_final);

		$num_rows = _num_rows($query);
		$filas=0;
    if($num_rows > 0){
            while($row = _fetch_array($query)) {
                $id_producto = $row['id_producto'];
                $descripcion=$row["descripcion"];
                $marca = $row['marca'];
								$modelo = $row['modelo'];
								$serie = $row['serie'];
								$tiene_garantia = $row['tiene_garantia'];
								$exento = $row['exento'];
								$cp = $row['costopro'];
								$precio1 = $row['precio1'];
								$precio2 = $row['precio2'];
								$precio3 = $row['precio3'];
                $modelo = $row['modelo'];
								$serie2=$row['serie'];
								$barcode = $row['barcode'];
								$descuento = $row['descuento'];
								//$descuento = $row['descuento'];
								$btnSelect='<input type="button" id="btnSelect" class="btn btn-primary fa" value="&#xf00c;">';
						?>
				   <tr class='tr1'  tabindex="<?php echo $filas;?>">
					  <td class='col10 td1'><input type='hidden'  id='exento' name='exento' value='<?php echo $exento;?>'> <h5><?php echo $id_producto;?></h5></td>
            <td class='col40 td1'><?php echo $descripcion;?></td>
						<td class='col10 td1 td_success'><?php echo $precio1;?></td>
            <td class='col10 td1'><?php echo $marca;?></td>
            <td class='col10 td1'><?php echo $modelo;?></td>
						<td class='col10 td1'><?php echo $serie;?></td>
						<td class='col10 td1'><?php echo $descuento;?></td>

          <?php
					$filas+=1;
          }
				}
  echo '<input type="hidden" id="cuantos_reg"  value="'.$num_rows.'">';
}
function get_sql( $keywords, $barcode, $marca, $modelo, $serie,$limite){
	$andSQL='';
		$id_sucursal= $_SESSION['id_sucursal'];
 $whereSQL="  WHERE pr.id_producto=st.id_producto
	AND st.id_sucursal='$id_sucursal'
	AND st.existencias>0 ";

	$keywords=trim($keywords);
	//$andSQL.= " AND co.serie='$serie'";

	if(!empty($barcode)){
			$andSQL.= " AND  pr.barcode LIKE '{$barcode}%'";
	}
	else{
  if(!empty($keywords)){
  $andSQL.= "AND  pr.descripcion LIKE '%".$keywords."%'";
      if(!empty($marca)){
          $andSQL.= " AND pr.marca LIKE '{$marca}%' ";
      }
      if(!empty($modelo)){
          $andSQL.= " AND pr.modelo LIKE '%{$modelo}%'";
      }
		if(!empty($serie)){
					$andSQL.= " AND pr.serie='$serie'";
			}
  }

  if(empty($keywords)  && !empty($marca)){
		$andSQL.= "AND  pr.marca LIKE '".$marca."%' ";
		if(!empty($modelo)){
				 $andSQL.= " AND pr.modelo LIKE '%{$modelo}%'";
		}
		if(!empty($serie)){
				$andSQL.= " AND pr.serie='$serie'";
		}
   }
	 if(empty($keywords)  && empty($marca) && !empty($modelo)){
	 $andSQL.= " AND pr.modelo LIKE '%{$modelo}%'";
		if(!empty($serie)){
				$andSQL.= " AND pr.serie='$serie'";
			}
	 }
	 if(empty($keywords)  && empty($marca) && empty($modelo) && !empty($serie)) {
			$limite=1000;
	 		$andSQL.= " AND pr.serie='$serie'";
 		}
	}

	$orderBy=" ";
	$limitSQL=" LIMIT ".$limite;
	$orderBy=" ORDER BY pr.id_producto,pr.descripcion, pr.barcode,pr.marca,pr.modelo,pr.serie ";

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
            $giro=$row["giro"];
            $registro=$row["nrc"];
						$direccion=$row["direccion"];

        }
    }
    $xdatos['nit']= $nit;
    $xdatos['registro']= $registro;
		$xdatos['nombreape']=   $nombre;
    echo json_encode($xdatos); //Return the JSON Array
}
//Impresion
function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
	$totalfact = $_POST['total'];
  $tipo_impresion= $_POST['tipo_impresion'];
  $id_factura= $_POST['num_doc_fact'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$numero_factura_consumidor = $_POST['numero_factura_consumidor'];
  $cambio_fin=$_POST['cambio_fin'];
	$efectivo_fin=$_POST['efectivo_fin'];
	$id_garantia= $_POST['id_garantia'];
	$nombre_garantia= $_POST['nombre_garantia'];
	//agregado lo de garantia !!!!!!!!!!!!!!!!!!!!!!!!!!!
	if ($id_garantia!=-1){
		$t_gc= 'garantia_cliente';

		$fd_gc = array(
				'nombre_garantia' => $nombre_garantia,
		);
		$wc_gc="WHERE id_garantia='$id_garantia' AND id_sucursal='$id_sucursal'";
		$actualizar0 = _update($t_gc,$fd_gc,$wc_gc);
	}
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
		$sql_pt="SELECT * FROM pago_tarjeta  WHERE idtransace='$id_factura'";
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
					'monto' => $totalfact,
			);
			$where_clause="WHERE idtransace='$id_factura'";
			$actualizar = _insert($table_pt,$form_data_pt);
			$id_pago= _insert_id();
		}
	}
	if   ($tipo_impresion=='CCF'){
		$tipo_entrada_salida="CREDITO FISCAL";
		$nit= $_POST['nit'];
		$nrc= $_POST['nrc'];
		$nombreape= $_POST['nombreape'];
	}
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$sql_fact="SELECT * FROM factura WHERE idtransace='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$fecha_movimiento=$row_fact['fecha_doc'];
		//$total=$row_fact['total'];
		$table_fact= 'factura';

		$form_data_fact = array(
			'finalizada' => '1',
			'id_pago_tarjeta' => $id_pago,
			'efectivo' => $efectivo_fin,
			'cambio' => $cambio_fin,
		);

		$where_clause="WHERE idtransace='$id_factura'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
	}
  $headers=""; $footers="";
	if ($tipo_impresion=='TIK'){
		$info_facturas=print_ticket($id_factura,$tipo_impresion);
		$sql_pos="SELECT *  FROM config_pos  WHERE id_sucursal='$id_sucursal' AND alias_tipodoc='TIK'";

		$result_pos=_query($sql_pos);
		$row1=_fetch_array($result_pos);

		$headers=$row1['header1']."|".$row1['header2']."|".$row1['header3']."|".$row1['header4']."|".$row1['header5']."|";
		$headers.=$row1['header6']."|".$row1['header7']."|".$row1['header8']."|".$row1['header9']."|".$row1['header10'];
		$footers=$row1['footer1']."|".$row1['footer2']."|".$row1['footer3']."|".$row1['footer4']."|".$row1['footer5']."|";
		$footers.=$row1['footer6']."|".$row1['footer7']."|".$row1['footer8']."|".$row1['footer8']."|".$row1['footer10']."|";
	}
	if ($tipo_impresion=='COF'){
		$info_facturas=print_fact($id_factura,$tipo_impresion);
	}
	if ($tipo_impresion=='CCF'){
		$info_facturas=print_ccf($id_factura,$tipo_impresion,$nit,$nrc,$nombreape);
	}
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
function buscar_reserva()
{
    $numero = $_POST['numero'];
    $sql0="SELECT idtransace, id_movimiento, cobrado, valor, concepto FROM mov_caja
		WHERE numero_doc='$numero' AND alias_tipodoc = 'RES'";
	$result0 = _query($sql0);
	if(_num_rows($result0)>0)
	{
		$xdatos["typeinfo"] = "Success";
		$row0 = _fetch_array($result0);
		$xdatos["valor"] = $row0["valor"];
		$xdatos["concepto"] = $row0["concepto"];
		$xdatos["id_vale"] = $row0["id_movimiento"];
		if($row0['cobrado'])
		{
			$xdatos["cobrado"] = "Si";
		}
		else
		{
			$xdatos["cobrado"] = "No";
			$id_reserva =$row0['idtransace'];
		  	$sql1="SELECT dr.id_producto, dr.cantidad
			FROM detalle_reservas AS dr
			WHERE dr.id_reserva='$id_reserva'";
		    $result1 = _query($sql1);
		    $array_prod = array();
		    $numrows= _num_rows($result1);
		    $cantidades = "";
		    $ids = "";
		    for ($i=0;$i<$numrows;$i++)
		    {
		        $row = _fetch_array($result1);
		        $id_producto =$row['id_producto'];
		        $cantidad =$row['cantidad'];
		        $cantidades.=$cantidad.",";
		        $ids.=$id_producto.",";
		    }
		    $xdatos["ids"] = $ids;
		    $xdatos["cantidades"] = $cantidades;
		}
	}
	else
    {
    	$xdatos["typeinfo"] = "Error";
    	$xdatos["msg"] = "No se encontro ninguna reserva que coincida con este numero";
    }
    //$xdatos['array_prod']=$array_prod;
    echo json_encode($xdatos); //Return the JSON Array
}
function buscar_vale()
{
    $numero = $_POST["numero"];
    $sql = _query("SELECT * FROM mov_caja WHERE numero_doc ='$numero' AND alias_tipodoc ='DEV'");
    if(_num_rows($sql)>0)
    {
    	$xdatos["typeinfo"] = 'Success';
    	$datos = _fetch_array($sql);
    	if($datos["cobrado"])
    	{
    		$xdatos["cobrado"] = "Si";
    	}
    	else
    	{
    		$xdatos["cobrado"] = "No";
    		$xdatos["valor"] = $datos["valor"];
    		$xdatos["id_vale"] = $datos["id_movimiento"];
    		$xdatos["fecha"] = $datos["fecha"];
    		$xdatos["concepto"] = $datos["concepto"];
    	}
    }
    else
    {
    	$xdatos["typeinfo"] = "Error";
    	$xdatos["msg"] = "No se encontro ningun vale que coincida con este numero";
    }
    //$xdatos['array_prod']=$array_prod;
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
	case 'buscar_reserva':
			buscar_reserva();
			break;
	case 'buscar_vale':
			buscar_vale();
			break;
	}
}
?>
