<?php
include ("_core.php");
function initial(){
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_cajachica_mov = $_REQUEST ['id_cajachica_mov'];
	$sql="SELECT *FROM caja_chica_mov,sucursal WHERE id_cajachica_mov='$id_cajachica_mov' and sucursal.id_sucursal='$id_sucursal'";
	$result = _query( $sql );
	$count = _num_rows( $result );

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Borrar transacción</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Nombre</th>
						</tr>
					</thead>
					<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );
										$id_cajachica_mov=$row["id_cajachica_mov"];
										echo "<tr><td>Id movimiento</th><td>$id_cajachica_mov</td></tr>";
										echo "<tr><td>Tipo Proceso</td><td>".$row['tipo_proceso']."</td>";
										echo "<tr><td>Fecha Movimiento</td><td>".$row['fecha_mov']."</td>";
										echo "</tr>";

									}
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
			echo "<input type='hidden' nombre='id_cajachica_mov' id='id_cajachica_mov' value='$id_cajachica_mov'>";
			?>
		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnDelete">Borrar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php
/*
}
	else {
		echo "<div></div><br><br><div class='alert alert-warning'>You don't have permission to use this module.</div>";
	}
	*/
}
function deleted() {
	$id_sucursal=$_SESSION["id_sucursal"];
	$id_cajachica_mov = $_POST['id_cajachica_mov'];
	$table = 'caja_chica_mov';
	$where_clause = "id_cajachica_mov='" .$id_cajachica_mov. "'";
	$delete = _delete ( $table, $where_clause );
	if ($delete) {
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Registro Borrado!';
	} else {
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Registro no pudo ser Borrado ';
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
