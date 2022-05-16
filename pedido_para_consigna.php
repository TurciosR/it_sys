<?php
include ("_core.php");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
function initial(){
	$id_pedido = $_REQUEST ['id_pedido'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	//$sql="SELECT * FROM pedido WHERE id_pedido='$id_pedido'";
	$sql="SELECT pedido.*, cliente.nombre,cliente.apellido FROM pedido JOIN cliente
	ON pedido.id_cliente=cliente.id_cliente
	WHERE id_pedido='$id_pedido'
	AND  pedido.id_sucursal='$id_sucursal'
	AND  pedido.finalizada=0";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Pedido a Consigna</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripcion</th>
						</tr>
					</thead>
					<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );
										$cliente=$row['nombre']." ".$row['apellido'];
										echo "<tr><td>Id pedido</th><td>$id_pedido</td></tr>";
										echo "<tr><td>Id Cliente</td><td>".$cliente."</td>";
										echo "<tr><td>Numero Doc</td><td>".$row['numero_doc']."</td>";
										echo "<tr><td>Total $:</td><td>".$row['total']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_pedido' id='id_pedido' value='$id_pedido'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<!--button type="button" class="btn btn-primary" id="btnAnular">Anular</button-->
	<button type="button" class="btn btn-primary" id="btnConvertir">Convertir</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->
<?php
}
function numero_doc($ult_doc){
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
function convertir(){
	$id_pedido = $_POST['id_pedido'];
$id_sucursal=$_SESSION['id_sucursal'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);

	$fecha_movimiento=date('Y-m-d');
	/* Traer el pedido que se va pasar a consignacion */
	$sql="SELECT pedido.*, cliente.nombre,cliente.apellido
	FROM pedido JOIN cliente
	ON pedido.id_cliente=cliente.id_cliente
	WHERE id_pedido='$id_pedido'
	AND  pedido.id_sucursal='$id_sucursal'
	AND  pedido.finalizada=0";
	$result = _query( $sql );
	$count = _num_rows( $result );
	if ($count > 0){
		for($i = 0; $i < $count; $i ++) {
		$row = _fetch_array ( $result );
		$cliente=$row['nombre']." ".$row['apellido'];
		$id_cliente=$row['id_cliente'];
		$id_usuario=$row['id_usuario'];
		$total_venta=$row['total'];

		}
	}
	/*Extraer el ultimo numero de la consignacion de la sucursal activa*/
	$sql="select * from ultimo_numdoc WHERE id_sucursal='$id_sucursal'";
	$result= _query($sql);
	$rows=_fetch_array($result);
	$nrows=_num_rows($result);
	$ult_cof=$rows['ult_cof'];
	$ult_ccf=$rows['ult_ccf'];
	$ult_ped=$rows['ult_ped'];
	$ult_csn=$rows['ult_csn'];
	$ult_csn=$ult_csn+1;
	$table_numdoc="ultimo_numdoc";
	_begin();
	$data_numdoc = array(
		'ult_csn' =>  $ult_csn,
	);
	if ($nrows==0){
			$insertar_numdoc = _insert($table_numdoc,$data_numdoc );
	}
	else {
		$where_clause_n="WHERE  id_sucursal='$id_sucursal'";
		$insertar_numdoc = _update($table_numdoc,$data_numdoc,$where_clause_n );
	}
	$numero_docx=numero_doc($ult_csn);
	$numero_doc =$numero_docx."_CSN";

	$sql_fact="SELECT * FROM consignacion WHERE numero_doc='$numero_doc' and fecha='$fecha_movimiento' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact==0){

		$table_fact= 'consignacion';
		$form_data_fact = array(
			'id_cliente' => $id_cliente,
			'fecha' => $fecha_movimiento,
			'numero_doc' => $numero_doc,
			'total' => $total_venta,
			'id_usuario'=>$id_usuario,
			'id_empleado' => $id_usuario,
			'id_sucursal' => $id_sucursal
		);

		$insertar_fact = _insert($table_fact,$form_data_fact);
		$id_consigna= _insert_id();

		/*Extraer el detalle del pedido */
	$sql2="SELECT p.*,
		dp.id_pedido_detalle, dp.id_prod_serv, dp.cantidad, dp.precio_venta,
		dp.subtotal, dp.id_empleado, dp.tipo_prod_serv, dp.id_sucursal, dp.cant_facturado
		FROM pedido AS p
		JOIN pedido_detalle AS dp
		ON p.id_pedido=dp.id_pedido
		WHERE p.id_pedido='$id_pedido'
		AND  p.id_sucursal='$id_sucursal'
		AND  p.finalizada=0";

	//Agregar a detalle consigna
	$result2 = _query( $sql2 );
	$count2 = _num_rows( $result2 );
	if ($count2 > 0){
		for($j = 0; $j < $count2; $j ++) {
				$row2 = _fetch_array($result2);
				$id_prod_serv=$row2['id_prod_serv'];
				$cantidad=$row2['cantidad'];
				$precio_venta=$row2['precio_venta'];
				$id_empleado=$row2['id_empleado'];
				$tipo_prod_serv=$row2['tipo_prod_serv'];
				$subtotal=$row2['subtotal'];
				$table_fact_det= 'consignacion_detalle';
				$data_fact_det = array(
				'id_consignacion' => $id_consigna,
				'id_prod_serv' => $id_prod_serv,
				'cantidad' => $cantidad,
				'precio_venta' => $precio_venta,
				'subtotal' => $subtotal,
				'tipo_prod_serv' => $tipo_prod_serv,
				'id_empleado' => $id_empleado,
				'id_sucursal' => $id_sucursal
				);
				$insertar_fact_det = _insert($table_fact_det, $data_fact_det);
			}
		}
	}
	$table3 = 'pedido';
	$where_clause3 = "id_pedido='$id_pedido' AND id_sucursal='$id_sucursal'";
	$form_data3 = array(
		'anulada' =>1,
		'finalizada' =>1
	);
	$finalizar_pedido = _update($table3,$form_data3,$where_clause3);

	if ($insertar_fact && $insertar_fact_det) {
		_commit();
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'pedido a Consignacion! --pedido:'.$insertar_fact."--detalle pedido:".$insertar_fact_det." fin ped:".$finalizar_pedido ;
	} else {
		_rollback();
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'pedido no convertido! '.$insertar_fact."--".$insertar_fact_det;
	}
	echo json_encode($xdatos);
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formAnular' :
				initial();
				break;
			case 'convertir' :
				convertir();
				break;
		}
	}
}
?>
