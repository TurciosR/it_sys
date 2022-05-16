<?php
include ("_core.php");
function initial(){
	$id_factura = $_REQUEST ['id_transace'];
	$id_sucursal=$_SESSION['id_sucursal'];
	$sql="SELECT factura.*, clientes.nombre FROM factura JOIN clientes
	ON factura.id_cliente=clientes.id_cliente
	WHERE id_factura='$id_factura' and factura.id_sucursal='$id_sucursal'
	";
	$result = _query( $sql );
	$count = _num_rows( $result );
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular factura</h4>
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
										$cliente=$row['nombre'];
										echo "<tr><td>Id factura</th><td>$id_factura</td></tr>";
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
			echo "<input type='hidden' nombre='id_factura' id='id_factura' value='$id_factura'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnDelete">Anular</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}
function deleted() {
	_begin();
	$id_factura=$_POST['id_transace'];
	$sel=_fetch_array(_query("SELECT movimiento_producto.id_movimiento,factura.total FROM factura JOIN movimiento_producto ON movimiento_producto.id_factura=factura.id_factura WHERE factura.id_factura=$id_factura"));
	$id_sucursal=$_SESSION['id_sucursal'];
  $id_movimiento = $sel["id_movimiento"];
	$id_mov=$id_movimiento;
	$total=$sel['total'];
  $up=0;
  $up2=0;
  $i=0;
  $an=0;
  $table="movimiento_stock_ubicacion";
  $form_data = array(
    'anulada' => 1,
  );
  $where_clause="id_mov_prod='".$id_movimiento."'";
  $update=_update($table,$form_data,$where_clause);

  if ($update) {
    # code...
  }
  else {
    # code...
    $up=1;
  }

  $table="factura";
  $form_data = array
  (
    'anulada' => 1,
  );
  $where_clause="id_factura='".$id_factura."'";
  $update=_update($table,$form_data,$where_clause);

  if ($update) {
    # code...

  }
  else {
    # code...
    $an=1;
  }

	/*$table="movimiento_producto_pendiente";
	$where_clause="id_movimiento='".$id_movimiento."'";
	$delete=_delete($table,$where_clause);*/

	$sql_des=_fetch_array(_query("SELECT id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));

	$destino = $sql_des;
	$fecha = date("Y-m-d");
	$total_compras = $total;
	$concepto="CARGA DE INVENTARIO";
	$hora=date("H:i:s");
	$fecha_movimiento = date("Y-m-d");
	$id_empleado=$_SESSION["id_usuario"];

	$sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
	$datos_num = _fetch_array($sql_num);
	$ult = $datos_num["ii"]+1;
	$numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';
	$tipo_entrada_salida='ENTRADA DE INVENTARIO';


	$z=1;

	/*actualizar los correlativos de II*/
	$corr=1;
	$table="correlativo";
	$form_data = array(
	  'ii' =>$ult
	);
	$where_clause_c="id_sucursal='".$id_sucursal."'";
	$up_corr=_update($table,$form_data,$where_clause_c);
	if ($up_corr) {
	  # code...
	}
	else {
	  $corr=0;
	}
	if ($concepto=='')
	{
	  $concepto='ENTRADA DE INVENTARIO';
	}
	$table='movimiento_producto';
	$form_data = array(
	  'id_sucursal' => $id_sucursal,
	  'correlativo' => $numero_doc,
	  'concepto' => $concepto,
	  'total' => $total_compras,
	  'tipo' => 'ENTRADA',
	  'proceso' => 'II',
	  'referencia' => $numero_doc,
	  'id_empleado' => $id_empleado,
	  'fecha' => $fecha,
	  'hora' => $hora,
	  'id_suc_origen' => $id_sucursal,
	  'id_suc_destino' => $id_sucursal,
	  'id_proveedor' => 0,
	);
	$insert_mov =_insert($table,$form_data);
	$id_movimiento=_insert_id();

	$j = 1 ;
	$k = 1 ;
	$l = 1 ;
	$m = 1 ;

	$sql_mp=_query("SELECT factura_detalle.id_prod_serv as id_producto,factura_detalle.precio_venta,factura_detalle.cantidad, productos.ultcosto AS precio_compra FROM factura_detalle JOIN productos ON factura_detalle.id_prod_serv = productos.id_producto WHERE id_factura=$id_factura ");
	while($row_mov=_fetch_array($sql_mp))
	{

		$id_producto=$row_mov['id_producto'];
		$cantidad=$row_mov['cantidad'];
		$precio_compra=$row_mov['precio_compra'];
		$fecha_caduca = "0000-00-00";
		$suu = round($cantidad * $precio_compra, 3);
		$sql_su="SELECT id_su, cantidad FROM stock_ubicacion WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal' AND id_ubicacion='1' AND id_estante=0 AND id_posicion=0";
		// echo $sql_su;
		$stock_su=_query($sql_su);
		$nrow_su=_num_rows($stock_su);
		$id_su="";
		/*cantidad de una presentacion por la unidades que tiene*/


		$cantidad=$cantidad;
		if($nrow_su >0)
		{
			$row_su=_fetch_array($stock_su);
			$cant_exis = $row_su["cantidad"];
			$id_su = $row_su["id_su"];
			$cant_new = $cant_exis + $cantidad;
			$form_data_su = array(
				'cantidad' => $cant_new,
			);
			$table_su = "stock_ubicacion";
			$where_su = "id_su='".$id_su."'";
			$insert_su = _update($table_su, $form_data_su, $where_su);
		}
		else
		{
			$form_data_su = array(
				'id_producto' => $id_producto,
				'id_sucursal' => $id_sucursal,
				'cantidad' => $cantidad,
				'id_ubicacion' => $destino,
			);
			$table_su = "stock_ubicacion";
			$insert_su = _insert($table_su, $form_data_su);
			$id_su=_insert_id();
		}
		if(!$insert_su)
		{
			$m=0;
		}

		$sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
		$stock2=_query($sql2);
		$row2=_fetch_array($stock2);
		$nrow2=_num_rows($stock2);
		//echo "aqui 2";
		if ($nrow2>0)
		{
			$existencias=$row2['stock'];
		}
		else
		{
			$existencias=0;
		}

		$sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
		$datos_lot = _fetch_array($sql_lot);
		$lote = $datos_lot["ultimo"]+1;
		$table1= 'movimiento_producto_detalle';
		$cant_total=$cantidad+$existencias;
		$form_data1 = array(
			'id_movimiento'=>$id_movimiento,
			'id_producto' => $id_producto,
			'cantidad' => $cantidad,
			'costo' => $precio_compra,
			//'precio' => $precio_venta,
			'stock_anterior'=>$existencias,
			'stock_actual'=>$cant_total,
			'lote' => $lote,
			//'id_presentacion' => $id_presentacion,
		);
		$insert_mov_det = _insert($table1,$form_data1);
		if(!$insert_mov_det)
		{
			$j = 0;
		}
		$table2= 'stock';
		if($nrow2==0)
		{
			$cant_total=$cantidad;
			$form_data2 = array(
				'id_producto' => $id_producto,
				'stock' => $cant_total,
				'costo_unitario'=>$precio_compra,
				//'precio_unitario'=>$precio_venta,
				'create_date'=>$fecha_movimiento,
				'update_date'=>$fecha_movimiento,
				'id_sucursal' => $id_sucursal
			);
			$insert_stock = _insert($table2,$form_data2 );
		}
		else
		{
			$cant_total=$cantidad+$existencias;
			$form_data2 = array(
				'id_producto' => $id_producto,
				'stock' => $cant_total,
				'costo_unitario'=>round(($precio_compra),2),
				//'precio_unitario'=>round(($precio_venta/$unidades),2),
				'update_date'=>$fecha_movimiento,
				'id_sucursal' => $id_sucursal
			);
			$where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
			$insert_stock = _update($table2,$form_data2, $where_clause );
		}

		if(!$insert_stock)
		{
			$k = 0;
		}
		if ($fecha_caduca!="0000-00-00" && $fecha_caduca!="")
		{
			$sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' and fecha_entrada='$fecha_movimiento' and vencimiento='$fecha_caduca' ";
			$result_caduca=_query($sql_caduca);
			$row_caduca=_fetch_array($result_caduca);
			$nrow_caduca=_num_rows($result_caduca);
			/*if($nrow_caduca==0){*/
			$table_perece= 'lote';

			if($fecha_movimiento>=$fecha_caduca)
			{
				$estado='VENCIDO';
			}
			else
			{
				$estado='VIGENTE';
			}
			$form_data_perece = array(
				'id_producto' => $id_producto,
				'referencia' => $numero_doc,
				'numero' => $lote,
				'fecha_entrada' => $fecha_movimiento,
				'vencimiento'=>$fecha_caduca,
				'precio' => $precio_compra,
				'cantidad' => $cantidad,
				'estado'=>$estado,
				'id_sucursal' => $id_sucursal,
				//'id_presentacion' => $id_presentacion,
			);
			$insert_lote = _insert($table_perece,$form_data_perece );
		}
		else
		{
			$sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' AND fecha_entrada='$fecha_movimiento'";
			$result_caduca=_query($sql_caduca);
			$row_caduca=_fetch_array($result_caduca);
			$nrow_caduca=_num_rows($result_caduca);
			$table_perece= 'lote';
			$estado='VIGENTE';

			$form_data_perece = array(
				'id_producto' => $id_producto,
				'referencia' => $numero_doc,
				'numero' => $lote,
				'fecha_entrada' => $fecha_movimiento,
				'vencimiento'=>$fecha_caduca,
				'precio' => $precio_compra,
				'cantidad' => $cantidad,
				'estado'=>$estado,
				'id_sucursal' => $id_sucursal,
				//'id_presentacion' => $id_presentacion,
			);
			$insert_lote = _insert($table_perece,$form_data_perece );
		}
		if(!$insert_lote)
		{
			$l = 0;
		}

		$table="movimiento_stock_ubicacion";
		$form_data = array(
			'id_producto' => $id_producto,
			'id_origen' => 0,
			'id_destino'=> $id_su,
			'cantidad' => $cantidad,
			'fecha' => $fecha_movimiento,
			'hora' => $hora,
			'anulada' => 0,
			'afecta' => 0,
			'id_sucursal' => $id_sucursal,
			//'id_presentacion'=> $id_presentacion,
			'id_mov_prod' => $id_movimiento,
		);

		$insert_mss =_insert($table,$form_data);

		if ($insert_mss) {
			# code...
		}
		else {
			# code...
			$z=0;
		}


		//detalle de compras
		/*$table_dc= 'detalle_compras';
		$form_data_dc = array(
				'id_compras' => $id_fact,
				'id_producto' => $id_producto,
				// 'numero_doc' => $numero_doc,
				'cantidad' => $cantidad,
				'ultcosto' => $precio_compra,
				//'descuento' => $descto, //es el porcentaje descto sin dividir entre 100
				'subtotal' => $suu,
				//'exento' => $exento,
		);
		$insertar_dc = _insert($table_dc, $form_data_dc);*/

		$sql_pro = _query("SELECT ultcosto FROM productos WHERE id_producto = '$id_producto'");
		$rowp = _fetch_array($sql_pro);
		$costo_anterior = $rowp['ultcosto'];
		if($precio_compra != $costo_anterior)
		{
			$tab_pp = "productos";
			$arrpp = array(
				'ultcosto' => $precio_compra,
			);
			$wpa = "id_producto='".$id_producto."'";
			$upp = _update($tab_pp, $arrpp, $wpa);
			if($upp)
			{
				$sql_por = _query("SELECT * FROM precio_producto WHERE id_producto = '$id_producto'");
				$cuenta_por = _num_rows($sql_por);
				if($cuenta_por > 0)
				{
					$lista_por = "";
					$cn = 0;
					while ($rowpor = _fetch_array($sql_por))
					{
						$porcentaje = $rowpor["porcentaje"];
						$lista_por .= $porcentaje.",";
						$cn += 1;
					}
					//echo $lista_por;
					$tabla_pre = "precio_producto";
					$wwp = "id_producto='".$id_producto."'";
					$delete = _delete($tabla_pre, $wwp);
					if($delete)
					{
						for ($i=0; $i < $cn; $i++)
						{
							$expp = explode("," , $lista_por);
							$porcentaje = $expp[$i];
							$resultado = round($precio_compra* ($porcentaje / 100) , 2);
							$resultado1 = $precio_compra + $resultado;
							$for_lis = array(
								'porcentaje' => $porcentaje,
								'ganancia' => $resultado,
								'costo' => $precio_compra,
								'id_producto' => $id_producto,
								'total' => $resultado1,
							);
							$inser = _insert($tabla_pre, $for_lis);
						}
					}
				}
			}

		}
	}

	//borrar credito
	_delete("credito","id_factura=$id_factura");

  if($i==0)
  {
    if ($up==0&&$up2==0&&$an==0)
    {
			_commit();
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro ingresado correctamente!';
      $xdatos['process']='insert';
    }
    else
    {
      _rollback();
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no pudo ser ingresado!';
      $xdatos['process']='none';
    }
 }
 else {
   _rollback();
   $xdatos['typeinfo']='Error';
   $xdatos['msg']='Stock insuficiente para realizar anulación!'.$stock_destino;
   $xdatos['process']='none';
 }
echo json_encode($xdatos);
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
