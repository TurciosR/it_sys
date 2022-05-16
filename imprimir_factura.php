<?php
include ("_core.php");
include ('num2letras.php');
include ('facturacion_funcion_imprimir.php');

function initial(){
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user, $filename);
	//permiso del script
	$sql_user="select * from usuario where id_usuario='$id_user'";
	$result_user= _query($sql_user);
	$row_user=_fetch_array($result_user);
	$nrow=_num_rows($result_user);
	$id_sucursal=$row_user['id_sucursal'];
	//include ('facturacion_funcion_imprimir.php');
	$id_factura = $_REQUEST ['id_factura'];
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql="SELECT factura.*, cliente.nombre,cliente.apellido FROM factura JOIN cliente
	ON factura.id_cliente=cliente.id_cliente
	WHERE id_factura='$id_factura'
	AND factura.id_sucursal='$id_sucursal'
	";
	$result = _query( $sql );
	$count = _num_rows( $result );
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
										//echo "<tr><td>Id factura</th><td>$id_factura</td></tr>";
										echo "<tr><td>Id Cliente</td><td><h5 class='text-warning'>".$cliente."</h5></td>";
										echo "<tr><td>Numero Doc</td><td><h5 class='text-danger'>".$row['numero_doc']."</h5></td>";
										echo "<tr><td>Total $:</td><td  id='facturado'><h5 class='text-success'>".$row['total']."</h5></td>";
										echo "<tr><td>Efectivo $:</td><td><input type='text' id='efectivo' name='efectivo' value=''  class='form-control decimal'></td>";
										//echo "<tr><td>Cambio $:</td><td><input type='text' id='cambio' name='cambio' value=''  class='form-control decimal'></td>";
										echo "<tr><td>Cambio $:</td><td id='cambio'><h5 class='text-danger'></h5></td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
						<tfoot>
						<td align='center'><button type="button" class="btn btn-primary" id="btnPrintFact"><i class="fa fa-print"></i> Imprimir</button> </td>
						<td align='center'><button type="button"  class="btn btn-danger" id="btnEsc" data-dismiss="modal"><i class="fa fa-stop"></i> Salir</button>	 </td>
						</tfoot>
				</table>
			</div>
		<!--/div-->
			<?php
			echo "<input type='hidden' nombre='id_factura' id='id_factura' value='$id_factura'>";
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
	$sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
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
		$where_clause="WHERE id_factura='$id_factura'";
		$actualizar = _update($table_fact,$form_data_fact, $where_clause );
		$numero_doc=trim($row_fact['numero_doc']);
	}

	$tipo_fact='idfact';
	$info_facturas=print_fact($id_factura,$tipo_fact);
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
