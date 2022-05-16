<?php
include_once "_core.php";
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');
function initial() {
	$title="Generar Venta e Impresión de Factura";
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

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
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
						 <div id='form_datos_cliente'>
                            <div class="col-lg-6">
                              <div class="form-group">
								  <label>Seleccione cliente</label><br>

							<?php
								echo"<select  name='id_cliente' id='id_cliente'  class='form-control' style='width:300px;'>";
								$qcliente=_query("SELECT * FROM cliente ORDER BY id_cliente");
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

                     $fecha_actual=date("Y-m-d");
						echo "<div class='col-md-4'>";
							echo "<div class='form-group has-info single-line'><label>Fecha:</label> <input type='text' placeholder='Fecha' class='datepick form-control' id='fecha' name='fecha' value='$fecha_actual'></div>";
						echo "</div>";//<div class='col-md-4'>"
                     echo "</div>";//row
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
											<input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto o servicio a facturar"  data-provide="typeahead">
										</div>
									</div>
								</div>
								<!--Widgwt imagen-->
								<div class="col-xs-4">
									<div class="widget style1 gray-bg text-center">
										<div class="m-b-sm" id='imagen'>
											<img alt="image" class="img-circle" src="img/productos/white.png" width="200px" height="100px" border='1'>
											<p class="font-bold">&nbsp;</p>
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
							<header><h4 class="text-navy">FACTURACION</h4></header>
							<section>
								<div class="table-responsive m-t">
									<table class="table  table-condensed table-striped" id="inventable">
									<thead class="thead-inverse">
										<tr>
										<th class='success'>Id</th>
										<th class='success'>Descripci&oacute;n</th>
										<th class='success'>Stock</th>
										<th class='success'>Precios</th>
										<th class='success'>Precio Vta.</th>
										<th class='success'>Cantidad</th>
										<th class='success' >Subtotal</th>
										<th class='success'>Combo</th>
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
										<td class="thick-line"></td>
										</tr>
									</tfoot>
								</table>
								<div class="well m-t"  id='totaltexto'><strong></strong> </div>
								<input type='hidden' name='totalfactura' id='totalfactura' value='0'>
								<input type='hidden' name='facturacion' id='facturacion' value='facturacion.php'>
						</div><!--div class="table-responsive m-t"-->

					</section>
					<div class="title-action" id='botones'>
						<button type="button" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
					</div>
				</div><!--div class='ibox-content'-->
	<!-- Modal -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-sm">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
				</div>

				<div class="modal-body">
				<!--div class="wrapper wrapper-content  animated fadeInRight"-->

					<table	class="table table-striped" id="tableview">
						<thead>
							<tr>
								<th><h5  class='text-navy'>Factura No:</h5></th>
								<th><h5><span id='fact_num'></span></h5></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><h5 class='text-warning'>Facturado</h5></td>
								<td><input type="text" id="facturado" name="facturado" value=0  class="form-control decimal" readonly > </td>
							</tr>
							<tr>
								<td><h5 class='text-danger'>Efectivo $ </h5></td>
								<td><input type="text" id="efectivo" name="efectivo" value=""  class="form-control decimal"> </td>
							</tr>
							<tr>
								<td><h5 class='text-success'>Cambio $</h5> </td>
								<td> <input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly ></td>
							</tr>
						</tbody>
						<tfoot>
						<td><button type="button" class="btn btn-primary" id="btnPrintFact"><i class="fa fa-print"></i> Imprimir</button> </td>
						<td><button type="button"  class="btn btn-danger" id="btnEsc"><i class="fa fa-stop"></i> Salir</button>	 </td>
						</tfoot>
					</table>
					<!--/div-->



					<!--div class="row">
							<button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
							<button type="button" class="btn btn-primary" id="btnEsc">Salir</button>
					</div-->
				</div>
			<!--div class="modal-footer"> Vuelva Pronto!</div-->
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
echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
//echo "<script src='js/funciones/facturacion.js'></script>";
echo "<script src='js/funciones/genera_venta.js'></script>";
} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div> ".$links;
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
	$valor_txt=$valor_txt.$ult_doc;
	return $valor_txt;
}
function insertar(){
	$cuantos = $_POST['cuantos'];
	$stringdatos = $_POST['stringdatos'];
	$id = '1';
	$id_usuario=$_SESSION["id_usuario"];
	/*
	$sql_user="select * from usuario where id_usuario='$id_usuario'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	*/
	$id_sucursal=$_SESSION['id_sucursal'];

	$tipo_entrada_salida='FACTURA CONSUMIDOR';
	$fecha_movimiento= $_POST['fecha_movimiento'];
	$id_cliente=$_POST['id_cliente'];
	$total_venta = $_POST['total_ventas'];
	//}
	if ($id=='2'){
		$tipo_entrada_salida='FACTURA CREDITO FISCAL';
		$numero_docx = $_POST['numero_doc'];
		$id_cliente=$_POST['id_cliente'];
		$fecha_movimiento= $_POST['fecha_movimiento2'];
		$numero_doc = $_POST['numero_doc']."_CCF";
		$total_venta = $_POST['total_ventas'];
	}
	$ult_cof=0;
	$sql="select * from ultimo_numdoc where id_sucursal='$id_sucursal'";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);
	$ult_cof=$rows['ult_cof'];
	$ult_ccf=$rows['ult_ccf'];
	$id_sucursal=$rows['id_sucursal'];
	$ult_cof=$ult_cof+1;

	$table_numdoc="ultimo_numdoc";
	$insertar_numdoc =false;
	$data_numdoc = array(
		'ult_cof' => $ult_cof,
			//'id_sucursal' => $id_sucursal
	);
	_begin();
	if ($nrows==0){
		$insertar_numdoc = _insert($table_numdoc,$data_numdoc );
	}
	else {
		$where_clause_n="WHERE  id_sucursal='$id_sucursal'";
		$insertar_numdoc = _update($table_numdoc,$data_numdoc,$where_clause_n );
	}

	$numero_docx=numero_tiquete($ult_cof);
	$numero_doc =$numero_docx."_COF";
	$insertar1=false;
	$insertar2=false;
	$insertar_fact=false;
	$insertar_fact_det=false;
	$insertar4 =false;
	$xdatos['typeinfo']='';
	$xdatos['msg']='';
	$xdatos['process']='';
	if ($id=='1'){
		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){
			//insertar a la tabla factura
			//INSERT INTO factura(id_factura, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
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
				'id_sucursal' => $id_sucursal
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );
				$id_fact= _insert_id();
			}
			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				list($id_prod_serv,$tipoprodserv,$precio_venta,$cantidad)=explode('|',$listadatos[$i]);
				$subtotal=$precio_venta*$cantidad;
				$table_fact_det= 'factura_detalle';
				$data_fact_det = array(
				'id_factura' => $id_fact,
				'id_prod_serv' => $id_prod_serv,
				'cantidad' => $cantidad,
				'precio_venta' => $precio_venta,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_usuario,
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

					$sql2="select producto.id_producto,producto.unidad,producto.perecedero,
					stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_prod_serv'
					and producto.id_producto=stock.id_producto
					and stock.id_sucursal='$id_sucursal'";
					$stock2=_query($sql2);
					$row2=_fetch_array($stock2);
					$nrow2=_num_rows($stock2);
					$unidad=$row2['unidad'];
					$existencias=$row2['stock'];
					$perecedero=$row2['perecedero'];

					$table1= 'movimiento_producto';
					$form_data1 = array(
					'id_producto' => $id_prod_serv,
					'fecha_movimiento' => $fecha_movimiento,
					'salida' => $cantidad,
					'observaciones' => $observaciones,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta,
					'id_sucursal_origen' => $id_sucursal
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
						//si es perecedero
					if($perecedero==1){
						$sql_perecedero="SELECT id_prod_perecedero, id_producto, fecha_entrada, fecha_caducidad, entrada,
						salida, estado, numero_doc, id_sucursal FROM producto_perecedero
						WHERE id_producto='$id_prod_serv'
						AND id_sucursal='$id_sucursal'
						AND entrada>=salida
						AND estado='VIGENTE'
						ORDER BY fecha_caducidad";
						$result_perecedero=_query($sql_perecedero);

						$table_pp='producto_perecedero';
						$nrow_perecedero=_num_rows($result_perecedero);
						$fecha_mov=ED($fecha_movimiento);
						$diferencia=0;
						if($nrow_perecedero>0){
							for($i=0;$i<$nrow_perecedero;$i++){
								$row_perecedero=_fetch_array($result_perecedero);
								$entrada=$row_perecedero['entrada'];
								$salida=$row_perecedero['salida'];
								$fecha_caducidad=$row_perecedero['fecha_caducidad'];
								$id_prod_perecedero=$row_perecedero['id_prod_perecedero'];
								$fecha_caducidad=ED($fecha_caducidad);

								$stock_fecha=$entrada-$salida;
								$comparafecha=compararFechas ("-",$fecha_caducidad,$fecha_mov);
								if($cantidad<$stock_fecha){
									$cant_sale=$cantidad+$salida;
									$diferencia=0;
									$cantidad=0;
									$estado='VIGENTE';

								}
								if($cantidad>=$stock_fecha){
									$cant_sale=$entrada;
									$diferencia=$cantidad-$stock_fecha;
									$cantidad=$diferencia;
									$estado='FINALIZADO';

								}
								//valida si la fecha de vencimineto ya expiro
								if($comparafecha<0)
									$estado='VENCIDO';

								$where_clause_pp="WHERE id_producto='$id_prod_serv'
									AND id_sucursal='$id_sucursal'
									AND entrada>=salida
									AND id_prod_perecedero='$id_prod_perecedero'";
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
					else
					$insertar4 =true;
					}
				}//si es PRODUCTO
			}//for
		if($insertar_numdoc  && $insertar1 && $insertar2 && $insertar_fact && $insertar_fact_det && $insertar4){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='Tiquete Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
					$xdatos['process']='insert';
					$xdatos['factura']=$numero_docx;
					$xdatos['insertados']=" factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;

				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
					$xdatos['factura']="";
					$xdatos['insertados']="" ;

				}
		}//if
	} //if $id=1

	if ($id=='2'){
		$observaciones=$tipo_entrada_salida;
		if ($cuantos>0){
			//insertar a la tabla factura
			//INSERT INTO factura(id_factura, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
			$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_doc' and fecha='$fecha_movimiento'";
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
				'id_usuario'=>$id_usuario
				);
				$insertar_fact = _insert($table_fact,$form_data_fact );
				$id_fact= _insert_id();
			}


			$listadatos=explode('#',$stringdatos);
			for ($i=0;$i<$cuantos ;$i++){
				//StringDatos+=campo0+"|"+tipoprodserv+"|"+precio_venta+"|"+cantidad+"|"+id_empleado+"|"+"#";
				//SELECT id_factura_detalle, id_factura, id_prod_serv, cantidad, precio_venta, subtotal, id_empleado, tipo_prod_serv FROM factura_detalle
				list($id_prod_serv,$tipoprodserv,$precio_venta,$cantidad,$id_empleado)=explode('|',$listadatos[$i]);
				$subtotal=$precio_venta*$cantidad;
				$table_fact_det= 'factura_detalle';
				$data_fact_det = array(
				'id_factura' => $id_fact,
				'id_prod_serv' => $id_prod_serv,
				'cantidad' => $cantidad,
				'precio_venta' => $precio_venta,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipoprodserv,
				'id_empleado' => $id_empleado,
				);
				$insertar_fact_det = _insert($table_fact_det,$data_fact_det );
				if ($tipoprodserv=='SERVICIO'){
					//INSERT INTO factura(id_factura, id_cliente, fecha, numero_doc, total, id_usuario) VALUES ();
					$sql="SELECT id_servicio as id, descripcion, tipo_prod_servicio FROM servicio WHERE id_servicio='$id_prod_serv'";
					$insertar1 =true;
					$insertar2 =true;
				}
			else {
				$sql1="select * from movimiento_producto where id_producto='$id_prod_serv' and tipo_entrada_salida='$tipo_entrada_salida'
					AND numero_doc='$numero_doc' and fecha_movimiento='$fecha_movimiento'";
				$stock1=_query($sql1);
				$row1=_fetch_array($stock1);
				$nrow1=_num_rows($stock1);

				$sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_prod_serv' and producto.id_producto=stock.id_producto";
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
					'precio_venta' => $precio_venta
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
						$where_clause="WHERE id_producto='$id_prod_serv'";

						$form_data2 = array(
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta
						);
						$insertar2 = _update($table2,$form_data2, $where_clause );
					}
				}//si es PRODUCTO
			}//for
			if($insertar1 && $insertar2 && $insertar_fact && $insertar_fact_det){
					_commit(); // transaction is committed
					$xdatos['typeinfo']='Success';
					$xdatos['msg']='Factura Numero: <strong>'.$numero_docx.'</strong>  Guardada con Exito !';
					$xdatos['process']='insert';
					$xdatos['factura']=$numero_docx;
					$xdatos['insertados']=" factura :".$insertar_fact." factura detalle:".$insertar_fact_det." mov prod:".$insertar1." stock:".$insertar2 ;

				}else{
					_rollback(); // transaction rolls back
					$xdatos['typeinfo']='Error';
					$xdatos['msg']='Registro de Factura no pudo ser Actualizado !';
					$xdatos['process']='noinsert';
					$xdatos['factura']="";
					$xdatos['insertados']="";

				}

		}//if
	} //if $id=2

	echo json_encode($xdatos);
}
/*
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
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_ccf_cof='1';
	$tipo_fact="";
	if ($id_ccf_cof=='1'){
		$numero_docx = $numero_doc."_COF";
		$tipo_fact="COF";
	}
	else{
		$numero_docx = $numero_doc."_CCF";
		$tipo_fact="CCF";
	}
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';
	$sql_fact="SELECT * FROM factura WHERE numero_doc='$numero_docx' and id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_factura=trim($row_fact['id_factura']);
		$table_fact= 'factura';
		$form_data_fact = array(
			'finalizada' => '1'
		);
		$where_clause="WHERE numero_doc='$numero_docx' and id_sucursal='$id_sucursal'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
	}


	$info_factura=print_fact($numero_doc,$tipo_fact);
	$nreg_encode['facturar'] = $info_factura;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);
}

//mostrar el num de factura de consumidor o credito fiscal
/*
function mostrar_numfact(){
	$id=$_REQUEST['id'];

	$sql="select * from ultimo_numdoc";
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


}
*/
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
