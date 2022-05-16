<?php
include_once "_core.php";
include ('num2letras.php');
//include ('trasladocion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
	$title="Traslado de Sucursal";
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
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

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
	include_once "header.php";
	include_once "main_menu.php";
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
                        <div class="ibox-content">

					 <div class="row">
						 <!--div id='form_datos_cliente'>
                            <div class="col-md-4">
                              <div class="form-group">
								  <label>Seleccione cliente</label><br-->

							<?php
							/*
								echo"<select  name='id_cliente' id='id_cliente'  class='form-control' style='width:300px;'>";
								$qcliente=_query("SELECT * FROM cliente  ORDER BY apellido");
								while($row_cliente=_fetch_array($qcliente))
                                   {
                                       $id_cliente=$row_cliente['id_cliente'];
                                       $nombre_cliente=$row_cliente['nombre']." ".$row_cliente['apellido'];
                                       echo "<option value='$id_cliente'>$nombre_cliente</option> ";
                                   }
							echo "</select>";
						echo "</div>"; //<div class='form-group'>
                     echo  "</div>";// <div class="col-lg-6">
                     echo "</div>";//<!--<div id='form_datos_cliente'> -->
                     */
                     $fecha_actual=date("Y-m-d");
						echo "<div class='col-md-4'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
						echo "</div>";//<div class='col-md-4'>"



						// 3 TRASLADO DE SUCURSAL

						//sucursal destino
						echo " <div class='col-md-4'>";
							echo"<div class='form-group has-info single-line'>
									<label>Seleccione Sucursal Destino</label>
                                <div class='input-group'>
									<select  name='id_sucursal_destino' id='id_sucursal_destino'   style='width:200px'>
									<option value=''>Seleccione</option>  ";

                                  $sql_sucursal="SELECT * FROM sucursal WHERE id_sucursal!='$id_sucursal' ORDER BY descripcion";
                                   $qsucursal1=_query($sql_sucursal);
                                   while($row_sucursal1=_fetch_array($qsucursal1))
                                   {
                                       $id_sucursal1=$row_sucursal1['id_sucursal'];
                                       $descripcion1=$row_sucursal1['descripcion'];
                                       echo "<option value='$id_sucursal1'>$descripcion1</option> ";
                                   }

                                  echo"</select>
                                  </div>
                               </div>  ";


					echo "</div>";
					echo "</div>";// <div class="ibox-content">
			?>
		<!--/div-->
						<!--div class="ibox "-->
						<div class="ibox-content">
							<header><h4 class="text-navy">BUSCAR PRODUCTO o SERVICIO</h4></header>
							<div class="row" id='buscador'>

								<div class="col-lg-6">
									<div class="widget style1 text-center">
										<div class='form-group has-info single-line'><label>Buscar Producto o Servicio</label>
											<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto o servicio a trasladar"  data-provide="typeahead">
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

						<!--div class="ibox "-->
						<div class="ibox-content">
							<!--load datables estructure html-->
							<header><h4 class="text-navy">TRASLADO</h4></header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Stock</th>
										<th class='success'>Precios</th>
										<th class='success'>Precio Vta.</th>
										<th class='success'>Cantidad</th>
										<th class='success' >Subtotal</th>
										<th class='success'>Acci&oacute;n</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line"></td>
										<td class="thick-line text-center"><strong>TOTAL $:</strong></td>
										<td  class="thick-line text-right" id='total_dinero' ></td>
										<td class="thick-line"></td>
										</tr>
									</tfoot>
								</table>
								<div class="well m-t"  id='totaltexto'><strong>Son:</strong> </div>
							<input type='hidden' name='totaltraslado' id='totaltraslado' value='0'>
						</div><!--div class="table-responsive m-t"-->


					</section>

							<div class="title-action" id='botones'>
								<button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
							</div>

								<!--/div-->
						</div><!--div class='ibox-content'-->
					<!--/div--><!--div class='ibox'-->

	<!-- Modal -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-sm">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
				</div>
				<div class="modal-body">
				<div class="wrapper wrapper-content  animated fadeInRight">
					<div class="row" id='fact_num'> </div>
					<div class="row">
								<div class="col-lg-12">
                                  <div class="form-group">
                                      <label><h4 class='text-navy'>Trasladado $ </h5></label><input type="text" id="trasladado" name="trasladado" value=0  class="form-control decimal" readonly >
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
				<button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
				<button type="button" class="btn btn-primary" id="btnEsc">Salir</button>
			</div>
			</div>
		</div>
	</div>

