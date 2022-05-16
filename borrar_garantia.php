<?php
include ("_core.php");
function initial(){
	$id_garantia = $_REQUEST ['id_garantia'];
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);	
	$id_sucursal=$row_user['id_sucursal'];
	//$sql="SELECT * FROM garantia WHERE id_garantia='$id_garantia'";
	$sql="SELECT garantia.*,
	usuario.nombre as nombreuser,proveedor.nombre_proveedor, proveedor.telefono1
	FROM garantia 
	JOIN usuario ON garantia.id_usuario=usuario.id_usuario
	JOIN proveedor ON garantia.id_proveedor=proveedor.id_proveedor
	WHERE id_garantia='$id_garantia' 
	AND garantia.id_sucursal='$id_sucursal'";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Eliminar garantia</h4>
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
										$cliente=$row['nombre_proveedor']." ".$row['telefono1'];
										echo "<tr><td>Id Garantia</th><td>$id_garantia</td></tr>";
										echo "<tr><td>Cliente</td><td>".$cliente."</td>";
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
			echo "<input type='hidden' nombre='id_garantia' id='id_garantia' value='$id_garantia'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnDelete">Borrar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function deleted() {
	$id_garantia = $_POST ['id_garantia'];
	
	
	$id_user=$_SESSION["id_usuario"];
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);	
	$id_sucursal=$row_user['id_sucursal'];
	
	$sql_fact="SELECT * FROM garantia WHERE id_garantia='$id_garantia' AND id_sucursal='$id_sucursal'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);
	if($nrows_fact>0){
		$id_cliente=$row_fact['id_garantia'];
		$fecha=$row_fact['fecha'];
		$numero_doc=trim($row_fact['numero_doc']);
		$total=$row_fact['total'];
					
	}			
			
	$sql_fact_det="SELECT  producto.id_producto, producto.descripcion,garantia_detalle.* 
		FROM garantia_detalle JOIN producto ON garantia_detalle.id_prod_serv=producto.id_producto
		WHERE  garantia_detalle.id_garantia='$id_garantia' 
		AND  garantia_detalle.tipo_prod_serv='PRODUCTO' AND garantia_detalle.id_sucursal='$id_sucursal'";
	
	$result_fact_det=_query($sql_fact_det);
	$nrows_fact_det=_num_rows($result_fact_det);	   
	$tipo_entrada_salida='INGRESO POR BORRAR GARANTIA';
	for($i=0;$i<$nrows_fact_det;$i++){
		$row_fact_det=_fetch_array($result_fact_det);
		$id_producto =$row_fact_det['id_producto'];
		$descripcion =$row_fact_det['descripcion'];
		$id_garantia_detalle =$row_fact_det['id_garantia_detalle'];
		$id_prod_serv =$row_fact_det['id_prod_serv'];
		$cantidad =$row_fact_det['cantidad'];
		$precio_venta =$row_fact_det['precio_venta'];
		$subt =$row_fact_det['subtotal'];
		$id_empleado =$row_fact_det['id_empleado'];
		$tipo_prod_serv =$row_fact_det['tipo_prod_serv'];
		//Buscamos que exista el producto en la tabla  movimiento_producto para eliminarlo
		$sql1="select * from movimiento_producto where id_producto='$id_producto' 
			AND numero_doc='$numero_doc' 
			AND id_sucursal_origen='$id_sucursal' 
			";
		$stock1=_query($sql1);
		$row1=_fetch_array($stock1);
		$nrow1=_num_rows($stock1);
		if($nrow1>0){
			
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
				$cantidad_stock=$existencias+$cantidad;
				$where_clause="WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";						
				$form_data2 = array(	
						'id_producto' => $id_prod_serv,
						'stock' => $cantidad_stock,
						'stock_minimo' => $cantidad,
						'precio_sugerido' => $precio_venta
				);    
				$insertar2 = _update($table2,$form_data2, $where_clause ); 
			}
			//Eliminar garantia
			$table = 'garantia';
			$where_clause_g = "id_garantia='".$id_garantia ."' AND id_sucursal='".$id_sucursal."'";
			$delete = _delete($table,$where_clause_g); 
			$table_gd = 'garantia_detalle';
			$where_clause_gd = "id_garantia='".$id_garantia ."' AND id_sucursal='".$id_sucursal."'";
			$delete_gd = _delete($table_gd,$where_clause_gd ); 
		}	
		
	}		
	
	
	if ($delete) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'garantia Eliminada!';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'garantia no Eliminada! ';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'deleted' :
				deleted();
				break;
		}
	}
}

?>
