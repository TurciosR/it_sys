<?php
include ("_core.php");
include ('num2letras.php');
//include ('facturacion_funcion_imprimir_dia.php');
include ('facturacion_funcion_imprimir2.php');
function initial(){
		//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri=$_SERVER['REQUEST_URI'];
	list($uri1,$uri2,$filename)=explode("/",$uri);
	$poschar = strpos($filename, '?');
	if ($poschar !== false) {
		list($namelink1,$namelink1)=explode("?",$filename);
		$links=permission_usr($id_user,$namelink1);
	}
	else{
		 $links=permission_usr($id_user,$filename);
	}
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	//include ('facturacion_funcion_imprimir.php');
	$id_factura = $_REQUEST ['id_factura'];
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql="SELECT factura_dia.*, cliente.nombre,cliente.apellido
	FROM factura_dia JOIN cliente ON factura_dia.id_cliente=cliente.id_cliente
	WHERE id_factura_dia='$id_factura'
	AND factura_dia.id_sucursal='$id_sucursal'
	";
	$result = _query( $sql );
	$count = _num_rows( $result );
	if ($count==0){
		$sql="SELECT *,'Cliente' as nombre, 'General' as apellido
		FROM factura_dia
		WHERE id_factura='$id_factura'
		AND id_sucursal='$id_sucursal'
		";
	}
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Imprimir factura</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
		<div class="row" id="row1">
			<!--div class="col-lg-12"-->
				<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>

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
										$fecha1=ed($row['fecha']);
										$id_fact=$row['id_factura_dia'];
										$numero=$row['numero'];
										echo "<tr><td>Numero:</td><td><h5 class='text-navy'>".$numero." del d&iacute;a</h5></td>";
										echo "<tr><td>Num doc Imprimir:</td><td><input type='text' id='num_doc_fact' name='num_doc_fact' value=''  class='form-control'></td>";
										echo "<tr><td>Fecha</td><td><div id='fecha_gen'>".$fecha1."</div></td>";
										echo "<tr><td>Cliente</td><td><h5 class='text-warning'>".$cliente."</h5></td>";
										echo "<tr><td>Total $:</td><td  id='facturado'><h5 class='text-success'>".$row['total']."</h5></td>";
										/*echo "<tr><td>Efectivo $:</td><td><input type='text' id='efectivo' name='efectivo' value=''  class='form-control decimal'></td>";
										echo "<tr><td>Cambio $:</td><td id='cambio'><h5 class='text-danger'></h5></td>";*/
										echo "</tr>";

									}
								}
							?>
						</tbody>
						<tfoot>
						<td align='center'><button type="button" class="btn btn-primary" id="btnPrintFactdia"><i class="fa fa-print"></i> Imprimir</button> </td>
						<td align='center'><button type="button"  class="btn btn-danger" id="btnEsc" data-dismiss="modal"><i class="fa fa-stop"></i> Salir</button>	 </td>
						</tfoot>
				</table>
			</div>
		<!--/div-->
			<?php
			echo "<input type='hidden' nombre='id_factura' id='id_factura' value='$id_fact'>";
			?>
		<!--/div-->

</div>
<!--div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div-->
<!--/modal-footer -->

<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function imprimir_fact(){
	$id_factura = $_POST['id_factura'];
	$fecha = $_POST['fecha'];
	$num_doc_fact = $_POST['num_doc_fact'];
	$fecha1 = md($fecha);
	$id_sucursal=$_SESSION["id_sucursal"];
	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
		$so_cliente='win';
	else
		$so_cliente='lin';

	$sql_fact="SELECT * FROM factura_dia WHERE id_factura_dia='$id_factura'";
	$result_fact=_query($sql_fact);
	$row_fact=_fetch_array($result_fact);
	$nrows_fact=_num_rows($result_fact);


	if($nrows_fact>0){
		$table_fact= 'factura_dia';
		$form_data_fact = array(
			'impresa' => '1'
		);
		$where_clause="WHERE id_factura_dia='$id_factura'";

		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
		$table_fact2= 'factura_detalle_dia';
		$actualizar2 = _update($table_fact2,$form_data_fact, $where_clause );

		//al imprimir tambien actualizar las generadas en factura y factura_detalle para evitar que se vuelvan a generar
		//comentado 01 sept para mientras se prueba la impresion en matricial epson lx-350
		//
	}


	$table_factu= 'factura_detalle';
	$form_data_factu = array(
		'impresa_lote' => '1'
	);
	$where_clause_f=" WHERE fecha='$fecha1' AND id_factura_dia='$id_factura'";
	$actualizar_f = _update($table_factu,$form_data_factu, $where_clause_f );

	$sql_fact2="SELECT * FROM factura_detalle WHERE  id_factura_dia='$id_factura' AND fecha='$fecha1'";
	$result_fact2=_query($sql_fact2);
	$nrows_fact2=_num_rows($result_fact2);

	for($i=0;$i<$nrows_fact2;$i++){
		$row2=_fetch_array($result_fact2);
		$id_factura1=$row2['id_factura'];

		$table_factu2= 'factura';
		if($num_doc_fact!=""){
		$form_data_factu2 = array(
			'impresa' => '1',
			'impresa_lote' => '1',
			'num_fact_impresa'=>$num_doc_fact
		);
	} else{
		$form_data_factu2 = array(
			'impresa' => '1',
			'impresa_lote' => '1',
			'num_fact_impresa'=>$id_factura1
		);
	}
		$where_clause_factu2=" WHERE fecha='$fecha1' AND id_factura='$id_factura1'";
		$actualizar_factu2 = _update($table_factu2,$form_data_factu2, $where_clause_factu2 );
		//pendiente !!!!
		//actualizar numero de documpento para imprimir y en movimiento_producto
		$sql_fact5="SELECT id_factura,numero_doc FROM factura WHERE id_factura='$id_factura1' AND fecha='$fecha1'";
		$tipo_entrada_salida='FACTURA LOTE CONSUMIDOR';
		$result_fact5=_query($sql_fact5);
		$nrows_fact5=_num_rows($result_fact5);

		for($k=0;$k<$nrows_fact5;$k++){
			$row5=_fetch_array($result_fact5);
			$numero_doc=$row5['numero_doc'];


		$table1= 'movimiento_producto';
		$where_clause1="WHERE
		tipo_entrada_salida='$tipo_entrada_salida'
		AND numero_doc='$numero_doc'
		AND fecha_movimiento='$fecha1'
		AND id_sucursal_origen='$id_sucursal'";

		$form_data1 = array(
		'numero_doc'=>$num_doc_fact,
		);
		$insertar1 = _update($table1,$form_data1,$where_clause1);
		}
		//fin pendiente !!!
	}


	$tipo_fact='idfact';
	$info_facturas=print_fact_dia($id_factura,$tipo_fact);
	$nreg_encode['facturar'] =$info_facturas;
	$nreg_encode['sist_ope'] =$so_cliente;

	echo json_encode($nreg_encode);
}


if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'imprimir_fact' :
				imprimir_fact();
				break;
		}
	}
}

?>