<!--/div-->
               	<!--/div--><!--div class='ibox-content'-->
       		</div><!--<div class='ibox float-e-margins' -->
		</div> <!--div class='col-lg-12'-->
	</div> <!--div class='row'-->
</div><!--div class='wrapper wrapper-content  animated fadeInRight'-->

<?php
include_once ("footer.php");
//echo "<script src='js/plugins/typehead/bootstrap3-typeahead.js'></script>";
echo "<script src='js/funciones/traslado.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function numero_tiquete($ult_doc){
	$ult_doc=trim($ult_doc);
	$len_ult_valor=strlen($ult_doc);
	$long_num_fact=7;
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
	$valor_txt=$valor_txt.$ult_doc."_TRS";
	return $valor_txt;
}
function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];

	$id_sucursal_destino= $_POST['id_sucursal_destino'];
	$id = '1';
	$id_usuario=$_SESSION["id_usuario"];

	$id_sucursal=$_SESSION['id_sucursal'];

	$tipo_entrada_salida='TRASLADO CONSUMIDOR';
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$id_cliente=$_POST['id_cliente'];
	$total_venta = $_POST['total_ventas'];
	//}
	if ($id=='2'){
		$tipo_entrada_salida='TRASLADO CREDITO FISCAL';
		$numero_docx = $_POST['numero_doc'];
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc']."_TRS";
		$total_venta = $_POST['total_ventas'];
	}
	$ult_cof=0;
	$sql="select * from ultimo_numdoc where id_sucursal='$id_sucursal'";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);
	$ult_cof=$rows['ult_cof'];
	$ult_ccf=$rows['ult_ccf'];
	$ult_trs=$rows['ult_trs'];
	$id_sucursal=$rows['id_sucursal'];
	$ult_trs=$ult_trs+1;

	$table_numdoc="ultimo_numdoc";

	$data_numdoc = array(
		'ult_trs' => $ult_trs,
			//'id_sucursal' => $id_sucursal
	);

	$numero_doc=numero_tiquete($ult_trs);
	//$numero_doc = $_POST['numero_doc']."_COF";
	$insertar1=false;
	$insertar2=false;
	$insertar_fact=false;
	$insertar_fact_det=false;
	$insertar_numdoc =false;
	_begin();
	if ($nrows==0){
		$insertar_numdoc = _insert($table_numdoc,$data_numdoc );
	}
	else {
		$where_clause_n="WHERE  id_sucursal='$id_sucursal'";
		$insertar_numdoc = _update($table_numdoc,$data_numdoc,$where_clause_n );
	}



	$xdatos['typeinfo']='';
	$xdatos['msg']='';
	$xdatos['process']='';
	$finalizada=0;
	if ($id=='1'){
		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){
			//insertar a la tabla traslado
			//INSERT INTO traslado(id_traslado, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
			$sql_fact="SELECT * FROM traslado WHERE numero_doc='$numero_doc'  and id_sucursal='$id_sucursal'";
			$result_fact=_query($sql_fact);
			$row_fact=_fetch_array($result_fact);
			$nrows_fact=_num_rows($result_fact);
			if($nrows_fact==0){

				$table_fact= 'traslado';
				$form_data_fact = array(
				'id_cliente' => $id_cliente,
				'fecha' => $fecha_movimiento,
				'numero_doc' => $numero_doc,
				'total' => $total_venta,
				'id_usuario'=>$id_usuario,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'finalizada' => $finalizada,
				'id_sucursal_destino' => $id_sucursal_destino
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );
				$id_fact= _insert_id();
			}
			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				list($id_prod_serv,$tipoprodserv,$precio_venta,$cantidad)=explode('|',$listadatos[$i]);
				$subtotal=$precio_venta*$cantidad;
				$table_fact_det= 'traslado_detalle';
				$data_fact_det = array(
				'id_traslado' => $id_fact,
				'id_prod_serv' => $id_prod_serv,
				'enviado' => $cantidad,
				'precio_venta' => $precio_venta,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal
				);
				$insertar_fact_det = _insert($table_fact_det,$data_fact_det );
				if ($tipoprodserv=='SERVICIO'){
					$sql="SELECT id_servicio as id, descripcion, tipo_prod_servicio FROM servicio WHERE id_servicio='$id_prod_serv'";
					$insertar1 =true;
					$insertar2 =true;
				}
				else {
					$sql1="select * from movimiento_producto where id_producto='$id_prod_serv' and tipo_entrada_salida='$tipo_entrada_salida'
					AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento' and id_sucursal_origen='$id_sucursal'";
					$stock1=_query($sql1);
					$row1=_fetch_array($stock1);
					$nrow1=_num_rows($stock1);

					$sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_prod_serv' and producto.id_producto=stock.id_producto and stock.id_sucursal='$id_sucursal'";
					$stock2=_query($sql2);
					$row2=_fetch_array($stock2);
					$nrow2=_num_rows($stock2);
					$unidad=$row2['unidad'];
					$existencias=$row2['stock'];
					$table1= 'movimiento_producto';
					$form_data1 = array(
					'id_producto' => $id_prod_serv,
					'fecha_movimiento' => $fecha_movimiento,
					'salida' => $cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta,
					'id_sucursal_origen' => $id_sucursal,
					'id_sucursal_destino' => $id_sucursal_destino
					);
					$table2= 'stock';
					if($existencias>=$cantidad)
						$cantidad_stock=$existencias-$cantidad;
					else
						$cantidad_stock=0;
					if ($nrow1==0){
						$insertar1 = _insert($table1,$form_data1 );
					}
					//Actualizar en stock si  hay registro del producto
					if ($nrow2>0 && $nrow1==0){
						$where_clause="WHERE id_producto='$id_prod_serv' and id_sucursal='$id_sucursal'";

						$form_data2 = array(
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta,
						'id_sucursal' => $id_sucursal
						);
						$insertar2 = _update($table2,$form_data2, $where_clause );
					}
				}//si es PRODUCTO
			}//for
		if($insertar_numdoc && $insertar1 && $insertar2 && $insertar_fact && $insertar_fact_det){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='Traslado Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
					$xdatos['process']='insert';
					$xdatos['insertados']=" traslado :".$insertar_fact." traslado detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de traslado no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
				}
		}//if
	} //if $id=1

	if ($id=='2'){
		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){
			//insertar a la tabla traslado
			//INSERT INTO traslado(id_traslado, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
			$sql_fact="SELECT * FROM traslado WHERE numero_doc='$numero_doc' and fecha='$fecha_movimiento'";
			$result_fact=_query($sql_fact);
			$row_fact=_fetch_array($result_fact);
			$nrows_fact=_num_rows($result_fact);
			if($nrows_fact==0){
				 $table_fact= 'traslado';
				$form_data_fact = array(
				'id_cliente' => $id_cliente,
				'fecha' => $fecha_movimiento,
				'numero_doc' => $numero_doc,
				'total' => $total_venta,
				'id_usuario'=>$id_usuario
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );
				$id_fact= _insert_id();
			}


			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				//StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"|"+"#";
				//SELECT id_traslado_detalle, id_traslado, id_prod_serv, cantidad, precio_venta, subtotal, id_empleado, tipo_prod_serv FROM traslado_detalle
				list($id_prod_serv,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
				$subtotal=$precio_venta*$cantidad;
				$table_fact_det= 'traslado_detalle';
				$data_fact_det = array(
				'id_traslado' => $id_fact,
				'id_prod_serv' => $id_prod_serv,
				'cantidad' => $cantidad,
				'precio_venta' => $precio_venta,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_empleado,
				'id_sucursal' => $id_sucursal
				);
				$insertar_fact_det = _insert($table_fact_det,$data_fact_det );
				if ($tipoprodserv=='SERVICIO'){
					//INSERT INTO traslado(id_traslado, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
					$sql="SELECT id_servicio as id, descripcion, tipo_prod_servicio FROM servicio WHERE id_servicio='$id_prod_serv'";
					$insertar1 =true;
					$insertar2 =true;
				}
			else {
				$sql1="select * from movimiento_producto where id_producto='$id_prod_serv' and tipo_entrada_salida='$tipo_entrada_salida'
					AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento'
					AND id_sucursal_origen='$id_sucursal' ";
				$stock1=_query($sql1);
				$row1=_fetch_array($stock1);
				$nrow1=_num_rows($stock1);

				$sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_prod_serv' and producto.id_producto=stock.id_producto
					AND stock.id_sucursal='$id_sucursal'";
				$stock2=_query($sql2);
				$row2=_fetch_array($stock2);
				$nrow2=_num_rows($stock2);
				$unidad=$row2['unidad'];
				$existencias=$row2['stock'];

				$table1= 'movimiento_producto';
				$form_data1 = array(
					'id_producto' => $id_prod_serv,
					'fecha_movimiento' => $fecha_movimiento,
					'salida' => $cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta,
					'id_sucursal_origen' => $id_sucursal,
					'id_sucursal_destino' => $id_sucursal_destino
				);
					$table2= 'stock';
					if($existencias>=$cantidad)
						$cantidad_stock=$existencias-$cantidad;
					else
						$cantidad_stock=0;
					if ($nrow1==0){
						$insertar1 = _insert($table1,$form_data1 );
					}
					//Actualizar en stock si  hay registro del producto
					if ($nrow2>0 && $nrow1==0){
						$where_clause="WHERE id_producto='$id_prod_serv' AND id_sucursal='$id_sucursal' ";

						$form_data2 = array(
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta,
						'id_sucursal' => $id_sucursal
						);
						$insertar2 = _update($table2,$form_data2, $where_clause );
					}
				}//si es PRODUCTO
			}//for
			if($insertar1 && $insertar2 && $insertar_fact && $insertar_fact_det){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='traslado Numero: <strong>'.$numero_docx.'</strong>  Guardada con Exito !';
					$xdatos['process']='insert';
					$xdatos['insertados']=" traslado :".$insertar_fact." traslado detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;
				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de traslado no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
				}

		}//if
	} //if $id=2

	echo json_encode($xdatos);
}
function consultar_stock(){
	$id_producto = $_REQUEST['id_producto'];
	$tipo = $_REQUEST['tipo'];

	$id_usuario=$_SESSION["id_usuario"];
	/*
	$sql_user="select * from usuario where id_usuario='$id_usuario'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	*/
	$id_sucursal=$_SESSION['id_sucursal'];
	$iva=0;
	$sql_iva="select iva from empresa";
	$result=_query($sql_iva);
	$row=_fetch_array($result);
	$iva=$row['iva'];

	if ($tipo =='PRODUCTO'){

		$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,
		producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,
		producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
		producto.porcentaje_utilidad4,producto.imagen,
		stock.stock,stock.costo_promedio,stock.precio_sugerido
		FROM producto JOIN stock ON producto.id_producto=stock.id_producto
		WHERE producto.id_producto='$id_producto'
		and stock.id_sucursal='$id_sucursal'";
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
		$xdatos['iva'] = $iva;
		$xdatos['unidades'] = $unidades;
		$xdatos['imagen'] = $imagen;
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
	$id_ccf_cof= $_POST['id'];
	if ($id_ccf_cof=='1'){
		$numero_docx = $numero_doc."_COF";
		$tipo_fact="COF";
	}
	else{
		$numero_docx = $numero_doc."_CCF";
		$tipo_fact="CCF";
	}
	print_fact($numero_doc,$tipo_fact);
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
	}

 //}
}
?>
