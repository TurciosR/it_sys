<?php
include ("_core.php");
function initial(){
	$id_consignacion = $_REQUEST ['id_consignacion'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];

	$sql="SELECT consignacion.*, cliente.nombre,cliente.apellido FROM consignacion JOIN cliente
	ON consignacion.id_cliente=cliente.id_cliente
	WHERE id_consignacion='$id_consignacion'
	AND  consignacion.id_sucursal='$id_sucursal'
	AND  consignacion.finalizada=0";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular consignacion</h4>
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
										echo "<tr><td>Id consignacion</th><td>$id_consignacion</td></tr>";
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
			echo "<input type='hidden' nombre='id_consignacion' id='id_consignacion' value='$id_consignacion'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnAnular">Anular</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function anular() {
	$id_consignacion = $_POST ['id_consignacion'];

	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	$table = 'consignacion';
	$where_clause = "id_consignacion='" . $id_consignacion . "' AND id_sucursal='".$id_sucursal."'";

	$form_data = array(
		'anulada' =>1
	);



	$sql_fact="SELECT * FROM consignacion WHERE id_consignacion='$id_consignacion' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_cliente'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];

	}
	$delete = _update($table,$form_data, $where_clause );
	//falta hacer update reingresar a stock si hay producto en consignacion anulada	!!!!!!
	$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,consignacion_detalle.*
		FROM consignacion_detalle JOIN producto ON consignacion_detalle.id_prod_serv=producto.id_producto
		WHERE  consignacion_detalle.id_consignacion='$id_consignacion'
		AND  consignacion_detalle.tipo_prod_serv='PRODUCTO' AND consignacion_detalle.id_sucursal='$id_sucursal'";

	$result_fact_det=_query($sql_fact_det);
	$nrows_fact_det=_num_rows($result_fact_det);
	$tipo_entrada_salida='INGRESO POR ANULACION CONSIGNACION';

	for($i=0;$i<$nrows_fact_det;$i++){
		$cant_dev_stock=0;
		$row_fact_det=_fetch_array($result_fact_det);
		$id_producto =$row_fact_det['id_producto'];
		$descripcion =$row_fact_det['descripcion'];
		$id_consignacion_detalle =$row_fact_det['id_consignacion_detalle'];
		$id_prod_serv =$row_fact_det['id_prod_serv'];
		$cantidad =$row_fact_det['cantidad'];
		$cant_facturado =$row_fact_det['cant_facturado'];
		$precio_venta =$row_fact_det['precio_venta'];
		$subt =$row_fact_det['subtotal'];
		$id_empleado =$row_fact_det['id_empleado'];
		$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
		//Buscamos que exista el producto en la tabla  movimiento_producto para reinsertar una sola vez
		$sql1="select * from movimiento_producto where id_producto='$id_producto'
			AND tipo_entrada_salida='$tipo_entrada_salida'
			AND numero_doc='$numero_doc'
			AND id_sucursal_origen='$id_sucursal'
			";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		$cant_dev_stock=$cantidad-$cant_facturado;
		if($nrow1==0){
			//Hacer el insert a movimiento_producto

					$table1= 'movimiento_producto';
					$form_data1 = array(
					'id_producto' => $id_prod_serv,
					'fecha_movimiento' => $fecha,
					'entrada' => $cant_dev_stock,
					'observaciones' => $tipo_entrada_salida,
					'tipo_entrada_salida' => $tipo_entrada_salida,
					'numero_doc' => $numero_doc,
					'precio_venta' => $precio_venta
					);
			$insertar1 = _insert($table1,$form_data1 );

			$sql2="select producto.id_producto,producto.unidad,stock.stock, stock.costo_promedio from producto,stock
					where producto.id_producto='$id_producto' and producto.id_producto=stock.id_producto
					AND stock.id_sucursal='$id_sucursal'";
			$stock2=_query($sql2);
			$nrow2=_num_rows($stock2);
			$table2= 'stock';
			//Actualizar en stock si  hay registro del producto
			if ($nrow2>0){
				$row2=_fetch_array($stock2);
				$unidad=$row2['unidad'];
				$existencias=$row2['stock'];
				$cantidad_stock=$existencias+$cant_dev_stock;
				$where_clause="WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
				$form_data2 = array(
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta
				);
				$insertar2 = _update($table2,$form_data2, $where_clause );
			}



		}

	}


	if ($delete) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'consignacion Anulada!';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'consignacion no Anulada! ';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formAnular' :
				initial();
				break;
			case 'anular' :
				anular();
				break;
		}
	}
}

?>
