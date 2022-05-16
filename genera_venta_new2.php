<?php
include_once "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
		$title="Venta e Impresión Factura Individual";
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
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style_fact2.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$sql="SELECT * FROM producto";
	$result=_query($sql);
	$count=_num_rows($result);
	$id_usuario=$_SESSION["id_usuario"];

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
?>

    <div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-2"></div>
    </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
           <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
						<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						echo "<div class='ibox-title'>";
						echo "<div class='pull-left'>";
            echo "<h3>$title</h3>";
            ?>
					</div>
					<div class='pull-right'>
					<div class="alert-group">
					<label class="alert style1 alert-primary"><input type='checkbox' id='busca_descrip_activo' name='busca_descrip_activo' value='1'></label>
					<label class="alert style1 yellow-bg"><i class="fa fa-search-plus"></i>&nbsp;&nbsp;<span class="bold">F2 Activar Barcode </span></label>
					<label class="alert style1 lazur-bg"><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<span class="bold"> F8 Activar Descripción</span></label>
					<label class="alert style1 red-bg"><i class="fa fa-save"></i> <span class="bold">F9 Guardar Factura</span></label>
					</div>
					</div>
					<p>&nbsp;&nbsp;</p>
					<p>&nbsp;&nbsp;</p>
					</div>
					<div class="ibox-content">
					 <div class="row">
						 <div id='form_datos_cliente'>
                <div class="col-md-4">
                  <div class="form-group has-info">
								  <label>Seleccione cliente</label><br>

							<?php
								echo"<select  name='id_cliente' id='id_cliente'  class='form-control'>";
								$qcliente=_query("SELECT * FROM cliente  ORDER BY apellido");
								while($row_cliente=_fetch_array($qcliente))
                {
                       $id_cliente=$row_cliente['id_cliente'];
                       $nombre_cliente=$row_cliente['nombre']." ".$row_cliente['apellido'];
                       echo "<option value='$id_cliente'>$nombre_cliente</option> ";
                }
							echo "</select>";
							echo "</div>"; //<div class='form-group'>
              echo  "</div>";// <div class="col-md-4">
              echo "</div>";//<!--<div id='form_datos_cliente'> -->
							?>
							<div class="col-md-2">&nbsp;</div>
							<?php
               $fecha_actual=date("Y-m-d");
						echo "<div class='col-md-3'>";
							echo "<div class='form-group has-info'>
										<label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'>
										</div>";
						echo "</div>";//<div class='col-md-4'>"
            //  echo "</div>";//row
					//echo "</div>";// <div class="ibox-content">
						?>
							<!--selecciona tipo de impresion -->
								<div class="col-md-3">
									<div class="form-group has-info">
										<label>Seleccione tipo Impresion</label><br>
										<select  name='tipo_impresion' id='tipo_impresion'  class='form-control'>
										<option value='TIK' selected>TICKET</option>
										</select>
									</div>
								</div>
							</div><!--div class="row"-->
							</div><!--div class="ibox-content"-->
								<!--div class="col-md-3">
									<div class="form-group has-info single-line">
							 			<label>Numero Factura</label>
					  				<input type='text' placeholder='Numero Factura' class='form-control' id='numfact' name='numfact' value='' readOnly>
					  			</div>
								</div-->
							<!--/div-->


						<div class="ibox-content">
							<section>
								<div class="panel">
								<div style="margin:7px">
									<div class="col-xs-6">
										<!--div class="form-group has-info">
									 			<label><h3 class="text-success">Activar busqueda: (por Descripcion F8 -- por Barcode  F2)&nbsp;&nbsp;<input type='checkbox'  id='busca_descrip_activo' name='busca_descrip_activo' value='1' /> </h3></label>
									 </div-->
									<div class="widget style1 text-center">
									<div class='form-group has-info single-line'><label id='buscar_habilitado'>Buscar Producto o Servicio </label>
										<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto o servicio a facturar"  data-provide="typeahead" style="border-radius:0px" >
										<input  type="text" id="producto_buscar2" name="producto_buscar2" size="20" class="form-control" placeholder="Ingrese  barcode producto o servicio a facturar"  style="border-radius:0px" >
									</div>
									</div>
									</div>

									</div>
									<div class="col-xs-6">
										<div class="widget style1 gray-bg text-center">
											<div class="m-b-sm" id='imagen'>
												<img alt="image" class="img-circle" src="img/productos/white.png" width="200px" height="100px" border='1'>
												<p class="font-bold">&nbsp;</p>
											</div>
										</div>
									</div>
								<!--/div-->
						<div class="panel-body" style="padding:0px">
							<!-- -->
								<!--div class="table-responsive m-t"-->
									<table class="table fact_table" id="inventable">
									<thead class="panel-title">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Stock</th>
										<th class='success'>Precios</th>
										<th class='success'>Pr-Venta</th>
										<th class='success'>Cant. G</th>
										<th class='success'>SubCant</th>
										<th class='success'>Subtotal</th>
										<th class='success'>Combo</th>
										<th class='success'>Acci&oacute;n</th>
										</tr>
									</thead>
									<tbody>
										<tr id="filainicial">
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td  class="thick-line text-right" id='total_dinero'>0</td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										</tr>
									</tfoot>
								</table>
								<div class='row'>
									<div class="col-md-10 well m-t"  id='totaltexto'><strong>Son:</strong> </div>

									<div class="col-md-2">
										<div class="title-action" id='botones'>
											<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button>
										</div>
									</div>
								</div>
							<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
							<input type='hidden' name='facturacion' id='facturacion' value='<?php echo $filename;?>'>


						</div><!--div class="panel-body" style="padding:0px"-->
						</div><!--div class="panel"-->
							<!-- -->

					</section>
						</div><!--div class='ibox-content'-->
	<!-- Modal -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-sm">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
				</div>
				<div class="modal-body">
				<div class="wrapper wrapper-content  animated fadeInRight">
					<div class="row">
						<div class='col-md-6'><h4>Factura:</h4></div>
						<div id='fact_num' class='col-md-6'></div>
					</div>

					<!--div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label><h4 class='text-danger'>Num. Doc</h4></label>
                            </div>
						</div>
						<div class="col-md-6">
															<div class="form-group">
																<input type="text" id='num_doc_fact' name='num_doc_fact' value="" class="form-control"></div>
															</div>
						</div-->

					<div class="row">
								<div class="col-md-6">
                                  <div class="form-group">
                                      <label><h4 class='text-navy'>Facturado $ </h4></label>
                                     </div>
								</div>
								<div class="col-md-6">
                                  <div class="form-group">
                                      <input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly >
                                  </div>
								</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label><h4 class='text-danger'>Efectivo $</h4></label>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal">
                            </div>
						</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label><h4 class='text-success'>Cambio $</h4></label>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly >
                            </div>
						</div>
						<div class="col-md-6">
							<div class="form-group" id='mensajes'></div>
						</div>
					</div>
				</div>
			</div>
				<div class="modal-footer">
					<!--button type="button" class="btn btn-primary" id="btnPrintFact"><i class="fa fa-print"></i> Imprimir</button-->
					<button type="button" class="btn btn-primary" id="btnPrintFact"><i class="fa fa-print"></i> Imprimir</button>
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
echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
echo "<script src='js/funciones/genera_venta_new2.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function numero_tiquete($ult_doc,$tipo_doc){
	$ult_doc=trim($ult_doc);
	$len_ult_valor=strlen($ult_doc);
	$long_num_fact=10;
	$long_increment=$long_num_fact-$len_ult_valor;
	$valor_txt="";
	if ($len_ult_valor<$long_num_fact){
		for ($j=0;$j<$long_increment;$j++){
			$valor_txt.="0";
		}
	}
	else{
			$valor_txt="";
	}
	$valor_txt=$valor_txt.$ult_doc."_".$tipo_doc;
	return $valor_txt;
}
function insertar(){
	$cuantos = $_POST['cuantos'];
	//$stringdatos = $_POST['stringdatos'];
	$tipo_impresion= $_POST['tipo_impresion'];
	$array_json=$_POST['json_arr'];
	$id = '1';
	$id_usuario=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION["id_sucursal"];


	$fecha_movimiento= $_POST['fecha_movimiento'];
	$id_cliente=$_POST['id_cliente'];
	$total_venta = $_POST['total_ventas'];

	$insertar1=false;
	$insertar2=false;
	$insertar_fact=false;
	$insertar_fact_det=false;
	$insertar_numdoc =false;
	$insertar4 =false;
  $hora=date("H:i:s");
	$xdatos['typeinfo']='';
	$xdatos['msg']='';
	$xdatos['process']='';
$tipoprodserv='PRODUCTO';
		_begin();
		$sql="select * from ultimo_numdoc where id_sucursal='$id_sucursal'";
		$result= _query($sql);
		$rows=_fetch_array($result);
		$nrows=_num_rows($result);
		$ult_cof=$rows['ult_cof'];
		$ult_tik=$rows['ult_tik'];
		$ult_flc=$rows['ult_flc'];
		$id_sucursal=$rows['id_sucursal'];
		$ult_cof=$ult_cof+1;
		$ult_tik=$ult_tik+1;
		$ult_flc=$ult_flc+1;
		$table_numdoc="ultimo_numdoc";

		switch ($tipo_impresion) {
			case 'COF':
				$tipo_entrada_salida='FACTURA CONSUMIDOR';
				$data_numdoc = array(
				'ult_cof' => $ult_cof
				);
				$tipo_doc='COF';
				$numero_doc=numero_tiquete($ult_cof,$tipo_doc);
				break;
			case 'TIK':
				$tipo_entrada_salida='TICKET';
				$data_numdoc = array(
				'ult_tik' => $ult_tik
				);
				$tipo_doc='TIK';
				$numero_doc=numero_tiquete($ult_tik,$tipo_doc);
				break;
			case 'FLC':
				$tipo_entrada_salida='FACTURA LOTE CONSUMIDOR';
				$data_numdoc = array(
				'ult_flc' => $ult_flc
				);
				$tipo_doc='FLC';
				$numero_doc=numero_tiquete($ult_flc,$tipo_doc);
				break;

		}

		if ($nrows==0){
			$insertar_numdoc = _insert($table_numdoc,$data_numdoc );
		}
		else {
			$where_clause_n="WHERE  id_sucursal='$id_sucursal'";
			$insertar_numdoc = _update($table_numdoc,$data_numdoc,$where_clause_n );
		}

		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){
			//select a la tabla factura
			$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_doc'  and id_sucursal='$id_sucursal'";
			$result_fact=_query($sql_fact);
			$row_fact=_fetch_array($result_fact);
			$nrows_fact=_num_rows($result_fact);
			if($nrows_fact==0){
				$table_fact= 'factura';
				$form_data_fact = array(
				'id_cliente' => $id_cliente,
				'fecha' => $fecha_movimiento,
				'numero_doc' => $numero_doc,
				'total' => $total_venta,
				'id_usuario'=>$id_usuario,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'tipo' => $tipo_entrada_salida,
				'hora' => $hora,
				'finalizada' => '1'
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );
				$id_fact= _insert_id();
			}
			$array = json_decode($array_json,true);
			$listadatos=explode('#',$stringdatos);
			foreach ($array as $fila){
        if( $fila['precio']>0 && $fila['subtotal']>0 ){
			//for ($i=0;$i<$cuantos;$i++){

				$subcantidad=0;
				$existencias=0;
				$nrow2=0;
				$id_producto=$fila['id'];
        $cantidad=$fila['cantidad'];
        $precio_venta=$fila['precio'];
				$subcantidad=$fila['subcantidad'];
				$unidades=$fila['unidades'];
				$subtotal=$fila['subtotal'];
				//list($id_producto,$precio_venta,$cantidad,$subcantidad,$unidades,$subtotal)=explode('|',$listadatos[$i]);

				//Primero revisar stock y q me facture solo las existencias reales
				$sql2="select producto.id_producto, producto.unidad,producto.perecedero,
					stock.stock as existencias, stock.costo_promedio
					from producto,stock
					where producto.id_producto='$id_producto'
					and producto.id_producto=stock.id_producto
					and stock.id_sucursal='$id_sucursal'";
					$stock2=_query($sql2);
					$nrow2=_num_rows($stock2);

					//Actualizar en stock si  hay registro del producto
					$cant_facturar=0;
					if ($nrow2>0){
						$row2=_fetch_array($stock2);
						$unidad=$row2['unidad'];
						$existencias=$row2['existencias'];
						$perecedero=$row2['perecedero'];
						$nueva_cantidad=0;
						$nueva_cantidad=($cantidad*$unidad)+$subcantidad;

						$cantidad_stock=$existencias-$nueva_cantidad;
						$cant_facturar=$nueva_cantidad;

						$table2= 'stock';
						$where_clause2="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";

						$form_data2 = array(
						'stock' => $cantidad_stock,
						);
						$insertar2 = _update($table2,$form_data2, $where_clause2 );
					}
					if($unidad>1){
						$precio_venta_unit=round($precio_venta/$unidad,4);
					}
					else {
						$precio_venta_unit=$precio_venta;
					}
				$subtotal=round($precio_venta_unit*$nueva_cantidad,2);
				$table_fact_det= 'factura_detalle';
				$data_fact_det = array(
				'id_factura' => $id_fact,
				'id_prod_serv' => $id_producto,
				'cantidad' => $nueva_cantidad,
				'precio_venta' => $precio_venta_unit,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'fecha' => $fecha_movimiento
				);
				if ($nueva_cantidad>0){
					$insertar_fact_det = _insert($table_fact_det,$data_fact_det );
				}

					$sql1="select * from movimiento_producto
					where id_producto='$id_producto'
					and tipo_entrada_salida='$tipo_entrada_salida'
					AND numero_doc='$numero_doc'
					AND fecha_movimiento='$fecha_movimiento'
					AND id_sucursal_origen='$id_sucursal'";

					$stock1=_query($sql1);
					$row1=_fetch_array($stock1);
					$nrow1=_num_rows($stock1);

					$table1= 'movimiento_producto';
					$form_data1 = array(
					'id_producto' => $id_producto,
					'fecha_movimiento' => $fecha_movimiento,
					'salida' => $nueva_cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta_unit,
					'stock_anterior' => $existencias,
					'stock_actual' => $cantidad_stock,
					'id_sucursal_origen' => $id_sucursal
					);
						if ($nueva_cantidad>0){
						$insertar_mov_prod = _insert($table1,$form_data1 );
					}

					//si es perecedero
				if($perecedero==1){
					$sql_perecedero="SELECT id_lote_prod, id_producto, fecha_entrada, fecha_caducidad, entrada,
					salida, estado, numero_doc, id_sucursal
					FROM lote
					WHERE id_producto='$id_producto'
					AND id_sucursal='$id_sucursal'
					AND entrada>=salida
					AND estado='VIGENTE'
					ORDER BY fecha_caducidad";
					$result_perecedero=_query($sql_perecedero);

					$table_pp='lote';
					$nrow_perecedero=_num_rows($result_perecedero);
					$fecha_mov=ED($fecha_movimiento);
					$diferencia=0;
					if($nrow_perecedero>0){
						for($j=0;$j<$nrow_perecedero;$j++){
							$row_perecedero=_fetch_array($result_perecedero);
							$entrada=$row_perecedero['entrada'];
							$salida=$row_perecedero['salida'];
							$fecha_caducidad=$row_perecedero['fecha_caducidad'];
							$id_prod_perecedero=$row_perecedero['id_lote_prod'];
							$fecha_caducidad=ED($fecha_caducidad);

							$stock_fecha=$entrada-$salida;
							$comparafecha=compararFechas("-",$fecha_caducidad,$fecha_mov);
							if($nueva_cantidad<$stock_fecha){
								$cant_sale=$nueva_cantidad+$salida;
								$diferencia=0;
								//$cantidad=0;
								$estado='VIGENTE';

							}
							if($nueva_cantidad>=$stock_fecha){
								$cant_sale=$entrada;
								$diferencia=$nueva_cantidad-$stock_fecha;
								$cantidad=$diferencia;
								$estado='FINALIZADO';

							}
							//valida si la fecha de vencimineto ya expiro
							if($comparafecha<0)
								$estado='VENCIDO';

							$where_clause_pp="WHERE id_producto='$id_producto'
								AND id_sucursal='$id_sucursal'
								AND entrada>=salida
								AND id_lote_prod='$id_prod_perecedero'";
							$form_data_pp = array(
							'salida' => $cant_sale,
							'estado' => $estado
							);
						$insertar4 = _update($table_pp,$form_data_pp, $where_clause_pp );
						//si la cantidad vendida no se pasa de la existencia de x lote perecedero  se sale del bucle for
						if ($diferencia==0)
							break;
							}
					}
				} //si es perecedero

				else{
				$insertar4 =true;
				}

			//}//for
		} // if($fila['cantidad']>0 && $fila['precio']>0){
	 } //foreach ($array as $fila){
			if($insertar_numdoc  && $insertar2  && $insertar_fact && $insertar_fact_det && $insertar_mov_prod){
						_commit(); // transaction is committed
						$xdatos['typeinfo']='Success';
						$xdatos['msg']='Documento Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
						$xdatos['process']='insert';
						$xdatos['factura']=$numero_doc;
						//$xdatos['insertados']=" factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
					}else{
						_rollback(); // transaction rolls back
						$xdatos['typeinfo']='Error';
						$xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
						$xdatos['process']='noinsert';
						$xdatos['insertados']=" num_doc :".$numero_doc." factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2." lote:".$insertar4  ;
					}
			}//if

	echo json_encode($xdatos);
}
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
	//ojo !!!!!!!!!!!!!!!!!!!!!!
	//utilidad teneindo precio venta y costo  : utlidad=(precio_venta-costo)/costo;
	$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,producto.combo,producto.perecedero,
		stock.stock,stock.costo_promedio,
		stock.utilidad, stock.pv_base, stock.precio_mayoreo,  stock.porc_desc_base , stock.stock_minimo,
		stock.pv_desc_base ,  stock.porc_desc_max ,  stock.pv_desc_max,
		stock.precio_oferta,stock.fecha_ini_oferta,stock.fecha_fin_oferta
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto'
		AND stock.id_sucursal='$id_sucursal'
		";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		if ($nrow1>0){
		$unidades=round($row1['unidad'],2);
		if($unidades==0)
			$unidades=1;
		$utilidad_activa=$row1['utilidad_activa'];
		$utilidad_seleccion=$row1['utilidad_seleccion'];
		$perecedero=$row1['perecedero'];

		$pu1=$row1['porcentaje_utilidad1']/100;
		$pu2=$row1['porcentaje_utilidad2']/100;
		$pu3=$row1['porcentaje_utilidad3']/100;
		$pu4=$row1['porcentaje_utilidad4']/100;
		$combo=$row1['combo'];
		$cp=$row1['costo_promedio'];
		$existencias=$row1['stock'];
		$exento=$row1['exento'];
	    //$precio_sugerido=$row1['precio_sugerido'];
		$imagen=$row1['imagen'];
		//costos y precios
		$utilidad=$row1['utilidad'];
		$pv_base=$row1['pv_base'];
		$precio_mayoreo=$row1['precio_mayoreo'];
		$porc_desc_base=$row1['porc_desc_base'];
		$pv_desc_base=$row1['pv_desc_base'];
		$porc_desc_max=$row1['porc_desc_max'];
		$pv_desc_max=$row1['pv_desc_max'];
		$fecha_ini_oferta=$row1['fecha_ini_oferta'];
		$fecha_fin_oferta=$row1['fecha_fin_oferta'];
		$precio_oferta=$row1['precio_oferta'];
		$stock_minimo=$row1['stock_minimo'];

		$pv_base_unit=round($pv_base,4);
		$precio_oferta_unit=$precio_oferta;

		//precio de venta
		$fecha_hoy=date("Y-m-d");
		$fecha_hoy2=date("d-m-Y");
		$fecha_fin_oferta2=ed($fecha_fin_oferta);
		$tiene_oferta=compararFechas('-',$fecha_fin_oferta2, $fecha_hoy2);
		if ($tiene_oferta>0){
			$precio_venta=$precio_oferta_unit;
			$oferta=1;

		}
		else{
			$oferta=0;
			$precio_venta=$pv_base_unit;
		}
		if ($precio_mayoreo>0) {
			$precios=array($precio_venta,$precio_mayoreo);
		}
		else {
			$precios=array($precio_venta);
		}
		//consultar si es perecedero
		$fecha_caducidad="0000-00-00";
		$stock_fecha=0;
		if($perecedero==1){
			$sql_perecedero="SELECT id_lote_prod, id_producto, fecha_entrada, fecha_caducidad, entrada,
			salida, estado, numero_doc, id_sucursal
			FROM lote
			WHERE id_producto='$id_producto'
			AND id_sucursal='$id_sucursal'
			AND entrada>salida
			AND estado='VIGENTE'
			AND (fecha_caducidad>='$fecha_hoy'
			OR  fecha_caducidad='0000-00-00')
			ORDER BY fecha_caducidad ASC";
			$result_perecedero=_query($sql_perecedero);
			$array_fecha=array();
			$array_stock=array();
			$nrow_perecedero=_num_rows($result_perecedero);
			if($nrow_perecedero>0){
				for ($i=0;$i<$nrow_perecedero;$i++){
					$row_perecedero=_fetch_array($result_perecedero);
					//$costos_pu=array($pu1,$pu2,$pu3,$pu4);
					$entrada=$row_perecedero['entrada'];
					$salida=$row_perecedero['salida'];
					$id_lote_prod=$row_perecedero['id_lote_prod'];
					$fecha_caducidad=$row_perecedero['fecha_caducidad'];
					if($fecha_caducidad=="")
						$fecha_caducidad="0000-00-00";
					$fecha_caducidad=ED($fecha_caducidad);
					$stock_fecha=$entrada-$salida;
					$array_fecha[] =$id_lote_prod."|".$fecha_caducidad;
					$array_stock[] =$id_lote_prod."|".$fecha_caducidad."|".$stock_fecha;
				}
			}

		}
		else{
			$array_fecha="";
			$array_stock="";
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
		$oferta=0;
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
		//$precio_venta=round($precio_venta,2);
		$xdatos['existencias'] = $existencias;
		$xdatos['precio_venta'] = $precio_venta;
		$xdatos['costo_prom'] = $cp;
		$xdatos['iva'] = $iva;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
		$xdatos['combo'] = $combo;
		$xdatos['fecha_caducidad'] = $fecha_caducidad;
		$xdatos['stock_fecha'] =$stock_fecha;
		$xdatos['oferta'] =$oferta;
		$xdatos['precio_oferta'] =$precio_oferta;
		$xdatos['porc_desc_base']=$porc_desc_base;
		$xdatos['porc_desc_max']=$porc_desc_max;
		$xdatos['perecedero']=$perecedero;
		$xdatos['fechas_vence'] = $array_fecha;
		$xdatos['stock_vence'] = $array_stock;
		$xdatos['fecha_ini_oferta']=$fecha_ini_oferta;
		$xdatos['fecha_fin_oferta']=$fecha_fin_oferta2;
		$xdatos['fecha_hoy']= $fecha_hoy;
		$xdatos['precios_vta']= $precios;
	echo json_encode($xdatos); //Return the JSON Array
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




function imprimir_fact() {
	$numero_doc = $_POST['numero_doc'];
  $tipo_impresion= $_POST['tipo_impresion'];
  $num_doc_fact= $_POST['num_doc_fact'];
	$id_sucursal=$_SESSION['id_sucursal'];

	if ($tipo_impresion=='COF'){
		$tipo_entrada_salida="FACTURA CONSUMIDOR";
	}
	if ($tipo_impresion=='TIK'){
		$tipo_entrada_salida="TICKET";
	}
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_doc' and id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_factura=trim($row_fact['id_factura']);
		$fecha_movimiento=$row_fact['fecha'];
		$table_fact= 'factura';
		if ($tipo_impresion=='COF' && $num_doc_fact!=""){
		$form_data_fact = array(
			'finalizada' => '1',
			'impresa' => '1',
			'impresa_individual' => '1',
			'num_fact_impresa'=>$num_doc_fact
		);
	}
	else{
		$form_data_fact = array(
			'finalizada' => '1',
			'impresa' => '1',
			'impresa_individual' => '1',
		);
	}
		$where_clause="WHERE numero_doc='$numero_doc' and id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
	}
//cambiar numero documento impreso para mostrar en reporte kardex
$where_clause1="WHERE
tipo_entrada_salida='$tipo_entrada_salida'
AND numero_doc='$numero_doc'
AND fecha_movimiento='$fecha_movimiento'
AND id_sucursal_origen='$id_sucursal'";

$table1= 'movimiento_producto';
$form_data1 = array(
'numero_doc'=>$num_doc_fact,
);
$insertar1 = _update($table1,$form_data1,$where_clause1);

if ($tipo_impresion=='COF'){
	$info_facturas=print_fact($id_factura);
}
if ($tipo_impresion=='TIK'){
	$info_facturas=print_ticket($id_factura);
}
//directorio de script impresion cliente
$sql_dir_print="SELECT shared_printer_win, dir_print_script FROM empresa";
$result_dir_print=_query($sql_dir_print);
$row_dir_print=_fetch_array($result_dir_print);
$dir_print=$row_dir_print['dir_print_script'];
$shared_printer_win=$row_dir_print['shared_printer_win'];
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['facturar'] =$info_facturas;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}
function finalizar_fact(){
	$numero_doc = $_POST['numero_doc'];
	$id_sucursal=$_SESSION['id_sucursal'];

	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_doc' and id_sucursal='$id_sucursal'";

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
		$where_clause="WHERE numero_doc='$numero_doc' and id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
		//$numero_doc=trim($row_fact['numero_doc']);
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
	echo json_encode($xdatos); //Return the JSON Array
}
function buscarBarcode(){
	$query = trim($_POST['id_producto']);
	$sql0="SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE barcode='$query'";
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
	case 'imprimir_fact':
		imprimir_fact();
		break;
	case 'print2':
		print2(); //Generacion de los datos de factura que se retornan para otro script que imprime!!!
		break;
	case 'mostrar_numfact':
		mostrar_numfact();
		break;
	case 'reimprimir' :
		reimprimir();
		break;
	case 'finalizar_fact' :
		finalizar_fact();
		break;
	case 'buscarBarcode' :
			buscarBarcode();
			break;
	}

 //}
}
?>
